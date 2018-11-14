<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>后台操作日志</title>
    <meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/style.css" />   
	<link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" />   
    <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/jquery-1.8.1.min.js"></script>
   <link href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/bui-min.css" rel="stylesheet" type="text/css" />
   
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
	日志表:
	<select id="table" style="width:200px">
		<option value="">请选择表</option>
		<?php 
			if(isset($table) && $table){
				for($t = 0 ;$t<count($table);$t++){
	
		?>
		<option value="<?php echo $table[$t];?>"><?php echo $table[$t];?></option>
		<?php 
			}
		}	
		?>
	</select>	
    url：
    <input type="text" name="url" id="url"class="abc input-default" placeholder="" value="">&nbsp;&nbsp;  
	<select id="condition">
		<option value="1">模糊搜索</option>
		<option value="2">精确搜索</option>
	</select>
	
	操作者: <input type="text" name="username" id="username"class="abc input-default" placeholder="" value="">&nbsp;&nbsp;  
	状态:<select id="status">
		<option value="">请选择</option>
		<option value="1">成功</option>
		<option value="0">失败</option>
	</select>
    <button type="submit" class="btn btn-primary" onclick="common_request(1)">查询</button>&nbsp;&nbsp; 
</div>
<table class="table table-bordered table-hover definewidth m10">
    <thead>
    <tr>
        <th>日志id</th>
        <th>url地址</th>
		<th>操作者</th>
        <th>添加日期</th>
        <th>ip地址</th>
        <th >sql语句</th>
		<th>状态</th>
		<th>备注</th>
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
	$("#table option").eq(1).attr("selected","selected");
	common_request(1);
});
function common_request(page){
	var url="<?php echo site_url("log/index");?>?inajax=1";
	var data_ = {
		'page':page,
		'time':<?php echo time();?>,
		'action':'ajax_data',
		'username':$("#username").val(),
		'url':$("#url").val(),
		'condition':$("#condition").val(),
		'table':$("#table").val(),
		'status':$("#status").val()
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
			var message = msg.resultinfo.errmsg;
			if(msg.resultcode<0){
				alert("没有权限执行此操作");
				return false ;
			}else if(msg.resultcode == 0 ){
				var s = '<div class="alert alert-warning alert-dismissable"><strong>Tips!</strong>'+message+'</div> ' ;
				$("#result_").html(s);
				return false ;				
			}else{
				
				for(var i in list){
					shtml+='<tr>';
					shtml+='<td>'+list[i].log_id+'</td>';
					shtml+='<td>'+list[i]['log_url']+'</td>';
					shtml+='<td>'+list[i]['log_person']+'</td>';
					shtml+='<td>'+list[i]['log_time']+'</td>';
					shtml+='<td>'+list[i]['log_ip']+'</td>';
					shtml+='<td width="100px" ><a href="javascript:void(0)" onclick="previewSql('+list[i]['log_id']+')">'+list[i]['log_sql'].substring(0,10)+'</a><span id="preview_sql_'+list[i].log_id+'" style="display:none">'+list[i]['log_sql']+'</span></td>';					
					shtml+='<td>'+list[i]['log_status']+'</td>';
					shtml+='<td>'+list[i]['log_message']+'</td>';
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
//预览sql
function previewSql(id){
		var content = $("#preview_sql_"+id).html();
		var Overlay = BUI.Overlay
		var dialog = new Overlay.Dialog({
		title:'sql语句如下：',
		width:500,
		height:300,
		mask:true,
		buttons:[],
		bodyContent:'<p>'+content+'</p>'
		});
		dialog.show();	
}
</script>
<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/bui-min.js"></script>
