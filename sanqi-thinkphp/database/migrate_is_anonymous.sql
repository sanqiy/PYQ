-- Migrate anonymous article state from essay.ptpname to essay.is_anonymous,
-- then remove the old essay.ptpname column.
--
-- Run once on an existing database before deploying code that no longer reads ptpname.
--
-- The hex value below is UTF-8 for "anonymous user" in Chinese:
--   E58CBFE5908DE794A8E688B7
-- It avoids mojibake when this file is opened in a non-UTF-8 console.

ALTER TABLE `essay`
  ADD COLUMN `is_anonymous` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'is anonymous post' AFTER `ptpuser`;

UPDATE `essay`
SET `is_anonymous` = 1
WHERE CONVERT(`ptpname` USING utf8mb4) = CONVERT(0xE58CBFE5908DE794A8E688B7 USING utf8mb4);

ALTER TABLE `essay`
  DROP COLUMN `ptpname`;
