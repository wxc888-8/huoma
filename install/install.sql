DROP TABLE IF EXISTS `dwz_config`;
create table `dwz_config` (
`k` varchar(32) NOT NULL,
`v` text NULL,
PRIMARY KEY  (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `dwz_config` VALUES ('admin_user', 'admin');
INSERT INTO `dwz_config` VALUES ('admin_pwd', '123456');
INSERT INTO `dwz_config` VALUES ('domain', 'd.com');
INSERT INTO `dwz_config` VALUES ('shixiao_tz', '30');
INSERT INTO `dwz_config` VALUES ('shixiao_zl', '30');
INSERT INTO `dwz_config` VALUES ('shixiao_url', 'https://mp.weixin.qq.com/wxawap/wxareadtemplate?errtype=link_expired&t=weapp%2Furl_scheme');
INSERT INTO `dwz_config` VALUES ('web_name', '短网址系统');
INSERT INTO `dwz_config` VALUES ('uid', '10001');
INSERT INTO `dwz_config` VALUES ('keywords', '短网址系统，短网址生成');
INSERT INTO `dwz_config` VALUES ('description', '短网址系统-短网址生成');
INSERT INTO `dwz_config` VALUES ('kf_qq', '10001');
INSERT INTO `dwz_config` VALUES ('template', 'blum');
INSERT INTO `dwz_config` VALUES ('index_bg', '2');
INSERT INTO `dwz_config` VALUES ('is_https', '0');
INSERT INTO `dwz_config` VALUES ('is_reg', '1');
INSERT INTO `dwz_config` VALUES ('statistics', '0');
INSERT INTO `dwz_config` VALUES ('group_link', '');
INSERT INTO `dwz_config` VALUES ('d_jump', '0');
INSERT INTO `dwz_config` VALUES ('forcelogin', '0');
INSERT INTO `dwz_config` VALUES ('gg1', '公告1');
INSERT INTO `dwz_config` VALUES ('gg2', '公告2');
INSERT INTO `dwz_config` VALUES ('gg3', '公告3');
INSERT INTO `dwz_config` VALUES ('icp', '');
INSERT INTO `dwz_config` VALUES ('mail_name', '');
INSERT INTO `dwz_config` VALUES ('mail_port', '465');
INSERT INTO `dwz_config` VALUES ('mail_pwd', '');
INSERT INTO `dwz_config` VALUES ('mail_smtp', 'smtp.qq.com');
INSERT INTO `dwz_config` VALUES ('htaccess', '1');
INSERT INTO `dwz_config` VALUES ('dwz_type', 'btfxw');
INSERT INTO `dwz_config` VALUES ('pattern', '1');
INSERT INTO `dwz_config` VALUES ('version', '4009');
INSERT INTO `dwz_config` VALUES ('alipay_api', '0');
INSERT INTO `dwz_config` VALUES ('qqpay_api', '0');
INSERT INTO `dwz_config` VALUES ('wxpay_api', '0');
INSERT INTO `dwz_config` VALUES ('epay_url', '');
INSERT INTO `dwz_config` VALUES ('epay_pid', '');
INSERT INTO `dwz_config` VALUES ('epay_key', '');
INSERT INTO `dwz_config` VALUES ('codepay_id', '');
INSERT INTO `dwz_config` VALUES ('codepay_key', '');
INSERT INTO `dwz_config` VALUES ('vip_month', '5');
INSERT INTO `dwz_config` VALUES ('vip_quarter', '13');
INSERT INTO `dwz_config` VALUES ('vip_year', '50');
INSERT INTO `dwz_config` VALUES ('alipay_appid', '');
INSERT INTO `dwz_config` VALUES ('alipay_publickey', '');
INSERT INTO `dwz_config` VALUES ('alipay_privatekey', '');
INSERT INTO `dwz_config` VALUES ('qqpay_mchid', '');
INSERT INTO `dwz_config` VALUES ('qqpay_key', '');
INSERT INTO `dwz_config` VALUES ('wxpay_appid', '');
INSERT INTO `dwz_config` VALUES ('wxpay_key', '');
INSERT INTO `dwz_config` VALUES ('wxpay_mchid', '');
INSERT INTO `dwz_config` VALUES ('wxpay_appsecret', '');
INSERT INTO `dwz_config` VALUES ('wxpay_domain', '');
INSERT INTO `dwz_config` VALUES ('link_length', '6');
INSERT INTO `dwz_config` VALUES ('index_top', '');
INSERT INTO `dwz_config` VALUES ('index_bottom', '');
INSERT INTO `dwz_config` VALUES ('default_vip', '0');
INSERT INTO `dwz_config` VALUES ('vip_api', '1');
INSERT INTO `dwz_config` VALUES ('vip_tj', '1');
INSERT INTO `dwz_config` VALUES ('jump1', '1');
INSERT INTO `dwz_config` VALUES ('jump2', '1');
INSERT INTO `dwz_config` VALUES ('jump3', '1');
INSERT INTO `dwz_config` VALUES ('jump4', '1');
INSERT INTO `dwz_config` VALUES ('limit_url', '');
INSERT INTO `dwz_config` VALUES ('second_jump', '0');
INSERT INTO `dwz_config` VALUES ('dwz_price', '1');
INSERT INTO `dwz_config` VALUES ('discount', '0.9');
INSERT INTO `dwz_config` VALUES ('dwz_token', '');
INSERT INTO `dwz_config` VALUES ('vip_fh', '0');
INSERT INTO `dwz_config` VALUES ('vip_zl', '0');
INSERT INTO `dwz_config` VALUES ('qqdomaincheck', '0');
INSERT INTO `dwz_config` VALUES ('wxdomaincheck', '0');
INSERT INTO `dwz_config` VALUES ('outqqdomain', '0');
INSERT INTO `dwz_config` VALUES ('outwxdomain', '0');
INSERT INTO `dwz_config` VALUES ('mail_recv', '');
INSERT INTO `dwz_config` VALUES ('domainsetemail', '0');
INSERT INTO `dwz_config` VALUES ('app_alert', '');
INSERT INTO `dwz_config` VALUES ('default_create', '0');
INSERT INTO `dwz_config` VALUES ('check_price', '1');
INSERT INTO `dwz_config` VALUES ('default_check', '0');
INSERT INTO `dwz_config` VALUES ('tz_template', 'jump1');
INSERT INTO `dwz_config` VALUES ('vip_edit', '1');
INSERT INTO `dwz_config` VALUES ('limit_url2', '100');
INSERT INTO `dwz_config` VALUES ('limit_url3', '500');

DROP TABLE IF EXISTS `dwz_cache`;
create table `dwz_cache` (
`k` varchar(32) NOT NULL,
`v` text NULL,
PRIMARY KEY  (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dwz_user`;
CREATE TABLE `dwz_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(20) NOT NULL,
  `pwd` varchar(32) NOT NULL,
  `addtime` datetime DEFAULT NULL,
  `vip` datetime DEFAULT NULL,
  `qq` varchar(12) DEFAULT NULL,
  `state` int(1) NOT NULL DEFAULT '1',
  `lasttime` datetime DEFAULT NULL,
  `mail` varchar(30) DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL,
  `addip` varchar(50) DEFAULT NULL,
  `lastip` varchar(50) DEFAULT NULL,
  `create_num` int(50) DEFAULT 0,
  `trial_vip` int(10) DEFAULT 0,
  `trial_create` int(10) DEFAULT 0,
  `check_num` int(50) DEFAULT 0,
  `pattern` int(1) DEFAULT 0,
  `dwz_type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=10001;

DROP TABLE IF EXISTS `dwz_url`;
CREATE TABLE `dwz_url`  (
  `id` varchar(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `addtime` datetime DEFAULT NULL,
  `url` mediumtext NULL,
  `dwz` varchar(100) NULL DEFAULT NULL,
  `qqjump` mediumtext NULL,
  `wxjump` mediumtext NULL,
  `alijump` mediumtext NULL,
  `ip` varchar(30) NULL DEFAULT NULL,
  `state` int(1) NULL DEFAULT 1,
  `remarks` mediumtext NULL,
  `pwd` varchar(30) NULL DEFAULT NULL,
  `deltime` datetime NULL DEFAULT NULL,
  `view` int(20) NULL DEFAULT 0,
  `pattern` int(1) NULL DEFAULT 1,
  `title` varchar(100) NULL,
  `domain` varchar(200) NULL,
  `visit` varchar(20) NULL,
  `visiturl` mediumtext NULL,
  `u_state` int(1) DEFAULT 1,
  `jumpmb` varchar(50) DEFAULT 'jump1',
  `lasttime` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dwz_black`;
CREATE TABLE `dwz_black`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NULL DEFAULT NULL,
  `type` int(1) NULL DEFAULT 0,
  `addtime` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
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
  `km` varchar(32) NULL DEFAULT NULL,
  `status` int(1) NULL DEFAULT 0,
  `usetime` datetime NULL DEFAULT NULL,
  `addtime` datetime NULL DEFAULT NULL,
  `days` int(255) NULL DEFAULT NULL,
  `uid` int(11) NULL DEFAULT 0,
  `type` int(5) NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dwz_log`;
CREATE TABLE `dwz_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(10) NULL DEFAULT NULL,
  `param` varchar(30) NULL DEFAULT NULL,
  `result` varchar(10) NULL DEFAULT NULL,
  `addtime` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dwz_points`;
CREATE TABLE `dwz_points`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `action` varchar(10) NULL,
  `point` varchar(32) NOT NULL,
  `bz` varchar(50) NULL,
  `addtime` datetime NULL DEFAULT NULL,
  `orderid` int(64) NULL DEFAULT NULL,
  `status` int(1) NULL DEFAULT 0,
  `number` int(30) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dwz_visitors`;
CREATE TABLE `dwz_visitors`  (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NULL DEFAULT NULL,
  `urlid` varchar(11) NULL DEFAULT NULL,
  `ip` varchar(20) NULL DEFAULT NULL,
  `froms` varchar(50) NULL DEFAULT NULL,
  `addtime` datetime NULL DEFAULT NULL,
  `system` varchar(30) NULL DEFAULT NULL,
  `pageview` varchar(100) NULL DEFAULT NULL,
  `source_link` varchar(100) NULL DEFAULT NULL,
  `browser` varchar(30) NULL DEFAULT NULL,
  `adddate` date NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dwz_pay`;
CREATE TABLE `dwz_pay`  (
  `trade_no` varchar(64) NOT NULL,
  `uid` int(11) NOT NULL,
  `num` varchar(11) NULL DEFAULT NULL,
  `money` varchar(32) NULL DEFAULT NULL,
  `ip` varchar(20) NULL DEFAULT NULL,
  `addtime` datetime NULL DEFAULT NULL,
  `status` int(1) NULL DEFAULT 0,
  `type` varchar(20) NULL DEFAULT NULL,
  `endtime` datetime NULL DEFAULT NULL,
  `name` varchar(64) NULL DEFAULT NULL,
  `domain` varchar(64) NULL DEFAULT NULL,
  PRIMARY KEY (`trade_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

DROP TABLE IF EXISTS `dwz_api`;
CREATE TABLE `dwz_api` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NULL DEFAULT NULL,
    `keyname` varchar(100) NULL DEFAULT NULL,
    `bz` text NULL DEFAULT NULL,
    `addtime` datetime NULL DEFAULT NULL,
    `type` int(1) NULL DEFAULT 0,
    `token` varchar(200) NULL DEFAULT NULL,
    `status` int(1) NULL DEFAULT 1,
    `domain` varchar(100) NULL DEFAULT NULL,
    `num` int(10) NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

INSERT INTO `dwz_api` (`id`, `name`, `keyname`, `bz`, `addtime`, `type`, `token`, `status`, `domain`, `num`) VALUES
(1, '本站', 'bz', '', '2022-03-08 18:17:51', 0, '', 1, 'http://aliyuncs.com', 0);