<?php
!defined(START_PATH)||_404();
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-12-9
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/

/**
 * Mysql数据库基类
 * @author 舒良府
 *
 */
abstract class Db implements DbIinterface{
	protected $link = null; //数据库连接
	protected $table = null;			// 表名
	public $field;							// 字段字符串
	public $joinField=array();			// 多表关联的查询字段
	public $fieldArr=array();			// 字段数组
	public $lastquery;					// 最后发送的查询结果集
	public $pri = null;					// 默认表主键
	public $opt = array();				// SQL 操作
	public $optOld = array();			// 旧的SQL操作
	public $lastSql;						// 最后发送的SQL
	public $error = null;					// 错误信息
	public $dbPrefix;				// 表前缀
	
	/**
	 * 将字符替换为标准的SQL语法
	 * @var  array
	 */
	protected $condition = array(
			'eq'		=>	 ' = ', 
			'neq'		=>	 ' <> ',
			'gt'		=>	 ' > ', 
			'egt'		=>	 ' >= ',
			'lt'			=> 	 ' < ',
			'elt'		=>	 ' <= '
	);

	/**
	 * 数据库连接
	 * 根据配置文件获得数据库连接对象
	 * @param string $table
	 * @return Object   连接对象
	 */
	public function connect($table,$bool){
		if (is_null($this->link)||empty($table)) {
			$this->link = $this->link(); //通过数据驱动如MYSQLI连接数据库
		}
		if (!empty($table)){
			$this->dbPrefix = C("DB_PREFIX"); //表前缀
			$this->table($table,$bool);
			$this->table = $this->dbPrefix.$table;
			$this->field = $this->opt['field'];
			$this->fieldArr = $this->opt['fieldArr'];
			
			if($bool){
				$fields=array();
				foreach ($this->opt['fieldArr'] as $f){
					$fields[]=$this->dbPrefix.$table.'.'.$f;
				}
				$this->joinField=$fields;
			}else{
				$this->joinField=$this->opt['fieldArr'];
			}
			
			
			
			$this->pri = $this->opt['pri'];
			$this->optInit(); //初始始化WHERE等参数
		} else {
			$this->optInit();
		}
		return $this->link;
	}

    /**
     * 初始化表字段与主键及发送字符集
     * @param string $table 表名
     * @param bool $bool 字段是否添加前缀
     */
    public function table($table,$bool){
    	$table=$this->dbPrefix.str_replace($this->dbPrefix, '', $table);
        if (is_null($table)){
        	return false;
        }
        $this->optInit();
        $field = $this->getFields($table); //获得表结构信息设置字段及主键属性
        $this->opt['table'] = $table;
        $this->opt['pri'] = isset($field['pri']) && !empty($field['pri']) ? $field['pri'] : '';
        $this->opt['field'] = '`'.implode('`,'.'`',$field['field']).'`';
        $this->opt['fieldArr'] = $field['field'];
        if($bool){
        	$fields=array();
        	foreach ($field['field'] as $f){
        		$fields[]=$table.'.'.$f;
        	}
        	$this->joinField=array_merge($this->joinField,$fields);
        }else{
        	$this->joinField=array_merge($this->joinField,$field['field']);
        }
    }

    /**
     * 查询操作条件选项初始化
     * @access public
     * @return null
     */
    public function optInit(){
        $this->opt_old = $this->opt;
        $this->cacheTime = -1; //SELECT查询缓存时间
        $this->error = NULL;
        $opt = array(
            'table'		=> $this->table,
            'pri'			=> $this->pri,
            'field'			=> $this->field,
            'fieldArr'		=> $this->fieldArr,
            'where'		=> '',
            'like'			=> '',
            'group'		=> '',
            'having'		=> '',
        	'count'		=> '',
            'order'		=> '',
            'limit'			=> '',
            'in'				=> '',
            'cache'		=> '',
            'filter_func' => array() //对数据进行过滤处理
        );
        $this->opt = array_merge($this->opt, $opt);
    }
    

    /**
     * 获得表字段
     * @access public
     * @param string $tableName 表名
     * @return type
     */
    public function getFields($tableName){
    	$tableCache = $this->getCacheTable($tableName);
    	$tableField = array();
    	foreach ($tableCache['fields'] as $v) {
    		$tableField['field'][] = $v['field'];
    		if ($v['key']) {
    			$tableField['pri'] = $v['field'];
    		}
    	}
    	return $tableField;
    }
    
