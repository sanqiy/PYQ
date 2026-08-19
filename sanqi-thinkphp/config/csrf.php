<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

// CSRF 验证配置
return [
    // 完全豁免路由（无需验证Token，使用URL路径，全小写）
    'exempt' => [
        'api/load-more',
        'api/home/load-more',
        'api/comment/load',
        'api/site-password-verify',
        'api/music/random',
        'api/music/netease',
        'api/music/qq',
        'api/music/kugou',
        'api/music/kuwo',
        'install/testdb',
        'install/doinstall',
        'server/api/install/report',
        'server/api/version/check',
    ],

    // 跳过同源检查但仍需验证Token的路由（文件上传等multipart请求）
    'skip_origin' => [
        'api/user/avatar',
        'api/user/cover',
        'api/upload/image',
        'api/upload/video',
    ],
];
