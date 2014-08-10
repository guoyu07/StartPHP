<?php
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-11-6
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/
defined('START_PATH')||exit();
/**
 * 编译缓存文件类
 * @author 舒良府
 *
 */
class Compile {
	private $tplPath;		//模板文件路径
	private $compile;	//编译文件路径
	private $cacheFile;	//静态缓存文件路径
	private $vars;			//向模板分配的变量
	
	
	public function __construct($tplPath,$vars,$path){
		$this->getSavePath($path);
		$this->tplPath=$tplPath;
		$this->vars=$vars;
		$this->createCompileFile();
	}
	
	/**
	 * 为兼容生成纯静态时路径问题，特加此方法
	 * @param unknown $path
	 */
	private function getSavePath($path){
		if(!empty($path)){
			$this->compile=TEMP_PATH.'/Compile/'.(IS_GROUP?$path['group'].'/':'').$path['control'].'/'.sha1(TEMP_TPL_NAME).'.php';
			$this->cacheFile=TEMP_PATH.'/Cache/'.(IS_GROUP?$path['group'].'/':'').$path['control'].'/'.sha1(TEMP_TPL_NAME).'.html';
		}else{
			//编译文件存储路径
			$this->compile=COMPILE_PATH.'/'.sha1(TEMP_TPL_NAME).'.php';
			//缓存文件存储路径
			$this->cacheFile=CACHE_PATH.'/'.sha1(TEMP_TPL_NAME).'.html';
		}
	}
	
	/**
	 * 创建编译文件
	 */
	private function createCompileFile(){
		//注入变量
		extract($this->vars);
		$parse=new Parse($this->tplPath);
		$comileFile=$parse->paseTpl();
		$comileFile='<?php defined("START_PATH")||exit();?>'."\n".$comileFile;
		if(!DEBUG&&is_file($this->compile)&&filemtime($this->tplPath)<filemtime($this->compile)&&!C('STATIC_CACHE')){
			require $this->compile;
		}else{
			is_dir(dirname($this->compile))||mkdir(dirname($this->compile),0777,true);//创建存储文件夹
			file_put_contents($this->compile, $comileFile)||exit('编译文件写入失败！！');
		}
		if(C('STATIC_CACHE')){
			var_dump(C('STATIC_CACHE'));
			echo 555;
			$this->createCacheFile();
		}else{
			require $this->compile;
		}
		
	}
	
	private function createCacheFile(){
		
// 		//注入变量
// 		extract($this->vars);
		
// 		ob_start();
// 		require $this->compile;
// 		$cacheFile=ob_get_contents();
// 		is_dir(dirname($this->cacheFile))||mkdir(dirname($this->cacheFile),0777,true);//创建存储文件夹
// 		file_put_contents($this->cacheFile, $cacheFile)||exit('缓存文件写入失败！！');
// 		ob_end_clean();
// 		require $this->cacheFile;
	}
	
}

?>