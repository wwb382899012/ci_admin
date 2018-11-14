<?php
/*
 *广告管理
 *author 王建 
 *time 2014-06-12
 */
if (! defined('BASEPATH')) {
    exit('Access Denied');
}
class Ad extends MY_Controller{
	public $upload_path = '' ;
	public  $v_upload_path ; //访问的路径 
	public $name = '' ; 
	public $ad_type = '' ; //广告类型
	public $type = '' ; //0或者1
	public $pic_des =  ''  ; //图片描述
	public $pic_url = '' ;//连接地址
	public $words = '' ; //文字描述
	public $begin_date = '' ; //开始日期
	public $end_date = '' ;//结束日期
	public $status = '' ;//状态
	function Ad(){
		parent::__construct();
		$this->load->model('M_common');
		$this->upload_path = config_item("ad_path") ; ; // 广告图片位置
		$this->v_upload_path = base_url().config_item("v_ad_path");  //访问路径
	}
	function index(){
		$action = $this->input->get_post("action");	
		$action_array = array("show","ajax_data");
		$action = !in_array($action,$action_array)?'show':$action ;
		if($action == 'show'){		
			$this->load->model('M_ad');
			$type_array = $this->M_ad->query_ad_type();	
			$data = array(
				'list'=>$type_array
			);						
			$this->load->view(__TEMPLET_FOLDER__."/views_ad",$data);
		}elseif($action == 'ajax_data'){
			$this->ajax_data();
		}
	}
	//ajax get data
	private function ajax_data(){
		$page = intval($this->input->get_post("page"));
		$name = daddslashes(html_escape(strip_tags(trim($this->input->get_post("name")))));	
		$ad_type = verify_id(trim($this->input->get_post("ad_type")));
		$type = trim($this->input->get_post("type"));
		$status = trim($this->input->get_post("status"));
		$beginDate = daddslashes(html_escape(strip_tags(trim($this->input->get_post("beginDate")))));//开始日期
		$enddate = daddslashes(html_escape(strip_tags(trim($this->input->get_post("enddate")))));//结束日期
		$this->load->library("common_page");
		if($page <=0 ){
			$page = 1 ;
		}
		$per_page = 15;//每一页显示的数量
		$limit = ($page-1)*$per_page;
		$limit.=",{$per_page}";
		$where = ' where 1= 1 ';
		if(in_array($type, array('1','0',true))){
			$where.=" AND `type` = '{$type}' ";
		}
		if($ad_type){
			$where.=" AND `ad_type` = '{$ad_type}' ";
		}
		
		if($name){
			$where.=" AND `name` like '%{$name}%' ";
		}
		if(in_array($status,array('0','1'),true)){
			$where.=" AND `status` = '{$status}' ";
		}
		if($beginDate && !$enddate){
			$where.=" AND  `begin_date` >=".strtotime($beginDate); 
		}elseif($beginDate && $enddate){
			$where.=" AND  `begin_date` >=".strtotime($beginDate)." AND `end_date` <=".(strtotime($enddate)+24*60*60);
		}elseif(!$beginDate && $enddate){
			$where.=" AND `end_date` <=".(strtotime($enddate)+24*60*60);
		}
		
		$sql_count = "SELECT COUNT(*) AS tt FROM {$this->table_}extra_ad as a  {$where} ";
		$total  = $this->M_common->query_count($sql_count);
		$page_string = $this->common_page->page_string($total, $per_page, $page);
		$sql_ad = "SELECT *  FROM {$this->table_}extra_ad as a {$where} order by id desc limit  {$limit}";	
		$list = $this->M_common->querylist($sql_ad);
		if($list){
			foreach($list as $k=>$v){
				$list[$k]['status'] = ($v['status'] == 1 )?"开启":'<font color="red">关闭</font>';
				$list[$k]['addtime'] = isset($v['addtime'])?date("Y-m-d H:i",$v['addtime']):'' ; 
				$list[$k]['begin_date'] = (isset($v['begin_date']) && $v['begin_date'] > 0 )?date("Y-m-d H:i",$v['begin_date']):'暂无' ; 
				$list[$k]['end_date'] = (isset($v['end_date']) && $v['end_date'] > 0 )?date("Y-m-d H:i",$v['end_date']):'暂无' ; 
				$list[$k]['type'] = ($v['type'] == 1 )?'文字广告':'图片广告';
				$pic_url =$this->v_upload_path.$v['pic'] ; 
				$list[$k]['pic'] = ($v['type'] == 0 )?"<img src='{$pic_url}' width=\"150\" height='90'>":'';
				$list[$k]['words'] = msubstr($v['words'],0,10,abslength($v['words']));				
			}		
		}

		echo result_to_towf_new($list, 1, '成功', $page_string) ;
	}
	
