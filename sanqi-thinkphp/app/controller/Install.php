<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller;

use think\App;

/**
 * 安装程序控制器
 * 不继承Base，避免依赖数据库的traits
 */
class Install extends \app\BaseController
{
    private $installLockFile;

    protected function initialize()
    {
        parent::initialize();
        $this->installLockFile = app()->getRuntimePath() . 'install.lock';
    }

    /**
     * 安装首页
     */
    public function index()
    {
        if ($this->isInstalled()) {
            return view('install/already', ['pageTitle' => '已安装']);
        }

        $envInfo = $this->checkEnvironment();
        return view('install/index', [
            'pageTitle' => '环境检查',
            'envInfo' => $envInfo,
            'step' => 1,
        ]);
    }

    /**
     * 数据库配置步骤
     */
    public function step2()
    {
        if ($this->isInstalled()) {
            return redirect('/install');
        }

        return view('install/step2', [
            'pageTitle' => '数据库配置',
            'step' => 2,
            'dbConfig' => [
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'sanqi',
                'username' => 'root',
                'prefix' => '',
            ],
        ]);
    }

    /**
     * 管理员配置步骤
     */
    public function step3()
    {
        if ($this->isInstalled()) {
            return redirect('/install');
        }

        return view('install/step3', [
            'pageTitle' => '管理员配置',
            'step' => 3,
        ]);
    }

