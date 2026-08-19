<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\service\AdminLogService;
use app\service\AdminSecurityService;
use app\service\DatabaseService;
use app\service\MigrationService;
use think\facade\Db;

class DatabaseTools extends Base
{
    public function index()
    {
        return view('admin/database', array_merge([
            'siteConfig' => $this->getSiteConfig(),
            'user' => $this->getUser(),
            'backups' => $this->listBackups(),
            'backupDir' => $this->backupDir(),
            'pageTitle' => '数据库备份',
        ], $this->getAdminViewData()));
    }

    public function backup()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $result = DatabaseService::backup();
        AdminLogService::operation('database.backup', 'database', ['file' => $result['filename'] ?? '']);
        return $this->success('备份成功', [
            'filename' => $result['filename'],
            'size' => $this->formatBytes($result['size'] ?? 0),
        ]);
    }

    public function restore()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }
        if (!AdminSecurityService::verifyAdminPassword($this->request->post('admin_password', ''))) {
            return $this->error('管理员密码错误');
        }

        $file = $this->safeFilename($this->request->post('file', ''));
        $path = $this->backupDir() . $file;
        if ($file === '' || !is_file($path)) {
            return $this->error('备份文件不存在');
        }

        $this->executeSqlFile($path);
        AdminLogService::operation('database.restore', 'database', ['file' => $file]);
        return $this->success('恢复成功');
    }

    public function delete()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }
        if (!AdminSecurityService::verifyAdminPassword($this->request->post('admin_password', ''))) {
            return $this->error('管理员密码错误');
        }

        $file = $this->safeFilename($this->request->post('file', ''));
        $path = $this->backupDir() . $file;
        if ($file === '' || !is_file($path)) {
            return $this->error('备份文件不存在');
        }

        @unlink($path);
        AdminLogService::operation('database.delete_backup', 'database', ['file' => $file]);
        return $this->success('删除成功');
    }

    public function migrate()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $result = MigrationService::runPending();
        AdminLogService::operation('database.migrate', 'database', [
            'executed' => $result['executed'],
            'skipped' => $result['skipped'],
            'failed' => $result['failed'],
            'cache_cleared' => $result['cache_cleared'],
        ]);

        $msg = '迁移完成：执行 ' . $result['executed'] . ' 个';
        if ($result['skipped'] > 0) {
            $msg .= '，跳过 ' . $result['skipped'] . ' 个';
        }
        if ($result['failed'] > 0) {
            $msg .= '，失败 ' . $result['failed'] . ' 个';
        }
        $msg .= '，清理缓存文件 ' . $result['cache_cleared'] . ' 个';
        return $this->success($msg, $result);
    }

    private function backupDir(): string
    {
        $dir = app()->getRuntimePath() . 'backup' . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    private function listBackups(): array
    {
        $files = glob($this->backupDir() . '*.sql') ?: [];
        rsort($files);
        $rows = [];
        foreach ($files as $path) {
            $rows[] = [
                'name' => basename($path),
                'size' => $this->formatBytes(filesize($path) ?: 0),
                'time' => date('Y-m-d H:i:s', filemtime($path) ?: time()),
            ];
        }
        return $rows;
    }

    private function executeSqlFile(string $path): void
    {
        $sql = (string)file_get_contents($path);
        $pdo = Db::connect()->getPdo();
        foreach ($this->parseSqlStatements($sql) as $statement) {
            $pdo->exec($statement);
        }
    }

    private function parseSqlStatements(string $sql): array
    {
        $statements = [];
        $current = '';
        foreach (explode("\n", $sql) as $line) {
            $trimmed = ltrim($line);
            if ($trimmed === '' || strpos($trimmed, '--') === 0) {
                continue;
            }
            $current .= $line . "\n";
            if (substr(rtrim($line), -1) === ';') {
                $stmt = trim($current);
                if ($stmt !== '' && $stmt !== ';') {
                    $statements[] = $stmt;
                }
                $current = '';
            }
        }

        $stmt = trim($current);
        if ($stmt !== '' && $stmt !== ';') {
            $statements[] = $stmt;
        }
        return $statements;
    }

    private function safeFilename(string $file): string
    {
        $file = basename($file);
        return preg_match('/^backup_[0-9]{14}\.sql$/', $file) ? $file : '';
    }

    private function formatBytes(int $bytes): string
    {
        $bytes = max(0, $bytes);
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, $i === 0 ? 0 : 2) . ' ' . $units[$i];
    }
}
