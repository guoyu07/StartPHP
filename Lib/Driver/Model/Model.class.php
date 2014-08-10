<?php
!defined(START_PATH)||_404();
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-11-13
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/

/**
 * 基础模型处理类
 * @author 舒良府
 *
 */
class Model{
    public $table;						//表名  通过table方法得到的
    public $field;						//表字段集
    public $view = array();		//多表关联
    public $db;							//数据库连接驱动
    public $data = array();		//SQL 操作参数
    public $validate = null;		//验证规则
    public $auto = null;				//自动完成
    public $error;						//验证不通过的错误信息
	
	public function __construct($driver,$table,$bool){
		if(method_exists($this, '__init')){
			$this->__init();
		}
		$this->run($driver, $table,$bool);
		$this->table=$this->db->opt['table'];
		$this->field=$this->db->opt['field'];
// 		p(get_object_vars($this));//获取对象定义的属性
	}
	
	public function __call($method,$args){
		throw new ExceptionStart(__CLASS__.'::'.$method.'()不存在！！');
	}
	
	protected function run($driver, $table,$bool){
		$db=DbFactory::factory($driver, $table,$bool);
		if($db){
			$this->db=$db;
		}else{
			if(DEBUG){
				error(mysql_error());
			}else{
				Log::write('数据库连接错误，请检查配置项是否正确！');
			}
		}
	}
	
	protected function init(){
		$opt = array(
				"trigger" => true,
				"joinTable" => array(),
				"result" => NULL,
				"data" => array(),
				"error" => ""
		);
	}
	
	public function __get($name){
		return isset($this->db->$name)?$this->db->$name:null;
	}
	
	public function __set($var,$value){
// 		echo 111;
		
	}
	
	/**
	 * 执行一个SQL语句  有返回值
	 * 示例：$Db->query("select title,click,addtime from hd_news where uid=18");
	 */
	public function query($query){
		return call_user_func(array($this->db, __FUNCTION__), $query);
	}
	
	/**
	 * 执行一个SQL语句  没有有返回值
	 * 示例：$Db->exe("delete from hd_news where id=16");
	 */
	public function exe($query){
// 		$args = func_get_args();
		$statu = call_user_func(array($this->db, __FUNCTION__), $query);
		$this->init();
		return $statu;
	}
	
	/**
	 * 更改临时操作表
	 * @param string $table
	 * @param bool $bool
	 * @return Model
	 */
	public function table($table,$bool=false){
		call_user_func(array($this->db,__FUNCTION__),$table,$bool);
		return $this;
	}
	
	/**
	 * 获得表信息
	 * @param   string $table 数据库名
	 * @return  array
	 */
	public function getTableInfo($table=''){
		return call_user_func(array($this->db,__FUNCTION__),$table);
	}
	
	/**
	 * SQL查寻条件
	 * $where参数用法示例
	 * 1、‘id=3’，
	 * 2、22 代表主键id=22，
	 * 3、array(1,2,3,4) 代表主键id IN (1,2,3,4)，
	 * 4、array('id'=>'eq.5','and.name'=>'neq.admin') 代表id=5 AND name!=admin
	 * @param multipartType $args
	 * @return boolean|Db
	 */
	public function where($args){
		call_user_func(array($this->db,__FUNCTION__),$args);
		return $this;
	}
	
	/**
	 * 确定需要查寻的字段
	 * @param string $fields
	 * @return object $this
	 */
	public function field($fields){
		call_user_func(array($this->db,__FUNCTION__),$fields);
		return $this;
	}
	
	/**
	 * @分组统计,可以进行多个分组及统计
	 * @param $args 分组字段 可以是数组和字符串
	 * @例如array('字段1''字段2',……);
	 * @return Model
	 */
	public function group($args){
		call_user_func(array($this->db,__FUNCTION__),$args);
		return $this;
	}
	
	/**
	 * 分组条件筛选(待完善)
	 * @return Model
	 */
	public function having($args){
		call_user_func(array($this->db,__FUNCTION__),$args);
		return $this;
	}
	
