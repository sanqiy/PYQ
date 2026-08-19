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
use app\model\Lcke;
use app\service\NotificationService;
use think\facade\Db;

/**
 * Like API controller.
 */
class Like extends Base
{
    /**
     * Toggle like / unlike.
     */
    public function toggle()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $articleCid = strip_tags(trim((string)$this->request->post('tieid', '')));
        $action = $this->request->post('zts', '0'); // 0=like, -1=unlike

        if ($articleCid === '') {
            return $this->error('参数错误');
        }

        $article = Essay::where('cid', $articleCid)->find();
        if (!$article) {
            return $this->error('文章不存在');
        }

        $hasGuestProfile = false;
        $user = $this->getUser();
        if ($user) {
            $likeUser = $user['username'];
            $likeName = $user['name'] ?: $user['username'];
            $likeImg = $user['img'];
        } else {
            $visitor = visitorIdentity();
            $likeUser = $visitor['id'];
            $visName = trim(strip_tags((string)$this->request->post('vis_name', '')));
            $visEmail = trim((string)$this->request->post('vis_email', ''));
            $visUrl = trim((string)$this->request->post('vis_url', ''));
            $hasGuestProfile = $visName !== '' && filter_var($visEmail, FILTER_VALIDATE_EMAIL);
            $likeName = $visName !== '' ? mb_substr($visName, 0, 20, 'UTF-8') : $visitor['name'];
            $likeImg = getVisitorAvatarByEmail($visEmail);
            if ($likeName !== '') {
                session('visykmz_userzh', $likeName);
            }
            if ($hasGuestProfile) {
                setVisitorCommentProfileCookies($visName, $visEmail, $visUrl);
            }
        }

        $existing = Lcke::where('lwz', $articleCid)->where('luser', $likeUser)->find();

        if ($action == '0') {
            if ($existing) {
                return $this->error('已经点赞过了');
            }

            try {
                $likeInsert = [
                    'luser' => $likeUser,
                    'lwz' => $articleCid,
                    'ltime' => date('Y-m-d H:i:s'),
                    'lname' => $hasGuestProfile ? $likeName : '',
                    'limg' => $hasGuestProfile ? $likeImg : '',
                ];
                Db::name('lcke')->strict(false)->insert($likeInsert);
            } catch (\think\exception\PDOException $e) {
                if ($e->getCode() == 23000) {
                    return $this->error('已经点赞过了');
                }
                throw $e;
            }

            $liker = $user
                ? ['username' => $user['username'], 'name' => $user['name'] ?: $user['username'], 'img' => $user['img']]
                : ['username' => $likeUser, 'name' => $likeName, 'img' => $likeImg];
            NotificationService::sendLikeNotify(
                $liker,
                $article['ptpuser'],
                $articleCid
            );

            return $this->success('点赞成功', [
                'action' => 'like',
                'name' => $likeName,
                'img' => resolveVisitorAvatar($likeImg, '/assets/img/tx.png'),
                'user' => $likeUser,
                'guest' => $user ? 0 : 1,
                'profiled' => $hasGuestProfile ? 1 : 0,
            ]);
        }

        if (!$existing) {
            return $this->error('未点赞');
        }

        if (!$user) {
            $siteConfig = $this->getSiteConfig();
            if (isFlag($siteConfig['vislike_cancel'] ?? 1, 1) !== 1) {
                return $this->error('游客暂不支持取消点赞');
            }
        }

        Lcke::where('lwz', $articleCid)->where('luser', $likeUser)->delete();
        return $this->success('取消成功', [
            'action' => 'unlike',
            'user' => $likeUser,
            'guest' => $user ? 0 : 1,
            'profiled' => (!$user && trim((string)($existing['lname'] ?? '')) !== '') ? 1 : 0,
        ]);
    }
}
