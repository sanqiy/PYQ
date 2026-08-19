-- Add cached visitor profile fields for guest likes.
-- Logged-in users still resolve their display name and avatar from the user table.

ALTER TABLE `lcke`
  ADD COLUMN `lname` varchar(100) NOT NULL DEFAULT '' COMMENT '点赞者昵称' AFTER `luser`,
  ADD COLUMN `limg` varchar(500) NOT NULL DEFAULT '' COMMENT '点赞者头像' AFTER `lname`;
