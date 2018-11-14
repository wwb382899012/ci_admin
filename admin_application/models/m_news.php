<?php
/*
 *news model 文件
 *@author 王建 
 */
class M_news extends M_common {
	function M_news(){
		parent::__construct();	
	}

	//生成下拉框
	function make_option_data(){
		$sql_news_type = "SELECT id,typename ,pid , type FROM {$this->table_}extra_newstype where `status` = 1 ORDER by id,disorder desc " ;
		$list = $this->M_common->querylist($sql_news_type);	
		if($list){
			$list = tree_format($list,0,0,"&nbsp;&nbsp;&nbsp;&nbsp;");		
		}		
		return $list ;
	}
	

}