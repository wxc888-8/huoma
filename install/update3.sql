INSERT INTO `dwz_config` VALUES ('default_vip', '0');
INSERT INTO `dwz_config` VALUES ('vip_api', '1');
INSERT INTO `dwz_config` VALUES ('vip_tj', '1');
INSERT INTO `dwz_config` VALUES ('jump1', '1');
INSERT INTO `dwz_config` VALUES ('jump2', '1');
INSERT INTO `dwz_config` VALUES ('jump3', '1');
INSERT INTO `dwz_config` VALUES ('jump4', '1');
INSERT INTO `dwz_config` VALUES ('dwz_pb', '');
INSERT INTO `dwz_config` VALUES ('limit_url', '');

ALTER TABLE `dwz_user` ADD COLUMN `addip` varchar(50);
ALTER TABLE `dwz_user` ADD COLUMN `lastip` varchar(50);
ALTER TABLE `dwz_url` ADD COLUMN `visit` varchar(20);
ALTER TABLE `dwz_url` ADD COLUMN `visiturl` mediumtext;

ALTER TABLE `dwz_points` MODIFY `orderid` int(64);