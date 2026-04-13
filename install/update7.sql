INSERT INTO `dwz_config` VALUES ('default_create', '0');
INSERT INTO `dwz_config` VALUES ('dwz_api', '1');
INSERT INTO `dwz_config` VALUES ('check_price', '1');
INSERT INTO `dwz_config` VALUES ('default_check', '0');

ALTER TABLE `dwz_visitors` MODIFY `id` int(50) AUTO_INCREMENT;

ALTER TABLE `dwz_km` ADD COLUMN `type` int(5) DEFAULT 0;
ALTER TABLE `dwz_user` ADD COLUMN `trial_vip` int(10) DEFAULT 0;
ALTER TABLE `dwz_user` ADD COLUMN `trial_create` int(10) DEFAULT 0;
ALTER TABLE `dwz_user` ADD COLUMN `check_num` int(50) DEFAULT 0;
ALTER TABLE `dwz_url` ADD COLUMN `u_state` int(1) DEFAULT 1;
ALTER TABLE `dwz_url` ADD COLUMN `jumpmb` varchar(50) DEFAULT 'jump1';
ALTER TABLE `dwz_visitors` ADD COLUMN `adddate` date DEFAULT NULL;

DROP TABLE IF EXISTS `dwz_check`;
CREATE TABLE `dwz_check`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text NULL DEFAULT NULL,
  `addtime` datetime NULL DEFAULT NULL,
  `type` int(1) NULL DEFAULT 0,
  `switch` int(1) NULL DEFAULT 1,
  `status` int(1) NULL DEFAULT 1,
  `pl` int(1) NULL DEFAULT 0,
  `num` int(100) NULL DEFAULT 0,
  `lasttime` datetime NULL DEFAULT NULL,
  `uid` int(20) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;