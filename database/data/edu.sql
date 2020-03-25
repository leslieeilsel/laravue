/*
 Navicat Premium Data Transfer

 Source Server         : MySQL5.7
 Source Server Type    : MySQL
 Source Server Version : 50722
 Source Host           : localhost:3306
 Source Schema         : fengxi-edu

 Target Server Type    : MySQL
 Target Server Version : 50722
 File Encoding         : 65001

 Date: 11/03/2020 18:20:59
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '2014_10_12_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '2018_12_07_113125_create_sys_menus_table', 1);
INSERT INTO `migrations` VALUES (3, '2018_12_16_213605_create_sys_roles_table', 1);
INSERT INTO `migrations` VALUES (4, '2018_12_16_214318_create_sys_roles_menus_table', 1);
INSERT INTO `migrations` VALUES (5, '2019_01_07_111419_create_sys_departments_table', 1);
INSERT INTO `migrations` VALUES (6, '2019_01_09_192434_create_sys_operation_logs_table', 1);
INSERT INTO `migrations` VALUES (7, '2019_01_14_103115_create_sys_dict_categories_table', 1);
INSERT INTO `migrations` VALUES (8, '2019_01_14_103133_create_sys_dict_data_table', 1);
INSERT INTO `migrations` VALUES (9, '2019_03_18_160436_create_sys_roles_departments_table', 1);
INSERT INTO `migrations` VALUES (10, '2019_06_05_160436_create_sys_notices_table', 1);
INSERT INTO `migrations` VALUES (11, '2019_08_26_164529_add_foreign_key_to_users_table', 1);
INSERT INTO `migrations` VALUES (12, '2019_08_26_171551_add_foreign_key_to_sys_roles_menus_table', 1);
INSERT INTO `migrations` VALUES (13, '2019_08_26_171631_add_foreign_key_to_sys_roles_departments_table', 1);

-- ----------------------------
-- Table structure for sys_departments
-- ----------------------------
DROP TABLE IF EXISTS `sys_departments`;
CREATE TABLE `sys_departments`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '名称',
  `sort` int(11) NOT NULL COMMENT '排序值',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '是否启用',
  `parent_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '父级ID',
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '描述',
  `create_by` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '创建人',
  `update_by` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '修改人',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 30 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_departments
-- ----------------------------
INSERT INTO `sys_departments` VALUES (29, '一级部门', 1, 1, 0, '11', '超级管理员', '超级管理员', '2019-05-27 16:57:18', '2019-09-29 13:48:01');

-- ----------------------------
-- Table structure for sys_dict_categories
-- ----------------------------
DROP TABLE IF EXISTS `sys_dict_categories`;
CREATE TABLE `sys_dict_categories`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '名称',
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '唯一名称',
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '描述',
  `created_user_id` int(11) NULL DEFAULT NULL COMMENT '创建人',
  `updated_user_id` int(11) NULL DEFAULT NULL COMMENT '修改人',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_dict_categories
-- ----------------------------
INSERT INTO `sys_dict_categories` VALUES (1, '性别', 'sex', NULL, 1, NULL, '2019-01-25 10:22:35', '2019-01-25 10:22:35');

-- ----------------------------
-- Table structure for sys_dict_data
-- ----------------------------
DROP TABLE IF EXISTS `sys_dict_data`;
CREATE TABLE `sys_dict_data`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '名称',
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '字段值',
  `dict_id` int(10) UNSIGNED NOT NULL COMMENT '字典ID',
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '描述',
  `created_user_id` int(11) NULL DEFAULT NULL COMMENT '创建人',
  `updated_user_id` int(11) NULL DEFAULT NULL COMMENT '修改人',
  `sort` int(11) NOT NULL COMMENT '排序值',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '是否启用',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sys_dict_data_dict_id_foreign`(`dict_id`) USING BTREE,
  CONSTRAINT `sys_dict_data_ibfk_1` FOREIGN KEY (`dict_id`) REFERENCES `sys_dict_categories` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_dict_data
-- ----------------------------
INSERT INTO `sys_dict_data` VALUES (1, '男', '0', 1, NULL, 1, NULL, 0, 1, '2019-01-25 10:23:00', '2019-01-25 10:23:00');
INSERT INTO `sys_dict_data` VALUES (2, '女', '1', 1, NULL, 1, NULL, 1, 1, '2019-01-25 10:23:23', '2019-01-25 10:23:23');

-- ----------------------------
-- Table structure for sys_menus
-- ----------------------------
DROP TABLE IF EXISTS `sys_menus`;
CREATE TABLE `sys_menus`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '标题',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '英文标题',
  `component` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '前端组件',
  `parent_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '父级ID',
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '描述',
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '地址',
  `link_type` int(11) NOT NULL DEFAULT 0 COMMENT '链接类型',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '第三方链接',
  `icon` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'icon',
  `target` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '_self' COMMENT '链接跳转类型',
  `enabled` int(11) NOT NULL DEFAULT 1 COMMENT '是否启用',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序值',
  `created_user_id` int(11) NULL DEFAULT NULL COMMENT '创建用户ID',
  `updated_user_id` int(11) NULL DEFAULT NULL COMMENT '修改用户ID',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 97 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_menus
-- ----------------------------
INSERT INTO `sys_menus` VALUES (1, '首页', 'base', NULL, 0, NULL, '/', 0, NULL, 'md-home', '_self', 1, 0, 1, NULL, '2019-03-25 20:44:45', '2019-03-25 20:45:32');
INSERT INTO `sys_menus` VALUES (2, '首页', 'home', 'views/sys/monitor/monitor', 1, NULL, '/home', 1, 'http://39.98.147.207:9007/slogan', 'md-home', '_self', 1, 0, 1, 1, '2019-03-25 20:46:08', '2020-02-27 08:55:06');
INSERT INTO `sys_menus` VALUES (3, '系统管理', 'sys-manage', NULL, 0, '系统管理', '/sys-manage', 0, NULL, 'md-briefcase', '_self', 1, 7, 1, 1, '2019-03-25 20:46:08', '2019-11-27 08:45:01');
INSERT INTO `sys_menus` VALUES (4, '用户管理', 'users', 'views/user/users', 3, '用户管理', 'users', 0, NULL, NULL, '_self', 1, 1, 1, 1, '2020-01-18 16:54:11', '2020-01-18 16:55:27');
INSERT INTO `sys_menus` VALUES (5, '角色权限管理', 'role-manage', 'views/user/role-manage', 3, '角色权限管理', 'role-manage', 0, NULL, NULL, '_self', 1, 2, 1, 1, '2020-01-18 16:55:08', '2020-01-18 16:55:08');
INSERT INTO `sys_menus` VALUES (6, '部门管理', 'department-manage', 'views/sys/department-manage/departmentManage', 3, '部门管理', 'department-manage/departmentManage', 0, NULL, NULL, '_self', 1, 3, 1, 1, '2020-01-18 16:58:43', '2020-01-18 16:58:43');
INSERT INTO `sys_menus` VALUES (7, '菜单管理', 'menuManage', 'views/sys/menu-manage/menuManage', 3, '重构菜单', 'menuManage', 0, NULL, NULL, '_self', 1, 4, 1, 1, '2020-01-18 16:59:15', '2020-01-18 16:59:15');
INSERT INTO `sys_menus` VALUES (8, '事件日志', 'eventlogs', 'views/sys/monitor/monitor', 3, '日志显示了程序中的潜在错误, 比如异常和调试信息。', 'eventlogs', 1, 'http://39.98.147.207:9007/logs', NULL, '_self', 1, 5, 1, 1, '2020-01-19 18:16:19', '2020-01-19 18:16:19');
INSERT INTO `sys_menus` VALUES (9, '操作日志', 'operationlogs', 'views/sys/operationlogs', 3, '记录系统功能操作日志', 'operationlogs', 0, NULL, NULL, '_self', 1, 6, 1, 1, '2020-01-11 18:05:42', '2020-01-12 14:32:19');
INSERT INTO `sys_menus` VALUES (10, '数据字典管理', 'dict', 'views/sys/dict-manage/dictManage', 3, '数据字典管理', 'dict-manage/dictManage', 0, NULL, NULL, '_self', 1, 7, 1, 1, '2020-01-11 18:06:20', '2020-01-11 18:06:20');
INSERT INTO `sys_menus` VALUES (11, '个人中心', 'profile', 'views/sys/profile/profile', 3, '个人中心', '/sys/profile', 0, NULL, NULL, '_self', 1, 8, 1, 1, '2020-01-11 18:07:34', '2020-01-11 18:07:34');

-- ----------------------------
-- Table structure for sys_notices
-- ----------------------------
DROP TABLE IF EXISTS `sys_notices`;
CREATE TABLE `sys_notices`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `type` int(11) NULL DEFAULT NULL,
  `send_user_id` int(11) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sys_operation_logs
-- ----------------------------
DROP TABLE IF EXISTS `sys_operation_logs`;
CREATE TABLE `sys_operation_logs`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '操作名称',
  `method` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '请求类型',
  `path` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '请求路径',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'ip地址',
  `ip_place` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'ip信息',
  `user_id` int(11) NOT NULL COMMENT '操作用户',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_operation_logs
-- ----------------------------
INSERT INTO `sys_operation_logs` VALUES (1, '用户登录', 'POST', 'api/login', '::1', '', 1, '2020-03-11 18:10:01', NULL);

-- ----------------------------
-- Table structure for sys_roles
-- ----------------------------
DROP TABLE IF EXISTS `sys_roles`;
CREATE TABLE `sys_roles`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '角色名称',
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '描述',
  `is_default` int(11) NOT NULL DEFAULT 0 COMMENT '是否设置为注册用户默认角色',
  `data_type` int(11) NOT NULL COMMENT '数据权限类型',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_roles
-- ----------------------------
INSERT INTO `sys_roles` VALUES (1, 'Superadmin', '超级管理员，拥有所有权限', 1, 0, '2018-12-17 18:47:02', '2019-04-09 09:33:07');
INSERT INTO `sys_roles` VALUES (2, '维修-司机', NULL, 0, 0, '2020-01-14 18:09:16', NULL);
INSERT INTO `sys_roles` VALUES (3, '维修-车队长', NULL, 0, 0, '2020-01-14 18:10:36', NULL);
INSERT INTO `sys_roles` VALUES (4, '维修-后勤部', NULL, 0, 0, '2020-01-14 18:10:48', NULL);
INSERT INTO `sys_roles` VALUES (5, '维修-分管领导', NULL, 0, 0, '2020-01-14 18:11:00', NULL);
INSERT INTO `sys_roles` VALUES (6, '入库经手人', '入库经手人', 0, 0, '2020-01-11 16:41:42', NULL);
INSERT INTO `sys_roles` VALUES (7, '仓库负责人', '仓库负责人', 0, 0, '2020-01-11 16:42:03', NULL);
INSERT INTO `sys_roles` VALUES (8, '负责人', '负责人', 0, 0, '2020-01-11 16:42:11', NULL);
INSERT INTO `sys_roles` VALUES (9, '财务', '财务', 0, 0, '2020-01-11 16:42:27', NULL);

-- ----------------------------
-- Table structure for sys_roles_menus
-- ----------------------------
DROP TABLE IF EXISTS `sys_roles_menus`;
CREATE TABLE `sys_roles_menus`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` int(10) UNSIGNED NOT NULL,
  `menu_id` int(10) UNSIGNED NOT NULL,
  `checked` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sys_roles_menus_menu_id_foreign`(`menu_id`) USING BTREE,
  INDEX `sys_roles_menus_role_id_foreign`(`role_id`) USING BTREE,
  CONSTRAINT `sys_roles_menus_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `sys_menus` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `sys_roles_menus_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `sys_roles` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sys_roles_menus
-- ----------------------------
INSERT INTO `sys_roles_menus` VALUES (1, 1, 1, 1);
INSERT INTO `sys_roles_menus` VALUES (2, 1, 2, 1);
INSERT INTO `sys_roles_menus` VALUES (3, 1, 3, 1);
INSERT INTO `sys_roles_menus` VALUES (4, 1, 4, 1);
INSERT INTO `sys_roles_menus` VALUES (5, 1, 5, 1);
INSERT INTO `sys_roles_menus` VALUES (6, 1, 6, 1);
INSERT INTO `sys_roles_menus` VALUES (7, 1, 7, 1);
INSERT INTO `sys_roles_menus` VALUES (8, 1, 8, 1);
INSERT INTO `sys_roles_menus` VALUES (9, 1, 9, 1);
INSERT INTO `sys_roles_menus` VALUES (10, 1, 10, 1);
INSERT INTO `sys_roles_menus` VALUES (11, 1, 11, 1);

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '用户名',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '姓名',
  `phone` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '电话',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '邮箱',
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '头像',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '密码',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '详细信息',
  `department_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '部门id',
  `office` int(11) NULL DEFAULT NULL COMMENT '职务',
  `group_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '角色',
  `remember_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'token',
  `ding_user_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '钉钉id',
  `last_login` timestamp(0) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `users_department_id_foreign`(`department_id`) USING BTREE,
  INDEX `users_group_id_foreign`(`group_id`) USING BTREE,
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `sys_departments` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `sys_roles` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'admin', '超级管理员', '15594990729', 'admin@admin.com', NULL, '$2y$10$q7IuhSqsnGL5g3CNQEypleEuDMZrJyQImZqwSlLEORMoGHBp9u9.u', '超级管理员', 29, 2, 1, NULL, '', '2020-03-11 18:10:01', '2019-03-26 15:10:17', '2020-03-11 18:10:01');

SET FOREIGN_KEY_CHECKS = 1;