	/**
	 * @统计计数
	 * @param $args
	 * @return Model
	 */
	public function count($args=''){
// 		$args=func_get_args();
		call_user_func(array($this->db,__FUNCTION__),$args);
		return $this;
	}
	
	/**
	 * 模糊条件查寻
	 * 调用方法，传参形式:
	 * 1、必须为数组，2、可以匹配开始及结束
	 * 例如：$db->like(array('title'=>'start.欢迎使用StartPHP框架','info'=>'end.StartPHP','author'=>'舒良府'));
	 * @param array() $args
	 */
	public function like(array$args){
		call_user_func(array($this->db,__FUNCTION__),$args);
		return $this;
	}
	
	/**
	 * 将废弃
	 * @param unknown $args
	 * @return Model
	 */
	public function in($args){
		call_user_func(array($this->db,__FUNCTION__),$args);
		return $this;
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
		call_user_func(array($this->db,__FUNCTION__),$args);
		return $this;
	}
	
	/**
	 * 查寻提取部分数据
	 * @param mutipayType $args
	 * @return Model
	 */
	public function limit($args){
		call_user_func(array($this->db,__FUNCTION__),$args);
		return $this;
	}
	
	/**
	 * 此方法废弃
	 */
// 	public function join($args){
// 		if(is_null($args)||empty($args)){
// 			return false;
// 		}elseif(is_string($args)){
// 			$this->tables=explode(',', $args);
// 		}elseif(is_array($args)){
// 			$this->tables=$args;
// 		}elseif(is_object($args)){
// 			error('JOIN参数不能使用对象');
// 		}
// 		return $this;
// 	}
	
	/**
	 * 查寻符合条件的所有数据
	 * @param string $where
	 * @return mixed
	 */
	public function select(){
		$args=func_get_args();
		$where=isset($args[0])?$args[0]:'';
		return call_user_func(array($this->db,__FUNCTION__),$where);
	}
	
	/**
	 * 查寻所有数据，select别名
	 * @param string $where
	 * @return mixed
	 */
	public function findAll($where=''){
		return call_user_func(array($this,'select'),$where);
	}
	
	/**
	 * 查寻所有数据，select别名
	 * @param string $where
	 * @return mixed
	 */
	public function all($where=''){
		return $this->select($where);
	}
	
	/**
	 * 查寻一条数据，select别名
	 * @param string $where
	 * @return mixed
	 */
	public function one($where=''){
		return $this->findOne($where);
	}
	
	/**
	 * 查寻一条数据
	 * @param string $where
	 * @return Ambigous <unknown, mixed>
	 */
	public function findOne($where=''){
		$this->limit(1);
		$data=$this->select($where);
		return isset($data[0])?$data[0]:$data;
	}
	
	
	public function rand($num=10){
		$this->order('rand');
		$this->limit($num);
		return call_user_func(array($this,'select'));
	}

	/**
	 * 获取表单数据
	 * @return Ambigous <NULL, $data>
	 */
	private function getFormData(){
		$post=G('post','slashes_e,html_e');
		foreach($post as $k=>$v){
			if(!(bool)$this->filterField($k)){
				unset($post[$k]);
			}
		}
		if(empty($post))return false;
		return $post;
	}
	
	/**
	 * 过滤非法字段
	 * @param  string or array $field
	 * @return mixed
	 */
	private function filterField($field){
		return call_user_func(array($this->db,__FUNCTION__),$field);
	}
	
	/**
	 *  插入数据
	 * @param string $data
	 * @param string $type
	 */
	public function insert($data=array(),$type='INSERT'){
		$data=empty($data)?$this->getFormData():$data;
		return call_user_func(array($this->db,__FUNCTION__),$data,$type);
	}
	
	/**
	 * insert方法别名
	 * @param string $data
	 * @param string $type
	 * @return mixed
	 */
	public function add($data=array()){
		$this->insert($data,'INSERT');
	}
	
