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

final class Dir{
	
	/**
	 * 转换成标准目录结构
	 * @access public 
	 * @param string $dirName 目录名
	 * @return string $dirName 目录名
	 */
	public static function dirPath($dirName){
		$dirName=realpath($dirName);
		return str_replace('\\', '/', trim($dirName));
	}
	
	/**
	 * 获取文件后缀
	 * @access public 
	 * @param string $fileName 文件名
	 * @return extension 文件后缀
	 */
	public static function getExt($fileName){
		$pathInfo=pathinfo($fileName);
		return $pathInfo['extension'];
	}
	
	/**
	 * 获得目录信息
	 * @param string $dirName 目录名
	 * @param string $ext 文件后缀
	 * @param string $child 是否扫描子目录
	 * @param array $list 保存的数组
	 * @return array 返回所有扫描的信息
	 */
	public static function tree($dirName,$ext='',$child=false,$list=array()){
		$dirName=self::dirPath($dirName);
		if(is_array($ext)){
			$ext=implode('|', $ext);
		}
		static $id=0;
		foreach (glob($dirName.'/*') as $v){
			$id++;
			if (!$ext||preg_match("/\.($ext)/i", $v)) {
				$list[$id]['name']=basename($v);
				$list[$id]['path']=realpath($v);
				$list[$id]['type']=filetype($v);
				$list[$id]['mktime']=filemtime($v);
				$list[$id]['ratime']=fileatime($v);
				$list[$id]['size']=filesize($v);
				$list[$id]['isread']=is_readable($v);
				$list[$id]['iswrite']=is_writeable($v);
				if ($child){
					if(is_dir($v)){
						$list=self::tree($v,$ext,$child,$list);
					}
				}
			}
		}
		return $list;
	}
	
	/**
	 * 只获取目录结构
	 * @param string $dirName
	 * @param number $pid
	 * @param string $child
	 * @param array $list
	 * @return string
	 */
	public static function treeDir($dirName,$pid=0,$child=false,$list=array()){
		$dirName=self::dirPath($dirName);
		static $id=0;
		foreach (glob($dirName.'/*') as $v){
			if(is_dir($v)){
				$id++;
				$list[$id]['id']=$id;
				$list[$id]['pid']=$pid;
				$list[$id]['name']=basename($v);
				$list[$id]['path']=realpath($v);
				if(is_dir($v)){
					$list=self::treeDir($v,$id,$child,$list);
				}
			}
		}
		return $list;
	}
	
	/**
	 * 递归删除目录
	 * @access public 
	 * @param string $dirName
	 * @param string $self
	 * @return boolean
	 */
	public static function delDir($dirName,$self=true){
		$dirName=self::dirPath($dirName);
		if(!is_dir($dirName))return false;
		foreach (glob($dirName.'/*') as $v){
			is_dir($v)?self::delDir($v,true):unlink($v);
		}
		if ($self){
			return rmdir($dirName);
		}else{
			return count(glob($dirName.'/*'))>0?false:true;
		}
	}
	
	/**
	 * 创建目录
	 * @param string $dirName
	 * @param number $auth
	 * @return boolean
	 */
	public static function create($dirName,$auth=0777){
		return is_dir($dirName)||mkdir($dirName,$auth,true);
	}
	
	/**
	 * 复制文件或文件夹
	 * @access public 
	 * @param string $name 文件或文件夹路径
	 * @param string $target 目标文件或文件夹路径
	 * @return boolean
	 */
	public static function copy($name,$target){
		$oldName=self::dirPath($name);
		if(is_dir($oldName)){
			is_dir($target)||self::create($target);
			foreach (glob($oldName.'/*') as $v){
				$toFile=$target.'/'.basename($v);
				if(is_file($toFile))continue;
				if(is_dir($v)){
					self::copy($v, $toFile);
				}
				if(is_file($v)){
					copy($v, $toFile);
					chmod($toFile,0777);
				}
			}
		}elseif(is_file($oldName)){
			is_dir(dirname($target))||self::create(dirname($target));
			copy($oldName, $target);
		}else{
			error($name.'不存在，复制失败！！');
		}
		return true;
	}
	
	/**
	 * 扫描文件
	 * @param string $dirPath 	文件夹路径
	 * @param string $blooen 	布尔值，是否递归扫描
	 * @return array $arrFile 		返回文件名路径数组
	 */
	public static function scanFile($dirPath,$blooen=false){
		!is_dir($dirPath)&&error($dirPath.'不是一个目录') ;
		static $arrFile=array();
		$files=glob($dirPath.'/*');
		if($blooen){
			foreach ($files as $f){
				if(is_dir($f)){
					self::scanFile($f,$blooen);
				}elseif(is_file($f)){
					$arrFile[]=$f;
				}
			}
		}else{
			foreach ($files as $f){
				if(is_file($f)){
					$arrFile[]=$f;
				}
			}
		}
		return $arrFile;
	}
	
	
	
	
}

?>