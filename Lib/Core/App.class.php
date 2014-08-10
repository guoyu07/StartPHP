<?php
defined('START_PATH')||exit();
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-11-11
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/

final class App{
	
	public static function run(){
		if(DEBUG||!C('STATIC_CACHE')){
			Debug::start('APP_START');
		}
		HtmlCache::run();
		
	}
	
	public static function init(){
		if(IS_GROUP){
			$group=GROUP_PATH.'/'.GROUP;
			if(!is_dir($group)){
				if(DEBUG){
					error(GROUP.'分组不存在！！');
				}else{
					_404();
				}
			}
		}
		$control=A(CONTROL);
		$action=ACTION;
		if(!method_exists($control, $action)){
			if(DEBUG){
				error(CONTROL.'控制器'.$action.'动作不存在');
			}else{
				_404();
			}
		}
		call_user_func(array(&$control,$action));
		if(DEBUG){
			Debug::show('APP_START', 'APP_END');
		}else{
			Log::save();
		}
		
	}
}

?>