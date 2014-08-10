<?php
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-11-8
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/

/**
 * 缓存控制
 * @author 舒良府
 *
 */
final class HtmlCache{
	

	public static function run(){
		self::checkLife();
		if(is_file(CACHE_FILE)){
			if(DEBUG || filemtime(TPL_FILE)>filemtime(CACHE_FILE)&&!C('STATIC_CACHE')){
				//如果DEBUG开启为真，或者模板文件修改时间大于编译文件，那么走动态
				App::init();
			}else{
				//如果DEBUG没有开启，或者模板文件修改时间小于编译文件，那么走缓存
				include CACHE_FILE;
				if(DEBUG){
					Debug::show('APP_START', 'APP_END');
				}
				Log::save();
			}
		}else{
			//如果缓存文件不存在，那么走动态生成缓存
			App::init();
		}
	}
	
	/**
	 * 缓存生命周期
	 */
	private static function checkLife(){
		$cacheDir=substr(CACHE_PATH, 0,strpos(CACHE_PATH,'Cache')).'Cache';
		$allCacheFile=Dir::scanFile($cacheDir,true);
		foreach ($allCacheFile as $f){
			if(filemtime($f)+C('STATIC_CACHE_LIFETIME')<time()){
				unlink($f);
			}
		}
	}
	

}

?>