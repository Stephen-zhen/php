<?php
namespace app\index\controller;
use think\Validate;
use qrcode\QRCode;
header('Content-Type: text/html; charset=utf-8');	//��ҳ����

class Productai extends Common{

//��ά������
public function getQrcode($client_id=false) {
		if($client_id){
			$qrcode = new QRCode();
			$host = $_SERVER['HTTP_HOST'];
			$data = 'http://'.$host.'/mobile/Productai/videoPhone/client_id/'.$client_id.'.html';
			// ������L��M��Q��H
			$level = 'L';
			// ��Ĵ�С��1��10,�����ֻ���4�Ϳ�����
			$size = 4;
			// ����ע���˰Ѷ�ά��ͼƬ���浽���صĴ���,���Ҫ����ͼƬ,��$fileName�滻�ڶ�������false
			$path = "./upload/qrcode/";
			// ���ɵ��ļ���
			$fileName = $path.$client_id.'.png';
			$src = '/upload/qrcode/'.$client_id.'.png';
			if(!is_file($fileName)){
				$qrcode::png($data, $fileName, $level, $size);
			}
			return $src;
		}
	}
}