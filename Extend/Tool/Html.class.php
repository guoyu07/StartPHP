<?php
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-11-19
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/
/**
 * 静态处理类
 * @author 舒良府
 *
 */
final class Html{
	private static $obj=array();		// 要生成静态的对象
	public $error;							// 静态生成失败的错误信息
	public $prikey;						// 主键ID
	public $id;								// 指定生成静态的ID
	public $pathinfo;						// 要操作的控制器和方法
	
	/**
	 * 初始化构造方法
	 * @param string $table
	 * @param number $id
	 * @param string $pathinfo
	 * @param string $limit
	 * @param string $prikey
	 */
	public function __construct($pathinfo){
		$this->pathinfo=$pathinfo;
	}
	
	/**
	 * 生成列表页HTML
	 * @param array $data 				要生成静态的分类数据
	 * @param string $prikeyfield 		分类的主键ID字段
	 * @param string $htmlfiekd 		判断是否生成静态的字段名
	 * @param string $dirfield 			静态目录字段字
	 * @param string $limit 				分类显示条数
	 */
	public function listHtml($data,$prikeyfield,$htmlfield,$dirfield){
		if(!is_array($data)||!filterArr($data))return ;
		foreach ($data as $v){
			$v=(array)$v;
			$_GET[$prikeyfield]=$v[$prikeyfield];
			$filename=getAddress($v[$htmlfield], $v[$dirfield], $v[$prikeyfield],$prikeyfield,2,true);
			$static=C('APP_STATIC_DIR')==''?'static':C('APP_STATIC_DIR');
			page::$staticUrl=__ROOT__.'/'.$static.'/'.$v[$dirfield].'/';
			$this->createHtml($filename);
			for ($i=1;$i<=Page::$staticTotalPage;$i++){
				$static=C('APP_STATIC_DIR')==''?'static':C('APP_STATIC_DIR');
				page::$staticUrl=__ROOT__.'/'.$static.'/'.$v[$dirfield].'/';
				$filename=getAddress($v[$htmlfield], $v[$dirfield],$i,$prikeyfield,2);
				$_GET[C('VAR_PAGE')]=$i;
				$this->createHtml($filename);
			}
			page::$staticUrl=0;
		}
		return true;
	}
	
	/**
	 * 
	 * @access public 
	 * @param array $data 				要生成静态文件的数据()
	 * @param string $prikeyfield 		详情表主键ID名称
	 * @param string $htmlfield 		判断是否生成静态的字段名称
	 * @param string $dirfield 			保存静态文件目录字段
	 * @param string $timefield 		详情内容加添时间字段
	 * @return null 
	 */
	public function detailsHtml($data,$prikeyfield,$htmlfield,$dirfield,$timefield){
		foreach ($data as $v){
			$v=(array)$v;
			if($v[$htmlfield]==1){
				$_GET[$prikeyfield]=$v[$prikeyfield];
				//获取保存路径
				$filename=getAddress($v[$htmlfield], $v[$dirfield], $v[$prikeyfield],$prikeyfield,2,false,'details',$v[$timefield]);
				$this->createHtml($filename);
			}
		}
		return true;
	}
	
	/**
	 * 创建静态文件
	 * @param string $filename
	 * @return boolean
	 */
	private function createHtml($filename){
		ob_start();
		O($this->pathinfo);
		$data=ob_get_contents();
		ob_clean();
		is_dir(dirname($filename))||Dir::create(dirname($filename));
		if(!is_writable(dirname($filename)))error('目录没有写入权限！！');
		if(file_put_contents($filename, $data)){
			return true;
		}else{
			$this->error=$filename.'静态文件生成失败！！';
		}
	}
	
	
}

?>