项目说明：
	此项目只是一个后台系统，并无前台，请根据自己的业务需要进行添加,运行环境php5.4

目录说明：
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
	


请把 data.sql文件导入数据库
config/config.inc.php 是数据库的配置文件

访问地址:域名/index.php 
默认管理员：wangjian
密码； wangjian


http://www.builive.com/demo/dialog.php#dialog/remote.php