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
 * 图片处理类
 * @author 舒良府
 *
 */
class Image {
	private $srcPath;				//原图像地址
	private $srcWidth;				//原图宽
	private $srcHeight;				//原图高
	private $srcType;				//原图类型
	private $srcImg;					//原图资源句柄
	private $thumbWidth;			//缩略图宽
	private $thumbHeight;			//缩略图高
	private $thumbImg;			//新图资源句柄
	private $maxWidth;				//上传图片最大宽度
        
     /**
      * 初始化
      * @param  $srcPath 
      * @param  boolean $boole 用于上传图片执行此类，自动裁切宽度超过配置文件的最大值
      */
	public function __construct($srcPath,$boole=false){
		extension_loaded('GD')||exit('GD图像处理库没有开启！！');
		$this->srcPath=$srcPath;
		$this->maxWidth=C('UP_PIC_MAX_WIDTH');
		list($this->srcWidth,$this->srcHeight,$srcType)=getimagesize($this->srcPath);
		$this->srcType=ltrim(image_type_to_extension($srcType),'.');
		$imgCreateFn='imagecreatefrom'.$this->srcType;
		$this->srcImg=$imgCreateFn($this->srcPath);//获取原图资源句柄
		if ($boole){
			$this->thumb();
		}
	}
	
	/**
	 * 自动缩略图，$width&&$height不为空时，按固定大小居中裁切，$width为空时，则固定高宽自动，$height为空时，宽固定高自动，
	 * 都为空时，则按原图大小，超出配置文件最大宽度时自动裁切
	 * @param int $width 		缩略图宽
	 * @param int $height 	缩略图高
	 * @param boolean $boole 布尔值，默认$boole为false，覆盖原文件不返回文件名，
	 * $boole为true时，将原来的文件名加上缩略图的宽度和高度组成新文件名(原文件名：abc.jpg,例如按照200*250生成缩略图，那么新文件名：abc_200_250.jpg)，不覆盖原文件，返回路径
	 */
	public function thumb($width='',$height='',$boole=false){
		if(empty($width)&&empty($height)){							//刚取原图大小，裁切图片宽度超过最大限定值的
			$this->thumbWidth=$this->srcWidth;
			if($this->thumbWidth>$this->maxWidth){
				$this->thumbWidth=$this->maxWidth;
			}
			$this->thumbHeight=$this->srcHeight;
			$this->fixWidthHeight();
		}else{
			if(!empty($width)&&!empty($height)){						//固定宽高，超出部分自动裁切
				$this->thumbWidth=$width;
				$this->thumbHeight=$height;
				$this->fixWidthHeight();
			}elseif(empty($height)&&!empty($width)){				//固定宽，高自动
				$this->thumbWidth=$width;
				$ratioWidth=$this->thumbWidth/$this->srcWidth;	//缩略图原图宽比
				$this->thumbHeight=$ratioWidth*$this->srcHeight;
				$this->autoWidthHeight();
			}elseif(empty($width)&&!empty($height)){				//固定高，宽自动
				$this->thumbHeight=$height;
				$ratioHeight=$this->thumbHeight/$this->srcHeight;
				$this->thumbWidth=$ratioHeight*$this->srcWidth;
				$this->autoWidthHeight();
			}
		}	
		return $this->outPut($boole);	
	}
	
	private function fixWidthHeight(){//固定宽高,裁切超出部分
		$this->thumbImg=imagecreatetruecolor($this->thumbWidth, $this->thumbHeight);
		if($this->thumbWidth/$this->thumbHeight<=$this->srcWidth/$this->srcHeight){
			$src_h=$this->srcHeight;
			$src_w=$this->srcHeight/$this->thumbHeight*$this->thumbWidth;
			$src_x=($this->srcWidth-$src_w)/2;
			$src_y=0;
		}else{
			$src_w=$this->srcWidth;
			$src_h=$this->srcWidth/$this->thumbWidth*$this->thumbHeight;
			$src_x=0;
			$src_y=($this->srcHeight-$src_h)/2;
		}
		imagecopyresampled($this->thumbImg, $this->srcImg, 0, 0, $src_x, $src_y, $this->thumbWidth,$this->thumbHeight, $src_w,$src_h);
	}
	
	private function autoWidthHeight(){//自动宽或高
		$this->thumbImg=imagecreatetruecolor($this->thumbWidth, $this->thumbHeight);
		imagecopyresampled($this->thumbImg, $this->srcImg, 0, 0, 0, 0, $this->thumbWidth,$this->thumbHeight,$this->srcWidth, $this->srcHeight);
	}
	
	/**
	 * 
	 * @param  $boole 布尔值 		是否覆盖原文件及返回路径
	 * @return string 	返回文件路径
	 */
	private function outPut($boole){
		$outFn='image'.$this->srcType;
		if($boole){
			$pathInfo=pathinfo($this->srcPath);
			$fileName=$pathInfo['dirname'].'/'.$pathInfo['filename'].'_'.$this->thumbWidth.'_'.$this->thumbHeight.'.'.$pathInfo['extension'];
			$outFn($this->thumbImg,$fileName);//,
			return $fileName;
		}else{
			$outFn($this->thumbImg,$this->srcPath);
		}
		imagedestroy($this->srcImg);
		imagedestroy($this->thumbImg);
	}              
}
?>