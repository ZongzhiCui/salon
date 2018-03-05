/*
Navicat MySQL Data Transfer

Source Server         : php-apache-mysql
Source Server Version : 50524
Source Host           : 127.0.0.1:3306
Source Database       : salon

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2018-03-04 15:13:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for mall
-- ----------------------------
DROP TABLE IF EXISTS `mall`;
CREATE TABLE `mall` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
  `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '商品描述',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `logo` varchar(255) NOT NULL DEFAULT '0' COMMENT '图片',
  `status` tinyint(255) unsigned NOT NULL DEFAULT '0' COMMENT '1上架  0下架',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '库存',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mall
-- ----------------------------
INSERT INTO `mall` VALUES ('1', '小碗熊', '玩具', '8888.00', './Uploads/Mall/20180303/img_5a9a58da5fc7a_340x340.jpg', '1', '99');
INSERT INTO `mall` VALUES ('2', '恐龙骨架', '一个骨架模型', '18888.00', './Uploads/Mall/20180303/img_5a9a595285b61_340x340.jpg', '1', '11');
INSERT INTO `mall` VALUES ('3', 'DIY', '私人定制商品', '100.00', './Uploads/Mall/20180303/img_5a9a59784e6ad_340x340.jpg', '1', '99999');
INSERT INTO `mall` VALUES ('4', '未知', '暂无介绍', '6666.00', './Uploads/Mall/20180303/img_5a9a599aa8290_340x340.jpg', '1', '88');
INSERT INTO `mall` VALUES ('5', '123', '1231231312', '999999.00', './Uploads/Mall/20180303/img_5a9a59b5c1d20_340x340.jpg', '1', '22');
INSERT INTO `mall` VALUES ('6', '123', '1231231312', '999999.00', './Uploads/Mall/20180303/img_5a9a59b5c1d20_340x340.jpg', '1', '22');
INSERT INTO `mall` VALUES ('7', '123', '1231231312', '999999.00', './Uploads/Mall/20180303/img_5a9a59b5c1d20_340x340.jpg', '1', '22');
INSERT INTO `mall` VALUES ('8', '积分商品11', '积分商品11的简介111', '8888.00', './Uploads/Mall/20180304/img_5a9b9ae5d8b87_340x340.jpg', '1', '11');
INSERT INTO `mall` VALUES ('9', '积分商品22', '积分商品22的简介222', '88888.00', './Uploads/Mall/20180304/img_5a9b9afb9768f_340x340.jpg', '1', '22');
INSERT INTO `mall` VALUES ('10', '积分商品33', '积分商品33的简介333', '888888.00', './Uploads/Mall/20180304/img_5a9b9b12384d7_340x340.jpg', '1', '33');
INSERT INTO `mall` VALUES ('11', '积分商品44', '积分商品44的简介444', '99999.00', './Uploads/Mall/20180304/img_5a9b9b27ba5f7_340x340.gif', '1', '44');
INSERT INTO `mall` VALUES ('12', '积分商品55', '积分商品55的简介555', '10000.00', './Uploads/Mall/20180304/img_5a9b9b453301d_340x340.jpg', '1', '100');
