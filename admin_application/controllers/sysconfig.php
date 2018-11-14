<?php 
/*
 *系统基本信息配置
 *author 王建 
 */
if (! defined('BASEPATH')) {
    exit('Access Denied');
}
class Sysconfig extends MY_Controller {
	private $group_ = array() ;
	private $type = array();
	private $sysconfig_cache_path = '' ;
	function Sysconfig(){
		parent::__construct();
		$this->load->model('M_common');
		$this->group_ =config_item("web_group");
		$this->type = config_item("web_type");
		$this->sysconfig_cache_path = config_item("sysconfig_cache"); 
	}

	function index(){
		$action_array = array(
			'config','add_config','get_data'
		);
		$action = $this->input->get_post("action");
		$action = (isset($action) && in_array($action,$action_array))?$action:'config';
		if($action == 'config'){
			$gid = 0 ;	
			$gid = intval($this->input->get_post("gid"));
			if($gid==0){
				if($this->group_){
					$index = 0 ;
					foreach($this->group_ as $k=>$v){
						if($index >=1){break ;}
						$gid = $k ;
						$index++ ;
					}
				}				
			}
			$sql_gid = "SELECT * FROM {$this->table_}common_sysconfig where groupid = '{$gid}'";
			$list_data = array();
			$list_data = $this->M_common->querylist($sql_gid);
			if($list_data){
				foreach($list_data as $k1=>$v1){
				
					$text = '' ;
					if(in_array($v1['type'],array('number','string'))){
						$text = "<input type='text' name='{$v1['varname']}' value='{$v1['value']}'>";
					}elseif($v1['type'] == 'boolean'){
						if($v1['value'] == 'Y'){
							$text = "是<input type='radio' name='{$v1['varname']}' value='Y' checked='checked'>否<input type='radio' name='{$v1['varname']}' value='N'>";
						}else{
							$text = "是<input type='radio' name='{$v1['varname']}' value='Y'>否<input type='radio' name='{$v1['varname']}' value='N' checked='checked'>";
						}
						
					}elseif($v1['type'] == 'textarea'){
						$text = "<textarea name='{$v1['varname']}' style='width:360px'>{$v1['value']}</textarea>";
					}
					$list_data[$k1]['text'] = $text ;
				}
				
				
			}

			$data = array(
				'group'=>$this->group_ ,
				'list'=>$list_data,
				'gid'=>$gid
			);
			$this->make();
			$this->load->view(__TEMPLET_FOLDER__."/views_sysconfig",$data);			
		}elseif($action == 'get_data'){
			$gid = intval($this->input->get_post("id"));
			//添加系统变量
			$add_data = array(
					'gid'=>$gid,
					'group'=>$this->group_,
					'type'=>$this->type
			);
			$this->load->view(__TEMPLET_FOLDER__."/views_sysconfig_add",$add_data);		
			
		}

	}
	//function add
	function add(){		
		$gid =verify_id($this->input->get_post("gid"));
		$varname = dowith_sql(daddslashes(html_escape(strip_tags($this->input->get_post("varname")))));//varname
		$value = dowith_sql(daddslashes(html_escape(strip_tags($this->input->get_post("value")))));//value
		$info = dowith_sql(daddslashes(html_escape(strip_tags($this->input->get_post("info")))));//info
		$type = (daddslashes(html_escape(strip_tags($this->input->get_post("type")))));//value
		if(!array_key_exists($type,$this->type)){
			$type = 'string';
		}
		$data = array(
			'varname'=>$varname,
			'value'=>$value,
			'info'=>$info,
			'type'=>$type,
			'groupid'=>$gid
		);
		if(empty($varname)){
			exit('varname is empty');
		}
		if(utf8_str($varname) != 1 ){
			showmessage("变量名称必须是英文","sysconfig/index",3,0,"?action=get_data&gid=true");
			exit();		
		}
		$sql_one = "SELECT * FROM {$this->table_}common_sysconfig WHERE varname = '{$varname}' limit 1 ";
		
		if($this->M_common->query_one($sql_one)){
			showmessage("对不起你要添加的数据已经存在","sysconfig/index",3,0,"?action=get_data&gid=true");
			exit();
		}
		$array = $this->M_common->insert_one("{$this->table_}common_sysconfig",$data);
		if($array['affect_num']>=1){
			write_action_log($array['sql'],$this->uri->uri_string(),login_name(),get_client_ip(),1,"添加系统变量{$varname}成功");
			$this->make();
			showmessage("添加成功","sysconfig/index",3,1,"?gid={$gid}");
			exit();
		}else{
			write_action_log($array['sql'],$this->uri->uri_string(),login_name(),get_client_ip(),0,"添加系统变量{$varname}失败");
			showmessage("服务器繁忙请稍候","sysconfig/index",3,0);
			exit();
		}
	}
	
	function edit(){
		$gid =verify_id($this->input->get_post("gid"));
		if($_POST){
			foreach($_POST as $last_key=>$last_val){
				$last_val = daddslashes(html_escape(strip_tags($last_val)));
				$last_key = daddslashes(html_escape(strip_tags($last_key)));
				$sql_ = "UPDATE `{$this->table_}common_sysconfig` SET `value` = '{$last_val}' WHERE `varname` = '{$last_key}'";
				$this->M_common->update_data($sql_);				
			}
			$this->make();
			write_action_log("sysconfig_update",$this->uri->uri_string(),login_name(),get_client_ip(),1,"修改系统变量成功");
			showmessage("修改成功","sysconfig/index",3,1,"?gid={$gid}");
		}else{
			write_action_log("sysconfig_update",$this->uri->uri_string(),login_name(),get_client_ip(),0,"修改系统变量失败");
			showmessage("请传递正确的参数","sysconfig/index",3,0);
		}
	}
	//生成
	private function make(){
		$sql_gid = "SELECT * FROM {$this->table_}common_sysconfig";
		$list_data = array();
		$list_data = $this->M_common->querylist($sql_gid);
		if(!is_really_writable(dirname($this->sysconfig_cache_path))){				
			exit("目录".dirname($this->sysconfig_cache_path)."不可写,或者不存在");
		}
		
		if(!file_exists($this->sysconfig_cache_path)){
			mkdir($this->sysconfig_cache_path);
		}
		$configfile = $this->sysconfig_cache_path."/sysconfig.inc.php";
		$fp = fopen($configfile,'w');
		flock($fp,3);
		fwrite($fp,"<"."?php\r\n");
		fwrite($fp,"/*网站基本信息配置*/\r\n");
		fwrite($fp,"/*author wangjian*/\r\n");
    	fwrite($fp,"/*time 2014_03_03*/\r\n");
		if($list_data){
			foreach($list_data as $j_key=>$j_val){
				$value = daddslashes($j_val['value']);
				if($j_val['type'] == 'number'){
					$value = intval($j_val['value']);
				}
					
				fwrite($fp,"\${$j_val['varname']} ='{$value}';\r\n");
			}
		}
	}
}




