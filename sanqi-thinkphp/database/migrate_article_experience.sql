-- Content experience enhancements: autosave draft versions.
CREATE TABLE IF NOT EXISTS `article_draft_versions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '作者账号',
  `draft_key` varchar(128) NOT NULL DEFAULT '' COMMENT '草稿会话标识',
  `article_cid` varchar(64) NOT NULL DEFAULT '' COMMENT '关联文章CID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '快照标题',
  `form_data` mediumtext NOT NULL COMMENT '表单快照JSON',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_article_draft_user_key` (`username`, `draft_key`, `id`),
  KEY `idx_article_draft_cid` (`article_cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