    /**
     * 获得表结构缓存  如果不存在则生生表结构缓存
     * @access public
     * @param type $tableName
     * @return array    字段数组
     */
    private function getCacheTable($table){
    	//字段缓存
    	if (C('DB_TABLA_CACHE')&&!DEBUG){
    		$cacheTableField = F($table, false, CACHE_TABLE_PATH);
    		if ($cacheTableField){
    			return $cacheTableField;
    		}
    	}
    	//获得表结构
    	$tableinfo = $this->getTableFields($table);
    	$tableFile=CACHE_TABLE_PATH.'/'.$table.'.php';
    	is_file($tableFile)&&unlink($tableFile);
    	//字段缓存
    	if (C('DB_TABLA_CACHE')){
    		F($table, $tableinfo,CACHE_TABLE_PATH);
    	}
    	return $tableinfo;
    }
    
    /**
     * 获得表结构及主键
     * 查询表结构获得所有字段信息，用于字段缓存
     * @access private
     * @param string $table
     * @return array 返回数组
     */
    private function getTableFields($table){
    	$sql = 'SHOW COLUMNS FROM '.$table;
    	$fields = $this->query($sql);
    	if ($fields === false){
    		error("表{$table}不存在", false);
    	}
    	$n_fields = array();
    	$f = array();
    	if(strtolower(C('DB_DATA_TYPE'))=='array'){
    		foreach ($fields as $res) {
    			$f ['field'] = $res ['Field'];
    			$f ['type'] = $res ['Type'];
    			$f ['null'] = $res ['Null'];
    			$f ['field'] = $res ['Field'];
    			$f ['key'] = ($res ['Key'] == "PRI" && $res['Extra']) || $res ['Key'] == "PRI";
    			$f ['default'] = $res ['Default'];
    			$f ['extra'] = $res ['Extra'];
    			$n_fields [$res ['Field']] = $f;
    		}
    		$pri = '';
    		foreach ($n_fields as $v) {
    			if ($v['key']) {
    				$pri = $v['field'];
    			}
    		}
    	}elseif(strtolower(C('DB_DATA_TYPE'))=='object'){
    		foreach ($fields as $res) {
    			$f['field'] = $res->Field;
    			$f['type'] = $res->Type;
    			$f['null'] = $res->Null;
    			$f['field'] = $res->Field;
    			$f['key'] = ($res->Key== "PRI" && $res->Extra) || $res->Key == "PRI";
    			$f['default'] = $res->Default;
    			$f['extra'] = $res->Extra;
    			$n_fields [$res->Field] = $f;
    		}
    		$pri = '';
    		foreach ($n_fields as $v) {
    			if ($v['key']) {
    				$pri = $v['field'];
    			}
    		}
    	}else{
    		$this->error('数据库返回数据的类型设置错误，请检查Config配置项！');
    	}
    	$info = array();
    	$info['fields'] = $n_fields;
    	$info['primarykey'] = $pri;
    	return $info;
    }

	/**
	 * 将查询SQL压入调试数组 show语句不保存
	 * @param $query
	 * @return null 
	 */
	protected function debug($query){
		$this->lastSql = $query;
		if (DEBUG && !preg_match("/^\s*show/i", $query)) {
			Debug::$sqlExeArr[] = $query; //压入一条成功发送SQL
		}
	}
	
	/**
	 * @access protected
	 * @param string $msg
	 * @return null 
	 */
	protected function error($msg){
		$this->error = $msg;
		if (DEBUG) {
			error($this->error);
		} else {
			Log::write($this->error);
		}
	}
	
	/**
	 * 过滤非法字段
	 * @param string | array $data 要过滤的表字段
	 * @return array $fields 返回过滤后的合法字段
	 */
	public function filterField($field){
		$fields=array();
		if(is_string($field)){
			$fields=preg_split('/[^\w\-]+/',$field);
		}elseif(is_array($field)){
			$fields=$field;
		}
		foreach ($fields as $k=>$f){
			if(!in_array($f, $this->opt['fieldArr'])){
				unset($fields[$k]);
			}
		}
		return $fields;
	}
	
