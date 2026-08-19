<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\service;

use think\facade\Db;

class MigrationService
{
    private const TABLE = 'schema_migrations';

    public static function summary(): array
    {
        self::ensureTable();
        $files = self::migrationFiles();
        $records = self::recordsByMigration();
        $pending = [];
        $current = '';

        foreach ($files as $file) {
            $name = basename($file);
            $record = $records[$name] ?? null;
            if ($record && in_array((string)$record['status'], ['success', 'skipped'], true)) {
                $current = $name;
                continue;
            }
            $pending[] = self::fileInfo($file, $record);
        }

        return [
            'current' => $current !== '' ? $current : '未记录',
            'total' => count($files),
            'executed' => count(array_filter($records, function ($row) {
                return in_array((string)$row['status'], ['success', 'skipped'], true);
            })),
            'pending' => $pending,
            'logs' => self::logs(100),
        ];
    }

    public static function runPending(): array
    {
        self::ensureTable();
        $files = self::migrationFiles();
        $records = self::recordsByMigration();
        $results = [];
        $executed = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($files as $file) {
            $name = basename($file);
            $record = $records[$name] ?? null;
            if ($record && in_array((string)$record['status'], ['success', 'skipped'], true)) {
                continue;
            }

            $result = self::runFile($file);
            $results[] = $result;
            if ($result['status'] === 'success') {
                $executed++;
            } elseif ($result['status'] === 'skipped') {
                $skipped++;
            } else {
                $failed++;
                break;
            }
        }

        $cleared = self::clearFieldCache();

        return [
            'executed' => $executed,
            'skipped' => $skipped,
            'failed' => $failed,
            'cache_cleared' => $cleared,
            'results' => $results,
        ];
    }

    public static function logs(int $limit = 100): array
    {
        self::ensureTable();
        return Db::name(self::TABLE)
            ->order('id', 'desc')
            ->limit($limit)
            ->select()
            ->toArray();
    }

    public static function clearFieldCache(): int
    {
        $runtimePath = app()->getRuntimePath();
        $cleared = 0;
        foreach (['cache', 'temp', 'schema'] as $dir) {
            $cleared += self::removePhpFiles($runtimePath . $dir . DIRECTORY_SEPARATOR);
        }
        return $cleared;
    }

    private static function runFile(string $file): array
    {
        $name = basename($file);
        $sql = (string)file_get_contents($file);
        $checksum = hash_file('sha256', $file) ?: '';
        $start = microtime(true);

        if (trim($sql) === '') {
            $result = [
                'migration' => $name,
                'checksum' => $checksum,
                'status' => 'skipped',
                'message' => '空迁移文件',
                'execution_ms' => 0,
            ];
            self::writeRecord($result);
            return $result;
        }

        try {
            $pdo = Db::connect()->getPdo();
            foreach (self::parseStatements($sql) as $statement) {
                $pdo->exec($statement);
            }
            $result = [
                'migration' => $name,
                'checksum' => $checksum,
                'status' => 'success',
                'message' => '执行成功',
                'execution_ms' => (int)round((microtime(true) - $start) * 1000),
            ];
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $result = [
                'migration' => $name,
                'checksum' => $checksum,
                'status' => self::isAlreadyAppliedError($message) ? 'skipped' : 'failed',
                'message' => mb_substr($message, 0, 500, 'UTF-8'),
                'execution_ms' => (int)round((microtime(true) - $start) * 1000),
            ];
        }

        self::writeRecord($result);
        return $result;
    }

    private static function writeRecord(array $result): void
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            'migration' => $result['migration'],
            'checksum' => $result['checksum'],
            'status' => $result['status'],
            'message' => $result['message'],
            'execution_ms' => (int)$result['execution_ms'],
            'started_at' => $now,
            'finished_at' => $now,
        ];

        $existing = Db::name(self::TABLE)->where('migration', $result['migration'])->find();
        if ($existing) {
            Db::name(self::TABLE)->where('id', (int)$existing['id'])->update($data);
        } else {
            Db::name(self::TABLE)->insert($data);
        }
    }

    private static function fileInfo(string $file, ?array $record = null): array
    {
        return [
            'migration' => basename($file),
            'size' => filesize($file) ?: 0,
            'checksum' => hash_file('sha256', $file) ?: '',
            'status' => $record['status'] ?? 'pending',
            'message' => $record['message'] ?? '',
            'mtime' => date('Y-m-d H:i:s', filemtime($file) ?: time()),
        ];
    }

    private static function migrationFiles(): array
    {
        $files = glob(app()->getRootPath() . 'database' . DIRECTORY_SEPARATOR . 'migrate_*.sql') ?: [];
        sort($files, SORT_NATURAL);
        return $files;
    }

    private static function recordsByMigration(): array
    {
        $rows = Db::name(self::TABLE)->select()->toArray();
        $records = [];
        foreach ($rows as $row) {
            $records[(string)$row['migration']] = $row;
        }
        return $records;
    }

    private static function parseStatements(string $sql): array
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

    private static function isAlreadyAppliedError(string $message): bool
    {
        $message = strtolower($message);
        $needles = [
            'duplicate column',
            'duplicate key name',
            'already exists',
            'table exists',
            'check that column/key exists',
        ];
        foreach ($needles as $needle) {
            if (strpos($message, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    private static function removePhpFiles(string $dir): int
    {
        if (!is_dir($dir)) {
            return 0;
        }

        $count = 0;
        $items = scandir($dir);
        if ($items === false) {
            return 0;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . $item;
            if (is_dir($path)) {
                $count += self::removePhpFiles($path . DIRECTORY_SEPARATOR);
                continue;
            }
            if (substr($path, -4) === '.php' && @unlink($path)) {
                $count++;
            }
        }
        return $count;
    }

    private static function ensureTable(): void
    {
        Db::execute(
            "CREATE TABLE IF NOT EXISTS `" . self::tableName() . "` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `migration` varchar(190) NOT NULL,
                `checksum` char(64) NOT NULL DEFAULT '',
                `status` varchar(20) NOT NULL DEFAULT 'pending',
                `message` text NULL,
                `execution_ms` int unsigned NOT NULL DEFAULT 0,
                `started_at` datetime NULL,
                `finished_at` datetime NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_migration` (`migration`),
                KEY `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    private static function tableName(): string
    {
        $default = (string)(config('database.default') ?: 'mysql');
        $prefix = (string)(config('database.connections.' . $default . '.prefix') ?: '');
        return $prefix . self::TABLE;
    }
}
