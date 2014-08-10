<?php
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
 * 数据库工厂类
 * @author 舒良府
 *
 */
final class DbFactory{
	private static $dbFactory; 			// 驱动对象
	private $driverList=array(); 				// 已经实例化过的驱动组
	
	private function __construct(){}
	
	private function __clone(){}
	
	/**
	 * 获取数据库驱动对象
	 * @param string $driver 数据库驱动
	 * @param string $table 数据表
	 * @return Object 返回DB对象
	 */
	public static function factory($driver,$table,$bool){
		// 单例模式，只实例化一个对象
		 if(!(self::$dbFactory instanceof self)){
		 	self::$dbFactory=new self();
		 }
		 $driver=empty($driver)?ucfirst(strtolower(C('DB_DRIVER'))):ucfirst(strtolower($driver));
		 $table=empty($table)?'empty':$table;
		 if(isset(self::$dbFactory->driverList[md5($table)])){
		 	return self::$dbFactory->driverList[md5($table)];
		 }
		 self::$dbFactory->getDriver($driver, $table,$bool);
		 return self::$dbFactory->driverList[md5($table)];
		 
	}
	
	/**
	 * 获取数据库驱动接口
	 * @param string $driver
	 * @param string $table
	 */
	private function getDriver($driver,$table,$bool){
		$driver='Db'.$driver;
		self::$dbFactory->driverList[md5($table)]=new $driver;
		$tableName=$table=='empty'?null:$table;
		return self::$dbFactory->driverList[md5($table)]->connect($tableName,$bool);
	}
	
	
	/**
	 * 释放连接驱动
	 */
	private function close(){
		foreach ($this->driverList as $link){
			$link->close();
		}
	}
	
	/**
	 * 析构函数
	 * Enter description here ...
	 */
	function __destruct(){
		$this->close();
	}
	
	
}

?>