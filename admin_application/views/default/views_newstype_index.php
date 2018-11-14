<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>新闻类别管理</title>
    <meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/style.css" />   
	<link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" />   
    <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/jquery-1.8.1.min.js"></script>
    <link href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" rel="stylesheet" type="text/css" />
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
  <!--
    类别名称：
    <input type="text" name="typename" id="typename" class="abc input-default" placeholder="" value="">&nbsp;&nbsp;  
	<select id="condition">
		<option value="1">模糊搜索</option>
		<option value="2">精确搜索</option>
	</select>

    <button type="submit" class="btn btn-primary" onclick="search_()">查询数据</button>
	-->&nbsp;&nbsp; <a  class="btn btn-success" id="addnew" href="javascript:void(0)" onclick="add_top()">新增顶级分类<span class="glyphicon glyphicon-plus"></span></a>
</div>
<table class="table table-bordered table-hover definewidth m10">
    <thead>
    <tr>
        <th>id</th>
        <th>类别名称</th>
		<th>seo标题</th>
		<th>关键字</th>
		<th>栏目描述</th>
		<th>栏目类型</th>
        <th>状态</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    </thead>
	<tbody id="result_">
		
	</tbody> 
</table>

<div id="page_string" class="form-inline definewidth m10">
  
</div>
<div class="alert alert-warning alert-dismissable">
<strong>Tips!</strong>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;暂不提供删除功能,如果你要删除,请联系数据库管理员，直接从数据库删除,如果分类不想要的话，直接把状态修改为不可用
</div>
</body>
</html>
<script>
var pid = 0 ; 
var page = 1 ; 
$(function () {	
	common_request();
});
function common_request(){
	var url="<?php echo site_url("newstype/index");?>?inajax=1";
	var data_ = {
		'page':page,
		'time':<?php echo time();?>,
		'action':'ajax_data',
		'typeid':$("#type").val(),
		'pid':pid,
		'typename':$("#typename").val(),
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
				BUI.Message.Alert("你没权限执行此操作" ,'error');
				return false ;
			}else if(msg.resultcode == 0 ){
				BUI.Message.Alert(msg.resultinfo.errmsg ,'error');
				return false ;				
			}else{
				if(list.length>0){
					for(var i in list){
						var onclick = '' ;					
				
						shtml+='<tr>';
						shtml+='<td>'+list[i].id+'</td>';
						shtml+='<td >'+list[i]['html']+''+list[i]['typename']+' '+list[i]['num']+'</td>';				
						shtml+='<td>'+list[i]['seotitle']+'</td>';
						shtml+='<td>'+list[i]['keywords']+'</td>';
						shtml+='<td>'+list[i]['description']+'</td>';
						shtml+='<td>'+list[i]['type']+'</td>';
						shtml+='<td>'+list[i]['status']+'</td>';
						shtml+='<td>'+list[i]['disorder']+'</td>';
						shtml+='<td><a href="javascript:void(0);" onclick="add_('+list[i].id+')" class="icon-plus" title="添加'+list[i]['typename']+'的子类"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onclick="edit('+list[i].id+','+list[i].pid+',\''+list[i].typename+'\')" class="icon-edit" title="编辑'+list[i]['typename']+'"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onclick="add_article('+list[i].id+')">添加文章</a></td>';
						shtml+='</tr>';
					}
					$("#result_").html(shtml);				
					$("#page_string").html(msg.resultinfo.obj);
				}else{
					$("#result_").html("");
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
	pid = 0 ;
	page = 1 ;
	common_request(1);	
}

</script>
<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/bui-min.js"></script>

<!-- script start-->
<script type="text/javascript">

function edit(id,mypid,name){
	var Overlay = BUI.Overlay
	var dialog = new Overlay.Dialog({
		title:"编辑新闻类别"+name+'数据',
		width:700,
		height:500,
		loader : {
		url : '<?php echo site_url("newstype/edit");?>',
		autoLoad : false, //不自动加载
		params : {"showpage":"1"},//附加的参数
		lazyLoad : true //不延迟加载	
		},
		mask:true,//遮罩层是否开启
		closeAction : 'destroy',
		success:function(){
			submit_edit(id,mypid); //编辑级别分类处理
			this.close();
		}
	});
	dialog.show();
	dialog.get('loader').load({"id":id});
}
function submit_edit(id,mypid){
	var data_ = $("#myform_edit").serializeArray();
	$.ajax({
		   type: "POST",
		   url: "<?php echo site_url('newstype/edit');?>?inajax=1" ,
		   data: data_,
		   cache:false,
		   dataType:"json",
		   async:false,
		   success: function(msg){
				if(msg.resultcode<0){
					BUI.Message.Alert('没有权限执行此操作','error');
					return false ;
				}else if(msg.resultcode == 0 ){
					BUI.Message.Alert(msg.resultinfo.errmsg,'error');
					return false ;
				}else{
						pid = mypid ;
						common_request();
				}
		   }
		  
		});		
	
}
//添加子类
//_typeid 分类ID
//_id 父级ID
function add_(_id){

	var Overlay = BUI.Overlay
	var dialog = new Overlay.Dialog({
		title:"添加新闻类别数据",
		width:700,
		height:400,
		loader : {
		url : '<?php echo site_url('newstype/add');?>',
		autoLoad : false, //不自动加载
		params : {"showpage":"1","pid":_id},//附加的参数
		lazyLoad : true //不延迟加载	
		},
		mask:true,//遮罩层是否开启
		closeAction : 'destroy',
		success:function(){
			submit_add(_id); //添加处理
			this.close();
		}
	});
	dialog.show();
	dialog.get('loader').load();
}
function submit_add(_id){
	var data_ = $("#myform_add").serializeArray();
	$.ajax({
		   type: "POST",
		   url: "<?php echo site_url('newstype/add');?>?inajax=1" ,
		   data: data_,
		   cache:false,
		   dataType:"json",
		   async:false,
		   success: function(msg){
				if(msg.resultcode<0){
					BUI.Message.Alert('没有权限执行此操作','error');
					return false ;
				}else if(msg.resultcode == 0 ){
					BUI.Message.Alert(msg.resultinfo.errmsg,'error');
					return false ;
				}else{
						pid = _id ;
						common_request();
				}
		   }
		  
		});		
}
//添加顶级分类
function add_top(){
	add_(0);
}
//
function jump_news(typeid){
	 top.topManager.openPage({
		 id : '#',
		 href  : '<?php echo site_url('news/index');?>?typeid='+typeid,
		 title : '新闻管理' ,
		 reload:true,
		// isClose:true
	});
	 top.topManager.closePage('86'); //关闭已经打开的
}
//添加文章
function add_article(typeid){
	 top.topManager.openPage({
		 id : '#',
		 href  : '<?php echo site_url('news/add');?>?typeid='+typeid,
		 title : '添加新闻' ,
		 reload:true,
		// isClose:true
	});
	 top.topManager.closePage('87'); //关闭已经打开的	
}
</script>
<!-- script end -->

