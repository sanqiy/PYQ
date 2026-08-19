-- 用户置顶字段迁移
-- 为 essay 表添加 user_top 字段，支持文章作者自行置顶文章
ALTER TABLE `essay` ADD COLUMN `user_top` tinyint(1) NOT NULL DEFAULT 0 COMMENT '用户置顶(0=否 1=是)' AFTER `cid`;
ALTER TABLE `essay` ADD KEY `idx_essay_user_top` (`user_top`, `ptpuser`, `ptpaud`, `ptpys`);
