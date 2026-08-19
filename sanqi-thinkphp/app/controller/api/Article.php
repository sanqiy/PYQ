<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\api;

use app\controller\Base;
use app\model\Essay;
use app\model\Comm;
use app\model\Lcke;
use app\model\ArticleAttachment;
use app\service\SiteConfigService;
use think\facade\Cache;
use app\service\ContentFeatureService;
use app\service\ImageService;
use app\service\UploadService;
use app\service\FileUploadService;
use app\validate\ArticleValidate;
use think\facade\Db;
use think\facade\View;

/**
 * 文章API控制器
 */
class Article extends Base
{
    const BASE64_IMAGE_MAX_SIZE = 5242880;
    const BASE64_IMAGE_MAX_COUNT = 15;
    const BASE64_IMAGE_MAX_TOTAL_SIZE = 52428800;

    /**
     * 清除文章列表缓存
     */
    protected function clearArticleCache()
    {
        Cache::tag('article')->clear();
    }

    protected function normalizeTags($raw)
    {
        $parts = preg_split('/[\s,，#＃]+/u', (string)$raw);
        $tags = [];
        foreach ($parts as $tag) {
            $tag = trim(strip_tags($tag));
            if ($tag === '') {
                continue;
            }
            $tag = mb_substr($tag, 0, 30, 'UTF-8');
            if (!in_array($tag, $tags, true)) {
                $tags[] = $tag;
            }
            if (count($tags) >= 10) {
                break;
            }
        }
        return implode(',', $tags);
    }

    protected function normalizeArticleCoverUrl($url)
    {
        $url = trim((string)$url);
        if ($url === '' || !isSafeHtmlUrl($url)) {
            return '';
        }
        return $url;
    }

    protected function normalizeImageUrl($url)
    {
        $url = trim((string)$url);
        if ($url === '' || !isSafeHtmlUrl($url)) {
            return '';
        }
        return $url;
    }

    protected function savePostedArticleCover($user)
    {
        $postedCovers = $this->request->post('article_cover_image', []);
        if (!is_array($postedCovers)) {
            $postedCovers = [$postedCovers];
        }

        foreach ($postedCovers as $base64) {
            if (trim((string)$base64) === '') {
                continue;
            }
            $url = $this->saveBase64Image((string)$base64, $user['username'] ?? '');
            if ($url !== '') {
                return $url;
            }
        }

        return '';
    }

    protected function extractArticleCover($text)
    {
        $text = (string)$text;
        if (preg_match('/!\[[^\]]*\]\(([^)\s]+)(?:\s+"[^"]*")?\)/u', $text, $match)) {
            return $this->normalizeArticleCoverUrl($match[1]);
        }
        if (preg_match('/<img\b[^>]*\bsrc\s*=\s*["\']([^"\']+)["\']/i', $text, $match)) {
            return $this->normalizeArticleCoverUrl($match[1]);
        }
        return '';
    }

    protected function defaultArticleCover()
    {
        return '/assets/img/thumbnailbg.svg';
    }

    protected function normalizePublishTime($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return date('Y-m-d H:i:s');
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return date('Y-m-d H:i:s');
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    protected function articleAuditStatus($saveStatus, array $siteConfig)
    {
        if ($saveStatus === 'draft') {
            return -2;
        }

        return isFlag($siteConfig['ptpaud'] ?? 0) === 1 && !$this->isAdmin() ? 0 : 1;
    }

    protected function saveBase64Image($base64, $username)
    {
        $base64 = str_replace(' ', '+', $base64);
        if (!preg_match('/^data:\s*image\/([a-zA-Z0-9.+-]+);base64,/', $base64, $match)) {
            return '';
        }

        $payload = substr($base64, strpos($base64, ',') + 1);
        $payload = preg_replace('/\s+/', '', $payload);
        if ($payload === '' || !preg_match('/^[A-Za-z0-9+\/=]+$/', $payload)) {
            return '';
        }

        $estimatedSize = (int)(strlen($payload) * 3 / 4);
        if ($estimatedSize > self::BASE64_IMAGE_MAX_SIZE) {
            return '';
        }

        $data = base64_decode($payload, true);
        if ($data === false) {
            return '';
        }
        if (strlen($data) > self::BASE64_IMAGE_MAX_SIZE) {
            return '';
        }

        $imageInfo = @getimagesizefromstring($data);
        if (!$imageInfo || empty($imageInfo['mime'])) {
            return '';
        }

        if (!ImageService::withinPixelLimit($imageInfo)) {
            return '';
        }

        // 计算 MD5，检查是否已存在
        $md5 = md5($data);
        $existing = FileUploadService::findByMd5($md5);
        if ($existing) {
            FileUploadService::incrementRef($existing['url']);
            return $existing['url'];
        }

        $datePath = date('Ym');
        $dir = public_path() . "upload/{$datePath}/";
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = str_replace('.', '', (string)microtime(true)) . random_int(100000, 999999) . substr(md5($username), 0, 12);
        $mimeExtMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];
        if (!isset($mimeExtMap[$imageInfo['mime']])) {
            return '';
        }
        $ext = $mimeExtMap[$imageInfo['mime']];
        $path = $dir . $filename . '.' . $ext;
        if (@file_put_contents($path, $data, LOCK_EX) === false) {
            return '';
        }
        ImageService::sanitizeImageFile($path);

        $siteConfig = $this->getSiteConfig();
        if ($ext !== 'webp' && isFlag($siteConfig['imgpres'] ?? 1, 1) === 1) {
            $webpPath = $dir . $filename . '.webp';
            if (ImageService::convertToWebp($path, $webpPath)) {
                @unlink($path);
                $path = $webpPath;
                $ext = 'webp';
                // WebP 转换后重新计算 MD5
                $md5 = md5_file($path);
            }
        }

        ImageService::createThumbnail($path, $dir . 'thumbs/' . $filename . '.' . $ext);
        $url = "/upload/{$datePath}/{$filename}.{$ext}";

        // 记录到 file_uploads 表
        FileUploadService::create($md5, $url, 'image');

        return $url;
    }

