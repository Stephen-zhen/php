<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose 
 */
use \GatewayWorker\Lib\Gateway;

class Events
{

	/**
	 * 有消息时
	 * @param int $client_id
	 * @param mixed $message
	 */
	public static function onMessage($client_id, $message)
	{
		// debug
		//echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id session:".json_encode($_SESSION)." onMessage:".$message."\n";

		// 客户端传递的是json数据
		$message_data = json_decode($message, true);
		if(!$message_data)
		{
			return ;
		}

		// 根据类型执行不同的业务
		switch($message_data['type'])
		{
			// 客户端回应服务端的心跳
		case 'pong':
			return;
			// 客户端登录 message格式: {type:login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室
		case 'login':
			// 判断是否有房间号
			if(!isset($message_data['client_name']))
			{
				throw new \Exception("\$message_data['client_name'] not set. client_ip:{$_SERVER['REMOTE_ADDR']} \$message:$message");
			}

			// 把房间号昵称放到session中
			$room_id = $message_data['client_name'];
			$_SESSION['room_id'] = $room_id;


			$new_message = array('type'=>$message_data['type'], 'client_id'=>$client_id, 'time'=>date('Y-m-d H:i:s'));
			Gateway::joinGroup($client_id, $room_id);

			Gateway::sendToCurrentClient(json_encode($new_message));
			return;

			// 客户端发言 message: {type:say, to_client_id:xx, content:xx}
		case 'img':
			// 非法请求
			if(!isset($_SESSION['room_id']))
			{
				throw new \Exception("\$_SESSION['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']}");
			}
			$room_id = $_SESSION['room_id'];
			$image = isset($message_data['img'])?$message_data['img']:false;
			$img_res = self::getSearchRes($image);
			// 查找图片 
			$new_message = array(
				'type'=>'img',
				'content'=>json_decode($img_res['list'],true),
				'imgurl'=>$img_res['imgurl']
			);
			
			return Gateway::sendToGroup($room_id ,json_encode($new_message));

		}
	}

	public static function getSearchRes($img){
		//匹配出图片的格式
		if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $img, $result)){
			$type = $result[2];
			$new_file = "../../public/upload/productAiFindImg/";
			if (!is_dir($new_file)) mkdir($new_file);
			$file_name = time().".{$type}";
			$new_file = $new_file.$file_name;
			if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $img)))){
				$new_file=substr($new_file, 1);
			}else{
				return array();
			}
		}
		$ur='http://xxx.com/'.$file_name;
		$url="https://xxx.com".$ur;

		$curl=curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		//执行命令
		$list = curl_exec($curl); 
		//关闭URL请求
		curl_close($curl);
		$data['list']=$list;
		$data['imgurl']=$ur;
		if($data){
			//显示获得的数据
			return $data;
		}else{
			return array();
		}
	}


	/**
	 * 当客户端断开连接时
	 * @param integer $client_id 客户端id
	 */
	public static function onClose($client_id)
	{
		// debug
		//echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";

		// 从房间的客户端列表中删除
		if(isset($_SESSION['room_id']))
		{
			//$room_id = $_SESSION['room_id'];
			//$new_message = array('type'=>'logout', 'from_client_id'=>$client_id, 'from_client_name'=>$_SESSION['client_name'], 'time'=>date('Y-m-d H:i:s'));
			//Gateway::sendToGroup($room_id, json_encode($new_message));
		}
	}

}
