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

 Date: 28/10/2019 17:09:15
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of menus
-- ----------------------------
INSERT INTO `menus` VALUES (1, '基础', NULL, NULL, 0, '首页', '/', 0, NULL, 'md-home', '_self', 1, 1, 1, NULL, '2018-12-27 11:50:13', '0000-00-00 00:00:00');
INSERT INTO `menus` VALUES (2, '首页', 'home', 'views/sys/monitor/monitor', 1, '首页', '/home', 1, 'https://datav.aliyuncs.com/share/cae4ff9c43400079f20c1dc175f3f760', 'md-home', '_self', 1, 1, 1, 1, '2018-12-27 14:10:27', '2019-10-21 14:17:59');
INSERT INTO `menus` VALUES (3, '系统管理', 'sys-manage', NULL, 0, '系统管理', '/sys-manage', 0, NULL, 'md-briefcase', '_self', 1, 99, 1, 1, '2019-01-03 14:36:56', '2019-10-19 12:06:35');
INSERT INTO `menus` VALUES (4, '用户管理', 'users', 'views/user/users', 3, '用户管理', 'users', 0, NULL, NULL, '_self', 1, 1, 1, NULL, '2019-01-25 11:08:19', '0000-00-00 00:00:00');
INSERT INTO `menus` VALUES (5, '角色权限管理', 'role-manage', 'views/user/role-manage', 3, '角色权限管理', 'role-manage', 0, NULL, NULL, '_self', 1, 2, 1, NULL, '2018-12-27 16:13:30', '0000-00-00 00:00:00');
INSERT INTO `menus` VALUES (6, '部门管理', 'department-manage', 'views/sys/department-manage/departmentManage', 3, '部门管理', 'department-manage/departmentManage', 0, NULL, NULL, '_self', 1, 3, 1, NULL, '2019-01-03 14:44:20', '0000-00-00 00:00:00');
INSERT INTO `menus` VALUES (7, '菜单管理', 'menuManage', 'views/sys/menu-manage/menuManage', 3, '重构菜单', 'menu-manage/menuManage', 0, NULL, NULL, '_self', 1, 4, 1, NULL, '2019-01-23 14:45:44', '0000-00-00 00:00:00');
INSERT INTO `menus` VALUES (8, '事件日志', 'eventlogs', 'views/sys/monitor/monitor', 3, '日志显示了程序中的潜在错误, 比如异常和调试信息。', 'eventlogs', 1, 'http://localhost:3114/logs', NULL, '_self', 1, 5, 1, 1, '2019-01-09 16:37:32', '2019-01-27 16:02:31');
INSERT INTO `menus` VALUES (9, '操作日志', 'operationlogs', 'views/sys/operationlogs', 3, '记录系统功能操作日志', 'operationlogs', 0, NULL, NULL, '_self', 1, 6, 1, NULL, '2019-01-10 02:46:52', '0000-00-00 00:00:00');
INSERT INTO `menus` VALUES (10, '系统设置', 'sys-setting', NULL, 0, '系统设置', '/sys-setting', 0, NULL, 'md-settings', '_self', 0, 100, 1, 1, '2019-01-25 11:03:28', '2019-10-22 09:51:15');
INSERT INTO `menus` VALUES (11, '数据字典管理', 'dict', 'views/sys/dict-manage/dictManage', 3, '数据字典管理', 'dict-manage/dictManage', 0, NULL, NULL, '_self', 1, 1, 1, NULL, '2019-01-25 11:06:21', '0000-00-00 00:00:00');
INSERT INTO `menus` VALUES (12, '个人中心', 'profile', 'views/sys/profile/profile', 3, '个人中心', '/sys/profile', 0, NULL, NULL, '_self', 1, 2, 1, NULL, '2019-01-18 14:54:09', '0000-00-00 00:00:00');
INSERT INTO `menus` VALUES (13, '云监督', 'cloud_supervision', NULL, 0, NULL, '/cloud_supervision', 0, NULL, 'ios-cloud-circle', '_self', 1, 2, 1, 1, '2019-10-19 12:04:02', '2019-10-19 12:06:18');
INSERT INTO `menus` VALUES (14, '视频巡店', 'video_shop', 'views/value/index', 13, NULL, '/video_shop', 0, NULL, NULL, '_self', 1, 1, 1, 1, '2019-10-19 12:08:11', '2019-10-28 17:07:31');
INSERT INTO `menus` VALUES (15, '服务监督', 'service_supervision', 'views/value/index', 13, NULL, '/service_supervision', 0, NULL, NULL, '_self', 1, 2, 1, 1, '2019-10-19 12:08:52', '2019-10-28 17:07:27');
INSERT INTO `menus` VALUES (16, '巡店分析', 'patrol_analysis', 'views/value/index', 13, NULL, '/patrol_analysis', 0, NULL, NULL, '_self', 1, 3, 1, 1, '2019-10-19 12:09:29', '2019-10-28 17:07:23');
INSERT INTO `menus` VALUES (17, '服务督查分析', 'service_inspection_analysis', 'views/value/index', 13, NULL, '/service_inspection_analysis', 0, NULL, NULL, '_self', 1, 4, 1, 1, '2019-10-19 12:10:15', '2019-10-28 17:07:19');
INSERT INTO `menus` VALUES (18, '活动管理', 'activity_management', NULL, 0, NULL, '/activity_management', 0, NULL, 'ios-people', '_self', 1, 3, NULL, NULL, '2019-10-19 12:12:57', '2019-10-19 12:13:08');
INSERT INTO `menus` VALUES (19, '活动绩效分析', 'activity_performance_analysis', 'views/value/index', 18, NULL, '/activity_performance_analysis', 0, NULL, NULL, '_self', 1, 1, NULL, 1, '2019-10-19 12:14:36', '2019-10-28 17:07:14');
INSERT INTO `menus` VALUES (20, '活动执行管理', 'activity_execution_management', 'views/value/index', 18, NULL, '/activity_execution_management', 0, NULL, NULL, '_self', 1, 2, NULL, 1, '2019-10-19 12:53:21', '2019-10-28 17:07:09');
INSERT INTO `menus` VALUES (21, '活动分析', 'activity_analysis', 'views/value/index', 18, NULL, '/activity_analysis', 0, NULL, NULL, '_self', 1, 3, NULL, 1, '2019-10-19 14:23:13', '2019-10-28 17:07:03');
INSERT INTO `menus` VALUES (22, '发展积分', 'development_score', NULL, 0, NULL, '/development_score', 0, NULL, 'ios-podium', '_self', 1, 5, NULL, NULL, '2019-10-19 15:00:14', '2019-10-19 15:00:14');
INSERT INTO `menus` VALUES (23, '价值积分', 'value_integral', NULL, 0, NULL, '/value_integral', 0, NULL, 'ios-pricetag', '_self', 1, 6, NULL, NULL, '2019-10-19 15:02:00', '2019-10-19 15:02:00');
INSERT INTO `menus` VALUES (24, '绩效目标', 'design_BSC', 'views/development/area-merits-aim/areaMeritsAim', 22, NULL, '/design_BSC', 0, NULL, NULL, '_self', 1, 1, NULL, 1, '2019-10-19 15:03:11', '2019-10-20 16:50:59');
INSERT INTO `menus` VALUES (25, '销售数据管理', 'sales_data_management', 'views/development/sales-data/salesData', 22, NULL, '/sales_data_management', 0, NULL, NULL, '_self', 1, 2, NULL, 1, '2019-10-19 15:04:17', '2019-10-20 16:51:20');
INSERT INTO `menus` VALUES (26, '发展积分分析', 'development integral analysis', 'views/value/index', 22, NULL, '/development_integral_analysis', 0, NULL, NULL, '_self', 1, 3, NULL, 1, '2019-10-19 15:05:08', '2019-10-28 17:03:13');
INSERT INTO `menus` VALUES (27, '橙分期分析', 'orange_stage_analysis', 'views/value/index', 22, NULL, '/orange_stage_analysis', 0, NULL, NULL, '_self', 1, 4, NULL, 1, '2019-10-19 15:05:52', '2019-10-28 17:06:56');
INSERT INTO `menus` VALUES (28, '智能宽带分析', 'wifi_analysis', 'views/value/index', 22, NULL, '/wifi_analysis', 0, NULL, NULL, '_self', 1, 5, NULL, 1, '2019-10-19 15:06:42', '2019-10-28 17:06:22');
INSERT INTO `menus` VALUES (29, '终端销售分析', 'terminal_sales_analysis', 'views/value/index', 22, NULL, '/terminal_sales_analysis', 0, NULL, NULL, '_self', 1, 6, NULL, 1, '2019-10-19 15:07:29', '2019-10-28 17:06:26');
INSERT INTO `menus` VALUES (30, '价值积分导入', 'value_points_import', 'views/value/value-integral/valueIntegral', 23, NULL, '/value_points_import', 0, NULL, NULL, '_self', 1, 1, NULL, 1, '2019-10-19 15:08:24', '2019-10-19 17:47:52');
INSERT INTO `menus` VALUES (31, '价值积分分析', 'value_integral_analysis', 'views/value/index', 23, NULL, '/value_integral_analysis', 0, NULL, NULL, '_self', 1, 2, NULL, 1, '2019-10-19 15:09:06', '2019-10-28 17:06:32');
INSERT INTO `menus` VALUES (32, '片区绩效目标', 'area_merits_aim', 'views/development/area-merits-aim/areaMeritsAim', 24, NULL, '/area_merits_aim', 0, NULL, NULL, '_self', 0, 1, NULL, 1, '2019-10-19 15:10:04', '2019-10-20 16:51:09');
INSERT INTO `menus` VALUES (33, '门店绩效目标', 'shop_merite_aim', NULL, 24, NULL, '/shop_merite_aim', 0, NULL, NULL, '_self', 0, 2, NULL, 1, '2019-10-19 15:11:09', '2019-10-20 16:51:13');
INSERT INTO `menus` VALUES (34, '销售数据填报', 'sales_data_form', 'views/development/sales-data/salesData', 25, NULL, '/sales_data_form', 0, NULL, NULL, '_self', 0, 1, NULL, 1, '2019-10-19 15:12:17', '2019-10-20 16:51:28');
INSERT INTO `menus` VALUES (35, '橙分期', 'orange_stage', NULL, 25, NULL, '/orange_stage', 0, NULL, NULL, '_self', 0, 2, NULL, 1, '2019-10-19 15:12:57', '2019-10-20 16:51:32');
INSERT INTO `menus` VALUES (36, '智能宽带', 'wifi', NULL, 25, NULL, '/wifi', 0, NULL, NULL, '_self', 0, 3, NULL, 1, '2019-10-19 15:13:36', '2019-10-20 16:51:37');
INSERT INTO `menus` VALUES (37, '智能终端', 'smart_terminal', NULL, 25, NULL, '/smart_terminal', 0, NULL, NULL, '_self', 0, 4, NULL, 1, '2019-10-19 15:14:29', '2019-10-20 16:51:42');

SET FOREIGN_KEY_CHECKS = 1;
