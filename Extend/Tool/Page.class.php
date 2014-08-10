<?php
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-11-20
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/

/**
 * 页面处理类
 * @author 舒良府
 *
 */
final class Page{
	public static $staticTotalPage;	// (生成静态页面)总页数 √
	public static $staticUrl;			// 当前URL地址 √
	public static $staticFix='';			// 静态页面页码地址后缀
	private $pageSize;					// 每页显示条数 √
	private $pageRowNum;			// 显示分页页码数量 √
	private $totalNum;					// 数据总数 √
	private $totalPage;					// 总分页数量 √
	private $page;	 					// 当前页码 √
	private $url; 							// 页面地址 √
	private $desc = array(); 			// 文字描述 √
	
	/**
	 * 初始化构造方法
	 * @param number $total 			数据总数
	 * @param string $pageSize 		每页显示条数
	 * @param string $pageRowNum 	页码显示个数
	 * @param string $pageStyle 		分页样式
	 */
	public function __construct($total,$pageSize='',$pageRowNum='',$pageStyle=''){
		$this->totalNum=$total;
		$this->pageSize=empty($pageSize)?C('PAGE_SIZE'):$pageSize;
		$this->pageRowNum=empty($pageRowNum)?C('PAGE_ROW_SIZE'):$pageRowNum;
		$this->pageStyle=empty($pageStyle)?C('PAGE_STYLE'):$pageStyle;
		$this->desc=C('PAGE_INFO');
		$this->totalPage=ceil($this->totalNum/$this->pageSize);
		self::$staticTotalPage=$this->totalPage;
		$this->page=$this->selfPage();
		$this->url=$this->selfUrl();
	}
	
	/**
	 * 重组当前URL地址
	 * @return string
	 */
	private function selfUrl(){
		if(isset($_GET[C('VAR_PAGE')]))unset($_GET[C('VAR_PAGE')]);
		if(IS_GROUP&&isset($_GET[C('VAR_GROUP')])&&!empty($_GET[C('VAR_GROUP')])){
			$group=$_GET[C('VAR_GROUP')].'/';
		}else{
			if(isset($_GET[C('VAR_GROUP')])){
				unset($_GET[C('VAR_GROUP')]);
			}
			$group='';
		}
		if(isset($_GET[C('VAR_CONTROL')])&&!empty($_GET[C('VAR_CONTROL')])){
			$control=$_GET[C('VAR_CONTROL')].'/';
			unset($_GET[C('VAR_CONTROL')]);
		}else{
			if(isset($_GET[C('VAR_CONTROL')])){
				unset($_GET[C('VAR_CONTROL')]);
			}
			$control='';
		}
		if(isset($_GET[C('VAR_ACTION')])&&!empty($_GET[C('VAR_ACTION')])){
			$action=$_GET[C('VAR_ACTION')];
			unset($_GET[C('VAR_ACTION')]);
		}else{
			if(isset($_GET[C('VAR_ACTION')])){
				unset($_GET[C('VAR_ACTION')]);
			}
			$action='';
		}
		unset($_GET[C('VAR_GROUP')]);
		
		
		$url=array('url'=>$group.$control.$action,'args'=>$_GET);
		return $url;
	}
	
	/**
	 * 获取当前页码
	 */
	private function selfPage(){
		$selfPage=isset($_GET[C('VAR_PAGE')])?(int)$_GET[C('VAR_PAGE')]:1;
		if($selfPage<1){
			$selfPage=1;
		}
		if($selfPage>$this->totalPage&&$this->totalPage>0){
			$selfPage=$this->totalPage;
		}
		return $selfPage;
	}
	
	/**
	 * 获取LIMIT参数
	 * @return string
	 */
	public function limit(){
		return ($this->page-1)*$this->pageSize.','.$this->pageSize;
	}
	
	/**
	 * 分页页码
	 * @param string $class 添加a标签的class样式
	 * @return string
	 */
	private function pageList($class){
		$midd=ceil(($this->pageRowNum-1)/2);
		$pageList = '';
		$start = max(1, min($this->page - ceil(($this->pageRowNum-1)/ 2), $this->totalPage - $this->pageRowNum+1));
		$end = min($this->pageRowNum-1 + $start, $this->totalPage);
		$list='';
		if($end==1)return '';
		for ($i=$start;$i<=$end;$i++){
			$page=array(C('VAR_PAGE')=>$i);
			$args=array_merge($this->url['args'],$page);
			$url=empty(self::$staticUrl)?U($this->url['url'],$args):self::$staticUrl.$i.self::$staticFix;
			if($i==$this->page){
				$list.='<a href="'.$url.'" class="'.$class.' self">'.$i.'</a>';
			}else{
				if(empty($class)){
					$list.='<a href="'.$url.'">'.$i.'</a>';
				}else{
					$list.='<a href="'.$url.'" class="'.$class.'">'.$i.'</a>';
				}
			}
		}
		return $list;
	}
	
