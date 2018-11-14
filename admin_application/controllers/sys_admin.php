<?php
/*
 *角色配置
 *author 王建 
 */
if (! defined('BASEPATH')) {
    exit('Access Denied');
}
class Sys_admin extends MY_Controller{
	private $admin_perm_path ='';
	private $perm_array = array() ; //用户的特殊权限
	function Sys_admin(){
		parent::__construct();
		$this->load->model('M_common');
		$this->admin_perm_path = config_item("role_cache");
	}
	function index(){
		$action = $this->input->get_post("action");	
		$action_array = array("show","ajax_data");
		$action = !in_array($action,$action_array)?'show':$action ;
		if($action == 'show'){
			$this->load->view(__TEMPLET_FOLDER__."/views_sys_admin");
		}elseif($action == 'ajax_data'){
			$this->ajax_data();
		}
		
		
	}
	//ajax get data
	private function ajax_data(){
		$page = intval($this->input->get_post("page"));		
		$this->load->library("common_page");
		if($page <=0 ){
			$page = 1 ;
		}
		$per_page = 10;//每一页显示的数量
		$limit = ($page-1)*$per_page;
		$limit.=",{$per_page}";
		$where = ' where 1= 1 ';
		$username = daddslashes(html_escape(strip_tags($this->input->get_post("username")))) ;
		if(!empty($username)){
			$condition = intval($this->input->get_post("condition"));
			$condition  = in_array($condition,array(1,2))?$condition:1;
			$array_condition_search  = array(
				1=>" LIKE '%{$username}%'", //模糊搜索
				2=>"= '{$username}'"
			);
			$where.=" AND username {$array_condition_search[$condition]}";
		}
		$sql_count = "SELECT COUNT(*) AS tt FROM {$this->table_}common_system_user as a  left join {$this->table_}common_role as b on a.gid = b.id {$where} ";
		$total  = $this->M_common->query_count($sql_count);
		$page_string = $this->common_page->page_string($total, $per_page, $page);
		$sql_role = "SELECT a.*,b.rolename FROM {$this->table_}common_system_user as a left join {$this->table_}common_role as b on a.gid = b.id {$where} order by id desc limit  {$limit}";	
		$list = $this->M_common->querylist($sql_role);
		foreach($list as $k=>$v){
			$list[$k]['status'] = ($v['status'] == 1 )?"开启":'<font color="red">关闭</font>';
			$list[$k]['rolename'] = ($v['super_admin'] == 1 )?'<font color="red">管理员</font>':$v['rolename'];
		}
		echo result_to_towf_new($list, 1, '成功', $page_string) ;
	}
	//添加后台用户
	function add(){
		
		$action = $this->input->get_post("action");		
		$action_array = array("add","doadd");
		$action = !in_array($action,$action_array)?'add':$action ;	
		if($action == 'add'){
			$sql_role = "SELECT * FROM {$this->table_}common_role where status = 1 order by id desc ";
			$list = $this->M_common->querylist($sql_role);
			$data  = array(
				'list'=>$list
			) ;
			$this->load->view(__TEMPLET_FOLDER__."/views_sys_admin_add",$data);		
		}elseif($action =='doadd'){
			$this->doadd();
		}
	}
	//处理添加
	private function doadd(){
		$gid = verify_id($this->input->get_post("gid")); //gid
		$username = dowith_sql(daddslashes(html_escape(strip_tags($this->input->get_post("username")))));//username
		$password = (daddslashes(html_escape(strip_tags($this->input->get_post("password")))));//passwd
		$status = verify_id($this->input->get_post("status")); //状态	
		$super_admin = verify_id($this->input->get_post("super_admin")); //状态	
		if($gid<=0 && $super_admin!=1 ){
			showmessage("请选择用户组","sys_admin/add",3,0);
			exit();
		}elseif(abslength($username)<3 || abslength($username)>16){
			showmessage("用户名长度必须在3-16之间","sys_admin/add",3,0);
			exit();
		}elseif($password == "" || utf8_str($password) != 1){
			showmessage("密码不能为空必须是英文","sys_admin/add",3,0);
			exit();
		}
		//查询所要添加的数据是不是存在
		$sql_one = "SELECT username FROM {$this->table_}common_system_user where username = '{$username}' limit 1  ";
		$info = $this->M_common->query_one($sql_one);
		if(!is_array($info) && empty($info)){
			showmessage("你要添加的用户已经存在","sys_admin/add",3,0);
			exit();
		}
		$data = array(
			'username'=>$username,
			'passwd'=>md5($password),
			'status'=>$status,
			'addtime'=>date("Y-m-d H:i:s",time()),
			'gid'=>$gid,
			'super_admin'=>$super_admin,
		);
		$array = $this->M_common->insert_one("{$this->table_}common_system_user",$data);
		if($array['affect_num']>=1){
			write_action_log($array['sql'],$this->uri->uri_string(),login_name(),get_client_ip(),1,"添加用户为{$username}成功");
			header("Location:".site_url("sys_admin/index"));
		}else{
			write_action_log($array['sql'],$this->uri->uri_string(),login_name(),get_client_ip(),0,"添加用户为{$username}失败");
			showmessage("服务器繁忙","sys_admin/add",3,0);
			exit();
		}
	}
	//编辑页面
	function edit(){
		$action = $this->input->get_post("action");		
		$action_array = array("edit","doedit");
		$action = !in_array($action,$action_array)?'edit':$action ;		
		
		if($action == 'edit'){
			$id = verify_id($this->input->get_post("id"));//用户的ID
			if($this->admin_id == $id ){
				showmessage("对不起你不能编辑自己的权限信息","sys_admin/index",3,0);
				exit();
			}
			$sql_super = "SELECT super_admin FROM {$this->table_}common_system_user WHERE username = '{$this->username}' limit 1 ";
			$info = $this->M_common->query_one($sql_super);			
			if(isset($info['super_admin']) && $info['super_admin']){//当前登陆的用户是管理员
				//如果是后台管理员
				$sql_= "SELECT a.* FROM {$this->table_}common_system_user as a where a.id = '{$id}'";					
				$info_ = $this->M_common->query_one($sql_);//修改后台用户的基本信息
				if(empty($info_)){
					showmessage("参数错误","sys_admin/index",3,0);
					exit();
				}
				if($info_['super_admin'] == 1 && !in_array($this->username,config_item("super_admin")) ){//当前修改的用户是管理员 ， 并且操作者必须是超级管理员					
					showmessage("对不起不能修改其他的管理员，因为你不是超级管理员","sys_admin/index",3,0);
					exit();
				}
				$sql_role = "SELECT * FROM {$this->table_}common_role where status = 1 order by id desc ";
				$list = $this->M_common->querylist($sql_role);
				$perm_array = $this->M_common->querylist("SELECT id,name,pid as parentid,url from {$this->table_}common_admin_nav order by disorder,id desc ");
				$result = array();
				if($perm_array){
					foreach($perm_array as $k=>$v){
						$result[$v['id']]  = $v ;
					}
				}
				$result = genTree9($result,'id','parentid','childs');
				$options =  getChildren($result);		
				
				//用户有的特殊权限
				$perms = $info_['perm'];
				$perm_array_exists = array();
				if($perms){
					$perm_array_exists = unserialize($perms);
				}
				$temp = array();
				if($options && $perm_array_exists){
					foreach($options as $o_=>$v_){
						if(in_array($v_['url'],$perm_array_exists)){
							$temp[$v_['url']] = $v_['name'];
						}
					}
				}
				unset($perm_array_exists);
				$perm_array_exists = $temp ;
				$data = array(
					'info'=>$info_,
					'list'=>$list,
					'options'=>$options,
					'perm_array_exists'=>$perm_array_exists
				);				
				$this->load->view(__TEMPLET_FOLDER__."/views_sys_admin_edit",$data);		
				
			}else{
				showmessage("对不起你没权限进行修改后台管理员信息","sys_admin/index",3,0);
				exit();
			}
			
		}elseif($action == 'doedit'){
			$this->doedit();
		}

	}
	//处理编辑数据
	private function doedit(){
		$id = verify_id($this->input->get_post("id"));	
		$username = dowith_sql(daddslashes(html_escape(strip_tags($this->input->get_post("username")))));//rolename
		$passwd = daddslashes(html_escape(strip_tags($this->input->get_post("password"))));//passwd
		$status = verify_id($this->input->get_post("status"));
		$gid = verify_id($this->input->get_post("gid"));//用户组
		$super_admin = verify_id($this->input->get_post("super_admin"));//是否是超级管理员
		$perms =$this->input->get_post("p");
		$perms_array = array();
		$perms_string = '' ;
		if($this->admin_id == $id ){
			showmessage("对不起你不能编辑自己的权限信息","sys_admin/index",3,0);
			exit();
		}
		if($perms){
			for($i = 0 ;$i <count($perms); $i++){
				if($perms[$i] == ''){
					continue ;
				}
				$perms_array[] = daddslashes(html_escape($perms[$i]));
			}
			if($perms_array){
				$perms_string = serialize($perms_array);
			}
		}
		if(abslength($username)<3 || abslength($username)>16){
			showmessage("用户名长度必须在3-16之间","sys_admin/edit",3,0,"?id={$id}");
			exit();
		}
		if(!empty($passwd)){
			if(abslength($passwd)<3 || abslength($passwd)>16){
				showmessage("密码长度必须在3-16之间","sys_admin/edit",3,0,"?id={$id}");
				exit();
			}		
		}

		$where = '';
		if(!empty($passwd)){
			$passwd = md5($passwd);
			$where.=", passwd = '{$passwd}'";
		}
		if(!$super_admin){
			$where.=", gid = '{$gid}'";
		}
		if(!$super_admin){
			$where.=", perm = '{$perms_string}'";
		}
		//写用户的特殊权限到缓存文件
		$this->admin_id = $id ; 
		$this->perm_array = $perms_array ;
		$this->make_cache(); //make cache		
		//写用户的特殊权限到缓存文件
		$sql_edit = "UPDATE `{$this->table_}common_system_user` SET status = '{$status}' {$where} ,username = '{$username}',super_admin = '{$super_admin}' where id = '{$id}'";
		$num = $this->M_common->update_data($sql_edit);

		if($num>=1){
			write_action_log($sql_edit,$this->uri->uri_string(),login_name(),get_client_ip(),1,"修改用户为{$username}成功");
			header("Location:".site_url("sys_admin/index/"));
		}else{
			write_action_log($sql_edit,$this->uri->uri_string(),login_name(),get_client_ip(),0,"修改用户为{$username}失败");
			showmessage("服务器繁忙，或者你没有修改任何数据","sys_admin/edit",3,0,"?id={$id}");
		}
	}
	//修改密码
	function edit_passwd(){
		$action = $this->input->get_post("action");		
		$action_array = array("edit","doedit");
		$action = !in_array($action,$action_array)?'edit':$action ;	
		if($action == 'edit'){
			$this->load->view(__TEMPLET_FOLDER__."/views_sys_admin_editpasswd");	
		}elseif($action == 'doedit'){
			$this->do_edit_passwd();
		}
	}
	//处理修改密码
	private function do_edit_passwd(){
		$password = daddslashes(html_escape(strip_tags($this->input->get_post("passwd"))));//passwd
		$repassword = daddslashes(html_escape(strip_tags($this->input->get_post("repasswd"))));//passwd
		if(empty($password) || empty($repassword)){
			echo result_to_towf_new('',0,'请检查密码不能为空',null);
			die();
		}
		if($password != $repassword){
			echo result_to_towf_new('',0,'2次密码 不相同',null);
			die();
		}	
		if(abslength($password)<3 || abslength($password)>16){
			echo result_to_towf_new('',0,'密码长度必须在3-16之间',null);
			exit();
		}	

		$password = md5($password);
		$sql_edit = "UPDATE `{$this->table_}common_system_user` SET passwd = '{$password}' where id = '{$this->admin_id}'";
		$num = $this->M_common->update_data($sql_edit);
		if($num>=1){
			write_action_log($sql_edit,$this->uri->uri_string(),login_name(),get_client_ip(),1,"用户{$this->admin_id}修改自己的密码成功");
			echo result_to_towf_new('',1,'密码修改成功',null);
			exit();
		}else{
			write_action_log($sql_edit,$this->uri->uri_string(),login_name(),get_client_ip(),0,"用户{$this->admin_id}修改自己的密码失败");
			echo result_to_towf_new('',0,'服务器繁忙,或者你可能没修改任何的数据',null);
			exit();
		}
	}
	
	//生成缓存
	private function make_cache(){
		if(!is_really_writable($this->admin_perm_path)){				
			exit("目录".$this->admin_perm_path."不可写");
		}
		
		if(!file_exists($this->admin_perm_path)){
			mkdir($this->admin_perm_path);
		}
		$configfile = $this->admin_perm_path."/cache_admin_{$this->admin_id}.inc.php";
		$fp = fopen($configfile,'w');
		flock($fp,3);
		fwrite($fp,"<"."?php\r\n");
    	fwrite($fp,"/*用户特殊的权限缓存*/\r\n");
    	fwrite($fp,"/*author wangjian*/\r\n");
    	fwrite($fp,"/*time 2014_03_01*/\r\n");
    	fwrite($fp,"\$admin_perm_array = array(\r\n");
		foreach($this->perm_array as $k=>$v){
			fwrite($fp,"'{$k}' => '{$v}',\r\n");
		}
		fwrite($fp,");\r\n");
		fwrite($fp,"?".">");
    	fclose($fp);
	}	
	
}