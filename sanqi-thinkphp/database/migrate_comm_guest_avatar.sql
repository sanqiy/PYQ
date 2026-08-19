-- Add cached visitor avatar field for guest comments.

ALTER TABLE `comm`
  ADD COLUMN `coimg` varchar(500) NOT NULL DEFAULT '' COMMENT '评论者头像' AFTER `courl`;
