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
 * 上传上传类
 * @author Administrator
 *
 */
class Upimage{
	private $updir=null;				//上传路径
	private $type=array();			//上传图片类型限制
	private $size=array();			//上传文件大小限制
	private $files=array();			//上传图片数组集
	private $error=array();			//上传错误消息集
	/**
	 * 构造方法，初始化数据
	 * @param unknown_type $updir
	 * @param unknown_type $type
	 */
	public function __construct($updir=null,$type=null,$size=null){
		$this->updir=is_null($updir)?C('UP_PIC_DIR'):$updir;
		$this->type=is_null($type)?C('UP_PIC_TYPE'):$type;
		$this->size=is_null($size)?C('UP_PIC_MAX_SIZE'):$size;
	}
	
	
	public function upload(){
		if (!$this->createDir()){
			$this->error('上传目录创建失败！！');
		}
		$this->files=$this->formatFiles();
		if(empty($this->files)){
			Tool::goBack('请选择上传图片！！');
			exit;
		}
		$this->checkUp();
		if (!empty($this->files)){
			$arrPath=$this->moveFile();
		}
		return array($arrPath,$this->error);	
	}
	
	private function createDir(){						//创建上传目录，如果目录存在则不创建
		return is_dir($this->updir)||mkdir($this->updir,0777,1);
	}
	
	private function moveFile(){
		$arrPath=array();
		foreach($this->files as $value){
			$filename=$this->updir.'/'.date('Ymd_His').'_'.mt_rand(10000,99999).'.'.$value['ext'];
			if (move_uploaded_file($value['tmp_name'],$filename)){
				$arrPath[]=$filename;
			}
		}
		return $arrPath;
	}
	
	private function checkUp(){							//上传验证
		$this->typeError();
		$this->sizeError();
		$this->isUpload();
		$this->checkType();
	}
	
	private function typeError(){						//验证错误类型
		foreach ($this->files as $key=>$value){
			if ($value['error']){
				$this->errorType($value['error'], $value['name']);
				unset($this->files[$key]);
			}
		}
	}
	
	private function errorType($num,$filename){				//判断错误类型
		switch ($num){
			case 1:
				$this->error[]['error']=array(
						'error'=>'<span style="color:red;">'.$filename.'</span>图片超过了服务器规定大小！',
						'fileame'=>$filename
					);
				break;
			case 2:
				$this->error[]['error']=array(
						'error'=>'<span style="color:red;">'.$filename.'</span>图片超过了本站规定的大小！',
						'fileame'=>$filename
					);
				break;
			case 3:
				$this->error[]['error']=array(
						'error'=>'<span style="color:red;">'.$filename.'</span>图片上传意外中断！',
						'fileame'=>$filename
					);
				break;
			case 4:
				$this->error[]['error']=array(
						'error'=>'<span style="color:red;">'.$filename.'</span>图片没有被上传！',
						'fileame'=>$filename
					);
				break;
		}
	}
	
	private function sizeError(){							//验证图片大小
		foreach ($this->files as $key=>$value){
			if ($value['size']>$this->size){
				$this->error[]['size']=array(
							'error'=>'<span style="color:red;">'.$value['name'].'</span>上传图片超过了'.round(pow($this->size,-3)).'M',
							'filename'=>$value['name']
						);
				unset($this->files[$key]);
			}
		}
	}
	
	private function isUpload(){							//验证是否是合法上传图片
		foreach($this->files as $key=>$value){
			if (!is_uploaded_file($value['tmp_name'])){
				$this->error[]['isup']=array(
						'error'=>'<span style="color:red;">'.$value['name'].'</span>是非法上传文件！',
						'filename'=>$value['name']
				);
				unset($this->files[$key]);
			}
		}
	}
	
	private function checkType(){						//验证图片类型（后缀）
		foreach($this->files as $key=>$value){
			if (!in_array($value['ext'],$this->type)){
				$this->error[]['type']=array(
						'error'=>'<span style="color:red;">'.$value['name'].'</span>图片格式错误，本站只接受'.implode(',', $this->type).'格式的图片！',
						'filename'=>$value['name']
				);
				unset($this->files[$key]);
			}
		}
	}
	
	
	
	
	private function formatFiles(){
		$file=array();
		foreach($_FILES as $value){					//整合数组
			if (is_array($value['name'])){
				foreach ($value as $key=>$val){
					$k=0;
					foreach ($val as $va){
						$file[$k][$key]=$va;
						$k++;
					}
				}
			}else{
				$file[$k]=$value;
				$k++;
			}
		}
		foreach($file as $key=>$value){				//删除空数组
			if (empty($value['name'])){
				unset($file[$key]);
			}else{
				$pathinfo=pathinfo($value['name']);
				$file[$key]['ext']=$pathinfo['extension'];
			}
		}
		return $file;		
	}
	
	//抛出错误
	private function error($msg){
		$str=<<<str
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<div style="width:600px;height:300px;line-height:300px;margin:100px auto;text-align:center;background:#eee;box-shadow:0 0 5px #000;border:1px solid #ccc;border-radius:10px;">
			<span style="font-size:25px;font-weight:800;color:red;">{$msg}</span>
			<a href="javascript:history.back()">点击返回</a>
		</div>
str;
		echo $str;
	}
	
	
	
	
	
	
	
	
	
}

?>