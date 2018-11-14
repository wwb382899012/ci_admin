<?php
/*
 *角色配置
 *author  王建 
 */
if (! defined('BASEPATH')) {
    exit('Access Denied');
}
class Role extends MY_Controller{
	private $role_cache_path = '' ;
	private $perm_data = array(); //角色权限数组
	function Role(){
		parent::__construct();
		$this->load->model('M_common');
		$this->role_cache_path =  config_item("role_cache");
	}
	function index(){
		$action = $this->input->get_post("action");	
		$action_array = array("show","ajax_data","preview_user");
		$action = !in_array($action,$action_array)?'show':$action ;
		if($action == 'show'){
			$this->load->view(__TEMPLET_FOLDER__."/views_role");
		}elseif($action == 'ajax_data'){
			$this->ajax_data();
		}elseif($action == "preview_user"){
			$this->preview_user();
		}
		
		
	}
	private function ajax_data(){
		$this->load->library("common_page");
		$page = $this->input->get_post("page");	
		if($page <=0 ){
			$page = 1 ;
		}
		$per_page = 10;//每一页显示的数量
		$limit = ($page-1)*$per_page;
		$limit.=",{$per_page}";
		$sql_count = "SELECT COUNT(*) AS tt FROM {$this->table_}common_role ";
		$total  = $this->M_common->query_count($sql_count);
		$page_string = $this->common_page->page_string($total, $per_page, $page);
		$sql_role = "SELECT * FROM {$this->table_}common_role order by id desc limit  {$limit}";	
		$list = $this->M_common->querylist($sql_role);
		foreach($list as $k=>$v){
			$list[$k]['status'] = ($v['status'] == 1 )?"开启":'<font color="red">关闭</font>';
			$list[$k]['cache_file'] = (file_exists(config_item("role_cache")."/cache_role_{$v['id']}.inc.php"))?"存在":'<font color="red">不存在</font>';
			$list[$k]['filemtime'] = (file_exists(config_item("role_cache")."/cache_role_{$v['id']}.inc.php"))?date("Y-m-d H:i:s",filemtime(config_item("role_cache")."/cache_role_{$v['id']}.inc.php")):'<font color="red">文件不存在</font>';
		}
		echo result_to_towf_new($list, 1, '成功', $page_string) ;
	}
	//编辑页面
	function edit(){
		$action = $this->input->get_post("action");		
		$action_array = array("edit","doedit");
		$action = !in_array($action,$action_array)?'edit':$action ;		
		if($action == 'edit'){
			$id = verify_id($this->input->get_post("id"));
			$sql_role = "SELECT id , rolename,perm,status FROM {$this->table_}common_role WHERE id = '{$id}'";
			$info = $this->M_common->query_one($sql_role);
			if(empty($info)){
				showmessage("暂无数据","role/index",3,0);
				exit();
			}
			$list = $this->M_common->querylist("SELECT id,name,pid as parentid,url,status,addtime,disorder from {$this->table_}common_admin_nav order by disorder,id desc ");
			$result = array();
			if($list){
				foreach($list as $k=>$v){
					$result[$v['id']]  = $v ;
				}
			}
			$result = genTree9($result,'id','parentid','childs');
			
			$data = array(
				'list'=>$result,
				'info'=>$info
			);
			$this->load->view(__TEMPLET_FOLDER__."/views_role_edit",$data);		
		}elseif($action == 'doedit'){
			$this->doedit();
		}

	}
	//处理编辑数据
	private function doedit(){
		$role_array = $this->input->get_post("role");
		$id = verify_id($this->input->get_post("id"));
		$status = verify_id($this->input->get_post("status"));
		$rolename = dowith_sql(daddslashes(html_escape(strip_tags($this->input->get_post("rolename")))));//rolename
		$sql_role = "SELECT rolename FROM {$this->table_}common_role WHERE id = '{$id}'";
		$info = $this->M_common->query_one($sql_role);
		if(!is_array($info)){
			write_action_log($sql_role,$this->uri->uri_string(),login_name(),get_client_ip(),0,"参数错误");
			showmessage("修改失败,参数错误","role/index",3,0);
			exit();
		}
		if(!$role_array){
			write_action_log('no sql',$this->uri->uri_string(),login_name(),get_client_ip(),0,"请选择权限");
			showmessage("请选择权限","role/edit",3,0,"?id={$id}");
			exit();
		}
		$role_data = array();
		if($role_array){
			for($k = 0 ; $k<count($role_array);$k++){
				$role_data[] = daddslashes(html_escape($role_array[$k]));
			}
			$perm = serialize($role_data);
		}
	
		$sql_edit = "UPDATE {$this->table_}common_role SET rolename = '{$rolename}' , perm = '{$perm}',status = '{$status}' where id = '{$id}'";
		$num = $this->M_common->update_data($sql_edit);
		$this->perm_data = $role_data  ;
		$this->role_id = $id ;
		$this->make_cache();//生成缓存文件
		if($num>=1){
			//
			write_action_log($sql_edit,$this->uri->uri_string(),login_name(),get_client_ip(),1,"修改角色为{$rolename}成功");
			header("Location:".site_url("role/index/"));
		}else{
			write_action_log($sql_edit,$this->uri->uri_string(),login_name(),get_client_ip(),0,"修改角色为{$rolename}失败");
			showmessage("服务器繁忙，或者你没有修改任何数据","role/edit",3,0,"?id={$id}");
			die();
		}
	}
	