    protected function normalizeArticleFormData($user)
    {
        if ($this->request->post('type') !== null) {
            return [
                'text' => $this->request->post('text', ''),
                'type' => $this->normalizeArticleType($this->request->post('type', 'only')),
                'article_title' => trim((string)$this->request->post('article_title', '')),
                'article_cover' => $this->normalizeSubmittedArticleCover($user),
                'cover_color' => $this->normalizeCoverColor($this->request->post('cover_color', '')),
                'images' => $this->request->post('images', ''),
                'video' => $this->request->post('video', ''),
                'video_cover' => $this->request->post('video_cover', ''),
                'music' => $this->request->post('music', ''),
                'location' => $this->request->post('location', ''),
                'is_ad' => (int)$this->request->post('is_ad', 0),
                'ad_url' => $this->request->post('ad_url', ''),
                'is_anonymous' => (int)$this->request->post('is_anonymous', 0),
                'disable_comment' => (int)$this->request->post('disable_comment', 0),
                'edit_mode' => $this->request->post('edlx', ''),
                'edit_cid' => strip_tags(trim((string)$this->request->post('cid', $this->request->post('edwzcid', '')))),
                'publish_time' => $this->request->post('publish_time', $this->request->post('fbtime', '')),
                'tags' => $this->request->post('tags', ''),
                'save_status' => $this->request->post('save_status', 'publish'),
                'article_template' => $this->normalizeArticleTemplate($this->request->post('article_template', '')),
            ];
        }

        $radio = (string)$this->request->post('radio', '1');
        $rawText = (string)$this->request->post('text', '');
        $text = $radio === '4' ? $rawText : str_replace(["\r\n", "\r", "\n"], '<br>', $rawText);
        $images = '';
        $video = '';
        $music = '';
        $type = 'only';
        $articleTitle = '';
        $articleCover = '';

        if ($radio === '1') {
            $imgul = trim((string)$this->request->post('imgul', ''));
            if ($imgul !== '') {
                $urls = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $imgul))));
                if (count($urls) > 15) {
                    return $this->error('不可超过15张图片!');
                }
                foreach ($urls as $url) {
                    if (!preg_match('/^https?:\/\//i', $url)) {
                        return $this->error('请输入正确的图片链接!');
                    }
                }
                $images = implode('(+@+)', $urls);
                $type = 'img';
            } else {
                $postedImages = $this->request->post('image', []);
                if (!is_array($postedImages)) {
                    $postedImages = [$postedImages];
                }
                $postedImages = array_values(array_filter($postedImages, function ($value) {
                    return trim((string)$value) !== '';
                }));
                if (count($postedImages) > self::BASE64_IMAGE_MAX_COUNT) {
                    return $this->error('不可超过15张图片!');
                }
                $saved = [];
                $totalSize = 0;
                foreach ($postedImages as $imageValue) {
                    $imageValue = trim((string)$imageValue);
                    if ($imageValue === '') {
                        continue;
                    }

                    if (preg_match('/^data:\s*image\//i', $imageValue)) {
                        $payloadPos = strpos($imageValue, ',');
                        if ($payloadPos !== false) {
                            $payload = preg_replace('/\s+/', '', substr($imageValue, $payloadPos + 1));
                            $totalSize += (int)(strlen($payload) * 3 / 4);
                            if ($totalSize > self::BASE64_IMAGE_MAX_TOTAL_SIZE) {
                                return $this->error('图片总体积不能超过50MB!');
                            }
                        }
                        $url = $this->saveBase64Image($imageValue, $user['username']);
                    } else {
                        $url = $this->normalizeImageUrl($imageValue);
                    }

                    if ($url !== '' && !in_array($url, $saved, true)) {
                        $saved[] = $url;
                    }
                }
                if (!empty($saved)) {
                    $images = implode('(+@+)', $saved);
                    $type = 'img';
                }
            }

            // 编辑模式下，如果没有上传新图片，保留原有图片
            if ($images === '' && ($this->request->post('edlx', '') === 'edites') && ($this->request->post('edwztype', '') === 'img')) {
                $images = (string)$this->request->post('edwzimages', '');
                if ($images !== '') {
                    $type = 'img';
                }
            }
        } elseif ($radio === '2') {
            $type = 'video';
            $videoUrl = trim((string)$this->request->post('spp', ''));
            $videoCover = trim((string)$this->request->post('sppfm', ''));
            if ($videoUrl === '') {
                $file = $this->fileToArray($this->request->file('file'));
                if (!$file) {
                    return $this->error('请填写视频链接或上传视频');
                }
                $result = (new UploadService())->uploadVideo($file);
                if (empty($result['success'])) {
                    return $this->error($result['message'] ?? '视频上传失败');
                }
                $videoUrl = $result['url'];
            }
            if ($videoCover === '') {
                $coverBase64 = trim((string)$this->request->post('video_cover_data', ''));
                if ($coverBase64 !== '') {
                    $savedCover = $this->saveBase64Image($coverBase64, $user['username'] ?? '');
                    if ($savedCover !== '') {
                        $videoCover = $savedCover;
                    }
                }
            }
            $video = $videoUrl . '|' . $videoCover;
        } elseif ($radio === '3') {
            $type = 'music';
            $musicUrl = trim((string)$this->request->post('music', ''));
            $musicName = trim((string)$this->request->post('musicm', ''));
            $musicArtist = trim((string)$this->request->post('musics', ''));
            $musicCover = trim((string)$this->request->post('musict', ''));
            $musicPlatform = trim((string)$this->request->post('musicplatform', 'netease'));
            if ($musicUrl === '' || $musicName === '' || $musicArtist === '') {
                return $this->error('请填写完整音乐信息');
            }
            // 仅网易云的纯数字ID需要转换为URL
            if (is_numeric($musicUrl) && $musicPlatform === 'netease') {
                $musicUrl = '//music.163.com/song/media/outer/url?id=' . $musicUrl . '.mp3';
            }
            $music = $musicUrl . '|' . $musicName . '|' . $musicArtist . '|' . $musicCover;
        } elseif ($radio === '4') {
            $type = 'article';
            $articleTitle = trim((string)$this->request->post('article_title', ''));
            if ((string)$this->request->post('article_cover_mode', 'link') === 'upload') {
                $articleCover = $this->savePostedArticleCover($user);
                if ($articleCover === '') {
                    $articleCover = $this->normalizeArticleCoverUrl($this->request->post('article_cover', ''));
                }
            } else {
                $articleCover = $this->normalizeArticleCoverUrl($this->request->post('article_cover', ''));
            }
            $coverColor = $this->normalizeCoverColor($this->request->post('cover_color', ''));
        }

        return [
            'text' => $text,
            'type' => $type,
            'article_title' => $articleTitle,
            'article_cover' => $articleCover,
            'cover_color' => $coverColor ?? '',
            'images' => $images,
            'video' => $video,
            'video_cover' => '',
            'music' => $music,
            'location' => $this->request->post('dw', ''),
            'is_ad' => (int)$this->request->post('radiogg', 0),
            'ad_url' => $this->request->post('gg', ''),
            'is_anonymous' => (int)$this->request->post('nmkg', 0),
            'disable_comment' => (string)$this->request->post('yxplkg', '1') === '0' ? 1 : 0,
            'edit_mode' => $this->request->post('edlx', ''),
            'edit_cid' => $this->request->post('edwzcid', ''),
            'publish_time' => $this->request->post('fbtime', ''),
            'tags' => $this->request->post('tags', ''),
            'save_status' => $this->request->post('save_status', 'publish'),
            'article_template' => $this->normalizeArticleTemplate($this->request->post('article_template', ''))
        ];
    }

    protected function normalizeArticleType($type)
    {
        $type = (string)$type;
        return in_array($type, ['only', 'img', 'video', 'music', 'article'], true) ? $type : 'only';
    }

    protected function normalizeArticleTemplate(string $template): string
    {
        $template = trim($template);
        if ($template === '') {
            return '';
        }
        // 只允许字母、数字、下划线
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $template)) {
            return '';
        }
        return $template;
    }

    protected function normalizeCoverColor(string $color): string
    {
        $color = trim($color);
        if ($color === '') return '';
        if (preg_match('/^#([0-9a-fA-F]{3}){1,2}$/', $color)) {
            if (strlen($color) === 4) {
                $color = '#' . $color[1] . $color[1] . $color[2] . $color[2] . $color[3] . $color[3];
            }
            return strtolower($color);
        }
        return '';
    }

    protected function normalizeSubmittedArticleCover($user)
    {
        if ((string)$this->request->post('article_cover_mode', 'link') === 'upload') {
            $cover = $this->savePostedArticleCover($user);
            if ($cover !== '') {
                return $cover;
            }
        }

        return $this->normalizeArticleCoverUrl($this->request->post('article_cover', ''));
    }

    /**
     * 保存文章
     */
    public function save()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        // 要求登录
        if (!$this->requireLogin()) return;

        $user = $this->getUser();
        $siteConfig = $this->getSiteConfig();
        $features = ContentFeatureService::getConfig();

        // 检查发布权限
        if (!in_array((string)($user['essqx'] ?? '0'), ['1', '2'], true)) {
            return $this->error('您没有发布权限');
        }

        // 获取参数
        $formData = $this->normalizeArticleFormData($user);
        // normalizeArticleFormData 可能直接返回错误响应
        if ($formData instanceof \think\response\Json) {
            return $formData;
        }

        // 验证内容类型
        $validate = new ArticleValidate();
        $validate->scene('save');
        if (!$validate->batch(true)->check(['type' => $formData['type']])) {
            return $this->error($validate->getError());
        }

        $type = $formData['type'];
        $text = $type === 'article' ? trim((string)$formData['text']) : cleanArticleHtml($formData['text']);
        $articleTitle = trim((string)($formData['article_title'] ?? ''));
        $articleCover = $this->normalizeArticleCoverUrl($formData['article_cover'] ?? '');
        $images = $formData['images'];
        $video = $formData['video'];
        $videoCover = $formData['video_cover'] ?? '';
        $music = $formData['music'];
        $location = $formData['location'];
        $isAd = $formData['is_ad'];
        $adUrl = $formData['ad_url'];
        $isAnonymous = $formData['is_anonymous'];
        $disableComment = $formData['disable_comment'];
        $editMode = $formData['edit_mode'];
        $editCid = $formData['edit_cid'];
        $publishTime = $formData['publish_time'];
        $tags = $formData['tags'];
        $coverColor = $formData['cover_color'] ?? '';
        $articleTemplate = $formData['article_template'] ?? '';
        $saveStatus = $formData['save_status'];

        $saveStatus = !empty($features['drafts_enabled']) && $saveStatus === 'draft' ? 'draft' : 'publish';
        $tags = !empty($features['tags_enabled']) ? $this->normalizeTags($tags) : '';
        $publishTime = $this->normalizePublishTime($publishTime);
        $ptpaud = $this->articleAuditStatus($saveStatus, $siteConfig);

        // 验证内容
        if (empty($text) && $type === 'only') {
            return $this->error('内容不能为空');
        }

        // 处理图片
        if ($type === 'article') {
            if ($articleTitle === '') {
                return $this->error('请填写文章标题');
            }
            if ($text === '') {
                return $this->error('请填写文章内容');
            }
            if ($articleCover === '') {
                $articleCover = $this->extractArticleCover($text);
            }
            if ($articleCover === '') {
                $articleCover = $this->defaultArticleCover();
            }
            if ($coverColor === '') {
                $coverColor = ImageService::dominantColorForUrl($articleCover);
            }
        } else {
            $articleTitle = '';
            $articleCover = '';
            $coverColor = '';
            $articleTemplate = '';
        }

        $ptpimag = '';
        if ($type === 'img' && !empty($images)) {
            $ptpimag = $images;
        }

        // 处理视频
        $ptpvideo = '';
        if ($type === 'video' && !empty($video)) {
            $ptpvideo = $video;
            if (!empty($videoCover)) {
                $ptpvideo .= '|' . $videoCover;
            }
        }

        // 处理音乐
        $ptpmusic = '';
        if ($type === 'music' && !empty($music)) {
            $ptpmusic = $music;
        }

        // 文章数据
        $data = [
            'ptpuser' => $user['username'],
            'is_anonymous' => $isAnonymous ? 1 : 0,
            'article_title' => $articleTitle,
            'article_cover' => $articleCover,
            'cover_color' => $coverColor,
            'ptptext' => $text,
            'ptpimag' => $ptpimag,
            'ptpvideo' => $ptpvideo,
            'ptpmusic' => $ptpmusic,
            'ptplx' => $type,
            'ptpdw' => $location,
            'tags' => $tags,
            'ptptime' => $publishTime,
            'ptpgg' => $isAd ? 1 : 0,
            'ptpggurl' => $adUrl,
            'ptpys' => 1,
            'commauth' => $disableComment ? 0 : 1,
            'ptpaud' => $ptpaud,
            'article_template' => $articleTemplate,
            'ip' => request()->ip()
        ];

        if ($editMode === 'edites' && !empty($editCid)) {
            // 编辑模式
            $article = Essay::where('cid', $editCid)->find();
            if (!$article) {
                return $this->error('文章不存在');
            }

            if ($article['ptpuser'] !== $user['username'] && !$this->isAdmin()) {
                return $this->error('无权编辑');
            }

            // 同步文件引用：对比新旧文件 URL
            $oldUrls = FileUploadService::extractUrlsFromArticle($article->toArray());
            $newUrls = FileUploadService::extractUrlsFromArticle($data);
            FileUploadService::syncReferences($oldUrls, $newUrls);

            try {
                Db::startTrans();
                Essay::where('cid', $editCid)->update($data);
                $this->saveAttachments($editCid);
                $pollResult = $this->savePoll($editCid);
                if ($pollResult instanceof \think\response\Json) {
                    Db::rollback();
                    return $pollResult;
                }
                Db::commit();
            } catch (\Throwable $e) {
                Db::rollback();
                return $this->error('保存失败: ' . $e->getMessage());
            }

            $this->clearArticleCache();
            $redirect = ($saveStatus === 'draft' || (int)$ptpaud === 0) ? '/home' : '/view/' . $editCid;
            return $this->success($saveStatus === 'draft' ? '草稿保存成功' : '编辑成功', [
                'cid' => $editCid,
                'redirect' => $redirect,
            ]);
        } else {
            // 新增模式
            $data['cid'] = uniqueId();

            // 为本地文件增加引用（处理用户输入的已有本地图片 URL）
            $newUrls = FileUploadService::extractUrlsFromArticle($data);
            foreach ($newUrls as $url) {
                $existing = FileUploadService::findByUrl($url);
                if ($existing) {
                    FileUploadService::incrementRef($url);
                }
            }

            try {
                Db::startTrans();
                Essay::create($data);
                $this->saveAttachments($data['cid']);
                $pollResult = $this->savePoll($data['cid']);
                if ($pollResult instanceof \think\response\Json) {
                    Db::rollback();
                    return $pollResult;
                }
                Db::commit();
            } catch (\Throwable $e) {
                Db::rollback();
                return $this->error('保存失败: ' . $e->getMessage());
            }

            $this->clearArticleCache();
            $redirect = ($saveStatus === 'draft' || (int)$ptpaud === 0) ? '/home' : '/view/' . $data['cid'];
            return $this->success($saveStatus === 'draft' ? '草稿保存成功' : ($ptpaud === 0 ? '提交成功，等待审核' : '发布成功'), [
                'cid' => $data['cid'],
                'redirect' => $redirect,
            ]);
        }
    }

    /**
     * 保存文章附件
     */
    private function saveAttachments(string $articleCid): void
    {
        $attachmentsJson = trim((string) $this->request->post('attachments', ''));
        if ($attachmentsJson === '') {
            return;
        }

        $attachments = json_decode($attachmentsJson, true);
        if (!is_array($attachments) || empty($attachments)) {
            return;
        }

        // 编辑模式：删除旧附件
        ArticleAttachment::where('article_cid', $articleCid)->delete();

        foreach ($attachments as $i => $att) {
            $type = ($att['type'] ?? '') === 'link' ? 'link' : 'file';
            $fileUrl = trim((string) ($att['url'] ?? ''));
            if ($fileUrl === '') {
                continue;
            }
            ArticleAttachment::create([
                'article_cid' => $articleCid,
                'type' => $type,
                'file_url' => $fileUrl,
                'file_name' => trim((string) ($att['name'] ?? '')),
                'file_desc' => trim((string) ($att['desc'] ?? '')),
                'file_size' => (int) ($att['size'] ?? 0),
                'extract_code' => trim((string) ($att['code'] ?? '')),
                'sort_order' => $i,
            ]);
        }
    }

    /**
     * 保存投票数据
     */
    private function savePoll(string $articleCid)
    {
        $pollJson = trim((string) $this->request->post('poll_data', ''));
        $existing = \app\model\Poll::where('article_cid', $articleCid)->find();
        if ($pollJson === '' || $pollJson === 'null') {
            if ($existing) {
                if (\app\model\PollVote::where('poll_id', $existing->id)->count() > 0 && !$this->pollChangeConfirmed()) {
                    return $this->error('修改投票将清空已有投票，请确认后再保存');
                }
                \app\model\PollVote::where('poll_id', $existing->id)->delete();
                $existing->delete();
            }
            return;
        }

        $pollData = $this->normalizePollData(json_decode($pollJson, true));
        if ($pollData === null) {
            return;
        }

        if ($existing) {
            $oldData = $this->normalizePollData([
                'question' => $existing->question,
                'options' => $existing->options,
                'type' => $existing->type,
                'expire_at' => $existing->expire_at ?? null,
            ]);

            if ($oldData !== null && $this->pollSignature($oldData) === $this->pollSignature($pollData)) {
                \app\model\Poll::where('id', $existing->id)->update([
                    'question' => $pollData['question'],
                    'options' => json_encode($pollData['options'], JSON_UNESCAPED_UNICODE),
                    'type' => $pollData['type'],
                    'expire_at' => $pollData['expire_at'],
                ]);
                return;
            }

            if (\app\model\PollVote::where('poll_id', $existing->id)->count() > 0 && !$this->pollChangeConfirmed()) {
                return $this->error('修改投票将清空已有投票，请确认后再保存');
            }

            \app\model\PollVote::where('poll_id', $existing->id)->delete();
            $existing->delete();
        }

        // 创建新投票
        \app\model\Poll::create([
            'article_cid' => $articleCid,
            'question' => $pollData['question'],
            'options' => json_encode($pollData['options'], JSON_UNESCAPED_UNICODE),
            'type' => $pollData['type'],
            'expire_at' => $pollData['expire_at'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function normalizePollData($pollData): ?array
    {
        if (empty($pollData) || empty($pollData['question']) || empty($pollData['options']) || !is_array($pollData['options'])) {
            return null;
        }

        $question = trim((string)$pollData['question']);
        $options = array_values(array_filter(array_map(function ($opt) {
            return trim((string)$opt);
        }, $pollData['options']), function ($opt) {
            return $opt !== '';
        }));

        if ($question === '' || count($options) < 2) {
            return null;
        }

        $expireAt = trim((string)($pollData['expire_at'] ?? ''));
        if ($expireAt !== '') {
            $ts = strtotime($expireAt);
            $expireAt = $ts ? date('Y-m-d H:i:s', $ts) : '';
        }

        return [
            'question' => $question,
            'options' => $options,
            'type' => isset($pollData['type']) && (int)$pollData['type'] === 2 ? 2 : 1,
            'expire_at' => $expireAt !== '' ? $expireAt : null,
        ];
    }

    private function pollSignature(array $pollData): string
    {
        return hash('sha256', json_encode([
            'question' => $pollData['question'],
            'options' => $pollData['options'],
            'type' => $pollData['type'],
            'expire_at' => $pollData['expire_at'] ?? null,
        ], JSON_UNESCAPED_UNICODE));
    }

    private function pollChangeConfirmed(): bool
    {
        return (string)$this->request->post('poll_change_confirm', '0') === '1';
    }

    public function markdownPreview()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $text = (string)$this->request->post('text', '');
        if (mb_strlen($text, 'UTF-8') > 50000) {
            return $this->error('内容过长');
        }

        return $this->success('ok', [
            'html' => renderArticleEmojis(renderMarkdownArticle($text)),
        ]);
    }

    public function autosaveDraft()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $loginCheck = $this->requireLogin();
        if ($loginCheck !== true) {
            return $loginCheck;
        }

        if (!ContentFeatureService::draftsEnabled()) {
            return $this->success('草稿箱未启用', ['enabled' => false]);
        }
        if (!$this->hasDraftVersionTable()) {
            return $this->success('草稿版本表未创建', ['enabled' => false]);
        }

        $user = $this->getUser();
        $draftKey = $this->normalizeDraftKey((string)$this->request->post('draft_key', ''));
        $articleCid = strip_tags(trim((string)$this->request->post('cid', '')));
        $payload = (string)$this->request->post('payload', '');
        $title = trim((string)$this->request->post('title', ''));

        if ($draftKey === '' || $payload === '') {
            return $this->error('草稿数据为空');
        }
        if (strlen($payload) > 300000) {
            return $this->error('草稿内容过大');
        }
        $data = json_decode($payload, true);
        if (!is_array($data)) {
            return $this->error('草稿格式错误');
        }

        if ($articleCid !== '') {
            $article = Essay::where('cid', $articleCid)->find();
            if (!$article || ($article['ptpuser'] !== $user['username'] && !$this->isAdmin())) {
                return $this->error('无权保存该草稿');
            }
        }

        Db::name('article_draft_versions')->insert([
            'username' => (string)$user['username'],
            'draft_key' => $draftKey,
            'article_cid' => $articleCid,
            'title' => mb_substr($title !== '' ? $title : '未命名草稿', 0, 120, 'UTF-8'),
            'form_data' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $this->trimDraftVersions((string)$user['username'], $draftKey);

        return $this->success('已自动保存', ['enabled' => true]);
    }

    public function draftVersions()
    {
        $loginCheck = $this->requireLogin();
        if ($loginCheck !== true) {
            return $loginCheck;
        }

        if (!ContentFeatureService::draftsEnabled() || !$this->hasDraftVersionTable()) {
            return $this->success('ok', ['versions' => []]);
        }

        $user = $this->getUser();
        $draftKey = $this->normalizeDraftKey((string)$this->request->get('draft_key', ''));
        if ($draftKey === '') {
            return $this->success('ok', ['versions' => []]);
        }

        $rows = Db::name('article_draft_versions')
            ->where('username', (string)$user['username'])
            ->where('draft_key', $draftKey)
            ->order('id', 'desc')
            ->limit(12)
            ->select()
            ->toArray();

        $versions = [];
        foreach ($rows as $row) {
            $formData = json_decode((string)($row['form_data'] ?? ''), true);
            $versions[] = [
                'id' => (int)$row['id'],
                'title' => (string)($row['title'] ?? ''),
                'created_at' => (string)($row['created_at'] ?? ''),
                'form_data' => is_array($formData) ? $formData : [],
            ];
        }

        return $this->success('ok', ['versions' => $versions]);
    }

    private function normalizeDraftKey(string $key): string
    {
        $key = trim($key);
        return preg_match('/^[a-zA-Z0-9_-]{1,128}$/', $key) ? $key : '';
    }

    private function trimDraftVersions(string $username, string $draftKey): void
    {
        $ids = Db::name('article_draft_versions')
            ->where('username', $username)
            ->where('draft_key', $draftKey)
            ->order('id', 'desc')
            ->limit(100)
            ->column('id');
        $keep = array_slice(array_map('intval', $ids), 0, 20);
        if (empty($keep)) {
            return;
        }
        Db::name('article_draft_versions')
            ->where('username', $username)
            ->where('draft_key', $draftKey)
            ->whereNotIn('id', $keep)
            ->delete();
    }

    private function hasDraftVersionTable(): bool
    {
        static $exists = null;
        if ($exists !== null) {
            return $exists;
        }
        try {
            $default = (string)(config('database.default') ?: 'mysql');
            $prefix = (string)(config('database.connections.' . $default . '.prefix') ?: '');
            $rows = Db::query("SHOW TABLES LIKE '" . $prefix . "article_draft_versions'");
            return $exists = !empty($rows);
        } catch (\Throwable $e) {
            return $exists = false;
        }
    }

    /**
     * 删除文章
     */
    public function delete()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        // 要求登录
        if (!$this->requireLogin()) return;

        $user = $this->getUser();
        $cid = $this->request->post('wzdid', '');

        $validate = new ArticleValidate();
        $validate->scene('delete');
        if (!$validate->batch(true)->check(['cid' => $cid])) {
            return $this->error($validate->getError());
        }

        $article = Essay::where('cid', $cid)->find();
        if (!$article) {
            return $this->error('文章不存在');
        }

        // 检查权限
        if ($article['ptpuser'] !== $user['username'] && !$this->isAdmin()) {
            return $this->error('无权删除');
        }

        // 清理文件引用
        $fileUrls = FileUploadService::extractUrlsFromArticle($article->toArray());

        // 删除文章（使用Db直接删除，绕过SoftDelete）
        try {
            Db::startTrans();
            Db::table('essay')->where('cid', $cid)->delete();
            // 删除相关评论和点赞
            Db::table('comm')->where('wzcid', $cid)->delete();
            Db::table('lcke')->where('lwz', $cid)->delete();
            // 删除附件
            Db::table('article_attachments')->where('article_cid', $cid)->delete();
            // 删除投票
            $poll = Db::table('polls')->where('article_cid', $cid)->find();
            if ($poll) {
                Db::table('poll_votes')->where('poll_id', $poll['id'])->delete();
                Db::table('polls')->where('id', $poll['id'])->delete();
            }
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            return $this->error('删除失败: ' . $e->getMessage());
        }

        // 删除文章成功后清理文件引用
        FileUploadService::cleanupArticleFiles($fileUrls);

        $this->clearArticleCache();
        return $this->success('删除成功');
    }

    /**
     * 设置文章隐私
     */
    public function privacy()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        // 要求登录
        if (!$this->requireLogin()) return;

        $user = $this->getUser();
        $cid = $this->request->post('ztys', '');
        $privacy = $this->request->post('ztwid', '1');

        $validate = new ArticleValidate();
        $validate->scene('privacy');
        if (!$validate->batch(true)->check(['cid' => $cid])) {
            return $this->error($validate->getError());
        }

        $article = Essay::where('cid', $cid)->find();
        if (!$article) {
            return $this->error('文章不存在');
        }

        // 检查权限
        if ($article['ptpuser'] !== $user['username'] && !$this->isAdmin()) {
            return $this->error('无权操作');
        }

        Essay::where('cid', $cid)->update(['ptpys' => $privacy]);
        Cache::tag('article')->clear();
        return $this->success('操作成功');
    }

    /**
     * 置顶/取消置顶文章
     */
    public function pin()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        // 要求管理员权限
        if (!$this->requireAdmin()) return;

        $cid = $this->request->post('wid', '');
        $action = $this->request->post('lx', '');

        $validate = new ArticleValidate();
        $validate->scene('pin');
        if (!$validate->batch(true)->check(['cid' => $cid, 'lx' => $action])) {
            return $this->error($validate->getError());
        }

        $siteConfig = $this->getSiteConfig();
        $topes = !empty($siteConfig['topes']) ? array_filter(explode("\n", $siteConfig['topes'])) : [];

        if ($action === 'sw') {
            // 置顶
            if (!in_array($cid, $topes)) {
                $topes[] = $cid;
            }
        } else {
            // 取消置顶
            $topes = array_filter($topes, function ($v) use ($cid) {
                return $v !== $cid;
            });
        }

        SiteConfigService::set('topes', implode("\n", $topes));
        Cache::tag('article')->clear();
        return $this->success('操作成功');
    }

    /**
     * 用户个人置顶/取消置顶文章
     */
    public function userPin()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $loginCheck = $this->requireLogin();
        if ($loginCheck !== true) {
            return $loginCheck;
        }

        $user = $this->getUser();
        $cid = $this->request->post('wid', '');
        $action = $this->request->post('lx', '');

        $validate = new ArticleValidate();
        $validate->scene('pin');
        if (!$validate->batch(true)->check(['cid' => $cid, 'lx' => $action])) {
            return $this->error($validate->getError());
        }

        $article = Essay::where('cid', $cid)->find();
        if (!$article) {
            return $this->error('文章不存在');
        }

        if ($article['ptpuser'] !== $user['username'] && !$this->isAdmin()) {
            return $this->error('无权操作');
        }

        Essay::where('cid', $cid)->update(['user_top' => ($action === 'sw' ? 1 : 0)]);
        Cache::tag('article')->clear();
        return $this->success('操作成功');
    }

    /**
     * 加载更多文章（首页）
     */
    public function loadMore()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $so = strip_tags(trim((string)$this->request->post('so', '')));
        $siteConfig = $this->getSiteConfig();
        $essgs = max(1, min(50, (int)($siteConfig['essgs'] ?? 10)));
        // page 仅作为旧客户端 fallback；新流程优先使用 last_id cursor。
        $offset = max(0, intval($this->request->post('page', 0)));
        $lastId = max(0, intval($this->request->post('last_id', 0)));
        $tag = trim((string)$this->request->post('tag', ''));
        $needsTotal = $so !== '' || (ContentFeatureService::tagsEnabled() && $tag !== '');

        $query = Essay::where('ptpaud', '1')
            ->where('ptpys', '<>', '0')
            ->where('ptptime', '<=', date('Y-m-d H:i:s'));

        // 排除置顶文章
        if (!empty($siteConfig['topes'])) {
            $topIds = array_filter(explode("\n", $siteConfig['topes']));
            if (!empty($topIds)) {
                $query->whereNotIn('cid', $topIds);
            }
        }

        if (!empty($so)) {
            $query->where('ptptext', 'like', '%' . $so . '%');
        }
        if (ContentFeatureService::tagsEnabled() && $tag !== '') {
            $query->whereRaw('FIND_IN_SET(?, tags)', [$tag]);
        }

        // 只有搜索/标签页需要总数；普通时间线避免每次 count()。
        $total = $needsTotal ? (clone $query)->count() : null;

        // 获取文章列表
        $fields = 'id,cid,ptpuser,is_anonymous,ptptext,ptpimag,ptpvideo,ptpmusic,ptplx,ptpdw,ptptime,ptpgg,ptpggurl,ptpys,commauth,ptpaud,tags,article_title,article_cover,cover_color,user_top,article_template';
        if ($lastId > 0) {
            $articles = (clone $query)->where('id', '<', $lastId)
                ->field($fields)
                ->order('id', 'desc')
                ->limit($essgs + 1)
                ->select();
        } else {
            $articles = (clone $query)->field($fields)
                ->order('id', 'desc')
                ->limit($offset, $essgs + 1)
                ->select();
        }

        // 渲染文章HTML
        $currentUser = $this->getUser();
        $articlesArr = $articles->toArray();
        $hasMore = count($articlesArr) > $essgs;
        if ($hasMore) {
            $articlesArr = array_slice($articlesArr, 0, $essgs);
        }
        $postMeta = $this->prefetchPostMeta($articlesArr, $currentUser);

        // 批量预取作者用户数据
        $authorNames = array_column($articlesArr, 'ptpuser');
        $this->prefetchUsers($authorNames);

        $html = '';
        foreach ($articlesArr as $article) {
            $html .= View::fetch('/component/post_item', [
                'post' => $article,
                'user' => $currentUser,
                'siteConfig' => $siteConfig,
                'postLikes' => $postMeta['likesByCid'],
                'postComments' => $postMeta['commentsByCid'],
                'postLikedMap' => $postMeta['likedCids'],
            ]);
        }

        // 统一 JSON 格式返回
        $loaded = count($articlesArr);
        $lastArticle = $loaded > 0 ? $articlesArr[$loaded - 1] : null;
        $nextCursor = $lastArticle ? (int)$lastArticle['id'] : $lastId;
        return $this->success('加载成功', [
            'html' => $html,
            'total' => $total,
            'loaded' => $loaded,
            'offset' => $offset + $loaded,
            'next_cursor' => $nextCursor,
            'hasMore' => $hasMore,
        ]);
    }
}
