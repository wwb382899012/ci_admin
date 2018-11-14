<?php
/*
 *后台操作日志
 *author 王建 
 */
if (! defined('BASEPATH')) {
    exit('Access Denied');
}
class Log extends MY_Controller{
	function Log(){
		parent::__construct();
		$this->load->model('M_common');
	}
	function index(){
		$action = $this->input->get_post("action");	
		$action_array = array("show","ajax_data");
		$action = !in_array($action,$action_array)?'show':$action ;
		if($action == 'show'){
			//查询所有的log表数据，生成select
			$table_array = $this->log_table();
			$data['table'] = $table_array ;
			$this->load->view(__TEMPLET_FOLDER__."/views_log_list",$data);
		}elseif($action == 'ajax_data'){
			$this->ajax_data();
		}
		
		
	}
	//ajax get data
	private function ajax_data(){
		$this->load->library("common_page");
		$page = intval($this->input->get_post("page"));
		$status = $this->input->get_post("status");		
		if($page <=0 ){
			$page = 1 ;
		}
		$per_page = 20;//每一页显示的数量
		$limit = ($page-1)*$per_page;
		$limit.=",{$per_page}";
		$where = ' where 1= 1 ';
		$username = daddslashes(html_escape(strip_tags($this->input->get_post("username")))) ;
		$url = daddslashes(html_escape(strip_tags($this->input->get_post("url")))) ;
		$table = daddslashes(html_escape(strip_tags($this->input->get_post("table")))) ;
		$tablename = '';
		if( in_array($table,$this->log_table())){
			$tablename = $table ;
			
		}else{
			echo result_to_towf_new('',0,'<font color="red">表不存在，请选择要查询的表</font>','');
			die();
		}
		if(!empty($url)){
			$condition = intval($this->input->get_post("condition"));
			$condition  = in_array($condition,array(1,2))?$condition:1;
			$array_condition_search  = array(
				1=>" LIKE '%{$url}%'", //模糊搜索
				2=>"= '{$url}'"
			);
			$where.=" AND `log_url` {$array_condition_search[$condition]}";
		}
		if(!empty($username)){
			$where.=" AND `log_person` LIKE '%{$username}%'";
		}
		if(in_array($status,array('1','0',true))){
			$where.=" AND `log_status` = '{$status}'"; 
		}
		$sql_count = "SELECT COUNT(*) AS tt FROM {$tablename} {$where} ";
		$total  = $this->M_common->query_count($sql_count);
		$page_string = $this->common_page->page_string($total, $per_page, $page);
		$sql_log = "SELECT * FROM {$tablename} {$where} order by log_id desc limit  {$limit}";	

		$list = $this->M_common->querylist($sql_log);
		foreach($list as $k=>$v){
			$list[$k]['log_status'] = ($v['log_status'] == 1 )?"成功":'<font color="red">失败</font>';
			
		}
		echo result_to_towf_new($list, 1, '成功', $page_string) ;
	}
	
		//获取log表的名字
	private function log_table(){
		$query_table = "show tables like '{$this->table_}common_log%'" ;	
		$res_table = $this->M_common->querylist($query_table);	
		
		$table_array = array();
		if($res_table){		
           foreach($res_table as $k_t=>$k_v){
           		foreach($k_v as $kk=>$vv){
           			$table_array[] = $vv ;
           		}
           }
			rsort($table_array)	;		   
		}
		return $table_array ;
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