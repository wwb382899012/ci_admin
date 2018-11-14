<?php
/*
 *广告类型
 *author  王建 
 */
if (! defined('BASEPATH')) {
    exit('Access Denied');
}
class Adtype extends MY_Controller{
	private $role_cache_path = '' ;
	private $perm_data = array(); //角色权限数组
	function Adtype(){
		parent::__construct();
		$this->load->model('M_common');
		$this->role_cache_path =  config_item("role_cache");
	}
	function index(){
		$action = $this->input->get_post("action");	
		$action_array = array("show","ajax_data","preview_user");
		$action = !in_array($action,$action_array)?'show':$action ;
		if($action == 'show'){
			$this->load->view(__TEMPLET_FOLDER__."/views_adtype");
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
		$per_page = 15;//每一页显示的数量
		$limit = ($page-1)*$per_page;
		$limit.=",{$per_page}";
		$sql_count = "SELECT COUNT(*) AS tt FROM {$this->table_}extra_adtype ";
		$total  = $this->M_common->query_count($sql_count);
		$page_string = $this->common_page->page_string($total, $per_page, $page);
		$sql_adtype = "SELECT * FROM {$this->table_}extra_adtype order by id desc limit  {$limit}";	
		$list = $this->M_common->querylist($sql_adtype);
		foreach($list as $k=>$v){
			$list[$k]['status'] = ($v['status'] == 1 )?"开启":'<font color="red">关闭</font>';
			$list[$k]['addtime'] = ($v['addtime']>0)?date("Y-m-d H:i:s",$v['addtime']):'';	
			$list[$k]['updatetime'] = ($v['updatetime']>0)?date("Y-m-d H:i:s",$v['updatetime']):'';			
		}
		echo result_to_towf_new($list, 1, '成功', $page_string) ;
	}
	//编辑页面
	function edit(){
		$action = $this->input->get_post("action");		
		$action_array = array("edit","doedit","dostatus");
		$action = !in_array($action,$action_array)?'edit':$action ;		
		if($action == 'edit'){
			$id = verify_id($this->input->get_post("id"));
			$sql_adtype = "SELECT * FROM {$this->table_}extra_adtype WHERE id = '{$id}'";
			$info = $this->M_common->query_one($sql_adtype);
			if(empty($info)){
				showmessage("暂无数据","adtype/index",3,0);
				exit();
			}	
			$data = array(
				'info'=>$info
			);
			$this->load->view(__TEMPLET_FOLDER__."/views_adtype_edit",$data);		
		}elseif($action == 'doedit'){
			$this->doedit();
		}elseif($action == "dostatus"){
			$this->dostatus();
		}

	}
	//处理编辑数据
	private function doedit(){
		$id = verify_id($this->input->get_post("id"));
		$status = verify_id($this->input->get_post("status"));
		$typename = dowith_sql(daddslashes(html_escape(strip_tags($this->input->get_post("typename")))));//typename
		if(empty($typename)){
			showmessage("类别名称不能为空","adtype/edit",3,0,"?id=3");
			exit();
		}
		$time = time(); 
		$sql_edit = "UPDATE {$this->table_}extra_adtype SET `typename` = '{$typename}' , `status` = '{$status}',`updatetime` = '{$time}'  where id = '{$id}'";
		$num = $this->M_common->update_data($sql_edit);
		if($num>=1){
			//
			write_action_log($sql_edit,$this->uri->uri_string(),login_name(),get_client_ip(),1,"修改类别为{$typename}成功");
			header("Location:".site_url("adtype/index/"));
		}else{
			write_action_log($sql_edit,$this->uri->uri_string(),login_name(),get_client_ip(),0,"修改类别为{$typename}失败");
			showmessage("服务器繁忙，或者你没有修改任何数据","adtype/edit",3,0,"?id={$id}");
			die();
		}
	}
	//修改状态
	private function dostatus(){
		$status  = verify_id($this->input->get_post("status",true));
		$ids = $this->input->get_post("ids");
		$id = '' ;
		$id = get_ids($ids) ; 
		if(!$id){
			echo result_to_towf_new("", 0, "请选择修改", null);
			die();
		}
		$sql_edit = "UPDATE `{$this->table_}extra_adtype` SET `status` = '{$status}' WHERE `id` in ($id)  " ; 
		$num = $this->M_common->del_data($sql_edit);
		if($num >= 1 ){
			echo result_to_towf_new("", 1, "修改成功", null);
		}else{
			echo result_to_towf_new("", 0, "修改失败,请重新尝试", null);
		}				
	}
	
	///角色增加
	 function add(){
		$action = $this->input->get_post("action");		
		$action_array = array("add","doadd");
		$action = !in_array($action,$action_array)?'show':$action ;	
		if($action == 'show'){			
			$this->load->view(__TEMPLET_FOLDER__."/views_adtype_add");		
		}elseif($action == 'doadd'){
			$this->doadd();
		}
	}
	//处理增加
	private function doadd(){
		$typename = dowith_sql(daddslashes(html_escape(strip_tags($this->input->get_post("typename")))));//rolename
		$status = verify_id($this->input->get_post("status")); //状态	
		if(empty($typename)){
			showmessage("类别名称不能为空","adtype/add",3,0);
			exit();
		}
		$data = array(
			'typename'=>$typename,
			'status'=>$status,
			'addtime'=>time(),
			'updatetime'=>time(),
		);
		$array = $this->M_common->insert_one("{$this->table_}extra_adtype",$data);
		if($array['affect_num']>=1){
			write_action_log($array['sql'],$this->uri->uri_string(),login_name(),get_client_ip(),1,"添加广告类型为{$typename}成功");
			header("Location:".site_url('adtype/index'));
			exit();
		}else{
			write_action_log($array['sql'],$this->uri->uri_string(),login_name(),get_client_ip(),0,"添加广告类型为{$typename}失败");
			showmessage("添加广告类型失败","adtype/index",3,0);
			exit();
		}
	}

	
	//删除广告类型
	public function del(){
		$ids = $this->input->get_post("ids");
		$id = '' ;
		$id = get_ids($ids) ;
		if(!$id){
			echo result_to_towf_new("", 0, "请选择删除", null);
			die();
		}
		$sql_del = "DELETE FROM `{$this->table_}extra_adtype` WHERE id in ($id) AND `status` = 0 " ;
		$num = $this->M_common->del_data($sql_del);
		if($num >= 1 ){
			echo result_to_towf_new("", 1, "删除成功", null);
		}else{
			echo result_to_towf_new("", 0, "删除失败,只能删除无效的广告类型", null);
		}
	}
}