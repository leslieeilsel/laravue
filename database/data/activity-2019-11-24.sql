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

 Date: 24/11/2019 19:02:17
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for activity
-- ----------------------------
DROP TABLE IF EXISTS `activity`;
CREATE TABLE `activity`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_plan_id` int(10) NULL DEFAULT NULL COMMENT '活动计划id',
  `position` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '活动执行位置（一般是手机定位）',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '活动复盘文字描述',
  `pic` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '活动照片',
  `applicant` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '填报人',
  `date_time` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '活动执行时间',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of activity
-- ----------------------------
INSERT INTO `activity` VALUES (2, 3, '222', '22', 'storage/value/activity/333/41004583.png,storage/value/activity/333/860852578.png', '1', '2019-11-24', '2019-11-24 16:18:01', '2019-11-24 18:13:59');
INSERT INTO `activity` VALUES (3, 3, '222', '22', 'storage/value/activity/333/41004583.png', '1', '2019-11-24', '2019-11-24 16:18:13', NULL);

-- ----------------------------
-- Table structure for activity_plan
-- ----------------------------
DROP TABLE IF EXISTS `activity_plan`;
CREATE TABLE `activity_plan`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '活动地点',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '活动名称',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '活动文字描述',
  `area` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '区域',
  `applicant` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '填报人',
  `plan_start_time` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '活动时间',
  `plan_end_time` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '活动时间',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of activity_plan
-- ----------------------------
INSERT INTO `activity_plan` VALUES (3, '22', '333', '5', '[5,45,199]', '1', '2019-11-23', '2019-11-25', NULL, NULL);
INSERT INTO `activity_plan` VALUES (4, '222', '5222', '222', '[4,37,179]', '1', '2019-11-23', '2019-12-01', '2019-11-23 15:46:29', '2019-11-23 16:54:39');
INSERT INTO `activity_plan` VALUES (5, '45353', '34435', '435', '[5,45,199]', '1', '2019-11-23', '2019-11-25', '2019-11-23 16:31:01', NULL);
INSERT INTO `activity_plan` VALUES (6, '6666', '6666', '666', '[5,45,199]', '1', '2019-11-23', '2019-11-26', '2019-11-23 16:37:41', NULL);
INSERT INTO `activity_plan` VALUES (7, '777', '777', '777', '[5,45,199]', '1', '2019-11-23', '2019-11-25', '2019-11-23 16:38:54', NULL);
INSERT INTO `activity_plan` VALUES (8, '555', '11', '22', '[3,25,155]', '1', '2019-11-24', '2019-11-10', '2019-11-24 18:40:05', NULL);

-- ----------------------------
-- Table structure for area_target
-- ----------------------------
DROP TABLE IF EXISTS `area_target`;
CREATE TABLE `area_target`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `duty_department` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `duty_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `target_start_time` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `target_end_time` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `product_type` int(10) NULL DEFAULT NULL,
  `target` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `business_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of area_target
-- ----------------------------
INSERT INTO `area_target` VALUES (2, '[3,25,155]', '22', '2019-11-18T16:00:00.000Z', NULL, NULL, '22', NULL, NULL, '2');
INSERT INTO `area_target` VALUES (3, '[3,25,155]', '555', '2019-11-22T16:00:00.000Z', '2019-11-24T16:00:00.000Z', NULL, '44', NULL, NULL, '2');
INSERT INTO `area_target` VALUES (4, '[2,15,139]', '33', '2019-11-23', '2019-11-30', NULL, '33', '2019-11-23 17:41:25', NULL, '1');

-- ----------------------------
-- Table structure for video_patrol
-- ----------------------------
DROP TABLE IF EXISTS `video_patrol`;
CREATE TABLE `video_patrol`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(10) NULL DEFAULT NULL COMMENT '部门id',
  `department_info_id` int(10) NULL DEFAULT NULL COMMENT '部门详情id',
  `shop_state` int(10) NULL DEFAULT NULL COMMENT '店铺状态',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '情况说明',
  `pic` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '店铺状态',
  `applicant` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '填报人',
  `date_time` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '巡店时间',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 929 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of video_patrol
-- ----------------------------
INSERT INTO `video_patrol` VALUES (927, 367, 925, 1, '11', NULL, 'Admin', '2019-11-10', NULL, '2019-11-21 19:41:56');
INSERT INTO `video_patrol` VALUES (928, 368, 926, 1, '22', NULL, 'Admin', '2019-11-21', NULL, NULL);

-- ----------------------------
-- Table structure for video_url
-- ----------------------------
DROP TABLE IF EXISTS `video_url`;
CREATE TABLE `video_url`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(10) NULL DEFAULT NULL COMMENT '部门id',
  `department_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `department_info_id` int(10) NULL DEFAULT NULL COMMENT '部门详情id',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '店铺状态',
  `applicant` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '填报人',
  `date_time` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '巡店时间',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 625 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of video_url
