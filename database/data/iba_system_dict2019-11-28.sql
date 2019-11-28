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

 Date: 28/11/2019 10:30:20
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for iba_system_dict
-- ----------------------------
DROP TABLE IF EXISTS `iba_system_dict`;
CREATE TABLE `iba_system_dict`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `created_user_id` int(11) NULL DEFAULT NULL,
  `updated_user_id` int(11) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of iba_system_dict
-- ----------------------------
INSERT INTO `iba_system_dict` VALUES (1, '性别', 'sex', NULL, 1, NULL, '2019-01-25 10:22:35', '2019-01-25 10:22:35');
INSERT INTO `iba_system_dict` VALUES (2, '产品类型价值', 'chanpinleixin', '产品类型-价值', NULL, 1, '2019-10-19 19:46:16', '2019-11-18 15:49:11');
INSERT INTO `iba_system_dict` VALUES (3, '业务类型', 'yewuleixing', NULL, NULL, NULL, '2019-10-19 19:46:32', '2019-10-19 19:46:32');
INSERT INTO `iba_system_dict` VALUES (5, '是否新用户', 'shifouxinyonghu', NULL, NULL, NULL, '2019-10-19 19:47:00', '2019-10-19 19:47:00');
INSERT INTO `iba_system_dict` VALUES (6, '终端类型', 'zhongduanleixing', '终端类型', 1, NULL, '2019-11-07 17:36:26', '2019-11-07 17:36:26');
INSERT INTO `iba_system_dict` VALUES (7, '产品类型发展', 'fazhan', '产品类型-发展', 1, 1, '2019-11-07 17:41:28', '2019-11-18 15:49:19');
INSERT INTO `iba_system_dict` VALUES (8, '套餐', 'ronghetaocan', '套餐', 1, 1, '2019-11-07 17:45:12', '2019-11-07 17:46:14');
INSERT INTO `iba_system_dict` VALUES (9, '融合套餐', 'ronghetaocan', '融合套餐', NULL, NULL, '2019-11-07 17:55:38', '2019-11-07 17:55:38');
INSERT INTO `iba_system_dict` VALUES (10, '单卡套餐', 'dankataocan', '单卡套餐', NULL, NULL, '2019-11-07 17:59:16', '2019-11-07 17:59:16');
INSERT INTO `iba_system_dict` VALUES (11, '智慧企业套餐', 'zhihuiqiye', '智慧企业套餐', NULL, NULL, '2019-11-07 18:03:17', '2019-11-07 18:03:17');
INSERT INTO `iba_system_dict` VALUES (12, '升级套餐', 'shengjitaocan', '升级套餐', NULL, NULL, '2019-11-07 18:04:57', '2019-11-07 18:04:57');
INSERT INTO `iba_system_dict` VALUES (13, '智慧家庭升级包', 'zhihuijiatingshengjibao', '智慧家庭升级包', NULL, NULL, '2019-11-07 18:06:28', '2019-11-07 18:06:28');
INSERT INTO `iba_system_dict` VALUES (14, '5G升级包', '5Gshengjibao', '5G升级包', NULL, NULL, '2019-11-07 18:06:42', '2019-11-07 18:06:42');
INSERT INTO `iba_system_dict` VALUES (15, '加第二路宽带', 'jiaerlukuandai', '加第二路宽带', NULL, NULL, '2019-11-07 18:06:56', '2019-11-07 18:06:56');
INSERT INTO `iba_system_dict` VALUES (16, '加卡', 'jiaka', '加卡', NULL, NULL, '2019-11-07 18:07:29', '2019-11-07 18:07:29');
INSERT INTO `iba_system_dict` VALUES (17, '门店状态', 'mendianzhuangtai', '门店状态', 1, NULL, '2019-11-09 23:49:18', '2019-11-09 23:49:18');
INSERT INTO `iba_system_dict` VALUES (18, '服务打分', 'fuwudafen', '服务打分', 1, NULL, '2019-11-24 18:27:29', '2019-11-24 18:27:29');

