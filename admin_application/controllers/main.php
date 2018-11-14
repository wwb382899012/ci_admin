<?php 
/*
 *系统基本信息配置
 *author 王建 
 */
if (! defined('BASEPATH')) {
    exit('Access Denied');
}
class Main extends MY_Controller {
	function Main(){
		parent::__construct();
		$this->load->model('M_common');
	}
	function index(){
		
		$data = array(
			'group_name'=>group_name(),
		);
	
		$this->load->view(__TEMPLET_FOLDER__."/views_main",$data);			
	}

	
}