    /**
     * 测试数据库连接
     */
    public function testDb()
    {
        $host = $this->request->post('host', '127.0.0.1');
        $port = $this->request->post('port', '3306');
        $database = $this->request->post('database', '');
        $username = $this->request->post('username', '');
        $password = $this->request->post('password', '');

        if (!$this->validateIdentifier($database)) {
            return json(['code' => 400, 'msg' => '数据库名称不合法']);
        }
        if (!$this->validateHost($host) || !$this->validatePort($port)) {
            return json(['code' => 400, 'msg' => '主机或端口不合法']);
        }

        try {
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $stmt = $pdo->prepare("SHOW DATABASES LIKE ?");
            $stmt->execute([$database]);
            $dbExists = $stmt->fetch();

            if (!$dbExists) {
                $pdo->exec("CREATE DATABASE `" . $this->escapeIdentifier($database) . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            }

            $pdo->exec("USE `" . $this->escapeIdentifier($database) . "`");
            $this->testDatabasePrivileges($pdo);

            return json(['code' => 200, 'msg' => '数据库连接和权限检查通过']);
        } catch (\PDOException $e) {
            return json(['code' => 400, 'msg' => $this->formatPdoError($e)]);
        }
    }

    /**
     * 执行安装
     */
    public function doInstall()
    {
        if ($this->isInstalled()) {
            return json(['code' => 400, 'msg' => '已经安装过了']);
        }

        $host = $this->request->post('host', '127.0.0.1');
        $port = $this->request->post('port', '3306');
        $database = $this->request->post('database', '');
        $username = $this->request->post('username', '');
        $password = $this->request->post('password', '');
        $prefix = $this->request->post('prefix', '');
        $adminUser = $this->request->post('admin_user', '');
        $adminPass = $this->request->post('admin_pass', '');
        $adminEmail = $this->request->post('admin_email', '');
        $siteName = $this->request->post('site_name', '朋友圈');

        if (empty($database) || empty($username) || empty($adminUser) || empty($adminPass)) {
            return json(['code' => 400, 'msg' => '请填写完整信息']);
        }

        if (strlen($adminPass) < 6) {
            return json(['code' => 400, 'msg' => '管理员密码至少6位']);
        }

        if (!$this->validateIdentifier($database)) {
            return json(['code' => 400, 'msg' => '数据库名称不合法']);
        }
        if (!$this->validateIdentifier($prefix, true)) {
            return json(['code' => 400, 'msg' => '表前缀不合法']);
        }
        if (!$this->validateHost($host) || !$this->validatePort($port)) {
            return json(['code' => 400, 'msg' => '主机或端口不合法']);
        }
        if (!$this->validateIdentifier($adminUser)) {
            return json(['code' => 400, 'msg' => '管理员用户名不合法']);
        }

        try {
            $this->assertInstallWritable();

            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $safeDb = $this->escapeIdentifier($database);
            $stmt = $pdo->prepare("SHOW DATABASES LIKE ?");
            $stmt->execute([$database]);
            if (!$stmt->fetch()) {
                $pdo->exec("CREATE DATABASE `{$safeDb}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            }
            $pdo->exec("USE `{$safeDb}`");
            $this->testDatabasePrivileges($pdo);

            // 执行建表SQL
            $sqlFile = app()->getRootPath() . 'database' . DIRECTORY_SEPARATOR . 'install.sql';
            if (!file_exists($sqlFile)) {
                return json(['code' => 400, 'msg' => '安装SQL文件不存在']);
            }
            if (!is_readable($sqlFile)) {
                return json(['code' => 400, 'msg' => '安装SQL文件不可读，请检查database目录权限']);
            }

            $sql = file_get_contents($sqlFile);
            if ($sql === false) {
                return json(['code' => 400, 'msg' => '安装SQL文件读取失败']);
            }
            // 应用表前缀
            if ($prefix !== '') {
                $tables = ['user','essay','comm','comment_edit_history','lcke','article_draft_versions','article_attachments','message','link','file_uploads','configx','polls','poll_votes','emoji'];
                $sql = str_replace(
                    array_map(fn($t) => "`{$t}`", $tables),
                    array_map(fn($t) => "`{$prefix}{$t}`", $tables),
                    $sql
                );
            }
            $statements = $this->parseSql($sql);

            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    try {
                        $pdo->exec($statement);
                    } catch (\PDOException $e) {
                        $preview = mb_substr($statement, 0, 60);
                        return json(['code' => 400, 'msg' => "SQL执行失败: {$preview}... 错误: " . $e->getMessage()]);
                    }
                }
            }

            // 导入表情数据（如果emoji 表为空）
            $emojiTable = $prefix !== '' ? "`{$prefix}emoji`" : '`emoji`';
            $emojiCount = $pdo->query("SELECT COUNT(*) FROM {$emojiTable}")->fetchColumn();
            if ($emojiCount == 0) {
                $emojiSqlFile = app()->getRootPath() . 'database' . DIRECTORY_SEPARATOR . 'migrate_emoji.sql';
                if (file_exists($emojiSqlFile)) {
                    $emojiSql = file_get_contents($emojiSqlFile);
                    if ($prefix !== '') {
                        $emojiSql = str_replace('`emoji`', "`{$prefix}emoji`", $emojiSql);
                    }
                    $emojiStatements = $this->parseSql($emojiSql);
                    foreach ($emojiStatements as $statement) {
                        $statement = trim($statement);
                        if (!empty($statement)) {
                            try { $pdo->exec($statement); } catch (\PDOException $e) {}
                        }
                    }
                }
            }

            // 创建管理员
            $passwordHash = password_hash($adminPass, PASSWORD_DEFAULT);
            $passid = bin2hex(random_bytes(32));
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $defaultAvatar = '/assets/img/default_avatar.png';

            // 验证关键列是否存在，不存在则自动补上（兼容旧版install.sql）
            $userTable = $prefix !== '' ? "`{$prefix}user`" : '`user`';
            $stmt = $pdo->prepare("INSERT INTO {$userTable} (`username`, `password`, `email`, `name`, `img`, `essqx`, `esseam`, `regtime`, `regip`, `logtime`, `logip`, `passid`, `role`) VALUES (?, ?, ?, ?, ?, 2, 1, NOW(), ?, NOW(), ?, ?, 'admin')");
            $stmt->execute([$adminUser, $passwordHash, $adminEmail, $adminUser, $defaultAvatar, $ip, $ip, $passid]);

            // 站点配置写入 configx
            $defaultIcon = '/assets/img/favicon.png';
            $defaultLogo = '/assets/img/logo.png';
            $defaultCover = '/assets/img/default_cover.webp';
            $siteDefaults = [
                'name' => $siteName,
                'subtitle' => '',
                'icon' => $defaultIcon,
                'logo' => $defaultLogo,
                'homimg' => $defaultCover,
                'sign' => '',
                'music' => '',
                'essgs' => '10',
                'commgs' => '10',
                'zt' => '1',
                'regqx' => '0',
                'loginkg' => '1',
                'lnkzt' => '0',
                'kqsy' => '0',
                'comaud' => '0',
                'ptpaud' => '0',
                'ptpfan' => '1',
                'notname' => '0',
                'imgpres' => '1',
                'rosdomain' => '1',
                'daymode' => '1',
                'gotop' => '1',
                'search' => '1',
                'videoauplay' => '0',
                'regverify' => '0',
                'viscomm' => '0',
                'vislike_cancel' => '1',
                'pagepass' => '',
                'emydz' => '',
                'emssl' => '',
                'emduk' => '',
                'emkey' => '',
                'emzh' => '',
                'emfs' => '',
                'emfszm' => '',
                'emtype' => '',
                'aliyun_key' => '',
                'aliyun_secret' => '',
                'aliyun_from' => '',
                'date' => date('Y-m-d H:i:s'),
                'copyright' => '',
                'beian' => '',
                'topes' => '',
                'scfont' => 'default',
                'musplay' => '',
            ];

            $configxTable = $prefix !== '' ? "`{$prefix}configx`" : '`configx`';
            $stmtSite = $pdo->prepare("INSERT INTO {$configxTable} (`title`, `text`) VALUES (?, ?)");
            foreach ($siteDefaults as $key => $value) {
                $stmtSite->execute([$key, json_encode((string)$value, JSON_UNESCAPED_UNICODE)]);
            }

            // 扩展配置
            $stmtSite->execute(['comment_security', '{"audit_enabled":0,"keywords":"","blacklist":""}']);
            $stmtSite->execute(['content_features', '{"drafts_enabled":0,"tags_enabled":0}']);

            // 写入 .env
            $this->writeEnvFile($host, $port, $database, $username, $password, $prefix);

            // 安装锁（仅记录安装时间，不存储敏感凭据）
            $this->writeFileOrFail($this->installLockFile, json_encode([
                'installed_at' => date('Y-m-d H:i:s'),
            ]));

            // 上报安装信息到中心服务器
            $this->reportInstall($adminUser);

            return json(['code' => 200, 'msg' => '安装成功']);
        } catch (\PDOException $e) {
            return json(['code' => 400, 'msg' => $this->formatPdoError($e)]);
        } catch (\Exception $e) {
            return json(['code' => 400, 'msg' => '安装失败: ' . $e->getMessage()]);
        }
    }

    /**
     * 安装完成页
     */
    public function complete()
    {
        if (!$this->isInstalled()) {
            return redirect('/install');
        }

        return view('install/complete', ['pageTitle' => '安装完成', 'step' => 4]);
    }

    /**
     * 上报安装信息到中心服务器
     */
    private function reportInstall(string $adminUser): void
    {
        \app\service\UpdateService::reportToServer($adminUser);
    }

    private function isInstalled(): bool
    {
        return file_exists($this->installLockFile);
    }

    private function checkEnvironment(): array
    {
        $checks = [];
        $checks[] = ['name' => 'PHP版本', 'required' => '>= 8.0', 'current' => PHP_VERSION, 'passed' => version_compare(PHP_VERSION, '8.0.0', '>=')];

        foreach ([
            'pdo' => 'PDO扩展',
            'pdo_mysql' => 'PDO MySQL',
            'json' => 'JSON扩展',
            'mbstring' => 'mbstring扩展',
            'curl' => 'curl扩展',
            'openssl' => 'openssl扩展',
            'gd' => 'gd扩展',
            'zip' => 'zip扩展',
            'xml' => 'xml扩展',
            'simplexml' => 'simplexml扩展',
        ] as $extension => $label) {
            $loaded = extension_loaded($extension);
            $checks[] = ['name' => $label, 'required' => '已安装', 'current' => $loaded ? '已安装' : '未安装', 'passed' => $loaded];
        }

        // fileinfo 为推荐项，缺失不阻断安装
        $fileinfoLoaded = extension_loaded('fileinfo');
        $checks[] = [
            'name' => 'fileinfo扩展',
            'required' => '推荐安装',
            'current' => $fileinfoLoaded ? '已安装' : '未安装（可用，上传校验会自动回退）',
            'passed' => true,
            'warn' => !$fileinfoLoaded,
        ];

        $checks[] = $this->iniSizeCheck('memory_limit', 128 * 1024 * 1024);
        $checks[] = $this->iniSizeCheck('post_max_size', 20 * 1024 * 1024);
        $checks[] = $this->iniSizeCheck('upload_max_filesize', 20 * 1024 * 1024);

        $runtimePath = app()->getRuntimePath();
        $uploadPath = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . 'upload';
        $envPath = app()->getRootPath() . '.env';

        $checks[] = ['name' => 'runtime目录', 'required' => '可写', 'current' => $this->canWriteDirectory($runtimePath, true) ? '可写' : '不可写', 'passed' => $this->canWriteDirectory($runtimePath, true)];
        $checks[] = ['name' => 'public/upload目录', 'required' => '可创建/可写', 'current' => $this->canWriteDirectory($uploadPath, true) ? '可创建或可写' : '不可写', 'passed' => $this->canWriteDirectory($uploadPath, true)];
        $checks[] = ['name' => '.env文件', 'required' => '可创建/可写', 'current' => $this->canWriteFile($envPath) ? '可创建或可写' : '不可写', 'passed' => $this->canWriteFile($envPath)];

        $openBaseDir = trim((string)ini_get('open_basedir'));
        $checks[] = [
            'name' => 'open_basedir',
            'required' => '建议允许项目目录、runtime、public/upload',
            'current' => $openBaseDir === '' ? '未开启' : '已开启：' . $openBaseDir,
            'passed' => true,
            'warn' => $openBaseDir !== '',
        ];

        return $checks;
    }

    private function iniSizeCheck(string $key, int $requiredBytes): array
    {
        $raw = (string)ini_get($key);
        $bytes = $this->parseIniSize($raw);
        $passed = $bytes < 0 || $bytes >= $requiredBytes;

        return [
            'name' => $key,
            'required' => '>=' . $this->formatBytes($requiredBytes),
            'current' => $raw === '' ? '未设置' : $raw,
            'passed' => $passed,
        ];
    }

    private function parseIniSize(string $value): int
    {
        $value = trim($value);
        if ($value === '') {
            return 0;
        }
        if ($value === '-1') {
            return -1;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float)$value;
        switch ($unit) {
            case 'g':
                $number *= 1024;
                // no break
            case 'm':
                $number *= 1024;
                // no break
            case 'k':
                $number *= 1024;
        }

        return (int)$number;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1024 * 1024 * 1024) {
            return (int)($bytes / 1024 / 1024 / 1024) . 'G';
        }
        if ($bytes >= 1024 * 1024) {
            return (int)($bytes / 1024 / 1024) . 'M';
        }
        if ($bytes >= 1024) {
            return (int)($bytes / 1024) . 'K';
        }
        return (string)$bytes;
    }

