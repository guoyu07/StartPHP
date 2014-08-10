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



final class Boot{
	static $compFile;
	
	public static function run(){
		//定义框架常量
// 		define("DS", DIRECTORY_SEPARATOR); //目录分隔符
		define("IS_WIN", strstr(PHP_OS, 'WIN') ? true : false); //window环境
		define('START_CORE_PATH',START_PATH.'/Lib/Core');					//核心目录
		define('START_FUNCTION_PATH', START_PATH.'/Lib/Function');		//函数目录
		define('START_EXTEND_PATH', START_PATH.'/Extend');					//扩展目录
		define('START_DATA_PATH', START_PATH.'/Data');						//数据目录
		define('START_CONFIG_PATH', START_PATH.'/Config');					//配置文件目录
		define('START_DRIVER_PATH', START_PATH.'/Lib/Driver');				//驱动目录
		define('START_MODEL_PATH', START_DRIVER_PATH.'/Model');		//模块路径
		define('START_SESSION_PATH', START_DRIVER_PATH.'/Session');	//Session驱动路径
		define('START_CACHE_PATH', START_DRIVER_PATH.'/Cache');
		define('START_DB_PATH', START_DRIVER_PATH.'/Db');					// Db驱动路径
		define('START_LANG_PATH', 'Lang/');											//语言包目录
		define('START_TPL_PATH', START_PATH.'/Lib/Tpl');						//框架模板目录
		self::loadCoreFile();
		//系统初始配置
		C(require START_CONFIG_PATH.'/Config.php');

		self::compileCore();
		self::complieFile();
		StartPHP::init();
		if(IS_GROUP&&(GROUP==C('DEFAULT_GROUP'))){
			self::mkdirs();
			self::configDemo();
			self::controlDemo();
		}
		App::run();
	}
	
	
	/**
	 * 载入框架核心文件
	 */
	private static function loadCoreFile(){
		$coreFile=array(
				START_CORE_PATH.'/ExceptionStart.class.php',
				START_CORE_PATH.'/Log.class.php',
				START_CORE_PATH.'/Debug.class.php',
				START_CORE_PATH.'/Route.class.php',
				START_CORE_PATH.'/App.class.php',
				START_CORE_PATH.'/StartPHP.class.php',
				START_FUNCTION_PATH.'/Functions.php',
				START_FUNCTION_PATH.'/Common.php',
		);
		foreach ($coreFile as $file){
			if(is_file($file)){
				require $file;
			}
		}
	}
	
	/**
	 * 创建应用目录
	 */
	private static function mkdirs(){
		$appDirs=array(
				CONFIG_PATH,						// 配置文件路径
				CONTROL_PATH,						// 控制器路径 
				MODEL_PATH,							// 数据模型路径
				TPL_PATH,								// TPL模板目录
				EXTEND_PATH,						// 扩展目录
				EXTEND_LANG_PATH,				// 扩展语言目录
				EXTEND_TAG_PATH,					// 扩展标签目录
				EXTEND_PLUGIN_PATH,			// 扩展插件目录
				EXTEND_CLASS_PATH,				// 扩展类目录
				EXTEND_FUNCTION_PATH,		// 扩展函数目录
				COMMON_PATH,						// 分组公共文件目录
				COMMON_CONFIG_PATH,			// 分组公共配置目录
				COMMON_PLUGIN_PATH,			// 分组公共插件目录
				COMMON_TAG_PATH,				// 分组公共标签目录
				COMMON_LANG_PATH,				// 分组公共语言包目录
				TEMP_PATH,							// 临时文件目录
				CACHE_PATH,							// 缓存文件目录
				COMPILE_PATH, 						// 编译文件目录
				LOG_PATH,								// 日志文件目录
		);
		foreach ($appDirs as $dir){
			if(!empty($dir)){
				is_dir($dir)||mkdir($dir,0777,true);
			}
		}
	}
	
	/**
	 * 编译核心文件
	 */
	private static function compileCore(){
		if(DEBUG){
			is_file(TEMP_FILE)&&unlink(TEMP_FILE);
		}
		$data='';
		// 读取用户常量写入
		$useConst=get_defined_constants(true);
		foreach ($useConst['user'] as $k=>$v){
			$data.="defined('$k')||define('$k','$v');";
		}
		// 读取系统初始配置
		$data.='C('.delSpace(var_export(C(),true)).');';
		$files=array(
				START_CORE_PATH.'/Route.class.php',
				START_CORE_PATH.'/App.class.php',
				START_CORE_PATH.'/StartPHP.class.php',
				START_FUNCTION_PATH.'/Common.php',
				START_FUNCTION_PATH.'/Functions.php',
		);
		foreach ($files as $file){
			$dat=file_get_contents($file);
			$data.=delSpace($dat);
		}
		
		self::$compFile=$data;
	}
	
	/**
	 * 编译核心文件
	 */
	private static function complieFile(){
		$data='<?php '.self::$compFile.' StartPHP::init();App::run();?>';
		is_dir(TEMP_PATH)||mkdir(TEMP_PATH,0777,true);
		@file_put_contents(TEMP_FILE, $data);
	}
	
	/**
	 * 创建控制器demo文件
	 */
	private static function controlDemo(){
		$controlFile=START_TPL_PATH.'/Control_demo.txt';
		!is_file($controlFile)&&exit('IndexControl控制器模板不存在！！');
		$data=file_get_contents($controlFile);
		$fileName=CONTROL_PATH.'/Index'.ucfirst(C('DEFAULT_CONTROL_FIX')).'.class.php';
		if(is_file($fileName))return;
		self::putFile($fileName, $data);
	}
	
	/**
	 * 创建配置文件demo文件
	 */
	private static function configDemo(){
		//配置文件模板
		$configFile=START_TPL_PATH.'/Config_demo.txt';
		if(!is_file($configFile))exit('配置文件模板不存在！！');
		$data=file_get_contents($configFile);
		$confArr=array();
		if(IS_GROUP){
			// 公共配置文件路径
			$confArr[]=COMMON_CONFIG_PATH.'/Config.php';
		}
		// 当前分组配置文件路径
		$confArr[]=CONFIG_PATH.'/Config.php';
		foreach ($confArr as $conf){
			self::putFile($conf, $data);
		}
	}
	
	/**
	 * 写入文件
	 */
	private static function putFile($fileName,$data){
		if(is_file($fileName))return ;
		if(!is_dir(dirname($fileName)))mkdir(dirname($fileName),0777,true);
		file_put_contents($fileName, $data);
	}
	
	
}









?>