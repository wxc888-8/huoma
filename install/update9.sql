INSERT INTO `dwz_config` VALUES ('tz_template', 'jump1');
INSERT INTO `dwz_config` VALUES ('vip_edit', '1');
INSERT INTO `dwz_config` VALUES ('limit_url2', '100');
INSERT INTO `dwz_config` VALUES ('limit_url3', '500');

ALTER TABLE `dwz_url` MODIFY COLUMN `dwz` varchar(100);
ALTER TABLE `dwz_url` ADD COLUMN `lasttime` datetime DEFAULT NULL;

UPDATE `dwz_url` set `lasttime`='2020-12-1 00:00:00';