	/**
	 * SQL查寻条件
	 * $where参数用法示例
	 * 1、‘id=3’，
	 * 2、22 代表主键id=22，
	 * 3、array(1,2,3,4) 代表主键id IN (1,2,3,4)，
	 * 4、array('id'=>'eq.5','and.name'=>'neq.admin') 代表id=5 AND name!=admin
	 * @param multitype $args 
	 * @return boolean|Db
	 */
	public function where($args){
		$where='';
		if(is_null($args)){// 判断为空不存在时的情况
			return false;
		}else	if(is_string($args)){// 判断为字符串时的情况
			$where=' '.$args.' ';
		}elseif(is_numeric($args)){// 判断为数字时的情况
			$where=' '.$this->opt['pri'].'='.$args.' ';
		}elseif(is_numeric(key($args))&&is_numeric(current($args))){// 判断为索引数组并且值为数值时的情况
			$where=' '.$this->opt['pri'].' IN('.implode(',', $args).') ';
		}elseif(is_array($args)){// 判断为关联数组时的情况
			$pattern='/[\.\|\-\+\_\@\%\>]+/';
			if(preg_match($pattern, current($args))){
				$tem=preg_split($pattern, current($args));
				if(!array_key_exists($tem[0], $this->condition)){
					$this->error('WHERE条件错误，请检查！！错误代码1');
				}
				$where.=key($args).$this->condition[$tem[0]].$tem[1];
			}else{
				$where.=key($args).' = '.current($args);
			}
			array_shift($args);
			if($args){
				$key='';
				$value='';
				foreach ($args as $k=>$v){
					if(preg_match($pattern, $k)){
						$tem=preg_split($pattern, $k);
						if(strtolower($tem[0])!='and' && strtolower($tem[0])!='or'){
							$this->error('WHERE条件错误，请检查！！错误代码2');
						}
						$key=strtoupper($tem[0]).' '.$tem[1];
					}else{
						$key='AND '.$k;
					}
					if(preg_match($pattern, $v)){
						$tem=preg_split($pattern, $v);
						if(!array_key_exists($tem[0], $this->condition)){
							if(strtolower($tem[0])=='in'){
								$value=' IN('.$tem[1].')';
							}else{
								$this->error('WHERE条件错误，请检查！！错误代码3');
							}
						}else{
							$value=$this->condition[$tem[0]].$tem[1];
						}
					}else{
						$value=' = '.$v;
					}
					$where.=' '.$key.$value;
				}
			}
		}else{
			$this->error('WHERE参数错误，请检查！！错误代码4');
		}
		$this->opt['where']=' WHERE '.$where;
		return $this;
	}
	
	/**
	 * 确定需要查寻的字段
	 * @param string $fields
	 * @return object $this
	 */
	public function field($fields){
		$fields=$this->filterField($fields);
		$field='';
		foreach ($fields as $f){
			$field.='`'.$f.'`,';
		}
		$this->opt['field']=rtrim($field,',');
		$this->opt['fieldArr']=$fields;
	}
	
	/**
	 * @分组统计,可以进行多个分组及统计
	 * @param $args 分组字段 可以是数组和字符串
	 * @例如array('字段1''字段2',……);
	 */
	public function group($args){
		$group='';
		if(is_string($args)){
			$group=' GROUP BY `'.$args.'`';
		}elseif(array($args)){
			$group=' GROUP BY `'.implode('`,`', $args).'`,';
		}
		$this->opt['group']=rtrim($group,',');
	}
	
	/**
	 * 分组条件筛选(待完善)
	 * @return Model
	 */
	public function having($args){
		$having='';
		if(is_string($args)){
			$having=' HAVING `'.$args.'`';
		}elseif(array($args)){
			$pattern='/[\.\,\|\-\+\_\@\%\>]+/';
			$con='';
			$tem='';
			foreach ($args as $key=>$value){
				$tem=preg_split($pattern, $value);
				if(isset($tem[1])){
					$con=$tem[0];
					$value=$tem[1];
				}else{
					$con='eq';
					$value=$tem[0];
				}
				
				$having.=' HAVING `'.$key.'`'.$this->condition[$con].$value.',';
			}
		}
		$this->opt['having'].=rtrim($having,',');
	}