	///角色增加
	 function add(){
		$action = $this->input->get_post("action");		
		$action_array = array("add","doadd");
		$action = !in_array($action,$action_array)?'show':$action ;	
		if($action == 'show'){			
			$this->load->view(__TEMPLET_FOLDER__."/views_role_add");		
		}elseif($action == 'doadd'){
			$this->doadd();
		}
	}
	//处理增加
	private function doadd(){
		$rolename = dowith_sql(daddslashes(html_escape(strip_tags($this->input->get_post("rolename")))));//rolename
		$status = verify_id($this->input->get_post("status")); //状态	
		if(empty($rolename)){
			showmessage("角色名称不能为空","role/add",3,0);
			exit();
		}
		$data = array(
			'rolename'=>$rolename,
			'status'=>$status,
			'addtime'=>date("Y-m-d H:i:s",time())
		);
		$array = $this->M_common->insert_one("{$this->table_}common_role",$data);
		if($array['affect_num']>=1){
			write_action_log($array['sql'],$this->uri->uri_string(),login_name(),get_client_ip(),1,"添加角色为{$rolename}成功");
			showmessage("添加角色成功","role/index",3,1);
			exit();
		}else{
			write_action_log($array['sql'],$this->uri->uri_string(),login_name(),get_client_ip(),0,"添加角色为{$rolename}失败");
			showmessage("添加角色失败","role/index",3,0);
			exit();
		}
	}
	
	//生成缓存
	private function make_cache(){
		if(!is_really_writable($this->role_cache_path)){				
			exit("目录".$this->role_cache_path."不可写");
		}
		
		if(!file_exists($this->role_cache_path)){
			mkdir($this->role_cache_path);
		}
		$configfile = $this->role_cache_path."/cache_role_{$this->role_id}.inc.php";
		$str = '' ; 
		$time = date("Y-m-d H:i:s",time());
		$fp = fopen($configfile,'w');
		flock($fp,3);
		fwrite($fp,"<"."?php\r\n");
    	fwrite($fp,"/*团队角色缓存*/\r\n");
    	fwrite($fp,"/*author wangjian*/\r\n");
    	fwrite($fp,"/*time {$time}*/\r\n");
    	//fwrite($fp,"\$role_array = array(\r\n");
		/* foreach($this->perm_data as $k=>$v){
			fwrite($fp,"'{$k}' => '{$v}',\r\n");
		} */
		$str.="\$role_array = ";
    	$str.= var_export($this->perm_data,true)  ; 
    	fwrite($fp,"{$str};\r\n");
		//fwrite($fp,");\r\n");
		fwrite($fp,"?".">");
    	fclose($fp);
	}
	
	//预览所在用户组的用户
	private function preview_user(){
		$id = verify_id($this->input->get_post("id"));
		if($id<=0){
			echo "参数传递错误"; 
			exit();
		}
		$list = $this->M_common->querylist("SELECT id,username,status FROM {$this->table_}common_system_user where gid = '{$id}' ");
		if($list){
			foreach($list as $k=>$v){
				$status = ($v['status'] == 1 )?"<font color='green'>正常</font>":'<font color="red" >禁止</font>' ;
				echo "<li style=\"text-decoration:none; display:block ; width:100px; height:30px; padding:2px; float:left; border:solid 1px #F0F0F0 ;  text-align:center;line-height:30px; margin-left:3px\">";
				echo $v['username']."【".$status."】";
				echo "</li>";
			}
		}else{
			echo "暂无用户";
		}
	}
	
	//删除角色组
	public function del(){
		$id = verify_id($this->input->get_post("id"));
		if(!$id){
			echo result_to_towf_new("", 0, "参数传递错误", null);
			die();
		}
		if(!in_array($this->username,config_item("super_admin"))){
			echo result_to_towf_new("", -8, "你没有权限执行此操作", null);
			die();
		}
		$sql_del = "DELETE FROM {$this->table_}common_role WHERE id = '{$id}' limit 1 " ;
		$num = $this->M_common->del_data($sql_del);
		if($num>=1){
			if(file_exists($this->role_cache_path."/cache_role_{$id}.inc.php") && is_writable($this->role_cache_path."/cache_role_{$id}.inc.php")){
				unlink($this->role_cache_path."/cache_role_{$id}.inc.php") ;
			}
			write_action_log($sql_del,$this->uri->uri_string(),login_name(),get_client_ip(),1,"删除角色id为{$id}成功");
			echo result_to_towf_new("", 1, "删除成功", null);
			die();
		}
		write_action_log($sql_del,$this->uri->uri_string(),login_name(),get_client_ip(),0,"删除角色id为{$id}失败");
		echo result_to_towf_new("", 0, "服务器繁忙请稍后", null);
	}
}