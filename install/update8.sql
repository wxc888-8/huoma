DELETE FROM `dwz_config` WHERE `k`='charge_dwz';
DELETE FROM `dwz_config` WHERE `k`='charge_dwz_list';
DELETE FROM `dwz_config` WHERE `k`='dwz_api';
DELETE FROM `dwz_config` WHERE `k`='dwz_pb';
DELETE FROM `dwz_config` WHERE `k`='charge_all';
DELETE FROM `dwz_config` WHERE `k`='cache';
DELETE FROM `dwz_config` WHERE `k`='charge_tcn';
DELETE FROM `dwz_config` WHERE `k`='charge_urlcn';

ALTER TABLE `dwz_user` ADD COLUMN `pattern` int(1) DEFAULT 0;
ALTER TABLE `dwz_user` ADD COLUMN `dwz_type` varchar(20) DEFAULT NULL;

DROP TABLE IF EXISTS `dwz_api`;
CREATE TABLE `dwz_api` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NULL DEFAULT NULL,
    `keyname` varchar(100) NULL DEFAULT NULL,
    `bz` text NULL DEFAULT NULL,
    `addtime` datetime NULL DEFAULT NULL,
    `type` int(1) NULL DEFAULT 0,
    `token` varchar(50) NULL DEFAULT NULL,
    `status` int(1) NULL DEFAULT 1,
    `domain` varchar(100) NULL DEFAULT NULL,
    `num` int(10) NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

INSERT INTO `dwz_api` (`id`, `name`, `keyname`, `bz`, `addtime`, `type`, `token`, `status`, `domain`, `num`) VALUES
(1, '本站', 'bz', '', '2022-03-08 18:17:51', 0, '', 1, 'http://aliyuncs.com', 0);