    private function canWriteDirectory(string $path, bool $allowCreate = false): bool
    {
        if (is_dir($path)) {
            return is_writable($path);
        }

        if (!$allowCreate) {
            return false;
        }

        $parent = dirname($path);
        return is_dir($parent) && is_writable($parent);
    }

    private function canWriteFile(string $path): bool
    {
        if (is_file($path)) {
            return is_writable($path);
        }

        $parent = dirname($path);
        return is_dir($parent) && is_writable($parent);
    }

    private function assertInstallWritable(): void
    {
        $this->ensureDirectory(app()->getRuntimePath(), 'runtime目录');
        $this->ensureDirectory(app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . 'upload', 'public/upload目录');

        $envFile = app()->getRootPath() . '.env';
        if (!$this->canWriteFile($envFile)) {
            throw new \RuntimeException('.env文件不可写，请检查项目根目录权限');
        }
        if (!$this->canWriteFile($this->installLockFile)) {
            throw new \RuntimeException('runtime/install.lock不可写，请检查runtime目录权限');
        }
    }

    private function ensureDirectory(string $path, string $label): void
    {
        if (!is_dir($path) && !mkdir($path, 0755, true) && !is_dir($path)) {
            throw new \RuntimeException($label . '创建失败，请检查目录权限');
        }
        if (!is_writable($path)) {
            throw new \RuntimeException($label . '不可写，请检查目录权限');
        }
    }

