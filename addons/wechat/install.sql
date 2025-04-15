
--
-- 表的结构 `__PREFIX__addons_wechat_account`
--

CREATE TABLE `__PREFIX__addons_wechat_account` (
                                             `id` int NOT NULL COMMENT '表id',
                                             `merchant_id` int NOT NULL DEFAULT '0' COMMENT '商户id',
                                             `wxname` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '公众号名称',
                                             `app_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'APPID',
                                             `secret` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'APPSECRET',
                                             `origin_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '公众号原始ID',
                                             `wx_code` char(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '微信号',
                                             `avatar` char(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '头像地址',
                                             `token` char(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '对接TOKEN',
                                             `aeskey` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '微信对接encodingaeskey',
                                             `related` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'addons/wechat/auth/wechatauth/related?merchant_id=1' COMMENT '微信对接地址',
                                             `type` tinyint(1) NOT NULL DEFAULT '3' COMMENT '类型=1:普通订阅号2:认证订阅号,3:普通服务号,4:认证服务号/认证媒体/政府订阅号',
                                             `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'logo',
                                             `qr` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '二维码',
                                             `status` tinyint(1) DEFAULT '1' COMMENT '微信接入状态=0:待接入,1:已接入',
                                             `create_time` int DEFAULT '0' COMMENT 'create_time',
                                             `update_time` int DEFAULT '0' COMMENT 'update_time',
                                             `delete_time` int DEFAULT '0' COMMENT 'delete_time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='微信公公众帐号';

--
-- 转存表中的数据 `__PREFIX__addons_wechat_account`
--

INSERT INTO `__PREFIX__addons_wechat_account` (`id`, `merchant_id`, `wxname`, `app_id`, `secret`, `origin_id`, `wx_code`, `avatar`, `token`, `aeskey`, `related`, `type`, `logo`, `qr`, `status`, `create_time`, `update_time`, `delete_time`) VALUES
    (1, 0, '测试号管理', 'wxecd04cbbfc06a972', 'ec83a45f2a561a90cf5f63e7476bae36', 'wxecd04cbbfc06a972', 'gh_8b042cc4ccf9', '', 'weixins', '', 'wechat/related', 4, NULL, '', 1, 1613184433, 1627261136, 0);

-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__addons_wechat_fans`
--

CREATE TABLE `__PREFIX__addons_wechat_fans` (
                                          `id` int NOT NULL COMMENT '粉丝ID',
                                          `member_id` int DEFAULT '0' COMMENT '会员编号ID',
                                          `source_member_id` int DEFAULT '0' COMMENT '推广人member_id',
                                          `merchant_id` int DEFAULT '0' COMMENT '店铺ID',
                                          `nickname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '昵称',
                                          `nickname_encode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
                                          `headimgurl` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
                                          `sex` smallint DEFAULT '1' COMMENT '性别',
                                          `language` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '用户语言',
                                          `country` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '国家',
                                          `province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '省',
                                          `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '城市',
                                          `district` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '行政区/县',
                                          `openid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'openid',
                                          `unionid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '粉丝unionid',
                                          `groupid` int DEFAULT '0' COMMENT '粉丝所在组id',
                                          `subscribe` tinyint(1) DEFAULT '1' COMMENT '是否订阅',
                                          `subscribe_scene` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '订阅场景',
                                          `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
                                          `tags` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '标签',
                                          `tagid_list` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '标签列表',
                                          `subscribe_time` int DEFAULT '0' COMMENT '订阅时间',
                                          `unsubscribe_time` int DEFAULT '0' COMMENT '解订阅时间',
                                          `qr_scene` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '二维码扫码场景（开发者自定义）',
                                          `qr_scene_str` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '二维码扫码场景描述（开发者自定义）',
                                          `status` tinyint(1) DEFAULT '1' COMMENT '状态=(0:禁用,1:启用)',
                                          `update_time` int DEFAULT '0' COMMENT '粉丝信息最后更新时间',
                                          `create_time` int DEFAULT '0' COMMENT '添加时间',
                                          `delete_time` int UNSIGNED DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='微信公众号获取粉丝列表';

--
-- 转存表中的数据 `__PREFIX__addons_wechat_fans`
--

INSERT INTO `__PREFIX__addons_wechat_fans` (`id`, `member_id`, `source_member_id`, `merchant_id`, `nickname`, `nickname_encode`, `headimgurl`, `sex`, `language`, `country`, `province`, `city`, `district`, `openid`, `unionid`, `groupid`, `subscribe`, `subscribe_scene`, `remark`, `tags`, `tagid_list`, `subscribe_time`, `unsubscribe_time`, `qr_scene`, `qr_scene_str`, `status`, `update_time`, `create_time`, `delete_time`) VALUES
                                                                                                                                                                                                                                                                                                                                                                                                                                    (2, 0, 0, 0, '李荣', '', 'http://thirdwx.qlogo.cn/mmopen/7jOTIafB9k714aTdeFthfpgIYZckKKOIrCwBYTF9PUjOnu7wB2vB1uQlPXs5CzKPyx0zAm4qhyoj1BcGp2yryoIceu55Cgpz/132', 1, 'zh_CN', '中国', '广东', '广州', '', 'oBSasxLWGxIQu6Wcfmx0bDQzGXV8', '', 0, 1, 'ADD_SCENE_QR_CODE', '', NULL, '[]', 1578303330, 0, '0', '', 1, 1627812776, 1626331710, 0),
                                                                                                                                                                                                                                                                                                                                                                                                                                    (3, 0, 0, 0, '讲一个笑话', '', 'http://thirdwx.qlogo.cn/mmopen/iatZI1Yp2aPI2sYCzCn2t5akxPQN3WPjIFBLaCcx5alFsyVhVPjE4kF7BQuKWbM97GpF7lBqSbBk3GNTPzkZGkmdiahriaMF7EW/132', 1, 'zh_CN', '中国', '', '', '', 'oBSasxDil8RE1caCl5QMxU8jvkP4', '', 0, 1, 'ADD_SCENE_QR_CODE', '', NULL, '[]', 1614932027, 0, '0', '', 1, 1627812776, 1626331798, 0),
                                                                                                                                                                                                                                                                                                                                                                                                                                    (4, 0, 0, 0, '赵睿霆', '', 'http://thirdwx.qlogo.cn/mmopen/BA8GZZ2EGwWiaiaGBnRokdzIrzib7a6dnx2c8kmnzb2TBOFrxXyGIiaH2L55hM5wkydibkfLhwGa87QGXugeCmwQpKg/132', 1, 'zh_CN', '中国', '云南', '丽江', '', 'oBSasxG5z6nbGoSl_ynngaGItuC8', '', 0, 1, 'ADD_SCENE_QR_CODE', '', NULL, '[]', 1577668988, 0, '0', '', 1, 1627812776, 1626331809, 0),
                                                                                                                                                                                                                                                                                                                                                                                                                                    (5, 0, 0, 0, '大泉', '', 'http://thirdwx.qlogo.cn/mmopen/Q3auHgzwzM6p4CXvgsB8qBOGl46XILHfxicKu2Rxw3LjeTSyhPWfqWKdURZMaHpgiaLPRCQKib8vz9B8SM4snYNsw/132', 1, 'zh_CN', '南乔治亚岛和南桑德韦奇岛', '', '', '', 'oBSasxOEPLmmgQNeDMrWh1UI-En0', '', 0, 1, 'ADD_SCENE_QR_CODE', '', NULL, '[]', 1576631958, 0, '0', 'foo', 1, 1627812776, 1626331814, 0),
                                                                                                                                                                                                                                                                                                                                                                                                                                    (6, 0, 0, 0, '扫地僧', '', 'http://thirdwx.qlogo.cn/mmopen/0YNSjjHibkflFlzwib2wUIN6fS2OmDBjZnj4nKRnQJgXgzSbTEzNDOsZmgflwZnrrC3OECR4sUJMCh5qzfE7EGiapicdCqOOdRLc/132', 1, 'zh_CN', '中国', '四川', '成都', '', 'oBSasxE0jkdZ9KFq_SuByqDkpR7I', '', 0, 1, 'ADD_SCENE_QR_CODE', '', NULL, '[]', 1576492841, 0, '0', '', 1, 1627812776, 1626331842, 0),
                                                                                                                                                                                                                                                                                                                                                                                                                                    (7, 0, 0, 0, 'Jonathan', '', 'http://thirdwx.qlogo.cn/mmopen/ajNVdqHZLLC5NwAJYqHdFfvVlIBh1dsMW05m5RoicWOia8sKBQric6uibN8VnABlAh8XD2nJre4VlpvAZFKlE1qj8g/132', 1, 'zh_CN', '中国', '', '', '', 'oBSasxLv_dfk0w77TMZJi0Ja9zUg', '', 0, 1, 'ADD_SCENE_QR_CODE', '', NULL, '[]', 1580223516, 0, '0', '', 1, 1627812776, 1626331957, 0),
                                                                                                                                                                                                                                                                                                                                                                                                                                    (8, 0, 0, 0, '夏天的肥皂沫子', '', 'http://thirdwx.qlogo.cn/mmopen/7jOTIafB9k5INibS1JVtq64TbfZ5r1ibfhMbN7IeP8wN77iauGpJjtjpYFvPlM6kLuO54kHDhG7UEWHCjP2smiak9A/132', 1, 'zh_CN', '中国', '天津', '滨海新区', '', 'oBSasxLqf9ZlFDhUjj3pUTTsVkfA', '', 2, 1, 'ADD_SCENE_QR_CODE', '', NULL, '[2]', 1574170023, 0, '0', '', 1, 1627812776, 1626331989, 0),
                                                                                                                                                                                                                                                                                                                                                                                                                                    (9, 0, 0, 0, '🐘 心之所向🐘', '', 'http://thirdwx.qlogo.cn/mmopen/Q3auHgzwzM4VFiaYnBD77jqvXaG55kz8cYgynjUAic5oNcrjkicjIGvVVyRYfLsiceojIlI709OKWPAQr95E2y2Ick6jSHSrIJXgtcn1VnDM4qE/132', 1, 'zh_CN', '中国', '广东', '深圳', '', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '', 0, 1, 'ADD_SCENE_QR_CODE', '', 'cesceee', '[]', 1627522635, 0, '0', '', 1, 1627812776, 1626332366, 0),
                                                                                                                                                                                                                                                                                                                                                                                                                                    (10, 0, 0, 0, '城虎', '', 'http://thirdwx.qlogo.cn/mmopen/7jOTIafB9k5sfAJqic0CU8crjicH6Wcq3NdWdsYvucMk4T1Y8V9NdbWDWXAIYPz9t5GM9vZg7viboG7VhrEvIqBicq1RuzBgGTOh/132', 1, 'zh_CN', '巴巴多斯岛', '', '', '', 'oBSasxLCVKBX-X8DSew8mH1S-ETc', '', 2, 1, 'ADD_SCENE_QR_CODE', '', NULL, '[2]', 1576720474, 0, '0', 'foo', 1, 1627812776, 1627812776, 0);

-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__addons_wechat_material`
--

CREATE TABLE `__PREFIX__addons_wechat_material` (
                                              `id` int UNSIGNED NOT NULL COMMENT '微信公众号素材',
                                              `merchant_id` int NOT NULL DEFAULT '1',
                                              `wx_aid` int DEFAULT NULL,
                                              `media_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '微信媒体id',
                                              `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '视频文件名',
                                              `media_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                              `local_cover` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ' ',
                                              `type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '图片（image）、视频（video）、语音 （voice）、图文（news）音乐（music）',
                                              `des` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT ' ' COMMENT '视频描述',
                                              `create_time` int DEFAULT '0' COMMENT '添加时间',
                                              `update_time` int UNSIGNED DEFAULT '0' COMMENT '更新时间',
                                              `delete_time` int UNSIGNED DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='微信公众号素材';

--
-- 转存表中的数据 `__PREFIX__addons_wechat_material`
--

INSERT INTO `__PREFIX__addons_wechat_material` (`id`, `merchant_id`, `wx_aid`, `media_id`, `file_name`, `media_url`, `local_cover`, `type`, `des`, `create_time`, `update_time`, `delete_time`) VALUES
                                                                                                                                                                                              (1, 1, NULL, 'Mkk-XekVsp2Cvr5VktS-vWmtq4eH9TKXr6H9P_PwosY', NULL, 'http://mmbiz.qpic.cn/mmbiz_png/nKp1y5rQibObSozfae9O4uHe9EfNQajicykH64VYBA2CTjEqYDCAjSRyNSfjnEn974A845xF5AfrGUSCbPdg370g/0?wx_fmt=png', '/storage/uploads/20210720/d2987c1722e3ca80db6c3b75c73275ce.jpg', 'image', ' ', 1626752438, 1626752438, 0),
                                                                                                                                                                                              (2, 1, NULL, 'Mkk-XekVsp2Cvr5VktS-vfgLLY3J3LaN8q9cG9dIw4U', NULL, 'http://mmbiz.qpic.cn/mmbiz_png/nKp1y5rQibObSozfae9O4uHe9EfNQajicykH64VYBA2CTjEqYDCAjSRyNSfjnEn974A845xF5AfrGUSCbPdg370g/0?wx_fmt=png', ' ', 'news', ' ', 1627625621, 1627625621, 0),
                                                                                                                                                                                              (3, 1, NULL, 'Mkk-XekVsp2Cvr5VktS-vYY_MYlzQoG419VGOtezJLg', NULL, 'http://mmbiz.qpic.cn/mmbiz_png/nKp1y5rQibOZiaW5KKOwneM171H4TxO9ro6HH1bibbRwlpNEWB9tuoomN09IyZg2XQvibrudolhnnibNV13rpzfWT9w/0?wx_fmt=png', '/storage/uploads/20210801/c0ce8c25c559af56d6888581f105ddb9.jpg', 'image', ' ', 1627807346, 1627807346, 0),
                                                                                                                                                                                              (4, 1, NULL, 'Mkk-XekVsp2Cvr5VktS-vX95HFgEI1mwAs9RYToERsk', NULL, 'http://mmbiz.qpic.cn/mmbiz_png/nKp1y5rQibOZiaW5KKOwneM171H4TxO9ro6HH1bibbRwlpNEWB9tuoomN09IyZg2XQvibrudolhnnibNV13rpzfWT9w/0?wx_fmt=png', '/storage/uploads/20210801/b970ee1f126764a39c9a0be9ed4a3f1d.jpg', 'image', ' ', 1627807594, 1627807594, 0),
                                                                                                                                                                                              (5, 1, NULL, 'Mkk-XekVsp2Cvr5VktS-vVkQSDqsyG7DKqmahpr-9qw', NULL, 'http://mmbiz.qpic.cn/mmbiz_jpg/nKp1y5rQibOZiaW5KKOwneM171H4TxO9roSVDNlOq4F28kcicyMFcfZsITbic2PLy5YUa7ibb3Tg8oxGdjNcuBMd7eg/0?wx_fmt=jpeg', '/storage/uploads/20210801/6aa7a1edd69042529fdfe6febfe5489d.jpg', 'image', ' ', 1627807609, 1627807609, 0),
                                                                                                                                                                                              (6, 1, NULL, 'Mkk-XekVsp2Cvr5VktS-vWXNShyEkwhhQ5unRz79DfY', NULL, 'http://mmbiz.qpic.cn/mmbiz_jpg/nKp1y5rQibOZiaW5KKOwneM171H4TxO9roSVDNlOq4F28kcicyMFcfZsITbic2PLy5YUa7ibb3Tg8oxGdjNcuBMd7eg/0?wx_fmt=jpeg', '/storage/uploads/20210801/13ab6e0f91d0679fe0ff3a037d91c807.jpg', 'image', ' ', 1627807869, 1627807869, 0);

-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__addons_wechat_material_info`
--

CREATE TABLE `__PREFIX__addons_wechat_material_info` (
                                                   `id` int UNSIGNED NOT NULL COMMENT 'id',
                                                   `merchant_id` int DEFAULT '0',
                                                   `material_id` int DEFAULT NULL,
                                                   `thumb_media_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '	图文消息的封面图片素材id（必须是永久mediaID）',
                                                   `local_cover` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                   `cover` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '图文消息封面',
                                                   `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                   `author` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '作者',
                                                   `show_cover` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示封面',
                                                   `digest` text COLLATE utf8mb4_unicode_ci COMMENT '图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空',
                                                   `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '正文',
                                                   `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '图文页的URL，或者，当获取的列表是图片素材列表时，该字段是图片的URL',
                                                   `content_source_url` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图文消息的原文地址，即点击“阅读原文”后的URL',
                                                   `need_open_comment` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Uint32 是否打开评论，0不打开，1打开',
                                                   `only_fans_can_comment` tinyint(1) DEFAULT '1' COMMENT 'Uint32 是否粉丝才可评论，0所有人可评论，1粉丝才可评论',
                                                   `sort` int NOT NULL DEFAULT '0' COMMENT '排序号',
                                                   `hits` int NOT NULL DEFAULT '0' COMMENT '阅读次数',
                                                   `delete_time` int UNSIGNED DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__addons_wechat_menu`
--

CREATE TABLE `__PREFIX__addons_wechat_menu` (
                                          `id` int NOT NULL COMMENT '主键',
                                          `merchant_id` int DEFAULT '0' COMMENT '店铺id',
                                          `menu_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
                                          `menu_data` json DEFAULT NULL COMMENT '菜单数据',
                                          `hits` int NOT NULL DEFAULT '0' COMMENT '触发数',
                                          `sort` int NOT NULL DEFAULT '0' COMMENT '排序',
                                          `status` tinyint(1) DEFAULT '1' COMMENT '是否启用',
                                          `create_time` int DEFAULT '0' COMMENT '创建日期',
                                          `update_time` int DEFAULT '0' COMMENT '修改日期',
                                          `delete_time` int UNSIGNED DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='微信设置->微信菜单';

--
-- 转存表中的数据 `__PREFIX__addons_wechat_menu`
--

INSERT INTO `__PREFIX__addons_wechat_menu` (`id`, `merchant_id`, `menu_name`, `menu_data`, `hits`, `sort`, `status`, `create_time`, `update_time`, `delete_time`) VALUES
                                                                                                                                                                (5, 0, '默认菜单', '[{\"key\": \"adsfasdfadfqweqweq\", \"url\": \"asdfasdfasdf\", \"name\": \"一级菜单1212\", \"type\": \"click\", \"sub_button\": [{\"key\": \"asdfasdfadf\", \"url\": \"http://static.kancloud.cn/manual/thinkphp6_0/1037632\", \"name\": \"二级菜单\", \"type\": \"click\", \"appid\": \"asdfa\", \"pagepath\": \"asdfasdf\"}]}]', 0, 0, 0, 1627434010, 1627541168, 0),
                                                                                                                                                                (6, 0, '默认菜单', '[{\"key\": \"guanjianzi\", \"name\": \"系统\", \"type\": \"click\", \"sub_button\": [{\"key\": \"关键字\", \"url\": \"http://www.baidu.com\", \"name\": \"百度\", \"type\": \"view\"}, {\"key\": \"rselfmenu_0_0\", \"name\": \"扫码\", \"type\": \"scancode_waitmsg\"}, {\"key\": \"rselfmenu_0_1\", \"name\": \"扫码2\", \"type\": \"scancode_push\"}, {\"key\": \"rselfmenu_2_0\", \"name\": \"地理位置\", \"type\": \"location_select\"}, {\"key\": \"rselfmenu_1_0\", \"name\": \"拍照发图\", \"type\": \"pic_sysphoto\"}]}, {\"key\": \"rselfmenu_1_2\", \"name\": \"管理\", \"type\": \"pic_weixin\", \"sub_button\": [{\"key\": \"你好\", \"name\": \"点击事件\", \"type\": \"click\"}, {\"key\": \"rselfmenu_1_1\", \"name\": \"拍照相册\", \"type\": \"pic_photo_or_album\"}, {\"key\": \"rselfmenu_1_2\", \"name\": \"相册发图\", \"type\": \"pic_weixin\"}]}, {\"key\": \"rselfmenu_0_0\", \"url\": \"http://www.sina.com\", \"name\": \"一级菜单1\", \"type\": \"view\", \"sub_button\": []}]', 0, 0, 1, 1627458569, 1627541172, 0),
                                                                                                                                                                (7, 0, '默认菜单', '[{\"key\": \"消息\", \"name\": \"一级菜单\", \"type\": \"click\", \"sub_button\": []}]', 0, 0, 0, 1627539359, 1627540793, 0);

-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__addons_wechat_message`
--

CREATE TABLE `__PREFIX__addons_wechat_message` (
                                             `id` int UNSIGNED NOT NULL,
                                             `merchant_id` int UNSIGNED DEFAULT '1' COMMENT '商户id',
                                             `media_id` int DEFAULT NULL,
                                             `keyword_id` int DEFAULT '0' COMMENT '关键字id',
                                             `nickname` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '昵称',
                                             `openid` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
                                             `content_json` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                             `content` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '微信消息',
                                             `type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
                                             `event` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '详细事件',
                                             `status` tinyint NOT NULL DEFAULT '1' COMMENT '状态0:禁用;1启用',
                                             `create_time` int UNSIGNED DEFAULT '0' COMMENT '创建时间',
                                             `update_time` int NOT NULL DEFAULT '0' COMMENT '修改时间',
                                             `delete_time` int UNSIGNED DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='微信_历史记录表';

--
-- 转存表中的数据 `__PREFIX__addons_wechat_message`
--

INSERT INTO `__PREFIX__addons_wechat_message` (`id`, `merchant_id`, `media_id`, `keyword_id`, `nickname`, `openid`, `content_json`, `content`, `type`, `event`, `status`, `create_time`, `update_time`, `delete_time`) VALUES
                                                                                                                                                                                                                     (1, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1626339124\",\"MsgType\":\"text\",\"Content\":\"\\u4f60\\u597d\",\"MsgId\":\"23283580287346465\"}', '你好', 'text', '', 1, 1626339124, 1626339125, 0),
                                                                                                                                                                                                                     (2, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1626339160\",\"MsgType\":\"text\",\"Content\":\"\\u4f60\\u597d\",\"MsgId\":\"23283579780121435\"}', '你好', 'text', '', 1, 1626339160, 1626339161, 0),
                                                                                                                                                                                                                     (3, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627033526\",\"MsgType\":\"event\",\"Event\":\"VIEW\",\"EventKey\":\"https:\\/\\/ask.csdn.net\\/questions\\/1084155\",\"MenuId\":\"426300732\"}', '', 'event', 'VIEW', 1, 1627033526, 1627033527, 0),
                                                                                                                                                                                                                     (4, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627369351\",\"MsgType\":\"event\",\"Event\":\"VIEW\",\"EventKey\":\"https:\\/\\/www.oschina.net\\/\",\"MenuId\":\"426301181\"}', '', 'event', 'VIEW', 1, 1627369351, 1627369352, 0),
                                                                                                                                                                                                                     (5, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627369409\",\"MsgType\":\"event\",\"Event\":\"VIEW\",\"EventKey\":\"https:\\/\\/www.oschina.net\\/\",\"MenuId\":\"426301181\"}', '', 'event', 'VIEW', 1, 1627369409, 1627369410, 0),
                                                                                                                                                                                                                     (6, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627369563\",\"MsgType\":\"event\",\"Event\":\"VIEW\",\"EventKey\":\"https:\\/\\/www.oschina.net\\/\",\"MenuId\":\"426301181\"}', '', 'event', 'VIEW', 1, 1627369563, 1627369564, 0),
                                                                                                                                                                                                                     (7, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627369579\",\"MsgType\":\"event\",\"Event\":\"VIEW\",\"EventKey\":\"https:\\/\\/www.oschina.net\\/\",\"MenuId\":\"426301181\"}', '', 'event', 'VIEW', 1, 1627369579, 1627369579, 0),
                                                                                                                                                                                                                     (8, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627460250\",\"MsgType\":\"event\",\"Event\":\"CLICK\",\"EventKey\":\"asdfasdfadf\"}', '', 'event', 'CLICK', 1, 1627460250, 1627460251, 0),
                                                                                                                                                                                                                     (9, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627460256\",\"MsgType\":\"event\",\"Event\":\"CLICK\",\"EventKey\":\"asdfasdfadf\"}', '', 'event', 'CLICK', 1, 1627460256, 1627460256, 0),
                                                                                                                                                                                                                     (10, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627462516\",\"MsgType\":\"event\",\"Event\":\"pic_photo_or_album\",\"EventKey\":\"rselfmenu_1_1\",\"SendPicsInfo\":{\"Count\":\"0\",\"PicList\":null}}', '', 'event', 'pic_photo_or_album', 1, 1627462516, 1627462517, 0),
                                                                                                                                                                                                                     (11, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627462517\",\"MsgType\":\"image\",\"PicUrl\":\"http:\\/\\/mmbiz.qpic.cn\\/mmbiz_jpg\\/hrm4Dn6o0vQhUNO9H8DuTgkibfsNAeIJnwX04ICsWmoCNH6ZGxw51e5sDWcxTjjAbbQZKR3oEaEcRaFxZXcEfIg\\/0\",\"MsgId\":\"23299663222232784\",\"MediaId\":\"iB1DLyC4Rc_HNJ1uW1qAAWhf6h6dnk5hRRBoemlMR-dqe5SGmFNmB3Yr1s34EYfP\"}', 'http://mmbiz.qpic.cn/mmbiz_jpg/hrm4Dn6o0vQhUNO9H8DuTgkibfsNAeIJnwX04ICsWmoCNH6ZGxw51e5sDWcxTjjAbbQZKR3oEaEcRaFxZXcEfIg/0', 'image', '', 1, 1627462517, 1627462519, 0),
                                                                                                                                                                                                                     (12, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627522519\",\"MsgType\":\"text\",\"Content\":\"\\u4f60\\u597d\",\"MsgId\":\"23300523391291017\"}', '你好', 'text', '', 1, 1627522519, 1627522520, 0),
                                                                                                                                                                                                                     (13, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627522635\",\"MsgType\":\"event\",\"Event\":\"subscribe\",\"EventKey\":null}', '', 'event', 'subscribe', 1, 1627522635, 1627522635, 0),
                                                                                                                                                                                                                     (14, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627539314\",\"MsgType\":\"event\",\"Event\":\"VIEW\",\"EventKey\":\"http:\\/\\/www.sina.com\",\"MenuId\":\"426302027\"}', '', 'event', 'VIEW', 1, 1627539314, 1627539314, 0),
                                                                                                                                                                                                                     (15, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627539317\",\"MsgType\":\"event\",\"Event\":\"CLICK\",\"EventKey\":\"\\u4f60\\u597d\"}', '', 'event', 'CLICK', 1, 1627539317, 1627539317, 0),
                                                                                                                                                                                                                     (16, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627539334\",\"MsgType\":\"event\",\"Event\":\"VIEW\",\"EventKey\":\"http:\\/\\/www.baidu.com\",\"MenuId\":\"426302027\"}', '', 'event', 'VIEW', 1, 1627539334, 1627539335, 0),
                                                                                                                                                                                                                     (17, 1, NULL, 2, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627541453\",\"MsgType\":\"text\",\"Content\":\"\\u4f60\\u597d\",\"MsgId\":\"23300792892666107\"}', '你好', 'text', '', 1, 1627541453, 1627541454, 0),
                                                                                                                                                                                                                     (18, 1, NULL, 2, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627542001\",\"MsgType\":\"text\",\"Content\":\"\\u4f60\\u597d\",\"MsgId\":\"23300801272292362\"}', '你好', 'text', '', 1, 1627542001, 1627542001, 0),
                                                                                                                                                                                                                     (19, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627542022\",\"MsgType\":\"text\",\"Content\":\"121\",\"MsgId\":\"23300802411709035\"}', '121', 'text', '', 1, 1627542022, 1627542023, 0),
                                                                                                                                                                                                                     (20, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627542117\",\"MsgType\":\"text\",\"Content\":\"121\",\"MsgId\":\"23300801569237934\"}', '121', 'text', '', 1, 1627542117, 1627542118, 0),
                                                                                                                                                                                                                     (21, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627542123\",\"MsgType\":\"text\",\"Content\":\"121\",\"MsgId\":\"23300801490543156\"}', '121', 'text', '', 1, 1627542123, 1627542124, 0),
                                                                                                                                                                                                                     (22, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627542125\",\"MsgType\":\"text\",\"Content\":\"121\",\"MsgId\":\"23300800689780278\"}', '121', 'text', '', 1, 1627542125, 1627542126, 0),
                                                                                                                                                                                                                     (23, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627542127\",\"MsgType\":\"text\",\"Content\":\"121\",\"MsgId\":\"23300800778323490\"}', '121', 'text', '', 1, 1627542127, 1627542128, 0),
                                                                                                                                                                                                                     (24, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627542138\",\"MsgType\":\"event\",\"Event\":\"VIEW\",\"EventKey\":\"http:\\/\\/www.baidu.com\",\"MenuId\":\"426302027\"}', '', 'event', 'VIEW', 1, 1627542138, 1627542139, 0),
                                                                                                                                                                                                                     (25, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627542143\",\"MsgType\":\"text\",\"Content\":\"1\\u554a\",\"MsgId\":\"23300798974875700\"}', '1啊', 'text', '', 1, 1627542143, 1627542143, 0),
                                                                                                                                                                                                                     (26, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627542144\",\"MsgType\":\"text\",\"Content\":\"167\\u4ebf\",\"MsgId\":\"23300801485538671\"}', '167亿', 'text', '', 1, 1627542144, 1627542145, 0),
                                                                                                                                                                                                                     (27, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627542146\",\"MsgType\":\"text\",\"Content\":\"\\u8fd8\\u6709\",\"MsgId\":\"23300801236099046\"}', '还有', 'text', '', 1, 1627542146, 1627542146, 0),
                                                                                                                                                                                                                     (28, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627543015\",\"MsgType\":\"text\",\"Content\":\"nihao \",\"MsgId\":\"23300815596513853\"}', 'nihao ', 'text', '', 1, 1627543015, 1627543016, 0),
                                                                                                                                                                                                                     (29, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627805310\",\"MsgType\":\"event\",\"Event\":\"VIEW\",\"EventKey\":\"http:\\/\\/www.sina.com\",\"MenuId\":\"426302027\"}', '', 'event', 'VIEW', 1, 1627805310, 1627805311, 0),
                                                                                                                                                                                                                     (30, 1, NULL, 0, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627805314\",\"MsgType\":\"event\",\"Event\":\"CLICK\",\"EventKey\":\"\\u4f60\\u597d\"}', '', 'event', 'CLICK', 1, 1627805314, 1627805315, 0),
                                                                                                                                                                                                                     (31, 1, NULL, 2, '🐘 心之所向🐘', 'oBSasxCSibhs0U_O8d1QCLRR6woQ', '{\"ToUserName\":\"gh_8b042cc4ccf9\",\"FromUserName\":\"oBSasxCSibhs0U_O8d1QCLRR6woQ\",\"CreateTime\":\"1627806538\",\"MsgType\":\"text\",\"Content\":\"\\u4f60\\u597d\",\"MsgId\":\"23304583473620659\"}', '你好', 'text', '', 1, 1627806538, 1627806538, 0);

-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__addons_wechat_qrcode`
--

CREATE TABLE `__PREFIX__addons_wechat_qrcode` (
                                            `id` int NOT NULL,
                                            `merchant_id` int DEFAULT '0' COMMENT '商户ID',
                                            `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '二维码名字',
                                            `qrcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '二维码',
                                            `scene_id` int DEFAULT NULL COMMENT '场景',
                                            `type` tinyint NOT NULL DEFAULT '0' COMMENT '类型=(0:临时,1:永久)',
                                            `ticket` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '	ticket',
                                            `expire_seconds` int NOT NULL COMMENT '有效期',
                                            `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ' ' COMMENT 'URL',
                                            `status` tinyint NOT NULL DEFAULT '1' COMMENT '状态=(0:禁用,1:启用)',
                                            `create_time` int UNSIGNED DEFAULT '0' COMMENT '创建时间',
                                            `update_time` int NOT NULL DEFAULT '0' COMMENT '修改时间',
                                            `delete_time` int UNSIGNED DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='微信二维码';

--
-- 转存表中的数据 `__PREFIX__addons_wechat_qrcode`
--

INSERT INTO `__PREFIX__addons_wechat_qrcode` (`id`, `merchant_id`, `name`, `qrcode`, `scene_id`, `type`, `ticket`, `expire_seconds`, `url`, `status`, `create_time`, `update_time`, `delete_time`) VALUES
                                                                                                                                                                                                 (1, 0, 'ces', 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQG38TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTmYtVTFUNUNlSTQxM0JMbjF4Y2cAAgTlou9gAwQAjScA', NULL, 0, 'gQG38TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTmYtVTFUNUNlSTQxM0JMbjF4Y2cAAgTlou9gAwQAjScA', 2592000, 'http://weixin.qq.com/q/02Nf-U1T5CeI413BLn1xcg', 1, 1626317541, 1627455496, 0),
                                                                                                                                                                                                 (2, 0, '112345', 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQHR8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyRFZxWTB0NUNlSTQxMDAwMHcwNzcAAgSaSLFdAwQAAAAA', NULL, 1, 'gQHR8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyRFZxWTB0NUNlSTQxMDAwMHcwNzcAAgSaSLFdAwQAAAAA', 0, 'http://weixin.qq.com/q/02DVqY0t5CeI410000w077', 1, 1627703654, 1627703694, 0),
                                                                                                                                                                                                 (3, 0, '11234', 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQHR8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyRFZxWTB0NUNlSTQxMDAwMHcwNzcAAgSaSLFdAwQAAAAA', NULL, 1, 'gQHR8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyRFZxWTB0NUNlSTQxMDAwMHcwNzcAAgSaSLFdAwQAAAAA', 30, 'http://weixin.qq.com/q/02DVqY0t5CeI410000w077', 1, 1627703655, 1627804505, 0);

-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__addons_wechat_reply`
--

CREATE TABLE `__PREFIX__addons_wechat_reply` (
                                           `id` int UNSIGNED NOT NULL COMMENT '微信关键词回复表',
                                           `merchant_id` int DEFAULT '0' COMMENT '店铺id',
                                           `rule` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '规则名',
                                           `keyword` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                           `type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'keyword' COMMENT '查询类型keyword,subscribe,default',
                                           `msg_type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '回复消息类型  文本（text ）图片（image）、视频（video）、语音 （voice）、图文（news） 音乐（music）',
                                           `data` mediumtext COLLATE utf8mb4_unicode_ci COMMENT 'text使用该自动存储文本',
                                           `material_id` int UNSIGNED DEFAULT NULL COMMENT 'news、video voice image music的素材id等',
                                           `status` tinyint(1) DEFAULT '1',
                                           `create_time` int UNSIGNED DEFAULT '0' COMMENT '创建时间',
                                           `update_time` int NOT NULL DEFAULT '0' COMMENT '修改时间',
                                           `delete_time` int UNSIGNED DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='微信回复表';

--
-- 转存表中的数据 `__PREFIX__addons_wechat_reply`
--

INSERT INTO `__PREFIX__addons_wechat_reply` (`id`, `merchant_id`, `rule`, `keyword`, `type`, `msg_type`, `data`, `material_id`, `status`, `create_time`, `update_time`, `delete_time`) VALUES
                                                                                                                                                                                     (1, 0, NULL, '你好', 'default', 'text', '欢迎关注FunAdmin', 0, 1, 1626399766, 1626399766, 0),
                                                                                                                                                                                     (2, 0, NULL, '你好', 'keyword', 'text', '这个是一个消息', 0, 1, 1627541435, 1627541435, 0),
                                                                                                                                                                                     (3, 0, NULL, 'ces', 'keyword', 'text', 'ces', 0, 1, 1626399766, 1626399766, 0);

-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__addons_wechat_tags`
--

CREATE TABLE `__PREFIX__addons_wechat_tags` (
                                          `id` int NOT NULL,
                                          `tags_id` int DEFAULT NULL COMMENT 'tag id',
                                          `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标签名',
                                          `count` int DEFAULT '0' COMMENT '数量',
                                          `merchant_id` int NOT NULL DEFAULT '1' COMMENT '店铺id',
                                          `status` tinyint(1) DEFAULT '1' COMMENT '状态=(0:禁用,1:启用)',
                                          `create_time` int UNSIGNED DEFAULT '0' COMMENT '创建时间',
                                          `update_time` int DEFAULT '0' COMMENT '修改时间',
                                          `delete_time` int UNSIGNED DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='微信用户标签表';

--
-- 转存表中的数据 `__PREFIX__addons_wechat_tags`
--

INSERT INTO `__PREFIX__addons_wechat_tags` (`id`, `tags_id`, `name`, `count`, `merchant_id`, `status`, `create_time`, `update_time`, `delete_time`) VALUES
                                                                                                                                                  (1, 2, '星标组', 2, 1, 1, 1626336321, 1627552708, 0),
                                                                                                                                                  (2, 100, '粉丝', 0, 1, 1, 1626336321, 1626336321, 0),
                                                                                                                                                  (3, 101, '其他', 0, 1, 1, 1626336321, 1626336321, 0),
                                                                                                                                                  (4, 102, '好友', 0, 1, 1, 1626336321, 1626336321, 0),
                                                                                                                                                  (5, 103, '商业合作伙伴9', 0, 1, 1, 1626336321, 1626336321, 0),
                                                                                                                                                  (6, 106, '大萨达撒', 0, 1, 1, 1626336321, 1626336321, 0),
                                                                                                                                                  (7, 107, '后台管理系统', 0, 1, 1, 1626336321, 1626336321, 0),
                                                                                                                                                  (8, 109, 'cesceee', 0, 1, 1, 1626336321, 1627552708, 0);

-- --------------------------------------------------------

--
-- 表的结构 `__PREFIX__addons_wechat_type`
--

CREATE TABLE `__PREFIX__addons_wechat_type` (
                                          `type_id` tinyint NOT NULL,
                                          `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                                          `status` tinyint(1) NOT NULL DEFAULT '1',
                                          `create_time` int UNSIGNED DEFAULT '0' COMMENT '创建时间',
                                          `update_time` int NOT NULL DEFAULT '0' COMMENT '修改时间',
                                          `delete_time` int UNSIGNED DEFAULT '0' COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='微信类型表';

--
-- 转存表中的数据 `__PREFIX__addons_wechat_type`
--

INSERT INTO `__PREFIX__addons_wechat_type` (`type_id`, `name`, `status`, `create_time`, `update_time`, `delete_time`) VALUES
                                                                                                                    (1, '普通订阅号', 1, 0, 0, 0),
                                                                                                                    (2, '认证订阅号', 1, 0, 0, 0),
                                                                                                                    (3, '普通服务号', 1, 0, 0, 0),
                                                                                                                    (4, '认证服务号/认证媒体/政府订阅号', 1, 0, 0, 0);

--
-- 转储表的索引
--

--
-- 表的索引 `__PREFIX__addons_wechat_account`
--
ALTER TABLE `__PREFIX__addons_wechat_account`
    ADD PRIMARY KEY (`id`);

--
-- 表的索引 `__PREFIX__addons_wechat_fans`
--
ALTER TABLE `__PREFIX__addons_wechat_fans`
    ADD PRIMARY KEY (`id`),
  ADD KEY `openid` (`openid`(191)),
  ADD KEY `unionid` (`unionid`(191));

--
-- 表的索引 `__PREFIX__addons_wechat_material`
--
ALTER TABLE `__PREFIX__addons_wechat_material`
    ADD PRIMARY KEY (`id`),
  ADD KEY `media_id` (`media_id`);

--
-- 表的索引 `__PREFIX__addons_wechat_material_info`
--
ALTER TABLE `__PREFIX__addons_wechat_material_info`
    ADD PRIMARY KEY (`id`);

--
-- 表的索引 `__PREFIX__addons_wechat_menu`
--
ALTER TABLE `__PREFIX__addons_wechat_menu`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_biz_shop_menu_orders` (`sort`),
  ADD KEY `IDX_biz_shop_menu_shopId` (`merchant_id`);

--
-- 表的索引 `__PREFIX__addons_wechat_message`
--
ALTER TABLE `__PREFIX__addons_wechat_message`
    ADD PRIMARY KEY (`id`);

--
-- 表的索引 `__PREFIX__addons_wechat_qrcode`
--
ALTER TABLE `__PREFIX__addons_wechat_qrcode`
    ADD PRIMARY KEY (`id`);

--
-- 表的索引 `__PREFIX__addons_wechat_reply`
--
ALTER TABLE `__PREFIX__addons_wechat_reply`
    ADD PRIMARY KEY (`id`);

--
-- 表的索引 `__PREFIX__addons_wechat_tags`
--
ALTER TABLE `__PREFIX__addons_wechat_tags`
    ADD PRIMARY KEY (`id`);

--
-- 表的索引 `__PREFIX__addons_wechat_type`
--
ALTER TABLE `__PREFIX__addons_wechat_type`
    ADD PRIMARY KEY (`type_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `__PREFIX__addons_wechat_account`
--
ALTER TABLE `__PREFIX__addons_wechat_account`
    MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '表id', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `__PREFIX__addons_wechat_fans`
--
ALTER TABLE `__PREFIX__addons_wechat_fans`
    MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '粉丝ID', AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `__PREFIX__addons_wechat_material`
--
ALTER TABLE `__PREFIX__addons_wechat_material`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '微信公众号素材', AUTO_INCREMENT=7;

--
-- 使用表AUTO_INCREMENT `__PREFIX__addons_wechat_material_info`
--
ALTER TABLE `__PREFIX__addons_wechat_material_info`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id';

--
-- 使用表AUTO_INCREMENT `__PREFIX__addons_wechat_menu`
--
ALTER TABLE `__PREFIX__addons_wechat_menu`
    MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT '主键', AUTO_INCREMENT=8;

--
-- 使用表AUTO_INCREMENT `__PREFIX__addons_wechat_message`
--
ALTER TABLE `__PREFIX__addons_wechat_message`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- 使用表AUTO_INCREMENT `__PREFIX__addons_wechat_qrcode`
--
ALTER TABLE `__PREFIX__addons_wechat_qrcode`
    MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `__PREFIX__addons_wechat_reply`
--
ALTER TABLE `__PREFIX__addons_wechat_reply`
    MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '微信关键词回复表', AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `__PREFIX__addons_wechat_tags`
--
ALTER TABLE `__PREFIX__addons_wechat_tags`
    MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