	/**
	 * @统计计数
	 * @param $args
	 * @return Model
	 */
	public function count($args){
		$count='';
		if(empty($args)){
			if(!empty($this->opt['pri'])){
				$count=' COUNT('.$this->opt["pri"].') AS `count`';
			}else{
				$count=' COUNT(*) AS `count`';
			}
		}
		if(is_string($args)&&!empty($args)){
			$count=' COUNT(`'.$args.'`) AS `count`';
		}elseif(is_array($args)){
			$count=' COUNT(`'.key($args).'`) AS `'.current($args).'`';
		}
		if(empty($this->opt['field'])){
			$this->opt['count']=$count;
		}else{
			$this->opt['count']=','.$count;
		}
		
	}
	
	/**
	 * 模糊条件查寻
	 * 调用方法，传参形式:
	 * 1、必须为数组，2、可以匹配开始及结束
	 * 例如：$db->like(array('title'=>'start.欢迎使用StartPHP框架','info'=>'end.StartPHP','author'=>'舒良府'));
	 * @param array() $args
	 */
	public function like(array$args){
		$like='';
		$pattern='/[\.\|\-\+\_\>\@\%]+/';
		if(!is_array($args)){
			$this->error('LIKE方法参数错误，请检查！！');
		}
		$tem=preg_split($pattern, current($args));
		if(isset($tem[1])){
			if($tem[0]=='start'){
				$like.='`'.key($args).'` LIKE '.'"%'.$tem[1].'"';
			}elseif($tem[0]=='end'){
				$like.='`'.key($args).'` LIKE '.'"'.$tem[1].'%"';
			}else{
				$this->error('LIKE方法参数错误，请检查！！');
			}
		}else{
			$like.='`'.key($args).'` LIKE '.'"%'.current($args).'%"';
		}
		array_shift($args);
		if($args){
			$key='';
			$value='';
			foreach ($args as $k=>$v){
				if(preg_match($pattern, $k)){
					$tem=preg_split($pattern, $k);
					if(strtolower($tem[0])!='and' && strtolower($tem[0])!='or'){
						$this->error('LIKE方法参数错误，请检查！！');
					}
					$key=strtoupper($tem[0]).' '.$tem[1];
				}else{
					$key='AND `'.$k.'`';
				}
				$tem=preg_split($pattern, $v);
				if(isset($tem[1])){
					if($tem[0]=='start'){
						$value=' LIKE "%'.$tem[1].'"';
					}elseif($tem[0]=='end'){
						$value=' LIKE "'.$tem[1].'%"';
					}else{
						$this->error('LIKE方法参数错误，请检查！！');
					}
				}else{
					$value=' LIKE "%'.$v.'%"';
				}
				$like.=' '.$key.$value;
			}
		}
		$this->opt['like']=' WHERE '.rtrim($like);
	}
	
	/**
	 * 此方法废弃 where方法涵盖了此功能
	 * @param unknown $in
	 */
	public function in($in){
		$this->in($in);
	}
	
	/**
	 * 多条件查寻结果排序
	 * 调用方法,传参形式：
	 * 1、参数字符串，$db->order('id.asc');
	 * 2、参数为数组，$db->order(array('id'=>'desc','sort'));
	 * 注意：参数为数组时，可以是关联和索引数组及关联索引混搭
	 * @param string | array() $args 指定排序字段及方式
	 * @return Model
	 */
	public function order($args){
		$order='';
		$tem='';
		if(is_string($args)){ // 字符串的情况
			if($args=='rand'){
				$order='rand()';
			}else{
				$tem=preg_split('/[^\w\-]+/',$args);
				$type=isset($tem[1])?strtoupper($tem[1]):'DESC';
				$order='`'.$tem[0].'` '.$type;
			}
		}elseif(is_array($args)){ // 关联数组的情况
			foreach ($args as $k=>$v){
				if(is_numeric($k)){
					$tem.='`'.$v.'` ASC,';
				}elseif(is_string($k)){
					$tem.='`'.$k.'` '.strtoupper($v).',';
				}
			}
			$order=rtrim($tem,',');
		}else{
			$this->error('ORDER方法参数错误，请检查！！');
		}
		
		$this->opt['order']=' ORDER BY '.$order;
	}
	
