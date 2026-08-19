<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

/**
 * 用户认证、访客身份与密码函数
 */

/**
 * 生成稳定的访客标识，用于游客操作（点赞、评论等）。
 */
function visitorIdentity(): array
{
    $existing = session('visykmz_userip');
    if (!empty($existing)) {
        return [
            'id' => $existing,
            'name' => session('visykmz_userzh') ?? '访客',
        ];
    }

    $seed = visitorCookieSeed();
    $identitySource = $seed !== '' ? 'cookie|' . $seed : visitorFallbackIdentitySource();
    try {
        $hash = substr(hash_hmac('sha256', $identitySource, sitePasswordKey()), 0, 24);
    } catch (\Throwable $e) {
        $hash = substr(hash('sha256', $identitySource), 0, 24);
    }
    $visitorId = 'vis#-[' . $hash . ']-#vis';

    session('visykmz_userip', $visitorId);
    session('visykmz_userzh', '访客');

    return [
        'id' => $visitorId,
        'name' => '访客',
    ];
}

function visitorCookieSeed(): string
{
    $cookieName = 'comment_visitor_seed';
    $seed = trim((string)cookie($cookieName));
    if ($seed === '' || !preg_match('/^[a-f0-9]{32,64}$/i', $seed)) {
        try {
            $seed = bin2hex(random_bytes(24));
        } catch (\Throwable $e) {
            $seed = hash('sha256', visitorFallbackIdentitySource() . '|' . microtime(true) . '|' . mt_rand());
        }
    }

    setVisitorCookie($cookieName, $seed, 365, true);
    return $seed;
}

function visitorFallbackIdentitySource(): string
{
    $ip = request()->ip() ?? '0.0.0.0';
    $ua = request()->header('user-agent', '');
    return 'fallback|' . $ip . '|' . $ua;
}

function setVisitorCommentProfileCookies(string $name, string $email, string $url = ''): void
{
    $name = mb_substr(trim(strip_tags($name)), 0, 20, 'UTF-8');
    $email = strtolower(trim($email));
    $url = trim($url);

    if ($name !== '') {
        setVisitorCookie('comment_vis_name', $name, 365, false);
    }
    if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setVisitorCookie('comment_vis_email', $email, 365, false);
    }
    if ($url !== '' && isSafeHtmlUrl($url)) {
        setVisitorCookie('comment_vis_url', $url, 365, false);
    }
}

function setVisitorCookie(string $name, string $value, int $days, bool $httpOnly): void
{
    $expires = time() + ($days * 86400);
    $secure = (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? null) === '443');

    setcookie($name, $value, [
        'expires' => $expires,
        'path' => '/',
        'secure' => $secure,
        'httponly' => $httpOnly,
        'samesite' => 'Lax',
    ]);
    $_COOKIE[$name] = $value;
}

function isVisitorUser(string $username): bool
{
    return strpos($username, 'vis#-[') !== false || strpos($username, ']-#vis') !== false;
}

function getVisitorAvatarByEmail(string $email, string $default = '/assets/img/tx.png'): string
{
    $email = strtolower(trim($email));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $default;
    }

    [$local, $domain] = explode('@', $email, 2);
    $domain = strtolower($domain);

    // QQ邮箱直接用QQ头像接口
    if (in_array($domain, ['qq.com', 'vip.qq.com', 'foxmail.com'], true) && preg_match('/^\d+$/', $local)) {
        return 'https://q1.qlogo.cn/g?b=qq&nk=' . rawurlencode($local) . '&s=100';
    }

    // Cravatar：不带 d=404，无头像时自动返回默认图片
    return 'https://cn.cravatar.com/avatar/' . md5($email) . '?s=100';
}

function getLikeDisplayName($like, string $fallback = ''): string
{
    $data = is_object($like) && method_exists($like, 'toArray') ? $like->toArray() : (array)$like;
    $username = trim((string)($data['luser'] ?? ''));
    $cachedName = trim((string)($data['lname'] ?? ''));

    if ($username !== '' && isVisitorUser($username)) {
        return $cachedName !== '' ? $cachedName : ($fallback !== '' ? $fallback : '访客');
    }

    return getUserDisplayName($username, $fallback);
}

