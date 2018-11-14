<?php 
/*
 *后台导航文件
 *author 王建 
 */
if (! defined('BASEPATH')) {
    exit('Access Denied');
}
class Nav extends MY_Controller {
	private $name = '';
	private $url = '';
	private $pid = '';
	private $disorder = '';
	private $status = '' ;
	private $id = '' ;
	private $url_type = '';
	private $collapsed = 0 ; 
	private $top = array() ;
	function Nav(){
		parent::__construct();
		$this->load->model('M_common');
	}
	//导航列表
	function index(){
		$list = $this->M_common->querylist("SELECT id,name,pid as parentid,url,status,addtime,disorder,path from {$this->table_}common_admin_nav order by disorder,id desc ");
			
		$result = array();
		if($list){
			foreach($list as $k=>$v){
				$result[$v['id']]  = $v ;
			}
		}
	
		$result = genTree9($result,'id','parentid','childs');
		
		$data = array(
			'list'=>$result
		);
		$this->load->view(__TEMPLET_FOLDER__."/views_nav_list",$data);
	}
	//function add
	function add(){		
		$action = $this->input->get_post("action");		
		$action_array = array("add","doadd");
		$action = !in_array($action,$action_array)?'show':$action ;	
		if($action == 'show'){		
			$id = intval($this->input->get_post("id"));	
			$list = $this->M_common->querylist("SELECT id,name,pid as parentid,url from {$this->table_}common_admin_nav order by disorder,id desc ");
			$result = array();
			if($list){
				foreach($list as $k=>$v){
					$result[$v['id']]  = $v ;
				}
			}
			$result = genTree9($result,'id','parentid','childs');
			$options =  getChildren($result);	
			$data['options'] = $options ;
			$data['pid'] = $id ;
			$this->load->view(__TEMPLET_FOLDER__."/views_nav_add",$data);
			
		}elseif($action == 'doadd'){
			$this->doadd();
		}
	}