	/**
	 * 查寻提取部分数据
	 * @param multitype $args
	 * @return Model
	 */
	public function limit($args){
		$limit='';
		if(is_string($args)||is_numeric($args)){
			$limit=$args;
		}elseif(is_array($args)){
			$limit=implode(',', $args);
		}else{
			$this->error('LIMIT方法参数错误，请检查！！');
		}
		$this->opt['limit']=' LIMIT '.$limit;
	}
	
	/**
	 * 查寻符合条件的所有数据
	 * @param multitype $where
	 * @return array result
	 */
	public function select($where){
		if(!empty($where)){
			$this->where($where);
		}
		$query='';
		$query='SELECT '
				.$this->opt['field']
				.$this->opt['count']
				.' FROM '
				.$this->table
				.$this->opt['group']
				.$this->opt['having']
				.$this->opt['where']
				.$this->opt['order']
				.$this->opt['limit'];
// 		exit($query);
		return $this->query($query);
	}
	
	/**
	 *  插入数据
	 * @param string $data
	 * @param string $type
	 */
	public function insert($data,$type){
		$fields='';
		$value='';
		foreach ($data as $k=>$v){
			$fields.='`'.$k.'`'.',';
			if(is_numeric($v)){
				$value.=$v.',';
			}else{
				$value.="'{$v}'".',';
			}
		}
		$query=strtoupper($type).' INTO '.$this->opt['table'].'('.rtrim($fields,',').') VALUES('.rtrim($value,',').')';
// 		exit($query);
		return $this->exe($query);
	}
	
	/**
	 * 更新数据
	 * @param array $data
	 */
	public function update($data){
		$kv='';
		foreach ($data as $k=>$v){
			if($k==$this->opt['pri']){
				$this->opt['where']=$k.'='.$v;
			}else{
				if(is_numeric($v)){
					$kv.='`'.$k.'`='.$v.',';
				}else{
					$kv.='`'.$k.'`="'.$v.'",';
				}
			}
		}
		if(empty($this->opt['where'])){
			$this->error('数据更新必须要加上WHERE条件！！');
		}
		$query='UPDATE '.$this->opt['table'].' SET '.rtrim($kv,',').' WHERE '.$this->opt['where'];
		return $this->exe($query);
	}
	
	/**
	 * 删除指定数据
	 * @param multipartTpey $where
	 * @return bool
	 */
	public function delete($where){
		if(!empty($where)){
			$this->where($where);
		}
		if(empty($this->opt['where'])){
			$this->error('删除必须要WHERE条件！！！');
		}
		$query='DELETE FROM '.$this->opt['table'].' '.$this->opt['where'];
		return $this->exe($query);
	}
	
	// 统计公共方法
	private function statist($type,$field){
		$field=empty($field)?$this->opt['pri']:$field;
		$field=empty($field)?'*':$field;
		$query='SELECT '.strtoupper($type).'('.$field.') AS '.$type.' FROM '.$this->opt['table'];
		$result=$this->query($query);
		$result=(array)$result[0];
		return $result[$type];
	}
	
	/**
	 * 查找最大的值
	 * @param string $field
	 * @return number $max 最大值
	 */
	public function max($field){
		$max=$this->statist(__FUNCTION__, $field);
		return $max;
	}
	
	/**
	 * 查找最小的值
	 * @param string $field
	 * @return number $min
	 */
	public function min($field){
		$min=$this->statist(__FUNCTION__, $field);
		return $min;
	}
	
	/**
	 * 求平均值
	 * @param string $field
	 * @return number $avg
	 */
	public function avg($field){
		$avg=$this->statist(__FUNCTION__, $field);
		return $avg;
	}
	
	/**
	 * 求平均值
	 * @param string $field
	 * @return number $sum
	 */
	public function sum($field){
		$sum=$this->statist(__FUNCTION__, $field);
		return $sum;
	}
	
	/**
	 * 获取记录总数
	 * @param string $table
	 * @return array
	 */
	public function total($table){
		if(!empty($table)){
			$this->table($table);
		}
		$field=empty($this->opt['pri'])?'*':$this->opt['pri'];
		$query='SELECT COUNT('.$field.') AS total FROM '.$this->opt['table'].$this->opt['where'];
		$res=$this->query($query);
		$res=(array)$res[0];
		return $res['total'];
	}
	