function getLikeAvatar($like, array $likeUsers = [], string $default = '/assets/img/tx.png'): string
{
    $data = is_object($like) && method_exists($like, 'toArray') ? $like->toArray() : (array)$like;
    $username = trim((string)($data['luser'] ?? ''));

    if ($username !== '' && isVisitorUser($username)) {
        $avatar = trim((string)($data['limg'] ?? ''));
        return resolveVisitorAvatar($avatar, $default);
    }

    $likeUserInfo = $likeUsers[$username] ?? null;
    if ($likeUserInfo) {
        return assetUrl($likeUserInfo['img'] ?: $default);
    }

    return getUserAvatar($username, $default);
}

function getCommentAvatar($comment, string $default = '/assets/img/tx.png'): string
{
    $data = is_object($comment) && method_exists($comment, 'toArray') ? $comment->toArray() : (array)$comment;
    $username = trim((string)($data['couser'] ?? ''));
    if ($username !== '' && isVisitorUser($username)) {
        $avatar = trim((string)($data['coimg'] ?? ''));
        return resolveVisitorAvatar($avatar, $default);
    }

    return getUserAvatar($username, $default);
}

/**
 * 解析访客头像：过滤掉无效的 cravatar URL，回退到默认头像
 */
function resolveVisitorAvatar(string $avatar, string $default = '/assets/img/tx.png'): string
{
    if ($avatar === '') {
        return $default;
    }
    // 过滤旧数据中带 d=404 的 cravatar URL（表示无头像）
    if (preg_match('#cravatar\.com/avatar/.+d=404#i', $avatar)) {
        return $default;
    }
    return assetUrl($avatar);
}

/**
 * 判断是否为管理员（带静态缓存）
 */
function isAdmin(string $username): bool
{
    static $cache = [];
    if (array_key_exists($username, $cache)) {
        return $cache[$username];
    }

    $role = \think\facade\Db::name('user')
        ->where('username', $username)
        ->value('role');
    $result = (string)$role === 'admin';
    $cache[$username] = $result;
    return $result;
}

/**
 * 获取用户公开主页URL
 */
function getUserHomeUrl(string $username): string
{
    return '/user/' . md5(md5($username));
}

/**
 * 获取用户头像URL（带缓存）
 */
function getUserAvatar(string $username, string $default = '/assets/img/tx.png'): string
{
    if (empty($username)) {
        return $default;
    }

    // 检查预填充缓存（由 prefetchUsers 写入）
    if (isset($GLOBALS['__mm_avatar_cache'][$username])) {
        return $GLOBALS['__mm_avatar_cache'][$username];
    }

    static $cache = [];
    if (isset($cache[$username])) {
        return $cache[$username];
    }

    $user = \think\facade\Db::name('user')
        ->where('username', $username)
        ->field('img,email')
        ->find();

    $avatar = buildAvatarUrl($user, $default);
    $cache[$username] = $avatar;
    return $avatar;
}

/**
 * 根据用户数据构建头像URL：自定义头像 > Cravatar > 默认头像
 */
function buildAvatarUrl(?array $user, string $default = '/assets/img/tx.png'): string
{
    if (empty($user)) {
        return $default;
    }

    // 优先使用自定义头像
    $img = trim((string)($user['img'] ?? ''));
    if ($img !== '') {
        // 外部URL（如cravatar）直接返回，不做本地路径拼接
        if (preg_match('#^https?://#i', $img)) {
            return $img;
        }
        return assetUrl($img);
    }

    // 尝试 Cravatar（基于邮箱哈希，d=404 表示无头像时返回404）
    $email = trim((string)($user['email'] ?? ''));
    if ($email !== '') {
        $hash = md5(strtolower(trim($email)));
        return 'https://cn.cravatar.com/avatar/' . $hash . '?s=100';
    }

    return $default;
}

