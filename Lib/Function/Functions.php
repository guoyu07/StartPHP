<?php
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-11-9
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/
/**
 * 实例化普通模型
 * @param String $table 表名
 * @param bool $bool 字段是否添加前缀
 * @return Model 返回模型对象
 */
function M($table='',$bool=false,$driver=''){
	return new Model($driver,$table,$bool);
}

/**
 * 实例化扩展模型
 * @param string $model
 * @param bool $bool 字段是否添加前缀
 * @return Model
 */
function K($table='',$bool=false,$driver=''){
	$className=empty($table)?ucfirst(CONTROL).'Model':ucfirst(str_replace('Model', '', $table)).'Model';
	$modelFile=MODEL_PATH.'/'.$className.'.class.php';
	loadFile($modelFile);
	return new $className($driver,$table,$bool);
}

/**
 * 实例化视图模型
 * @param string $model
 * @return Model
 */
function V($table='',$bool=false,$driver=''){
	return new ViewModel($driver,$table,$bool);
}

/**
 * 实例化关联模型
 * @param string $model
 * @return Model
 */
function R($table='',$bool=false,$driver=''){
	return new RelationModel($driver,$table,$bool);
}

/**
 * 获取列表及详细页的URL地址 (静态地址或动态地址)
 * @access public 
 * @param boolean $ishtml  	是否生成静态
 * @param string $htmlDir 	 	静态目录
 * @param number $id 		 	主键ID值
 * @param string $prikey 	 	主键名称
 * @param blooean $addrtype 	地址类型 (1为URL绝对地址，2为绝对物理地址)
 * @param string $type 		 	URL地址类型 (List页面及Details页面)
 * @param string $addtime 	 	详细内容添加时间
 * @return string 				 	返回URL地址
 */
function getAddress($ishtml,$htmlDir,$id,$prikey='id',$addrtype=1,$index=false,$type='list',$addtime=''){
	$url='';
	$root=$addrtype==1?__ROOT__:ROOT_PATH;
	if($type=='list'){//列表页URL地址
		if($ishtml){
			if($index){
				$url=$root.'/'.C('APP_STATIC_DIR').'/'.$htmlDir.'/index.html';
			}else{
				$url=$root.'/'.C('APP_STATIC_DIR').'/'.$htmlDir.'/'.$id.'.html';
			}
			
		}else{
			$url=U('Index/'.ucfirst($type).'/index',array($prikey=>$id));
		}
	}else{//详细页URL地址
		if($ishtml){
			if(empty($addtime))error('请传递$addtime参数！！');
			$url=$root.'/'.C('APP_STATIC_PATH').'/'.$htmlDir.'/'.date('Ym',$addtime).'/'.$id.'.html';
		}else{
			$url=U('Index/'.ucfirst($type).'/index',array($prikey=>$id));
		}
	}
	return $url;
}

/**
 * 系统函数
 * URL地址配置
 * $pathinfo->要生成的URL地址
 * $html_suffix->伪静态后缀，默认为html
 * $redrict->是否跳转
 * @param string $addr 要生成的链接地址
 * @param string $redrict 是否进行跳转
 * @param string $param 链接地址所附带的参数
 * @return string $html_suffix 伪静态后缀名
 */
