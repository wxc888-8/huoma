INSERT INTO `dwz_config` VALUES ('htaccess', '1');
INSERT INTO `dwz_config` VALUES ('dwz_type', 'tcn');
INSERT INTO `dwz_config` VALUES ('pattern', '1');

ALTER TABLE `dwz_url` ADD COLUMN `pattern` int(1) DEFAULT 1;
ALTER TABLE `dwz_url` ADD COLUMN `title` varchar(100);

DROP TABLE IF EXISTS `dwz_black`;
CREATE TABLE `dwz_black`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NULL DEFAULT NULL,
  `type` int(1) NULL DEFAULT 0,
  `addtime` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;