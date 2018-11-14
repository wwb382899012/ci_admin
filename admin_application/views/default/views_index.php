<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>

<!DOCTYPE HTML>
<html>
 <head>
  <title>后台管理系统</title>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <style>
	.float_layer { width: 300px; border: 1px solid #aaaaaa; display:none; background: #fff; }
	.float_layer h2 { height: 25px; line-height: 25px; padding-left: 10px; font-size: 14px; color: #333; background: url(<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Images/title_bg.gif) repeat-x; border-bottom: 1px solid #aaaaaa; position: relative; }
	.float_layer .min { width: 21px; height: 20px; background: url(<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Images/min.gif) no-repeat 0 bottom; position: absolute; top: 2px; right: 25px; }
	.float_layer .min:hover { background: url(<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Images/min.gif) no-repeat 0 0; }
	.float_layer .max { width: 21px; height: 20px; background: url(<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Images/max.gif) no-repeat 0 bottom; position: absolute; top: 2px; right: 25px; }
	.float_layer .max:hover { background: url(<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Images/max.gif) no-repeat 0 0; }
	.float_layer .close { width: 21px; height: 20px; background: url(<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Images/close.gif) no-repeat 0 bottom; position: absolute; top: 2px; right: 3px; }
	.float_layer .close:hover { background: url(<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Images/close.gif) no-repeat 0 0; }
	.float_layer .content { height: 120px; overflow: hidden; font-size: 14px; line-height: 18px; color: #666; text-indent: 28px; }
	.float_layer .wrap { padding: 10px; }
</style>
  <link href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/bui-min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/main-min.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/jquery-1.8.1.min.js"></script>
 <script>
 $(function(){
	nav();
 });

 </script>
<script type="text/javascript">
function miaovAddEvent(oEle, sEventName, fnHandler)
{
	if(oEle.attachEvent)
	{
		oEle.attachEvent('on'+sEventName, fnHandler);
	}
	else
	{
		oEle.addEventListener(sEventName, fnHandler, false);
	}
}
window.onload = function()
{
	var oDiv=document.getElementById('miaov_float_layer');
	var oBtnMin=document.getElementById('btn_min');
	var oBtnClose=document.getElementById('btn_close');
	var oDivContent=oDiv.getElementsByTagName('div')[0];
	
	var iMaxHeight=0;
	
	var isIE6=window.navigator.userAgent.match(/MSIE 6/ig) && !window.navigator.userAgent.match(/MSIE 7|8/ig);
	
	oDiv.style.display='block';
	iMaxHeight=oDivContent.offsetHeight;
	
	if(isIE6)
	{
		oDiv.style.position='absolute';
		repositionAbsolute();
		miaovAddEvent(window, 'scroll', repositionAbsolute);
		miaovAddEvent(window, 'resize', repositionAbsolute);
	}
	else
	{
		oDiv.style.position='fixed';
		repositionFixed();
		miaovAddEvent(window, 'resize', repositionFixed);
	}
	
	oBtnMin.timer=null;
	oBtnMin.isMax=true;
	oBtnMin.onclick=function ()
	{
		startMove
		(
			oDivContent, (this.isMax=!this.isMax)?iMaxHeight:0,
			function ()
			{
				oBtnMin.className=oBtnMin.className=='min'?'max':'min';
			}
		);
	};
	
	oBtnClose.onclick=function ()
	{
		oDiv.style.display='none';
	};
};

function startMove(obj, iTarget, fnCallBackEnd)
{
	if(obj.timer)
	{
		clearInterval(obj.timer);
	}
	obj.timer=setInterval
	(
		function ()
		{
			doMove(obj, iTarget, fnCallBackEnd);
		},30
	);
}

function doMove(obj, iTarget, fnCallBackEnd)
{
	var iSpeed=(iTarget-obj.offsetHeight)/8;
	
	if(obj.offsetHeight==iTarget)
	{
		clearInterval(obj.timer);
		obj.timer=null;
		if(fnCallBackEnd)
		{
			fnCallBackEnd();
		}
	}
	else
	{
		iSpeed=iSpeed>0?Math.ceil(iSpeed):Math.floor(iSpeed);
		obj.style.height=obj.offsetHeight+iSpeed+'px';
		
		((window.navigator.userAgent.match(/MSIE 6/ig) && window.navigator.userAgent.match(/MSIE 6/ig).length==2)?repositionAbsolute:repositionFixed)()
	}
}

function repositionAbsolute()
{
	var oDiv=document.getElementById('miaov_float_layer');
	var left=document.body.scrollLeft||document.documentElement.scrollLeft;
	var top=document.body.scrollTop||document.documentElement.scrollTop;
	var width=document.documentElement.clientWidth;
	var height=document.documentElement.clientHeight;
	
	oDiv.style.left=left+width-oDiv.offsetWidth+'px';
	oDiv.style.top=top+height-oDiv.offsetHeight+'px';
}

function repositionFixed()
{
	var oDiv=document.getElementById('miaov_float_layer');
	var width=document.documentElement.clientWidth;
	var height=document.documentElement.clientHeight;
	
	oDiv.style.right='0px';
	oDiv.style.bottom='0px';
}
</script> 
 </head>
 <body>

  <div class="header">
    
      <div class="dl-title">
       <!--<img src="/chinapost/Public/assets/img/top.png">-->
      </div>

    <div class="dl-log">欢迎您，<span class="dl-log-user"><?php echo $username ;?>所在组:<?php echo $group_name ;?></span>|<a href="#" id="btnShow"  class="dl-log-quit" >[修改密码]</a>|<a href="javascript:void(0)" title="退出系统" class="dl-log-quit" onclick="login_out()">[退出]</a>
    <div><a href="javascript:void(0);" onclick="cc()">saas</a></div>
	</div> 
  </div>
   <div class="content">
    <div class="dl-main-nav">
      <div class="dl-inform"><div class="dl-inform-title"><s class="dl-inform-icon dl-up"></s></div></div>
      <ul id="J_Nav"  class="nav-list ks-clear">
     			 <!-- 
        		<li class="nav-item dl-selected"><div class="nav-item-inner nav-home">系统管理</div></li>
        		<li class="nav-item dl-selected"><div class="nav-item-inner nav-order">业务管理</div></li>  
        		-->
        		<?php 
        			if(isset($list) && $list){
        				foreach($list as $l_k=>$l_v){
        					$selected= '' ;
        					if($l_k == 0){
        						$selected = 'dl-selected';
        					}
        			
        		?> 
        		 <li class="nav-item <?php echo $selected ;?>"><div class="nav-item-inner nav-order"><?php echo $l_v['name'];?></div></li>  
        		 <?php 
        		 		}
        		 	}	
        		 ?>     

      </ul>
    </div>
    <ul id="J_NavContent" class="dl-tab-conten">

    </ul>
   </div>
  
  <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/bui-min.js"></script>
  <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/common/main-min.js"></script>
  <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/config-min.js"></script>
  <script type="text/javascript" >
 	function nav(){
 	    BUI.use('common/main',function(){ 
 	 	 // new PageUtil.MainPage({
 	        //modulesConfig : config
 	       //});
		   //获取json
			$.getJSON('<?php echo site_url('common/get_menu');?>',function(config){	
				//console.dir(config);
				new PageUtil.MainPage({
					modulesConfig : config
				});
			 
			});
 	     });
	    
 	}  
 </script>
 </body>
</html>
<div class="float_layer" id="miaov_float_layer">
    <h2>
        <strong>温馨提示:</strong>
        <a id="btn_min" href="javascript:;" class="min"></a>
        <a id="btn_close" href="javascript:;" class="close"></a>
    </h2>
    <div class="content">
    	<div class="wrap">
			<p>你好:<?php echo $this->username ;?></p>
			<p>上次登录时间:<?php echo isset($login_info[1]['logintime'])?$login_info[1]['logintime']:'首次登录';?></p>
			<p>登录ip地址:<?php echo isset($login_info[1]['ip'])?$login_info[1]['ip']:'首次登录' ;?></p>
        </div>
     </div>
</div>

<script type="text/javascript">
var Overlay = BUI.Overlay
var dialog = new Overlay.Dialog({
	title:'修改密码',
	width:500,
	height:200,
	loader : {
		url : '<?php echo site_url('sys_admin/edit_passwd')?>',
		autoLoad : false, //不自动加载
		params : {"inajax":"1"},//附加的参数
		lazyLoad : false //不延迟加载
	},
	mask:true,
	success:function(){
		var passwd = $("#passwd").val() ;
		var repasswd = $("#repasswd").val() ;
		if(passwd == '' || repasswd == ''){
			BUI.Message.Alert('密码不可以为空','error');
			return false ;
		}else if(passwd != repasswd){
			BUI.Message.Alert('2次密码不相同','error');
			return false ;
		}
	var data_ = {
		'action':"doedit",
		'passwd':passwd,
		'repasswd':repasswd
	} ;	
	$.ajax({
		   type: "POST",
		   url: "<?php echo site_url("sys_admin/edit_passwd");?>" ,
		   data: data_,
		   cache:false,
		   dataType:"json",
		 //  async:false,
		   success: function(msg){
			var code = msg.resultcode ;
			var message = msg.resultinfo.errmsg ;
			if(code ==  1 ){
				window.location.href="<?php echo site_url("login/login_out");?>";
			}else if(code <0){
				BUI.Message.Alert('对不起没权限','error');
				return false ;
			}else{
				BUI.Message.Alert(message,'error');
				return false ;
			}
		   },
		   beforeSend:function(){
			  $("#result_").html('<font color="red"><img src="<?php echo base_url();?>/<?php echo APPPATH?>/views/static/Images/progressbar_microsoft.gif"></font>');
		   },
		   error:function(){
			   alert('服务器繁忙请稍。。。。');
		   }
		  
		});			
		 
		this.close();
	}
});
//dialog.show(); //是否自动显示
$('#btnShow').on('click',function () {
	dialog.show();
	dialog.get('loader').load();
});
function login_out(){
	 BUI.Message.Confirm('确定要退出系统吗?',function(){
		window.location.href="<?php echo site_url("login/login_out");?>";
	},'question');
	
}

/*
*加载提示消息
*/
function tips(){
	var Overlay = BUI.Overlay
	var dialog = new Overlay.Dialog({
		title:'系统提示信息',
		width:500,
		height:200,
		loader : {
			url : '<?php echo site_url('sys_admin/edit_passwd')?>',
			autoLoad : false, //不自动加载
			params : {"inajax":"1"},//附加的参数
			lazyLoad : false //不延迟加载
		},
		mask:true,
		success:function(){
			
		}
	});
	dialog.show();
	dialog.get('loader').load();
}
//setInterval("tips()",5000);
</script>