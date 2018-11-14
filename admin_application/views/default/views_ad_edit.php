<?php 
if (! defined('BASEPATH')) {
	exit('Access Denied');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>编辑广告_<?php echo $info['name'];  ?></title>
    <meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Css/style.css" />   
	<link rel="stylesheet" type="text/css" href="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/css/dpl-min.css" />   
	<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Js/DatePicker/WdatePicker.js"></script>
    <script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/assets/js/jquery-1.8.1.min.js"></script>
	<script type="text/javascript" src="<?php echo  base_url() ;?>/<?php echo APPPATH?>/views/static/Js/validate/validator.js"></script>   
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
   <a  class="btn btn-primary" id="addnew" href="<?php echo site_url("ad/index");?>">广告列表数据</a>
</div>
<?php echo form_open_multipart('ad/edit');?>
<input type="hidden" value="doedit" name="action">
<input type="hidden" value="<?php echo $info['id'];?>" name="id">
<table class="table table-bordered table-hover definewidth m10">
    <tr>
        <td width="10%" class="tableleft">名称</td>
        <td><input type="text" name="name" placeholder="名称" required="true" style="width:300px" value="<?php echo $info['name'];  ?>"/></td>
    </tr>	
     <tr>
        <td class="tableleft">广告分类</td>
        <td>
         <select name="ad_type" style="width:300px"  required="true">
			<option value="">请选择</option>
         	<?php 
     
         		if(isset($ad_type) && $ad_type ){
         			foreach($ad_type as $type_key=>$type_v){
         		
         	?>
         	<option value="<?php echo $type_v['id'] ; ?>" <?php if($info['ad_type'] == $type_v['id'] ){echo "selected='selected'";}?>><?php echo $type_v['typename']  ; ?></option>
         	<?php 
         					
         		}
         	}
         	?>
         </select>
        </td>
    </tr> 	
	<tr>
        <td width="10%" class="tableleft">广告类别</td>
        <td>
		<select name="type" onchange="_ad()" id="type">
			<option value="0" <?php if($info['type'] == 0 ){echo "selected='selected'";}?>>图片广告</option>
			<option value="1"  <?php if($info['type'] == 1 ){echo "selected='selected'";}?>>文字广告</option>
		</select>
		</td>
    </tr>
	<tbody id="t_0" class="pp" <?php if($info['type'] == 1 ){echo "style='display:none';";}?>>
    <tr>
        <td width="10%" class="tableleft">图片</td>
        <td><input type="file" name="pic"  style="width:300px"/><img src="<?php echo $this->v_upload_path."/{$info['pic']}"?>" style="width:300px"></td>
    </tr> 
	 <tr>
        <td width="10%" class="tableleft">图片描述</td>
        <td>
		<input type="text" name="pic_des" placeholder="图片描述"  style="width:300px" value="<?php  echo $info['pic_des']; ?>"/> 
       </td>
    </tr> 
    <tr>
        <td width="10%" class="tableleft">连接地址</td>
        <td>
		<input type="text" name="pic_url" placeholder="连接地址"  style="width:300px"  value="<?php  echo $info['pic_url']; ?>"/> 
       </td>
    </tr> 
	</tbody>
	<tbody id="t_1" class="pp" <?php if($info['type'] == 0 ){echo "style='display:none';";}?>>
    <tr>
        <td width="10%" class="tableleft">文字描述</td>
        <td>
       <textarea style="width:100%; height:150px" name="words" placeholder="文字描述" ><?php  echo $info['words']; ?></textarea>
       </td>
    </tr> 
	</tbody>
    <tr>
        <td width="10%" class="tableleft">开始日期</td>
        <td>
		<input id="beginDate" class="Wdate" style="width:300px" type="text" name="begin_date" placeholder="" value="<?php echo ($info['begin_date'] > 0 )?date("Y-m-d H:i:s",$info['begin_date']):'';?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'%y-%M-%d',isShowClear:true,readOnly:true})" readonly="">
       </td>
    </tr> 
    <tr>
        <td width="10%" class="tableleft">结束日期</td>
        <td>
			<input style="width:300px" id="end_date" class="Wdate" type="text" name="end_date" placeholder="" value="<?php echo ($info['end_date']>0)?date("Y-m-d H:i:s",$info['end_date']):'';?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:true,readOnly:true})" readonly="">
       </td>
    </tr> 	
    <tr>
        <td class="tableleft">状态</td>
        <td>
            <input type="radio" name="status" value="1" <?php if($info['status'] == 1 ){echo "checked";}?>/> 启用
           <input type="radio" name="status" value="0" <?php if($info['status'] == 0 ){echo "checked";}?>/> 禁用
        </td>
    </tr>
    	
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
$(function () {   
	$("#btnSave").click(function(){		
		if($("#myform").Valid() == false || !$("#myform").Valid()) {
			return false ;
		}
	});
});
function _ad(){
	$(".pp").hide() ; 
	$("#t_"+$("#type").val()).show() ; 
}
</script>
<script>
</script>