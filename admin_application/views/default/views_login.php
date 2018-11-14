<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>后台用户登录</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/style.css" />
	<link href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" rel="stylesheet" type="text/css" />
	 <link href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/bui-min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/jquery-1.8.1.min.js"></script> 
	<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/bui-min.js"></script>
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
<div style="left: 33%;
    margin: auto;
    position: absolute;
    top: 20%;
    width: 500px;
}">
<form action="<?php echo site_url("login/dologin");?>" method="post" class="definewidth m2"  name="myform" id="myform">
<table class="table table-bordered table-hover m0">
	<tr style="background:#F5F5F5">
        <td class="" align="center" colspan="2"><img src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Images/logo.png" style="padding-left:30%"></td>
       
    </tr>
    <tr>
        <td class="tableleft">用户名：</td>
        <td><input type="text" name="username" id="name"  placeholder="User name" /></td>
    </tr>
    <tr>
        <td class="tableleft">密&nbsp;码：</td>
        <td><input type="password" name="passwd"  placeholder="Password"  id="passwd" /></td>
    </tr>
	<?php 
		if(config_item("yzm_open")){
	?>
    <tr>
        <td class="tableleft">验证码：</td>
        <td><input type="text" name="yzm"  placeholder="请输入验证码" id='yzm' onblur="check_yzm()" /><span id="message_code" style="color:green"></span></td>
    </tr>
    <tr>
        <td class="tableleft"></td>
        <td id="yzm"><img id="imgage_"   style="cursor:pointer"  title="点击更换验证码" alt="点击更换验证码" src="<?php echo site_url("login/code");?>" onclick="javascript:this.src='<?php echo site_url("login/code");?>?'+Math.random()">
		<br />
		看不清楚,<a href="javascript:void(0)" style="color:#FF6600" onclick="change_code()">请点击这里</a>
		</td>
    </tr>	
	<?php 
		}
	?>
    <tr>
        <td class="tableleft"></td>
        <td>
             <button class="btn btn-lg btn-primary btn-block" type="submit" id="btnSave">登&nbsp;&nbsp;录 </button>
             <span style="margin-left:70px; float:right; margin-top:10px;font-size:10px">powered by wangjian</span>
        </td>
    </tr>
</table>
</div>
</form>
</body>
</html>
<script>
$(function () {       
		
		$("#btnSave").click(function(){
			if($("#name").val() == ''){
				BUI.Message.Alert('用户名不能为空','error');
				return false ;
			}else if($("#passwd").val() == ''){
				BUI.Message.Alert('密码不能为空','error');
				return false ;
			}else if($("#yzm").val() == ''){
				BUI.Message.Alert('验证码不能为空','error');
				return false ;
			}else if(!check_code()){
				BUI.Message.Alert('验证码错误','error');
				return false ;
			}
		});
		
});
//校验验证码，返回true 或者false
function check_code(){
	var status = false ;
	$.ajax({
		   type: "POST",
		   url: "<?php echo site_url("login/check_code");?>" ,
		   data: {'code':$("#yzm").val()},
		   cache:false,
		   dataType:"text",
		   async:false,
		   success: function(msg){
				if(msg == 'success'){
					status = true ;
				}
		   }
		});	
		return status ;
}
function change_code(){
	$("#imgage_").click();
}

function check_yzm(){
	if($("#yzm").val() == ""){
		$("#message_code").html("<span style='color:red'>验证码不能为空</span>");
		return false ; 
	}
	if(!check_code()){
		BUI.Message.Alert('验证码错误','error');
		return false ;
	}else{
		$("#message_code").html("正确");
	}
}
 
 
</script>

