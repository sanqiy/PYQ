-- Migrate user QR code columns for payment tips.
-- Safe to run on existing databases.

ALTER TABLE `user`
  ADD COLUMN IF NOT EXISTS `alipay_qr` varchar(500) NOT NULL DEFAULT '' COMMENT '支付宝收款码' AFTER `url`,
  ADD COLUMN IF NOT EXISTS `wechat_qr` varchar(500) NOT NULL DEFAULT '' COMMENT '微信收款码' AFTER `alipay_qr`;
