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
 * 数据库接口
 * @author 舒良府
 *
 */
interface DbIinterface {
	
	public function link(); 						// 获取数据库接连资料

    public function close();						// 关闭数据库

    public function exe($query);				// 发送没有返回值的sql

    public function query($query); 			// 有返回值的sql

    public function insertId();					// 获得最后插入的id

    public function affectedRows(); 			// 受影响的行数

    public function version(); 					// 获得版本

    public function begin();						// 自动提交模式true开启false关闭
    
    public function commit();					// 提交一个事务

    public function rollback(); 					// 回滚事务
}

?>