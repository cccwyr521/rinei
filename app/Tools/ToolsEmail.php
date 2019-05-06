<?php 
namespace App\Tools;
use Mail;
/*
/
*/

	class ToolsEmail
	{
		//发送纯文本信息
		public static function sendEmail($emailData)
		{

			//发送纯文本信息
	        $res = Mail::raw($emailData['content'], function($message) use($emailData){//邮件内容
	            $to = $emailData['email_address'];//邮件的收件人
	            $message->to($to)->subject($emailData['subject']); //邮件的主题
	        });

	        return $res;
		}


		//发送html的信息
		public static function sendHtmlEmail($viewData,$emailData)
		{
			 //发送html的邮件
	       $res = Mail::send($viewData['url'],$viewData['assign'], function($message) use($emailData){//模板地址和值
	            $to = $emailData['email_address'];//邮件的收件人
	            $message->to($to)->subject($emailData['subject']); //邮件的主题
	        });

	       return $res;
		}

		//设置激活码
		public static function createActiveCode($username,$email)
		{
			$rand = rand(100000,999999);

			$key = 'forget_'.$username."_".$email;

			$redis = new \Redis();

			$redis->connect('127.0.0.1',6379);

			$redis->setex($key, 1200 ,$rand);

			return $rand;
		}

	}

 ?>