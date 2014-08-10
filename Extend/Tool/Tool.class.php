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
/**
 * 工具类
 * @author 舒良府
 *
 */
class Tool {
	
	
	/**
	 * 自动转义单双引号
	 * @param multipartType $data 要转义的数据
	 * @return $data 返回转义后的数据
	 */
	public static function addslashe($data){
		if(!get_magic_quotes_gpc()){
			return self::filter($data,'addslashes');
		}else{
			return $data;
		}
	}
	
	/**
	 * 反转义数据
	 * @param mutiparyType $data 需要反转义的数据
	 * @return $data 返回反转义后的数据
	 */
	public static function unslashe($data){
		return self::filter($data,'stripslashes');
	}
	
	/**
	 * 基础过滤函数
	 * @param array or string $data 要过滤的数据
	 * @param string $filter 过滤函数名称
	 * @return $data 返回过滤后的数据
	 */
	public static function filter($data,$filter=''){
		$filter=empty($filter)?C('DEFAULT_FILTER'):$filter;
		if(is_array($data)) {
			foreach ($data as $key=>$value){
				if(is_array($value)){
					self::filter($value,$filter);
				}if(is_object($value)){
					self::filter($value,$filter);
				}else{
					$data[$key]=$filter($value);
				}
			}
		}elseif (is_object($data)) {
			foreach ($data as $key=>$value){
				if(is_array($value)){
					self::filter($value,$filter);
				}elseif(is_object($value)){
					self::filter($value,$filter);
				}else{
					$data->$key=$filter($value);
				}
			}
		}elseif(is_string($data)||is_numeric($data)){
			$data=$filter($data);
		}
		return $data;
	}

	/**
	 * 删除HTML标签
	 * @param multipartType $data
	 * @return $data
	 */
	public static function stripHtml($data){
		return self::filter($data,'strip_tags');
	}
	
	/**
	 * 实体化HTML标签
	 * @param multipartType $data
	 * @return $data
	 */
	public static function html_e($data){
		return self::filter($data,'htmlspecialchars');
	}
	
	
	
	/**
	 * 恢复实体化HTML标签
	 * @param unknown_type $data
	 * @return string
	 */
	public static function html_d($data){
		return self::filter($data,'htmlspecialchars_decode');
	}
	
	/**
	 * 关键词过滤
	 * @param unknown_type $data
	 * @param unknown_type $keyWord
	 * @return mixed
	 */
	public static function filterWords($data,$keyWord=''){
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
		return $data;
	}
	
	/**
	 * 过滤空数组
	 * @param array $arr
	 * @return array 
	 */
	public static function fileterArr($arr){
		foreach ($arr as $key=>$value){
			if(is_array($value)||is_object($value))self::fileterArr($value);
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
	public static function arrayToObj($arr){
		if(!is_array($arr))return;
		foreach ($arr as $key=>$value){
			if (is_array($value)){
				$arr[$key]=(object)$value;
				self::arrayToObj($value);
			}
		}
		return $arr;
	}
	
	/**
	 * @param timestamp $time
	 * @return string
	 */
	public static function formatTime($time){
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
	
	/**
	 * 获取RGB颜色(将16进制转换成10进制)
	 * @param unknown $color
	 * @return multitype:number
	 */
	public static  function getRgb($color){
		$color=ltrim($color,'#');
		$rgb=array();
		$rgb['r']=hexdec(substr($color,0,2));
		$rgb['g']=hexdec(substr($color,2,2));
		$rgb['b']=hexdec(substr($color,4,2));
		return $rgb;
	}
	
	public static function createDir($pathname){
		is_dir($pathname)||mkdir($pathname,0777,true);
	}
	
	public static function clearCache(){
		$cacheDir=ROOT_PATH.'/'.C('RUNTIME_DIR').'/'.C('CACHE_DIR');
		$allFile=glob($cacheDir.'/*');
		if($allFile){
			foreach ($allFile as $value){
				unlink($value);
			}
		}
		return true;
	}
	
	public static function getPrevUrl(){
		return $_SERVER['HTTP_REFERER'];
	}
	
	public static function verifySite(){
		$js=<<<str
<script type="text/javascript">
		function fn(data){
			if(!data){
				alert("网站已经过期，请及时续费！客服电话：18007733715");
				location.href="?a=error";
			}
		}
</script>
<script type="text/javascript" src="http://localhost/bzyf1235/ck/index.php?sn=1&call=fn"></script>
		
str;
		echo $js;
	}
	
	public static function getUid(){
		$userVar=$_SERVER["HTTP_USER_AGENT"];
		$userIp=$_SERVER["REMOTE_ADDR"];
		return sha1($userVar.$userIp);
	}
	
	
	
	
	
	
}
	
?>