-- ----------------------------
-- Table structure for iba_system_dict_data
-- ----------------------------
DROP TABLE IF EXISTS `iba_system_dict_data`;
CREATE TABLE `iba_system_dict_data`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `dict_id` int(10) UNSIGNED NOT NULL,
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
-- Records of iba_system_dict_data
-- ----------------------------
INSERT INTO `iba_system_dict_data` VALUES (1, '男', '0', 1, NULL, 1, NULL, 0, 1, '2019-01-25 10:23:00', '2019-01-25 10:23:00');
INSERT INTO `iba_system_dict_data` VALUES (2, '女', '1', 1, NULL, 1, NULL, 1, 1, '2019-01-25 10:23:23', '2019-01-25 10:23:23');
INSERT INTO `iba_system_dict_data` VALUES (3, '加卡', '4', 2, '加卡', NULL, 1, 4, 1, '2019-10-19 19:48:07', '2019-11-08 17:33:19');
INSERT INTO `iba_system_dict_data` VALUES (4, '加宽', '5', 2, '加宽', NULL, 1, 5, 1, '2019-10-19 19:48:19', '2019-11-08 17:33:32');
INSERT INTO `iba_system_dict_data` VALUES (5, '加IPTV', '6', 2, '加IPTV', NULL, 1, 6, 1, '2019-10-19 19:48:31', '2019-11-08 17:33:42');
INSERT INTO `iba_system_dict_data` VALUES (6, '智能宽带升级包', '7', 2, '智能宽带升级包', NULL, 1, 7, 1, '2019-10-19 19:48:41', '2019-11-08 17:33:50');
INSERT INTO `iba_system_dict_data` VALUES (7, '5G升级包（存量用户）', '8', 2, '5G升级包（存量用户）', NULL, 1, 8, 1, '2019-10-19 19:48:52', '2019-11-08 17:33:57');
INSERT INTO `iba_system_dict_data` VALUES (8, '花呗', '0', 3, '花呗', NULL, NULL, 0, 1, '2019-10-19 19:49:06', '2019-10-19 19:49:06');
INSERT INTO `iba_system_dict_data` VALUES (9, '京东白条', '1', 3, '京东白条', NULL, NULL, 1, 1, '2019-10-19 19:49:20', '2019-10-19 19:49:20');
INSERT INTO `iba_system_dict_data` VALUES (10, '红包分期', '2', 3, '橙分期', NULL, 1, 2, 1, '2019-10-19 19:49:30', '2019-11-07 17:34:40');
INSERT INTO `iba_system_dict_data` VALUES (13, '存量用户', '0', 5, '价值积分', NULL, 1, 0, 1, '2019-10-19 19:50:09', '2019-11-07 17:32:36');
INSERT INTO `iba_system_dict_data` VALUES (14, '新发展用户', '1', 5, '发展用户', NULL, 1, 1, 1, '2019-10-19 19:50:19', '2019-11-07 17:32:53');
INSERT INTO `iba_system_dict_data` VALUES (15, '无', '3', 3, '无', 1, 1, 3, 1, '2019-11-07 17:35:01', '2019-11-07 17:35:11');
INSERT INTO `iba_system_dict_data` VALUES (16, '手机', '0', 6, '手机', 1, NULL, 0, 1, '2019-11-07 17:37:41', '2019-11-07 17:37:41');
INSERT INTO `iba_system_dict_data` VALUES (17, '泛智能终端', '1', 6, '泛智能终端', 1, NULL, 1, 1, '2019-11-07 17:37:56', '2019-11-07 17:37:56');
INSERT INTO `iba_system_dict_data` VALUES (18, '家电', '2', 6, '家电', 1, NULL, 2, 1, '2019-11-07 17:38:30', '2019-11-07 17:38:30');
INSERT INTO `iba_system_dict_data` VALUES (19, '电动车', '3', 6, '电动车', 1, NULL, 3, 1, '2019-11-07 17:38:44', '2019-11-07 17:38:44');
INSERT INTO `iba_system_dict_data` VALUES (20, '其他', '4', 6, '其他', 1, NULL, 4, 1, '2019-11-07 17:38:59', '2019-11-07 17:38:59');
INSERT INTO `iba_system_dict_data` VALUES (21, '单C', '0', 7, '单C', 1, NULL, 0, 1, '2019-11-07 17:42:07', '2019-11-07 17:42:07');
INSERT INTO `iba_system_dict_data` VALUES (22, '融合', '1', 7, '融合', 1, NULL, 1, 1, '2019-11-07 17:42:18', '2019-11-07 17:42:18');
INSERT INTO `iba_system_dict_data` VALUES (23, '单宽', '2', 7, '单宽', 1, NULL, 2, 1, '2019-11-07 17:42:30', '2019-11-07 17:42:30');
INSERT INTO `iba_system_dict_data` VALUES (24, 'IPTV', '3', 7, 'IPTV', 1, NULL, 3, 1, '2019-11-07 17:42:45', '2019-11-07 17:42:45');
INSERT INTO `iba_system_dict_data` VALUES (25, '套餐迁转', '9', 2, '套餐迁转', 1, 1, 9, 1, '2019-11-07 17:44:10', '2019-11-08 17:34:07');
INSERT INTO `iba_system_dict_data` VALUES (28, '融合套餐', '0', 8, '融合套餐', 1, NULL, 0, 1, '2019-11-07 17:51:00', '2019-11-07 17:51:00');
INSERT INTO `iba_system_dict_data` VALUES (29, '单卡套餐', '1', 8, '单卡套餐', 1, NULL, 1, 1, '2019-11-07 17:51:22', '2019-11-07 17:51:22');
INSERT INTO `iba_system_dict_data` VALUES (30, '智慧企业套餐', '2', 8, '智慧企业', NULL, NULL, 2, 1, '2019-11-07 17:54:49', '2019-11-07 17:54:49');
INSERT INTO `iba_system_dict_data` VALUES (31, '融合399元', '0', 9, '融合399元', NULL, NULL, 0, 1, '2019-11-07 17:55:58', '2019-11-07 17:55:58');
INSERT INTO `iba_system_dict_data` VALUES (32, '融合329元', '1', 9, '融合329元套餐', NULL, NULL, 1, 1, '2019-11-07 17:56:28', '2019-11-07 17:56:28');
INSERT INTO `iba_system_dict_data` VALUES (33, '融合229元', '2', 9, '融合229元套餐', NULL, NULL, 2, 1, '2019-11-07 17:56:41', '2019-11-07 17:57:15');
INSERT INTO `iba_system_dict_data` VALUES (34, '融合209元', '3', 9, '融合209元套餐', NULL, NULL, 3, 1, '2019-11-07 17:57:05', '2019-11-07 17:57:05');
INSERT INTO `iba_system_dict_data` VALUES (35, '融合169元', '4', 9, '融合169元套餐', NULL, NULL, 4, 1, '2019-11-07 17:57:41', '2019-11-07 17:57:41');
INSERT INTO `iba_system_dict_data` VALUES (36, '融合109元', '5', 9, '融合109元套餐', NULL, NULL, 5, 1, '2019-11-07 17:58:06', '2019-11-07 17:58:06');
INSERT INTO `iba_system_dict_data` VALUES (37, '融合79元', '6', 9, '融合79元套餐', NULL, NULL, 6, 1, '2019-11-07 17:58:42', '2019-11-07 17:58:42');
INSERT INTO `iba_system_dict_data` VALUES (38, '融合59元', '7', 9, '融合329元套餐', NULL, NULL, 7, 1, '2019-11-07 17:58:58', '2019-11-07 17:58:58');
INSERT INTO `iba_system_dict_data` VALUES (39, '无忧卡5元', '0', 10, '无忧卡5元', NULL, NULL, 0, 1, '2019-11-07 17:59:49', '2019-11-07 17:59:49');
INSERT INTO `iba_system_dict_data` VALUES (40, '新步步高19', '1', 10, '新步步高19', NULL, NULL, 1, 1, '2019-11-07 18:00:00', '2019-11-07 18:00:00');
INSERT INTO `iba_system_dict_data` VALUES (41, '新步步高39', '2', 10, '新步步高39', NULL, NULL, 2, 1, '2019-11-07 18:00:12', '2019-11-07 18:00:12');
INSERT INTO `iba_system_dict_data` VALUES (42, '新步步高69', '3', 10, '新步步高69', NULL, NULL, 3, 1, '2019-11-07 18:00:26', '2019-11-07 18:00:26');
INSERT INTO `iba_system_dict_data` VALUES (43, '畅享升级99', '4', 10, '畅享升级99', NULL, NULL, 4, 1, '2019-11-07 18:00:50', '2019-11-07 18:00:50');
INSERT INTO `iba_system_dict_data` VALUES (44, '5G畅享129', '5', 10, '5G畅享129', NULL, NULL, 5, 1, '2019-11-07 18:01:15', '2019-11-07 18:01:15');
INSERT INTO `iba_system_dict_data` VALUES (45, '5G畅享169', '6', 10, '5G畅享169', NULL, NULL, 6, 1, '2019-11-07 18:01:26', '2019-11-07 18:01:26');
INSERT INTO `iba_system_dict_data` VALUES (46, '5G畅享199', '7', 10, '5G畅享199', NULL, NULL, 7, 1, '2019-11-07 18:01:54', '2019-11-07 18:01:54');
INSERT INTO `iba_system_dict_data` VALUES (47, '5G畅享239', '8', 10, '5G畅享239', NULL, NULL, 8, 1, '2019-11-07 18:02:10', '2019-11-07 18:02:10');
INSERT INTO `iba_system_dict_data` VALUES (48, '5G畅享299', '9', 10, '5G畅享299', NULL, NULL, 9, 1, '2019-11-07 18:02:25', '2019-11-07 18:02:25');
INSERT INTO `iba_system_dict_data` VALUES (49, '5G畅享399', '10', 10, '5G畅享399', NULL, NULL, 10, 1, '2019-11-07 18:02:41', '2019-11-07 18:02:41');
INSERT INTO `iba_system_dict_data` VALUES (50, '5G畅享599', '11', 10, '5G畅享599', NULL, NULL, 11, 1, '2019-11-07 18:02:52', '2019-11-07 18:02:52');
INSERT INTO `iba_system_dict_data` VALUES (51, '智慧企业399元', '0', 11, '智慧企业399元', NULL, NULL, 0, 1, '2019-11-07 18:03:43', '2019-11-07 18:03:43');
INSERT INTO `iba_system_dict_data` VALUES (52, '智慧企业499元', '1', 11, '智慧企业499元', NULL, NULL, 1, 1, '2019-11-07 18:03:57', '2019-11-07 18:03:57');
INSERT INTO `iba_system_dict_data` VALUES (53, '智慧企业699元', '2', 11, '智慧企业699元', NULL, NULL, 2, 1, '2019-11-07 18:04:11', '2019-11-07 18:04:11');
INSERT INTO `iba_system_dict_data` VALUES (54, '智慧企业999元', '3', 11, '智慧企业999元', NULL, NULL, 3, 1, '2019-11-07 18:04:27', '2019-11-07 18:04:27');
INSERT INTO `iba_system_dict_data` VALUES (55, '智慧家庭升级包', '0', 12, '智慧家庭升级包', NULL, NULL, 0, 1, '2019-11-07 18:05:09', '2019-11-07 18:05:09');
INSERT INTO `iba_system_dict_data` VALUES (56, '5G升级包', '1', 12, '5G升级包', NULL, NULL, 1, 1, '2019-11-07 18:05:25', '2019-11-07 18:05:25');
INSERT INTO `iba_system_dict_data` VALUES (57, '加第二路宽带', '2', 12, '加第二路宽带', NULL, NULL, 2, 1, '2019-11-07 18:05:38', '2019-11-07 18:05:38');
INSERT INTO `iba_system_dict_data` VALUES (58, '加卡', '3', 12, '加卡', NULL, NULL, 3, 1, '2019-11-07 18:05:51', '2019-11-07 18:05:51');
INSERT INTO `iba_system_dict_data` VALUES (59, '城市版19元', '0', 13, '城市版19元', NULL, NULL, 0, 1, '2019-11-07 18:07:48', '2019-11-07 18:07:48');
INSERT INTO `iba_system_dict_data` VALUES (60, '城市版39元', '1', 13, '城市版39元', NULL, NULL, 1, 1, '2019-11-07 18:08:01', '2019-11-07 18:08:01');
INSERT INTO `iba_system_dict_data` VALUES (61, '城市版59元', '2', 13, '城市版59元', NULL, NULL, 2, 1, '2019-11-07 18:08:13', '2019-11-07 18:08:13');
INSERT INTO `iba_system_dict_data` VALUES (62, '农村版19元', '3', 13, '农村版19元', NULL, NULL, 3, 1, '2019-11-07 18:08:49', '2019-11-07 18:08:59');
INSERT INTO `iba_system_dict_data` VALUES (63, '农村版29元', '4', 13, '农村版29元', NULL, NULL, 4, 1, '2019-11-07 18:09:15', '2019-11-07 18:09:15');
INSERT INTO `iba_system_dict_data` VALUES (64, '5G升级会员10G包-19元', '0', 14, '5G升级会员10G包-19元', NULL, NULL, 0, 1, '2019-11-07 18:09:36', '2019-11-07 18:09:36');
INSERT INTO `iba_system_dict_data` VALUES (65, '5G升级会员20G包-29元', '1', 14, '5G升级会员20G包-29元', NULL, NULL, 1, 1, '2019-11-07 18:09:49', '2019-11-07 18:09:49');
INSERT INTO `iba_system_dict_data` VALUES (66, '5G升级会员60G包-69元', '2', 14, '5G升级会员60G包-69元', NULL, NULL, 2, 1, '2019-11-07 18:10:05', '2019-11-07 18:10:05');
INSERT INTO `iba_system_dict_data` VALUES (67, '201910畅享融合第二路宽带专属套餐（不带ITV）100M', '0', 15, '201910畅享融合第二路宽带专属套餐（不带ITV）100M', NULL, NULL, 0, 1, '2019-11-07 18:10:21', '2019-11-07 18:10:21');
INSERT INTO `iba_system_dict_data` VALUES (68, '201910畅享融合第二路宽带专属套餐（带ITV）100M+1路4K', '1', 15, '201910畅享融合第二路宽带专属套餐（带ITV）100M+1路4K', NULL, NULL, 1, 1, '2019-11-07 18:10:34', '2019-11-07 18:10:34');
INSERT INTO `iba_system_dict_data` VALUES (69, '加副卡-10元', '0', 16, '加副卡-10元', NULL, NULL, 0, 1, '2019-11-07 18:10:46', '2019-11-07 18:10:46');
INSERT INTO `iba_system_dict_data` VALUES (70, '加IPTB-10元', '1', 16, '加IPTB-10元', NULL, NULL, 1, 1, '2019-11-07 18:10:58', '2019-11-07 18:10:58');
INSERT INTO `iba_system_dict_data` VALUES (71, '正在营业', '1', 17, '正在营业', NULL, NULL, 1, 1, '2019-11-07 18:10:46', '2019-11-07 18:10:46');
INSERT INTO `iba_system_dict_data` VALUES (72, '未营业', '0', 17, '未营业', NULL, NULL, 0, 1, '2019-11-07 18:10:46', '2019-11-07 18:10:46');
INSERT INTO `iba_system_dict_data` VALUES (73, '工装', '0', 18, '工装', 1, 1, 0, 1, '2019-11-24 18:37:31', '2019-11-28 10:24:25');
INSERT INTO `iba_system_dict_data` VALUES (74, '工牌', '1', 18, '工牌', 1, 1, 1, 1, '2019-11-24 18:37:43', '2019-11-28 10:24:40');
INSERT INTO `iba_system_dict_data` VALUES (75, '鞋套', '2', 18, '鞋套', 1, 1, 2, 1, '2019-11-24 18:37:54', '2019-11-28 10:24:53');
INSERT INTO `iba_system_dict_data` VALUES (76, '垫布', '3', 18, '垫布', 1, 1, 3, 1, '2019-11-24 18:38:09', '2019-11-28 10:25:12');
INSERT INTO `iba_system_dict_data` VALUES (77, '抹布', '4', 18, '抹布', 1, 1, 4, 1, '2019-11-24 18:38:25', '2019-11-28 10:25:25');
INSERT INTO `iba_system_dict_data` VALUES (78, '服务监督卡', '5', 18, '服务监督卡', 1, NULL, 5, 1, '2019-11-28 10:25:42', '2019-11-28 10:25:42');
INSERT INTO `iba_system_dict_data` VALUES (79, '业务宣传单', '6', 18, '业务宣传单', 1, NULL, 6, 1, '2019-11-28 10:25:58', '2019-11-28 10:25:58');
INSERT INTO `iba_system_dict_data` VALUES (80, '即时贴', '7', 18, '即时贴', 1, NULL, 7, 1, '2019-11-28 10:26:15', '2019-11-28 10:26:15');
INSERT INTO `iba_system_dict_data` VALUES (81, '服务态度', '8', 18, '服务态度', 1, NULL, 8, 1, '2019-11-28 10:26:31', '2019-11-28 10:26:31');
INSERT INTO `iba_system_dict_data` VALUES (82, '礼貌用语', '9', 18, '礼貌用语', 1, NULL, 9, 1, '2019-11-28 10:26:44', '2019-11-28 10:26:44');
INSERT INTO `iba_system_dict_data` VALUES (83, '测速', '10', 18, '测速', 1, NULL, 10, 1, '2019-11-28 10:27:02', '2019-11-28 10:27:02');
INSERT INTO `iba_system_dict_data` VALUES (84, '业务演示', '11', 18, '业务演示', 1, NULL, 11, 1, '2019-11-28 10:27:15', '2019-11-28 10:27:15');
INSERT INTO `iba_system_dict_data` VALUES (85, '小翼管家安装绑定', '12', 18, '小翼管家安装绑定', 1, NULL, 12, 1, '2019-11-28 10:27:26', '2019-11-28 10:27:26');
INSERT INTO `iba_system_dict_data` VALUES (86, '扫码测评', '14', 18, '扫码测评', 1, NULL, 14, 1, '2019-11-28 10:27:38', '2019-11-28 10:27:38');
INSERT INTO `iba_system_dict_data` VALUES (87, '智能包推介', '15', 18, '智能包推介', 1, NULL, 15, 1, '2019-11-28 10:27:49', '2019-11-28 10:27:49');
INSERT INTO `iba_system_dict_data` VALUES (88, '即时贴张贴及告知', '16', 18, '即时贴张贴及告知', 1, NULL, 16, 1, '2019-11-28 10:28:03', '2019-11-28 10:28:03');
INSERT INTO `iba_system_dict_data` VALUES (89, '用户签字', '17', 18, '用户签字', 1, NULL, 17, 1, '2019-11-28 10:28:13', '2019-11-28 10:28:13');

SET FOREIGN_KEY_CHECKS = 1;