	//上一页
	private function pre($class){
		if($this->totalPage==1)return '';
		if($this->page-1<=0){
			$url='javascript:void(0)';
		}else{
			$page=array(C('VAR_PAGE')=>$this->page-1);
			$args=array_merge($this->url['args'],$page);
			$url=empty(self::$staticUrl)?U($this->url['url'],$args):self::$staticUrl.($this->page-1).self::$staticFix;
		}
		$pre='';
		if(empty($class)){
			$pre.='<a href="'.$url.'">'.$this->desc['pre'].'</a>';
		}else{
			$pre.='<a href="'.$url.'" class="'.$class.'">'.$this->desc['pre'].'</a>';
		}
		return $pre;
	}
	
	//下一页
	private function next($class){
		if($this->totalPage==1)return '';
		if($this->page+1>$this->totalPage){
			$url='javascript:void(0)';
		}else{
			$page=array(C('VAR_PAGE')=>$this->page+1);
			$args=array_merge($this->url['args'],$page);
			$url=empty(self::$staticUrl)?U($this->url['url'],$args):self::$staticUrl.($this->page+1).self::$staticFix;
		}
		$next='';
		if(empty($class)){
			$next.='<a href="'.$url.'">'.$this->desc['next'].'</a>';
		}else{
			$next.='<a href="'.$url.'" class="'.$class.'">'.$this->desc['next'].'</a>';
		}
		return $next;
	}
	
	//第一页
	private function first($class){
		if($this->totalPage==1)return '';
		$page=array(C('VAR_PAGE')=>1);
		$args=array_merge($this->url['args'],$page);
		$url=empty(self::$staticUrl)?U($this->url['url'],$args):self::$staticUrl.(1).self::$staticFix;
		$first='';
		if(empty($class)){
			$first.='<a href="'.$url.'">'.$this->desc['first'].'</a>';
		}else{
			$first.='<a href="'.$url.'" class="'.$class.'">'.$this->desc['first'].'</a>';
		}
		return $first;
	}
	
	//尾页
	private function end($class){
		if($this->totalPage==1)return '';
		$page=array(C('VAR_PAGE')=>$this->totalPage);
		$args=array_merge($this->url['args'],$page);
		$url=empty(self::$staticUrl)?U($this->url['url'],$args):self::$staticUrl.$this->totalPage.self::$staticFix;
		$end='';
		if(empty($class)){
			$end.='<a href="'.$url.'">'.$this->desc['end'].'</a>';
		}else{
			$end.='<a href="'.$url.'" class="'.$class.'">'.$this->desc['end'].'</a>';
		}
		return $end;
	}
	
	//下拉选择
	private function select(){
		if($this->totalPage==1)return '';
		$select='';
		$select.='<select onChange="location.href=(this.value)" class="inline">';
		for($i=1;$i<=$this->totalPage;$i++){
			$page=array(C('VAR_PAGE')=>$i);
			$args=array_merge($this->url['args'],$page);
			$url=empty(self::$staticUrl)?U($this->url['url'],$args):self::$staticUrl.$i.self::$staticFix;
			if($this->page==$i){
				$select.='<option value="'.$url.'" selected="selected">'.$i.'</option>';
			}else{
				$select.='<option value="'.$url.'">'.$i.'</option>';
			}
			
		}
		$select.='</select>';
		return $select;
	}
	
	
	
	// 表彰提交??->此方法尚未完善
	private function go(){
		if($this->totalPage==1)return '';
		$page=array(C('VAR_PAGE')=>$this->totalPage);
		$args=array_merge($this->url['args'],$page);
		$url=U($this->url['url'],$this->url['args'],'');
		$go='';
		$go.='<form method="get">';
		$go.='<input type="text" name="'.C('VAR_PAGE').'" id="page" />';
		$go.='<input type="button" value="确定" onclick="location.href=\''.$url.'\'+document.getElementById(\"page\").value()"/>';
		$go.='</form>';
		return $go;
	}
	
	//总分页数
	private function totalNum($class){
		return '<a class="'.$class.'">'.$this->totalNum.$this->desc['unit'].'</a>';
	}
	
	//当前页及总分页数
	private function totalPage($class){
		return '<a class="'.$class.'">'.$this->page.'/'.$this->totalPage.'</a>';
	}
	
	public function show($style='',$class=''){
		$style=empty($style)?C('PAGE_STYLE'):$style;
		switch ($style){
			case 1:
				return $this->first($class).$this->pre($class).$this->pageList($class).$this->next($class).$this->end($class).$this->totalNum($class).$this->totalPage($class);
				break;
			case 2:
				return $this->totalNum($class).' '.$this->totalPage($class).' '.$this->first($class).$this->pre($class).' '.$this->pageList($class).' '.$this->next($class).$this->end($class);
				break;
			case 3:
				
				break;
			case 4:
				
				break;
			default:
				$this->show(1);
				break;
		}
	}
	
}

?>