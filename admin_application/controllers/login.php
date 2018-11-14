<?php
/*
 *登录控制器
 *author 王建 
 */
if (! defined('BASEPATH')) {
    exit('Access Denied');
}

class Login extends CI_Controller{
	private $table_ ; //表的前缀
	function Login(){
		parent::__construct();
		$this->load->model('M_common');
		$this->table_ =table_pre('real_data');
	}
	function index(){
		//判断用户是否登录 如果登录直接跳转到后台首页
		@ob_clean() ;
		@session_start();				
		$data = array() ; 
		$data = decode_data() ; 
		if(isset($data['username']) && $data['username'] != ""){
			header("Location:".site_url("admin/index"));
		}
						
		$this->load->view(__TEMPLET_FOLDER__."/views_login");
	}
	function dologin(){
		$username = dowith_sql(daddslashes(html_escape(strip_tags(trim($this->input->get_post("username"))))));//name
		$password = (daddslashes(html_escape(strip_tags(trim($this->input->get_post("passwd"))))));//passwd	
		if(config_item("yzm_open")){
			$yzm = (daddslashes(html_escape(strip_tags($this->input->get_post("yzm")))));//验证码	
			@ob_clean() ;
			@session_start();			
			if(strtolower($_SESSION['code']) != strtolower($yzm) ){
				showmessage("验证码错误","login",3,0);
				exit();
			}
		}
		if(empty($username) || empty($password)){
			showmessage("用户名或者密码不可以为空","login",3,0);
			exit();
		}		
		$password = md5($password);
		$sql_user = "SELECT * FROM {$this->table_}common_system_user where username = '{$username}' and passwd = '{$password}' and status = 1 limit 1 ";
		$info = $this->M_common->query_one($sql_user);
		if(empty($info)){
			showmessage("用户不存在或者已经被禁用","login",3,0);
			exit();
		}
		$gid = intval($info['gid']);
		$group_name = '' ;
		$sql_role = "SELECT rolename FROM {$this->table_}common_role where id = '{$gid}' limit 1 ";
		$role_info = $this->M_common->query_one($sql_role);
		
		$group_name = ($info['super_admin'] == 1 )?'超级管理员':(isset($role_info['rolename'])?$role_info['rolename']:'');
		if($group_name == "" ){
			showmessage("此用户可能没加入到系统组里面,请联系管理员!!","login",3,0);
			die();
		}
		
		//登录成功

		//write cookie
		$data_cookie = array() ; 
		$data_string = '' ;
		$data_cookie = array(
			'username'=>$info['username'],
			'client_ip'=>get_client_ip(),
			'group_name'=>$group_name , 
			'role_id'=>$gid, 
			'admin_id'=>$info['id'],
			'isadmin'=>($info['super_admin'] == 1 )?true:false,
		) ; 
		$data_string = serialize($data_cookie) ; 
		$data_string = auth_code($data_string , "ENCODE" , config_item("s_key"));
		setcookie("admin_auth",$data_string,time()+config_item("cookie_expire"),config_item("cookie_path"),config_item("cookie_domain"));
		//写入日志文件
		write_action_log("login_sql",$this->uri->uri_string(),$username,get_client_ip(),1,"用户{$username}登录成功");
		//写入登陆日志记录表里面
		##############################################################################
		$data_login_log = array(
			'username'=>$username,
			'logintime'=>date("Y-m-d H:i:s",time()),
			'ip'=>get_client_ip(),//登陆ip地址
		);
		$this->M_common->insert_one("{$this->table_}common_adminloginlog",$data_login_log);
		###############################################################################
		redirect("admin/index");
	}
	public function login_out(){
		if(isset($_COOKIE['admin_auth']) && $_COOKIE['admin_auth'] ){
			setcookie("admin_auth","",time()-config_item("cookie_expire"),config_item("cookie_path"),config_item("cookie_domain"));
			
		}
		redirect("login/index");
	
	}
	//生成验证码
	function code(){
		$this->load->library("code",array(
			'width'=>80,
			'height'=>35,
			'fontSize'=>20,
			'font'=>__ROOT__."/".APPPATH."/fonts/font.ttf"
		));
		$this->code->show();
		//echo $this->code->getCode();		
	}
	//校验验证码
	function check_code(){
		@ob_clean() ;
	    @session_start() ;
		$yzm = daddslashes(html_escape(strip_tags($this->input->get_post("code"))));//code
		if(strtolower($_SESSION['code']) != strtolower($yzm) ){
			//showmessage("验证码错误","login",3,0);
			exit('验证码不正确');
		}
		exit('success');
	}

}