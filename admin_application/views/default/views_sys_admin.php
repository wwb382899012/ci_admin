<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>系统用户</title>
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
    用户名称：
    <input type="text" name="username" id="username"class="abc input-default" placeholder="" value="">&nbsp;&nbsp;  
	<select id="condition">
		<option value="1">模糊搜索</option>
		<option value="2">精确搜索</option>
	</select>
    <button type="submit" class="btn btn-primary" onclick="common_request(1)">查询</button>&nbsp;&nbsp; <a  class="btn btn-success" id="addnew" href="<?php echo site_url("sys_admin/add");?>">新增用户<span class="glyphicon glyphicon-plus"></span></a>
</div>
<table class="table table-bordered table-hover definewidth m10">
    <thead>
    <tr>
        <th>用户id</th>
        <th>用户名称</th>
		<th>所属组</th>
        <th>添加日期</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    </thead>
	<tbody id="result_">
	</tbody> 
</table>
<div id="page_string" class="form-inline definewidth m10">
  
</div>
</body>
</html>
<script>
$(function () {
	common_request(1);
});
function common_request(page){
	var url="<?php echo site_url("sys_admin/index");?>?inajax=1";
	var data_ = {
		'page':page,
		'time':<?php echo time();?>,
		'action':'ajax_data',
		'username':$("#username").val(),
		'condition':$("#condition").val()
	} ;
	$.ajax({
		   type: "POST",
		   url: url ,
		   data: data_,
		   cache:false,
		   dataType:"json",
		 //  async:false,
		   success: function(msg){
			var shtml = '' ;
			var list = msg.resultinfo.list;
			if(msg.resultcode<0){
				alert("没有权限执行此操作");
				return false ;
			}else if(msg.resultcode == 0 ){
				alert("服务器繁忙");
				return false ;				
			}else{
				
				for(var i in list){
					shtml+='<tr>';
					shtml+='<td>'+list[i].id+'</td>';
					shtml+='<td>'+list[i]['username']+'</td>';
					shtml+='<td>'+list[i]['rolename']+'</td>';
					shtml+='<td>'+list[i]['addtime']+'</td>';
					shtml+='<td>'+list[i]['status']+'</td>';
					shtml+='<td><a href="<?php echo site_url('sys_admin/edit');?>?id='+list[i].id+'" class="icon-edit" title="编辑用户'+list[i]['username']+'"></a></td>';
					shtml+='</tr>';
				}
				$("#result_").html(shtml);
				
				$("#page_string").html(msg.resultinfo.obj);
			}
		   },
		   beforeSend:function(){
			  $("#result_").html('<font color="red"><img src="<?php echo base_url();?>/<?php echo APPPATH?>/views/static/Images/progressbar_microsoft.gif"></font>');
		   },
		   error:function(){
			   
		   }
		  
		});		
	

}
function ajax_data(page){
	common_request(page);	
}
</script>