	/**
	 * 获取下一条即将插入数据的ID
	 * @param string $table
	 */
	public function getNextId($table){
		if(!empty($table)){
			$this->table($table);
		}
		$res=$this->getTableInfo($this->opt['table']);
		return $res['table'][$table]['autoincrement'];
	}
	
	/**
	 * 添加字段索引(自动优化索引)
	 * @param string $fields 	要添加索引的字段
	 * @param string $type 	索引类型
	 * @return bool
	 */
	public function index($fields,$type){
		$field='';
		if(is_string($fields)){
			$sql='SELECT CHAR_LENGTH('.$fields.') AS LENGTH FROM '.$this->opt['table'].' ORDER BY LENGTH DESC LIMIT 1';
			$field=$fields;
		}elseif(is_array($fields)){
			$sql='SELECT CHAR_LENGTH('.$fields[0].') AS LENGTH FROM '.$this->opt['table'].' ORDER BY LENGTH DESC LIMIT 1';
			$field=$fields[0];
		}
		$pri=empty($this->opt['pri'])?'*':$this->opt['pri'];
		// 求出字符长度
		$length=$this->query($sql);
		$maxLength=(array)$length[0];
		$goldPoint=array();
		// 求出黄金分割点
		for($i=1;$i<=$maxLength['length'];$i++){
			$sql='SELECT COUNT(DISTINCT(LEFT('.$field.','.$i.')))/COUNT('.$pri.') AS point FROM '.$this->opt['table'];
			$goldPoint[$i]=$this->query($sql);
			$goldPoint[$i]['point']=$i;
		}
		foreach ($goldPoint as $k=>$v){
			$tem=(array)$v[0];
			$goldPoint[$k]['gt']=abs($tem['point']-0.31);;
			unset($goldPoint[$k][0]);
		}
		function cmp_fn($v1,$v2){
			if($v1['gt']==$v2['gt']) return 0;
			return $v1['gt']>$v2['gt']?1:-1;
		}
		usort($goldPoint,'cmp_fn');
		// 取得黄金分割点所对应的值
		$point=$goldPoint[0]['point'];
		$indexField='';
		if(is_array($fields)){
			$tem=$fields[0].'('.$point.')';
			unset($fields[0]);
			if(!empty($fields)){
				foreach ($fields as $v){
					$tem.=','.$v;
				}
			}
			$indexField=$tem;
		}elseif(is_string($fields)){
			$indexField=$fields.'('.$point.')';
		}
		// 寻查所有索引
		$query='SHOW INDEX FROM '.$this->opt['table'];
		$allIndex=$this->query($query);
		foreach ($allIndex as $index){
			$i=(array)$index;
			if($i['Key_name']==strtoupper($type.$field)){
				// 在添加索引前先删除原有的索引
				$query='ALTER TABLE '.$this->opt['table'].' DROP  '.strtoupper($type).' '.strtoupper($type.$field).'';
				$this->exe($query);
				break;
			}
		}
		// 添加索引
		$query='ALTER TABLE '.$this->opt['table'].' ADD  '.strtoupper($type).' '.strtoupper($type.$field).'('.$indexField.')';
		return $this->exe($query);
	}
	
	/**
	 * 修复表
	 * @param string $table
	 */
	public function  repair($table){
		if(!empty($table)){
			$this->table($table);
		}
		$query='REPAIR TABLE `'.$this->opt['table'].'`';
		return $this->exe($query);
	}
	
	/**
	 * 优化表(整理数据表碎片)
	 * @param string||array||empty $table
	 * @return statu
	 */
	public function optimize($table){
		$tables=array();
		if(!empty($table)){
			if(is_string($table)){
				$this->table($table);
				$tables=(array)$this->opt['table'];
			}elseif(is_array($table)){
				foreach ($table as $t){
					$tables[]=$this->dbPrefix.str_replace($this->dbPrefix, '', $t);
				}
			}
		}else{
			$query = "SHOW TABLE STATUS FROM " . C("DB_NAME");
			$allTable=$this->query($query);
			foreach ($allTable as $t){
				$tem=(array)$t;
				$tables[]=$tem['Name'];
			}
		}
		foreach ($tables as $t){
			$query='OPTIMIZE TABLE `'.$t.'`';
			$this->exe($query);
		}
		return true;
	}
	
