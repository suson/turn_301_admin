/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50527
Source Host           : localhost:3306
Source Database       : turn_301

Target Server Type    : MYSQL
Target Server Version : 50527
File Encoding         : 65001

Date: 2013-08-11 09:17:44
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `turn_url`
-- ----------------------------
DROP TABLE IF EXISTS `turn_url`;
CREATE TABLE `turn_url` (
  `id` int(11) NOT NULL DEFAULT '0',
  `frompath` char(255) NOT NULL DEFAULT '',
  `firsttime` datetime NOT NULL,
  `turntime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of turn_url
-- ----------------------------
INSERT INTO turn_url VALUES ('6604', 'http://www.google.com', '2013-08-10 22:37:25', '2013-08-10 22:37:25');