	//处理添加数据
	private function doadd(){
			
		$this->set_field();
		$one = $this->M_common->query_one("select pid,path from `{$this->table_}common_admin_nav` where id = '{$this->pid}' limit 1 ") ; 
		//递归查询所有的上级ID	
		$this->queryPid(isset($one['pid'])?$one['pid']:0);
		$path_array = array_reverse($this->top) ;
		$path = implode("-" , $path_array);
		$newpath = '' ; 
		if($path == '0' ){
			$newpath = 0 ; 
		}else{
			array_pop($path_array);
			array_push($path_array , $this->pid) ;
			$newpath = implode("-" , $path_array);
		}
		
		$data = array(
			'pid'=>$this->pid,
			'name'=>$this->name,
			'status'=>$this->status,
			'addtime'=>date("Y-m-d H:i:s",time()),
			'url'=>$this->url,
			'disorder'=>$this->disorder,
			'path'=>$newpath
		);
		
		$array = $this->M_common->insert_one("{$this->table_}common_admin_nav",$data);
		
		if($array['affect_num']>=1){
			$one = $this->M_common->query_one("select pid from `{$this->table_}common_admin_nav` where id = '{$array['insert_id']}' limit 1 ") ; 
			$this->queryPid($one['pid']);
			$path_array = array_reverse($this->top) ;
			$path = implode("-" , $path_array);
			$sql_update = "UPDATE `{$this->table_}common_admin_nav` set path = '{$path}' where id = '{$array['insert_id']}'";
			$num = $this->M_common->update_data($sql_update);
			//showmessage("添加成功","nav/index",3,1);
			write_action_log($array['sql'],$this->uri->uri_string(),login_name(),get_client_ip(),1,"添加导航{$this->name}成功");
			redirect("nav/index");
			exit();
		}else{
			write_action_log($array['sql'],$this->uri->uri_string(),login_name(),get_client_ip(),0,"添加导航{$this->name}失败");
			showmessage("服务器繁忙请稍候","nav/add",3,1);
			exit();
		}
	}
	//编辑导航页面
	public function edit(){
		$is_super_admin = is_super_admin();
		if(!$is_super_admin){
			showmessage("对不起你没权限进行修改菜单,请联系管理员","nav/index",3,0);
			die();
		}
		$action = $this->input->get_post("action");
		$action_array = array("edit","doedit");
		$action = !in_array($action,$action_array)?'edit':$action ;
		if($action == 'edit'){
			$id = verify_id($this->input->get_post("id")); //pid
			$info = $this->M_common->query_one("SELECT * FROM {$this->table_}common_admin_nav WHERE id = '{$id}'");
			if(empty($info)){
				showmessage("请传递正确的参数","nav/index",3,0);
				exit() ;
			}
			$list = $this->M_common->querylist("SELECT id,name,pid as parentid,url from {$this->table_}common_admin_nav order by disorder,id desc ");
			$result = array();
			if($list){
				foreach($list as $k=>$v){
					$result[$v['id']]  = $v ;
				}
			}
			$result = genTree9($result,'id','parentid','childs');
			$options =  getChildren($result);
			$data = array(
				'info'=>$info,
				'options'=>$options,
			);
			$this->load->view(__TEMPLET_FOLDER__."/views_nav_edit",$data);
		}elseif($action == 'doedit'){
			$this->doedit();
		}
	}
	//处理编辑
	private function doedit(){	
		$this->set_field();	
		//根据当前的ID查询
		$one = $this->M_common->query_one("select pid,path from `{$this->table_}common_admin_nav` where id = '{$this->id}' limit 1 ") ; 
		
		//递归查询所有的上级ID	
		$this->queryPid($one['pid']);
		$path_array = array_reverse($this->top) ;
		$path = implode("-" , $path_array);
		$newpath = '' ; 
		if($path == '0' ){
			$newpath = 0 ; 
		}else{
			array_pop($path_array);
			array_push($path_array , $this->pid) ;
			$newpath = implode("-" , $path_array);
		}
		
		$sql_update = "UPDATE `{$this->table_}common_admin_nav` set pid = '{$this->pid}',name = '{$this->name}',url = '{$this->url}' ,disorder = '{$this->disorder}' ,status = '{$this->status}' ,url_type = '{$this->url_type}' ,`collapsed` = '{$this->collapsed}' , path = '{$newpath}' where id = '{$this->id}'";
		$num = $this->M_common->update_data($sql_update);
		//修改下一级的状态
		$sql_child  = "UPDATE `{$this->table_}common_admin_nav` SET `status` = '{$this->status}' where path like '{$one['path']}-{$this->id}%'  " ;
		
		$this->M_common->update_data($sql_child);
		if($num>=1){
			write_action_log($sql_update,$this->uri->uri_string(),login_name(),get_client_ip(),1,"编辑导航{$this->name}成功");	
			redirect("nav/index");
			//showmessage("修改成功","nav/index",3,1);
		
			exit();
		}else{
			write_action_log($sql_update,$this->uri->uri_string(),login_name(),get_client_ip(),0,"编辑导航{$this->name}失败");
			showmessage("服务器繁忙请稍候","nav/edit",3,0,"?id={$this->id}");
			exit();
		}
	}
	private function set_field(){
		$this->pid = verify_id($this->input->get_post("pid")); //pid
		$this->id = verify_id($this->input->get_post("id")); //id
		$this->name = dowith_sql(daddslashes(html_escape(strip_tags($this->input->get_post("name")))));//name
		$this->url = daddslashes(html_escape(strip_tags($this->input->get_post("url"))));//url地址
		$this->disorder = verify_id($this->input->get_post("disorder")); //排序
		$this->status = verify_id($this->input->get_post("status")); //状态	
		$this->url_type = verify_id($this->input->get_post("url_type")); //url类型	
		$this->collapsed = verify_id($this->input->get_post("collapsed")); //是否收缩
		
	}
	//其中ID是导航的id 
	private function queryPid($id){
		$sql = "select pid ,id  from `{$this->table_}common_admin_nav` where id = '{$id}' limit 1 " ; 
		$data = array() ;
		$data = $this->M_common->query_one($sql) ; 
		$this->top[] = isset($data['id'])?$data['id']:0;
		if(isset($data) && $data  ){		
			$this->queryPid(isset($data['pid'])?$data['pid']:0) ; 
		}
		
	}
}




