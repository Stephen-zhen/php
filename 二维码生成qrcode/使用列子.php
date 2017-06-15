<?php
namespace app\index\controller;
use think\Validate;
use qrcode\QRCode;
header('Content-Type: text/html; charset=utf-8');	//网页编码

class Productai extends Common{

//二维码生成
public function getQrcode($client_id=false) {
		if($client_id){
			$qrcode = new QRCode();
			$host = $_SERVER['HTTP_HOST'];
			$data = 'http://'.$host.'/mobile/Productai/videoPhone/client_id/'.$client_id.'.html';
			// 纠错级别：L、M、Q、H
			$level = 'L';
			// 点的大小：1到10,用于手机端4就可以了
			$size = 4;
			// 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
			$path = "./upload/qrcode/";
			// 生成的文件名
			$fileName = $path.$client_id.'.png';
			$src = '/upload/qrcode/'.$client_id.'.png';
			if(!is_file($fileName)){
				$qrcode::png($data, $fileName, $level, $size);
			}
			return $src;
		}
	}
}