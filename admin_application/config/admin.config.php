<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include __ROOT__."/include/config/common_config.php";  //加载公共的配置文件

/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| 后台cookie的周期
| 默认1个小时的时间
|
*/
$config['cookie_expire'] =  60*60 ; //

/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| 后台cookie的路径
| 
|
*/
$config['cookie_path'] = "/" ; //

/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| 后台cookie的域
| 
|
*/
$config['cookie_domain'] = "" ; //

/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| 后台加密的key
| 
|
*/
$config['s_key'] = "phpspeak_" ; //

/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| 角色缓存文件的路径
|
*/
$config['role_cache'] = __ROOT__."/".APPPATH."/cache/role_cache/" ; //备注要确保role_cache文件夹存在
/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| 系统环境的基本信息路径
|
*/
$config['sysconfig_cache'] = __ROOT__."/data/cache/sysconfig/" ; //备注要确保文件夹sysconfig存在
/*
| 没有权限的时候返回的一个code值 写小于0的值
*/
$config['no_permition'] = -8 ;
/*
| 开发者的邮箱地址
*/
$config['web_admin_email'] = "wangjian@phpspeak.com" ;

/*
| 网站基本信息组
*/
$config['web_group'] =array(
		1=>'站点设置',
		2=>'会员设置',
		3=>'性能设置',

);

/*
| 网站基本信息输入框类型配置
*/
$config['web_type'] =array(
	'string'=>'文本输入',
	'boolean'=>'boolean值',
	'textarea'=>'文本域',
	'number'=>'数字输入',
);
/*
| 不需要进行权限认证的控制器里面的方法（但是需要进行登录才能使用的）
| 注意每个控制器后面需要加上/
*/
$config['no_need_perm'] = array(
	'admin/index/' , 
	'sys_admin/edit_passwd/',
	'http://www.57sy.com'
) ;

/*
| 是否保存日志到数据库里面
| 默认是true
*/
$config['is_write_log_to_database'] = true ; 
/*
| 是否在后台登录的时候有验证码
| 默认是true
*/
$config['yzm_open'] = true ; 
/*
| 验证码图片保存的路径
*/
$config['yzm_path'] = __ROOT__.'/data/captcha/' ; 
/*
| 联动模型的 缓存路径
| 
*/
$config['category_model_cache'] = __ROOT__."/data/cache/category/" ; ; //

/*
| 联动模型数据 缓存路径
| 
*/
$config['category_modeldata_cache'] = __ROOT__."/data/cache/category/" ; ; //

/*
| 产品图片本地路径
| 
*/
$config['product_path'] = __ROOT__."/data/upload/product/" ; ; //

/*
| 广告图片访问路径
| 
*/
$config['v_product_path'] = "/data/upload/product/" ; ; //

/*
| 广告图片本地路径
| 
*/
$config['ad_path'] = __ROOT__."/data/upload/ad/" ; ; //

/*
| 广告图片访问路径
| 
*/
$config['v_ad_path'] = "/data/upload/ad/" ; ; //

/*
| 文章图片本地路径
| 
*/
$config['news_path'] = __ROOT__."/data/upload/news/" ; ; //

/*
| 广告图片访问路径
| 
*/
$config['v_news_path'] = "/data/upload/news/" ; ; //

/*
| 超级管理员 ， 【这个主要是为了操作一些危险操作的】
| 
*/
$config['super_admin'] =array("wangjian") ;

/*
| 用户的字段类型 配置
| 
*/
$config['field_type'] = array(
	array('type'=>'varchar','info'=>'单行文本(varchar)'),
	array('type'=>'char','info'=>'单行文本(char)'),
	array('type'=>'int','info'=>'整数类型'),
	array('type'=>'text','info'=>'多行文本 text类型'),
	
	array('type'=>'mediumtext','info'=>'HTML文本'),
	
	array('type'=>'float','info'=>'小数类型'),
	array('type'=>'datetime','info'=>'时间类型'),
	array('type'=>'enum','info'=>'enum 类型的数据'),
);