-- ----------------------------
INSERT INTO `video_url` VALUES (1, NULL, '爱琴海魅族通讯产品经营部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (2, NULL, '安康oppo巴山中路专营3店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (3, NULL, '安康白河县人民路营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024414/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (4, NULL, '安康国美', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (5, NULL, '安康汉滨区鑫盛大齐通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (6, NULL, '安康汉阴县北城街营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (7, NULL, '安康江风巴山路三星合作厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (8, NULL, '安康江风雷神殿卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022727/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (9, NULL, '安康岚皋县建设路营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022714/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (10, NULL, '安康宁陕县长安路营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (11, NULL, '安康平利县新正街营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021886/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (12, NULL, '安康深蓝兴安中路专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022721/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (13, NULL, '安康石泉县向阳路营业厅', NULL, 'rtmp://125.76.229.199:1935/live/pag/125.76.229.199/7302/024340/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (14, NULL, '安康市百鑫通科技有限公司', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (15, NULL, '安康市汉宾区江风通讯营业部', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025137/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (16, NULL, '安康市汉滨区大齐全网通手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025143/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (17, NULL, '安康市汉滨区海鑫通讯器材经营部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (18, NULL, '安康市汉滨区鸿海通讯经营部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (19, NULL, '安康市汉滨区结缘专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (20, NULL, '安康市汉滨区金康通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (21, NULL, '安康市汉滨区明通通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (22, NULL, '安康市汉滨区仁辉通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (23, NULL, '安康市汉滨区四喜通讯服务部', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025055/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (24, NULL, '安康市汉滨区育才路秋林社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (25, NULL, '安康市区恒口镇营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025129/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (26, NULL, '安康市区兴安中路营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (27, NULL, '安康市智博通讯安运司专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (28, NULL, '安康苏宁云商销售有限公司', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (29, NULL, '安康旬阳县振旬东路营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (30, NULL, '安康镇坪县上新街营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (31, NULL, '安康紫阳县紫府路营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022704/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (32, NULL, '白河仓上农庄村服务站-吕福平', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (33, NULL, '白河城关镇辉煌通讯人民路专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024415/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (34, NULL, '白河城区郭金凤', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (35, NULL, '白河卡子镇卡子村服务站-邱田忠', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (36, NULL, '白河宋家镇成兴全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (37, NULL, '白河县仓上镇天翼卓越店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024860/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (38, NULL, '白河县仓上镇智尚格通讯店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024517/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (39, NULL, '白河县诚泰通讯人民路专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (40, NULL, '白河县城关镇爱尚通讯手机店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (41, NULL, '白河县城关镇大虎网络维护社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024357/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (42, NULL, '白河县城关镇飞讯通通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (43, NULL, '白河县城关镇和讯通讯运营中心', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (44, NULL, '白河县城关镇华晟通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (45, NULL, '白河县城关镇辉煌通讯清风路店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (46, NULL, '白河县城关镇辉煌艳杰通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (47, NULL, '白河县城关镇惠百姓网络运营中心', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (48, NULL, '白河县城关镇玖玖玖伍通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (49, NULL, '白河县城关镇特快网络服务社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (50, NULL, '白河县构扒镇龙源家电销售部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (51, NULL, '白河县构朳镇锐捷通讯', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025529/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (52, NULL, '白河县宏泰家电有限公司', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (53, NULL, '白河县卡子镇梁华电器店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (54, NULL, '白河县卡子镇全网通手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024512/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (55, NULL, '白河县卡子镇鑫达科技部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (56, NULL, '白河县卡子镇悦鑫通讯专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (57, NULL, '白河县冷水镇手机专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (58, NULL, '白河县冷水镇万丰家电零售店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (59, NULL, '白河县冷水镇小李电器维修中心', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (60, NULL, '白河县冷水镇小双汤贵海手机店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (61, NULL, '白河县领航通讯狮子山社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (62, NULL, '白河县领航通讯狮子山专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (63, NULL, '白河县麻虎镇诚信全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (64, NULL, '白河县麻虎镇李应文通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (65, NULL, '白河县麻虎镇小熊家电经营部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (66, NULL, '白河县茅南桥头五金专卖', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (67, NULL, '白河县茅坪镇辉煌通讯茅坪营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (68, NULL, '白河县茅坪镇另一个号通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (69, NULL, '白河县茅坪镇世杰通讯', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025148/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (70, NULL, '白河县茅坪镇众友通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (71, NULL, '白河县瑞鑫通讯手机店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (72, NULL, '白河县双丰镇邹艳智能手机店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024513/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (73, NULL, '白河县宋家电信代理店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (74, NULL, '白河县宋家明康五金店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (75, NULL, '白河县西营镇全网通卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024510/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (76, NULL, '白河县西营镇志通电脑数码店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024511/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (77, NULL, '白河县贤泽通讯中心', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (78, NULL, '白河县小双镇全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (79, NULL, '白河县新天地有限公司西营加盟店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (80, NULL, '白河县阳光通讯狮子山专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (81, NULL, '白河县中厂镇世洋通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (82, NULL, '白河县中厂镇讯峰通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (83, NULL, '草坪社区体验店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (84, NULL, '曾家镇天翼合作营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (85, NULL, '曾家镇天翼手机店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (86, NULL, '城郊坝河镇电信专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022691/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (87, NULL, '城郊大竹园电信卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022692/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (88, NULL, '城郊付家河泽兴电子通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (89, NULL, '城郊关庙恒扬通讯专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (90, NULL, '城郊关庙镇电信海信专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (91, NULL, '城郊河西翼琴手机专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (92, NULL, '城郊河西镇电信美的专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (93, NULL, '城郊河西专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (94, NULL, '城郊洪山镇云朵朵通讯服务店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (95, NULL, '城郊洪山专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (96, NULL, '城郊吉河镇吉河村服务站-陈建会', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (97, NULL, '城郊吉河镇康晖通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (98, NULL, '城郊建民代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (99, NULL, '城郊建民合作厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022689/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (100, NULL, '城郊建民江风通讯专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025021/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (101, NULL, '城郊建民新OPPO专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (102, NULL, '城郊流水全网通手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022690/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (103, NULL, '城郊流水镇天天电信通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (104, NULL, '城郊牛蹄代理服务店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (105, NULL, '城郊石转旭日通讯专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (106, NULL, '城郊石转周洋洋通讯专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (107, NULL, '城郊双龙镇诚兴通讯代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (108, NULL, '城郊双龙镇代理服务店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (109, NULL, '城郊田坝乡代理服务店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (110, NULL, '城郊县河代理服务店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022693/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (111, NULL, '城郊新坝电信代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (112, NULL, '城郊晏坝兴隆电信专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (113, NULL, '城郊瀛湖镇朝飞家电通讯专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (114, NULL, '城郊枣园猛子通讯专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025013/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (115, NULL, '城郊张滩天星通讯专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025015/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (116, NULL, '城郊张滩鑫之垚手机专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (117, NULL, '城郊张滩专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (118, NULL, '城郊正义代理服务店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (119, NULL, '城区博尧通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (120, NULL, '城区超群通讯代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (121, NULL, '城区超越通信代理店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (122, NULL, '城区朝阳支局阳光城片区社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025154/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (123, NULL, '城区朝阳支局长兴片区社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024334/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (124, NULL, '城区大桥路支局教场片区社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025141/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (125, NULL, '城区国辉通讯巴山中路店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (126, NULL, '城区洪强oppo手机通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (127, NULL, '城区华晟兴安路旗舰店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025051/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (128, NULL, '城区华鑫巴山东路店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (129, NULL, '城区魅族国红手机经销店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (130, NULL, '城区深蓝巴山中路店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (131, NULL, '城区香溪路代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (132, NULL, '城区香溪支局香溪路片区社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022716/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (133, NULL, '城区香溪支局育才路片区社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022711/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (134, NULL, '城区香溪支局御公馆片区社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024335/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (135, NULL, '城区兴安城市花园片区社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (136, NULL, '城区智朗通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (137, NULL, '城区智友通讯经营部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (138, NULL, '创新科技（赛格）', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (139, NULL, '大河镇兴达电讯手机店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022725/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (140, NULL, '大同镇金恒源电信代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (141, NULL, '电子渠道_易一公司_安康', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (142, NULL, '高新安成通讯经营部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (143, NULL, '高新花园沟博成通信社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (144, NULL, '高新慧信通讯代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (145, NULL, '高新智明科技便利店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (146, NULL, '广货街镇营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (147, NULL, '汉滨区陈小姣通讯店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025063/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (148, NULL, '汉滨区茨沟慧飞通讯技术经销部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (149, NULL, '汉滨区大河镇尊贵电器电信代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (150, NULL, '汉滨区国龙电信服务店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025087/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (151, NULL, '汉滨区李智鹏通讯服务部', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025139/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (152, NULL, '汉滨区深蓝胜达通信设备店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (153, NULL, '汉滨区盛泰鑫达通讯科技店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (154, NULL, '汉滨区万浩新程通讯器材店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (155, NULL, '汉滨区希瑞oppo手机通讯部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (156, NULL, '汉滨区新宏良通讯经营部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (157, NULL, '汉滨区炫顺电信业务代办店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (158, NULL, '汉滨区烨瑞手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (159, NULL, '汉滨区智东通讯器材经营部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (160, NULL, '汉滨区智翼联通讯部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (161, NULL, '汉滨区卓盛手机店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (162, NULL, '汉唐通讯大同电信专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025121/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (163, NULL, '汉阴城关镇数码店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (164, NULL, '汉阴恒泰通信城南oppo专卖', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (165, NULL, '汉阴利华诚泰华为专卖', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024992/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (166, NULL, '汉阴平梁镇专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024373/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (167, NULL, '汉阴蒲溪先锋村服务站-徐良艳', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (168, NULL, '汉阴蒲溪镇合作营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024359/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (169, NULL, '汉阴双河镇代理店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (170, NULL, '汉阴双乳三同村服务站-闵小英', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (171, NULL, '汉阴县城东支局五一片区社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (172, NULL, '汉阴县城关江风通讯器材经销部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (173, NULL, '汉阴县城关镇百佳通讯卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024693/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (174, NULL, '汉阴县城关镇诚泰手机通信卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024994/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (175, NULL, '汉阴县城关镇九鼎通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (176, NULL, '汉阴县城关镇廖加寿家便利店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (177, NULL, '汉阴县城关镇世纪通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (178, NULL, '汉阴县城关镇世联OPPO体验店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (179, NULL, '汉阴县城关镇思宇通讯二店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (180, NULL, '汉阴县城关镇泰立通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (181, NULL, '汉阴县城关镇小米专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (182, NULL, '汉阴县城西支局北城片区社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (183, NULL, '汉阴县汉阳镇精品电汇代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (184, NULL, '汉阴县汉阳镇希翼通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (185, NULL, '汉阴县汉阳镇张超便利点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (186, NULL, '汉阴县涧池镇鼎盛通讯二店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (187, NULL, '汉阴县涧池镇华为专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024367/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (188, NULL, '汉阴县涧池镇双安通讯专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (189, NULL, '汉阴县涧池镇寅申通讯', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024369/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (190, NULL, '汉阴县涧池镇中美家电便利店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (191, NULL, '汉阴县俊辰通讯城南营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (192, NULL, '汉阴县平梁镇时代通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (193, NULL, '汉阴县平梁镇翼通通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (194, NULL, '汉阴县蒲溪镇美玉通讯手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (195, NULL, '汉阴县双乳镇双悦通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (196, NULL, '汉阴县西南支局城南片区社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (197, NULL, '汉阴县县城关镇智博通通讯中心', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (198, NULL, '汉阴县邹瑜鼎盛通讯专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024712/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (199, NULL, '号百商旅电子商务安康分公司', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (200, NULL, '恒口OPPO专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (201, NULL, '恒口大河沈坝电信手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022687/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (202, NULL, '恒口大河镇双溪乡电信代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (203, NULL, '恒口大河镇赵军手机店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (204, NULL, '恒口大河中原镇电信合作厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (205, NULL, '恒口大同福磊全网通手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024341/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (206, NULL, '恒口大同镇平一专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (207, NULL, '恒口鸿盛商贸手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025125/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (208, NULL, '恒口华信通讯VIVO手机店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (209, NULL, '恒口康秦电器电信代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (210, NULL, '恒口龙飞通讯OPPO手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (211, NULL, '恒口梅子铺镇电信代理店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024349/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (212, NULL, '恒口苹果三星专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025127/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (213, NULL, '恒口区江风通讯专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025124/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (214, NULL, '恒口区正东支局南区社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022685/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (215, NULL, '恒口区正西支局北片社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022686/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (216, NULL, '恒口三合村服务站-马付文', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (217, NULL, '恒口五里茨沟镇全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (218, NULL, '恒口五里江风专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (219, NULL, '恒口五里宇芳通讯代理店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (220, NULL, '恒口五里镇陈奇电信业务代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (221, NULL, '恒口五里镇东镇电信代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (222, NULL, '恒口五里镇建设代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (223, NULL, '恒口五里镇江风通讯专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (224, NULL, '恒口五里镇李湾村服务站-李鸿芳', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (225, NULL, '恒口五里镇谭坝代理店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (226, NULL, '恒口五里镇五茨路口手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025102/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (227, NULL, '恒口镇和平家电电信代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (228, NULL, '恒口镇南大街口移动卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (229, NULL, '恒口镇宇威手机店VIVO卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (230, NULL, '恒口正东北社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (231, NULL, '恒口正西南社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (232, NULL, '恒口志联手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022708/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (233, NULL, '红卫通讯恒口叶坪镇街道全网通店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (234, NULL, '宏达电器行', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (235, NULL, '华昇通讯兴安中路合作厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022719/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (236, NULL, '华晟通讯公园店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025053/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (237, NULL, '江北安铁飞宇通讯专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022724/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (238, NULL, '江北安铁智慧家庭服务中心', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025759/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (239, NULL, '江北大道北瑞文通信社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025757/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (240, NULL, '江北大道支局南片区社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022718/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (241, NULL, '江北高新安恒路片区社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025955/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (242, NULL, '江北高新菠萝蜜通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (243, NULL, '江北高新江风科技华为专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (244, NULL, '江北高新千家网络仕府大院社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (245, NULL, '江北高新世联云通网络专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (246, NULL, '江北高新现代城片区社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (247, NULL, '江北高新支局黄沟大道片区社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (248, NULL, '江北甲字电信代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (249, NULL, '江北江风科技专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (250, NULL, '江北天星通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (251, NULL, '江北张沟桥华飞通讯社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (252, NULL, '江风翼咖啡手机玩家体验店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (253, NULL, '江口镇营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025340/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (254, NULL, '聚内组', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (255, NULL, '君妍通讯金州城社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (256, NULL, '凯胜通讯专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (257, NULL, '岚皋孟石岭镇天悦手机销售店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (258, NULL, '岚皋县昶瑞通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (259, NULL, '岚皋县畅享达通讯中心', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (260, NULL, '岚皋县诚信电器专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (261, NULL, '岚皋县城关镇华晟通讯分店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (262, NULL, '岚皋县烽火通讯专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022698/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (263, NULL, '岚皋县官元镇昊天科技维修店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (264, NULL, '岚皋县花里镇诚信手机全网通卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022670/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (265, NULL, '岚皋县佳和通讯东三路社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (266, NULL, '岚皋县建设路片区社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022700/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (267, NULL, '岚皋县金城通讯店二店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022701/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (268, NULL, '岚皋县金福鸿通讯华为店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (269, NULL, '岚皋县金福鸿通讯专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (270, NULL, '岚皋县浪涛通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (271, NULL, '岚皋县老城支局老街片区社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (272, NULL, '岚皋县老城支局新街片区社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (273, NULL, '岚皋县蔺河合作营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/026049/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (274, NULL, '岚皋县六口代理营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (275, NULL, '岚皋县民主镇陈守海商店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (276, NULL, '岚皋县民主镇高治通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (277, NULL, '岚皋县民主镇睿智通讯', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022667/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (278, NULL, '岚皋县民主镇中天通讯电信专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (279, NULL, '岚皋县仁和电器行', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (280, NULL, '岚皋县石门镇鸿鑫通讯专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (281, NULL, '岚皋县石门镇鑫鑫通讯店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022669/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (282, NULL, '岚皋县肖家坝片区社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022699/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (283, NULL, '岚皋县堰门镇大名通讯', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025442/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (284, NULL, '岚皋县易得通通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (285, NULL, '岚皋县益新通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (286, NULL, '岚皋县翼凡通讯建设路专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (287, NULL, '岚皋县翼兴通讯专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (288, NULL, '岚皋县翼泽手机销售店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (289, NULL, '岚皋县众城天翼通讯中心', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (290, NULL, '岚皋县佐龙镇鑫盛通讯', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022666/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (291, NULL, '岚皋县佐龙镇鑫盛通讯', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022665/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (292, NULL, '岚皋县佐龙镇佐龙通讯', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022668/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (293, NULL, '岚皋堰门镇中武村服务站-寇青华', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (294, NULL, '凌云通讯平利县东一路oppo店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (295, NULL, '凌云通讯平利县东一路VIVO店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (296, NULL, '龙王镇营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (297, NULL, '梅子铺海信专卖电信代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (298, NULL, '宁陕国兴通讯迎宾大道卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025334/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (299, NULL, '宁陕皇冠镇全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (300, NULL, '宁陕江口沙坪村代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (301, NULL, '宁陕四亩地镇营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (302, NULL, '宁陕筒车湾营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025527/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (303, NULL, '宁陕县爱逛手机销售店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (304, NULL, '宁陕县城关镇弘康通讯商圈店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025336/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (305, NULL, '宁陕县城关镇汇鑫通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (306, NULL, '宁陕县城关镇锦轩通讯社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (307, NULL, '宁陕县城关镇康乐家电百货商城', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (308, NULL, '宁陕县城关镇学文电器王牌专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (309, NULL, '宁陕县广货街欣威代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (310, NULL, '宁陕县江口镇松茂电器商行', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (311, NULL, '宁陕县江口镇王大军电信代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (312, NULL, '宁陕县江口镇夏巧荣电信代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (313, NULL, '宁陕县四亩地镇盛鑫便民店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (314, NULL, '宁陕县太山庙李明珍代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (315, NULL, '宁陕县卫成通讯代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (316, NULL, '宁陕县长安东街欣然通讯社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (317, NULL, '宁陕新场营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (318, NULL, '牛头店代理店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022726/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (319, NULL, '平利八仙二厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021870/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (320, NULL, '平利八仙永发通讯店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021775/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (321, NULL, '平利大贵镇军梅手机专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (322, NULL, '平利大贵镇儒林堡村服务站-王维', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (323, NULL, '平利广佛洪银珍通讯部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (324, NULL, '平利广佛铁炉村服务站-杨丽', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (325, NULL, '平利江风魅族专卖店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021854/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (326, NULL, '平利金日通讯OPPO专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (327, NULL, '平利老县镇邓将手机专卖店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021856/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (328, NULL, '平利老县镇凤桥村服务站-邹定勇', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (329, NULL, '平利莲花通讯OPPO专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (330, NULL, '平利洛河三坪村服务站-詹远强', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (331, NULL, '平利洛河镇顺达全网通店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (332, NULL, '平利三阳泗王庙村服务站-汪传霞', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (333, NULL, '平利三阳镇孝焕全网通店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021786/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (334, NULL, '平利西河天翼手机专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021765/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (335, NULL, '平利县大贵镇南方家居城', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (336, NULL, '平利县广佛镇君意通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (337, NULL, '平利县海英家电行', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (338, NULL, '平利县恒源东一路社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (339, NULL, '平利县老县镇老县村喜盈门超市', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (340, NULL, '平利县凌云通讯专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (341, NULL, '平利县秋山通讯中心社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021877/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (342, NULL, '平利县双贤OPPO专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (343, NULL, '平利县兴隆镇翼旭通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (344, NULL, '平利县长安镇精诚家电行', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (345, NULL, '平利县长安镇鑫晟通讯专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (346, NULL, '平利鑫致远通讯东二路专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021796/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (347, NULL, '平利迅捷oppo专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (348, NULL, '平利翼腾通信社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021794/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (349, NULL, '平利翼途新城社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021782/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (350, NULL, '平利长安张店村服务站-田家平', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (351, NULL, '平利正阳乡电信合作营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021788/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (352, NULL, '秋林巴山路卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (353, NULL, '秋林商贸公园店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (354, NULL, '秋林通讯兴安中路合作营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025631/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (355, NULL, '区域三组', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (356, NULL, '区域四组直销', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (357, NULL, '区域一组', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (358, NULL, '日升通讯关庙手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022723/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (359, NULL, '睿晨通讯电信代理店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (360, NULL, '石泉池河镇老街专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (361, NULL, '石泉池河镇明星村服务站-王运动', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (362, NULL, '石泉后柳专营店', NULL, 'rtmp://125.76.229.15:1936/live/pag/125.76.229.15/7302/026162/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (363, NULL, '石泉凯瑞通专营店', NULL, 'rtmp://125.76.229.199:1935/live/pag/125.76.229.199/7302/025521/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (364, NULL, '石泉两河纯华通讯专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024556/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (365, NULL, '石泉天启通讯社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (366, NULL, '石泉县诚誉洋鑫通信店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025951/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (367, NULL, '石泉县城西通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (368, NULL, '石泉县池河镇怀玉通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (369, NULL, '石泉县池河镇天天通讯手机店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (370, NULL, '石泉县都得意通讯店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025338/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (371, NULL, '石泉县恒宾实业有限责任公司', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (372, NULL, '石泉县后柳镇长风专营店', NULL, 'rtmp://125.76.229.15:1936/live/pag/125.76.229.15/7302/026123/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (373, NULL, '石泉县家合商贸有限责任公司', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (374, NULL, '石泉县金信通电讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (375, NULL, '石泉县秋林电讯向阳路专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (376, NULL, '石泉县全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (377, NULL, '石泉县陕通福翼讯通通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (378, NULL, '石泉县王觅通讯店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025589/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (379, NULL, '石泉县维沃通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (380, NULL, '石泉县喜河镇罗昌利手机卖场', NULL, 'rtmp://125.76.229.15:1936/live/pag/125.76.229.15/7302/026125/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (381, NULL, '石泉县祥飞商贸有限公司', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (382, NULL, '石泉县艺千宏通讯手机零售部', NULL, 'rtmp://125.76.229.199:1935/live/pag/125.76.229.199/7302/025531/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (383, NULL, '石泉县翼果果通讯店', NULL, 'rtmp://125.76.229.199:1935/live/pag/125.76.229.199/7302/025492/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (384, NULL, '石泉县张德建通信店', NULL, 'rtmp://125.76.229.199:1935/live/pag/125.76.229.199/7302/025494/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (385, NULL, '石泉县珍壵通信店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/026117/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (386, NULL, '石泉县正义通讯向阳路店', NULL, 'rtmp://125.76.229.199:1935/live/pag/125.76.229.199/7302/025496/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (387, NULL, '石泉迎丰专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (388, NULL, '石泉熨斗专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025591/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (389, NULL, '石泉长安专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025944/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (390, NULL, '石泉中池专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (391, NULL, '四季镇合作营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022720/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (392, NULL, '太山庙乡营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (393, NULL, '谭坝镇伟业电器电信代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (394, NULL, '汪茂玲电信业务宴台代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (395, NULL, '五里鑫五星家电电信代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (396, NULL, '西安迪信通（江北晏台店)', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (397, NULL, '西安迪信通@安康大桥路朝都荟', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (398, NULL, '西安中鸿讯信息技术有限公司安康', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (399, NULL, '肖善兵手机专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (400, NULL, '小河锋行智能体验店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (401, NULL, '小河桐木知己通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (402, NULL, '小曙河支局曙坪专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (403, NULL, '兴达电讯茨沟OPPO手机店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (404, NULL, '旬阳白柳电信合作营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022683/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (405, NULL, '旬阳段家河九九通讯全网通卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (406, NULL, '旬阳飞阳通讯振旬路专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022710/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (407, NULL, '旬阳甘溪电信合作营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (408, NULL, '旬阳公馆全网通手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022681/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (409, NULL, '旬阳构元全网通手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022713/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (410, NULL, '旬阳构元镇羊山村服务站-夏英库', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (411, NULL, '旬阳关口大庙村服务站-郭富仓', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (412, NULL, '旬阳关口电信合作营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (413, NULL, '旬阳桂花电信代理店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024337/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (414, NULL, '旬阳海燕手机店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (415, NULL, '旬阳蒿塔全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (416, NULL, '旬阳红军茨坪社区服务站-韩定翠', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (417, NULL, '旬阳红军上马村服务站-陈京奇', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (418, NULL, '旬阳红军镇张霞全网通手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022684/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (419, NULL, '旬阳红军中湾村服务站-金正路', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (420, NULL, '旬阳尖山电信代理店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (421, NULL, '旬阳金寨郭家湾村服务站-郭小飞', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (422, NULL, '旬阳金寨花房村服务站-陈锋', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (423, NULL, '旬阳金寨庙子娅村服务站-陈艳丽', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (424, NULL, '旬阳金寨权口村服务站-张春萍', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (425, NULL, '旬阳金寨谭家园服务站-石正花', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (426, NULL, '旬阳金寨张河村服务站-冯彩霞', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (427, NULL, '旬阳仁河电信代理店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (428, NULL, '旬阳尚佳OPPO手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (429, NULL, '旬阳尚佳二店手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (430, NULL, '旬阳神河卖场电信合作营业厅1', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (431, NULL, '旬阳十全十美通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (432, NULL, '旬阳石门全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (433, NULL, '旬阳石门王家坪服务站-秦晓玲', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (434, NULL, '旬阳蜀河沙沟村服务站-金花', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (435, NULL, '旬阳武王全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (436, NULL, '旬阳县白柳镇梁鹏电信代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (437, NULL, '旬阳县城东支局菜湾片区社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (438, NULL, '旬阳县城东支局老城片区社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (439, NULL, '旬阳县城关镇九州电信代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (440, NULL, '旬阳县城关镇立信通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (441, NULL, '旬阳县城关镇领先VIVO旬中店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (442, NULL, '旬阳县城关镇泰然社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (443, NULL, '旬阳县城郊支局商业住宅区社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (444, NULL, '旬阳县赤岩镇代理二店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022697/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (445, NULL, '旬阳县甘溪镇耐用电信代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (446, NULL, '旬阳县海燕移动通信店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (447, NULL, '旬阳县宏达通讯VIVO专卖一店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (448, NULL, '旬阳县金寨镇玲玲电信通讯经营部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (449, NULL, '旬阳县吕河代办点', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022678/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (450, NULL, '旬阳县麻坪镇宏达电信代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (451, NULL, '旬阳县麻坪镇誉兴通讯全网通卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022680/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (452, NULL, '旬阳县牌楼玉峰通讯全网通卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (453, NULL, '旬阳县仁河乡垂猛电信代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (454, NULL, '旬阳县神河镇亿佳通讯全网通卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022694/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (455, NULL, '旬阳县蜀河镇电信合作营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (456, NULL, '旬阳县蜀河镇吕关电信代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (457, NULL, '旬阳县蜀河镇星轩电信便利店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (458, NULL, '旬阳县蜀河镇旭日电信代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (459, NULL, '旬阳县双河街道营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (460, NULL, '旬阳县双河镇西岔电信便利店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (461, NULL, '旬阳县双河镇鑫源电信代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (462, NULL, '旬阳县桐木镇富恒电信代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (463, NULL, '旬阳县铜钱关镇潘氏电信代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (464, NULL, '旬阳县下菜湾智慧家庭服务中心', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024336/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (465, NULL, '旬阳县仙河镇鑫誉通讯全网通卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (466, NULL, '旬阳县仙河镇讯龙电信通信经营部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (467, NULL, '旬阳县小河锋行通讯全网通卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022695/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (468, NULL, '旬阳县小河街道宏达电信代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (469, NULL, '旬阳县小河镇天宇通讯全网通卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (470, NULL, '旬阳县翼锋通讯商贸大街', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (471, NULL, '旬阳县赵湾镇诚心通讯全网通卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022677/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (472, NULL, '旬阳县赵湾镇电信代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (473, NULL, '旬阳县赵湾镇丫头沟天翼通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (474, NULL, '旬阳小河电信合作营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022679/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (475, NULL, '旬阳小河两河关村服务站-赵国瑛', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (476, NULL, '旬阳小河坪槐村服务站-杨孝第', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (477, NULL, '旬阳义慧电信代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (478, NULL, '旬阳翼龙手机卖场电信合作营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022696/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (479, NULL, '旬阳张河电信代理店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022709/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (480, NULL, '旬阳赵湾红岩社区服务站-杨桂婷', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (481, NULL, '旬阳赵湾全岭村服务站-江开辉', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (482, NULL, '旬阳竹筒全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (483, NULL, '旬阳棕溪车家坡村服务站-许名爱', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (484, NULL, '旬阳棕溪全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (485, NULL, '旬阳棕溪狮子岩村服务站-龚长格', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (486, NULL, '旬阳棕溪枣园村服务站-王修海', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (487, NULL, '亿和高井社区体验店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025158/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (488, NULL, '易杰江南一品社区体验店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025160/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (489, NULL, '翼成网络安康高新区现代城专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (490, NULL, '瀛湖镇成兴手机专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025031/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (491, NULL, '张岭合作营业厅', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (492, NULL, '镇坪三星精品店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (493, NULL, '镇坪县OPPO手机专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (494, NULL, '镇坪县曾家镇VIVO手机专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (495, NULL, '镇坪县曾家镇小敏手机专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (496, NULL, '镇坪县诚信手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (497, NULL, '镇坪县上新街OPPO手机专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (498, NULL, '镇坪县曙坪镇永红通讯专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (499, NULL, '镇坪县熙雅手机通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (500, NULL, '镇坪县讯畅手机通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (501, NULL, '镇坪县永红通讯上新街二店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (502, NULL, '镇坪县语尚通讯手机店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (503, NULL, '镇坪县直销团队', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (504, NULL, '镇坪县钟宝镇鸿福电器服务中心', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (505, NULL, '中国电信智慧家庭服务中心中太店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (506, NULL, '中国邮政金州路代理点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (507, NULL, '中通福安康白河县城关镇营业部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (508, NULL, '中通福安康汉滨区南环东路店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024343/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (509, NULL, '中通福安康旬阳县小河北社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022715/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (510, NULL, '钟宝代理店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022717/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (511, NULL, '钟宝全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (512, NULL, '周台便捷通讯服务部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (513, NULL, '紫阳高滩镇八庙村服务站-刘斌', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (514, NULL, '紫阳锐翔通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (515, NULL, '紫阳天目通讯', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (516, NULL, '紫阳县VIVO专卖1店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (517, NULL, '紫阳县VIVO专卖店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (518, NULL, '紫阳县翱翱通讯社区店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022671/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (519, NULL, '紫阳县芭蕉电信专营店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (520, NULL, '紫阳县百佳通信合作店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (521, NULL, '紫阳县斑桃镇全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (522, NULL, '紫阳县洞河镇电信代理店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025415/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (523, NULL, '紫阳县高滩镇电信二厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022705/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (524, NULL, '紫阳县高滩镇合作营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022675/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (525, NULL, '紫阳县汉王镇电信专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025438/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (526, NULL, '紫阳县蒿坪合作营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022672/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (527, NULL, '紫阳县蒿坪镇电信第二合作营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022674/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (528, NULL, '紫阳县红椿镇全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (529, NULL, '紫阳县焕古镇电信专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022706/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (530, NULL, '紫阳县黄正芳家电生活馆', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (531, NULL, '紫阳县洄水刘权家电经营部', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (532, NULL, '紫阳县洄水镇电信专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022702/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (533, NULL, '紫阳县洄水镇合作营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025439/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (534, NULL, '紫阳县界岭镇全网通手机卖场', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (535, NULL, '紫阳县柯勇家电生活馆', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (536, NULL, '紫阳县乐享通讯惠民社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (537, NULL, '紫阳县立信通讯', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025476/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (538, NULL, '紫阳县麻柳朱荟代办点', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (539, NULL, '紫阳县毛坝镇电信专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022676/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (540, NULL, '紫阳县毛坝镇合作营业厅', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022707/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (541, NULL, '紫阳县任河嘴城东片区社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (542, NULL, '紫阳县润霆通讯店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024339/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (543, NULL, '紫阳县尚品家电生活馆', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (544, NULL, '紫阳县双安镇全网通手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022673/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (545, NULL, '紫阳县双桥镇全网通店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (546, NULL, '紫阳县天目专营店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025474/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (547, NULL, '紫阳县瓦庙镇全网通手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022703/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (548, NULL, '紫阳县王清芳家电生活馆', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (549, NULL, '紫阳县西关锐翔社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (550, NULL, '紫阳县西关社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (551, NULL, '紫阳县显成家电生活馆', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (552, NULL, '紫阳县向阳镇全网通手机卖场', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022712/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (553, NULL, '紫阳县新浪通讯', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025478/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (554, NULL, '紫阳县永宏家电生活馆', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (555, NULL, '紫阳县优创智慧便利店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (556, NULL, '紫阳县朱婷婷通讯店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (557, NULL, '紫阳县紫府路南片区社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (558, NULL, '紫阳县紫府路南社区店', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (559, NULL, '紫阳县紫盛通讯店', NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025472/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (560, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024509/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (561, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025149/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (562, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025150/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (563, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021852/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (564, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024375/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (565, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024377/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (566, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021722/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (567, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021773/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (568, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021699/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (569, NULL, NULL, NULL, 'rtmp://125.76.229.199:1935/live/pag/125.76.229.199/7302/025151/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (570, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021720/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (571, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025113/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (572, NULL, NULL, NULL, 'rtmp://125.76.229.15:1936/live/pag/125.76.229.15/7302/026121/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (573, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021771/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (574, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/021696/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (575, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/025515/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (576, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022688/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (577, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022722/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (578, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/022682/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (579, NULL, NULL, NULL, 'rtmp://125.76.229.16:1936/live/pag/125.76.229.16/7302/024338/0/MAIN/TCP', NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (580, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (581, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (582, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (583, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (584, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (585, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (586, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (587, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (588, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (589, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (590, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (591, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (592, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (593, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (594, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (595, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (596, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (597, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (598, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (599, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (600, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (601, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (602, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (603, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (604, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (605, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (606, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (607, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (608, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (609, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (610, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (611, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (612, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (613, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (614, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (615, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (616, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (617, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (618, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (619, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (620, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (621, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (622, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (623, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `video_url` VALUES (624, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;
