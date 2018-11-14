<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>main</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" />   

	

    <style type="text/css">
        body {
            padding-bottom: 40px;
        }
        .sidebar-nav {
            padding: 9px 0;
        }

        @media (max-width: 980px) {
            /* Enable use of floated navbar text */
            .navbar-text.pull-right {
                float: none;
                padding-left: 5px;
                padding-right: 5px;
            }
        }


    </style>
</head>
<body>
<table class="table table-bordered table-hover m10">
    <tr>
        <td class="tableleft" ><b>后台信息</b>:</td>
        <td>值</td>		
    </tr>
    <tr>
        <td class="tableleft">所在组:</td>
        <td><?php echo isset($group_name)?"<font color='red'>".$group_name."</font>":''; ?></td>	
    </tr>	
    <tr>
        <td class="tableleft">角色缓存文件的路径:</td>
        <td><?php echo config_item("role_cache");?></td>	
    </tr>
    <tr>
        <td class="tableleft">角色缓存文件的路径权限:</td>
        <td><?php echo is_really_writable(config_item("role_cache"))?"可写":'<font color="red">不可写</font>';?></td>	
    </tr>    
    <tr>
        <td class="tableleft">系统环境的基本信息路径:</td>
        <td><?php echo config_item("sysconfig_cache");?></td>
		
    </tr>
    <tr>
        <td class="tableleft">系统环境的基本信息路径权限:</td>
        <td><?php echo is_really_writable(config_item("sysconfig_cache"))?"可写":'<font color="red">不可写</font>';?></td>	
    </tr>      
    <tr>
        <td class="tableleft" >没权限的返回值（必须小于0）:</td>
        <td><?php echo config_item("no_permition");?></td>
		
    </tr>
    <tr>
        <td class="tableleft">开发者的邮箱地址:</td>
        <td><?php echo config_item("web_admin_email");?></td>
		
    </tr>
    <tr>
        <td class="tableleft">是否保存日志到数据库里面:</td>
        <td><?php echo config_item("is_write_log_to_database")?"是":"<font color='red'>不是</font>";?></td>
		
    </tr>	
    <tr>
        <td class="tableleft">网站基本信息组:</td>
        <td><?php echo config_item("web_group")?join(",",config_item("web_group")):'';?></td>
		
    </tr>	
    <tr>
        <td class="tableleft">网站基本信息输入框类型配置:</td>
        <td><?php echo config_item("web_type")?join(",",config_item("web_type")):'';?></td>
		
    </tr>	
    <tr>
        <td class="tableleft">不需要进行权限认证的控制器里面的方法（但是需要进行登录才能使用的）:</td>
        <td><?php echo config_item("no_need_perm")?join(",",config_item("no_need_perm")):'';?></td>	
    </tr>			
    <tr>
        <td class="tableleft">是否在后台登录的时候有验证码:</td>
        <td><?php echo config_item("yzm_open")?"是":'<font color="red">否</font>';?></td>	
    </tr>
    <!-- <tr>
        <td class="tableleft">验证码保存的路径:</td>
        <td><?php echo config_item("yzm_path");?></td>	
    </tr>	
    <tr>
        <td class="tableleft">验证码路径权限:</td>
        <td><?php echo is_really_writable(config_item("yzm_path"))?"可写":'<font color="red">不可写</font>';?></td>	
    </tr>	
    -->
     <tr>
        <td class="tableleft">联动模型数据缓存路径:</td>
        <td><?php echo config_item("category_model_cache");?></td>	
    </tr>	
    <tr>
        <td class="tableleft">验证码路径权限:</td>
        <td><?php echo is_really_writable(config_item("category_model_cache"))?"可写":'<font color="red">不可写</font>';?></td>	
    </tr>  
</table>
<table class="table table-bordered table-hover m10">
    <tr>
        <td class="tableleft"><b>系统基本信息</b>:</td>
       
    </tr>
    <tr>
        <td class="tableleft">操作系统:<b><?php echo  PHP_OS; ?></b></td>      
    </tr>   
    <tr>
        <td class="tableleft">运行环境:<b><?php echo  isset($_SERVER['SERVER_SOFTWARE'])?$_SERVER['SERVER_SOFTWARE']:''; ?></b></td>      
    </tr>   
    <tr>
        <td class="tableleft">PHP运行方式:<b><?php echo  php_sapi_name(); ?></b></td>      
    </tr>   
    <tr>
        <td class="tableleft">上传附件限制:<b><?php echo  ini_get("post_max_size"); ?></b></td>      
    </tr>    
    <tr>
        <td class="tableleft">PHP版本:<b><?php echo   PHP_VERSION; ?></b></td>      
    </tr> 
    <tr>
        <td class="tableleft">服务器的IP:<b><?php echo isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:''  ; ?></b></td>      
    </tr>      
    <tr>
        <td class="tableleft">服务器的域名:<b><?php echo  isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:''; ?></b></td>      
    </tr>                     
</table>
<div class="alert alert-warning alert-dismissable">
<strong>Tips!</strong>
此参数表示的是系统的基本参数设置
</div>
</body>
</html>
