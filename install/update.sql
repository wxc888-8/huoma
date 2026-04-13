DROP TABLE IF EXISTS `url_safe`;
DROP TABLE IF EXISTS `visitors`;

ALTER TABLE `config` RENAME TO `dwz_config`;
ALTER TABLE `log_list` RENAME TO `dwz_log`;
ALTER TABLE `url_list` RENAME TO `dwz_url`;
ALTER TABLE `user_list` RENAME TO `dwz_user`;

DROP TABLE IF EXISTS `dwz_cache`;
create table `dwz_cache` (
`k` varchar(32) NOT NULL,
`v` text NULL,
PRIMARY KEY  (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dwz_domain`;
CREATE TABLE `dwz_domain`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(100) NULL DEFAULT NULL,
  `type` int(1) NULL DEFAULT 0,
  `qqsafe` int(1) NULL DEFAULT 1,
  `wxsafe` int(1) NULL DEFAULT 1,
  `dysafe` int(1) NULL DEFAULT 1,
  `addtime` datetime NULL DEFAULT NULL,
  `state` int(1) NULL DEFAULT 1,
  `is_https` int(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dwz_km`;
CREATE TABLE `dwz_km`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `km` varchar(15) NULL DEFAULT NULL,
  `status` int(1) NULL DEFAULT 0,
  `usetime` datetime NULL DEFAULT NULL,
  `addtime` datetime NULL DEFAULT NULL,
  `days` int(255) NULL DEFAULT NULL,
  `uid` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dwz_points`;
CREATE TABLE `dwz_points`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `action` varchar(10) NULL,
  `point` decimal(10, 2) NOT NULL,
  `bz` varchar(50) NULL,
  `addtime` datetime NULL DEFAULT NULL,
  `orderid` int(64) NULL DEFAULT NULL,
  `status` int(1) NULL DEFAULT 0,
  `days` int(10) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dwz_visitors`;
CREATE TABLE `dwz_visitors`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NULL DEFAULT NULL,
  `urlid` int(11) NULL DEFAULT NULL,
  `ip` varchar(20) NULL DEFAULT NULL,
  `froms` varchar(50) NULL DEFAULT NULL,
  `addtime` datetime NULL DEFAULT NULL,
  `system` varchar(30) NULL DEFAULT NULL,
  `pageview` varchar(100) NULL DEFAULT NULL,
  `source_link` varchar(100) NULL DEFAULT NULL,
  `browser` varchar(30) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `dwz_url` ADD COLUMN `view` int(20) DEFAULT 0;
ALTER TABLE `dwz_user` ADD COLUMN `vip` datetime;
ALTER TABLE `dwz_user` ADD COLUMN `name` varchar(50);
ALTER TABLE `dwz_user` ADD COLUMN `img` varchar(50);

ALTER TABLE `dwz_url` MODIFY `id` varchar(11);
ALTER TABLE `dwz_url` MODIFY `uid` int(11);
ALTER TABLE `dwz_user` MODIFY `id` int(11);
ALTER TABLE `dwz_user` MODIFY `user` varchar(20);
ALTER TABLE `dwz_user` MODIFY `pwd` varchar(30);
ALTER TABLE `dwz_user` MODIFY `qq` varchar(12);
ALTER TABLE `dwz_user` MODIFY `mail` varchar(30);
ALTER TABLE `dwz_user` MODIFY `token` varchar(40);
ALTER TABLE `dwz_user` MODIFY `lasttime` datetime;

ALTER TABLE `dwz_url` DROP `urlcn`;
ALTER TABLE `dwz_user` DROP `power`;

ALTER TABLE `dwz_url` CHANGE `tcn` `dwz` varchar(30);
ALTER TABLE `dwz_url` CHANGE `setip` `ip` varchar(30);

UPDATE `dwz_config` SET `k`='kf_qq' WHERE `k`='web_qq';
UPDATE `dwz_config` SET `v`='4000' WHERE `k`='version';

INSERT INTO `dwz_config` VALUES ('statistics', '0');
INSERT INTO `dwz_config` VALUES ('group_link', 'https://jq.qq.com/?_wv=1027&k=5dTIxoK');
INSERT INTO `dwz_config` VALUES ('d_jump', '0');
INSERT INTO `dwz_config` VALUES ('icp', '');
INSERT INTO `dwz_config` VALUES ('jump_5', 'http://www.btstu.cn');