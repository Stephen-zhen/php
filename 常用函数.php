<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

function getNewDate($time){
	return date('y-m-d H:i',$time);
}
function getImagesByIds($ids,$m=null){
    if ($m=='k'){
        $model=db('KplusImage');
    }else{
        $model = db('PlusImage');
    }
	$ids_arr = json_decode($ids,true);
	if($ids_arr){
		$images = $model->where('id','in',implode(',',$ids_arr))->select();
	}else{
		$images = array();
	}
	return $images;
}
function getCommentTitleByCode($status){
	switch($status){
	case 0 : $title = '未读';break;
	case 1 : $title = '已读';break;
	case 2 : $title = '删除';break;
	default: $title = '未知';
	}
	return $title;
}
function getGradeByCode($status){
	switch($status){
	case 1 : $title = '一星';break;
	case 2 : $title = '二星';break;
	case 3 : $title = '二星';break;
	case 4 : $title = '二星';break;
	case 5 : $title = '二星';break;
	default: $title = '未知';
	}
	return $title;
}

function getStatusTitleByCode($status){
	switch($status){
	case 0 : $title = '未上架';break;
	case 1 : $title = '已上架';break;
	default: $title = '未知';
	}
	return $title;
}

/* 将一个字符串转变成键值对数组
     * @param    : string str 要处理的字符串 $str ='TranAbbr=IPER|AcqSsn=000000073601|MercDtTm=20090615144037';
     * @param    : string sp 键值对分隔符
     * @param    : string kv 键值分隔符
     * @return    : array
    */
    function strToArr ($str, $sp="&", $kv="=")
    {
        $arr = str_replace(array($kv,$sp), array('"=>"','","'),'array("'.$str.'")');
        echo $arr."<br>";
        eval("\$arr"." = $arr;");
        return $arr;
    }


 /*
   短信接口
   $mobile 手机号；
   $content 短信内容，
   $type 短信类型 0 登录短信验证；1面料买手短信通知；2营销短信；
  */
 function smssend($mobile,$param = array(),$type){

	   switch ($type) {
	   	case '0'://登录短信验证
	   		 	   $content='【布联网】尊敬的用户：您的验证码是:666666，工作人员不会索取，请勿泄露。';
	   		break;
        case '1'://提交完成时
        //000001 某某某+编号
                $content='【布联网】尊敬的用户，您成功提交找布信息。买手'.$param['ms'].'正在全力帮您找布。';
            break;
        case '2'://找布完成时
        //000001 $param[5]:某某某+编号
//                $content='【布联网】尊敬的用户，您提交的找布信息'.$param['no'].'，有新进度啦。价格'.$param['jiage'].'成分'.$param['chengfen'].'起订量'.$param['dingliang'].'可以寄色卡。买手'.$param['ms'].'';
           $content='【布联网】尊敬的用户，买手'.$param['ms'].'已经完成了您的找布信息'.$param['no'].'。';
            break;
        case '3'://发送给客户
        //$param[1]:某某某+编号 000001
                $content='【布联网】尊敬的用户，买手'.$param['ms'].'已经完成了您的找布信息'.$param['no'].'。';
            break;
        case '4'://分配给找布人员
        //$param[1]:林先生13456789011
//                $content='【布联网】有新的找布信息，'.$param['no'].'，成分'.$param['chengfen'].'数量'.$param['shuliang'].'，做上衣。';
               $content='【布联网】有新的找布信息，'.$param['content'];
            break;
        case 5:
            // 找布进度
            $content = '【布联网】您的找布服务有新的进度啦，点击查看详情' . $param['msg'] . '。';
            break;
	   	default:
	   		# code...
            $content=$param;
	   		break;
	   }

	   $userid='blw123';//账号
	   $pwd='Blw12345';	//密码
        $url='http://116.62.64.214/msg/HttpBatchSendSM?account='.$userid.'&ts=&pswd='.$pwd.'&mobile='.$mobile.'&msg='.$content.'&needstatus=false';

		 	// 初始化一个 cURL 对象
		$curl = curl_init();
		// 设置你需要抓取的URL
		curl_setopt($curl, CURLOPT_URL,$url);
		// 设置header 响应头是否输出
		curl_setopt($curl, CURLOPT_HEADER, 0);
		// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
		// 1如果成功只将结果返回，不自动输出任何内容。如果失败返回FALSE
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		// 运行cURL，请求网页
		$data = curl_exec($curl);
		// 关闭URL请求
		curl_close($curl);
		// 显示获得的数据
		if ($data==FALSE) {
			return array('code'=>0,'msg'=>'短信发送失败');
		}else{
			return array('code'=>1,'msg'=>'短信发送成功');
		}

 }


 /**
 * 系统邮件发送函数
 * @param string $tomail 接收邮件者邮箱
 * @param string $name 接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body 邮件内容
 * @param string $attachment 附件列表
 * @return boolean
 * @author static7 <static7@qq.com>
 */
