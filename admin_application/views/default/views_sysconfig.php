<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>后台系统基本信息设置</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" />   
	<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/jquery-1.8.1.min.js"></script>
	

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
<?php 
	if(isset($group) && $group){
		foreach($group as $g_=>$v_){

?>
<a  class="btn <?php if(isset($gid) && $gid == $g_){echo "btn-primary";}?>" id="addnew" href="<?php echo site_url("sysconfig/index")."?gid={$g_}";?>"><?php echo $v_ ;?><span class="glyphicon glyphicon-plus"></span></a>
<?php 
	}
}	
?>  
<a id="addnew" class="btn" href="<?php echo site_url('sysconfig/index')."?action=get_data&gid=true";?>">
添加系统环境变量

</a>
</div>


<form action="<?php echo site_url("sysconfig/edit");?>" method="post" class="definewidth m2"  name="myform" id="myform">
<input type="hidden" name="gid" value="<?php echo isset($gid)?$gid:0;?>">
<table class="table table-bordered table-hover m10">
    <tr>
        <td class="tableleft">参数说明:</td>
        <td>参数值</td>
		 <td>变量名</td>
    </tr>
<?php 
	if(isset($list) && $list){
		foreach($list as $ll=>$vv){

?>
    <tr>
        <td class="tableleft"><?php echo $vv['info'];?>:</td>
        <td><?php echo $vv['text'];?></td>
		<td><?php echo $vv['varname'];?></td>
    </tr>
<?php 
	}
}	
?>


    <tr>
        <td class="tableleft" ></td>
        <td colspan="2">
            <button type="submit" class="btn btn-primary" type="button" id="btnSave">保存</button> &nbsp;&nbsp;
        </td>
    </tr>
</table>

<div class="alert alert-warning alert-dismissable">
  <strong>Tips!</strong> 每次添加修改或者删除会自动的创建缓存文件， 保存的路径是 <?php echo config_item("sysconfig_cache");?>sysconfig.inc.php
</div>
</form>

</body>
</html>