function getUserDisplayName(string $username, string $fallback = ''): string
{
    $username = trim($username);
    $fallback = trim($fallback);
    if ($username === '') {
        return $fallback;
    }

    if (isVisitorUser($username)) {
        return $fallback !== '' ? $fallback : '访客';
    }

    // 检查预填充缓存（由 prefetchUsers 写入）
    if (isset($GLOBALS['__mm_display_name_cache'][$username])) {
        $name = $GLOBALS['__mm_display_name_cache'][$username];
        return $name !== '' ? $name : ($fallback !== '' ? $fallback : $username);
    }

    static $cache = [];
    if (!array_key_exists($username, $cache)) {
        $user = \think\facade\Db::name('user')
            ->where('username', $username)
            ->field('username,name')
            ->find();
        $cache[$username] = $user ? trim((string)($user['name'] ?: $user['username'])) : '';
    }

    if ($cache[$username] !== '') {
        return $cache[$username];
    }

    return $fallback !== '' ? $fallback : $username;
}

/**
 * 生成密码哈希（自动升级：新密码使用 password_hash，旧 MD5 保持兼容）
 */
function hashPassword(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * 兼容旧函数名，内部调用 hashPassword
 */
function md5Password(string $password): string
{
    return hashPassword($password);
}

/**
 * 验证密码（兼容旧 MD5 + 自动升级）
 *
 * 验证成功后，如果是旧 MD5 哈希，自动升级为 password_hash 并返回新哈希。
 * 调用方应将返回值写回数据库：
 *   $newHash = verifyPassword($input, $dbHash);
 *   if ($newHash !== null) {
 *       // 旧密码已升级，保存新哈希
 *       Database::update('user', ['password' => $newHash], 'id = ?', [$userId]);
 *   }
 */
function verifyPassword(string $password, string $hash): bool|string
{
    // 空哈希直接拒绝
    if (empty($hash)) {
        return false;
    }

    // password_hash 生成的哈希以 $2y$ 开头（60字符）
    if (password_verify($password, $hash)) {
        // 检查是否需要重新哈希（password_needs_rehash 用于算法升级场景）
        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            return password_hash($password, PASSWORD_DEFAULT);
        }
        return true;
    }

    // 兼容旧 MD5：32位十六进制字符串
    if (strlen($hash) === 32 && ctype_xdigit($hash)) {
        if (md5($password) === $hash) {
            // 验证成功，返回新哈希供调用方升级
            return password_hash($password, PASSWORD_DEFAULT);
        }
    }

    return false;
}

/**
 * 直接验证密码（不触发自动升级，用于中间件等只需判断的场景）
 */
function verifyPasswordOnly(string $password, string $hash): bool
{
    if (empty($hash)) {
        return false;
    }

    if (password_verify($password, $hash)) {
        return true;
    }

    // 兼容旧 MD5
    if (strlen($hash) === 32 && ctype_xdigit($hash)) {
        return md5($password) === $hash;
    }

    return false;
}

function sitePasswordKey(): string
{
    $key = config('app.app_key');
    if (empty($key)) {
        $key = env('APP_KEY', '');
    }
    if (!empty($key)) {
        return (string)$key;
    }

    throw new \RuntimeException('应用密钥 APP_KEY 未配置，请在 .env 文件中设置 APP_KEY');
}

function sitePasswordHash(string $password): string
{
    return 'hmac$' . hash_hmac('sha256', (string)$password, sitePasswordKey());
}

function isSitePasswordEnabled(string $stored): bool
{
    return trim((string)$stored) !== '';
}

function verifySitePasswordInput(string $input, string $stored): bool
{
    $stored = (string)$stored;
    if ($stored === '') {
        return true;
    }
    if (strpos($stored, 'hmac$') === 0) {
        return hash_equals($stored, sitePasswordHash($input));
    }

    return hash_equals(md5(md5((string)$stored)), md5(md5((string)$input)));
}

function sitePasswordCookieValue(string $stored): string
{
    $stored = (string)$stored;
    if ($stored === '') {
        return '';
    }
    return hash_hmac('sha256', $stored, sitePasswordKey());
}

function hasValidSitePasswordCookie(string $stored): bool
{
    $cookieValue = cookie('pagepass');
    return $cookieValue !== null && hash_equals(sitePasswordCookieValue($stored), (string)$cookieValue);
}

function setSitePasswordCookie(string $stored): void
{
    cookie('pagepass', sitePasswordCookieValue($stored), 604800);
}