function U($pathinfo='',$param=array(),$html_suffix=null,$redrict=false){
	
	$url='';
	//伪静态后缀
	if(is_null($html_suffix)){
		$html='.'.ltrim(C('PATHINFO_FIX'),'.');
	}else{
		$html=$html_suffix;
	}
	//拆分pathinfo
	$info=preg_split('/[^\w\-]+/', $pathinfo);
	$info=array_filter($info);
	switch (count($info)){
		case 1:
			$group=IS_GROUP?GROUP:'';
			$control=CONTROL;
			$action=$info[0];
			break;
		case 2:
			$group=IS_GROUP?GROUP:'';
			$control=$info[0];
			$action=$info[1];
			break;
		case 3:
			$group=$info[0];
			$control=$info[1];
			$action=$info[2];
			break;
		default:
			$group=IS_GROUP?GROUP:'';
			$control=CONTROL;
			$action=ACTION;
			break;
	}
	
	//获取参数
	$args='';
	if(!empty($param)){
		foreach ($param as $k=>$v){
			if(C('URL_TYPE')==3){
				$args.='&'.$k.'='.$v;
			}else{
				$args.='/'.$k.'/'.$v;
			}
		}
	}
	//判断URL类型及组合URL地址
	switch (C('URL_TYPE')){
		case 1:// PATH_INFO模式
			if(C('URL_REWRITE')){	// 判断是否开房URL重写
				if(IS_GROUP){
					$url=__ROOT__.'/'.$group.'/'.$control.'/'.$action.$args.$html;
				}else{
					$url=__ROOT__.'/'.$control.'/'.$action.$args.$html;
				}
			}else{
				if(IS_GROUP){
					$url=__WEB__.'/'.$group.'/'.$control.'/'.$action.$args.$html;
				}else{
					$url=__WEB__.'/'.$control.'/'.$action.$args.$html;
				}
			}
			break;
		case 2:// 兼容模式
			if(C('URL_REWRITE')){	// 判断是否开房URL重写
				if(IS_GROUP){
					$url=__ROOT__.'?'.C('PATHINFO_VAR').'='.$group.'/'.$control.'/'.$action.$args.$html;
				}else{
					$url=__ROOT__.'?'.C('PATHINFO_VAR').'='.$control.'/'.$action.$args.$html;
				}
			}else{
				if(IS_GROUP){
					$url=__WEB__.'?'.C('PATHINFO_VAR').'='.$group.'/'.$control.'/'.$action.$args.$html;
				}else{
					$url=__WEB__.'?'.C('PATHINFO_VAR').'='.$control.'/'.$action.$args.$html;
				}
			}
			break;
		case 3:// 普通模式
			if(C('URL_REWRITE')){	// 判断是否开房URL重写
				if(IS_GROUP){
					$url=__ROOT__.'?'.C('VAR_GROUP').'='.$group.'&'.C('VAR_CONTROL').'='.$control.'&'.C('VAR_ACTION').'='.$action.$args.$html;
				}else{
					$url=__ROOT__.'?'.C('VAR_CONTROL').'='.$control.'&'.C('VAR_ACTION').'='.$action.$args.$html;
				}
			}else{
				if(IS_GROUP){
					$url=__WEB__.'?'.C('VAR_GROUP').'='.$group.'&'.C('VAR_CONTROL').'='.$control.'&'.C('VAR_ACTION').'='.$action.$html;
				}else{
					$url=__WEB__.'?'.C('VAR_CONTROL').'='.$control.'&'.C('VAR_ACTION').'='.$action.$html;
				}
			}
			break;
			default:
				error('URL类型错误！！');
	}
	
	// 跳转
	if ($redrict) {
		header('location:'.$url);
	}else{
		return $url;
	}
}

/**
 * 调用函数
 * @param array() $var
 * @param string $boole
 */
function p($var,$boole=false){
	if($boole){
		var_dump($var);
	}else{
		echo '<pre/>';
		print_r($var);
	}
}


/**
 * 载入文件，loadFile函数的别名
 * @access public
 * @param string $fileName 要载入的文件名
 * @return boolean
 */
function import($fileName){
	return loadFile($fileName);
}


/**
 * 实例化对象
 * @param string $class
 * @param string $method
 * @param string or array $args
 * @return Ambigous <NULL, mixed, unknown>
 */
// function O($class,$method='',$args=array()){
// 	static $object=array();
// 	$name=empty($args)?$class.$method:$class.$method._sha1($args);
// 	if(!isset($object[$name])){
// 		$obj=new $class();
// 		if(!is_null($method)&&method_exists($obj, $method)){
// 			if(!empty($args)){
// 				$object[$name]=call_user_func_array(array(&$obj,$method),array($args));
// 			}else{
// 				$object[$name]=$obj->$method();
// 			}
// 		}else{
// 			$object[$name]=$obj;
// 		}
// 	}
// 	return $object[$name];
// }

