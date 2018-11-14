<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>广告管理</title>
    <meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/style.css" />   
	<link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" />   
	<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Js/DatePicker/WdatePicker.js"></script>
    <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/jquery-1.8.1.min.js"></script>
    <link href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" rel="stylesheet" type="text/css" />
   <link href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/bui-min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Js/admin.js"></script>
     <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/bui-min.js"></script>
      <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Js/admin.js"></script>
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
<table cellpadding="10">
	<tr>
		<td>
			广告名称：<input type="text" name="" id="name">
		    广告类别：
			  <select id="ad_type">
				<option value="">请选择</option>
				<?php 
					if(isset($list) && $list){
						foreach($list as $k=>$v){
						
				?>
				<option value="<?php echo isset($v['id'])?$v['id']:'' ;?>"> <?php echo isset($v['typename'])?$v['typename']:'' ;?></option>
				<?php 
					}
				}	
				?>
		 	</select>  
			类型：
			 <select id="type">
				<option value="">请选择</option>
				<option value="0">图片广告</option>
				<option value="1">文字广告</option>
		 	</select>
		 	&nbsp;&nbsp;&nbsp;
		 	<a href="javascript:void(0)" onclick="more_search() ;">更多条件搜索</a> 	
		 	<button type="submit" class="btn btn-primary" onclick="search_()">查询数据</button>&nbsp;&nbsp; <a  class="btn btn-success" id="addnew" href="javascript:void(0)" onclick="add_ad()">添加广告<span class="glyphicon glyphicon-plus"></span></a>				
		</td>
	</tr>
	<tr id="more_search" style="display:none">
		<td>
	状态：
	 <select id="status">
		<option value="">请选择</option>
		<option value="1">开启</option>
		<option value="0">关闭</option>
 	</select> 	
 	开始日期:
 	<input id="beginDate" class="Wdate" type="text" readonly="" onclick="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'%y-%M-%d',isShowClear:true,readOnly:true})" value="" placeholder="" name="">
   ~~~ 结束时间：
<input id="enddate" class="Wdate" type="text" readonly="" onclick="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:'%y-%M-%d',isShowClear:true,readOnly:true})" value="" placeholder="" name="">
    
		</td>
	</tr>
</table>

 	
</div>
<table class="table table-bordered table-hover definewidth m10">
    <thead>
    <tr>
        <th></th>
        <th>广告名称</th>  
       <th>图片</th>	
       <th>文字描述</th>  
       <th>类别</th>  
	   <th>添加日期</th>  
	   <th>开始日期</th>
	   <th>结束日期</th>
	   <th>状态</th>  
       <th>操作</th>  
    </tr>
    </thead>
	<tbody id="result_">
	</tbody> 
	<tr>
		<td colspan="11">
		全选<input type="checkbox" id="selAll" onclick="selectAll()">
		反选:<input type="checkbox" id="inverse" onclick="inverse();">
			<button class="button button-small" type="button" onclick="del()">
			<i class="icon-remove"></i>
			删除
			</button>
			<button class="button button-small" type="button" onclick="change_status(0)">
			<i class="icon-off"></i>
			设为禁用
			</button>
			<button class="button button-small" type="button" onclick="change_status(1)">
			<i class="icon-eye-open"></i>
			设为开启
			</button>
		</td>
	</tr>
</table>
<div id="page_string" class="form-inline definewidth m10">
  
</div>

