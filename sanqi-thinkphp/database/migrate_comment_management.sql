-- Comment management enhancements: visitor email and edit history.
ALTER TABLE `comm`
  ADD COLUMN `coemail` varchar(255) NOT NULL DEFAULT '' COMMENT '评论者邮箱' AFTER `courl`;

CREATE TABLE IF NOT EXISTS `comment_edit_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ecid` varchar(64) NOT NULL DEFAULT '' COMMENT '评论ID',
  `admin_user` varchar(100) NOT NULL DEFAULT '' COMMENT '编辑管理员',
  `old_text` text NOT NULL COMMENT '编辑前内容',
  `new_text` text NOT NULL COMMENT '编辑后内容',
  `created_at` datetime NOT NULL COMMENT '编辑时间',
  PRIMARY KEY (`id`),
  KEY `idx_comment_edit_ecid` (`ecid`),
  KEY `idx_comment_edit_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
