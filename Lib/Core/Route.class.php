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

final class Route{
	static $pathInfo;//保存PATHINFO信息
	
	/**
	 * 解析分组PATHINFO
	 * @access public
	 * @param null
	 * @return null
	 */
	public static function groupPathinfo(){
		if (C('URL_TYPE')==1&&self::pathInfo()) {
			$pathInfo=self::$pathInfo;
			$info=explode(C('PATHINFO_LIMIT'),$pathInfo);
			if($info[0]!=C('VAR_GROUP')){
				$get[C('VAR_GROUP')]=ucfirst($info[0]);
				array_shift($info);
				$get[C('VAR_CONTROL')]=isset($info[0])?ucfirst($info[0]):'';
				array_shift($info);
				$get[C('VAR_ACTION')]=isset($info[0])?$info[0]:'';
				array_shift($info);
			}
			for($i=0;$i<count($info);$i+=2){
				$get[$info[$i]]=isset($info[$i+1])?$info[$i+1]:'';
			}
			$_GET=$get;
		}elseif (C('URL_TYPE')==2&&self::pathInfo()) {
			$pathInfo=self::$pathInfo;
			$info=explode(C('PATHINFO_LIMIT'),$pathInfo);
			if($info[0]!=C('VAR_GROUP')){
				$get[C('VAR_GROUP')]=ucfirst($info[0]);
				array_shift($info);
				$get[C('VAR_CONTROL')]=isset($info[0])?ucfirst($info[0]):'';
				array_shift($info);
				$get[C('VAR_ACTION')]=isset($info[0])?$info[0]:'';
				array_shift($info);
			}
			for($i=0;$i<count($info);$i+=2){
				$get[$info[$i]]=isset($info[$i+1])?$info[$i+1]:'';
			}
			$_GET=$get;
		}
	}
	
	/**
	 * 解析单应用PATHINFO
	 * @access private 
	 * @param null
	 * @return null
	 */
	public static function appPathinfo(){
		if(!IS_GROUP){
			if (C('URL_TYPE')==1&&self::pathInfo()) {
				$pathInfo=self::$pathInfo;
				$info=explode(C('PATHINFO_LIMIT'),$pathInfo);
				if($info[0]!=C('VAR_CONTROL')){
					$get[C('VAR_CONTROL')]=isset($info[0])?ucfirst($info[0]):'';
					array_shift($info);
					$get[C('VAR_ACTION')]=isset($info[0])?$info[0]:'';
					array_shift($info);
				}
				for($i=0;$i<count($info);$i+=2){
					$get[$info[$i]]=isset($info[$i+1])?$info[$i+1]:'';
				}
				$_GET=$get;
			}elseif (C('URL_TYPE')==2&&self::pathInfo()) {
				$pathInfo=self::$pathInfo;
				$info=explode(C('PATHINFO_LIMIT'),$pathInfo);
				p($info);
				if($info[0]!=C('VAR_CONTROL')){
					$get[C('VAR_CONTROL')]=isset($info[0])?$info[0]:'';
					array_shift($info);
					$get[C('VAR_ACTION')]=isset($info[0])?$info[0]:'';
					array_shift($info);
				}
				for($i=0;$i<count($info);$i+=2){
					$get[$info[$i]]=isset($info[$i+1])?$info[$i+1]:'';
				}
				$_GET=$get;
			}
		}
		self::setUrlConst();
	}
	
