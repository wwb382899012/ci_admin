<?php 
/*
 *后台控制器不需要进行权限控制
 *author 王建 
 */
if (! defined('BASEPATH')) {
    exit('Access Denied');
}
class Common extends CI_Controller{
	private $table_ ; //表的前缀
	private $username = '' ;
	private $role_id = '' ;
	private $admin_id = '' ;
	function Common(){
		parent::__construct();
		$this->load->model('M_common');
		$this->table_ =table_pre('real_data');
		$this->username = login_name();//当前登录的用户
		$this->role_id = role_id();
		$this->admin_id = admin_id();
	}

	function get_menu(){
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
		$list = array() ;
		//查询path=0 的数据 顶层的
		$top_sql = "SELECT id,name,pid as parentid,url,status,addtime,disorder ,url_type,collapsed,path from {$this->table_}common_admin_nav where status = 1 and pid = '0'   order by disorder,id desc " ;
		$top_list = $this->M_common->querylist($top_sql);
		if($top_list && !$isadmin){
			foreach($top_list as $top_key => $top_val){
				if(!in_array($top_val['url'] , $perm_array)){
					unset($top_list[$top_key]);
				}else{
					$path_string.=" or path like '{$top_val['path']}-{$top_val['id']}%' ";
					$sql = "SELECT id,name,pid as parentid,url,status,addtime,disorder ,url_type,collapsed from {$this->table_}common_admin_nav where status = 1 and path like '{$top_val['path']}-{$top_val['id']}%' " ; 
					$list[] = $this->M_common->querylist($sql);
				}
			}
			//$path_string = ltrim($path_string  , " or"  );
			//$path = " AND ( ".$path_string." )";
		}else{
			$path = '' ;
		}
		
		$temp = array();
		
		if($list){
			foreach($list as $list_key => $list_val ){
					foreach($list_val as $vv_key =>$vv_val){
						$temp[] = array(
							'id'=>$vv_val['id'],
							'name'=>$vv_val['name'],
							'parentid'=>$vv_val['parentid'],
							'url'=>$vv_val['url'],
							'status'=>$vv_val['status'],
							'addtime'=>$vv_val['addtime'],
							'disorder'=>$vv_val['disorder'],
							'url_type'=>$vv_val['url_type'],
							'collapsed'=>$vv_val['collapsed'],
					);
					}
				
			}
			unset($list) ; 
			$list = $temp ; 
		}
		
		if($isadmin){
		//查询菜单列表
			$sql = "SELECT id,name,pid as parentid,url,status,addtime,disorder ,url_type,collapsed from {$this->table_}common_admin_nav where status = 1   order by disorder,id desc " ;
			$list = $this->M_common->querylist($sql);
		}
		
		$result = array();
		if($list){
			foreach($list as $k=>$v){
				$result[$v['id']]  = $v ;
			}
		}
		$result = genTree9($result,'id','parentid','items');
		
		$last_data = array();
		$top_array = array();
		$two = array();
		$three = array();
		$homePage = '';
	
		if($result){
			foreach($result as $k=>$v){
				$top_array[] = $v['name'];
				if(isset($v['items']) && $v['items']){
					foreach($v['items'] as $t_k=>$t_v){
						if(!$isadmin && !in_array($t_v['url'],$perm_array)){//二级菜单的权限
								continue ;
						}
						//二级的菜单
						if(isset($t_v['items']) && $t_v['items']){
							foreach($t_v['items'] as $three_key=>$three_val){
								//判断权限开始
								if(!$isadmin && !in_array($three_val['url'],$perm_array)){//三级菜单的权限
									continue ;
								}
								$three[] = array(
										'text'=>$three_val['name'],
										'href'=>($three_val['url_type'] == 1 )?site_url($three_val['url']):$three_val['url'],
										'id'=>$three_val['id'],
										
								);																									
							}
							
						}
						
						$two[] = array(
							'text'=> $t_v['name'] ,
							'items'=>isset($three)?$three:array(),
							'collapsed'=>($t_v['collapsed'] == 1) ?true:false ,//判断菜单是否是收缩 
						);
						unset($three);
					}
				}
				if(isset($two) && !empty($two)){
					$last_data[] = array(
						'menu'=>isset($two)?$two:array() ,
						'homePage'=>isset($two[0]['items'][0]['id'])?$two[0]['items'][0]['id']:0,
						'id'=>isset($two[0]['items'][0]['id'])?$two[0]['items'][0]['id']:0,
						'collapsed'=>false,//默认是不是收缩的
					);				
				}

				unset($two);
			}
		}
		//$last_data['top'] = $top_list ;
		
		echo json_encode($last_data);
		
	}

}