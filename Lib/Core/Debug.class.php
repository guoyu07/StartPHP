<?php
defined('START_PATH')||exit();
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


class Debug{
	public static $runtime=array();					//运行时间
	public static $memory=array();				//占用内存
	public static $memory_peak=array();		//内存峰值
	public static $info = array();					//信息内容
	public static $sqlExeArr = array();				//所有发送的SQL语句
	public static $tpl = array();						//编译模板
	public static $cache=array(						//缓存记录
			'write_s'		=>0,
			'write_f'		=>0,
			'read_s'		=>0,
			'read_f'		=>0
			
	);
	
	
	public static function start($start){
		self::$runtime[$start]=microtime(true);
		self::$memory[$start]=memory_get_usage();
		self::$memory_peak[$start]=memory_get_peak_usage();
	}
	
	/**
	 * 项目运行时间
	 * @param string $start
	 * @param string $end
	 * @param number $decimals
	 * @return string
	 */
	public static function runtime($start,$end='',$decimals=4){
		if(!isset(self::$runtime[$start])){
			error('必须设置项目运行时间起点！！');
		}
		if(empty(self::$runtime[$end])){
			self::$runtime[$end]=microtime(true);
			return number_format(self::$runtime[$end]-self::$runtime[$start],$decimals);
		}
	}
	
	/**
	 * 内存占用峰值
	 * @param unknown $start
	 * @param string $end
	 * @return boolean
	 */
	static function memory_peak($start,$end=''){
		if(!isset(self::$memory_peak[$start])){
			return false;
		}
		if(!empty($end)){
			self::$memory_peak[$end]=memory_get_peak_usage();
		}
		return max(self::$memory_peak[$start],self::$memory_peak[$end]);
	}
	
	static function show($start,$end){
		$msg='运行时间：'.self::runtime($start, $end).'秒　内存峰值：'.number_format(self::memory_peak($start,$end)/1024).'KB';
// 		$load_file_list=loadFile();
		$load_file_list=get_included_files();
		$info='';
		$i=1;
		$display=C('DEBUG_SHOW')?'block':'none';
		foreach ($load_file_list as $k=>$v){
			$info.='['.$i++.'] '.$v.'<br/>';
		}
		$e['info']=$info.'<p>'.$msg.'</p>';
		include C('DEBUG_TPL');
		
	}
	
	
	
}











?>