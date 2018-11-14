<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>后台导航添加</title>
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

<div class="form-inline definewidth m20" >
   <a  class="btn btn-primary" id="addnew" href="<?php echo site_url("nav/index");?>">导航列表</a>
</div>
<form action="<?php echo site_url("nav/add");?>" method="post" class="definewidth m2"  name="myform" id="myform">
<input type="hidden" name="action" value="doadd">
<table class="table table-bordered table-hover m10">
    <tr>
        <td width="10%" class="tableleft">上级</td>
        <td>
            <select name="pid">
            <option value="0">一级菜单</option>
            <?php 
            	if($options){           		
            		foreach($options as $row){
            	
            ?>         		
            <option value="<?php echo $row['id'] ?>" <?php if($row['pid'] == 0){echo 'style="background:lightgreen"';}?>   <?php if($pid == $row['id']){echo "selected='selected'";}?>><?php echo str_pad("",$row['deep']*3, "-",STR_PAD_RIGHT); ?><?php echo $row['name']; ?></option>
         <?php 
            }

            }         	
        ?> 
             </select>
        </td>
    </tr>
    <tr>
        <td class="tableleft">导航名称</td>
        <td><input type="text" name="name" id="name" required="true" errMsg="请输入导航名称" tip="请输入导航名称"/></td>
    </tr>
    <tr>
        <td class="tableleft">排序</td>
        <td><input type="text" name="disorder" required="true" errMsg="请输入整数" tip="请输入整数" valType="int"/></td>
    </tr>
    <tr>
        <td class="tableleft">url地址</td>
        <td><input type="text" name="url"/><span class="tip">温馨提示:此处的url地址格式是:控制器/方法/ 后面不要忘记加/</span></td>
    </tr>
   
  
    <tr>
        <td class="tableleft">状态</td>
        <td>
            <input type="radio" name="status" value="1" checked/> 启用
            <input type="radio" name="status" value="0"/> 禁用
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
		$('#backid').click(function(){
				window.location.href="<?php echo site_url("nav/index");?>";
		 });
		$("#btnSave").click(function(){
			if($("#myform").Valid() == false || !$("#myform").Valid()) {
				return false ;
			}
		});
});

 
</script>
