<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>修改密码</title>
    <meta charset="UTF-8">
<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Js/bootstrap.min.js"></script>
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
<form action="<?php echo site_url("nav/add");?>" method="post" class="definewidth m2"  name="myform" id="myform">
<input type="hidden" name="action" value="doadd">
<table class="table table-bordered table-hover m20">

    <tr>
        <td class="tableleft">密码:</td>
        <td><input type="password" name="passwd" id="passwd" placeholder="新密码"/></td>
    </tr>
    <tr>
        <td class="tableleft">确认密码:</td>
        <td><input type="password" name="repasswd" id="repasswd" placeholder="确认密码" /></td>
    </tr>
  <tr>
	<td id="result_" colspan="2"> 
		
	</td>
  </tr>
    <tr>
       <td id="" colspan="2"> 
	   
	   </td>
    </tr>
</table>
</form>
</body>
</html>
<script>
$(function () {       
		$('#backid').click(function(){
				window.location.href="<?php echo site_url("nav/index");?>";
		 });
		$("#btnSave").click(function(){
			if($("#myform").Valid() == false || !$("#myform").Valid()) {
				return false ;
			}
		});
});

 
</script>