</body>
</html>
<script>
var page = 1 ; 
$(function () {	
	var typeid = parseInt(getQueryStringValue("typeid"));
	if(typeid >0 ){
		$("#type").val(typeid) ;
	}
	common_request();
});
function common_request(){
	var url="<?php echo site_url("ad/index");?>?inajax=1";
	var data_ = {
		'page':page,
		'time':<?php echo time();?>,
		'action':'ajax_data',
		'type':$("#type").val(),
		'ad_type':$("#ad_type").val(),
		'name':$("#name").val(),
		'status':$("#status").val(),
		'beginDate':$("#beginDate").val(),
		'enddate':$("#enddate").val()
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
				BUI.Message.Alert("你没权限执行此操作" ,'error');
				return false ;
			}else if(msg.resultcode == 0 ){
				BUI.Message.Alert(msg.resultinfo.errmsg ,'error');
				return false ;				
			}else{
				if(list.length>0){
					for(var i in list){
						
						shtml+='<tr>';
						shtml+='<td width="20px"><input type="checkbox" name="checkAll[]" onclick="setSelectAll();" value="'+list[i]['id']+'"></td>';
						shtml+='<td ><a href="#">'+list[i]['name']+'</a></td>';				
						shtml+='<td>'+list[i]['pic']+'</td>';
						shtml+='<td>'+list[i]['words']+'</td>';
						shtml+='<td>'+list[i]['type']+'</td>';
						shtml+='<td>'+list[i]['addtime']+'</td>';
						shtml+='<td>'+list[i]['begin_date']+'</td>';
						shtml+='<td>'+list[i]['end_date']+'</td>';
						shtml+='<td>'+list[i]['status']+'</td>';
						shtml+='<td><a href="<?php echo site_url("ad/edit") ; ?>?id='+list[i].id+'" class="icon-edit" title="编辑广告'+list[i]['name']+'"></a>&nbsp;&nbsp;<!--<a href="<?php echo site_url("news/index") ; ?>?id='+list[i].id+'&action=preview">查看</a>--></td>';
						shtml+='</tr>';
					}
					$("#result_").html(shtml);				
					$("#page_string").html(msg.resultinfo.obj);
				}else{
					$("#result_").html("暂无数据");	
					$("#page_string").html("");	
				}

			}
		   },
		   beforeSend:function(){
			  $("#result_").html('<font color="red"><img src="<?php echo base_url();?>/<?php echo APPPATH?>/views/static/Images/progressbar_microsoft.gif"></font>');
		   },
		   error:function(){
			   BUI.Message.Alert('服务器繁忙请稍后' ,'error');
		   }
		  
		});		
	

}
function ajax_data(p){
	page = p ;
	common_request();	
}

	

//查询
function search_(){
	page = 1 ;
	common_request(1);	
}

function del(){
	var selectCount = 0;
	var data = [] ; 	
	var o = select_data() ;
	selectCount = o.selectCount ; 
	data = o.data ;
	if(selectCount == 0 ){
		BUI.Message.Alert('请选择进行删除','error');
		return false ;
	}
	BUI.Message.Confirm('此操作不可恢复,是否确定此操作',function(){
		$.ajax({
			   type: "POST",
			   url: "<?php echo site_url('ad/del');?>" ,
			   data: {"ids":data},
			   cache:false,
			   dataType:"json",
			 //  async:false,
			   success: function(msg){
				   if(msg.resultcode<0){
					   BUI.Message.Alert('没有权限执行此操作','error');
					   return false ; 
					}else if(msg.resultcode == 0 ){
						BUI.Message.Alert(msg.resultinfo.errmsg ,'error');
						common_request();
						return false ;				
					}else{
						common_request();
					}
			   },
			   beforeSend:function(){
				  $("#result_").html('<font color="red"><img src="<?php echo base_url();?>/<?php echo APPPATH?>/views/static/Images/progressbar_microsoft.gif"></font>');
			   },
			   error:function(){
				   BUI.Message.Alert('服务器繁忙请稍后','error');
			   }
			  
			});		
	},'question');

}
function select_data(){
	var obj=document.getElementsByName("checkAll[]");
	var count = obj.length;
	var selectCount = 0;
	var data = [] ; 
	for(var i = 0; i < count; i++)
	{
		if(obj[i].checked == true)
		{
			selectCount++;
			data.push(obj[i].value);
		}
	}
	var o = {
		'selectCount':selectCount , 
		'data':data
	} ;
	return o ;
}
//设置状态
function change_status(status){
	var selectCount = 0;
	var data = [] ; 	
	var o = select_data() ;
	selectCount = o.selectCount ; 
	data = o.data ;
	if(selectCount == 0 ){
		BUI.Message.Alert('请选择进行修改状态','error');
		return false ;
	}
	$.ajax({
			   type: "POST",
			   url: "<?php echo site_url('ad/edit');?>" ,
			   data: {"ids":data,"action":'dostatus',"status":status},
			   cache:false,
			   dataType:"json",
			 //  async:false,
			   success: function(msg){
				   if(msg.resultcode<0){
					   BUI.Message.Alert('没有权限执行此操作','error');
					   return false ; 
					}else if(msg.resultcode == 0 ){
						BUI.Message.Alert(msg.resultinfo.errmsg ,'error');
						common_request();
						return false ;				
					}else{
						common_request();
					}
			   },
			   beforeSend:function(){
				  $("#result_").html('<font color="red"><img src="<?php echo base_url();?>/<?php echo APPPATH?>/views/static/Images/progressbar_microsoft.gif"></font>');
			   },
			   error:function(){
				   BUI.Message.Alert('服务器繁忙请稍后','error');
			   }
			  
	});		
	
}
function more_search(){
	$("#more_search").toggle();
}
//添加新闻
function add_ad(){
	 top.topManager.openPage({
		 id : '#',
		 href  : '<?php echo site_url('ad/add');?> ' ,
		 title : '添加广告' ,
		 reload:true,
		// isClose:true
	});
	 top.topManager.closePage('101'); //关闭已经打开的	
}
</script>