	/**
	 * 获得数据库或表大小
	 * @param string $table
	 * @return string
	 */
	public function getSize($table){
		$t='';
		if(!empty($table)){
			$this->table($table);
			$t=$this->opt['table'];
		}
		$sql = "SHOW TABLE STATUS FROM " . C("DB_DATABASE");
		$row = $this->query($sql);
		$size = 0;
		foreach ($row as $v) {
			if ($t) {
				$size += in_array(strtolower($v['Name']), $table) ? $v['Data_length'] + $v['Index_length'] : 0;
			} else {
				$size += $v['Data_length'] + $v['Index_length'];
			}
		}
		return get_size($size);
	}
	

	/**
	 * 获得表信息
	 * @param   string $table 数据库名
	 * @return  array
	 */
	public function getTableInfo($table){
		if(!empty($table)){
			$this->table($table);
		}
		$query='';
		if(empty($this->opt['table'])){
			$query = "SHOW TABLE STATUS FROM " . C("DB_NAME");
		}else{
			$query = "SHOW TABLE STATUS LIKE '{$this->opt['table']}'";
		}
		$info=$this->query($query);
		$arr = array();
		if(strtolower(C('DB_DATA_TYPE'))=='array'){
			$arr['total_size'] = 0; //总大小
			$arr['total_row'] = 0; //总条数
		}elseif(strtolower(C('DB_DATA_TYPE'))=='object'){
			$arr['total_size'] = 0; //总大小
			$arr['total_row'] = 0; //总条数
		}else{
			$this->error('数据库返回数据的类型设置错误，请检查Config配置项！');
		}
		foreach ($info as $k => $t) {
			if(strtolower(C('DB_DATA_TYPE'))=='array'){
				$arr['table'][$t['Name']]['tablename'] = $t['Name'];
				$arr['table'][$t['Name']]['engine'] = $t['Engine'];
				$arr['table'][$t['Name']]['rows'] = $t['Rows'];
				$arr['table'][$t['Name']]['collation'] = $t['Collation'];
				$charset = $arr['table'][$t['Name']]['collation'] = $t['Collation'];
				$charset = explode("_", $charset);
				$arr['table'][$t['Name']]['charset'] = $charset[0];
				$arr['table'][$t['Name']]['datafree'] = $t['Data_free'];
				$arr['table'][$t['Name']]['size'] = $t['Data_free'] + $t['Data_length'];
				$info = $this->getTableFields($t['Name']);
				$arr['table'][$t['Name']]['field'] = $info['fields'];
				$arr['table'][$t['Name']]['primarykey'] = $info['primarykey'];
				$arr['table'][$t['Name']]['autoincrement'] = $t['Auto_increment'] ? $t['Auto_increment'] : '';
				$arr['total_size'] += $arr['table'][$t['Name']]['size'];
				$arr['total_row']++;
			}elseif(strtolower(C('DB_DATA_TYPE'))=='object'){
				$arr['table'][$t->Name]['tablename'] = $t->Name;
				$arr['table'][$t->Name]['engine'] = $t->Engine;
				$arr['table'][$t->Name]['rows'] = $t->Rows;
				$arr['table'][$t->Name]['collation'] = $t->Collation;
				$charset = $arr['table'][$t->Name]['collation'] = $t->Collation;
				$charset = explode("_", $charset);
				$arr['table'][$t->Name]['charset'] = $charset[0];
				$arr['table'][$t->Name]['datafree'] = $t->Data_free;
				$arr['table'][$t->Name]['size'] = $t->Data_free + $t->Data_length;
				$info = $this->getTableFields($t->Name);
				$arr['table'][$t->Name]['field'] = $info['fields'];
				$arr['table'][$t->Name]['primarykey'] = $info['primarykey'];
				$arr['table'][$t->Name]['autoincrement'] = $t->Auto_increment ? $t->Auto_increment : '';
				$arr['total_size'] += $arr['table'][$t->Name]['size'];
				$arr['total_row']++;
			}
		}
		return empty($arr) ? false : $arr;
	}
	
	// 备份数据库
	public function backDatabase(){
	
	}
	
	// 备份数据表
	public function backTable($table){
		
	}
	
	// 删除表 须谨慎操作
	public function dropTable($table){
	
	}
	
	// 清空表  须谨慎操作
	public function clearTable($table){
	
	}
	
	
	
}

?>