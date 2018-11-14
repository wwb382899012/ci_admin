<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>角色编辑__<?php echo $info['rolename'];?></title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/style.css" />
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
   <a  class="btn btn-primary" id="addnew" href="<?php echo site_url("role/index");?>">角色管理</a>
</div>
<form action="<?php echo site_url("role/edit");?>" method="post" name="">
<input type="hidden" name="action" value="doedit">
<input type="hidden" name="id" value="<?php echo $info['id'];?>">
<table class="table table-bordered table-hover definewidth m10">
    <thead>
    <tr>
        <th>权限名称如下</th>
        <th>权限地址</th>      
    </tr>
    </thead>
	<?php 
		if(isset($list) && $list){
			$perm_array = array();
			$perm_array = unserialize($info['perm']);
			$perm_array = empty($perm_array)?array():$perm_array;
			foreach($list as $k=>$v){
			
	?>
	     <tr>
            <td colspan="" style="font-weight:bold"><?php echo $v['name'];?><input type="checkbox" name="role[]" value="<?php echo $v['url'];?>" <?php echo in_array($v['url'],$perm_array)?"checked":'';?>></td>			
			<td>无需</td>			       
        </tr>
		<?php 
			if(isset($v['childs']) && $v['childs']){
				foreach($v['childs'] as $child_key=>$child_val){
				
		?>
		<tr>
            <td colspan=""><?php echo str_repeat("&nbsp;",4); ?><?php echo $child_val['name'];?><input type="checkbox" name="role[]" value="<?php echo $child_val['url'];?>" <?php echo in_array($child_val['url'],$perm_array)?"checked":'';?>></td>
          
			<td>无需</td>
			
       </tr>
		<!--第三层关系开始-->
		<?php 
			if(isset($child_val['childs']) && $child_val['childs']){
				foreach($child_val['childs'] as $t_key=>$t_val){
				
		?>
		<tr>
                <td><?php echo str_repeat("&nbsp;",10); ?><?php echo $t_val['name'];?><input type="checkbox" name="role[]" value="<?php echo $t_val['url'];?>" <?php echo in_array($t_val['url'],$perm_array)?"checked":'';?>></td>
             
                <td><?php echo $t_val['url'];?></td>
			
        </tr>
		<!--第四层关系开始-->
		<?php
			if(isset($t_val['childs']) && $t_val['childs']){
				foreach($t_val['childs'] as $f_key=>$f_val){
	
		?>
		<tr>
                <td><?php echo str_repeat("&nbsp;",18); ?><?php echo $f_val['name'];?><input type="checkbox" name="role[]" value="<?php echo $f_val['url'];?>" <?php echo in_array($f_val['url'],$perm_array)?"checked":'';?>></td>
                
                <td><?php echo $f_val['url'];?></td>
               
        </tr>		
		<?php 
			}
		}	
		?>
		<!--第四层关系结束-->
		<?php 
			}
		}	
		?>
		<!--第三层关系结束-->
		
	   <?php 
			}
		}	
	   ?>
	   <?php 
			}
		}	
	   ?>
	   <tr colspan="2">
		<td>
			角色名称:<input class="abc input-default" type="text" name="rolename" value="<?php echo $info['rolename'];?>">
		</td>
	   </tr>	   
	   <tr colspan="2">
		<td>
		状态:<input type="radio" checked="" value="1" name="status" <?php if($info['status'] == 1 ){echo "checked";}?>>
		启用
		<input type="radio" value="0" name="status" <?php if($info['status'] == 0 ){echo "checked";}?>>
		禁用 
		</td>
	   </tr>
	   <tr colspan="2">
		<td>
		<input type="submit" value="提&nbsp;交" class="btn btn-primary">
		</td>
	   </tr>
	   </table>
</form>	   
</body>
</html>