function O($pathInfo,$args=array()){
	static $object=array();
	$info=preg_split('/[^a-zA-Z0-9]/', $pathInfo);
	switch (count($info)){
		case 1:
			$group=IS_GROUP?GROUP:'';
			$control=CONTROL;
			$action='';
			break;
		case 2:
			$group=IS_GROUP?GROUP:'';
			$control=$info[0];
			$action=$info[1];
			break;
		case 3:
			$group=$info[0];
			$control=$info[1];
			$action=$info[2];
			break;
			default:
				error('没有传递PATH_INFO参数！！');
	}
	$className=$control.C('DEFAULT_CONTROL_FIX');
	if(IS_GROUP){
		$classFile=GROUP_PATH.'/'.$group.'/'.C('APP_CONTROL_DIR').'/'.$className.'.'.ltrim(C('DEFAULT_CLASS_FIX'),'.').'.php';
	}else{
		$classFile=APP_PATH.'/'.$group.'/'.C('APP_CONTROL_DIR').'/'.$className.'.'.ltrim(C('DEFAULT_CLASS_FIX'),'.').'.php';
	}
	loadFile($classFile);
	$name=empty($args)?sha1($pathInfo):_sha1($pathInfo.implode(',', $args));
	if(isset($object[$name]))return $object[$name];
	$obj=new $className();
	if(!empty($action)&&method_exists($obj, $action)){
		if(!empty($args)){
			$object[$name]=call_user_func_array(array(&$obj,$action), $args);
		}else{
			$object[$name]=$obj->$action();
		}
	}else{
		$object[$name]=$obj;
	}
	return $object[$name];
}

/**
 * 解析Foreach语句的回调函数
 * 放在此处是为避免重定义
 * @param array $arg
 * @return string
 */
function replaceForeach($arg){
	if(isset($arg[3])){
		$str='<?php if(isset($'.$arg[1].')):foreach($'.$arg[1].' as $'.$arg[3].'=>$'.$arg[2].'):?>';
	}else{
		$str='<?php if(isset($'.$arg[1].')):foreach($'.$arg[1].' as $'.$arg[2].'):?>';
	}
	return $str;
}

/**
 * 正则替换模板关联数组标签调用函数
 * @param array $args
 * @return string
 */
function replaceArray($args){
	$key='';
	$arg = explode('.', trim($args[2],'.'));
	$str  ='<?php ';
	$arr=$args[1];
	foreach ($arg as $v){
		$key.='["'.$v.'"]';
	}
	$str.='if(isset('.$arr.$key.')){echo'.$arr.$key.';}';
	$str .='?>';
	return $str;
}


/**
 * 生成唯一序列号
 * @param string or array $var
 * @return string
 */
function _sha1($var){
	return sha1(serialize($var));
}

/**
 * 实例化控制器
 * @param string $control
 * @return Ambigous <>|Ambigous <unknown>|boolean
 */
function A($control){
	if(strstr($control,'.')){
		$arr=explode('.', $control);
		$group=GROUP_NAME.'/'.ucfirst($arr[0]).'/';
		$control=ucfirst($arr[1]);
	}else{
		$group=IS_GROUP?GROUP_NAME.'/'.ucfirst(GROUP).'/':'';
	}
	static $contObj=array();
	$control=$control.ucfirst(C('DEFAULT_CONTROL_FIX'));
	if(isset($contObj[$control])){
		return $contObj[$control];
	}else{
		$control_file=APP_PATH.'/'.$group.C('APP_CONTROL_DIR').'/'.$control.strtolower(C('DEFAULT_CLASS_FIX')).'.php';
		if(!is_file($control_file)){
			if(DEBUG){
				error($control_file.'‘文件不存在！！');
			}else{
				_404();
			}
		}
		loadFile($control_file);
		if(class_exists($control)){
			$contObj[$control]=new $control();
			return $contObj[$control];
		}else{
			return false;
		}
	}
}

/**
 * 提示性错误
 * @param  $e
 */
function notic($e){
	if(DEBUG&&C('NOTIC_SHOW')){
		$time=number_format(microtime(true)-Debug::$runtime['APP_START'],4);
		$memory=memory_get_peak_usage();
		$message=$e[1];
		$file=$e[2];
		$line=$e[3];
		$msg='<h2 style="width:980px;margin:20px auto 0;padding-left:20px;line-height:1.8em;background:#999;color:#fff;font-size:12px;font-weight:400;box-shadow:0 0 5px #999;" >NOTIC:'.$message.'</h2>
		<div style="box-shadow:0 0 5px #999;width:1000px;margin:0 auto 20px;">
			<table style="width:1000px;margin:0 auto;" border="0" cellpadding="0"  cellspacing="1" bgcolor="#ccc">
				<tr>
					<td align="center" style="background:#fff">Time</td>
					<td align="center" style="background:#fff">Memory</td>
					<td align="center" style="background:#fff">FilePath</td>
					<td align="center" style="background:#fff">Line</td>
				</tr>
				<tr>
					<td align="center" style="background:#fff">'.$time.'</td>
					<td align="center" style="background:#fff">'.$memory.'</td>
					<td align="center" style="background:#fff">'.$file.'</td>
					<td align="center" style="background:#fff">'.$line.'</td>
				</tr>
			</table>		
		</div>';
		echo $msg;
	}else{
		Log::setLog('错误类型：['.$e[5].']　错误编号：['.$e[0].']　错误信息：'.$e[1].'　错误文件：'.$e[2].'　错误行号：['.$e[3].']');
	}
}

