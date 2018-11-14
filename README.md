# ci_admin

#### 项目介绍
此项目是一个  类资讯网站 管理系统，运行环境php5.4

#### 软件架构
软件架构说明


#### 安装教程

1. 把 ci_admin.sql文件导入数据库
####
config/config.inc.php 是数据库的配置文件，修改为（host=localhost,username=root,pwd=root）
####
2.账号密码 
####
默认管理员：wangjian
密码； wangjian
####
3. 访问地址:localhost/ci_admin/index.php
####
4. php版本要求5.4
####
5. 目录说明
####
        admin_application  后台应用文件夹
	config 数据库的配置文件
	data
		cache 缓存文件夹
			cache/category 模型缓存#css
			cache/sysconfig 系统环境变量缓存文件夹
			
	include 一些常用的文件
		function  方法文件夹，前后台公用
			common_function.php  前台和后台的公共方法
		
		config   
			common_config.php 前台和后台的公用配置文件
		
	system CI的系统文件夹	
	
	index.php 后台入口文件

