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
INSERT INTO `dwz_config` VALUES ('index_bottom', '<i class="fa fa-heart text-danger animation-pulse"></i>
          <font color=#CB0034>本</font>
          <font color=#BE0041>站</font>
          <font color=#B1004E>网</font>
          <font color=#A4005B>址</font>
          <font color=#970068>：70api.com</font>
          <font color=#2F00D0></font>
          <font color=#CB0034> </font>
          <font color=#CB0034>建</font>
          <font color=#BE0041>议</font>
          <font color=#B1004E>收</font>
          <font color=#A4005B>藏</font>
          <i class="fa fa-heart text-danger animation-pulse"></i>
</a>
        <br><br>
        <div align="center">
          <tr>
            <td>
              友情链接：
              <a target="_blank" href="/" target="_blank">短网址系统</a>
               | 
              <a target="_blank" href="/">友链申请</a>
               | 
              <a target="_blank" href="/" target="_blank">短网址系统</a>
               | 
             <a target="_blank" href="/" target="_blank">短网址系统</a>
               | 
              <a target="_blank" href="/" target="_blank">短网址系统</a>
               | 
              <a target="_blank" href="/" target="_blank">短网址系统</a>
               | 
              <a target="_blank" href="/" target="_blank">短网址系统</a>
               | 
              <a target="_blank" href="/">友链申请</a>
               | 
              <a target="_blank" href="/" target="_blank">短网址系统</a>
               | 
              <a target="_blank" href="#">Ten</a>
               | 
              <a target="_blank" href="/" target="_blank">短网址系统</a>
              <br><br>
              <center class="bityears"> © 2023 短网址系统</center>
            </td>
          </tr>
        </div>
      </div>');

ALTER TABLE `dwz_url` ADD COLUMN `domain` varchar(200);

ALTER TABLE `dwz_user` MODIFY `img` varchar(200);
ALTER TABLE `dwz_visitors` MODIFY `urlid` varchar(11);

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
  PRIMARY KEY (`trade_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;