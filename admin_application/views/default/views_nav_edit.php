<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>编辑导航处理</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/style.css" />
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
<div class="definewidth m10">
    <ul class="nav nav-pills">
	    <li class="">
	    <a href="<?php echo site_url('nav/index') ;?>">导航列表</a>
	    </li>
	     <li class="active">
	    <a href="#">编辑导航</a>
	    
	    </li>
	  
    </ul>
</div>
<form action="<?php echo site_url("nav/edit");?>" method="post" class="definewidth m2"  name="myform" id="myform">
<input type="hidden" name="action" value="doedit">
<input type="hidden" name="id" value="<?php echo $info['id'];?>">
<input type="hidden" name="path" value="<?php echo $info['path'];?>">
<table class="table table-bordered table-hover m10">
    <tr>
        <td width="10%" class="tableleft">上级</td>
        <td>
            <select name="pid">
            <option value="0">一级菜单</option>
            <?php 
            	if($options){           		
            		foreach($options as $row){
						$selected = '';
						if($row['id'] == $info['pid']){
							$selected = "selected='selected'";
						}
            ?>         		
            <option value="<?php echo $row['id'] ?>" <?php echo $selected ;?> <?php if($row['pid'] == 0){echo 'style="background:lightgreen"';}?>><?php echo str_pad("",$row['deep']*3, "-",STR_PAD_RIGHT); ?><?php echo $row['name']; ?></option>
         <?php 
            }

            }         	
        ?> 
             </select>
        </td>
    </tr>
    <tr>
        <td class="tableleft">导航名称</td>
        <td><input type="text" name="name" value="<?php echo $info['name'];?>" id="name"  required="true" errMsg="请输入导航名称" tip="请输入导航名称"/></td>
    </tr>
    <tr>
        <td class="tableleft">排序</td>
        <td><input type="text" name="disorder" value="<?php echo $info['disorder'];?>" required="true" errMsg="请输入整数" tip="请输入整数" valType="int"/></td>
    </tr>
    <tr>
        <td class="tableleft">url地址</td>
        <td><input type="text" name="url" value="<?php echo $info['url'];?>" /></td>
    </tr>
	
    <tr>
        <td class="tableleft">状态</td>
        <td>
            <input type="radio" name="status" value="1" <?php if($info['status'] == 1 ){echo "checked";} ?>/> 启用
            <input type="radio" name="status" value="0" <?php if($info['status'] == 0 ){echo "checked";} ?>/> 禁用
        </td>
    </tr>
    <tr>
        <td class="tableleft">是否是本站地址</td>
        <td>
            <input type="radio" name="url_type" value="1" <?php if($info['url_type'] == 1 ){echo "checked";} ?>/> 是
            <input type="radio" name="url_type" value="0" <?php if($info['url_type'] == 0 ){echo "checked";} ?>/> 不是
        </td>
    </tr>	
    <tr>
        <td class="tableleft">导航是否收缩</td>
        <td>
            <input type="radio" name="collapsed" value="1" <?php if($info['collapsed'] == 1 ){echo "checked";} ?>/> 收缩
            <input type="radio" name="collapsed" value="0" <?php if($info['collapsed'] == 0 ){echo "checked";} ?>/> 不收缩
			<span style="color:red">注意此选项只是针对导航是二级导航的时候起作用</span>
        </td>
    </tr>		
    <tr>
        <td class="tableleft"></td>
        <td>
            <button type="submit" class="btn btn-primary" type="button" id="btnSave">保存</button> &nbsp;&nbsp;<button type="button" class="btn btn-success" name="backid" id="backid">返回列表</button>
        </td>
    </tr>
</table>
</form>
<div class="alert alert-warning alert-dismissable">
  <strong>Tips!</strong> 添加url的时候后面一定要加上/(主要是为了进行权限判断的)
</div>
</body>
</html>
<script>
$(function () {       
		$('#backid').click(function(){
				window.location.href="<?php echo site_url("nav/index");?>";
		 });
		$("#btnSave").click(function(){
			if($("#myform").Valid() == false || !$("#myform").Valid()){
				 return false
			}
		});
});

 
</script>