/**
 * 
 * @param string $msg
 */
function error($msg){
	if(!is_array($msg)){
		$backtrac=debug_backtrace();
		$e['msg']='<span style="color:red;">'.$msg.'</span>';
		$info='';
		foreach ($backtrac as $v){
			$file=isset($v['file'])?$v['file']:'';
			$line=isset($v['line'])?'['.$v['line'].']':'';
			$class=isset($v['class'])?$v['class']:'';
			$type=isset($v['type'])?$v['type']:'';
			$function=isset($v['function'])?$v['function'].'()':'';
			$info.=$file.$line.$class.$type.$function.'<br/>';
		}
		$e['info']= $info;
	}else{
		$e=$msg;
	}
	if(!DEBUG){
		Log::write(strip_tags($msg)."\n\r");
		$e['msg']=C('ERROR_MESSAGE');
	}
	$display=C('DEBUG_SHOW')?'block':'none';
	include C('DEBUG_TPL');
	exit();
}

/**
 * 载入文件
 * @param string $file
 */
function loadFile($file=null){
	static $fileArr=array();
	if(is_null($file))return $fileArr;
	$filePath=realpath($file);
	if(isset($fileArr[$filePath])){
		return $fileArr[$filePath];
	}
	if(!is_file($filePath)||!is_readable($filePath)){
		trigger_error($file.' 文件不存在或者没有读取权限 ！！',E_USER_ERROR);
	}
	require $filePath;
	$fileArr[$filePath]=true;
	return $fileArr[$filePath];
}

/**
 * 配置项操作函数
 * @param string $name
 * @param string $value
 * @return multitype:|NULL|Ambigous <NULL, string>
 */
function C($name=null,$value=null){
	static $config=array();
	if(is_null($name))return $config;
	if(is_string($name)){
		$name=strtolower($name);
		if(!strstr($name,'.')){
			if(is_null($value)){
				return isset($config[$name])?$config[$name]:null;
			}else{
				$config[$name]=$value;
			}
		}else{
			$name=explode('.', $name);
			if(is_null($value)){
				return isset($config[$name[0][1]])?$config[$name[0][1]]:null;
			}else{
				$config[$name[0][1]]=$value;
			}
		}
	}
	if(is_array($name)){
		$config=array_merge($config,array_change_key_case($name,CASE_LOWER));
	}
}

/**
 * 是否为AJAX提交
 * @return boolean
 */
function ajax_request(){
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		return true;
	}else{
		return false;
	}		
}

function delSpace($var){
	if(is_file($var)){
		$data=file_get_contents($var);
	}else{
		$data=$var;
	}
	$data=rtrim(ltrim(trim($data),'<?php'),'?>');
	$pattern=array('/\/\*.*?\*\/\s*/is','/(?<!http:|https:)\/\/.*?[\n\r]/is','/(?!\w)\s+?(?!\w)/');
	return preg_replace($pattern, '', $data);
}

function _404(){
	include C('TPL_404_ERROR');
	exit();
}

function globalVar($args=''){
	$globVar=array(
			'get'			=>$_GET,
			'post'			=>$_POST,
			'session'		=>$_SESSION,
			'cookie'		=>$_COOKIE,
			'server'		=>$_SERVER,
			'env'			=>$_ENV,
	);
	if(empty($args)){
		return $globVar;
	}
	$glob=array();
	if(is_array($args)){
		foreach ($args as $k){
			$glob[$k]=$globVar[$k];
		}
	}elseif(is_string($args)){
		foreach (explode(',', $args) as $k){
			$glob[$k]=$globVar[$k];
		}
	}
	return $glob;
}

/**
 * 接收GET及POST数据并且过滤
 */
