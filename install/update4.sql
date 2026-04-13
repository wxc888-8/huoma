INSERT INTO `dwz_config` VALUES ('second_jump', '0');
INSERT INTO `dwz_config` VALUES ('charge_tcn', '0');
INSERT INTO `dwz_config` VALUES ('charge_urlcn', '0');
INSERT INTO `dwz_config` VALUES ('dwz_price', '1');
INSERT INTO `dwz_config` VALUES ('discount', '0.9');
INSERT INTO `dwz_config` VALUES ('dwz_token', '');
INSERT INTO `dwz_config` VALUES ('vip_fh', '0');
INSERT INTO `dwz_config` VALUES ('vip_zl', '0');

ALTER TABLE `dwz_points` CHANGE `days` `number` int(30);
ALTER TABLE `dwz_user` ADD COLUMN `create_num` int(50) DEFAULT 0;