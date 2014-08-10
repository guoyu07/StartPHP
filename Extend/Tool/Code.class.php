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
 * 验证码类
 * @author Administrator
 *
 */
class Code{
	private $imgWidth;				//验证码图片宽
	private $imgHeight;			//验证码图片高
	private $font;						//字体
	private $fontSize;				//字体大小
	private $charset;				//验证码字符
	private $charsetLength;		//验证码长度 
	private $style;					//验证码样式
	private $type;					//验证码类型
	private $codeBg;					//验证码背景图片	
	private $codeImg;				//图像资源句本
	private $code;					//验证码
	private $circleNum;				//圆图形最大数量
	private $rectangleNum;		//矩形最大数量
	private $lineNum;				//线条最大数量
	private $snowNum;				//雪花最大数量
	private $pixNum;				//点的最大数量
	
	/**
	 * 验证码初始化
	 * @param string $width 		验证码长度
	 * @param string $height 	验证码高度
	 * @param string $codeBg 	验证码背景色
	 * @param array $style 		图形数组配置，array(圆圈数，矩形数，线条数，雪花数，点数)
	 */
	public function __construct($codeSize=array(),$style=array()){
		extension_loaded('GD')||exit('GD图形处理库没有开启！！');
		$this->imgWidth=isset($codeSize[0])&&!empty($codeSize[0])?$codeSize[0]:100;
		$this->imgHeight=isset($codeSize[1])&&!empty($codeSize[1])?$codeSize[1]:35;
		$this->font=C('CODE_FONT');
		$this->fontSize=$this->imgHeight*0.7;
		$this->codeBg=$this->getRgb(C('CODE_BGCOLOR'));
		$this->charsetLength=C('CODE_LENGTH');
		$this->circleNum=isset($style[0])&&!empty($style[0])?$style[0]:10;
		$this->rectangleNum=isset($style[1])&&!empty($style[1])?$style[1]:10;
		$this->lineNum=isset($style[2])&&!empty($style[2])?$style[2]:20;
		$this->snowNum=isset($style[3])&&!empty($style[3])?$style[3]:50;
		$this->pixNum=isset($style[4])&&!empty($style[4])?$style[4]:200;
		
	}
	
	private function createImg(){
		$this->codeImg=imagecreatetruecolor($this->imgWidth, $this->imgHeight);//创建图片资源句柄
		$color=imagecolorallocate($this->codeImg, $this->codeBg['r'], $this->codeBg['g'], $this->codeBg['b']);
		imagefill($this->codeImg, 0, 0, $color);
	}
	
	
	/**
	 * 获取随机颜色
	 */
	private function getColor($min=0,$max=255,$alpha=true){
		if($alpha){
			$color=imagecolorallocatealpha($this->codeImg, mt_rand($min, $max), mt_rand($min, $max), mt_rand($min, $max),mt_rand(30, 100));
		}else{
			$color=imagecolorallocate($this->codeImg, mt_rand($min, $max), mt_rand($min, $max), mt_rand($min, $max));
		}
		return $color;
	}
	
	/**
	 * 随机大小位置生成圆
	 */
	private function createCircle(){
		for($i=0;$i<$this->circleNum;$i++){
			$cx=mt_rand(0, $this->imgWidth);
			$cy=mt_rand(0, $this->imgHeight);
			$width=mt_rand(0, $this->imgHeight);
			$height=mt_rand(0, $this->imgHeight);
			$color=$this->getColor(100,255);
			imagefilledellipse($this->codeImg, $cx, $cy, $width, $height, $color);
		}
	}
	
	/**
	 * 随机位置长短生成线条
	 */
	private function createLine(){
		for($i=0;$i<$this->lineNum;$i++){
			$x1=mt_rand(0, $this->imgWidth);
			$x2=mt_rand(0, $this->imgWidth);
			$y1=mt_rand(0, $this->imgHeight);
			$y2=mt_rand(0, $this->imgHeight);
			imageline($this->codeImg, $x1, $y1, $x2, $y2, $this->getColor(100,255));
		}
	}
	
	/**
	 * 随机大小位置生成矩形
	 */
	private function createRectangle(){
		for($i=0;$i<$this->rectangleNum;$i++){
			$x1=mt_rand(0, $this->imgWidth);
			$x2=mt_rand(0, $this->imgWidth);
			$y1=mt_rand(0, $this->imgHeight);
			$y2=mt_rand(0, $this->imgHeight);
			imagerectangle($this->codeImg, $x1, $y1, $x2, $y2, $this->getColor(0,200));
		}
	}
	