function G($arg='',$filter=''){
	$arrData=globalVar(array('post','get','cookie','session'));
	$pattern='/[\.\|\-\+\_\>\@\%]+/';
	$tem=preg_split($pattern, $arg);
	$type=$tem[0];
	$name=isset($tem[1])?$tem[1]:'';
	if(empty($type)){
		return filter($arrData,$filter);
	}
	switch ($type){
		case 'post':
			if(empty($name)){
				return filter($arrData['post'],$filter);
			}else{
				return filter($arrData['post'][$name],$filter);
			}
			break;
		case 'get':
			if(empty($name)){
				return filter($arrData['get'],$filter);
			}else{
				return filter($arrData['get'][$name],$filter);
			}
			break;
		case 'cookie':
			if(empty($name)){
				return filter($arrData['cookie'],$filter);
			}else{
				return filter($arrData['cookie'][$name],$filter);
			}
			break;
		case 'session':
			if(empty($name)){
				return filter($arrData['session'],$filter);
			}else{
				return filter($arrData['session'][$name],$filter);
			}
			break;
		default :
			return null;
			break;
	}
}

/**
 * 将表达式转换成数组
 * 例如：value='123'转换成array('value'=>'123');
 */
function expToArr($args){
	if(empty($args))return false;
	$arr=array();
	if(is_string($args)){
		$arr=(array)$args;
	}elseif(is_array($args)){
		$arr=$args;
	}
	$newArr=array();
	foreach ($arr as $v){
		$res=preg_replace('/"/', '', $v);
		$tem=preg_split('/\=/', $res);
		$con=isset($tem[2])?'='.$tem[2]:'';
		$newArr[$tem[0]]=$tem[1].$con;
	}
	return $newArr;
}

/**
 * 快速缓存 以文件形式缓存
 * @param String $name 缓存KEY
 * @param bool $value 删除缓存
 * @param string $path 缓存目录
 * @return bool
 */
function F($name, $value = false, $path = CACHE_PATH)
{
	static $_cache = array();
	$cacheFile = rtrim($path, '/') . '/' . $name . '.php';
	if (is_null($value)) {
		if (is_file($cacheFile)) {
			unlink($cacheFile);
			unset($_cache[$name]);
		}
		return true;
	}
	if ($value === false) {
		if (isset($_cache[$name])){
			return $_cache[$name];
		}else{
			$_cache[$name]=is_file($cacheFile) ? include $cacheFile : null;
			return $_cache[$name];
		}
		
	}
 	$data = "<?php if(!defined('START_PATH'))exit;\nreturn " . compress(var_export($value, true)) . ";\n?>";
	is_dir($path) || dir_create($path);
	if (!file_put_contents($cacheFile, $data)) {
		return false;
	}
	$_cache[$name] = $data;
	return true;
}


/**
 * @param timestamp $time
 * @return string
 */
function formatTime($time){
	$diffTime=time()-$time;
	$str='';
	switch ($diffTime){
		case $diffTime<60:
			$str=$diffTime.'秒之前';
			break;
		case $diffTime<pow(60, 2):
			$str=floor($diffTime/60).'分钟之前';
			break;
		case $diffTime<pow(60,2)*24:
			$str=floor($diffTime/pow(60,2)).'小时之前';
			break;
		case $diffTime<pow(60, 2)*24*idate('t'):
			$str=floor($diffTime/(pow(60, 2)*24)).'天之前';
			break;
		default :
			$str=date('Y-m-d H:i:s',$time);
			break;
	}
	return $str;
}

function get_client_ip($type = 0){
	$type = intval($type);
	$ip = ''; //保存客户端IP地址
	if (isset($_SERVER)) {
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$ip = $_SERVER["REMOTE_ADDR"];
		}
	} else {
		if (getenv("HTTP_X_FORWARDED_FOR")) {
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		} else if (getenv("HTTP_CLIENT_IP")) {
			$ip = getenv("HTTP_CLIENT_IP");
		} else {
			$ip = getenv("REMOTE_ADDR");
		}
	}
	$long = ip2long($ip);
	$clientIp = $long ? array($ip, $long) : array("0.0.0.0", 0);
	return $clientIp[$type];
}

/**
 * 获取RGB颜色(将16进制转换成10进制)
 * @param unknown $color
 * @return multitype:number
 */
function getRgb($color){
	$color=ltrim($color,'#');
	$rgb=array();
	$rgb['r']=hexdec(substr($color,0,2));
	$rgb['g']=hexdec(substr($color,2,2));
	$rgb['b']=hexdec(substr($color,4,2));
	return $rgb;
}

