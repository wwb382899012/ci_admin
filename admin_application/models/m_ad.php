<?php
/*
 *ad model 文件
 *@author 王建 
 */
class M_ad extends M_common {
	function M_ad(){
		parent::__construct();	
	}

	//生成下拉框
	function query_ad_type(){
		$sql_ad_type = "SELECT id,typename  FROM {$this->table_}extra_adtype where `status` = 1 ORDER by id desc " ;
		$list = $this->M_common->querylist($sql_ad_type);			
		return $list ;
	}
	
	

}