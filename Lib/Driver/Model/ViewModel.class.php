<?php
!defined(START_PATH)||_404();
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-12-14
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/

/**
 * 视图关联模型处理类
 * @author 舒良府
 */
class ViewModel extends Model{
	public $view=array(); 		// 关联条件
	private $fields=array();	// 关联表的字段
	
	private function checkView($set){
		$type=empty($set['type'])?'inner':$set['type'];
		if(!in_array(strtoupper($type), array('INNER','LEFT','RIGHT'))){
			error('关联定义规则[type]参数设置错误，必须是(inner,left,right)之中！！');
		}
		if(empty($set['on'])){
			error('关联定义规则[on]参数不能为空！！');
		}
		return true;
	}
	
	/**
	 * 要查询的字段
	 * @see Model::field()
	 */
	public function field($args){
		$fields=array();
		if(is_string($args)){
			$fields=explode(',', $args);
		}elseif (is_array($args)){
			$fields=$args;
		}
		$this->fields=$fields;
		return $this;
	}
	
	/**
	 * 关联查询
	 * @see Model::select()
	 */
	public function select(){
		$args=func_get_args();
		$where=isset($args[0])?$args[0]:'';
		$view=filterArr($this->view);
		if(empty($view)){
			$this->init();
			return call_user_func(array($this->db,__FUNCTION__),$where);
		}
		$query='SELECT '.implode(',', $this->joinField).$this->opt['count'].' FROM '.$this->table.' ';
		$condition=$this->opt['group'].$this->opt['having'].$this->opt['where'].$this->opt['order'].$this->opt['limit'];
		foreach ($this->view as $table=>$set){
			if(!$this->checkView($set))continue;
			$this->table($table,true);
		}
		if(!empty($this->fields)){
			$field=array();
			foreach ($this->fields as $f){
				foreach ($this->joinField as $jf){
					if(preg_match('/'.$f.'$/', $jf)){
						$field[]=$jf;
					}
				}
			}
			$this->db->joinField=$field;
		}
		$join='';
		foreach ($this->view as $t=>$v){
			$t=$this->dbPrefix.str_replace($this->dbPrefix, '', $t);
			$join.=(isset($v['type'])?strtoupper($v['type']):'INNER').' JOIN '.$t.' ON '.$v['on'].' ';
		}
		$query.=$join.' '.$condition;
		if(isset($args[1])&&$args[1]===true){
			$this->init();
			return $query;
		}
		if(!!$data=$this->query($query)){
			$this->db->lastSql=$query;
			$this->init();
			return $data;
		}
	}
	
	/**
	 * 创建视图
	 * @param string $view
	 * @return bool
	 */
	public function createView($view){
		$seleteQuery=$this->select('',true);
		$query='SHOW TABLE STATUS WHERE COMMENT="VIEW"';
		$allView=$this->query($query);
		foreach ($allView as $v){
			if($v['Name']==$view){
				error($view.'视图已经存在，如需要修改视图请执行updateView()方法！');
			}
		}
		$query='CREATE VIEW '.$view.' AS '.$seleteQuery;
		
		return $this->exe($query);
	}
	
	/**
	 * 修改视图
	 * @param string $view
	 * @return bool
	 */
	public function updateView($view){
		$seleteQuery=$this->select('',true);
		$query='SHOW TABLE STATUS WHERE COMMENT="VIEW"';
		$allView=$this->query($query);
		foreach ($allView as $v){
			if($v['Name']==$view){
				$this->exe('DROP VIEW '.$view);
			}
		}
		$query='CREATE VIEW '.$view.' AS '.$seleteQuery;
		
		return $this->exe($query);
	}
	
	/**
	 * 删除视图
	 * @param string $view
	 * @return bool
	 */
	public function deleteView($view){
		$seleteQuery=$this->select('',true);
		$query='SHOW TABLE STATUS WHERE COMMENT="VIEW"';
		$allView=$this->query($query);
		foreach ($allView as $v){
			if($v['Name']==$view){
				return $this->exe('DROP VIEW '.$view);
			}
		}
		error('您要删除的视图'.$view.'不存在！！');
	}
	
	
	
	
}


?>