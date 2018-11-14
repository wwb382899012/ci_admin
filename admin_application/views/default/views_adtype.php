<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>广告类型</title>
    <meta charset="UTF-8">
   	<link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/style.css" />   
	<link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" />   
    <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/jquery-1.8.1.min.js"></script>
    <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Js/admin.js"></script>
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
   <a  class="btn btn-primary" id="addnew" href="<?php echo site_url("adtype/add");?>">新增广告类别<span class="glyphicon glyphicon-plus"></span></a>
</div>
<table class="table table-bordered table-hover definewidth m10">
    <thead>
    <tr>
        <th>typeid</th>
        <th>广告类别名称</th>       
		<th>添加日期</th>
		<th>修改日期</th>
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
  <div id="page_string" class="form-inline definewidth m1" style="float:right ; text-align:right ; margin:-4px">
  
  </div>


</body>
</html>



<script>
$(function () {

	common_request(1);
});
function common_request(page){
	var url="<?php echo site_url("adtype/index");?>?inajax=1";
	var data_ = {
		'page':page,
		'time':<?php echo time();?>,
		'action':'ajax_data'
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
				BUI.Message.Alert("没有权限执行此操作",'error');
				return false ;
			}else if(msg.resultcode == 0 ){
				BUI.Message.Alert("服务器繁忙",'error');
				return false ;				
			}else{
				
				for(var i in list){
					shtml+='<tr>';
					shtml+='<td><input type="checkbox" name="checkAll[]" onclick="setSelectAll();" value="'+list[i]['id']+'">'+list[i].id+'</td>';
					shtml+='<td>'+list[i]['typename']+'</td>';				
					shtml+='<td>'+list[i]['addtime']+'</td>';
					shtml+='<td>'+list[i]['updatetime']+'</td>';
					shtml+='<td>'+list[i]['status']+'</td>';
					shtml+='<td><a href="<?php echo site_url('adtype/edit');?>?id='+list[i].id+'" class="icon-edit" title="编辑广告类别'+list[i]['typename']+'"></a>&nbsp;&nbsp;</td>';
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
			   BUI.Message.Alert("服务器繁忙",'error');
		   }
		  
		});		
	

}
function ajax_data(page){
	common_request(page);	
}
//
function preview_user(id,name){
	var Overlay = BUI.Overlay
	var dialog = new Overlay.Dialog({
		title:"预览<font color='red'>"+name+"</font>下的后台用户",
		width:700,
		height:300,
		loader : {
		url : '<?php echo site_url("role/index");?>',
		autoLoad : false, //不自动加载
		params : {"action":"preview_user","id":id},//附加的参数
		lazyLoad : true //不延迟加载	
		},
		mask:true,//遮罩层是否开启
		closeAction : 'destroy',
		buttons:[
			/*{
					text:'自定义',
					elCls : 'button button-primary',
					handler : function(){
					//do some thing
					this.close();
					}
					},{
					text:'关闭',
					elCls : 'button',
					handler : function(){
					this.close();
					}
			}*/
		]
	});
	dialog.show();
	dialog.get('loader').load();
}
//删除角色
function del_role(roleid,o){
	BUI.Message.Confirm('确认要删除此数据吗？删除之后无法进行恢复请慎重操作',function(){
		$.ajax({
		   type: "POST",
		   url: "<?php echo site_url("role/del");?>" ,
		   data: {"id":roleid,"inajax":1},
		   cache:false,
		   dataType:"json",
		 //  async:false,
		   success: function(msg){
			var shtml = '' ;
			if(msg.resultcode<0){
				BUI.Message.Alert("没有权限执行此操作",'error');
				return false ;
			}else if(msg.resultcode == 0 ){
				BUI.Message.Alert("服务器繁忙",'error');
				return false ;				
			}else{				
				//成功
				common_request(1);
			}
		   },
		   beforeSend:function(){
				$(o).html("删除中...").removeAttr("onclick");
		   },
		   error:function(){
			   BUI.Message.Alert("服务器繁忙",'error');
			   return false ;
		   }
		  
		});			
	},'question');


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
			   url: "<?php echo site_url('adtype/del');?>" ,
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
			   url: "<?php echo site_url('adtype/edit');?>" ,
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
</script>
<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/bui-min.js"></script>