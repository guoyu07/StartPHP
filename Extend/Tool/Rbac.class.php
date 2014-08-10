<?php
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-11-21
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/

final class Rbac{
	public static $error;//错误信息
	
	/**
	 * 获取RABC所有数据表
	 * @return array()
	 */
	private static function getTable(){
		$tableName=array(
				"RBAC_USER_TABLE",
				"RBAC_ROLE_TABLE",
				"RBAC_NODE_TABLE",
				"RBAC_ROLE_USER_TABLE",
				"RABC_ACCESS_TABLE"
		);
		$tabPrefix=C('DB_PREFIX');
		$tables=array();
		foreach ($tableName as $name){
			$table=C($name);
			if(!empty($table)){
				$tables[$name]=$tabPrefix.$table;
			}
		}
		return $tables;
	}
	
	public static function createTable(){
		$rabcTab=self::getTable();
		$db=M();
		$data=$db->query('SHOW TABLES');
		$tables=array();
		$field='Tables_in_'.DbMysql::$databaseName;
		foreach ($data as $v){
			$tables[]=$v->$field;
		}
		foreach($rabcTab as $v){
			if(!in_array($v, $tables)){
				$query=self::rbacTabQuery();
				$query=$query[$v];
				echo $db->execute($query);
			}
		}
		
	}
	
	private static function rbacTabQuery(){
		if(C('ENCRYPTION_TYPE')=='sha1'){
			$length=40;
		}elseif(C('ENCRYPTION_TYPE')=='md5'){
			$length=32;
		}
		$query=array(
				C('DB_PREFIX').C('RBAC_USER_TABLE')=>'CREATE TABLE '.C('DB_PREFIX').C('RBAC_USER_TABLE').'(uid INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,'.C('RBAC_USERNAME_FIELD').' VARCHAR(20) NOT NULL DEFAULT \'\','.C('RBAC_PASSWORD_FIELD').' CHAR('.$length.') NOT NULL DEFAULT \'\');',
				C('DB_PREFIX').C('RBAC_NODE_TABLE')=>'CREATE TABLE '.C('DB_PREFIX').C('RBAC_NODE_TABLE').'(nid INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,name VARCHAR(20) NOT NULL DEFAULT "",title VARCHAR(20) NOT NULL DEFAULT "",pid INT UNSIGNED NOT NULL DEFAULT 0,level TINYINT(1) DEFAULT 1);',
				C('DB_PREFIX').C('RBAC_ROLE_TABLE')=>'CREATE TABLE '.C('DB_PREFIX').C('RBAC_ROLE_TABLE').'(rid SMALLINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,name VARCHAR(20) NOT NULL DEFAULT "",uid INT UNSIGNED NOT NULL DEFAULT 0);',
				C('DB_PREFIX').C('RBAC_ROLE_USER_TABLE')=>'CREATE TABLE '.C('DB_PREFIX').C('RBAC_ROLE_USER_TABLE').'(uid INT UNSIGNED NOT NULL DEFAULT 0,rid SMALLINT UNSIGNED NOT NULL DEFAULT 0);',
				C('DB_PREFIX').C('RABC_ACCESS_TABLE')=>'CREATE TABLE '.C('DB_PREFIX').C('RABC_ACCESS_TABLE').'(nid INT UNSIGNED NOT NULL DEFAULT 0,rid SMALLINT UNSIGNED NOT NULL DEFAULT 0);'
		);
		return $query;
	}
	
	
	
	
}

?>