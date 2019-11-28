/*
 Navicat Premium Data Transfer

 Source Server         : 3307
 Source Server Type    : MySQL
 Source Server Version : 50717
 Source Host           : localhost:3307
 Source Schema         : tip

 Target Server Type    : MySQL
 Target Server Version : 50717
 File Encoding         : 65001

 Date: 28/11/2019 16:23:45
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for supervise_service_dict_data
-- ----------------------------
DROP TABLE IF EXISTS `supervise_service_dict_data`;
CREATE TABLE `supervise_service_dict_data`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `dict_id` int(10) UNSIGNED NOT NULL,
  `service_grade` int(10) NULL DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `created_user_id` int(11) NULL DEFAULT NULL,
  `updated_user_id` int(11) NULL DEFAULT NULL,
  `sort` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 90 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of supervise_service_dict_data
-- ----------------------------
INSERT INTO `supervise_service_dict_data` VALUES (1, '工装', '0', 18, 5, '工装', 1, 1, 0, 1, '2019-11-24 18:37:31', '2019-11-28 10:24:25');
INSERT INTO `supervise_service_dict_data` VALUES (2, '工牌', '1', 18, 5, '工牌', 1, 1, 1, 1, '2019-11-24 18:37:43', '2019-11-28 10:24:40');
INSERT INTO `supervise_service_dict_data` VALUES (3, '鞋套', '2', 18, 3, '鞋套', 1, 1, 2, 1, '2019-11-24 18:37:54', '2019-11-28 10:24:53');
INSERT INTO `supervise_service_dict_data` VALUES (4, '垫布', '3', 18, 2, '垫布', 1, 1, 3, 1, '2019-11-24 18:38:09', '2019-11-28 10:25:12');
INSERT INTO `supervise_service_dict_data` VALUES (5, '抹布', '4', 18, 2, '抹布', 1, 1, 4, 1, '2019-11-24 18:38:25', '2019-11-28 10:25:25');
INSERT INTO `supervise_service_dict_data` VALUES (6, '服务监督卡', '5', 18, 2, '服务监督卡', 1, NULL, 5, 1, '2019-11-28 10:25:42', '2019-11-28 10:25:42');
INSERT INTO `supervise_service_dict_data` VALUES (7, '业务宣传单', '6', 18, 3, '业务宣传单', 1, NULL, 6, 1, '2019-11-28 10:25:58', '2019-11-28 10:25:58');
INSERT INTO `supervise_service_dict_data` VALUES (8, '即时贴', '7', 18, 5, '即时贴', 1, NULL, 7, 1, '2019-11-28 10:26:15', '2019-11-28 10:26:15');
INSERT INTO `supervise_service_dict_data` VALUES (9, '服务态度', '8', 18, 10, '服务态度', 1, NULL, 8, 1, '2019-11-28 10:26:31', '2019-11-28 10:26:31');
INSERT INTO `supervise_service_dict_data` VALUES (10, '礼貌用语', '9', 18, 10, '礼貌用语', 1, NULL, 9, 1, '2019-11-28 10:26:44', '2019-11-28 10:26:44');
INSERT INTO `supervise_service_dict_data` VALUES (11, '测速', '10', 18, 10, '测速', 1, NULL, 10, 1, '2019-11-28 10:27:02', '2019-11-28 10:27:02');
INSERT INTO `supervise_service_dict_data` VALUES (12, '业务演示', '11', 18, 10, '业务演示', 1, NULL, 11, 1, '2019-11-28 10:27:15', '2019-11-28 10:27:15');
INSERT INTO `supervise_service_dict_data` VALUES (13, '小翼管家安装绑定', '12', 18, 10, '小翼管家安装绑定', 1, NULL, 12, 1, '2019-11-28 10:27:26', '2019-11-28 10:27:26');
INSERT INTO `supervise_service_dict_data` VALUES (14, '扫码测评', '14', 18, 10, '扫码测评', 1, NULL, 14, 1, '2019-11-28 10:27:38', '2019-11-28 10:27:38');
INSERT INTO `supervise_service_dict_data` VALUES (15, '智能包推介', '15', 18, 5, '智能包推介', 1, NULL, 15, 1, '2019-11-28 10:27:49', '2019-11-28 10:27:49');
INSERT INTO `supervise_service_dict_data` VALUES (16, '即时贴张贴及告知', '16', 18, 5, '即时贴张贴及告知', 1, NULL, 16, 1, '2019-11-28 10:28:03', '2019-11-28 10:28:03');
INSERT INTO `supervise_service_dict_data` VALUES (17, '用户签字', '17', 18, 3, '用户签字', 1, NULL, 17, 1, '2019-11-28 10:28:13', '2019-11-28 10:28:13');

SET FOREIGN_KEY_CHECKS = 1;