	/**
	 * 随机位置生成雪花
	 */
	private function createSnow(){
		for ($i=0;$i<$this->snowNum;$i++){
			$x=mt_rand(0, $this->imgWidth);
			$y=mt_rand(0, $this->imgHeight);
			imagestring($this->codeImg, mt_rand(1, 5), $x, $y, '*',$this->getColor(100,255));
		}
	}
	
	private function createPix(){
		for ($i=0;$i<$this->pixNum;$i++){
			$x=mt_rand(0, $this->imgWidth);
			$y=mt_rand(0, $this->imgHeight);
			imagesetpixel($this->codeImg, $x, $y, $this->getColor(0,255));
		}
	}
	
	/**
	 * 居中生成文字
	 */
	private function createText(){
		$this->getRandom();
		$txtSize=imagettfbbox($this->fontSize, 0, $this->font, $this->code);
		$codeW=$txtSize[2];
		$codeH=abs($txtSize[5]);
		if ($this->style==1) {
			list($this->imgWidth,$this->imgHeight)=getimagesize($this->codeBg);
		}
		$x=($this->imgWidth-$codeW)/2;
		$y=($this->imgHeight+$codeH)/2;
		imagettftext($this->codeImg, $this->fontSize,0, $x, $y, $this->getColor(0,255,false), $this->font, $this->code);
		
	}
	
	/**
	 * 创建随机码
	 */
	private function getRandom(){
		for($i=0;$i<$this->charsetLength;$i++){
			$this->code.=substr($this->charset,mt_rand(0,strlen($this->charset)-1),1);
		}
	}


	/**
	 * 居中生成随机算术
	 */
	private function createMath(){
		$operator=array('+','-','×');
		$num1=mt_rand(0, 9);
		$num2=mt_rand(0, 9);
		$oper=$operator[mt_rand(0,count($operator)-1)];
		$text=$num1.$oper.$num2.'=?';
		$txtSize=imagettfbbox($this->fontSize, 0, $this->font, $text);
		$codeW=$txtSize[2];
		$codeH=abs($txtSize[5]);
		$x=($this->imgWidth-$codeW)/2;
		$y=($this->imgHeight+$codeH)/2;
		imagettftext($this->codeImg, $this->fontSize,0, $x, $y, $this->getColor(0,250,false), $this->font,$text);
		switch ($oper){
			case '+':
				$this->code=$num1+$num2;
				break;
			case '-':
				$this->code=$num1-$num2;
				break;
			case '×':
				$this->code=$num1*$num2;
				break;
		}
	}
	
	/**
	 * 验证码类型
	 * @param  $type
	 */
	private function codeType($type){
		switch ($type){
			case 1://随机小写字母
				$this->charset='abcdefghijklmnopqrstuvwxyz';
				$this->createText();
				break;
			case 2://随机大写字母
				$this->charset='ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$this->createText();
				break;
			case 3://随机数字
				$this->charset='1234567890';
				$this->createText();
				break;
			case 4://随机大小写字母及数字(混合模式)
				$this->charset='abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
				$this->createText();
				break;
			case 5://随机算术
				$this->createMath();
				break;
		}
	}
	
	/**
	 * 验证码样式
	 * @param  $style
	 */
	private function codeStyle($style){
		switch ($style){
			case 1://画圆
				$this->createCircle();
				break;
			case 2://画矩形
				$this->createRectangle();
				break;
			case 3://画线
				$this->createLine();
				break;
			case 4://画雪花
				$this->createSnow();
				break;
			case 5://画点
				$this->createPix();
				break;
		}
	}
	
	

	/**
	 * 对外接口
	 * @param  $type 	int 	类型
	 * @param  $style 	array 	样式
	 * @return  string  验证码字符
	 */
	public function showCode($type=1,$style=array(1)){
		$this->createImg();
		$type=(int)$type;
		$style=(array)$style;
		foreach ($style as $value){
			$this->codeStyle($value);
		}
		$this->codeType($type);
		$this->printCode();
		return $this->code;
	}
	
	
	/**
	 * 输出及销毁图像
	 */
	private function printCode(){
		header('Content-type:image/png');		//设置图片文档
		imagepng($this->codeImg);				//输出图片
		imagedestroy($this->codeImg);			//销毁内存
	}
	
	/**
	 * 获取RGB三原色
	 * @param  $color
	 * @return multitype:number
	 */
	private static  function getRgb($color){
		$color=ltrim($color,'#');
		$rgb=array();
		$rgb['r']=hexdec(substr($color,0,2));
		$rgb['g']=hexdec(substr($color,2,2));
		$rgb['b']=hexdec(substr($color,4,2));
		return $rgb;
	}
	
	
}


?>