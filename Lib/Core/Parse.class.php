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
 * 模板标签解析类
 * @author 舒良府
 *
 */
class Parse {
	
	private $tplContent;		// 模板内容
	private $_left;				//左定界符
	private $_right;				//右定界符
	
	public function __construct($tplPath){
		$this->_left=C('TPL_DELIMITER_LEFT');
		$this->_right=C('TPL_DELIMITER_RIGHT');
		$this->tplContent=file_get_contents($tplPath);
	}
	
	public function paseTpl(){
		$this->parseConst();
		$this->getTempFile();
		$tags=new Tags($this->tplContent);
		$this->tplContent=$tags->getTags();
		$this->parseIf();
		$this->parseFor();
		$this->parseForeach();
		$this->parseFunction();
		$this->parseCommon();
		$this->parseAssocArray();
		$this->parseLoad();
		$this->parseLink();
		$this->parseScript();
		$this->parseBootstrap();
		$this->parseJquery();
		$this->ParseUeditor();
		$this->parseUpload();
		return $this->tplContent;
	}
	
	/**
	 * 解析普通变量{$title}对象{$title->name}
	 * 可以进行简单的加减乘除运算
	 */
	private function parseCommon(){
		$pattern='/'.$this->_left.'\s?(\$\w+(?:[\$\-\+*\/\>\[\]\'\"\w]+[\w+]?)?)\s?'.$this->_right.'/';
// 		$pattern='/'.$this->_left.'\s?(\$\w+[^\..]*)\s?'.$this->_right.'/';
		if(preg_match($pattern, $this->tplContent)){
			$this->tplContent=preg_replace($pattern, '<?php if(isset($1)): echo $1;endIf;?>', $this->tplContent);
		}
	}
	
	/**
	 * 解析IF条件判断语句
	 * 可以进行简单的加减乘除运算及大小比较
	 */
	private function parseIf(){
		$patternIf='/(?:'.$this->_left.'|<)if\s+value=(?:\"|\')([\w\$\+\-,\(\)\!*\/\>\<=]+)(?:\'|\")\s?(?:'.$this->_right.'|>)/';
// 		$patternIf='/(?:'.$this->_left.'|<)if\s+value=(.+)\s?(?:'.$this->_right.'|>)/';
		$patternEndif='/(?:'.$this->_left.'|<)\/if(?:'.$this->_right.'|>)/';
		$patternElse='/(?:'.$this->_left.'|<)else(?:'.$this->_right.'|>)/';
		if(preg_match_all($patternIf,$this->tplContent,$arr1)){
			if(preg_match_all($patternEndif,$this->tplContent,$arr2)){
				$this->tplContent=preg_replace($patternIf, '<?php if($1){ ?>', $this->tplContent);
				$this->tplContent=preg_replace($patternEndif, '<?php } ?>', $this->tplContent);
				if(preg_match($patternElse, $this->tplContent)){
					$this->tplContent=preg_replace($patternElse, '<?php }else{ ?>', $this->tplContent);
				}
			}else{
				$this->error('IF判断语句没有关闭！！');
			}
			if(count($arr1[0])!=count($arr2[0]))$this->error('IF判断语句没有关闭！！');
		}
		
	}
	
	/**
	 * 解析FOR循环语句
	 * 可以嵌套使用
	 * 使用格式{for start=$num1 end=$num2 item=i step=1}{/for}
	 */
	private function parseFor(){
		$patternFor='/'.$this->_left.'for\s+start=(\$?\w+)\s+end=(\$?\w+)\sitem=([a-zA-Z]+)\s+step=([1-9]+)'.$this->_right.'/';
		$patternEndfor='/'.$this->_left.'\/for'.$this->_right.'/';
		if(preg_match_all($patternFor, $this->tplContent,$arr1)){
			if(preg_match_all($patternEndfor, $this->tplContent,$arr2)){
				$this->tplContent=preg_replace($patternFor, '<?php for(\$$3=$1;\$$3<$2;\$$3++):?>', $this->tplContent);
				$this->tplContent=preg_replace($patternEndfor, '<?php endFor;?>', $this->tplContent);
			}else{
				$this->error('啊哦………………FOR循环语句没有关闭！！');
			}
			if(count($arr1[0])!=count($arr2[0]))$this->error('IF判断语句没有关闭！！');
		}
		
	}
	
	/**
	 * 解析Foreach循环语句
	 * 可以嵌套使用
	 * 使用格式:{foreach from=arr item=item key=key}{/foreach}
	 */
	private function parseForeach(){
		$patternForeach='/(?:'.$this->_left.'|<)foreach\s+from=(?:\'|\")(\$?\w+)(?:\'|\")\s+item=(?:\'|\")(\$?\w+)(?:\'|\")(?:\s+key=(?:\'|\")(\$?\w+)(?:\'|\"))?(?:'.$this->_right.'|>)/';
		$patternEndforeach='/(?:'.$this->_left.'|<)\/foreach\s?(?:'.$this->_right.'|>)/';
		$patternElseForeach='/(?:'.$this->_left.'|<)elseforeach(?:'.$this->_right.'|>)/';
		if(preg_match_all($patternForeach, $this->tplContent,$arr1)){
			if(preg_match_all($patternEndforeach, $this->tplContent,$arr2)){
				$this->tplContent=preg_replace_callback($patternForeach,'replaceForeach', $this->tplContent);
				$this->tplContent=preg_replace($patternEndforeach,'<?php endForeach;endIf?>', $this->tplContent);
				if(preg_match($patternElseForeach,$this->tplContent)){
						
				}
			}else{
				$this->error('Foreach循环语句没有关闭！！');
			}
			if(count($arr1[0])!=count($arr2[0]))$this->error('Foreach循环语句没有关闭！！');
		}
	}
	