function createDir($pathname){
	is_dir($pathname)||mkdir($pathname,0777,true);
}

function clearCache(){
	$cacheDir=ROOT_PATH.'/'.C('RUNTIME_DIR').'/'.C('CACHE_DIR').'/';
	$allFile=glob($cacheDir.'*');
	if($allFile){
		foreach ($allFile as $value){
			unlink($value);
		}
	}
	return true;
}

function getPrevUrl(){
	return $_SERVER['HTTP_REFERER'];
}

function getUid(){
	$userVar=$_SERVER["HTTP_USER_AGENT"];
	$userIp=$_SERVER["REMOTE_ADDR"];
	return sha1($userVar.$userIp);
}




/******************************************过滤函数********************************************/


/**
 * 基础过滤函数
 * @param array or string $data 要过滤的数据
 * @param string $filter 过滤函数名称
 * @return $data 返回过滤后的数据
 */
function filter($data,$filter=''){
	$filter=empty($filter)?C('DEFAULT_FILTER'):$filter;
	$pattern='/[^\w\-]+/';
	$filterArr=preg_split($pattern, $filter);
	foreach ($filterArr as $f){
		if(empty($f))continue;
		if(!function_exists($f)){
			error($f.'函数不存在！！');
		}
		if(is_array($data)) {
			foreach ($data as $key=>$value){
				if(is_array($value)){
					filter($value,$f);
				}elseif(is_object($value)){
					filter($value,$f);
				}else{
					$data[$key]=$f($value);
				}
			}
		}elseif (is_object($data)) {
			foreach ($data as $key=>$value){
				if(is_array($value)){
					filter($value,$f);
				}elseif(is_object($value)){
					filter($value,$f);
				}else{
					$data->$key=$f($value);
				}
			}
		}elseif(is_string($data)||is_numeric($data)){
			$data=$f($data);
		}
	}
	return $data;
}


/**
 * 自动转义单双引号
 * @param multipartType $data 要转义的数据
 * @return $data 返回转义后的数据
 */
function slashes_e($data){
	if(!get_magic_quotes_gpc()){
		return filter($data,'addslashes');
	}else{
		return $data;
	}
}

/**
 * 反转义数据
 * @param mutiparyType $data 需要反转义的数据
 * @return $data 返回反转义后的数据
 */
function slashes_d($data){
	return filter($data,'stripslashes');
}

/**
 * 删除HTML标签
 * @param multipartType $data
 * @return $data
 */
function stripHtml($data){
	return filter($data,'strip_tags');
}

/**
 * 实体化HTML标签
 * @param multipartType $data
 * @return $data
 */
function html_e($data){
	return filter($data,'htmlspecialchars');
}



/**
 * 恢复实体化HTML标签
 * @param unknown_type $data
 * @return string
 */
function html_d($data){
	return filter($data,'htmlspecialchars_decode');
}

/**
 * 关键词过滤
 * @param unknown_type $data
 * @param unknown_type $keyWord
 * @return mixed
 */
function filterWords($data,$keyWord=''){
	$pattern='/'.implode('|', C('MINGAN_WORDS')).'/';
	$pattern=!empty($keyWord)?$keyWord:$pattern;
	if (is_array($data)){
		foreach ($data as $value){
			if (preg_match($pattern, $value)){
				$data=preg_replace($pattern, '*', $value);
			}
		}
	}else{
		if (preg_match($pattern, $data)){
			$data=preg_replace($pattern, '*', $data);
		}
	}
	return  $data;
}

/**
 * 过滤空数组
 * @param array $arr
 * @return array
 */
function filterArr($arr){
	foreach ($arr as $key=>$value){
		if(is_array($value)||is_object($value))filterArr($value);
		if(empty($value)){
			if(is_object($arr))unset($arr->$key);
			if(is_array($arr))unset($arr[$key]);
		}
	}
	return $arr;
}

/**
 * 将二维数组转换成对象数组
 * @param unknown_type $arr
 * @return StdClass
 */
function arrayToObj($arr){
	if(!is_array($arr))return;
	foreach ($arr as $key=>$value){
		if (is_array($value)){
			$arr[$key]=(object)$value;
			arrayToObj($value);
		}
	}
	return $arr;
}

function mbsubstr($str,$length,$charset='utf8'){
	if(strlen($str)<$length)return $str;
	return mb_substr($str, 0,$length,$charset);
}



?>