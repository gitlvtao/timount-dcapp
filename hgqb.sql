-- MySQL dump 10.13  Distrib 5.5.53, for Win32 (AMD64)
--
-- Host: localhost    Database: hgqb
-- ------------------------------------------------------
-- Server version	5.5.53

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `hgqb_ad_positions`
--

DROP TABLE IF EXISTS `hgqb_ad_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_ad_positions` (
  `positions_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `positions_title` varchar(100) DEFAULT NULL COMMENT '广告位置名称',
  `positions_ident` varchar(20) DEFAULT NULL COMMENT '广告位置编码',
  `positions_brief` varchar(255) DEFAULT NULL COMMENT '广告位置描述',
  `positions_create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`positions_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='广告位置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_ad_positions`
--

LOCK TABLES `hgqb_ad_positions` WRITE;
/*!40000 ALTER TABLE `hgqb_ad_positions` DISABLE KEYS */;
INSERT INTO `hgqb_ad_positions` VALUES (4,'首页banner','banner_index','首页banner',1542340466);
/*!40000 ALTER TABLE `hgqb_ad_positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_admin_auth`
--

DROP TABLE IF EXISTS `hgqb_admin_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_admin_auth` (
  `auth_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限id',
  `auth_pid` int(11) DEFAULT '0' COMMENT '上级权限id',
  `auth_name` varchar(20) NOT NULL DEFAULT '' COMMENT '权限名称',
  `auth_url` varchar(255) DEFAULT NULL COMMENT '权限地址',
  `auth_icon` varchar(50) DEFAULT NULL COMMENT '权限图标',
  `auth_sort` smallint(2) DEFAULT '99' COMMENT '排序',
  `auth_menu_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '菜单状态 1 显示 0 隐藏',
  `auth_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '权限状态 1 整除 0 禁用',
  `auth_create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`auth_id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='管理权限节点表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_admin_auth`
--

LOCK TABLES `hgqb_admin_auth` WRITE;
/*!40000 ALTER TABLE `hgqb_admin_auth` DISABLE KEYS */;
INSERT INTO `hgqb_admin_auth` VALUES (1,0,'权限管理','','layui-icon-template-1',1,1,1,0),(2,1,'管理员列表','admin/adminuser/index','',99,1,1,0),(3,1,'用户组列表','admin/adminrole/index','',99,1,1,0),(4,1,'权限列表','admin/adminauth/index','',99,0,1,0),(8,0,'文章管理','','layui-icon-form',4,1,1,0),(9,8,'文章列表','article/article/index','',99,1,1,0),(10,8,'单页管理','article/single/index','',99,1,1,0),(11,0,'模块管理','','layui-icon-layouts',3,1,1,0),(12,11,'模块列表','blocks/block/index','',99,1,1,0),(13,8,'分类管理','article/column/index','',99,1,1,0),(14,0,'产品管理','','layui-icon-cart',2,1,1,0),(25,14,'产品列表管理','goods/goods/index','',99,1,1,0),(17,0,'消息管理','','layui-icon-dialogue',5,1,1,0),(18,17,'消息列表','notice/notice/index','',99,1,1,0),(26,14,'产品分类管理','column/column/index','',1,1,1,0),(27,0,'用户管理','','layui-icon-user',99,1,1,0),(21,0,'广告管理','','layui-icon-picture',99,1,1,0),(22,21,'广告位列表','ads/positions/index','',99,1,1,0),(23,14,'产品筛选管理','screen/screen/index','',2,1,1,0),(24,21,'广告列表','ads/ad/index','',99,1,1,0),(28,27,'用户列表','user/user/index','',99,1,1,0),(29,27,'贷款测一测','user/test/index','',99,1,1,0);
/*!40000 ALTER TABLE `hgqb_admin_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_admin_role`
--

DROP TABLE IF EXISTS `hgqb_admin_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_admin_role` (
  `role_id` tinyint(3) NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `role_name` varchar(20) DEFAULT NULL COMMENT '角色名称',
  `role_describe` varchar(255) DEFAULT NULL COMMENT '角色描述',
  `role_auth` varchar(255) DEFAULT NULL COMMENT '权限列表',
  `role_create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_admin_role`
--

LOCK TABLES `hgqb_admin_role` WRITE;
/*!40000 ALTER TABLE `hgqb_admin_role` DISABLE KEYS */;
INSERT INTO `hgqb_admin_role` VALUES (1,'超级管理员','超级管理员',NULL,1539072772);
/*!40000 ALTER TABLE `hgqb_admin_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_admin_user`
--

DROP TABLE IF EXISTS `hgqb_admin_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_admin_user` (
  `user_id` mediumint(8) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `user_username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `user_password` varchar(50) NOT NULL DEFAULT '' COMMENT '用户密码',
  `user_nickname` varchar(20) DEFAULT '' COMMENT '用户昵称',
  `user_group` varchar(20) DEFAULT NULL COMMENT '用户分组',
  `user_create_time` int(10) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `user_login_ip` varchar(20) DEFAULT NULL COMMENT '最后一次登录ip',
  `user_login_time` int(10) DEFAULT NULL COMMENT '最后一次登录时间',
  `user_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '用户状态',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_admin_user`
--

LOCK TABLES `hgqb_admin_user` WRITE;
/*!40000 ALTER TABLE `hgqb_admin_user` DISABLE KEYS */;
INSERT INTO `hgqb_admin_user` VALUES (1,'admin','e10adc3949ba59abbe56e057f20f883e','红光商城','1',1539070264,'192.168.1.123',1543214325,1);
/*!40000 ALTER TABLE `hgqb_admin_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_ads`
--

DROP TABLE IF EXISTS `hgqb_ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_ads` (
  `ad_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `ad_position_id` int(11) DEFAULT NULL COMMENT '广告位置id',
  `ad_title` varchar(100) DEFAULT NULL COMMENT '广告名称',
  `ad_brief` varchar(255) DEFAULT NULL COMMENT '广告简要',
  `ad_url` varchar(255) DEFAULT NULL COMMENT '广告网址',
  `ad_image` varchar(255) DEFAULT NULL COMMENT '广告图片',
  `ad_sort` tinyint(2) DEFAULT '99' COMMENT '广告排序',
  `ad_status` tinyint(2) DEFAULT '1' COMMENT '广告状态 1 正常 0 下线 -1 删除',
  `ad_create_time` int(10) DEFAULT NULL COMMENT '广告创建时间',
  PRIMARY KEY (`ad_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='广告表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_ads`
--

LOCK TABLES `hgqb_ads` WRITE;
/*!40000 ALTER TABLE `hgqb_ads` DISABLE KEYS */;
INSERT INTO `hgqb_ads` VALUES (2,4,'啊啊','啊啊','https://www.layui.com/','http://phx4bho4j.bkt.clouddn.com/20181116_cc258c0c718f662e3122b1c729de7e80.jpg',99,1,1542340498);
/*!40000 ALTER TABLE `hgqb_ads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_article`
--

DROP TABLE IF EXISTS `hgqb_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_article` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `article_column_id` int(11) DEFAULT '0' COMMENT '栏目id',
  `article_title` varchar(100) DEFAULT NULL COMMENT '文章标题',
  `article_thumb` varchar(255) DEFAULT NULL COMMENT '文章缩略图',
  `article_brief` varchar(255) DEFAULT NULL COMMENT '文章简要',
  `article_content` text COMMENT '文章内容',
  `article_create_time` int(10) DEFAULT NULL COMMENT '文章创建时间',
  `article_update_time` int(10) DEFAULT NULL COMMENT '文章更新时间',
  `article_comment_num` int(11) DEFAULT '0' COMMENT '评论数',
  `article_collect_num` int(11) DEFAULT '0' COMMENT '收藏数',
  `article_view_num` int(11) DEFAULT '0' COMMENT '浏览数',
  `article_zan_num` int(11) DEFAULT '0' COMMENT '点赞数',
  `article_is_single` tinyint(1) DEFAULT '0' COMMENT '是否单页 1 是 0 不是',
  `article_single_ident` varchar(20) DEFAULT '' COMMENT '单页标识',
  `article_sort` tinyint(2) unsigned DEFAULT '99' COMMENT '排序',
  `article_attr` tinyint(1) DEFAULT '0' COMMENT '属性',
  `article_status` tinyint(2) DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`article_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='文章表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_article`
--

LOCK TABLES `hgqb_article` WRITE;
/*!40000 ALTER TABLE `hgqb_article` DISABLE KEYS */;
INSERT INTO `hgqb_article` VALUES (1,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,15,18,0,2,0,'',99,1,1),(2,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(3,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(4,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(5,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(6,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(7,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(8,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(9,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(10,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(11,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(12,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(13,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(14,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(15,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(16,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(17,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(18,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(19,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(20,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(21,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(22,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(23,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(24,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(25,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(26,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(27,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(28,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(29,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(30,8,'测试1 ','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','测试攻略内容阿斯利康你打的','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1542704048,NULL,0,0,0,0,0,'',99,1,1),(31,9,'123','','123','<h1>123</h1><p>&nbsp;123</p><p><br></p><p>123123&nbsp;</p>',1542704048,1542704048,0,0,0,0,0,'',99,0,1);
/*!40000 ALTER TABLE `hgqb_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_article_comment`
--

DROP TABLE IF EXISTS `hgqb_article_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_article_comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `comment_pid` int(11) DEFAULT '0' COMMENT '回复评论id',
  `comment_article_id` int(11) DEFAULT NULL COMMENT '文章id',
  `comment_user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `comment_content` varchar(255) DEFAULT NULL COMMENT '评论内容',
  `comment_create_time` int(10) DEFAULT NULL COMMENT '评论时间',
  `comment_status` tinyint(3) DEFAULT '1' COMMENT '评论状态',
  `comment_zan_num` int(11) unsigned DEFAULT '0' COMMENT '评论点赞数',
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='文章评论表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_article_comment`
--

LOCK TABLES `hgqb_article_comment` WRITE;
/*!40000 ALTER TABLE `hgqb_article_comment` DISABLE KEYS */;
INSERT INTO `hgqb_article_comment` VALUES (1,0,1,1,'123',1542791538,1,4),(2,1,1,2,'456',1542793538,1,0),(3,1,1,3,'789',1542798538,1,0),(4,0,1,5,'123456',1542858538,1,1),(5,0,1,1,'sadfsadfdsaf',1542867198,1,1),(6,0,1,1,'sfsfsdf',1542867269,1,1),(7,0,1,1,'121212',1542868064,1,0),(8,0,1,1,'ahahaahh',1542868982,1,0),(9,0,1,1,'ehehheeh',1542869016,1,1),(10,0,1,1,'ahaah',1542869097,1,1),(11,10,1,1,'121212',1542869415,1,0),(12,10,1,1,'1212121212',1542869430,1,0),(13,10,1,1,'水电费水电费苏打粉',1542869436,1,0),(14,9,1,1,'122112',1542869450,1,0),(15,10,1,1,'12121212',1542870204,1,0),(16,8,1,1,'sdfsdf',1542870225,1,0),(17,7,1,1,'12121',1542870378,1,0),(18,5,1,1,'zhiy',1542870391,1,0),(19,4,1,1,'阿哈哈哈哈',1542870414,1,0);
/*!40000 ALTER TABLE `hgqb_article_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_bind_user_notice`
--

DROP TABLE IF EXISTS `hgqb_bind_user_notice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_bind_user_notice` (
  `bind_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bind_user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `bind_notice_id` int(10) unsigned NOT NULL COMMENT '已读消息id',
  `bind_type` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '标识   1 已读  2 删除',
  `bind_create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `bind_update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除消息时间',
  PRIMARY KEY (`bind_id`,`bind_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_bind_user_notice`
--

LOCK TABLES `hgqb_bind_user_notice` WRITE;
/*!40000 ALTER TABLE `hgqb_bind_user_notice` DISABLE KEYS */;
INSERT INTO `hgqb_bind_user_notice` VALUES (17,1,1,1,0,1542353543),(18,1,2,1,0,1542353895),(19,1,3,1,0,1542355594),(22,1,4,2,0,1542966483),(23,1,5,2,0,1542966483);
/*!40000 ALTER TABLE `hgqb_bind_user_notice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_block_data`
--

DROP TABLE IF EXISTS `hgqb_block_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_block_data` (
  `data_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `data_block_id` int(11) DEFAULT NULL COMMENT '模块id',
  `data_object_id` int(11) DEFAULT NULL COMMENT '关联对象id',
  `data_sort` tinyint(2) unsigned DEFAULT '99' COMMENT '排序',
  `data_create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`data_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='模块数据管理';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_block_data`
--

LOCK TABLES `hgqb_block_data` WRITE;
/*!40000 ALTER TABLE `hgqb_block_data` DISABLE KEYS */;
INSERT INTO `hgqb_block_data` VALUES (8,3,6,99,1542357537),(9,3,7,99,1542357537),(10,4,3,99,1542357575),(11,4,1,99,1542357575);
/*!40000 ALTER TABLE `hgqb_block_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_blocks`
--

DROP TABLE IF EXISTS `hgqb_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_blocks` (
  `block_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `block_ident` varchar(20) DEFAULT NULL COMMENT '模块标识',
  `block_title` varchar(100) DEFAULT NULL COMMENT '模块名称',
  `block_brief` varchar(255) DEFAULT NULL COMMENT '模块简要',
  `block_create_time` int(10) DEFAULT NULL COMMENT '模块创建时间',
  `block_status` tinyint(2) DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`block_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='模块表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_blocks`
--

LOCK TABLES `hgqb_blocks` WRITE;
/*!40000 ALTER TABLE `hgqb_blocks` DISABLE KEYS */;
INSERT INTO `hgqb_blocks` VALUES (3,'column_index','首页ICON','首页ICON',1542340744,1),(4,'product_index','热门推荐','热门推荐',1542357565,1);
/*!40000 ALTER TABLE `hgqb_blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_collect_log`
--

DROP TABLE IF EXISTS `hgqb_collect_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_collect_log` (
  `collect_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `collect_user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `collect_object_id` int(11) DEFAULT NULL COMMENT '浏览对象id',
  `collect_create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `collect_type` tinyint(1) DEFAULT NULL COMMENT '浏览类型  1 产品 2攻略',
  PRIMARY KEY (`collect_id`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_collect_log`
--

LOCK TABLES `hgqb_collect_log` WRITE;
/*!40000 ALTER TABLE `hgqb_collect_log` DISABLE KEYS */;
INSERT INTO `hgqb_collect_log` VALUES (37,1,1,1542593029,1),(33,1,3,1542267546,1),(58,1,1,1542877278,2);
/*!40000 ALTER TABLE `hgqb_collect_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_column`
--

DROP TABLE IF EXISTS `hgqb_column`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_column` (
  `column_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `column_pid` int(11) DEFAULT NULL COMMENT '栏目父od',
  `column_name` varchar(20) NOT NULL COMMENT '栏目名称',
  `column_thumb` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `column_banner` varchar(255) DEFAULT NULL COMMENT '栏目主图',
  `column_summary` varchar(255) DEFAULT NULL COMMENT '栏目摘要',
  `column_sort` tinyint(2) unsigned DEFAULT '99' COMMENT '排序',
  `column_create_time` int(11) unsigned NOT NULL COMMENT '栏目创建时间',
  `column_type` tinyint(1) DEFAULT NULL COMMENT '栏目类别 1 产品 2 文章',
  `column_status` tinyint(2) DEFAULT '1' COMMENT 'aa',
  PRIMARY KEY (`column_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_column`
--

LOCK TABLES `hgqb_column` WRITE;
/*!40000 ALTER TABLE `hgqb_column` DISABLE KEYS */;
INSERT INTO `hgqb_column` VALUES (6,NULL,'大额贷款','http://phx4bho4j.bkt.clouddn.com/20181113_d3b20d4ce83c118ee102d908b1010b33.png','http://phx4bho4j.bkt.clouddn.com/20181113_f57a284a2bfbf04c083521e207e46d1e.png','大额贷款',99,1542103209,1,1),(7,NULL,'超低门槛','http://phx4bho4j.bkt.clouddn.com/20181113_d649eab72a736c74645281925c535e67.png','http://phx4bho4j.bkt.clouddn.com/20181113_d332399eb259b8d3d9d4c72595cd43cf.png','超低门槛',99,1542103509,1,1),(8,0,'攻略','','','攻略',99,1542595627,2,1),(9,0,'常见问题','','','常见问题',99,1542703056,2,1);
/*!40000 ALTER TABLE `hgqb_column` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_config`
--

DROP TABLE IF EXISTS `hgqb_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `config_title` varchar(20) DEFAULT NULL COMMENT '配置标题',
  `config_key` varchar(20) DEFAULT NULL COMMENT '配置key',
  `config_content` varchar(255) DEFAULT NULL COMMENT '配置内容',
  `config_create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`config_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_config`
--

LOCK TABLES `hgqb_config` WRITE;
/*!40000 ALTER TABLE `hgqb_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `hgqb_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_fabulous_log`
--

DROP TABLE IF EXISTS `hgqb_fabulous_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_fabulous_log` (
  `fabulous_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `fabulous_user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `fabulous_object_id` int(11) DEFAULT NULL COMMENT '浏览对象id',
  `fabulous_create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `fabulous_type` tinyint(1) DEFAULT NULL COMMENT '浏览类型  1 文章 2评论',
  PRIMARY KEY (`fabulous_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_fabulous_log`
--

LOCK TABLES `hgqb_fabulous_log` WRITE;
/*!40000 ALTER TABLE `hgqb_fabulous_log` DISABLE KEYS */;
INSERT INTO `hgqb_fabulous_log` VALUES (8,10,1,1542877969,2),(9,1,1,1542877973,1),(7,1,1,1542877927,2),(10,9,1,1542877983,2),(11,6,1,1542878012,2),(12,1,10,1542878123,2),(13,1,9,1542878126,2),(14,1,6,1542878129,2),(15,1,5,1542878347,2),(16,1,4,1542878386,2);
/*!40000 ALTER TABLE `hgqb_fabulous_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_loan_test`
--

DROP TABLE IF EXISTS `hgqb_loan_test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_loan_test` (
  `test_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `test_user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `test_money` tinyint(1) DEFAULT NULL COMMENT '贷款额度',
  `test_term` tinyint(1) DEFAULT NULL COMMENT '贷款期限',
  `test_use` tinyint(1) DEFAULT NULL COMMENT '贷款用途',
  `test_follow` tinyint(1) DEFAULT NULL COMMENT '关注项',
  `test_create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `test_update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`test_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='贷款测一测';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_loan_test`
--

LOCK TABLES `hgqb_loan_test` WRITE;
/*!40000 ALTER TABLE `hgqb_loan_test` DISABLE KEYS */;
INSERT INTO `hgqb_loan_test` VALUES (1,2,1,1,2,1,1542764827,1542764838),(2,1,1,1,1,1,1542769556,1542880071);
/*!40000 ALTER TABLE `hgqb_loan_test` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_notice`
--

DROP TABLE IF EXISTS `hgqb_notice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_notice` (
  `notice_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `notice_title` varchar(150) NOT NULL DEFAULT '' COMMENT '消息标题',
  `notice_short_title` varchar(255) DEFAULT '' COMMENT '消息短标题',
  `notice_content` text NOT NULL COMMENT '消息内容',
  `notice_status` tinyint(2) DEFAULT '1' COMMENT '消息状态  -1 删除',
  `notice_create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`notice_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_notice`
--

LOCK TABLES `hgqb_notice` WRITE;
/*!40000 ALTER TABLE `hgqb_notice` DISABLE KEYS */;
INSERT INTO `hgqb_notice` VALUES (1,'测试编辑器','1111','<h1>测试编辑器</h1><ol><li>测试编辑器<br></li><li><p>测试编辑器</p></li><li><p><a href=\"www.baidu.com\" target=\"_blank\">测试编辑器</a><br></p></li><li><p>测试编辑器</p></li></ol>',1,1542009209),(2,'萨大','2222','<p>阿萨大</p><p>阿萨大</p><p><img src=\"http://phx4bho4j.bkt.clouddn.com/20181113_e946cedbc09ae9d2f1b896532dfe7ce1.png\" style=\"max-width:100%;\"><br></p>',1,1542071668),(3,'测试数据','3333','<h1>测试数据</h1><p>测试数据</p><p><a href=\"www.baidu.com\" target=\"_blank\">测试数据</a><br></p><p>测试数据</p><p>测试数据</p>',1,1542352704),(4,'测试数据测试数据','4444','<h1>测试数据测试数据测试数据</h1><p><br></p><p>测试数据测试数据测试数据</p><p><br></p><p><a href=\"www.baidu.com\" target=\"_blank\">测试数据</a><br></p><p><img src=\"http://phx4bho4j.bkt.clouddn.com/20181116_1e48c1cede5427965eb03f380dfd6469.png\" style=\"max-width:100%;\"><br></p><p><br></p><p>测试数据测试数据测试数据</p>',1,1542352746),(5,'dasasdas','5555','<h1>dasasdasdasasdasdasasdasdasasdas</h1><p><br></p><p>dasasdasdasasdasdasasdas</p><p><br></p><p><img src=\"http://phx4bho4j.bkt.clouddn.com/20181116_18088c5737d75c17568a72f967bbe6db.png\" style=\"max-width:100%;\"><br></p>',1,1542352790);
/*!40000 ALTER TABLE `hgqb_notice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_product`
--

DROP TABLE IF EXISTS `hgqb_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_product` (
  `product_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_column_id` int(11) DEFAULT '0' COMMENT '栏目id',
  `product_title` varchar(20) NOT NULL DEFAULT '' COMMENT '产品名称',
  `product_logo` varchar(255) DEFAULT NULL COMMENT '产品logo',
  `product_interest_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '利息方式 1 日利息 2 月利息',
  `product_interest` float(5,4) NOT NULL COMMENT '利息',
  `product_apply_people` int(10) NOT NULL DEFAULT '0' COMMENT '申请人数',
  `product_term` varchar(20) NOT NULL COMMENT '期限范围',
  `product_adopt` float(5,4) NOT NULL COMMENT '通过率',
  `product_max_amount` int(11) DEFAULT NULL COMMENT '最高额度',
  `product_url` varchar(255) NOT NULL COMMENT '商品链接',
  `product_attr` tinyint(2) DEFAULT '25' COMMENT '25-无属性 26-热门  27- 推荐（关联attr_config表id）',
  `product_tag` varchar(20) NOT NULL COMMENT '产品标签 1 无视黑白 2 身份证件 3 审核简单 4 大额贷款 5 急速下款 6 新品上市 7 高通过率 8 操作简单',
  `product_limit_range` int(11) DEFAULT NULL COMMENT '额度范围',
  `product_interest_range` int(11) DEFAULT NULL COMMENT '利息范围',
  `product_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '产品状态 1 正常 0 下线 -1 删除',
  `product_grant` varchar(255) DEFAULT '' COMMENT '发放时间',
  `product_info` text COMMENT '申请详情',
  `product_sort` tinyint(2) unsigned DEFAULT '99' COMMENT '排序',
  `product_create_time` int(10) NOT NULL DEFAULT '0' COMMENT '产品创建时间',
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_product`
--

LOCK TABLES `hgqb_product` WRITE;
/*!40000 ALTER TABLE `hgqb_product` DISABLE KEYS */;
INSERT INTO `hgqb_product` VALUES (1,6,'测试1','http://phx4bho4j.bkt.clouddn.com/20181114_4614232874b3b741d47818c30fc6cb16.png',1,0.0015,942,'1-7天',0.9874,5000,'http://www.baidu.com',27,'13,15',6,12,1,'1-6小时',NULL,1,0),(3,6,'测试2','http://phx4bho4j.bkt.clouddn.com/20181114_54e34d805bc0fd1dd3cd4f588d80b9a9.png',1,0.0004,965,'1-2月',0.9450,1000,'http://www.baidu.com',25,'13,19',4,10,1,'1-2小时',NULL,2,1542158342),(4,6,'测试3','http://phx4bho4j.bkt.clouddn.com/20181121_46538d195f0767949b3b1b15572a43a6.png',1,0.0005,25,'1-2月',0.9846,1500,'http://www.baidu.com',25,'13,15',5,10,1,'1-2小时',NULL,99,1542768104),(5,6,'测试4','http://phx4bho4j.bkt.clouddn.com/20181121_477988380a8ca39b020e9681d55085b8.png',1,0.0005,45,'1-3月',0.9846,5000,'http://www.baidu.com',25,'13,15',6,10,1,'1-2小时',NULL,99,1542768196),(6,6,'测试5','http://phx4bho4j.bkt.clouddn.com/20181121_3f06d0f1c80be1947b9fbf2c8d4f1704.png',1,0.0008,32,'1-2月',0.9846,8000,'http://www.baidu.com',25,'13,17',7,11,1,'1-2小时',NULL,99,1542768256),(7,6,'测试6','http://phx4bho4j.bkt.clouddn.com/20181114_4614232874b3b741d47818c30fc6cb16.png',1,0.0015,942,'1-7天',0.9874,5000,'http://www.baidu.com',27,'13,15',6,12,1,'1-6小时',NULL,1,1542148342),(8,6,'测试7','http://phx4bho4j.bkt.clouddn.com/20181114_54e34d805bc0fd1dd3cd4f588d80b9a9.png',1,0.0004,965,'1-2月',0.9450,1000,'http://www.baidu.com',25,'13,19',4,10,1,'1-2小时',NULL,2,1542158342),(9,6,'测试8','http://phx4bho4j.bkt.clouddn.com/20181121_46538d195f0767949b3b1b15572a43a6.png',1,0.0005,25,'1-2月',0.9846,1500,'http://www.baidu.com',25,'13,15',5,10,1,'1-2小时',NULL,99,1542768104),(10,6,'测试9','http://phx4bho4j.bkt.clouddn.com/20181121_477988380a8ca39b020e9681d55085b8.png',1,0.0005,45,'1-3月',0.9846,5000,'http://www.baidu.com',25,'13,15',6,10,1,'1-2小时',NULL,99,1542768196),(11,6,'测试10','http://phx4bho4j.bkt.clouddn.com/20181121_3f06d0f1c80be1947b9fbf2c8d4f1704.png',1,0.0008,32,'1-2月',0.9846,8000,'http://www.baidu.com',25,'13,17',7,11,1,'1-2小时',NULL,99,1542768256),(12,6,'测试11','http://phx4bho4j.bkt.clouddn.com/20181114_4614232874b3b741d47818c30fc6cb16.png',1,0.0015,942,'1-7天',0.9874,5000,'http://www.baidu.com',27,'13,15',6,12,1,'1-6小时',NULL,1,1542148342),(13,6,'测试12','http://phx4bho4j.bkt.clouddn.com/20181114_54e34d805bc0fd1dd3cd4f588d80b9a9.png',1,0.0004,965,'1-2月',0.9450,1000,'http://www.baidu.com',25,'13,19',4,10,1,'1-2小时',NULL,2,1542158342),(14,6,'测试13','http://phx4bho4j.bkt.clouddn.com/20181121_46538d195f0767949b3b1b15572a43a6.png',1,0.0005,25,'1-2月',0.9846,1500,'http://www.baidu.com',25,'13,15',5,10,1,'1-2小时',NULL,99,1542768104),(15,6,'测试14','http://phx4bho4j.bkt.clouddn.com/20181121_477988380a8ca39b020e9681d55085b8.png',1,0.0005,45,'1-3月',0.9846,5000,'http://www.baidu.com',25,'13,15',6,10,1,'1-2小时',NULL,99,1542768196),(16,6,'测试15','http://phx4bho4j.bkt.clouddn.com/20181121_3f06d0f1c80be1947b9fbf2c8d4f1704.png',1,0.0008,32,'1-2月',0.9846,8000,'http://www.baidu.com',25,'13,17',7,11,1,'1-2小时',NULL,99,1542768256),(17,6,'测试16','http://phx4bho4j.bkt.clouddn.com/20181114_4614232874b3b741d47818c30fc6cb16.png',1,0.0015,942,'1-7天',0.9874,5000,'http://www.baidu.com',27,'13,15',6,12,1,'1-6小时',NULL,1,1542148342),(18,6,'测试17','http://phx4bho4j.bkt.clouddn.com/20181114_54e34d805bc0fd1dd3cd4f588d80b9a9.png',1,0.0004,965,'1-2月',0.9450,1000,'http://www.baidu.com',25,'13,19',4,10,1,'1-2小时',NULL,2,1542158342),(19,6,'测试18','http://phx4bho4j.bkt.clouddn.com/20181121_46538d195f0767949b3b1b15572a43a6.png',1,0.0005,25,'1-2月',0.9846,1500,'http://www.baidu.com',25,'13,15',5,10,1,'1-2小时',NULL,99,1542768104),(20,6,'测试19','http://phx4bho4j.bkt.clouddn.com/20181121_477988380a8ca39b020e9681d55085b8.png',1,0.0005,45,'1-3月',0.9846,5000,'http://www.baidu.com',25,'13,15',6,10,1,'1-2小时',NULL,99,1542768196),(21,6,'测试20','http://phx4bho4j.bkt.clouddn.com/20181121_3f06d0f1c80be1947b9fbf2c8d4f1704.png',1,0.0008,32,'1-2月',0.9846,8000,'http://www.baidu.com',25,'13,17',7,11,1,'1-2小时',NULL,99,1542768256),(22,6,'测试21','http://phx4bho4j.bkt.clouddn.com/20181114_4614232874b3b741d47818c30fc6cb16.png',1,0.0015,942,'1-7天',0.9874,5000,'http://www.baidu.com',27,'13,15',6,12,1,'1-6小时',NULL,1,1542148342),(23,6,'测试22','http://phx4bho4j.bkt.clouddn.com/20181114_54e34d805bc0fd1dd3cd4f588d80b9a9.png',1,0.0004,965,'1-2月',0.9450,1000,'http://www.baidu.com',25,'13,19',4,10,1,'1-2小时',NULL,2,1542158342),(24,6,'测试23','http://phx4bho4j.bkt.clouddn.com/20181121_46538d195f0767949b3b1b15572a43a6.png',1,0.0005,25,'1-2月',0.9846,1500,'http://www.baidu.com',25,'13,15',5,10,1,'1-2小时',NULL,99,1542768104),(25,6,'测试24','http://phx4bho4j.bkt.clouddn.com/20181121_477988380a8ca39b020e9681d55085b8.png',1,0.0005,45,'1-3月',0.9846,5000,'http://www.baidu.com',25,'13,15',6,10,1,'1-2小时',NULL,99,1542768196);
/*!40000 ALTER TABLE `hgqb_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_product_attr`
--

DROP TABLE IF EXISTS `hgqb_product_attr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_product_attr` (
  `attr_id` int(11) NOT NULL AUTO_INCREMENT,
  `attr_product_id` int(11) DEFAULT NULL COMMENT '产品id',
  `attr_config_id` mediumint(8) DEFAULT NULL COMMENT '属性id',
  PRIMARY KEY (`attr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品属性表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_product_attr`
--

LOCK TABLES `hgqb_product_attr` WRITE;
/*!40000 ALTER TABLE `hgqb_product_attr` DISABLE KEYS */;
/*!40000 ALTER TABLE `hgqb_product_attr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_product_attr_config`
--

DROP TABLE IF EXISTS `hgqb_product_attr_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_product_attr_config` (
  `config_id` mediumint(8) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `config_title` varchar(20) DEFAULT NULL COMMENT '属性标题',
  `config_pid` mediumint(8) DEFAULT NULL COMMENT '父id',
  `config_status` tinyint(2) DEFAULT '1' COMMENT '-1  删除',
  PRIMARY KEY (`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='产品属性配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_product_attr_config`
--

LOCK TABLES `hgqb_product_attr_config` WRITE;
/*!40000 ALTER TABLE `hgqb_product_attr_config` DISABLE KEYS */;
INSERT INTO `hgqb_product_attr_config` VALUES (1,'标签类型',0,1),(2,'贷款额度',0,1),(3,'贷款利息',0,1),(4,'1000以下',2,1),(5,'1000~2000',2,1),(6,'2000~5000',2,1),(7,'5000~1万',2,1),(8,'1万以上',2,1),(9,'0.03%以下',3,1),(10,'0.03%~0.05%',3,1),(11,'0.05%~0.1%',3,1),(12,'0.1%以上',3,1),(13,'无视黑白',1,1),(14,'身份证件',1,1),(15,'审核简单',1,1),(16,'大额贷款',1,1),(17,'秒批秒下',1,1),(18,'最新口子',1,1),(19,'高通过率',1,1),(20,'操作简单',1,1),(21,'风口平台',1,1),(22,'门槛不高',1,1),(23,'狗分专享',1,1),(24,'商品属性',0,1),(25,'无属性',24,1),(26,'热门',24,1),(27,'推荐',24,1);
/*!40000 ALTER TABLE `hgqb_product_attr_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_users`
--

DROP TABLE IF EXISTS `hgqb_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `user_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号码',
  `user_password` varchar(36) NOT NULL DEFAULT '' COMMENT '用户密码',
  `user_headpic` varchar(255) DEFAULT NULL COMMENT '用户头像',
  `user_nickname` varchar(50) DEFAULT NULL COMMENT '用户昵称',
  `user_sex` tinyint(1) unsigned DEFAULT '0' COMMENT '用户性别  0 位置；1 男；2女',
  `user_age` tinyint(2) unsigned DEFAULT NULL COMMENT '用户年龄',
  `user_reg_time` int(10) NOT NULL DEFAULT '0' COMMENT '用户注册时间',
  `user_reg_ip` varchar(20) DEFAULT NULL COMMENT '注册ip',
  `user_login_time` int(10) DEFAULT NULL COMMENT '用户最后一次登录时间',
  `user_login_ip` varchar(20) DEFAULT NULL COMMENT '最后一次登录ip',
  `user_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '用户装填 1正常 0 冻结',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_users`
--

LOCK TABLES `hgqb_users` WRITE;
/*!40000 ALTER TABLE `hgqb_users` DISABLE KEYS */;
INSERT INTO `hgqb_users` VALUES (1,'13222222222','96e79218965eb72c92a549dd5a330112','http://phx4bho4j.bkt.clouddn.com/20181123174722-5bf7ccaa223a3.jpeg','行走的代码',1,18,0,NULL,1543199418,'192.168.0.122',1),(2,'13344443333','e10adc3949ba59abbe56e057f20f883e',NULL,NULL,0,0,1542181132,NULL,1542182654,'127.0.0.1',1),(3,'15111111111','e10adc3949ba59abbe56e057f20f883e','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','e0176312beb4a36d',0,0,1542611199,'0.0.0.0',1542611199,'0.0.0.0',1),(4,'15311111111','e10adc3949ba59abbe56e057f20f883e','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','7109b79376c332ba',0,NULL,1542611947,'0.0.0.0',1542611947,'0.0.0.0',1),(5,'15144444444','e10adc3949ba59abbe56e057f20f883e','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','77019a1ecd607364',0,NULL,1542611983,'0.0.0.0',1542611983,'0.0.0.0',1),(6,'18311111111','e10adc3949ba59abbe56e057f20f883e','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','8fce94812e93ae57',0,NULL,1542613611,'0.0.0.0',1542613611,'0.0.0.0',1),(7,'18312121212','e10adc3949ba59abbe56e057f20f883e','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','708d3d745ce6ce97',0,NULL,1542613882,'0.0.0.0',1542613882,'0.0.0.0',1),(8,'13555555555','e3ceb5881a0a1fdaad01296d7554868d','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','用户_ausd12',0,NULL,1542613974,'0.0.0.0',1542693903,'0.0.0.0',1),(9,'13444444444','e3ceb5881a0a1fdaad01296d7554868d','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','用户_217f71',0,NULL,1542692474,'0.0.0.0',1542692474,'0.0.0.0',1),(10,'13888888888','21218cca77804d2ba1922c33e0151105','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','用户_8220e9',0,NULL,1542696473,'0.0.0.0',1542696473,'0.0.0.0',1),(11,'13777777777','f63f4fbc9f8c85d409f2f59f2b9e12d5','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','用户_038e03',0,NULL,1542696677,'0.0.0.0',1542696677,'0.0.0.0',1),(12,'13222222221','e3ceb5881a0a1fdaad01296d7554868d','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','用户_854b79',0,NULL,1542696768,'0.0.0.0',1542696768,'0.0.0.0',1),(13,'13233333333','1a100d2c0dab19c4430e7d73762b3423','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','He22asdf',1,25,1542696859,'0.0.0.0',1542696859,'0.0.0.0',1),(14,'18368478010','96e79218965eb72c92a549dd5a330112','http://phx4bho4j.bkt.clouddn.com/20181115181037-5bed461d96b8d.png','用户_73bd7e',0,NULL,1542939826,'0.0.0.0',1542939855,'0.0.0.0',1);
/*!40000 ALTER TABLE `hgqb_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hgqb_view_log`
--

DROP TABLE IF EXISTS `hgqb_view_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hgqb_view_log` (
  `view_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `view_user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `view_object_id` int(11) DEFAULT NULL COMMENT '浏览对象id',
  `view_create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `view_type` tinyint(1) DEFAULT NULL COMMENT '浏览类型  1 产品 2攻略',
  PRIMARY KEY (`view_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hgqb_view_log`
--

LOCK TABLES `hgqb_view_log` WRITE;
/*!40000 ALTER TABLE `hgqb_view_log` DISABLE KEYS */;
INSERT INTO `hgqb_view_log` VALUES (2,1,3,1542102326,1);
/*!40000 ALTER TABLE `hgqb_view_log` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-11-26 14:56:04
