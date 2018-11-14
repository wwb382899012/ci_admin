<?php
/*
 *新闻管理
 *author 王建 
 *time 2014-05-18
 */
if (! defined('BASEPATH')) {
    exit('Access Denied');
}
class News extends MY_Controller{
	public $extra_news_from =  '' ;//来源类型数组
	public $news_type_from = 8 ; //来源类型type
	public $upload_path = '' ; 
	public $upload_save_url = '' ;
	
	public  $thum_upload_path = ''; //上传图片缩略图的保存路径
	public  $v_upload_path ; //访问的路径
	public 	$news_attr = '' ; //文章属性
	public 	$title = '' ;//文章title
	public	$url = '' ;//url地址
	public	$keysword = '' ;//关键词
	public 	$introduce = '' ;//介绍
	public	$weight = '' ;//权重
	public	$content = '' ;//内容
	public	$type = '' ;//类别
	public	$from = '' ;//来源
	public	$status = '' ;//状态
	public	$typename = '' ;//类别名称
	public	$fromname = '';//来源名称
	public	$create_date = '' ;//创建日期
	public	$flag = '' ;//属性
	public	$click = '' ;//点击数量
	public	$publish_name = '' ;//发表人
	function News(){
		parent::__construct();
		$this->load->model('M_common');
		$this->cache_category_path =  config_item("category_modeldata_cache") ; 
		$this->upload_path = __ROOT__."/data/upload/k/" ; ; // 编辑器上传的文件保存的位置
		$this->upload_save_url = base_url()."/data/upload/k/"; //编辑器上传图片的访问的路径
		$this->get_news_from();
		$this->news_attr = config_item("content_att") ;
		$this->thum_upload_path = config_item("news_path");  //上传路径
		$this->v_upload_path = base_url().config_item("v_news_path");  //访问路径
	}
	function index(){
		$action = $this->input->get_post("action");	
		$action_array = array("show","ajax_data","preview","upload");
		$action = !in_array($action,$action_array)?'show':$action ;
		if($action == 'show'){
			$this->load->model('M_news');			
			$list = $this->M_news->make_option_data();
			$data = array(
				'from'=>$this->extra_news_from,
				'list'=>$list
			);			
			
			$this->load->view(__TEMPLET_FOLDER__."/views_news",$data);
		}elseif($action == 'ajax_data'){
			$this->ajax_data();
		}elseif($action == "preview" ){
			$this->preview() ;
		}elseif($action == "upload" ){
			$this->upload() ;
		}	
	}
	//ajax get data
	private function ajax_data(){
		$page = intval($this->input->get_post("page"));
		$title = daddslashes(html_escape(strip_tags(trim($this->input->get_post("title")))));	
		$flag = daddslashes(html_escape(strip_tags(trim($this->input->get_post("attr")))));	
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
		$type = verify_id($this->input->get_post("type"));//类型ID
		if($type){
			$where.=" AND type = '{$type}' ";
		}
		$from  = verify_id($this->input->get_post("from"));//from
		if($from){
			$where.=" AND `from` = '{$from}' " ;
		}
		if($title){
			$where.=" AND title like '%{$title}%' ";
		}
		if($flag){
			$where.=" AND `flag` like '%{$flag}%' ";
		}
		if($beginDate && !$enddate){
			$where.=" AND  `create_date` >=".strtotime($beginDate); 
		}elseif($beginDate && $enddate){
			$where.=" AND  `create_date` >=".strtotime($beginDate)." AND `create_date` <=".(strtotime($enddate)+24*60*60);
		}elseif(!$beginDate && $enddate){
			$where.=" AND `create_date` <=".(strtotime($enddate)+24*60*60);
		}
		
		$sql_count = "SELECT COUNT(*) AS tt FROM {$this->table_}extra_news as a  {$where} ";
		$total  = $this->M_common->query_count($sql_count);
		$page_string = $this->common_page->page_string($total, $per_page, $page);
		$sql_category = "SELECT *  FROM {$this->table_}extra_news as a {$where} order by id desc limit  {$limit}";	
		$list = $this->M_common->querylist($sql_category);
		//$this->load->library("category");
		//$list = $this->category->format_category_data($list);
		if($list){
			foreach($list as $k=>$v){
				$list[$k]['status'] = ($v['status'] == 1 )?"开启":'<font color="red">关闭</font>';
				$list[$k]['introduce'] = msubstr($v['introduce'],0,20,abslength($v['introduce']));
				$list[$k]['create_date'] = isset($v['create_date'])?date("Y-m-d H:i",$v['create_date']):'' ; 
				$list[$k]['modify_date'] = isset($v['modify_date'])?date("Y-m-d H:i",$v['modify_date']):'' ; 
				$flag = $v['flag'];
				$str = '' ;
				$str = $this->news_attr($flag);
				$str="&nbsp;&nbsp;".$str;
				$list[$k]['title'] = msubstr($v['title'],0,20,abslength($v['title']));	
				$list[$k]['flag'] = $str;				
			}		
		}

		echo result_to_towf_new($list, 1, '成功', $page_string) ;
	}
	//根据新闻的标记获取新闻属性
	private function news_attr($flag){
		$str = '' ;
		$flag_array = array();
		if($flag){
			$flag_array  = explode(",", $flag) ;
			for($kk = 0 ; $kk<count($flag_array);$kk++){
					$str.=isset($this->news_attr[$flag_array[$kk]])?"<font color='red'>".$this->news_attr[$flag_array[$kk]]."</font>,":'' ;
			}
			$str = rtrim($str,",");
		}
		return $str ;
	}
	//添加新闻数据
	function add(){		
		$action = $this->input->get_post("action");		
		$action_array = array("add","doadd");
		$action = !in_array($action,$action_array)?'add':$action ;	
		if($action == 'add'){
			$this->load->model('M_news');			
			$result = $this->M_news->make_option_data();//新闻类别
			/* echo "<pre>";
			print_r($result); */
			$data = array(
				'from'=>$this->extra_news_from,
				'category'=>$result,
			);		
			$this->load->view(__TEMPLET_FOLDER__."/views_news_add",$data);		
		}elseif($action =='doadd'){			
			$this->doadd();
		}
	}
	//设置参数
	private function set_params(){
		$this->title = html_escape($this->input->get_post("title",true));
		$this->url = daddslashes(html_escape(strip_tags($this->input->get_post("url",true))));
		$this->keysword = daddslashes(html_escape(strip_tags($this->input->get_post("keysword",true))));
		$this->content = html_escape($this->input->get_post("content",true));
		$this->introduce = html_escape($this->input->get_post("introduce",true));
		if(empty($this->introduce) && !empty($this->content)){
			$this->introduce = msubstr(html_escape(strip_tags($this->content)),0,100,abslength(html_escape(strip_tags($this->content)),'utf-8',false));	
			$this->introduce= str_replace(PHP_EOL, '', $this->introduce); 
		}	
		$this->weight = verify_id($this->input->get_post("weight",true));		
		$this->type = verify_id($this->input->get_post("type",true));
		$this->from = verify_id($this->input->get_post("from",true));
		$this->status  = verify_id($this->input->get_post("status",true));
		$this->typename = daddslashes(html_escape(strip_tags($this->input->get_post("typename",true))));
		$this->fromname = daddslashes(html_escape(strip_tags($this->input->get_post("fromname",true))));
		$this->create_date = (html_escape($this->input->get_post("create_date",true)))?strtotime(html_escape($this->input->get_post("create_date",true))):time();
		$this->flag =( html_escape($this->input->get_post("flag",true)) )?(implode(",", html_escape($this->input->get_post("flag",true)))):'';
		$this->click = verify_id($this->input->get_post("click",true));		
		$this->publish_name = (daddslashes(html_escape(strip_tags($this->input->get_post("addperson",true)))) == '' )?$this->username:daddslashes(html_escape(strip_tags($this->input->get_post("addperson",true))));
		
	}
	//处理添加
	private function doadd(){	
		$this->set_params();
		if($this->title == "" ){
			showmessage("标题不能为空","news/add",3,0,"");
			exit();
		}
		$this->load->library("common_upload");
		$data = array(
			'title'=>$this->title,
			'url'=>$this->url,
			'keysword'=>$this->keysword,
			'introduce'=>$this->introduce,
			'weight'=>$this->weight,
			'content'=>$this->content,
			'type'=>$this->type,
			'from'=>$this->from,
			'status'=>$this->status,
			'typename'=>$this->typename,
			'create_date'=>$this->create_date,
			'modify_date'=>$this->create_date,
			'addperson'=>$this->username,
			'fromname'=>$this->fromname,
			'flag'=>$this->flag,
			'click'=>$this->click ,
			'image'=>$this->common_upload->upload_path($this->thum_upload_path),
			
		);		
		$array = $this->M_common->insert_one("{$this->table_}extra_news",$data);
		if($array['affect_num']>=1){
			write_action_log($array['sql'],$this->uri->uri_string(),login_name(),get_client_ip(),1,"添加标题为{$title}成功");
			header("Location:".site_url("news/index"));
		}else{
			write_action_log($array['sql'],$this->uri->uri_string(),login_name(),get_client_ip(),0,"添加标题为{$title}失败");		
			showmessage("服务器繁忙","news/add",3,0,"");
			exit();
		}
	}
	//编辑页面
	function edit(){
		$action = $this->input->get_post("action");		
		$action_array = array("edit","doedit","dostatus");
		$action = !in_array($action,$action_array)?'edit':$action ;		
		if($action == 'edit'){
			$id = verify_id($this->input->get_post("id"));//数据
			$sql_= "SELECT a.* FROM {$this->table_}extra_news as a where a.id = '{$id}'";					
			$info_ = $this->M_common->query_one($sql_);
			if(empty($info_)){
				showmessage("参数错误","news/index",3,0);
				exit();
			}		
			
			$this->load->model('M_news');
			$result = $this->M_news->make_option_data();		
			//echo "<pre>";
			//print_r($result);	
			$data = array(
					'info'=>$info_,	
					'id'=>$id,
					'from'=>$this->extra_news_from,
					'category'=>$result		
			);				
			$this->load->view(__TEMPLET_FOLDER__."/views_news_edit",$data);	
			
		}elseif($action == 'doedit'){
			$this->doedit();
		}elseif($action == "dostatus"){
			$this->dostatus();
		}

	}
	//处理编辑数据
	private function doedit(){
		$id  = verify_id($this->input->get_post("id",true));
		$this->set_params();
		if($this->title == "" ){	
			showmessage("标题不能为空","extra_news/edit",3,0,"?id={$id}");
			exit();
		}
		$sql_= "SELECT a.* FROM {$this->table_}extra_news as a where a.id = '{$id}' limit 1 ";					
		$info_ = $this->M_common->query_one($sql_);
		if(!$info_){
			showmessage("暂无数据","extra_news/edit",3,0,"?id={$id}");
			exit();
		}
		$this->load->library("common_upload");
		$is_set_image = '' ;
		$image =$this->common_upload->upload_path($this->thum_upload_path) ; 
		if($image){
			$is_set_image = " `image` = '{$image}' , " ;
		}
		$time = time();
		$sql_edit = "UPDATE `{$this->table_}extra_news` SET 
		`status` = '{$this->status}',
		{$is_set_image}
		`title` = '{$this->title}',
		`url` 	=	'{$this->url}',
		`keysword`	=	'{$this->keysword}',
		`introduce`	=	'{$this->introduce}',
		`weight`	=	'{$this->weight}',
		`content`	=	'{$this->content}' ,
		`type`		=	'{$this->type}' , 
		`from`		=	'{$this->from}',
		`modify_date`	=	'{$time}',
		`create_date` = '{$this->create_date}',
		`typename`		=	'{$this->typename}',
		`fromname` = '{$this->fromname}' ,
		`flag` = '{$this->flag}',
		`click` = '{$this->click}' ,
		`addperson` = '{$this->publish_name}'
		 where id = '{$id}'";
		$num = $this->M_common->update_data($sql_edit);
		if($num>=1){
			if(file_exists($this->thum_upload_path."/{$info_['image']}") && $image ){
				@unlink($this->thum_upload_path."/{$info_['image']}");
			}
			write_action_log($sql_edit,$this->uri->uri_string(),login_name(),get_client_ip(),1,"修改标题为{$title}成功");
			header("Location:".site_url("news/index"));		
		}else{
			write_action_log($sql_edit,$this->uri->uri_string(),login_name(),get_client_ip(),0,"修改标题为{$title}失败");			
			showmessage("服务器繁忙，或者你没有修改任何数据","news/edit",3,0,"?id={$id}");
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
		$sql_del = "DELETE FROM `{$this->table_}extra_news` WHERE id in ($id) AND `status` = 0 " ;
		$num = $this->M_common->del_data($sql_del);
		if($num >= 1 ){
			echo result_to_towf_new("", 1, "删除成功", null);
		}else{
			echo result_to_towf_new("", 0, "删除失败,只能删除无效的新闻数据", null);
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
		$sql_edit = "UPDATE `{$this->table_}extra_news` SET `status` = '{$status}' WHERE `id` in ($id)  " ; 
		$num = $this->M_common->del_data($sql_edit);
		if($num >= 1 ){
			echo result_to_towf_new("", 1, "修改成功", null);
		}else{
			echo result_to_towf_new("", 0, "修改失败,请重新尝试", null);
		}				
	}
	
	//预览
	private function preview(){
		$id = verify_id($this->input->get_post("id"));//数据
		$sql_= "SELECT a.* FROM {$this->table_}extra_news as a where a.id = '{$id}'";					
		$info_ = $this->M_common->query_one($sql_);
		if(empty($info_)){
			showmessage("参数错误","extra_news/index",3,0);
			exit();
		}
		$data = array(
			'info'=>$info_
		);				
		$this->load->view(__TEMPLET_FOLDER__."/views_news_preview",$data);			
	}
	//上传文件
	private function upload(){
		//包含kindeditor的上传文件
		$save_path =$this->upload_path ; // 编辑器上传的文件保存的位置
		$save_url = $this->upload_save_url; //访问的路径
		include_once __ROOT__.'/'.APPPATH."libraries/JSON.php" ;
		include_once __ROOT__.'/'.APPPATH."libraries/upload_json.php" ;
	}
	//获取新闻的来源
	private function get_news_from(){	
		if(file_exists(config_item("category_modeldata_cache")."/cache_categorydata_{$this->news_type_from}.inc.php")){
			include config_item("category_modeldata_cache")."/cache_categorydata_{$this->news_type_from}.inc.php";
			$this->extra_news_from = $category_data ;
		}
	}	
	
}
//file end