function send_mail($tomail, $name, $subject = '', $body = '', $attachment = null) {
    vendor('PHPMailer.class#phpmailer');
    $mail = new \PHPMailer();           //实例化PHPMailer对象
    $mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();                    // 设定使用SMTP服务
    $mail->SMTPDebug = 0;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
    $mail->SMTPAuth = true;             // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'ssl';          // 使用安全协议
    $mail->Host = "smtp.exmail.qq.com"; // SMTP 服务器
    $mail->Port = 465;                  // SMTP服务器的端口号
    $mail->Username = "zixun@b2l.com";    // SMTP服务器用户名
    $mail->Password = "BYFlcx669555";     // SMTP服务器密码
    $mail->SetFrom('zixun@b2l.com', '布联网');
    $replyEmail = '';                   //留空则为发件人EMAIL
    $replyName = '';                    //回复名称（留空则为发件人名称）
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($tomail, $name);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}

/*
   分配面料买手找布负责人
   根据进行中的任务次数，分配给任务次数最少的人
 */
function randPlusStaff($role=null){
    $db = db('admin');
    $buyuser_id = $db ->alias('a')
                      ->field('a.id')
                        // ->join('plus p',' p.buyuser_id=a.id','left')
                      ->where(array('a.role'=>$role,'del'=>1,'status'=>1))
                      ->select();
    if($role == 6666){
        $plus = db('plus')->field('buyuser_id id')
                          ->where(array('buyuser_id'=>['neq','null'],'status'=>1))
                          ->select();
    }elseif($role == 6669){
        $plus = db('kplus')->field('buyuser_id id')
                           ->where(array('buyuser_id'=>['neq','null'],'status'=>1))
                           ->select();
    }
    $str = array();
    foreach ($plus as $key => $value) {
        $str[$key] = $value["id"];
    }
    $num = '';//进行中的任务次数
    $id  = '';//买手id
//          $buyuser_id=shuffle($buyuser_id);
    foreach ($buyuser_id as $key => $value) {
        if (in_array($value['id'],$str) == false) {
            return $value['id'].'no'; //  在PLUS 中没有过任务
        }else {
            $arr = array_count_values($str);
            $ls  = $arr[$value['id']];
//                 $ls = substr_count($str, $value['id']);//判断此人在进行中的任务有几个
            if ($num == '') {
                $num = $ls;
                $id  = $value['id'];
            } else if ($ls < $num) {
                $num = $ls;
                $id  = $value['id'];
            }
        }
    }
    return  $id;//返回面料买手人员id   此人进行中任务最少
}
function getStatusTitle ($status){
	switch($status) {
	case 0: $label = '未完成'; break;
	case 1: $label = '进行中'; break;
	case 2: $label = '已完成'; break;
	default:$label = '-';
	}
	return $label;
}


/**
 * 邮箱 隐藏中间四位
 * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
 * @param string $user_name 姓名
 * @return string 格式化后的姓名
 */
function substr_email($str){
    $email_array = explode("@", $str);
      $prevfix = (strlen($email_array[0]) < 4) ? "" : substr($str, 0, 3); //邮箱前缀
      $count = 0;
      $str = preg_replace('/([\d\w+_-]{0,100})@/', '****@', $str, -1, $count);
      $rs = $prevfix . $str;
    return $rs;
}

/**
 *  返回json信息
 *  @param $code 返回码
 *  @param $msg  返回信息
 *  @param $data 返回数据
 */
function retJson($code = 200, $msg = '', $data = ''){
    return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
}

// 微信模板消息 curl模拟请求发送信息
function send_template_message($data,$access_token){
   //return $data.'----'.$access_token;
   //$access_token = get_access_token($appId,$appSecret);
   $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_POST, 1);
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Content-Length: ' . strlen($data)));
   ob_start();
   curl_exec($ch);
   $return_content = ob_get_contents();
   ob_end_clean();
   $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
   return array($return_code, $return_content);
}


//获取微信access_token
function get_accessToken($appid='',$appsecret=''){
   $tokenFile = "./access_token.txt"; // 缓存文件名
   $data = json_decode(file_get_contents($tokenFile)); //转换为json格式
   if ($data->expire_time < time() or ! $data->expire_time) {
       //token过期的情况
       $res = file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret);
       $res = json_decode($res, true); // 对 JSON 格式的字符串进行编码
       $access_token = $res['access_token'];
       if ($access_token) {

           $data['expire_time'] = time() + 3600; //保存1小时

           $data['access_token'] = $access_token;

           $fp = fopen($tokenFile, "w"); //只写文件

           fwrite($fp, json_encode($data)); //写入json格式文件

           fclose($fp); //关闭连接
       }
   } else {
       $access_token = $data->access_token;
   }
   return $access_token;
}

//获取微信access_token
function getaccessToken($appid='',$appsecret=''){
    $access_token = cache('access_token');
    //token过期的情况
    if (!$access_token){
        $res = file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret);
        $res = json_decode($res, true); // 对 JSON 格式的字符串进行编码
        $access_token = $res['access_token'];
        cache('access_token',$access_token,7000);
    }

   return $access_token;
}
