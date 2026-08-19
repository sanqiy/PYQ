-- 迁移：为 user 表添加 username/email 唯一约束，为 lcke 表添加 (lwz,luser) 唯一约束
-- 执行前请先检查是否存在重复数据，若存在需先清理

SET NAMES utf8mb4;

-- ========================================
-- 1. 检查 user 表是否有重复 username
-- ========================================
-- SELECT username, COUNT(*) AS cnt FROM `user` GROUP BY username HAVING cnt > 1;

-- ========================================
-- 2. 检查 user 表是否有重复 email（排除空邮箱）
-- ========================================
-- SELECT email, COUNT(*) AS cnt FROM `user` WHERE email != '' GROUP BY email HAVING cnt > 1;

-- ========================================
-- 3. 检查 lcke 表是否有重复点赞
-- ========================================
-- SELECT lwz, luser, COUNT(*) AS cnt FROM `lcke` GROUP BY lwz, luser HAVING cnt > 1;

-- 如果上面的查询有结果，需要先清理重复数据再执行下面的迁移

-- ========================================
-- 执行迁移
-- ========================================

-- user 表：普通索引改为唯一索引
ALTER TABLE `user` DROP INDEX `idx_user_username`, ADD UNIQUE INDEX `idx_user_username` (`username`);
ALTER TABLE `user` DROP INDEX `idx_user_email`, ADD UNIQUE INDEX `idx_user_email` (`email`);

-- lcke 表：联合索引改为唯一索引
ALTER TABLE `lcke` DROP INDEX `idx_lcke_lwz_luser`, ADD UNIQUE INDEX `idx_lcke_lwz_luser` (`lwz`, `luser`);