	/**
	 * 解析框架及系统魔法常量
	 */
	private function parseConst(){
		$pattern='/(\_\_(?:[A-Z_?]+\_\_)+|(?:[A-Z]+(\_[A-Z]+)+))/';
		if(preg_match($pattern, $this->tplContent)){
			$this->tplContent=preg_replace($pattern, '<?php echo $1;?>', $this->tplContent);
		}
	}

	
	/**
	 * 解析系统及用户函数
	 * 使用格式{:p($arr)}注意：必须加上“：”号
	 */
	private function parseFunction(){
		$funcLimit=C('TPL_FUNCTION_LIMIT');//函数解析分割符
		$pattern='/'.$this->_left.'\\'.$funcLimit.'([\w\s]+\(?.*\))'.$this->_right.'/';
// 		echo $pattern;die;
		if(preg_match_all($pattern,$this->tplContent,$arr)){
			$this->tplContent=preg_replace($pattern, '<?php echo $1;?>', $this->tplContent);
		}
	}
	
	/**
	 * 解析START框架全局数组$start及普通关联数组
	 * @return string
	 */
	private function parseAssocArray(){
		$pattern='/'.$this->_left.'\s?(\$\w+)((?:\.\w+)+)\s?'.$this->_right.'/';
		if(preg_match($pattern, $this->tplContent)){
			$this->tplContent = preg_replace_callback($pattern, 'replaceArray', $this->tplContent);
		}
	}
	
	/**
	 * 解析便捷包含文件(include)
	 * 使用格式<load file=('|")__PUBLIC__/Css/style.css('|")>
	 * 定界符还可以为配置项定义的{}
	 */
	private function parseLoad(){
		$patternInclude='/(?:'.$this->_left.'|<)load\s+file=[\'\"]([\w+\/\.\:?]+)[\'\"](?:\s+\/)?(?:'.$this->_right.'|>)/';
		if(preg_match_all($patternInclude, $this->tplContent,$arr)){
			foreach ($arr[1] as $includeFile){
				if(!is_file($includeFile)){
					$this->error($includeFile.'包含文件不存在！！');
				}else{
					$parse=new Parse($includeFile);
					file_put_contents($includeFile, $parse->paseTpl());
				}
			}
			$this->tplContent=preg_replace($patternInclude, '<?php include "$1"?>', $this->tplContent);
		}
	}
	
	/**
	 * 解析便捷导入CSS
	 * 使用格式<css file=__PUBLIC__/Css/style.css>
	 * 定界符还可以为配置项定义的{}
	 */
	private function parseLink(){
		$patternLink='/(?:'.$this->_left.'|<)css\s+file=[\'\"]?([\w+\/\.\:?]+)[\'\"]?(?:\s+\/)?(?:'.$this->_right.'|>)/';
		if(preg_match($patternLink, $this->tplContent)){
			$this->tplContent=preg_replace($patternLink, '<link rel="stylesheet" type="text/css" href="$1"/>', $this->tplContent);
		}
	}
	
	/**
	 * 解析便捷导入JS
	 * 使用格式<js file=__PUBLIC__/Js/index.js>
	 * 定界符还可以为配置项定义的{}
	 */
	private function parseScript(){
		$patternLink='/(?:'.$this->_left.'|<)js\s+file=(?:\'|\")?(.*?)(?:\'|\")?(?:\s+\/)?(?:'.$this->_right.'|>)/';
		if(preg_match($patternLink, $this->tplContent)){
			$this->tplContent=preg_replace($patternLink, '<script type="text/javascript" src="$1"></script>', $this->tplContent);
		}
	}
	
	/**
	 * 解析简捷调用bootstrap
	 * version为版本号，可选，2和3两个版本，默认为2
	 * 调用方式 <bootstrap version="2">
	 * 定界符还可以为配置项定义的{}
	 */
	private function parseBootstrap(){
		$pattern='/(?:'.$this->_left.'|<)bootstrap(?:\s+version=(?:\"|\')([\d\.?]+)(?:\"|\'))?(?:\s?\/)?(?:'.$this->_right.'|>)/';
		if(preg_match_all($pattern, $this->tplContent,$arr)){
			$version=!empty($arr[1][0])?$arr[1][0]:'2';
			$bootatrap=START_TPL_PATH.'/Bootstrap'.$version.'.txt';
			$bootatrapContent=file_get_contents($bootatrap);
			$this->tplContent=preg_replace($pattern, $bootatrapContent, $this->tplContent);
		}
	}
	
