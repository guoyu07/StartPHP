<?php
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/

class Tree{
	
	public static  function getTree(array$data,$prikey='id',$parid='pid',$pid=0,$level=0){
		if(!is_array($data))return;
		$tree=array();
		foreach ($data as $key=>$value){
			if($value->$parid==$pid){
				$value->level=$level;
				$value->child=self::getTree($data,$prikey,$parid,$value->$prikey,$level+1);
				$tree[]=$value;
			}
		}
		return $tree;
	}
	
	public static function unTree($arr){
		if(!is_array($arr)) return;
		static $newarr=array();
		foreach ($arr as $key=>$value){
			if(isset($value->child)){
				$newarr[]=$value;
				$child=$value->child;
				unset($value->child);
				self::unTree($child);
			}else{
				$newarr[]=$value;
			}
		}
		foreach ($newarr as $k=>$v){
			$newarr[$k]->html=self::addhtml($v->level);
		}
		return $newarr;
	}
	
	private static function addhtml($level){
		$html='';
		for($i=0;$i<$level;$i++){
			$html.='&nbsp;&nbsp';
		}
		return $html;
	}
}

?>