	//添加新闻数据
	function add(){		
		$action = $this->input->get_post("action");		
		$action_array = array("add","doadd");
		$action = !in_array($action,$action_array)?'add':$action ;	
		if($action == 'add'){
			$this->load->model('M_ad');
			$this->load->helper(array('form', 'url'));					
			$result = $this->M_ad->query_ad_type();//广告类别
			$data = array(
				'ad_type'=>$result,
			);		
			$this->load->view(__TEMPLET_FOLDER__."/views_ad_add",$data);		
		}elseif($action =='doadd'){			
			$this->doadd();
		}
	}
	//set params
	private function set_params(){
		$this->name = html_escape($this->input->get_post("name",true)); //广告名称
		$this->ad_type = verify_id($this->input->get_post("ad_type",true));//广告分类
		$this->type = verify_id($this->input->get_post("type",true));//广告类别
		$this->pic_des = daddslashes(html_escape(strip_tags($this->input->get_post("pic_des",true))));//图片描述
		$this->pic_url = html_escape($this->input->get_post("pic_url",true));//连接地址
		$this->words =  html_escape($this->input->get_post("words",true)); //文字描述 
		$this->begin_date = (stripos(html_escape($this->input->get_post("begin_date",true)), "-") !== false )?html_escape($this->input->get_post("begin_date",true)):0;
		$this->end_date =  (stripos(html_escape($this->input->get_post("end_date",true)), "-") !== false )?html_escape($this->input->get_post("end_date",true)):0;
		$this->status = verify_id($this->input->get_post("status",true));
	}
	//处理添加
	private function doadd(){	
		$this->set_params() ; 
		if($this->name == "" ){
			showmessage("广告名称不能为空","ad/add",3,0,"");
			exit();
		}
		$this->load->library("common_upload");
		$pic  = $this->common_upload->upload_path($this->upload_path,"pic") ; 	
		$data = array() ; 
		
		$data = array(
			'name'=>$this->name , 
			'pic'=>$pic , 
			'pic_des'=>$this->pic_des , 
			'pic_url'=>$this->pic_url , 
			'words'=>$this->words ,			
			'ad_type'=>$this->ad_type , 
			'type'=>$this->type ,
			'status'=>$this->status , 
			'addtime'=>time() ,
			'begin_date'=>strtotime($this->begin_date) , 
			'end_date'=>strtotime($this->end_date) ,
			'add_person'=>$this->username , 			
		);	
		$array = $this->M_common->insert_one("{$this->table_}extra_ad",$data);
		if($array['affect_num']>=1){
			write_action_log($array['sql'],$this->uri->uri_string(),login_name(),get_client_ip(),1,"添加广告为{$this->name}成功");
			header("Location:".site_url("ad/index"));
		}else{
			write_action_log($array['sql'],$this->uri->uri_string(),login_name(),get_client_ip(),0,"添加广告为{$this->name}失败");		
			showmessage("服务器繁忙","ad/add",3,0,"");
			exit();
		}
	}
	//编辑页面
	function edit(){
		$action = $this->input->get_post("action");		
		$action_array = array("edit","doedit","dostatus");
		$action = !in_array($action,$action_array)?'edit':$action ;		
		if($action == 'edit'){
			$this->load->helper(array('form', 'url'));			
			$id = verify_id($this->input->get_post("id"));//数据
			$sql_= "SELECT a.* FROM {$this->table_}extra_ad as a where a.id = '{$id}'";					
			$info_ = $this->M_common->query_one($sql_);
			if(empty($info_)){
				showmessage("参数错误","ad/index",3,0);
				exit();
			}		
			
			$this->load->model('M_ad');
			$type_array = $this->M_ad->query_ad_type();			
			$data = array(
					'info'=>$info_,	
					'id'=>$id,
					'ad_type'=>$type_array		
			);				
			$this->load->view(__TEMPLET_FOLDER__."/views_ad_edit",$data);	
			
		}elseif($action == 'doedit'){
			$this->doedit();
		}elseif($action == "dostatus"){
			$this->dostatus();
		}

	}
	//处理编辑数据
	private function doedit(){
		$this->set_params() ; 
		if($this->name == "" ){
			showmessage("广告名称不能为空","ad/add",3,0,"");
			exit();
		}
		$is_update_pic = '' ; 
		$this->load->library("common_upload");
		$pic  = $this->common_upload->upload_path($this->upload_path,"pic") ; 	
		
		if($pic){
			$is_update_pic = " `pic` = '{$pic}' ," ;
		} 
		$id = verify_id($this->input->get_post("id"));
		$sql_= "SELECT a.* FROM {$this->table_}extra_ad as a where a.id = '{$id}' limit 1 ";					
		$info_ = $this->M_common->query_one($sql_);
		if(!$info_){
			showmessage("暂无数据","ad/edit",3,0,"?id={$id}");
			exit();
		}
		$this->end_date = strtotime($this->end_date) ; 
		$this->begin_date = strtotime($this->begin_date) ;
		$time = time();
		$sql_edit = "UPDATE `{$this->table_}extra_ad` SET 
		`name` = '{$this->name}',
		$is_update_pic
		`pic_des`	=	'{$this->pic_des}',
		`pic_url`	=	'{$this->pic_url}',
		`words`	=	'{$this->words}',
		`ad_type`	=	'{$this->ad_type}' ,
		`type`		=	'{$this->type}' , 
		`status` = '{$this->status}',
		`begin_date`	=	'{$this->begin_date}',
		`end_date` = '{$this->end_date}'
		 where id = '{$id}'";
		$ad_array = $this->M_common->query_one("SELECT `pic` FROM `{$this->table_}extra_ad` WHERE id = '{$id}' LIMIT 1  ") ; 
		$num = $this->M_common->update_data($sql_edit);
		if($num>=1){
			if(isset($ad_array['pic']) && $ad_array['pic'] && file_exists($this->upload_path)."/".$ad_array['pic'] && $pic ){
				@unlink($this->upload_path."/".$ad_array['pic']); 
			}
			write_action_log($sql_edit,$this->uri->uri_string(),login_name(),get_client_ip(),1,"修改广告为{$this->name}成功");
			header("Location:".site_url("ad/index"));		
		}else{
			write_action_log($sql_edit,$this->uri->uri_string(),login_name(),get_client_ip(),0,"修改广告为{$this->name}失败");			
			showmessage("服务器繁忙，或者你没有修改任何数据","ad/edit",3,0,"?id={$id}");
		}
	}
	//删除
	public function del(){
		$ids = $this->input->get_post("ids");
		$id = '' ;
		$id = get_ids($ids) ;
		if(!$id){
			echo result_to_towf_new("", 0, "请选择删除", null);
			die();
		}
		$pic_array = $this->M_common->querylist("SELECT `pic` FROM `{$this->table_}extra_ad` WHERE id in ($id) AND `status` = 0")  ;
		$sql_del = "DELETE FROM `{$this->table_}extra_ad` WHERE id in ($id) AND `status` = 0 " ;
		$num = $this->M_common->del_data($sql_del);
		if($num >= 1 ){
			//删除图片
			if($pic_array){
				foreach($pic_array as $p_k => $p_v){
					if(file_exists($this->upload_path."/".$p_v['pic'])){
						@unlink($this->upload_path."/".$p_v['pic']);
					}
				}
			}
			echo result_to_towf_new("", 1, "删除成功", null);
		}else{
			echo result_to_towf_new("", 0, "删除失败,只能删除无效的广告数据", null);
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
		$sql_edit = "UPDATE `{$this->table_}extra_ad` SET `status` = '{$status}' WHERE `id` in ($id)  " ; 
		$num = $this->M_common->del_data($sql_edit);
		if($num >= 1 ){
			echo result_to_towf_new("", 1, "修改成功", null);
		}else{
			echo result_to_towf_new("", 0, "修改失败,请重新尝试", null);
		}				
	}
}
//file end