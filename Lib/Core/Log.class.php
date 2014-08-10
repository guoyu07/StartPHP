<?php
defined('START_PATH')||exit();
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-11-10
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/

class Log{
	static $log=array();
	
	/**
	 * 记录日志内容到静态变量
	 * @param string $message
	 * @param string $type
	 */
	public static function setLog($message,$type='NOTICE'){
		if(in_array($type, C('LOG_TYPE'))){
			$date=date('Y-m-d H:i:s');
			self::$log[]='发生时间：'.$date."\n".$message."\n\r";
		}
	}
	
	/**
	 * 储存日志到日志文件
	 * @param number $message_type  日志处理类型 1为系统默认，2为邮件，3为文本保存
	 * @param string $destination  邮件地址
	 * @param string $extra_headers  邮件头信息
	 */
	public static function save($message_type = 3, $destination = null, $extra_headers = null){
		if(!C('LOG_START'))return ;
		if(is_null($destination)){
			$destination=LOG_PATH.'/'.date('Y_m_d').'.log';
		}
		if($message_type==3){
			if(is_file($destination)&&filesize($destination)>C('LOG_SIZE')){
				$destination=LOG_PATH.'/'.date('Y_m_d').'.log';
			}
		}
		error_log(implode(',', self::$log),$message_type,$destination);
	}
	
	/**
	 * 直接写入日志文件
	 * @param unknown $message
	 * @param number $message_type
	 * @param string $destination
	 * @param string $extra_headers
	 */
	public static function write($message,$message_type = 3, $destination = null, $extra_headers = null){
		if(!C('LOG_START'))return ;
		if(is_null($destination)){
			$destination=LOG_PATH.'/'.date('Y_m_d').'.log';
		}
		if($message_type==3){
			if(is_file($destination)&&filesize($destination)>C('LOG_SIZE')){
				$destination=LOG_PATH.'/'.date('Y_m_d').'.log';
			}
		}
		$date=date('Y-m-d H:i:s');
		$message='发生时间：'.$date."\n".$message."\n\r";
		error_log($message,$message_type,$destination);
	}
	
	
}

?>