	/**
	 * 解析简捷导航jquery
	 * version为版本号，该参数为可选,1.7,1.8,10,三个版本
	 * 调用方式 <jquery version="1.8">
	 * 定界符还可以为配置项定义的{}
	 */
	private function parseJquery(){
		$pattern='/(?:'.$this->_left.'|<)jquery(?:\s+version=(?:\"|\')([\d\.?]+)(?:\"|\'))?(?:\s?\/)?(?:'.$this->_right.'|>)/';
		if(preg_match_all($pattern, $this->tplContent,$arr)){
			$version=!empty($arr[1][0])?$arr[1][0]:'1.8';
			$jquery=__START_EXTEND__.'/Org/Jquery/jquery-'.$version.'.2.min.js';
			$this->tplContent=preg_replace($pattern, '<script type="text/javascript" src="'.$jquery.'"></script>', $this->tplContent);
		}
	}
	
	/**
	 * 解析简捷调用Ueditor百度编辑器
	 * width,height 两个参数，可选
	 * 定界符还可以为配置项定义的{}
	 * @access private
	 * @param null
	 * @return null
	 */
	private function ParseUeditor(){
		$pattern='/(?:'.$this->_left.'|<)ueditor(\s+.+?)(?:'.$this->_right.'|>)/';
		if(preg_match_all($pattern, $this->tplContent,$arr)){
			$args=$arr[1][0];
			$width=preg_match_all('/width=(?:\"|\')([\w%?]+)(?:\"|\')/', $args,$name1)?$name1[1][0]:'98%';
			$height=preg_match_all('/height=(?:\"|\')([\w%?]+)(?:\"|\')/', $args,$name1)?$name1[1][0]:300;
			$content=preg_match_all('/value=(?:\"|\')(.*?)(?:\"|\')/s', $args,$name1)?$name1[1][0]:'';
			// 编辑器调用路径
			$ueditor=START_TPL_PATH.'/Ueditor.txt';
			// 读取调用文件的内容
			$udContent=file_get_contents($ueditor);
			// 替换编辑模板的参数
			$udContent=preg_replace('/width\:(\w+%?)/', 'width:'.$width, $udContent);
			$udContent=preg_replace('/height\:(\w+%?)/', 'height:'.$height, $udContent);
			$udContent=preg_replace('/><\/textarea>/', '>'.$content.'</textarea>', $udContent);
			$this->tplContent=preg_replace($pattern, $udContent, $this->tplContent);
		}
	}
	
	private function parseUpload(){
		$pattern='/(?:'.$this->_left.'|<)upload(\s+(?:[\s?\w]+\=(?:\"|\')(?:[\w,]+)?(?:\"|\'))+)?(?:\s?\/)?(?:'.$this->_right.'|>)/';
		if(preg_match_all($pattern, $this->tplContent,$arr)){
			$args=$arr[1][0];
			// 获取参数
			$name=preg_match_all('/name=(?:\"|\')(\w+)(?:\"|\')/', $args,$name1)?$name1[1][0]:'file';
// 			$type=preg_match_all('/type=(?:\"|\')([\w,]+)(?:\"|\')/', $args,$name1)?$name1[1][0]:'jpg,png,gif';
			$size=preg_match_all('/size=(?:\"|\')(\d+)(?:\"|\')/', $args,$name1)?$name1[1][0]:200000;
			$limit=preg_match_all('/limit=(?:\"|\')(\d+)(?:\"|\')/', $args,$name1)?$name1[1][0]:10;
			// uploadify插件调用路径
			$uploadFile=__START_TPL__.'/Uploadify.txt';
			// 读取上传插件模板内容
			$upload=file_get_contents($uploadFile);
			$upload=preg_replace('/var\sfileSize=\d+/', 'var fileSize='.$size, $upload);
			$upload=preg_replace('/var\supLimit=\d+/', 'var upLimit='.$limit, $upload);
			$upload=preg_replace('/var\sinputName=[\w\']+/', 'var inputName="'.$name.'"', $upload);
			$this->tplContent=preg_replace($pattern, $upload, $this->tplContent);
		}
	}
	
	
	/**
	 * 解析include包含文件模板标签
	 * 解析后再包含
	 */
	private function getTempFile(){
		ob_start();
		$tempFile='';
		if(isset($_SERVER['PATH_INFO'])&&C('PATH_INFO')){
			$tempFile=TEMP_PATH.'/'.md5($_SERVER['PATH_INFO']).'.php';
		}else{
			$tempFile=@TEMP_PATH.'/'.md5(COMPILE_FILE).'.php';
		}
		is_dir(dirname($tempFile))||mkdir(dirname($tempFile),0777,true);
		if(file_put_contents($tempFile, $this->tplContent));
		@include $tempFile;
		$this->tplContent=ob_get_contents();
		ob_end_clean();
		@unlink($tempFile);
	}
	
	/**
	 * 错误提示方法
	 * @param string $info
	 */
	private  function error($msg){
		error($msg);
	}
	
	public function __destruct(){}
	
}

?>