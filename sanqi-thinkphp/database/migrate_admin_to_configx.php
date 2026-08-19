<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

/**
 * 数据迁移脚本：将 admin 表字段迁移到 configx 键值表
 *
 * 使用方法：
 *   php database/migrate_admin_to_configx.php
 *
 * 迁移内容：
 *   1. 读取 admin 表单行数据
 *   2. 将每个字段（id/username 除外）写入 configx 表
 *   3. 为 configx.title 添加唯一索引
 *   4. 删除 admin 表中已迁移的列
 *
 * 幂等：检测 admin 表列数判断是否已迁移
 */

chdir(__DIR__ . '/..');

// 加载 Composer autoloader
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    echo "错误：找不到 vendor/autoload.php，请先运行 composer install\n";
    exit(1);
}

// 读取 .env
$envFile = '.env';
if (!file_exists($envFile)) {
    echo "错误：找不到 .env 文件\n";
    exit(1);
}

$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    if (strpos($line, '=') !== false) {
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v);
    }
}

$dbHost = $env['DB_HOST'] ?? '127.0.0.1';
$dbPort = $env['DB_PORT'] ?? '3306';
$dbName = $env['DB_NAME'] ?? 'sanqi';
$dbUser = $env['DB_USER'] ?? 'root';
$dbPass = $env['DB_PASS'] ?? '';
$dbCharset = $env['DB_CHARSET'] ?? 'utf8mb4';

try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset={$dbCharset}";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    echo "数据库连接失败: " . $e->getMessage() . "\n";
    exit(1);
}

// 检查是否已迁移
$columns = $pdo->query("SHOW COLUMNS FROM `admin`")->fetchAll();
$columnNames = array_column($columns, 'Field');
if (count($columnNames) <= 2) {
    echo "已迁移，admin 表仅有 " . implode(', ', $columnNames) . " 列。\n";
    exit(0);
}

echo "开始迁移...\n";
echo "admin 表当前有 " . count($columnNames) . " 列: " . implode(', ', $columnNames) . "\n\n";

$skip = ['id', 'username'];
$migrateCols = array_diff($columnNames, $skip);

$pdo->beginTransaction();

try {
    // 1. 读取 admin 行
    $row = $pdo->query("SELECT * FROM `admin` LIMIT 1")->fetch();
    if (!$row) {
        echo "admin 表无数据，跳过迁移。\n";
        $pdo->rollBack();
        exit(0);
    }

    // 2. 写入 configx
    $stmt = $pdo->prepare("INSERT INTO `configx` (`title`, `text`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `text` = VALUES(`text`)");
    $count = 0;
    foreach ($migrateCols as $col) {
        $value = (string)($row[$col] ?? '');
        $stmt->execute([$col, json_encode($value, JSON_UNESCAPED_UNICODE)]);
        $count++;
        echo "  迁移字段: {$col} = " . mb_substr($value, 0, 50) . "\n";
    }
    echo "\n已写入 {$count} 个字段到 configx 表。\n";

    // 3. 添加唯一索引（如果不存在）
    $indexCheck = $pdo->query("SHOW INDEX FROM `configx` WHERE Key_name = 'idx_configx_title'")->fetchAll();
    if (empty($indexCheck)) {
        $pdo->exec("ALTER TABLE `configx` ADD UNIQUE KEY `idx_configx_title` (`title`)");
        echo "已为 configx.title 添加唯一索引。\n";
    } else {
        echo "configx.title 唯一索引已存在。\n";
    }

    // 4. 删除 admin 表多余列（DDL 会隐式提交事务，无法回滚）
    foreach ($migrateCols as $col) {
        $safe = preg_replace('/[^a-zA-Z0-9_]/', '', $col);
        $pdo->exec("ALTER TABLE `admin` DROP COLUMN `{$safe}`");
    }
    echo "已从 admin 表删除 " . count($migrateCols) . " 列。\n";

    // DDL 已隐式提交，尝试 commit 仅处理数据写入部分
    if ($pdo->inTransaction()) {
        $pdo->commit();
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "\n迁移失败: " . $e->getMessage() . "\n";
    exit(1);
}

// 验证（在事务外执行）
try {
    $finalCols = $pdo->query("SHOW COLUMNS FROM `admin`")->fetchAll();
    $configxCount = $pdo->query("SELECT COUNT(*) AS cnt FROM `configx`")->fetch()['cnt'];

    echo "\n迁移完成！\n";
    echo "admin 表剩余列: " . implode(', ', array_column($finalCols, 'Field')) . "\n";
    echo "configx 表共 {$configxCount} 行配置。\n";
} catch (Exception $e) {
    echo "\n迁移已完成，但验证查询出错: " . $e->getMessage() . "\n";
}
