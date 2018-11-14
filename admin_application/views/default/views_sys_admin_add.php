<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>系统用户添加</title>
    <meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/style.css" />   
	<link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" />   
    <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/jquery-1.8.1.min.js"></script>
	<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Js/validate/validator.js"></script>   
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
<div class="form-inline definewidth m20" >
   <a  class="btn btn-primary" id="addnew" href="<?php echo site_url("sys_admin/index");?>">系统用户列表</a>
</div>
<form action="<?php echo site_url("sys_admin/add");?>" method="post" class="definewidth m20" id="myform">
<input type="hidden" value="doadd" name="action">
<table class="table table-bordered table-hover definewidth m10">
    <tr>
        <td width="10%" class="tableleft">登录名</td>
        <td><input type="text" name="username" placeholder="User name" required="true"/></td>
    </tr>
    <tr>
        <td class="tableleft">密码</td>
        <td><input type="text" name="password" placeholder="password " required="true"/></td>
    </tr>
    <tr>
        <td class="tableleft">状态</td>
        <td>
            <input type="radio" name="status" value="1" checked/> 启用
           <input type="radio" name="status" value="0"/> 禁用
        </td>
    </tr>
    <tr>
        <td class="tableleft">是否是超级管理员</td>
        <td>
            <input type="radio" name="super_admin" value="1" onclick="super_admin_(1);"/> 是
           <input type="radio" name="super_admin" value="0" onclick="super_admin_(0);" checked/> 不是
		  
        </td>
    </tr>	    
    <tr id="group_">
        <td class="tableleft">用户组</td>
        <td>
			<select name="gid" >
				<?php 
					if(isset($list) && $list){
						foreach($list as $k_=>$v_){
					
				?>
				<option value="<?php echo $v_['id'];?>"><?php echo $v_['rolename'];?></option>
				<?php 
						}	
					}
				?>
			</select>
		</td>
    </tr>

    <tr>
        <td class="tableleft"></td>
        <td>
            <button type="submit" class="btn btn-primary" type="button" id="btnSave">保存</button> &nbsp;&nbsp;
        </td>
    </tr>
</table>
</form>
</body>
</html>
<script>
$(function () {       
		$("#btnSave").click(function(){
				if($("#myform").Valid() == false || !$("#myform").Valid()) {
					return false ;
				}
		});
});
function super_admin_(type){
	if(type == 1 ){
		$("#group_").hide();
	}else{
		$("#group_").show();
	}
}
</script>