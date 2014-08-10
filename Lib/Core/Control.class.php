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


defined('START_PATH')||exit();

/**
 * 控制器主类
 * @author 舒良府
 *
 */

class Control {
	private $vars=array();
	
	public function __construct(){
		$this->assign(globalVar());	// 分配全局变量
		if(method_exists($this,'__init')) {
			$this->__init();
		}
		if(isset($_GET['upload'])&&$_GET['upload']=='uploadify'){
			$this->upload();
		}
	}
	
	/**
	 * 分配模板变量
	 * @access  protected
	 * @param  $name 分配的变量名，也可以是关联数组
	 * @param  $value
	 * @return  Model
	 */
	public function assign($name,$value=null){
		if(is_null($value)&&is_array($name)){
			if(empty($name))return $this;
			foreach ($name as $k=>$v){
				$this->vars[$k]=$v;
			}
		}else{
			$this->vars[$name]=$value;
		}
		return $this;
	}
	
	/**
	 * 显示模板
	 * @param string $tplFile 		PATH_INFO形式'Index/Index/index'
	 * @param string $blooean 		此参数在跨应用生成纯静态时为ture,用于纠正编译及缓存路径
	 */
	protected function display($tplFile='',$blooean=false){
		$tplFile=empty($tplFile)?ACTION:$tplFile;
		$param=explode(C('PATHINFO_LIMIT'), $tplFile);
		if(IS_GROUP){	//分组的情况
			if(count($param)==3){
				$group=$param[0];
				$control=$param[1];
				$action=$param[2];
			}elseif(count($param)==2){
				$group=GROUP;
				$control=$param[0];
				$action=$param[1];
			}else{
				$group=GROUP;
				$control=CONTROL;
				$action=$param[0];
			}
		}else{	//非分组情况
			if(count($param)==2){
				$control=$param[0];
				$action=$param[1];
			}else{
				$control=CONTROL;
				$action=$param[0];
			}
		}
		//获取参数
		$pathInfo=pathinfo($tplFile);
		$tplfix=isset($pathInfo['extension'])?$pathInfo['extension']:null;
		$action=!is_null($action)&&!empty($action)?$action:ACTION;
		if(IS_GROUP){
			$group=!is_null($group)&&!empty($group)?$group:GROUP;
		}
		
		//获取模板后缀
		$tplSuffix=isset($pathInfo['extension'])?ltrim($pathInfo['extension'],'.'):ltrim(C('TPL_TEMPLATE_SUFFIX'),'.');
		$tplPath='';
		if(!$tplfix){
			if(IS_GROUP){
				$tplPath=GROUP_PATH.'/'.$group.'/'.C('APP_TPL_DIR').'/'.$control.'/'.$action.'.'.$tplSuffix;
			}else{
				$tplPath=APP_PATH.'/'.C('APP_TPL_DIR').'/'.$control.'/'.$action.'.'.$tplSuffix;
			}
		}else{
			if(IS_GROUP){
				$tplPath=GROUP_PATH.'/'.$group.'/'.C('APP_TPL_DIR').'/'.$control.'/'.$action;
			}else{
				$tplPath=APP_PATH.'/'.C('APP_TPL_DIR').'/'.$control.'/'.$action;
			}
		}
		if(!is_file($tplPath)){
			if(C('TPL_AUTO_CREATE')){
				if(!$this->createTemplate($tplPath)){
					error('模板文件创建失败！！');
				}
			}else{
				error('模板文件不存在！！');
			}
		}
		$path=$blooean?array(
				'group'=>$group,
				'control'=>$control,
				'action'=>$action,
		):'';
		$compile=new Compile($tplPath,$this->vars,$path);
	}
	
	/**
	 * 分页方法
	 * @param model $model
	 * @param number $size
	 * @param number $type，1为普通分页,2为Ajax分页
	 */
	protected function page($model=null,$size=0,$type=1){
		
	}
	
	/**
	 * 操作成功跳转
	 * @param string $sdata
	 */
	protected function success($url,$explain=''){
		$explain=empty($explain)?'操作成功！！':$explain;
		$time=C('DEFAULT_JUMP_TIME');
		if(is_file(C('TPL_ACTION_SUCCESS'))){
			include C('TPL_ACTION_SUCCESS');
		}else{
			exit('成功方法模板不存在！！');
		}
		exit();
	}
	
	
	/**
	 * 操作失败跳转
	 * @param string $data
	 */
	protected function error($explain='',$url=''){
		$explain=empty($explain)?'操作操失败！！':$explain;
		$url=empty($url)?Tool::getPrevUrl():$url;
		$time=C('DEFAULT_JUMP_TIME');
		if(is_file(C('TPL_ACTION_ERROR'))){
			include C('TPL_ACTION_ERROR');
		}else{
			exit('错误方法模板不存在！！');
		}
		exit();
	}
	
	private function createTemplate($tplName){
		$template=file_get_contents(C('TPL_TEMPLATE_FILE'));
// 		echo $template;die;
		is_dir(dirname($tplName))||mkdir(dirname($tplName),0777,true);
		return file_put_contents($tplName, $template);
	}
	
	private function upload(){
		include START_EXTEND_PATH.'/Org/Uploadify/uploadify.php';
		exit();
	}
}
?>