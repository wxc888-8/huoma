ALTER TABLE `dwz_points` MODIFY `point` varchar(32);
ALTER TABLE `dwz_pay` ADD COLUMN `domain` varchar(64);
ALTER TABLE `dwz_km` MODIFY `km` varchar(32);

DROP TABLE IF EXISTS `dwz_modify`;
CREATE TABLE `dwz_modify`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `urlid` varchar(50) NOT NULL,
  `addtime` datetime NULL DEFAULT NULL,
  `url1` text NULL DEFAULT NULL,
  `url2` text NULL DEFAULT NULL,
  `dwz` text NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;