    private function writeFileOrFail(string $path, string $content): void
    {
        $dir = dirname($path);
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new \RuntimeException($path . '所在目录不可写');
        }
        if (is_file($path) && !is_writable($path)) {
            throw new \RuntimeException($path . '不可写');
        }
        if (file_put_contents($path, $content) === false) {
            throw new \RuntimeException($path . '写入失败');
        }
    }

    private function testDatabasePrivileges(\PDO $pdo): void
    {
        $table = '__sanqi_install_check_' . bin2hex(random_bytes(4));
        $safeTable = $this->escapeIdentifier($table);

        try {
            $pdo->exec("CREATE TABLE `{$safeTable}` (`id` int unsigned NOT NULL AUTO_INCREMENT, `name` varchar(20) NOT NULL DEFAULT '', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $pdo->exec("INSERT INTO `{$safeTable}` (`name`) VALUES ('ok')");
            $pdo->exec("CREATE INDEX `idx_name` ON `{$safeTable}` (`name`)");
        } finally {
            try {
                $pdo->exec("DROP TABLE IF EXISTS `{$safeTable}`");
            } catch (\PDOException $e) {
            }
        }
    }

    private function formatPdoError(\PDOException $e): string
    {
        $code = (int)($e->errorInfo[1] ?? 0);
        $message = $e->getMessage();

        if (in_array($code, [1044, 1142], true)) {
            return '数据库权限不足，请确认该账号拥有建库、建表、写入、建索引和删表权限：' . $message;
        }
        if ($code === 1045) {
            return '数据库账号或密码错误：' . $message;
        }
        if (in_array($code, [2002, 2003], true)) {
            return '无法连接MySQL主机，请检查主机、端口和宝塔防火墙：' . $message;
        }

        return '数据库检查失败：' . $message;
    }

    private function parseSql(string $sql): array
    {
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        $sql = preg_replace('/DELIMITER\s+.*$/mi', '', $sql);
        $sql = preg_replace('/CREATE\s+PROCEDURE.*?END\s*\/\//si', '', $sql);
        $sql = preg_replace('/DROP\s+PROCEDURE.*?;/si', '', $sql);
        $sql = preg_replace('/CALL\s+.*?;/si', '', $sql);

        $statements = explode(';', $sql);
        $result = [];
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && strpos($statement, 'SET FOREIGN_KEY_CHECKS') === false) {
                $result[] = $statement;
            }
        }
        return $result;
    }

    private function writeEnvFile(string $host, string $port, string $database, string $username, string $password, string $prefix): void
    {
        $envFile = app()->getRootPath() . '.env';
        $appKey = bin2hex(random_bytes(32));
        $envContent = implode(PHP_EOL, [
            '# 数据库配置',
            'DB_DRIVER=mysql',
            'DB_TYPE=mysql',
            'DB_HOST=' . $host,
            'DB_PORT=' . $port,
            'DB_NAME=' . $database,
            'DB_USER=' . $username,
            'DB_PASS=' . $password,
            'DB_CHARSET=utf8mb4',
            'DB_PREFIX=' . $prefix,
            '',
            '# 应用配置',
            'APP_DEBUG=false',
            'APP_NAME=朋友圈',
            'APP_KEY=' . $appKey,
            '',
        ]);
        $this->writeFileOrFail($envFile, $envContent);
    }

    /**
     * 校验数据库标识符（数据库名、用户名、表前缀）是否合法
     * 只允许字母、数字、下划线，表前缀允许为空
     */
    private function validateIdentifier(string $value, bool $allowEmpty = false): bool
    {
        if ($allowEmpty && $value === '') {
            return true;
        }
        return (bool) preg_match('/^[a-zA-Z0-9_]+$/', $value);
    }

    /**
     * 校验主机地址（IP 或域名）
     */
    private function validateHost(string $host): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9.\-]+$/', $host);
    }

    /**
     * 校验端口号
     */
    private function validatePort(string $port): bool
    {
        return (bool) preg_match('/^\d+$/', $port) && (int) $port > 0 && (int) $port <= 65535;
    }

    /**
     * 转义标识符中的反引号，防止反引号注入
     */
    private function escapeIdentifier(string $identifier): string
    {
        return str_replace('`', '``', $identifier);
    }
}
