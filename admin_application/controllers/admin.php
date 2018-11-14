<?php 
/*
 *后台首页文件
 *author 王建 
 */
if (! defined('BASEPATH')) {
    exit('Access Denied');
}
class Admin extends MY_Controller {
	function Admin(){
		parent::__construct();
		$this->load->model('M_common');
	}
	//后台框架首页
	function index(){
		//查询当前登录的用户的菜单 为了配置权限
		$data = decode_data(); //获取cookie数据	
		$isadmin = false ;//判断是不是超级管理员
		if(isset($data['isadmin']) && $data['isadmin']){	
			$isadmin = true ;			
		}
		$user_perm_array = array();
		$admin_perm_array = array();
		$perm_array= array(); //用户的权限数组
		if(!$isadmin){
			if(file_exists(config_item("role_cache")."/cache_role_{$this->role_id}.inc.php")){
				include config_item("role_cache")."/cache_role_{$this->role_id}.inc.php" ;
				$user_perm_array = isset($role_array)?$role_array:array() ;
			}//用户的角色
			if(file_exists(config_item("role_cache")."/cache_admin_{$this->admin_id}.inc.php")){
				include config_item("role_cache")."/cache_admin_{$this->admin_id}.inc.php" ;
				$admin_perm_array = isset($admin_perm_array )?$admin_perm_array :array() ;
			}//查询用户的特殊权限
			
			if($user_perm_array && $admin_perm_array ){
				$perm_array = array_merge($user_perm_array,$admin_perm_array);
				$perm_array = array_unique($perm_array);
			}elseif($user_perm_array && !$admin_perm_array){
				$perm_array = $user_perm_array ;
			}elseif(!$user_perm_array && $admin_perm_array){
				$perm_array = $admin_perm_array ;
			}
			if($perm_array && config_item("no_need_perm")){
				$perm_array  = @array_merge($perm_array,config_item("no_need_perm"));
			}			
		}
		$path = '' ;
		$path_string = '' ;
		//查询path=0 的数据 顶层的
		$top_sql = "SELECT id,name,pid as parentid,url,status,addtime,disorder ,url_type,collapsed,path from {$this->table_}common_admin_nav where status = 1 and path = '0'   order by disorder,id desc " ;
		$top_list = $this->M_common->querylist($top_sql);
		if($top_list && !$isadmin){
			foreach($top_list as $top_key => $top_val){
				if(!in_array($top_val['url'] , $perm_array)){
					unset($top_list[$top_key]);
				}else{
					
					$path_string.=" or path like '{$top_val['path']}-{$top_val['id']}%' ";
				}
			}
			$path_string = ltrim($path_string  , " or"  );
			$path = " AND ( ".$path_string." )";
		}else{
			$path = '' ;
		}
		
		$sql="SELECT name from {$this->table_}common_admin_nav where pid = 0 and status = 1 {$path}   order by disorder,id desc " ;
	//	echo $sql;
		//$list = $this->M_common->querylist($sql);	
		//print_r($list);
		$sql_login_log = "SELECT * FROM {$this->table_}common_adminloginlog WHERE username = '{$this->username}' order by id desc limit 2 " ;
		$login_info = $this->M_common->querylist($sql_login_log);
		$data = array(
			'list'=>$top_list,
			'username'=>$this->username,
			'group_name'=>"<font color='red'>".$this->group_name."</font>",
			'login_info'=>$login_info
		);
		$this->load->view(__TEMPLET_FOLDER__."/views_index",$data);
	}
	
}
