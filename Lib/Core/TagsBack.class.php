<?php
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-11-18
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/

/**
 * 自定义标签处理类
 * @author 舒良府
 *
 */
final class Tags{
	private $tplContent;
	private $pattern;
	
	/**
	 * 构造方法 初始化
	 * @param string $tplContent
	 */
	public function __construct($tplContent){
// 		$pattern='html|head|title|body|div|span|object|h1|h2|h3|h4|h5|h5|h7|ul|ol|li|dl|dt|dd|table|tr|th|td|thead|tbody|form|a|select|option|em|b|strong|s|u|d|frame|iframe|frameset|p|i|ilayer|ins|kbd|label|layer|legend|listing|map|marquee|menu|textarea|multicol|nobr|noembed|noframes|noscript|optgroup|param|pre|q|samp|style|script|server|small|strike|sub|sup|tfoot|tt|var|xmp|abbr|acronym|address|applet|bdo|big|blink|blockquote|caption|center|cite|code|comment|del|dfn|dir';
		$this->tplContent=$tplContent;
		
	}
	
	/**
	 * 获取模板自定义标签
	 */
	public  function getTags(){
		$tags=C('TPL_TAGS');
		foreach ($tags as $tag){
			$this->pattern='/<'.$tag.'((?:\s+\w+=[\'\"].+?[\'\"])*)\s?>(.*?)<\/'.$tag.'>/s';
			$tagClassFile=EXTEND_TAG_PATH.'/'.$tag.'Tag.class.php';
			if(is_file($tagClassFile)){// 如果用户自定义标签处理类存在，那么就载入并且实例化
				include $tagClassFile;
				$args=$this->getArgs();
				$tagClass=ucfirst($tag).'Tag';
				$tagObj=new $tagClass();
				$replaceContent=$tagObj->$tag($args['attr'],$args['content']);
				$this->tplContent=preg_replace($this->pattern, $replaceContent, $this->tplContent);
			}elseif(C('TPL_TAGS_AUTO_PARSE')){ // 如果文件不存在，则Start框架自动解析(前提得符合本框架定义参数，如where,table,limit等)
				$this->tplContent=$this->parseTags($tag);
			}
		}
		return $this->tplContent;
	}
	
	/**
	 * 获取用户自定义标签传递的参数
	 * @return $args
	 */
	public function getArgs(){
		if(preg_match_all($this->pattern, $this->tplContent,$arr)){
			$args=expToArr(filterArr(preg_split('/\s+/', $arr[1][0])));
			$content=$arr[2][0];
			$arrArgs=array();
			$arrArgs['attr']=$args;
			$arrArgs['content']=$content;
			return $arrArgs;
		}
	}
	
	/**
	 * 框架自动解析用户自定义标签
	 * 如果要使用Start框架自动解析启用定义标签，
	 * 那么自定义标签所传递的参数必须符合本方法的预定参数
	 * @return $tplContent 返回解析后的模板
	 */
	public function parseTags(){
		if(preg_match_all($this->pattern, $this->tplContent,$arr)){
			// Start框架定义预参数
			$sysTem=array(
					'item',
					'table',
					'count',
					'group',
					'where',
					'order',
					'limit',
			);
			// 获取用户自定义参数
			$args=$this->getArgs();
			$sys=array();
			foreach ($args['attr'] as $k=>$a){
				if(in_array($k, $sysTem)){
					$sys[$k]=$a;
				}
			}
			$item=array_key_exists('item', $sys)?$sys['item']:'item';
			$table=array_key_exists('table', $sys)?$sys['table']:error('必须加在table参数！！');
			$count=array_key_exists('count', $sys)?$sys['count']:'';
			$group=array_key_exists('group', $sys)?$sys['group']:'';
			$where=array_key_exists('where', $sys)?$sys['where']:'';
			$order=array_key_exists('order', $sys)?$sys['order']:'';
			$limit=array_key_exists('limit', $sys)?$sys['limit']:'';
			$c='';
			if(!empty($count)){
				if(is_array($count)){
					$c='count('.$count.')->';
				}elseif(is_string($count)){
					$c='count("'.$count.'")->';
				}
			}
			$g='';
			if(!empty($group)){
				if(is_string($group)){
					$g='group("'.$group.'")->';
				}elseif(is_array($group)){
					$g='group('.$group.')->';
				}
			}
			$w='';
			if(!empty($where)){
				if(is_string($where)){
					$w='where("'.$where.'")->';
				}elseif(is_array($where)||is_numeric($where)){
					$w='where('.$where.')->';
				}
			}
			$o='';
			if(!empty($order)){
				if(is_string($order)){
					$o='order("'.$order.'")->';
				}elseif(is_array($order)){
					$o='order('.$order.')->';
				}
			}
			$l='';
			if(!empty($limit)){
				if(is_array($limit)){
					$l='limit("'.$limit.'")->';
				}elseif(is_array($limit)||is_numeric($limit)){
					$l='limit('.$limit.')->';
				}
			}

			$str='<?php  $db=M("'.$table.'"); $data=$db->'.$c.$g.$w.$o.$l.'all();?>'."\n";
			$str.='<?php if(isset($data)): foreach ($data as $'.$item.'):?>';
			$str.=$args['content'];
			$str.='<?php endForeach;endIf;?>';
			

			$this->tplContent=preg_replace($this->pattern, $str, $this->tplContent);
		}
		return $this->tplContent;
	}
	

}

?>