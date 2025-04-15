--
-- 表的结构 `__PREFIX__addons_demo`
--

CREATE TABLE `__PREFIX__addons_demo` (
                                         `id` int UNSIGNED NOT NULL COMMENT 'ID',
                                         `member_id` int DEFAULT NULL COMMENT '会员ID',
                                         `cate_id` int NOT NULL COMMENT '分类ID',
                                         `cate_ids` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分类IDS',
                                         `week` enum('monday','tuesday','wednesday') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monday' COMMENT '星期=[monday:星期一,tuesday:星期二,wednesday:星期三]',
                                         `sexdata` enum('male','female','secret') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'secret' COMMENT '性别=[male:男,female:女,secret:保密]',
                                         `textarea` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
                                         `image` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图片=1',
                                         `images` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '图片集合=10',
                                         `attach_file` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '附件=1',
                                         `attach_files` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '附件=10',
                                         `attach_file_choose` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '文件',
                                         `keywords` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关键字',
                                         `price` float(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '价格',
                                         `startdate` date NOT NULL COMMENT '开始日期',
                                         `activitytime` datetime NOT NULL COMMENT '活动时间',
                                         `timestaptime` timestamp NOT NULL COMMENT '时间戳\r\n',
                                         `year` year NOT NULL COMMENT '年',
                                         `times` time NOT NULL COMMENT '时间',
                                         `switch` tinyint(1) NOT NULL DEFAULT '1' COMMENT '上架状态=[0:下架,1:正常]',
                                         `open_switch` tinyint(1) NOT NULL DEFAULT '0' COMMENT '开关=[0:OFF,1:ON]',
                                         `mystate` set('1','2','3') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT '复选=[1:选项1,2:选项2,3:选项3]',
                                         `mystatetest` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '复选=[1:选项1,2:选项2,3:选项3]',
                                         `my2state` set('0','1','2') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '2' COMMENT '爱好=[0:唱歌,1:跳舞,2:游泳]',
                                         `editor_content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '富文本',
                                         `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '描述',
                                         `my_color` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '颜色',
                                         `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态=[0:OFF,1:ON]',
                                         `create_time` int DEFAULT '0' COMMENT '创建时间',
                                         `update_time` int NOT NULL DEFAULT '0' COMMENT '更新时间',
                                         `delete_time` int DEFAULT '0' COMMENT '删除时间',
                                         `province_id` int DEFAULT NULL COMMENT '省份',
                                         `city_id` int DEFAULT NULL COMMENT '市',
                                         `area_id` int DEFAULT NULL COMMENT '区县',
                                         `citys` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '省市区',
                                         `citys_ids` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '省市区ID',
                                         `rate` int DEFAULT NULL COMMENT '评分',
                                         `slider` int NOT NULL COMMENT '滑块',
                                         `region` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '城市区域'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='测试表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `__PREFIX__addons_demo`
--

INSERT INTO `__PREFIX__addons_demo` (`id`, `member_id`, `cate_id`, `cate_ids`, `week`, `sexdata`, `textarea`, `image`, `images`, `attach_file`, `attach_files`, `attach_file_choose`, `keywords`, `price`, `startdate`, `activitytime`, `timestaptime`, `year`, `times`, `switch`, `open_switch`, `mystate`, `mystatetest`, `my2state`, `editor_content`, `description`, `my_color`, `status`, `create_time`, `update_time`, `delete_time`, `province_id`, `city_id`, `area_id`, `citys`, `citys_ids`, `rate`, `slider`, `region`) VALUES
    (46, 0, 1, '1', 'monday', 'secret', '1', 'http://fundemo.funadmin.com/storage/upload/20220328/8aad749d551d9ccb510a2ea4cd0b33ae.jpeg', 'http://fundemo.funadmin.com/storage/undefined/20220328/94103576e1e24d08f0e99e714566cfd8.png,http://fundemo.funadmin.com/storage/upload/20220328/8aad749d551d9ccb510a2ea4cd0b33ae.jpeg', 'http://fundemo.funadmin.com/storage/undefined/20220328/94103576e1e24d08f0e99e714566cfd8.png', 'http://fundemo.funadmin.com/storage/upload/20220328/8aad749d551d9ccb510a2ea4cd0b33ae.jpeg', '/storage/uploads/20220415/f9a4c78f45aa944dd5a0166893ebb73d.jpg', 'afd ', 0.00, '2022-03-29', '2022-03-29 00:00:00', '2022-03-28 08:00:00', 2022, '00:00:00', 1, 0, '1', '1', '2', '2', '1', '', 1, 1648537861, 1650091773, 0, 340000, 340200, 340203, '安徽省/芜湖市/弋江区', '540000,540200,540232', 3, 0, '北京,上海,江西,广东-深圳');

-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__addons_demo_cate`
--

CREATE TABLE `__PREFIX__addons_demo_cate` (
                                              `id` int NOT NULL COMMENT 'ID',
                                              `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名字',
                                              `thumb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '缩略图',
                                              `create_time` int DEFAULT '0' COMMENT '创建时间',
                                              `update_time` int DEFAULT '0' COMMENT '更新时间',
                                              `delete_time` int DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='测试分类表';

--
-- 转存表中的数据 `__PREFIX__addons_demo_cate`
--

INSERT INTO `__PREFIX__addons_demo_cate` (`id`, `title`, `thumb`, `create_time`, `update_time`, `delete_time`) VALUES
                                                                                                                   (1, '测试1', '', 0, 0, 0),
                                                                                                                   (2, '测试2', '', 0, 0, 0);

--
-- 转储表的索引
--

--
-- 表的索引 `__PREFIX__addons_demo`
--
ALTER TABLE `__PREFIX__addons_demo`
    ADD PRIMARY KEY (`id`) USING BTREE,
    ADD UNIQUE KEY `demo` (`keywords`) USING BTREE;

--
-- 表的索引 `__PREFIX__addons_demo_cate`
--
ALTER TABLE `__PREFIX__addons_demo_cate`
    ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `__PREFIX__addons_demo`
--
ALTER TABLE `__PREFIX__addons_demo`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=47;

--
-- 使用表AUTO_INCREMENT `__PREFIX__addons_demo_cate`
--
ALTER TABLE `__PREFIX__addons_demo_cate`
    MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;
COMMIT;

--
-- 表的结构 `__PREFIX__addons_curd`
--

CREATE TABLE `__PREFIX__addons_curd` (
                                   `id` int(11) NOT NULL COMMENT 'ID',
                                   `admin_id` int(11) NOT NULL COMMENT '管理员ID\r\n',
                                   `post_json` text NOT NULL COMMENT 'POST数据',
                                   `curd` text NOT NULL COMMENT '命令行',
                                   `status` tinyint(1) DEFAULT '1' COMMENT '状态=[1:启用,0:禁用]',
                                   `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
                                   `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
                                   `delete_time` int(11) DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='curd命令';

--
-- 转储表的索引
--

--
-- 表的索引 `__PREFIX__addons_curd`
--
ALTER TABLE `__PREFIX__addons_curd`
    ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `__PREFIX__addons_curd`
--
ALTER TABLE `__PREFIX__addons_curd`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