	/**
	 * REPLACE方式插入
	 * @param array $data
	 */
	public function replace($data=array()){
		$this->insert($data,'REPLACE');
	}

	/**
	 * 更新数据,如果提交的数据中包含主键，那么可以省去where条件
	 * @return bool
	 */
	public function update($data=array()){
		$data=empty($data)?$this->getFormData():$data;
		return call_user_func(array($this->db,__FUNCTION__),$data);
	}
	
	// update方法别名
	public function save($data=array()){
		$this->update($data);
	}
	
	/**
	 * 删除指定数据
	 * @param string $where
	 * @return mixed
	 */
	public function delete($where=''){
		return call_user_func(array($this->db,__FUNCTION__),$where);
	}
	

	/**
	 * 删除指定数据 delete别名
	 * @param string $where
	 * @return mixed
	 */
	public function del($where=''){
		$this->delete($where);
	}
	
	

	/**
	 * 获取记录总数
	 * @param string $table
	 * @return array
	 */
	public function total($table=''){
		return call_user_func(array($this->db,__FUNCTION__),$table);
	}
	
	/**
	 * 获取下一条即将插入数据的ID
	 * @param string $table
	 */
	public function getNextId($table=''){
		return call_user_func(array($this->db,__FUNCTION__),$table);
	}
	
	/**
	 * 查找最大的值
	 * @param string $field
	 * @return number $max 最大值
	 */
	public function max($field){
		return call_user_func(array($this->db,__FUNCTION__),$field);
	}
	
	/**
	 * 查找最小的值
	 * @param string $field
	 * @return number $min
	 */
	public function min($field){
		return call_user_func(array($this->db,__FUNCTION__),$field);
	}
	
	/**
	 * 求平均值
	 * @param string $field
	 * @return number $avg
	 */
	public function avg($field){
		return call_user_func(array($this->db,__FUNCTION__),$field);
	}
	
	/**
	 * 求平均值
	 * @param string $field
	 * @return number $sum
	 */
	public function sum($field){
		return call_user_func(array($this->db,__FUNCTION__),$field);
	}
	
	/**
	 * 获得数据库或表大小
	 * @param string $table
	 * @return string
	 */
	public function getSize($table=''){
		return call_user_func(array($this->db,__FUNCTION__),$table);
	}
	
	
	public function index($field,$type='index'){
		return call_user_func(array($this->db,__FUNCTION__),$field,$type);
	}
	
	
	/**
	 * 修复表
	 * @param string $table
	 */
	public function  repair($table=''){
		return call_user_func(array($this->db,__FUNCTION__),$table);
	}
	
	/**
	 * 优化表(整理数据表碎片)
	 * @param string $table
	 */
	public function optimize($table){
		return call_user_func(array($this->db,__FUNCTION__),$table);
	}
	
	/**
	 * 开启事务
	 * @see DbIinterface::beginTrans()
	 */
	public function begin(){
		call_user_func(array($this->db,__FUNCTION__));
	}
	
	/**
	 * 提交一个事务
	 * @see DbIinterface::commit()
	 */
	public function commit(){
		call_user_func(array($this->db,__FUNCTION__));
	}
	
	/**
	 * 回滚事务
	 * @see DbIinterface::rollback()
	 */
	public function rollback(){
		call_user_func(array($this->db,__FUNCTION__));
	}
	
	/**
	 * 获取当前数据插入的主键
	 * @see DbIinterface::insertId()
	 */
	public function insertId(){
		return call_user_func(array($this->db,__FUNCTION__));
	}
	
	/**
	 * 获取受影响的行数
	 * @see DbIinterface::affectedRows()
	 */
	public function affectedRows(){
		return call_user_func(array($this->db,__FUNCTION__));
	}
	
	/**
	 * 获取Mysql版本号
	 * @see DbIinterface::version()
	 */
	public function version(){
		return call_user_func(array($this->db,__FUNCTION__));
	}
	
	
}

?>