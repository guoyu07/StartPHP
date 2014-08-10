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
 * 水印处理类，(注：本类生成的水印图片只能替换原来的图片，路径地址不变)
 * @author 舒良府
 *
 */
class Mark {
	private $srcPath;                 //原图像地址
	private $srcWidth;               //原图宽
	private $srcHeight;              //原图高
	private $srcType;                 //原图类型
	private $srcImg;                    //原图资源句柄
	private $_x;					//水印图片X轴位置
	private $_y;					//水印图片Y轴位置



	public function __construct($filename){
		extension_loaded('GD')||exit('GD图像处理库没有开启！！');
		$this->srcPath=$filename;
		list($this->srcWidth,$this->srcHeight,$type)=getimagesize($this->srcPath);
		$this->srcType=ltrim(image_type_to_extension($type),'.');
		$imageCreateFn='imagecreatefrom'.$this->srcType;
		$this->srcImg=$imageCreateFn($this->srcPath);
		$this->createMark();
		$this->outPut();
	}

	/**
	 * 生成水印
	 */
	private function createMark(){
		if (C('MARK_TYPE')==1){
			$markContent=C('MARK_TEXT');
			if (!empty($markContent)){
				$this->createTextMark();
			}else{
				exit('请先设置水印文字！！');
			}
		}elseif (C('MARK_TYPE')==2){
			$markUrl=C('MARK_PIC');
			if (!empty($markUrl)){
				$this->createPicMark();
			}else{
				exit('水印图片不存在，请指定水印图片！！');
			}
		}else{
			exit('没有该类型的水印！！');
		}
	}
	
	/**
	 * 创建文本水印
	 */
	private function createTextMark(){
		$regColor=$this->getRgb(C('MARK_COLOR'));
		$alpha=127-127/100*C('MARK_ALPHA');
		$color=imagecolorallocatealpha($this->srcImg, $regColor['r'],$regColor['g'],$regColor['b'],$alpha);//文字水印颜色
		$fontfile=C('MARK_FONT');
		$fontSize=C('MARK_FONT_SIZE');
		$text=C('MARK_TEXT');
		$angle=C('MARK_ANGLE');
		$textSize=imagettfbbox($fontSize, $angle, $fontfile, $text);
		$markWidth=$textSize[2];
		$markHeight=abs($textSize[5]);
		$this->markPosition($markWidth, $markHeight,true);
		imagettftext($this->srcImg, $fontSize, $angle, $this->_x, $this->_y, $color, $fontfile, $text);
	}
	
	/**
	 * 创建图片水印
	 */
	private function createPicMark(){
		$markPath=C('MARK_PIC');
		$markImg=imagecreatefrompng($markPath);
		list($markWidth,$markHeight)=getimagesize($markPath);
		$this->markPosition($markWidth, $markHeight);
		imagecopy($this->srcImg, $markImg, $this->_x, $this->_y, 0, 0, $markWidth, $markHeight);
		imagedestroy($markImg);
	}


	/**
	 * 获取水印位置
	 * @param  $markWidth 水印宽
	 * @param  $markHeight 水印高
	 * @param string $blooe
	 */
	private function markPosition($markWidth,$markHeight,$blooe=false){
		$skewing=C('MARK_SKEWING');//水印偏移的距离
		switch (C('MARK_POSITION')){
			case 1:
				$this->_x=($this->srcWidth-$markWidth)/2;
				$this->_y=($this->srcHeight-$markHeight)/2;
				break;
			case 2:
				$this->_x=$skewing;
				$this->_y=($this->srcHeight-$markHeight)/2;
				break;
			case 3:
				$this->_x=$this->srcWidth-$markWidth-$skewing;
				$this->_y=($this->srcHeight-$markHeight)/2;
				break;
			case 4:
				$this->_x=($this->srcWidth-$markWidth)/2;
				if($blooe){
					$this->_y=$this->srcHeight-$skewing;
				}else{
					$this->_y=$this->srcHeight-$markHeight-$skewing;
				}
				break;
			case 5:
				$this->_x=($this->srcWidth-$markWidth)/2;
				if ($blooe){
					$this->_y=$markHeight+$skewing;
				}else{
					$this->_y=$skewing;
				}				
				break;
			case 6:
				$this->_x=$skewing;
				if($blooe){
					$this->_y=$this->srcHeight-$skewing;
				}else{
					$this->_y=$this->srcHeight-$markHeight-$skewing;
				}
				break;
			case 7:
				$this->_x=$this->srcWidth-$markWidth-$skewing;
				if($blooe){
					$this->_y=$this->srcHeight-$skewing;
				}else{
					$this->_y=$this->srcHeight-$markHeight-$skewing;
				}
				break;
			case 8:
				$this->_x=$skewing;
				if ($blooe){
					$this->_y=$markHeight+$skewing;
				}else{
					$this->_y=0+$skewing;
				}	
				break;
			case 9:
				$this->_x=$this->srcWidth-$markWidth-$skewing;
				if ($blooe){
					$this->_y=$markHeight+$skewing;
				}else{
					$this->_y=$skewing;
				}	
				break;
		}
	}

	//输出图像
	public function outPut(){
		$printImgFn='image'.$this->srcType;
		$printImgFn($this->srcImg,$this->srcPath);
		imagedestroy($this->srcImg);
	}
	
	
	/**
	 * 获取RGB三原色
	 * @param  $color
	 * @return multitype:number
	 */
	private  function getRgb($color){
		$color=ltrim($color,'#');
		$rgb=array();
		$rgb['r']=hexdec(substr($color,0,2));
		$rgb['g']=hexdec(substr($color,2,2));
		$rgb['b']=hexdec(substr($color,4,2));
		return $rgb;
	}
}

?>