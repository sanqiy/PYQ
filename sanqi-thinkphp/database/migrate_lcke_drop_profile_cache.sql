-- Remove cached liker profile fields from lcke.
-- User display name and avatar are now read from the user table by luser.

ALTER TABLE `lcke`
  DROP COLUMN `lname`,
  DROP COLUMN `limg`;
