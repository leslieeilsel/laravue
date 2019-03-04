/*
 Navicat Premium Data Transfer

 Source Server         : MySQL-5.7
 Source Server Type    : MySQL
 Source Server Version : 50722
 Source Host           : localhost:3306
 Source Schema         : icp

 Target Server Type    : MySQL
 Target Server Version : 50722
 File Encoding         : 65001

 Date: 04/03/2019 09:57:35
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for iba_project_early_warning
-- ----------------------------
DROP TABLE IF EXISTS `iba_project_early_warning`;
CREATE TABLE `iba_project_early_warning`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `warning_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `project_info_id` int(10) UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of iba_project_early_warning
-- ----------------------------
INSERT INTO `iba_project_early_warning` VALUES (1, '西咸新区沣西新城智慧沣西1号楼', '项目填报异常', 1, NULL, '2019-02-18 00:07:19');
INSERT INTO `iba_project_early_warning` VALUES (2, '智慧沣西机房建设', '项目延期开始', 2, NULL, '2019-02-18 00:07:22');
INSERT INTO `iba_project_early_warning` VALUES (3, '沣西新城西部云谷7号楼', '项目进度延迟', 3, NULL, '2019-02-21 10:35:44');
INSERT INTO `iba_project_early_warning` VALUES (5, '沣西新城双创中心建设项目', '项目延期结束', 5, NULL, '2019-02-21 10:13:36');
INSERT INTO `iba_project_early_warning` VALUES (6, '沣西新城第三小学建设项目', '项目延期开始', 6, NULL, '2019-02-21 10:13:56');
INSERT INTO `iba_project_early_warning` VALUES (7, '沣西新城第三幼儿园建设项目', '项目延期结束', 7, NULL, '2019-02-18 00:07:33');
INSERT INTO `iba_project_early_warning` VALUES (8, '2018年项目计划', '项目延期开始,项目延期结束', 8, NULL, '2019-02-21 10:49:42');
INSERT INTO `iba_project_early_warning` VALUES (9, '2019年项目计划', '项目进度延迟', 9, NULL, '2019-02-21 10:50:03');
INSERT INTO `iba_project_early_warning` VALUES (10, '2020年项目计划', '项目进度延迟', 10, NULL, NULL);
INSERT INTO `iba_project_early_warning` VALUES (11, '2019年1月项目计划', '项目进度延迟', 11, NULL, NULL);
INSERT INTO `iba_project_early_warning` VALUES (12, '2019年2月项目计划', '项目进度延迟', 12, NULL, NULL);

-- ----------------------------
-- Table structure for iba_project_plan
-- ----------------------------
DROP TABLE IF EXISTS `iba_project_plan`;
CREATE TABLE `iba_project_plan`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `project_id` int(10) UNSIGNED NULL DEFAULT NULL,
  `parent_id` int(10) UNSIGNED NULL DEFAULT NULL,
  `amount` decimal(10, 2) NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of iba_project_plan
-- ----------------------------
INSERT INTO `iba_project_plan` VALUES (10, '2019年项目计划', 4, 0, 34534534.00, NULL, '2019-02-28 23:28:29');
INSERT INTO `iba_project_plan` VALUES (13, '4月', 4, 10, 43453555.00, NULL, '2019-02-28 23:24:45');

-- ----------------------------
-- Table structure for iba_project_projects
-- ----------------------------
DROP TABLE IF EXISTS `iba_project_projects`;
CREATE TABLE `iba_project_projects`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `num` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `owner` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `unit` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `amount` decimal(10, 2) NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `is_gc` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `center_point` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `positions` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `plan_start_at` date NULL DEFAULT NULL,
  `plan_end_at` date NULL DEFAULT NULL,
  `actual_start_at` date NULL DEFAULT NULL,
  `actual_end_at` date NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of iba_project_projects
-- ----------------------------
INSERT INTO `iba_project_projects` VALUES (4, '西咸新区沣西新城智慧沣西1号楼', '345345', '在建', '沣西新城', '陕西建工第一建设集团有限公司', 345345.00, '绿化', '不是改创项目', '本项目建设内容为房建，项目总投资为100万元。资金来源通过自筹及申请中省市专项建设资金解决。', '108.708638,34.297493', '108.705233,34.299387;108.711162,34.299469;108.711809,34.29589;108.706032,34.295778', '2019-02-22', '2019-02-20', '2019-02-14', '2019-02-21', NULL, NULL);
INSERT INTO `iba_project_projects` VALUES (9, '沣西新城西部云谷7号楼', '34543', '计划', '西部云谷', '陕西建工第三建设集团有限公司', 3455.00, '市政', '不是改创项目', '本项目建设内容为房建，项目总投资为100万元。资金来源通过自筹及申请中省市专项建设资金解决。', '108.735518, 34.300319', '108.730882,34.30175;108.737242,34.30263;108.737997,34.295159;108.732535, 34.294727', '2019-02-01', '2019-02-06', '2019-02-12', '2019-02-21', NULL, NULL);
INSERT INTO `iba_project_projects` VALUES (10, '沣西新城第三幼儿园建设项目', '35334', '完成', '沣西新城', '陕西建工第三建设集团有限公司', 345345.00, '绿化', '是改创项目', '本项目建设内容为房建，项目总投资为100万元。资金来源通过自筹及申请中省市专项建设资金解决。', '108.72605,34.303882', '108.721846,34.30509;108.729409,34.306089;108.730343,34.302078;108.723624,34.301243', '2019-02-01', '2019-02-08', '2019-02-04', '2019-02-08', NULL, NULL);
INSERT INTO `iba_project_projects` VALUES (11, '沣西新城双创中心建设项目', '456456', '在建', '双创中心', '陕西建工第四建设集团有限公司', 456456.00, '房建', '是改创项目', '本项目建设内容为房建，项目总投资为100万元。资金来源通过自筹及申请中省市专项建设资金解决。', '108.71218,34.306969', '108.708083,34.307565;108.7143,34.312217;108.715665,34.304643;108.710527,34.303837', '2019-02-05', '2019-02-06', '2019-02-20', '2019-02-20', NULL, NULL);

-- ----------------------------
-- Table structure for iba_system_department
-- ----------------------------
DROP TABLE IF EXISTS `iba_system_department`;
CREATE TABLE `iba_system_department`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(11) NULL DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `parent_id` int(10) UNSIGNED NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `create_by` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `update_by` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of iba_system_department
-- ----------------------------
INSERT INTO `iba_system_department` VALUES (1, '沣西新城开发建设有限公司', 1, 1, 0, '沣西新城开发建设有限公司', 'Admin', 'Admin', '2019-01-09 10:18:23', '2019-03-01 12:05:21');
INSERT INTO `iba_system_department` VALUES (2, '房建工程项目管理部', 1, 1, 1, '电彩部', 'Admin', 'Admin', '2019-01-07 14:38:42', '2019-03-01 14:02:29');
INSERT INTO `iba_system_department` VALUES (3, '市政工程项目管理部', 2, 1, 1, '市政工程项目管理部', 'Admin', 'Admin', '2019-03-01 14:04:32', '2019-03-01 14:04:48');
INSERT INTO `iba_system_department` VALUES (4, '绿化工程项目管理部', 3, 1, 1, '绿化工程项目管理部', 'Admin', NULL, '2019-03-01 14:05:20', NULL);
INSERT INTO `iba_system_department` VALUES (5, '水利工程项目管理部', 4, 1, 1, '水利工程项目管理部', 'Admin', 'Admin', '2019-03-01 14:06:01', '2019-03-01 14:06:32');
INSERT INTO `iba_system_department` VALUES (6, '发展经营部', 5, 1, 1, '发展经营部', 'Admin', NULL, '2019-03-01 14:08:07', NULL);

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
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of iba_system_dict
-- ----------------------------
INSERT INTO `iba_system_dict` VALUES (1, '性别', 'sex', NULL, 1, NULL, '2019-01-25 10:22:35', '2019-01-25 10:22:35');
INSERT INTO `iba_system_dict` VALUES (2, '项目类型', 'XMLX', NULL, 6, NULL, '2019-02-21 09:42:58', '2019-02-21 09:42:58');
INSERT INTO `iba_system_dict` VALUES (3, '工程类项目分类', 'GCXMFL', NULL, 6, NULL, '2019-02-21 09:43:45', '2019-02-21 09:43:45');
INSERT INTO `iba_system_dict` VALUES (4, '资金来源', 'ZJLY', NULL, 6, NULL, '2019-02-21 09:53:29', '2019-02-21 09:53:29');
INSERT INTO `iba_system_dict` VALUES (5, '建设性质', 'JSXZ', NULL, 6, NULL, '2019-02-21 09:55:36', '2019-02-21 09:55:36');
INSERT INTO `iba_system_dict` VALUES (6, '项目状态', 'XMZT', NULL, 6, NULL, '2019-02-21 10:00:48', '2019-02-21 10:00:48');

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
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of iba_system_dict_data
-- ----------------------------
INSERT INTO `iba_system_dict_data` VALUES (1, '男', '0', 1, NULL, 1, NULL, 0, 1, '2019-01-25 10:23:00', '2019-01-25 10:23:00');
INSERT INTO `iba_system_dict_data` VALUES (2, '女', '1', 1, NULL, 1, NULL, 1, 1, '2019-01-25 10:23:23', '2019-01-25 10:23:23');
INSERT INTO `iba_system_dict_data` VALUES (3, '工程类项目', '1', 2, NULL, 6, NULL, 0, 1, '2019-02-21 09:43:11', '2019-02-21 09:43:11');
INSERT INTO `iba_system_dict_data` VALUES (4, '非工程类项目', '0', 2, NULL, 6, NULL, 0, 1, '2019-02-21 09:43:25', '2019-02-21 09:43:25');
INSERT INTO `iba_system_dict_data` VALUES (5, '房间', '0', 3, NULL, 6, NULL, 0, 1, '2019-02-21 09:43:58', '2019-02-21 09:43:58');
INSERT INTO `iba_system_dict_data` VALUES (6, '绿化', '1', 3, NULL, 6, NULL, 0, 1, '2019-02-21 09:44:05', '2019-02-21 09:44:05');
INSERT INTO `iba_system_dict_data` VALUES (7, '市政', '2', 3, NULL, 6, NULL, 0, 1, '2019-02-21 09:44:11', '2019-02-21 09:44:11');
INSERT INTO `iba_system_dict_data` VALUES (8, '水利', '3', 3, NULL, 6, NULL, 0, 1, '2019-02-21 09:44:18', '2019-02-21 09:44:18');
INSERT INTO `iba_system_dict_data` VALUES (9, '财政', '1', 4, NULL, 6, NULL, 0, 1, '2019-02-21 09:53:38', '2019-02-21 09:53:38');
INSERT INTO `iba_system_dict_data` VALUES (10, '自筹', '0', 4, NULL, 6, NULL, 0, 1, '2019-02-21 09:53:52', '2019-02-21 09:53:52');
INSERT INTO `iba_system_dict_data` VALUES (11, '新建', '0', 5, NULL, 6, NULL, 0, 1, '2019-02-21 09:55:46', '2019-02-21 09:55:46');
INSERT INTO `iba_system_dict_data` VALUES (12, '改建', '1', 5, NULL, 6, NULL, 0, 1, '2019-02-21 09:55:52', '2019-02-21 09:55:52');
INSERT INTO `iba_system_dict_data` VALUES (13, '扩建', '2', 5, NULL, 6, NULL, 0, 1, '2019-02-21 09:56:00', '2019-02-21 09:56:00');
INSERT INTO `iba_system_dict_data` VALUES (14, '恢复', '3', 5, NULL, 6, NULL, 0, 1, '2019-02-21 09:56:08', '2019-02-21 09:56:08');

-- ----------------------------
-- Table structure for ibiart_slms_role_menus
-- ----------------------------
DROP TABLE IF EXISTS `ibiart_slms_role_menus`;
CREATE TABLE `ibiart_slms_role_menus`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` int(10) UNSIGNED NOT NULL,
  `menu_id` int(10) UNSIGNED NOT NULL,
  `checked` int(10) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 91 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ibiart_slms_role_menus
-- ----------------------------
INSERT INTO `ibiart_slms_role_menus` VALUES (1, 2, 1, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (2, 2, 2, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (3, 2, 3, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (4, 2, 4, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (5, 2, 5, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (6, 2, 6, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (7, 2, 7, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (8, 2, 8, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (9, 2, 9, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (10, 2, 10, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (11, 2, 11, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (12, 2, 12, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (13, 2, 13, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (14, 2, 14, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (15, 2, 15, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (16, 2, 16, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (17, 2, 17, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (18, 2, 18, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (19, 3, 1, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (20, 3, 2, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (21, 3, 3, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (22, 3, 4, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (23, 3, 5, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (24, 3, 6, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (25, 3, 7, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (26, 3, 8, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (27, 3, 9, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (28, 3, 10, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (29, 3, 11, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (30, 3, 12, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (31, 3, 13, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (32, 3, 14, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (33, 3, 15, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (34, 3, 16, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (35, 3, 17, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (36, 3, 18, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (37, 4, 1, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (38, 4, 2, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (39, 4, 3, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (40, 4, 4, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (41, 4, 5, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (42, 4, 6, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (43, 4, 7, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (44, 4, 8, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (45, 4, 9, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (46, 4, 10, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (47, 4, 11, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (48, 4, 12, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (49, 4, 13, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (50, 4, 14, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (51, 4, 15, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (52, 4, 16, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (53, 4, 17, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (54, 4, 18, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (55, 1, 1, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (56, 1, 2, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (57, 1, 3, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (58, 1, 4, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (59, 1, 5, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (60, 1, 6, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (61, 1, 7, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (62, 1, 8, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (63, 1, 9, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (64, 1, 10, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (65, 1, 11, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (66, 1, 12, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (67, 1, 13, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (68, 1, 14, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (69, 1, 15, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (70, 1, 16, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (71, 1, 17, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (72, 1, 18, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (73, 5, 1, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (74, 5, 2, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (75, 5, 3, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (76, 5, 4, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (77, 5, 5, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (78, 5, 6, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (79, 5, 7, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (80, 5, 8, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (81, 5, 9, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (82, 5, 10, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (83, 5, 11, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (84, 5, 12, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (85, 5, 13, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (86, 5, 14, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (87, 5, 15, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (88, 5, 16, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (89, 5, 17, 1);
INSERT INTO `ibiart_slms_role_menus` VALUES (90, 5, 18, 1);

-- ----------------------------
-- Table structure for ibiart_slms_roles
-- ----------------------------
DROP TABLE IF EXISTS `ibiart_slms_roles`;
CREATE TABLE `ibiart_slms_roles`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `is_default` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ibiart_slms_roles
-- ----------------------------
INSERT INTO `ibiart_slms_roles` VALUES (1, 'Admin', '管理员', 0, '2018-12-17 18:47:02', '2019-01-23 14:10:29');
INSERT INTO `ibiart_slms_roles` VALUES (2, 'Super_Admin', '超级管理员', 0, '2018-12-17 18:47:34', '2019-01-23 14:10:29');
INSERT INTO `ibiart_slms_roles` VALUES (3, 'Guest', '访客', 0, '2018-12-17 18:48:31', '2019-01-23 14:10:29');
INSERT INTO `ibiart_slms_roles` VALUES (4, 'User', '普通用户', 1, '2018-12-18 13:45:51', '2019-01-23 14:10:29');
INSERT INTO `ibiart_slms_roles` VALUES (5, 'Others', '其它用户', 0, '2018-12-20 11:13:59', '2019-01-23 14:10:29');

-- ----------------------------
-- Table structure for ibiart_slms_user_roles
-- ----------------------------
DROP TABLE IF EXISTS `ibiart_slms_user_roles`;
CREATE TABLE `ibiart_slms_user_roles`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for menus
-- ----------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `component` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `parent_id` int(10) UNSIGNED NULL DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `link_type` int(11) NULL DEFAULT 0,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `icon` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  `target` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '_self',
  `enabled` int(11) NOT NULL DEFAULT 1,
  `sort` int(11) NULL DEFAULT NULL,
  `created_user_id` int(11) NULL DEFAULT NULL,
  `updated_user_id` int(11) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of menus
-- ----------------------------
INSERT INTO `menus` VALUES (1, '决策分析', 'analysis', NULL, 0, '首页', '/', 0, NULL, 'md-analytics', '_self', 1, 1, 1, NULL, '2018-12-27 11:50:13', NULL);
INSERT INTO `menus` VALUES (2, '挂图作战', 'home', 'views/project/project-map/projectMap', 1, '挂图作战', 'projectMap', 0, NULL, NULL, '_self', 1, 1, 1, NULL, '2018-12-27 14:10:27', NULL);
INSERT INTO `menus` VALUES (3, '投资分析', 'analyse', 'views/sys/monitor/monitor', 1, '投资分析', 'projectAnalyse', 1, 'https://www.baidu.com/', NULL, '_self', 1, 2, 1, NULL, '2019-02-25 16:24:50', NULL);
INSERT INTO `menus` VALUES (4, '投资项目预警', 'projectEarlyWarning', 'views/project/project-early-warning/projectEarlyWarning', 1, '投资项目预警', 'projectEarlyWarning', 0, NULL, NULL, '_self', 1, 3, 1, NULL, '2019-02-25 16:25:44', NULL);
INSERT INTO `menus` VALUES (5, '投资管理', 'investment', NULL, 0, '项目管理', '/investment', 0, NULL, 'md-medkit', '_self', 1, 2, 1, NULL, '2019-02-16 19:02:54', NULL);
INSERT INTO `menus` VALUES (6, '投资项目库', 'projects', 'views/project/projects/projects', 5, NULL, 'projects', 0, NULL, NULL, '_self', 1, 1, 1, 1, '2019-02-25 17:01:46', '2019-02-26 10:42:15');
INSERT INTO `menus` VALUES (7, '项目投资计划', 'projectPlan', 'views/project/project-plan/projectPlan', 5, '投资项目库', 'projectPlan', 0, NULL, NULL, '_self', 1, 2, 1, NULL, '2019-02-16 19:04:47', NULL);
INSERT INTO `menus` VALUES (8, '投资项目进度填报', 'projectStatus', 'views/sys/monitor/monitor', 5, '投资项目进度填报', 'projectStatus', 1, 'http://localhost:5781/', NULL, '_self', 1, 3, 1, NULL, '2019-02-16 19:13:09', NULL);
INSERT INTO `menus` VALUES (9, '投资项目台账', 'projectAccount', 'views/sys/monitor/monitor', 5, '投资项目台账', 'projectAccount', 1, 'https://www.baidu.com/', NULL, '_self', 1, 4, 1, NULL, '2019-02-17 13:45:10', NULL);
INSERT INTO `menus` VALUES (10, '系统管理', 'sys-manage', NULL, 0, '系统管理', '/sys-manage', 0, NULL, 'md-briefcase', '_self', 1, 3, 1, NULL, '2019-01-03 14:36:56', NULL);
INSERT INTO `menus` VALUES (11, '用户管理', 'users', 'views/user/users', 10, '用户管理', 'users', 0, NULL, NULL, '_self', 1, 1, 1, NULL, '2019-01-25 11:08:19', NULL);
INSERT INTO `menus` VALUES (12, '角色权限管理', 'role-manage', 'views/user/role-manage', 10, '角色权限管理', 'role-manage', 0, NULL, NULL, '_self', 1, 2, 1, NULL, '2018-12-27 16:13:30', NULL);
INSERT INTO `menus` VALUES (13, '部门管理', 'department-manage', 'views/sys/department-manage/departmentManage', 10, '部门管理', 'department-manage/departmentManage', 0, NULL, NULL, '_self', 1, 3, 1, NULL, '2019-01-03 14:44:20', NULL);
INSERT INTO `menus` VALUES (14, '菜单管理', 'menuManage', 'views/sys/menu-manage/menuManage', 10, '重构菜单', 'menuManage', 0, NULL, NULL, '_self', 1, 4, 1, NULL, '2019-01-23 14:45:44', NULL);
INSERT INTO `menus` VALUES (15, '事件日志', 'eventlogs', 'views/sys/monitor/monitor', 10, '日志显示了程序中的潜在错误, 比如异常和调试信息。', 'eventlogs', 1, 'http://localhost:3114/logs', NULL, '_self', 1, 5, 1, NULL, '2019-01-09 16:37:32', NULL);
INSERT INTO `menus` VALUES (16, '操作日志', 'operationlogs', 'views/sys/operationlogs', 10, '记录系统功能操作日志', 'operationlogs', 0, NULL, NULL, '_self', 1, 6, 1, NULL, '2019-01-10 02:46:52', NULL);
INSERT INTO `menus` VALUES (17, '数据字典管理', 'dict', 'views/sys/dict-manage/dictManage', 10, '数据字典管理', 'dict-manage/dictManage', 0, NULL, NULL, '_self', 1, 7, 1, NULL, '2019-01-25 11:06:21', NULL);
INSERT INTO `menus` VALUES (18, '个人中心', 'profile', 'views/sys/profile/profile', 10, '个人中心', '/sys/profile', 0, NULL, NULL, '_self', 1, 8, 1, NULL, '2019-01-18 14:54:09', NULL);

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 31 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '2014_10_12_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '2014_10_12_100000_create_password_resets_table', 1);
INSERT INTO `migrations` VALUES (3, '2018_11_25_144044_create_department_table', 2);
INSERT INTO `migrations` VALUES (4, '2018_12_07_113125_create_menus_table', 3);
INSERT INTO `migrations` VALUES (5, '2018_12_16_213605_create_ibiart_slms_roles_table', 4);
INSERT INTO `migrations` VALUES (13, '2018_12_16_214318_create_ibiart_slms_role_menus_table', 5);
INSERT INTO `migrations` VALUES (14, '2018_12_18_145341_create_ibiart_slms_user_roles_table', 5);
INSERT INTO `migrations` VALUES (15, '2019_01_07_111419_create_iba_system_department_table', 6);
INSERT INTO `migrations` VALUES (22, '2019_01_09_192434_create_operation_log_table', 7);
INSERT INTO `migrations` VALUES (23, '2019_01_14_103115_create_iba_system_dict_table', 7);
INSERT INTO `migrations` VALUES (24, '2019_01_14_103133_create_iba_system_dict_data_table', 7);
INSERT INTO `migrations` VALUES (26, '2019_02_16_194843_create_iba_project_info_table', 8);
INSERT INTO `migrations` VALUES (27, '2019_02_17_135128_create_iba_project_early_warning_table', 9);
INSERT INTO `migrations` VALUES (28, '2019_02_25_173859_create_iba_project_projects_table', 10);
INSERT INTO `migrations` VALUES (30, '2019_02_27_150253_create_iba_project_plan_table', 11);

-- ----------------------------
-- Table structure for operation_log
-- ----------------------------
DROP TABLE IF EXISTS `operation_log`;
CREATE TABLE `operation_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `method` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ip_place` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 345 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of operation_log
-- ----------------------------
INSERT INTO `operation_log` VALUES (1, '创建菜单', 'POST', 'api/menu/add', '::1', '', 0, '2019-01-18 14:12:57', NULL);
INSERT INTO `operation_log` VALUES (2, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-18 14:13:12', NULL);
INSERT INTO `operation_log` VALUES (3, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-18 14:16:42', NULL);
INSERT INTO `operation_log` VALUES (4, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-18 14:16:56', NULL);
INSERT INTO `operation_log` VALUES (5, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-18 14:18:25', NULL);
INSERT INTO `operation_log` VALUES (6, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-18 14:26:58', NULL);
INSERT INTO `operation_log` VALUES (7, '创建菜单', 'POST', 'api/menu/add', '::1', '', 1, '2019-01-18 14:54:09', NULL);
INSERT INTO `operation_log` VALUES (8, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-18 14:55:45', NULL);
INSERT INTO `operation_log` VALUES (9, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-18 14:56:23', NULL);
INSERT INTO `operation_log` VALUES (10, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-18 15:30:15', NULL);
INSERT INTO `operation_log` VALUES (11, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-18 17:15:05', NULL);
INSERT INTO `operation_log` VALUES (12, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-18 17:35:39', NULL);
INSERT INTO `operation_log` VALUES (13, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-18 17:36:01', NULL);
INSERT INTO `operation_log` VALUES (14, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-21 09:46:19', NULL);
INSERT INTO `operation_log` VALUES (15, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-21 11:25:00', NULL);
INSERT INTO `operation_log` VALUES (16, '修改密码', 'POST', 'api/user/resetPassword', '::1', '', 1, '2019-01-21 11:26:32', NULL);
INSERT INTO `operation_log` VALUES (17, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-21 11:26:57', NULL);
INSERT INTO `operation_log` VALUES (18, '修改密码', 'POST', 'api/user/resetPassword', '::1', '', 1, '2019-01-21 11:32:54', NULL);
INSERT INTO `operation_log` VALUES (19, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-21 11:34:44', NULL);
INSERT INTO `operation_log` VALUES (20, '修改密码', 'POST', 'api/user/resetPassword', '::1', '', 1, '2019-01-21 11:40:41', NULL);
INSERT INTO `operation_log` VALUES (21, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-21 11:40:54', NULL);
INSERT INTO `operation_log` VALUES (22, '修改密码', 'POST', 'api/user/resetPassword', '::1', '', 1, '2019-01-21 11:41:09', NULL);
INSERT INTO `operation_log` VALUES (23, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-21 11:41:13', NULL);
INSERT INTO `operation_log` VALUES (24, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-21 13:53:17', NULL);
INSERT INTO `operation_log` VALUES (25, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-21 16:24:37', NULL);
INSERT INTO `operation_log` VALUES (26, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-22 10:36:58', NULL);
INSERT INTO `operation_log` VALUES (27, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-22 14:17:05', NULL);
INSERT INTO `operation_log` VALUES (28, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-22 15:45:18', NULL);
INSERT INTO `operation_log` VALUES (29, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-22 17:43:18', NULL);
INSERT INTO `operation_log` VALUES (30, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-22 18:47:04', NULL);
INSERT INTO `operation_log` VALUES (31, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-22 18:53:26', NULL);
INSERT INTO `operation_log` VALUES (32, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-22 21:35:18', NULL);
INSERT INTO `operation_log` VALUES (33, '创建用户', 'POST', 'api/user/regist', '::1', '', 1, '2019-01-22 21:46:38', NULL);
INSERT INTO `operation_log` VALUES (34, '创建用户', 'POST', 'api/user/regist', '::1', '', 1, '2019-01-22 21:51:54', NULL);
INSERT INTO `operation_log` VALUES (35, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 09:52:39', NULL);
INSERT INTO `operation_log` VALUES (36, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 10:00:15', NULL);
INSERT INTO `operation_log` VALUES (37, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 11:04:50', NULL);
INSERT INTO `operation_log` VALUES (38, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 14:09:52', NULL);
INSERT INTO `operation_log` VALUES (39, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 14:17:36', NULL);
INSERT INTO `operation_log` VALUES (40, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 14:18:08', NULL);
INSERT INTO `operation_log` VALUES (41, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 14:20:11', NULL);
INSERT INTO `operation_log` VALUES (42, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 14:20:45', NULL);
INSERT INTO `operation_log` VALUES (43, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 14:22:40', NULL);
INSERT INTO `operation_log` VALUES (44, '创建菜单', 'POST', 'api/menu/add', '::1', '', 1, '2019-01-23 14:45:44', NULL);
INSERT INTO `operation_log` VALUES (45, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-23 14:46:08', NULL);
INSERT INTO `operation_log` VALUES (46, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-23 14:46:15', NULL);
INSERT INTO `operation_log` VALUES (47, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 15:24:04', NULL);
INSERT INTO `operation_log` VALUES (48, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 16:24:36', NULL);
INSERT INTO `operation_log` VALUES (49, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 16:49:09', NULL);
INSERT INTO `operation_log` VALUES (50, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 17:57:22', NULL);
INSERT INTO `operation_log` VALUES (51, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 18:00:03', NULL);
INSERT INTO `operation_log` VALUES (52, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-23 18:02:09', NULL);
INSERT INTO `operation_log` VALUES (53, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-24 09:17:54', NULL);
INSERT INTO `operation_log` VALUES (54, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-24 09:30:02', NULL);
INSERT INTO `operation_log` VALUES (55, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-24 09:34:51', NULL);
INSERT INTO `operation_log` VALUES (56, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-24 10:35:03', NULL);
INSERT INTO `operation_log` VALUES (57, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-24 11:38:33', NULL);
INSERT INTO `operation_log` VALUES (58, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-24 14:40:07', NULL);
INSERT INTO `operation_log` VALUES (59, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-24 15:42:30', NULL);
INSERT INTO `operation_log` VALUES (60, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-01-24 15:47:39', NULL);
INSERT INTO `operation_log` VALUES (61, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-24 15:48:08', NULL);
INSERT INTO `operation_log` VALUES (62, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-24 15:48:21', NULL);
INSERT INTO `operation_log` VALUES (63, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-01-24 15:57:29', NULL);
INSERT INTO `operation_log` VALUES (64, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-01-24 15:58:21', NULL);
INSERT INTO `operation_log` VALUES (65, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-01-24 16:26:03', NULL);
INSERT INTO `operation_log` VALUES (66, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-01-24 16:30:31', NULL);
INSERT INTO `operation_log` VALUES (67, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-24 16:48:37', NULL);
INSERT INTO `operation_log` VALUES (68, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-24 18:53:54', NULL);
INSERT INTO `operation_log` VALUES (69, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-25 09:34:29', NULL);
INSERT INTO `operation_log` VALUES (70, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 0, '2019-01-25 11:01:50', NULL);
INSERT INTO `operation_log` VALUES (71, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 0, '2019-01-25 11:03:28', NULL);
INSERT INTO `operation_log` VALUES (72, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 0, '2019-01-25 11:05:29', NULL);
INSERT INTO `operation_log` VALUES (73, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 0, '2019-01-25 11:06:22', NULL);
INSERT INTO `operation_log` VALUES (74, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 0, '2019-01-25 11:08:19', NULL);
INSERT INTO `operation_log` VALUES (75, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-25 11:09:56', NULL);
INSERT INTO `operation_log` VALUES (76, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-25 11:29:54', NULL);
INSERT INTO `operation_log` VALUES (77, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-25 11:32:31', NULL);
INSERT INTO `operation_log` VALUES (78, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-25 11:53:42', NULL);
INSERT INTO `operation_log` VALUES (79, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-25 11:54:16', NULL);
INSERT INTO `operation_log` VALUES (80, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-25 11:54:25', NULL);
INSERT INTO `operation_log` VALUES (81, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-25 11:54:38', NULL);
INSERT INTO `operation_log` VALUES (82, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-25 11:55:10', NULL);
INSERT INTO `operation_log` VALUES (83, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-25 11:56:41', NULL);
INSERT INTO `operation_log` VALUES (84, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-25 15:06:57', NULL);
INSERT INTO `operation_log` VALUES (85, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 1, '2019-01-25 15:52:03', NULL);
INSERT INTO `operation_log` VALUES (86, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-25 16:38:39', NULL);
INSERT INTO `operation_log` VALUES (87, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-26 10:12:53', NULL);
INSERT INTO `operation_log` VALUES (88, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-26 10:37:47', NULL);
INSERT INTO `operation_log` VALUES (89, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-26 10:38:29', NULL);
INSERT INTO `operation_log` VALUES (90, '用户登录', 'POST', 'api/login', '::1', '', 2, '2019-01-26 10:38:52', NULL);
INSERT INTO `operation_log` VALUES (91, '用户登录', 'POST', 'api/login', '::1', '', 2, '2019-01-26 10:49:38', NULL);
INSERT INTO `operation_log` VALUES (92, '用户登录', 'POST', 'api/login', '::1', '', 2, '2019-01-26 11:05:49', NULL);
INSERT INTO `operation_log` VALUES (93, '用户登录', 'POST', 'api/login', '::1', '', 2, '2019-01-26 11:06:51', NULL);
INSERT INTO `operation_log` VALUES (94, '用户登录', 'POST', 'api/login', '::1', '', 2, '2019-01-26 11:08:42', NULL);
INSERT INTO `operation_log` VALUES (95, '用户登录', 'POST', 'api/login', '::1', '', 2, '2019-01-26 11:09:32', NULL);
INSERT INTO `operation_log` VALUES (96, '用户登录', 'POST', 'api/login', '::1', '', 2, '2019-01-26 11:10:14', NULL);
INSERT INTO `operation_log` VALUES (97, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-26 11:10:28', NULL);
INSERT INTO `operation_log` VALUES (98, '用户登录', 'POST', 'api/login', '::1', '', 2, '2019-01-26 11:11:19', NULL);
INSERT INTO `operation_log` VALUES (99, '用户登录', 'POST', 'api/login', '::1', '', 2, '2019-01-26 11:15:52', NULL);
INSERT INTO `operation_log` VALUES (100, '用户登录', 'POST', 'api/login', '::1', '', 2, '2019-01-26 11:23:56', NULL);
INSERT INTO `operation_log` VALUES (101, '用户登录', 'POST', 'api/login', '::1', '', 2, '2019-01-26 11:28:41', NULL);
INSERT INTO `operation_log` VALUES (102, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-26 11:29:00', NULL);
INSERT INTO `operation_log` VALUES (103, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-26 11:30:05', NULL);
INSERT INTO `operation_log` VALUES (104, '用户登录', 'POST', 'api/login', '::1', '', 2, '2019-01-26 11:30:18', NULL);
INSERT INTO `operation_log` VALUES (105, '用户登录', 'POST', 'api/login', '::1', '', 2, '2019-01-26 11:47:04', NULL);
INSERT INTO `operation_log` VALUES (106, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-26 12:58:32', NULL);
INSERT INTO `operation_log` VALUES (107, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-26 14:41:12', NULL);
INSERT INTO `operation_log` VALUES (108, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-26 16:31:24', NULL);
INSERT INTO `operation_log` VALUES (109, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-26 17:33:26', NULL);
INSERT INTO `operation_log` VALUES (110, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-27 14:23:18', NULL);
INSERT INTO `operation_log` VALUES (111, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-27 14:26:50', NULL);
INSERT INTO `operation_log` VALUES (112, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-27 14:37:28', NULL);
INSERT INTO `operation_log` VALUES (113, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 1, '2019-01-27 15:24:30', NULL);
INSERT INTO `operation_log` VALUES (114, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-27 15:26:44', NULL);
INSERT INTO `operation_log` VALUES (115, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-27 15:49:44', NULL);
INSERT INTO `operation_log` VALUES (116, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 1, '2019-01-27 15:58:30', NULL);
INSERT INTO `operation_log` VALUES (117, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 1, '2019-01-27 16:02:31', NULL);
INSERT INTO `operation_log` VALUES (118, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-27 16:15:36', NULL);
INSERT INTO `operation_log` VALUES (119, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-01-27 16:23:38', NULL);
INSERT INTO `operation_log` VALUES (120, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-27 16:23:55', NULL);
INSERT INTO `operation_log` VALUES (121, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-01-27 16:29:38', NULL);
INSERT INTO `operation_log` VALUES (122, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-27 16:29:56', NULL);
INSERT INTO `operation_log` VALUES (123, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-01-27 16:31:47', NULL);
INSERT INTO `operation_log` VALUES (124, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-27 16:32:05', NULL);
INSERT INTO `operation_log` VALUES (125, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-01-27 16:34:00', NULL);
INSERT INTO `operation_log` VALUES (126, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-01-27 16:34:13', NULL);
INSERT INTO `operation_log` VALUES (127, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 1, '2019-01-27 16:44:33', NULL);
INSERT INTO `operation_log` VALUES (128, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-27 16:46:24', NULL);
INSERT INTO `operation_log` VALUES (129, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-01-27 17:15:27', NULL);
INSERT INTO `operation_log` VALUES (130, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-13 09:30:28', NULL);
INSERT INTO `operation_log` VALUES (131, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-13 15:56:55', NULL);
INSERT INTO `operation_log` VALUES (132, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-15 16:22:23', NULL);
INSERT INTO `operation_log` VALUES (133, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-16 18:55:26', NULL);
INSERT INTO `operation_log` VALUES (134, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-02-16 19:02:54', NULL);
INSERT INTO `operation_log` VALUES (135, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-02-16 19:04:47', NULL);
INSERT INTO `operation_log` VALUES (136, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-02-16 19:13:09', NULL);
INSERT INTO `operation_log` VALUES (137, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-02-16 19:14:22', NULL);
INSERT INTO `operation_log` VALUES (138, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-16 19:14:35', NULL);
INSERT INTO `operation_log` VALUES (139, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-16 19:21:22', NULL);
INSERT INTO `operation_log` VALUES (140, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-16 19:29:10', NULL);
INSERT INTO `operation_log` VALUES (141, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-16 19:45:34', NULL);
INSERT INTO `operation_log` VALUES (142, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-16 20:51:43', NULL);
INSERT INTO `operation_log` VALUES (143, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-16 22:13:34', NULL);
INSERT INTO `operation_log` VALUES (144, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-16 23:19:59', NULL);
INSERT INTO `operation_log` VALUES (145, '创建项目信息', 'POST', 'api/project/addDepartment', '::1', '', 1, '2019-02-17 00:10:25', NULL);
INSERT INTO `operation_log` VALUES (146, '创建项目信息', 'POST', 'api/project/addDepartment', '::1', '', 1, '2019-02-17 00:12:11', NULL);
INSERT INTO `operation_log` VALUES (147, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-17 00:20:14', NULL);
INSERT INTO `operation_log` VALUES (148, '创建项目信息', 'POST', 'api/project/addDepartment', '::1', '', 1, '2019-02-17 01:04:07', NULL);
INSERT INTO `operation_log` VALUES (149, '修改项目信息', 'POST', 'api/project/editDepartment', '::1', '', 1, '2019-02-17 01:21:29', NULL);
INSERT INTO `operation_log` VALUES (150, '修改项目信息', 'POST', 'api/project/editDepartment', '::1', '', 0, '2019-02-17 01:21:43', NULL);
INSERT INTO `operation_log` VALUES (151, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-17 01:22:15', NULL);
INSERT INTO `operation_log` VALUES (152, '修改项目信息', 'POST', 'api/project/editDepartment', '::1', '', 1, '2019-02-17 01:22:29', NULL);
INSERT INTO `operation_log` VALUES (153, '修改项目信息', 'POST', 'api/project/editDepartment', '::1', '', 1, '2019-02-17 01:22:38', NULL);
INSERT INTO `operation_log` VALUES (154, '修改项目信息', 'POST', 'api/project/editDepartment', '::1', '', 1, '2019-02-17 01:22:47', NULL);
INSERT INTO `operation_log` VALUES (155, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-17 02:14:03', NULL);
INSERT INTO `operation_log` VALUES (156, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-17 08:34:00', NULL);
INSERT INTO `operation_log` VALUES (157, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-17 09:57:12', NULL);
INSERT INTO `operation_log` VALUES (158, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-17 11:12:32', NULL);
INSERT INTO `operation_log` VALUES (159, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-17 12:13:05', NULL);
INSERT INTO `operation_log` VALUES (160, '创建项目信息', 'POST', 'api/project/addDepartment', '::1', '', 1, '2019-02-17 13:10:21', NULL);
INSERT INTO `operation_log` VALUES (161, '创建项目信息', 'POST', 'api/project/addDepartment', '::1', '', 1, '2019-02-17 13:10:55', NULL);
INSERT INTO `operation_log` VALUES (162, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-17 13:23:01', NULL);
INSERT INTO `operation_log` VALUES (163, '创建项目信息', 'POST', 'api/project/addDepartment', '::1', '', 1, '2019-02-17 13:24:18', NULL);
INSERT INTO `operation_log` VALUES (164, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-02-17 13:45:10', NULL);
INSERT INTO `operation_log` VALUES (165, '设置菜单权限', 'POST', 'api/role/setrolemenus', '::1', '', 1, '2019-02-17 13:45:22', NULL);
INSERT INTO `operation_log` VALUES (166, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-17 14:36:19', NULL);
INSERT INTO `operation_log` VALUES (167, '创建项目信息', 'POST', 'api/project/addDepartment', '::1', '', 1, '2019-02-17 14:42:07', NULL);
INSERT INTO `operation_log` VALUES (168, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-17 15:38:30', NULL);
INSERT INTO `operation_log` VALUES (169, '修改项目信息', 'POST', 'api/project/editDepartment', '::1', '', 1, '2019-02-17 16:04:56', NULL);
INSERT INTO `operation_log` VALUES (170, '用户登录', 'POST', 'api/login', '47.104.102.128', '', 1, '2019-02-17 20:30:35', NULL);
INSERT INTO `operation_log` VALUES (171, '创建用户', 'POST', 'api/user/regist', '47.104.102.128', '', 1, '2019-02-17 20:32:11', NULL);
INSERT INTO `operation_log` VALUES (172, '设置菜单权限', 'POST', 'api/role/setrolemenus', '47.104.102.128', '', 1, '2019-02-17 20:32:26', NULL);
INSERT INTO `operation_log` VALUES (173, '用户登录', 'POST', 'api/login', '47.104.102.128', '', 6, '2019-02-17 20:32:41', NULL);
INSERT INTO `operation_log` VALUES (174, '用户登录', 'POST', 'api/login', '1.85.200.177', '', 1, '2019-02-17 20:33:30', NULL);
INSERT INTO `operation_log` VALUES (175, '用户登录', 'POST', 'api/login', '113.201.134.75', '', 6, '2019-02-17 20:33:58', NULL);
INSERT INTO `operation_log` VALUES (176, '用户登录', 'POST', 'api/login', '1.85.200.177', '', 6, '2019-02-17 20:34:06', NULL);
INSERT INTO `operation_log` VALUES (177, '设置菜单权限', 'POST', 'api/role/setrolemenus', '113.201.134.75', '', 6, '2019-02-17 20:34:12', NULL);
INSERT INTO `operation_log` VALUES (178, '修改部门', 'POST', 'api/department/editDepartment', '1.85.200.177', '', 6, '2019-02-17 20:39:29', NULL);
INSERT INTO `operation_log` VALUES (179, '修改部门', 'POST', 'api/department/editDepartment', '1.85.200.177', '', 6, '2019-02-17 20:39:58', NULL);
INSERT INTO `operation_log` VALUES (180, '修改部门', 'POST', 'api/department/editDepartment', '113.201.134.75', '', 6, '2019-02-17 20:42:24', NULL);
INSERT INTO `operation_log` VALUES (181, '修改菜单', 'POST', 'api/menu/editMenu', '1.85.200.177', '', 6, '2019-02-17 20:46:47', NULL);
INSERT INTO `operation_log` VALUES (182, '修改菜单', 'POST', 'api/menu/editMenu', '1.85.200.177', '', 6, '2019-02-17 20:47:22', NULL);
INSERT INTO `operation_log` VALUES (183, '修改菜单', 'POST', 'api/menu/editMenu', '1.85.200.177', '', 6, '2019-02-17 20:47:35', NULL);
INSERT INTO `operation_log` VALUES (184, '设置菜单权限', 'POST', 'api/role/setrolemenus', '113.201.134.75', '', 6, '2019-02-17 20:51:45', NULL);
INSERT INTO `operation_log` VALUES (185, '用户登录', 'POST', 'api/login', '113.201.134.75', '', 6, '2019-02-17 21:13:31', NULL);
INSERT INTO `operation_log` VALUES (186, '用户登录', 'POST', 'api/login', '113.201.134.75', '', 6, '2019-02-17 21:21:09', NULL);
INSERT INTO `operation_log` VALUES (187, '创建项目信息', 'POST', 'api/project/addDepartment', '1.85.200.177', '', 6, '2019-02-17 21:27:35', NULL);
INSERT INTO `operation_log` VALUES (188, '创建项目信息', 'POST', 'api/project/addDepartment', '1.85.200.177', '', 6, '2019-02-17 21:29:01', NULL);
INSERT INTO `operation_log` VALUES (189, '创建项目信息', 'POST', 'api/project/addDepartment', '1.85.200.177', '', 6, '2019-02-17 21:30:46', NULL);
INSERT INTO `operation_log` VALUES (190, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:31:58', NULL);
INSERT INTO `operation_log` VALUES (191, '用户登录', 'POST', 'api/login', '113.201.134.75', '', 6, '2019-02-17 21:32:21', NULL);
INSERT INTO `operation_log` VALUES (192, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:32:23', NULL);
INSERT INTO `operation_log` VALUES (193, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:32:28', NULL);
INSERT INTO `operation_log` VALUES (194, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:32:35', NULL);
INSERT INTO `operation_log` VALUES (195, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:32:43', NULL);
INSERT INTO `operation_log` VALUES (196, '修改项目信息', 'POST', 'api/project/editDepartment', '113.201.134.75', '', 6, '2019-02-17 21:33:10', NULL);
INSERT INTO `operation_log` VALUES (197, '创建项目信息', 'POST', 'api/project/addDepartment', '113.201.134.75', '', 6, '2019-02-17 21:33:53', NULL);
INSERT INTO `operation_log` VALUES (198, '创建项目信息', 'POST', 'api/project/addDepartment', '1.85.200.177', '', 6, '2019-02-17 21:34:05', NULL);
INSERT INTO `operation_log` VALUES (199, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 0, '2019-02-17 21:34:40', NULL);
INSERT INTO `operation_log` VALUES (200, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 0, '2019-02-17 21:35:09', NULL);
INSERT INTO `operation_log` VALUES (201, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 0, '2019-02-17 21:35:40', NULL);
INSERT INTO `operation_log` VALUES (202, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 0, '2019-02-17 21:36:09', NULL);
INSERT INTO `operation_log` VALUES (203, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 0, '2019-02-17 21:36:15', NULL);
INSERT INTO `operation_log` VALUES (204, '创建项目信息', 'POST', 'api/project/addDepartment', '1.85.200.177', '', 0, '2019-02-17 21:39:25', NULL);
INSERT INTO `operation_log` VALUES (205, '用户登录', 'POST', 'api/login', '1.85.200.177', '', 6, '2019-02-17 21:41:06', NULL);
INSERT INTO `operation_log` VALUES (206, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:43:04', NULL);
INSERT INTO `operation_log` VALUES (207, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:43:14', NULL);
INSERT INTO `operation_log` VALUES (208, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:43:24', NULL);
INSERT INTO `operation_log` VALUES (209, '创建项目信息', 'POST', 'api/project/addDepartment', '1.85.200.177', '', 6, '2019-02-17 21:45:44', NULL);
INSERT INTO `operation_log` VALUES (210, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:45:59', NULL);
INSERT INTO `operation_log` VALUES (211, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:46:15', NULL);
INSERT INTO `operation_log` VALUES (212, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:46:37', NULL);
INSERT INTO `operation_log` VALUES (213, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:46:44', NULL);
INSERT INTO `operation_log` VALUES (214, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:46:49', NULL);
INSERT INTO `operation_log` VALUES (215, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:46:56', NULL);
INSERT INTO `operation_log` VALUES (216, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:48:10', NULL);
INSERT INTO `operation_log` VALUES (217, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:49:00', NULL);
INSERT INTO `operation_log` VALUES (218, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:49:19', NULL);
INSERT INTO `operation_log` VALUES (219, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 21:49:29', NULL);
INSERT INTO `operation_log` VALUES (220, '创建项目信息', 'POST', 'api/project/addDepartment', '1.85.200.177', '', 6, '2019-02-17 21:51:01', NULL);
INSERT INTO `operation_log` VALUES (221, '用户登录', 'POST', 'api/login', '123.139.25.141', '', 6, '2019-02-17 22:09:07', NULL);
INSERT INTO `operation_log` VALUES (222, '创建项目信息', 'POST', 'api/project/addDepartment', '1.85.200.177', '', 6, '2019-02-17 22:10:10', NULL);
INSERT INTO `operation_log` VALUES (223, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 22:10:23', NULL);
INSERT INTO `operation_log` VALUES (224, '修改项目信息', 'POST', 'api/project/editDepartment', '123.139.25.141', '', 6, '2019-02-17 22:10:57', NULL);
INSERT INTO `operation_log` VALUES (225, '用户登录', 'POST', 'api/login', '1.85.200.177', '', 6, '2019-02-17 22:59:38', NULL);
INSERT INTO `operation_log` VALUES (226, '修改项目信息', 'POST', 'api/project/editDepartment', '1.85.200.177', '', 6, '2019-02-17 23:01:02', NULL);
INSERT INTO `operation_log` VALUES (227, '用户登录', 'POST', 'api/login', '113.201.134.75', '', 6, '2019-02-17 23:21:50', NULL);
INSERT INTO `operation_log` VALUES (228, '修改项目信息', 'POST', 'api/project/editDepartment', '113.201.134.75', '', 6, '2019-02-18 00:07:19', NULL);
INSERT INTO `operation_log` VALUES (229, '修改项目信息', 'POST', 'api/project/editDepartment', '113.201.134.75', '', 6, '2019-02-18 00:07:22', NULL);
INSERT INTO `operation_log` VALUES (230, '修改项目信息', 'POST', 'api/project/editDepartment', '113.201.134.75', '', 6, '2019-02-18 00:07:25', NULL);
INSERT INTO `operation_log` VALUES (231, '修改项目信息', 'POST', 'api/project/editDepartment', '113.201.134.75', '', 6, '2019-02-18 00:07:27', NULL);
INSERT INTO `operation_log` VALUES (232, '修改项目信息', 'POST', 'api/project/editDepartment', '113.201.134.75', '', 6, '2019-02-18 00:07:30', NULL);
INSERT INTO `operation_log` VALUES (233, '修改项目信息', 'POST', 'api/project/editDepartment', '113.201.134.75', '', 6, '2019-02-18 00:07:33', NULL);
INSERT INTO `operation_log` VALUES (234, '用户登录', 'POST', 'api/login', '106.36.217.172', '', 6, '2019-02-18 09:23:52', NULL);
INSERT INTO `operation_log` VALUES (235, '用户登录', 'POST', 'api/login', '1.80.82.181', '', 6, '2019-02-18 10:30:17', NULL);
INSERT INTO `operation_log` VALUES (236, '修改菜单', 'POST', 'api/menu/editMenu', '1.80.82.181', '', 6, '2019-02-18 10:30:49', NULL);
INSERT INTO `operation_log` VALUES (237, '修改菜单', 'POST', 'api/menu/editMenu', '1.80.82.181', '', 6, '2019-02-18 10:31:34', NULL);
INSERT INTO `operation_log` VALUES (238, '用户登录', 'POST', 'api/login', '113.200.204.51', '', 6, '2019-02-18 14:23:40', NULL);
INSERT INTO `operation_log` VALUES (239, '用户登录', 'POST', 'api/login', '113.200.204.51', '', 1, '2019-02-18 14:26:18', NULL);
INSERT INTO `operation_log` VALUES (240, '用户登录', 'POST', 'api/login', '221.11.69.110', '', 6, '2019-02-18 14:43:42', NULL);
INSERT INTO `operation_log` VALUES (241, '修改菜单', 'POST', 'api/menu/editMenu', '221.11.69.110', '', 6, '2019-02-18 14:47:28', NULL);
INSERT INTO `operation_log` VALUES (242, '用户登录', 'POST', 'api/login', '1.80.82.181', '', 6, '2019-02-18 18:05:12', NULL);
INSERT INTO `operation_log` VALUES (243, '用户登录', 'POST', 'api/login', '117.35.134.173', '', 6, '2019-02-20 10:35:17', NULL);
INSERT INTO `operation_log` VALUES (244, '用户登录', 'POST', 'api/login', '117.35.134.173', '', 6, '2019-02-20 15:02:02', NULL);
INSERT INTO `operation_log` VALUES (245, '用户登录', 'POST', 'api/login', '117.35.134.173', '', 6, '2019-02-20 17:02:37', NULL);
INSERT INTO `operation_log` VALUES (246, '用户登录', 'POST', 'api/login', '1.86.247.197', '', 6, '2019-02-21 09:17:41', NULL);
INSERT INTO `operation_log` VALUES (247, '用户登录', 'POST', 'api/login', '123.139.83.183', '', 6, '2019-02-21 09:39:18', NULL);
INSERT INTO `operation_log` VALUES (248, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 1, '2019-02-21 10:02:48', NULL);
INSERT INTO `operation_log` VALUES (249, '修改项目信息', 'POST', 'api/project/editDepartment', '::1', '', 1, '2019-02-21 10:13:36', NULL);
INSERT INTO `operation_log` VALUES (250, '修改项目信息', 'POST', 'api/project/editDepartment', '::1', '', 1, '2019-02-21 10:13:56', NULL);
INSERT INTO `operation_log` VALUES (251, '修改项目信息', 'POST', 'api/project/editDepartment', '::1', '', 1, '2019-02-21 10:35:44', NULL);
INSERT INTO `operation_log` VALUES (252, '修改项目信息', 'POST', 'api/project/editDepartment', '::1', '', 1, '2019-02-21 10:49:42', NULL);
INSERT INTO `operation_log` VALUES (253, '修改项目信息', 'POST', 'api/project/editDepartment', '::1', '', 1, '2019-02-21 10:50:03', NULL);
INSERT INTO `operation_log` VALUES (254, '创建项目信息', 'POST', 'api/project/addDepartment', '::1', '', 1, '2019-02-21 10:50:29', NULL);
INSERT INTO `operation_log` VALUES (255, '创建项目信息', 'POST', 'api/project/addDepartment', '::1', '', 1, '2019-02-21 10:52:46', NULL);
INSERT INTO `operation_log` VALUES (256, '创建项目信息', 'POST', 'api/project/addDepartment', '::1', '', 1, '2019-02-21 10:53:16', NULL);
INSERT INTO `operation_log` VALUES (257, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-21 10:54:23', NULL);
INSERT INTO `operation_log` VALUES (258, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-21 11:56:04', NULL);
INSERT INTO `operation_log` VALUES (259, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-21 16:08:39', NULL);
INSERT INTO `operation_log` VALUES (260, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-21 16:46:04', NULL);
INSERT INTO `operation_log` VALUES (261, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-22 09:45:09', NULL);
INSERT INTO `operation_log` VALUES (262, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-22 10:57:01', NULL);
INSERT INTO `operation_log` VALUES (263, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-22 12:05:01', NULL);
INSERT INTO `operation_log` VALUES (264, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-22 14:47:53', NULL);
INSERT INTO `operation_log` VALUES (265, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-22 16:22:48', NULL);
INSERT INTO `operation_log` VALUES (266, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-22 18:11:30', NULL);
INSERT INTO `operation_log` VALUES (267, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-25 10:01:40', NULL);
INSERT INTO `operation_log` VALUES (268, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-25 11:14:59', NULL);
INSERT INTO `operation_log` VALUES (269, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-25 15:53:54', NULL);
INSERT INTO `operation_log` VALUES (270, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 1, '2019-02-25 16:21:09', NULL);
INSERT INTO `operation_log` VALUES (271, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 1, '2019-02-25 16:22:57', NULL);
INSERT INTO `operation_log` VALUES (272, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-02-25 16:24:50', NULL);
INSERT INTO `operation_log` VALUES (273, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-02-25 16:25:44', NULL);
INSERT INTO `operation_log` VALUES (274, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-25 16:26:32', NULL);
INSERT INTO `operation_log` VALUES (275, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-25 16:28:22', NULL);
INSERT INTO `operation_log` VALUES (276, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-25 16:30:00', NULL);
INSERT INTO `operation_log` VALUES (277, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-25 16:31:09', NULL);
INSERT INTO `operation_log` VALUES (278, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-25 16:39:14', NULL);
INSERT INTO `operation_log` VALUES (279, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-25 16:41:36', NULL);
INSERT INTO `operation_log` VALUES (280, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-25 16:42:56', NULL);
INSERT INTO `operation_log` VALUES (281, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 1, '2019-02-25 16:54:01', NULL);
INSERT INTO `operation_log` VALUES (282, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 1, '2019-02-25 16:54:31', NULL);
INSERT INTO `operation_log` VALUES (283, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 1, '2019-02-25 16:54:44', NULL);
INSERT INTO `operation_log` VALUES (284, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 1, '2019-02-25 16:54:56', NULL);
INSERT INTO `operation_log` VALUES (285, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 1, '2019-02-25 16:59:53', NULL);
INSERT INTO `operation_log` VALUES (286, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-02-25 17:01:46', NULL);
INSERT INTO `operation_log` VALUES (287, '创建菜单', 'POST', 'api/menu/addMenu', '::1', '', 1, '2019-02-25 17:07:02', NULL);
INSERT INTO `operation_log` VALUES (288, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-25 17:22:19', NULL);
INSERT INTO `operation_log` VALUES (289, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-25 17:23:10', NULL);
INSERT INTO `operation_log` VALUES (290, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-26 09:24:24', NULL);
INSERT INTO `operation_log` VALUES (291, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-26 10:38:59', NULL);
INSERT INTO `operation_log` VALUES (292, '修改菜单', 'POST', 'api/menu/editMenu', '::1', '', 1, '2019-02-26 10:42:15', NULL);
INSERT INTO `operation_log` VALUES (293, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-26 13:54:25', NULL);
INSERT INTO `operation_log` VALUES (294, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-26 14:00:15', NULL);
INSERT INTO `operation_log` VALUES (295, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-26 15:12:56', NULL);
INSERT INTO `operation_log` VALUES (296, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-26 16:24:22', NULL);
INSERT INTO `operation_log` VALUES (297, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-26 17:32:06', NULL);
INSERT INTO `operation_log` VALUES (298, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-26 17:36:25', NULL);
INSERT INTO `operation_log` VALUES (299, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-26 22:27:53', NULL);
INSERT INTO `operation_log` VALUES (300, '创建项目信息', 'POST', 'api/project/addProject', '::1', '', 1, '2019-02-26 22:29:01', NULL);
INSERT INTO `operation_log` VALUES (301, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-27 09:33:01', NULL);
INSERT INTO `operation_log` VALUES (302, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-27 10:46:01', NULL);
INSERT INTO `operation_log` VALUES (303, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-27 14:16:04', NULL);
INSERT INTO `operation_log` VALUES (304, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-27 14:25:35', NULL);
INSERT INTO `operation_log` VALUES (305, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-27 15:30:38', NULL);
INSERT INTO `operation_log` VALUES (306, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-27 16:35:48', NULL);
INSERT INTO `operation_log` VALUES (307, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-27 18:00:50', NULL);
INSERT INTO `operation_log` VALUES (308, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-27 19:11:41', NULL);
INSERT INTO `operation_log` VALUES (309, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-27 22:10:49', NULL);
INSERT INTO `operation_log` VALUES (310, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-28 09:42:46', NULL);
INSERT INTO `operation_log` VALUES (311, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-28 09:45:32', NULL);
INSERT INTO `operation_log` VALUES (312, '创建项目信息', 'POST', 'api/project/addProject', '::1', '', 1, '2019-02-28 10:24:14', NULL);
INSERT INTO `operation_log` VALUES (313, '创建项目信息', 'POST', 'api/project/addProject', '::1', '', 1, '2019-02-28 10:24:51', NULL);
INSERT INTO `operation_log` VALUES (314, '创建项目信息', 'POST', 'api/project/addProject', '::1', '', 1, '2019-02-28 10:25:01', NULL);
INSERT INTO `operation_log` VALUES (315, '创建项目信息', 'POST', 'api/project/addProject', '::1', '', 1, '2019-02-28 10:34:13', NULL);
INSERT INTO `operation_log` VALUES (316, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-28 10:52:48', NULL);
INSERT INTO `operation_log` VALUES (317, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-28 11:19:58', NULL);
INSERT INTO `operation_log` VALUES (318, '创建项目信息', 'POST', 'api/project/addProject', '::1', '', 1, '2019-02-28 11:24:38', NULL);
INSERT INTO `operation_log` VALUES (319, '创建项目信息', 'POST', 'api/project/addProject', '::1', '', 1, '2019-02-28 11:25:03', NULL);
INSERT INTO `operation_log` VALUES (320, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-28 15:07:18', NULL);
INSERT INTO `operation_log` VALUES (321, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-28 16:30:38', NULL);
INSERT INTO `operation_log` VALUES (322, '创建项目信息', 'POST', 'api/project/addProject', '::1', '', 1, '2019-02-28 16:39:11', NULL);
INSERT INTO `operation_log` VALUES (323, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-28 16:42:14', NULL);
INSERT INTO `operation_log` VALUES (324, '创建项目信息', 'POST', 'api/project/addProject', '::1', '', 0, '2019-02-28 17:43:12', NULL);
INSERT INTO `operation_log` VALUES (325, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-28 17:45:08', NULL);
INSERT INTO `operation_log` VALUES (326, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-28 18:54:46', NULL);
INSERT INTO `operation_log` VALUES (327, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-02-28 22:41:48', NULL);
INSERT INTO `operation_log` VALUES (328, '修改项目信息', 'POST', 'api/project/edit', '::1', '', 1, '2019-02-28 23:24:45', NULL);
INSERT INTO `operation_log` VALUES (329, '修改项目信息', 'POST', 'api/project/edit', '::1', '', 1, '2019-02-28 23:28:29', NULL);
INSERT INTO `operation_log` VALUES (330, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-03-01 10:16:18', NULL);
INSERT INTO `operation_log` VALUES (331, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-03-01 11:37:08', NULL);
INSERT INTO `operation_log` VALUES (332, '修改部门', 'POST', 'api/department/editDepartment', '::1', '', 1, '2019-03-01 12:05:21', NULL);
INSERT INTO `operation_log` VALUES (333, '修改部门', 'POST', 'api/department/editDepartment', '::1', '', 1, '2019-03-01 12:05:41', NULL);
INSERT INTO `operation_log` VALUES (334, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-03-01 14:00:14', NULL);
INSERT INTO `operation_log` VALUES (335, '修改部门', 'POST', 'api/department/editDepartment', '::1', '', 1, '2019-03-01 14:02:29', NULL);
INSERT INTO `operation_log` VALUES (336, '创建部门', 'POST', 'api/department/addDepartment', '::1', '', 1, '2019-03-01 14:04:32', NULL);
INSERT INTO `operation_log` VALUES (337, '修改部门', 'POST', 'api/department/editDepartment', '::1', '', 1, '2019-03-01 14:04:48', NULL);
INSERT INTO `operation_log` VALUES (338, '创建部门', 'POST', 'api/department/addDepartment', '::1', '', 1, '2019-03-01 14:05:20', NULL);
INSERT INTO `operation_log` VALUES (339, '创建部门', 'POST', 'api/department/addDepartment', '::1', '', 1, '2019-03-01 14:06:01', NULL);
INSERT INTO `operation_log` VALUES (340, '修改部门', 'POST', 'api/department/editDepartment', '::1', '', 1, '2019-03-01 14:06:21', NULL);
INSERT INTO `operation_log` VALUES (341, '修改部门', 'POST', 'api/department/editDepartment', '::1', '', 1, '2019-03-01 14:06:32', NULL);
INSERT INTO `operation_log` VALUES (342, '创建部门', 'POST', 'api/department/addDepartment', '::1', '', 1, '2019-03-01 14:08:07', NULL);
INSERT INTO `operation_log` VALUES (343, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-03-01 16:02:54', NULL);
INSERT INTO `operation_log` VALUES (344, '用户登录', 'POST', 'api/login', '::1', '', 1, '2019-03-03 18:35:08', NULL);

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets`  (
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  INDEX `password_resets_email_index`(`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_resets
-- ----------------------------
INSERT INTO `password_resets` VALUES ('admin@admin.com', '$2y$10$099y9HWV.8dLZvmyq81bXOelt6gMnokHjPLWsGtYFWgEnsbZCdNVC', '2018-11-29 18:02:23');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `department_id` int(11) NULL DEFAULT NULL,
  `group_id` int(11) NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `last_login` timestamp(0) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'admin', 'Admin', '15594990729', 'admin@admin.com', NULL, '$2y$10$q7IuhSqsnGL5g3CNQEypleEuDMZrJyQImZqwSlLEORMoGHBp9u9.u', '超级管理员', 2, 4, NULL, '2019-03-03 18:35:08', '2018-11-25 00:06:23', '2019-03-03 18:35:08');
INSERT INTO `users` VALUES (2, 'user01', 'user01', '12345678900', 'user01@admin.com', NULL, '$2y$10$rtuz3jfr81IKP8Mmm/pLBOvYq270GD2ReG9FOJPzcL14DySDD7SrS', 'user01', 2, 5, NULL, '2019-01-26 11:47:04', '2019-01-09 15:11:02', '2019-01-26 11:47:04');
INSERT INTO `users` VALUES (3, 'user02', 'user02', '12345678900', 'user02@user02.com', NULL, '$2y$10$5uRNH/DJLS9.gOYwpTp1ROmgcwl8HejJrdhhBQka/WyHxmE28jC6S', 'user02', 2, 5, NULL, NULL, '2019-01-10 02:44:13', NULL);
INSERT INTO `users` VALUES (4, 'user03', 'user03', '12345678901', 'user03@user03.com', NULL, '$2y$10$9MoQkbhMrQfjKYhVm5ltSekQqHwb/2kuthB3tPPYvAHpymFhrgQ7u', 'user03', 2, 5, NULL, NULL, '2019-01-22 21:46:38', NULL);
INSERT INTO `users` VALUES (5, 'user04', 'user04', '12345678901', 'user04@user04.com', NULL, '$2y$10$Tem47zuM67AXxn8Aq/WUmuiE8p5/TGdXbMz2iCPT/JECRI/OURCmO', 'user04', 2, 3, NULL, NULL, '2019-01-22 21:51:53', NULL);
INSERT INTO `users` VALUES (6, 'admin123', 'Admin123', NULL, NULL, NULL, '$2y$10$2KMOpI5ZQ2FOS8BYHjrXT.bNiEpU6SZoPlPq1GUXXqWYB/gNmm4pG', 'Admin123', 2, 1, NULL, '2019-02-21 09:39:18', '2019-02-17 20:32:11', '2019-02-21 09:39:18');

SET FOREIGN_KEY_CHECKS = 1;
