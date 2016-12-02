/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-12-02 20:11:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for pc_room
-- ----------------------------
DROP TABLE IF EXISTS `pc_room`;
CREATE TABLE `pc_room` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '房间名',
  `max_num` int(11) NOT NULL DEFAULT '100' COMMENT '最大人数',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '房间状态，1：正常，2：关闭',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