	/**
	 * 设置操作常量
	 * @access private
	 * @param null
	 * @return null
	 */
	private static function setUrlConst(){
		// 当前域名
		$host = $_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
		// 当前域名加协议
		defined('__HOST__')||define("__HOST__", C("HTTPS") ? 'https://' : "http://" . trim($host, '/'));
		//网站根目录不含入口文件
		$documentRoot = str_ireplace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']));
		$root = empty($documentRoot) ? "" : '/' . trim(str_replace('\\', '/', $documentRoot), '/');
		//根目录
		defined('__ROOT__')||define("__ROOT__", __HOST__ . $root);
		$url = isset($_SERVER['REDIRECT_URL']) ? rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') : $_SERVER['SCRIPT_NAME'];
		//网站根-含入口文件
		defined('__WEB__')||define("__WEB__", __HOST__ . $url);
		//完整URL地址
		defined('__URL__')||define("__URL__", __HOST__ . '/' . trim($_SERVER['REQUEST_URI'], '/'));
		//框架目录相关URL
		defined('__START__')||define("__START__", __HOST__ . '/' . trim(str_ireplace(str_replace('\\', '/',$_SERVER['DOCUMENT_ROOT']), "", START_PATH), '/'));
		//框架附加数据绝对URL地址，如字体图片
		defined('__START_DATA__')||define("__START_DATA__", __START__ . '/Data');
		//框架模板URL绝对地址
		defined('__START_TPL__')||define("__START_TPL__", __START__ . '/Lib/Tpl');
		//框架扩展URL绝对地址
		defined('__START_EXTEND__')||define("__START_EXTEND__", __START__ . '/Extend');
		if (IS_GROUP){//如分组则定义
			//当前分组
			defined('GROUP')||define('GROUP',isset($_GET[C('VAR_GROUP')])&&!empty($_GET[C('VAR_GROUP')])?$_GET[C('VAR_GROUP')]:C('DEFAULT_GROUP'));
			//当前分组绝对URL地址
			defined('__GROUP__')||define("__GROUP__", __ROOT__ . '/'.GROUP );
			defined('__CONTROL__')||define("__CONTROL__", __GROUP__ . '/'.(isset($_GET[C('VAR_CONTROL')])&&!empty($_GET[C('VAR_CONTROL')])?$_GET[C('VAR_CONTROL')]:C('DEFAULT_CONTROL')));
			defined('__METH__')||define("__METH__", __CONTROL__ . '/'.(isset($_GET[C('VAR_ACTION')])&&!empty($_GET[C('VAR_ACTION')])?$_GET[C('VAR_ACTION')]:C('DEFAULT_ACTION')));
		}else{
			defined('__CONTROL__')||define('__CONTROL__',__ROOT__.'/'.(isset($_GET[C('VAR_CONTROL')])&&!empty($_GET[C('VAR_CONTROL')])?$_GET[C('VAR_CONTROL')]:C('DEFAULT_CONTROL')));
			defined('__METH__')||define("__METH__", __CONTROL__ . '/'.(isset($_GET[C('VAR_ACTION')])&&!empty($_GET[C('VAR_ACTION')])?$_GET[C('VAR_ACTION')]:C('DEFAULT_ACTION')));
		}
		
		//当前控制器
		defined('CONTROL')||define('CONTROL',isset($_GET[C('VAR_CONTROL')])&&!empty($_GET[C('VAR_CONTROL')])?$_GET[C('VAR_CONTROL')]:C('DEFAULT_CONTROL'));
		//当前动作方法
		defined('ACTION')||define('ACTION',isset($_GET[C('VAR_ACTION')])&&!empty($_GET[C('VAR_ACTION')])?$_GET[C('VAR_ACTION')]:C('DEFAULT_ACTION'));
		//当前应用公共目录的URL绝对地址
		defined('__PUBLIC__')||defined("__PUBLIC__") or define("__PUBLIC__", C('__PUBLIC__'));
		//当前所有应用公共目录的URL绝对地址
		defined("__COMMON__") or define("__COMMON__", __ROOT__ .'/'. APP_NAME.'/Common');
// 		echo __PUBLIC__;die;
	}
	
	/**
	 * 获取PATHINFO
	 * @access parivate
	 * @param null
	 * @return boolean
	 */
	private static function pathInfo(){
		$pathInfo='';
		if(C('URL_TYPE')==1&&isset($_SERVER['PATH_INFO'])){
			$pathInfo=trim($_SERVER['PATH_INFO'],'/');
		}elseif(C('URL_TYPE')==2){
			if(isset($_GET[C('PATHINFO_VAR')])){
				$pathInfo=trim($_GET[C('PATHINFO_VAR')],'/');
			}			
		}else{
			return false;
		}
		//伪静态后缀
		$pathInfoFix='.'.ltrim(C('PATHINFO_FIX'),'.');
		//路由解析
		if($pathInfo){
			self::$pathInfo=str_replace($pathInfoFix, '', $pathInfo);
			self::parseRoute();
		}
		return true;
	}

	private static function parseRoute(){
		//读取路由规则
		$route=C('URL_ROUTE');
		$pathinfo=explode(C('PATHINFO_LIMIT'), self::$pathInfo);
		//获取PATHINFO里的第一个参数路由配置比较
		$urlRoute=$pathinfo[0];
		if(isset($route[$urlRoute])){
			$pathinfo=trim($route[$urlRoute],'/');
			//伪静态后缀
			$pathInfoFix='.'.ltrim(C('PATHINFO_FIX'),'.');
			//得到参数
			if(C('URL_TYPE')==1){
				$pathinfo.=str_replace(array($pathInfoFix,$urlRoute), '', trim($_SERVER['PATH_INFO'],'/'));
			}elseif(C('URL_TYPE')==2){
				$pathinfo.=str_replace(array($pathInfoFix,$urlRoute), '', $_GET[C('PATHINFO_VAR')]);
			}else{
				return false;
			}
			self::$pathInfo=$pathinfo;
		}
	}
	
	
}

?>