<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>系统用户编辑_<?php echo $info['username']; ?></title>
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
<form action="<?php echo site_url("sys_admin/edit");?>" method="post" class="definewidth m20" id="myform">
<input type="hidden" value="doedit" name="action">
<input type="hidden" value="<?php echo $info['id'];?>" name="id">
<table class="table table-bordered table-hover definewidth m10">
    <tr>
        <td width="10%" class="tableleft">登录名</td>
        <td><input type="text" name="username" value="<?php echo $info['username']; ?>" placeholder=""  required="true"/></td>
    </tr>
    <tr>
        <td class="tableleft">密码</td>
        <td><input type="text" name="password" placeholder="" value=""/>如果此处是空,将不进行修改密码</td>
    </tr>
    <tr>
        <td class="tableleft">状态</td>
        <td>
            <input type="radio" name="status" value="1"  <?php if($info['status'] == 1 ){echo "checked";}?>/> 启用
           <input type="radio" name="status" value="0" <?php if($info['status'] == 0 ){echo "checked";}?>/> 禁用
        </td>
    </tr>
    <tr>
        <td class="tableleft">是否是超级管理员</td>
        <td>
            <input type="radio" name="super_admin" value="1" <?php if($info['super_admin'] == 1 ){echo "checked";}?> onclick="s_(1)"/> 是
           <input type="radio" name="super_admin" value="0" <?php if($info['super_admin'] == 0 ){echo "checked";}?> onclick="s_(0)"/> 不是
		  如果是超级管理员,下面就不要进行选择
        </td>
    </tr>	
	<tbody id="s_">
    <tr>
        <td class="tableleft">用户组</td>
        <td>
			<select name="gid">
				<?php 
					if(isset($list) && $list){
						foreach($list as $k_=>$v_){
					
				?>
				<option value="<?php echo $v_['id'];?>" <?php if($info['gid'] == $v_['id']){echo "selected='selected'";}?>><?php echo $v_['rolename'];?></option>
				<?php 
						}	
					}
				?>
			</select>
		</td>
    </tr>
    <tr>
        <td class="tableleft">用户特殊权限</td>
        <td>
		温馨提示:<font color="red">请双击下面的进行 ，点击右边的进行删除 只能选择3级和四级功能</font>
		<br>
            <select  size="1" multiple style="width:200px;height:250px;" id="select1">
            <?php 
            	if($options){           		
            		foreach($options as $row){
            	
            ?>         		
            <option value="<?php echo isset( $row['url'] )?$row['url']:''; ?>" <?php if($row['pid'] == 0){echo 'style="background:lightgreen"';}?> ><?php echo str_pad("",$row['deep']*3, "-",STR_PAD_RIGHT); ?><?php echo $row['name']; ?></option>
         <?php 
            }

            }         	
        ?> 
        </select>
		
		已经选择的：
		<span id="choose">
			<?php 

				if(isset($perm_array_exists) && $perm_array_exists){
					foreach($perm_array_exists as $p_=>$v_ ){
						echo "<span><font color='red'>".$v_."</font><input type='checkbox' name='p[]' value='{$p_}' checked='checked' onclick=\"del_o(this)\"></span>";
					}
				}
			?>
		</span>
		
        </td>
    </tr>
	</tbody>	
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
<?php
$perms_ = '[' ;
if(isset($perm_array_exists) && $perm_array_exists){
	foreach($perm_array_exists as $k1=>$v1){
		$perms_.='\''.$k1.'\',';
	}
	$perms_ = rtrim($perms_,",");
}
$perms_.=']';
?>
var exists_perms = <?php echo $perms_ ;?> ;
function s_(type){
	if(type == 1 ){
		//隐藏
		$("#s_").hide();
	}else{
		$("#s_").show();
	}
}
$(function(){
	$("#btnSave").click(function(){
			if($("#myform").Valid() == false || !$("#myform").Valid()) {
				return false ;
			}
	});
	var super_admin = $('input:radio[name="super_admin"]:checked').val(); 
	s_(super_admin);
});
$('#select1').dblclick(function(){ //绑定双击事件
		var value = $("option:selected",this).val(); //获取选中的值
		var text = $("option:selected",this).text() ;
		text = text.replace(/-/g,"");
		if(value && !in_array(value,exists_perms)){
			$("#choose").append("<span><font color='green' >"+text+"</font><input type='checkbox' value='"+value+"' name='p[]' checked='true' onclick='del_o(this)'></span>&nbsp;");
			exists_perms.push(value);
			
		}
});

//判断元素是不是在数组里面
function in_array(needle, haystack) {
	if(typeof needle == 'string' || typeof needle == 'number') {
		for(var i in haystack) {
			if(haystack[i] == needle) {
					return true;
			}
		}
	}
	return false;
}
//删除checkbox
function del_o(o){
	o = $(o);//转化为jquery对象
	if(!o.is(":checked")){
		o.parent().remove();
	}
}
</script>
