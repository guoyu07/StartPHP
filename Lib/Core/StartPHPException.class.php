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

class StartPHPException extends Exception{
	
	public function __construct($message,$code=0){
		parent::__construct($message,$code);
	}
	
	public function show(){
		$trace=$this->getTrace();
		$error['msg']='Message:'.$this->message.'<br/>';
		$error['msg'].='File:'.$this->file.' ['.$this->line.']<br/>';
		$error['msg'].=$trace[0]['class'];
		$error['msg'].=$trace[0]['type'];
		$error['msg'].=$trace[0]['function'].'()';
		array_shift($trace);
		$info='';
		foreach ($trace as $v){
			$class=isset($v['class'])?$v['class']:'';
			$type=isset($v['type'])?$v['type']:'';
			$file=isset($v['file'])?$v['file']:'';
			$info.=$file."\t".$class.$type.$v['function'].'()<br/>';
		}
		$error['info']=$info;
		Log::write('错误类型：EXCEP'."\n".'错误消息：'.$this->message."\n".'错误文件：'.$this->file."\n".'错误行号：'.$this->line);
		return $error;
	}
}

?>