<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\service;

use think\facade\Db;

/**
 * 数据库优化服务类
 */
class DatabaseService
{
    private static function prefixTable(string $table): string
    {
        $prefix = config('database.prefix') ?? '';
        return $prefix . $table;
    }

    /**
     * 优化数据库表
     */
    public static function optimizeTables()
    {
        $tables = ['admin', 'user', 'essay', 'comm', 'lcke', 'message', 'link', 'configx'];
        $results = [];

        foreach ($tables as $table) {
            $fullName = self::prefixTable($table);
            try {
                Db::execute("OPTIMIZE TABLE `{$fullName}`");
                $results[$table] = 'success';
            } catch (\Exception $e) {
                $results[$table] = 'failed';
            }
        }

        return $results;
    }

    /**
     * 修复数据库表
     */
    public static function repairTables()
    {
        $tables = ['admin', 'user', 'essay', 'comm', 'lcke', 'message', 'link', 'configx'];
        $results = [];

        foreach ($tables as $table) {
            $fullName = self::prefixTable($table);
            try {
                Db::execute("REPAIR TABLE `{$fullName}`");
                $results[$table] = 'success';
            } catch (\Exception $e) {
                $results[$table] = 'failed';
            }
        }

        return $results;
    }

    /**
     * 检查数据库表状态
     */
    public static function checkTables()
    {
        $tables = ['admin', 'user', 'essay', 'comm', 'lcke', 'message', 'link', 'configx'];
        $results = [];

        foreach ($tables as $table) {
            $fullName = self::prefixTable($table);
            $result = Db::query("CHECK TABLE `{$fullName}`");
            $row = $result[0] ?? [];
            $results[$table] = $row['Msg_text'] ?? 'unknown';
        }

        return $results;
    }

    /**
     * 获取数据库大小
     */
    public static function getDatabaseSize()
    {
        $dbName = Db::scalar("SELECT DATABASE()");
        $size = Db::scalar(
            "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size FROM information_schema.tables WHERE table_schema = ?",
            [$dbName]
        );

        return $size ?: 0;
    }

    /**
     * 获取表统计信息
     */
    public static function getTableStats()
    {
        $dbName = Db::scalar("SELECT DATABASE()");
        $tables = Db::select(
            "SELECT table_name, table_rows, data_length, index_length FROM information_schema.tables WHERE table_schema = ?",
            [$dbName]
        );

        $stats = [];
        foreach ($tables as $table) {
            $stats[$table['table_name']] = [
                'rows' => $table['table_rows'],
                'data_size' => round($table['data_length'] / 1024 / 1024, 2),
                'index_size' => round($table['index_length'] / 1024 / 1024, 2)
            ];
        }

        return $stats;
    }

    /**
     * 清理过期数据
     */
    public static function cleanExpiredData()
    {
        $cleaned = 0;

        // 清理已删除的消息（30天前）
        $result = \app\model\Message::where('msg', -1)
            ->where('ftime', '<', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->delete();
        $cleaned += $result;

        // 清理过期的封禁
        \app\model\User::where('bantime', '!=', 'true')
            ->where('bantime', '<', date('Y-m-d'))
            ->where('ban', 1)
            ->update(['ban' => 0, 'bantime' => '']);

        return $cleaned;
    }

    /**
     * 备份数据库（分页导出，避免内存溢出）
     */
    public static function backup()
    {
        $dbNameRow = Db::query("SELECT DATABASE() AS db");
        $dbName = $dbNameRow[0]['db'] ?? '';
        $tables = Db::query("SHOW TABLES");

        // 保存备份文件
        $backupDir = app()->getRuntimePath() . 'backup/';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = 'backup_' . date('YmdHis') . '.sql';
        $filepath = $backupDir . $filename;

        $fp = fopen($filepath, 'w');
        if (!$fp) {
            throw new \RuntimeException('无法创建备份文件');
        }

        fwrite($fp, "-- Database Backup\n");
        fwrite($fp, "-- Date: " . date('Y-m-d H:i:s') . "\n");
        fwrite($fp, "-- Database: {$dbName}\n\n");
        fwrite($fp, "SET NAMES utf8mb4;\n");
        fwrite($fp, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

        $connection = Db::getConnection()->getPdo();
        $chunkSize = 500;

        foreach ($tables as $table) {
            $tableName = $table["Tables_in_{$dbName}"];

            // 获取表结构
            $createTable = Db::query("SHOW CREATE TABLE `{$tableName}`");
            $createRow = $createTable[0] ?? [];
            fwrite($fp, "-- Table: {$tableName}\n");
            fwrite($fp, "DROP TABLE IF EXISTS `{$tableName}`;\n");
            fwrite($fp, ($createRow['Create Table'] ?? '') . ";\n\n");

            // 获取总行数
            $countRow = Db::query("SELECT COUNT(*) AS cnt FROM `{$tableName}`");
            $total = intval($countRow[0]['cnt'] ?? 0);

            if ($total === 0) {
                continue;
            }

            fwrite($fp, "-- Data for {$tableName} ({$total} rows)\n");

            // 分页导出数据
            $offset = 0;
            $batchNum = 0;
            while ($offset < $total) {
                $rows = Db::query("SELECT * FROM `{$tableName}` LIMIT {$offset}, {$chunkSize}");
                if (empty($rows)) {
                    break;
                }

                $values = [];
                foreach ($rows as $row) {
                    $rowValues = array_map(function ($v) use ($connection) {
                        return $v === null ? 'NULL' : $connection->quote($v);
                    }, array_values($row));
                    $values[] = '(' . implode(', ', $rowValues) . ')';
                }

                if (!empty($values)) {
                    fwrite($fp, "INSERT INTO `{$tableName}` VALUES\n");
                    fwrite($fp, implode(",\n", $values) . ";\n\n");
                }

                $offset += $chunkSize;
                $batchNum++;
                unset($rows, $values);
            }
        }

        fwrite($fp, "SET FOREIGN_KEY_CHECKS = 1;\n");
        fclose($fp);

        // 设置备份文件权限（仅所有者可读写）
        @chmod($filepath, 0600);

        $size = filesize($filepath) ?: 0;

        return ['filename' => $filename, 'path' => $filepath, 'size' => $size];
    }
}
