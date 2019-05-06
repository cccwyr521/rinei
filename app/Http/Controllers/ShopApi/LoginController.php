<?php

namespace App\Http\Controllers\ShopApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tools\ToolsSms;
use App\Model\MemberInfo;
use App\Model\Member;
class LoginController extends Controller
{

    //发送短信验证码的接口
    public function sendSms(Request $request)
    {
    	$phone = $request->input('phone');

    	$return = [
    		'code' => 2000,
    		'msg'  => '短信发送成功'
    	];

    	if (empty($phone)) {

    		$return = [
    			'code' => 4001,
    			'msg'  => '手机号不能为空'
    		];

    		$this->returnJson($return);
    	}

    	//验证手机号格式
    	if (!preg_match('/^1[34578]\d{9}$/', $phone)) {

    		$return  = [
    			'code' => 4002, 
    			'msg'  => '手机号格式不正确'
    		];

    		$this->returnJson($return);
    	}

    	//生成手机号的验证码
    	$code = rand(100000,999999);
    	//存储验证码的key值
    	$key = "REGISTER_".$phone."_CODE";

    	\Log::info('手机号'.$phone.'发送短信验证码成功:' .$code); 
    	//实例化redis
    	$redis = new \Redis(); 
    	//连接redis
    	$redis->connect('127.0.0.1',6379);

    	//当前手机号发送请求的次数
    	$key1 = $phone."NUMS";

    	$nums = $redis->get($key1);

    	if ($nums >=3) {
    		$return = [
    			'code' => 4004,
    			'msg'  => '今日短信发送次数已经上线，请明日再来'
    		];

    		$this->returnJson($return);
    	}

    	//设置redis的缓存
    	$redis->setex($key,1200,$code);

    	//发送短信验证码
    	$res = ToolsSms::sendSms($phone, $code);
    	// dd($res);

    	if (!$res['status']) {

    		$return = [
    			'code' => 4003,
    			'msg'  => $res['msg']
    		];
 	
 		$this->returnJson($return);
    		
    	}
    	//给redis存储的次数自增一次
    	$redis->incr($key1);

    	$redis->expire($key1,24*3600);

    	$this->returnJson($return);
    }


    //注册的功能
    public function register(Request $request)
    {
    	$params = $request->all();
    	// dd($params);

    	$redis = new \Redis();

    	$redis->connect('127.0.0.1',6379);
    	//获取缓存存储的短信验证码
    	$code = $redis->get("REGISTER_".$params['phone']."_CODE");

    	$return = [
    			'code' => 2000,
    			'msg'  => '注册成功'
    		];

    	if ($code != $params['code']) {
    		$return = [
    			'code' => 4000,
    			'msg'  => '手机验证码错误,请重新输入'
    		];

    		$this->returnJson($return);
    	}


    	//删除验证码
    	$redis->del("REGISTER_".$params['phone']."_CODE");

    	//用户注册的功能
    	try{
    		//开启事务
    		\DB::beginTransaction();

    		//添加到user主表信息
    		$member = new member();

    		$data = [
    			'phone' => $params['phone'],
    			'password' => md5($params['password'])
    		];
    		// dd($data,'sfssgg');

    		$userId = $this->storeDataGetId($member ,$data);
    		// dd($userId);
    		//添加user_info表信息
    		$memberInfo = new memberInfo();


    		$data1 = [
    			'user_id' => $userId,
    			'invite_code' => rand(100000,999999)
    		];

    		$this->storeData($memberInfo, $data1);

    		\DB::commit();
    	}catch(\Exception $e){
    		\DB::rollback();

    		\Log::error('用户注册失败'.$e->getMessage());
    		$return = [
    			'code' => $e->getCode(),
    			'msg'  => $e->getMessage()
    		];

    	}

    	$this->returnJson($return);
    }


    //登录的接口
    public function login(Request $request)
    {
        $params = $request->all();

        $phone = $params['phone'];

        $password = $params['password'];

        $return = [
            'code' => 2000,
            'msg'  => '登录成功'
        ];
        //判断手机号
        if (empty($phone) || !isset($phone)){

            $return = [
                'code' => 4001,
                'msg'  => '手机号不能为空'
            ];

            $this->returnJson($return);
        }

        //判断密码
        if (!isset($password) || empty($password)) {

            $return = [
                'code' => 4002,
                'msg'  => '密码不能为空'
             ];

             $this->returnJson($return);
        }

        //判断用户是否存在
        $userInfo = \DB::table('jy_user')->where(['phone' => $phone])->first();

        if (empty($userInfo)) {

            $return = [
                'code' => 4003,
                'msg'  => '用户不存在'
            ];

            $this->returnJson($return);
        }else{

            $postPwd = md5($password);

            if ($userInfo->password != $postPwd) {

                $return = [
                    'code' => 4004,
                    'msg'  => '用户密码错误'
                ];

                $this->returnJson($return);
            } 

            //生成的token的sql语句
            $datas = \DB::select('select replace(uuid(),"-","") as token');
            
            $token = $datas[0]->token;

            $redis = new \Redis();

            $redis->connect('127.0.0.1',6379);

            $redis->setex($token, 1800, $phone);//把用户生成的token值存入redis

            //把token值返回给用户
            $return['data'] = $token;

            $this->returnJson($return);
        }
    }

    //校验token值
    public function token(Request $request)
    {
        $params = $request->all();

        $return = [
            'code' => 2000,
            'msg'  => '登录成功'
        ];

        if (empty($params['token'])) {

            $return = [
                'code' => 4001,
                'msg'  => 'token不能为空'
            ];
        } 

        $res = $this->checkToken($params['token']);

        if (!$res['status']) {
            $return = [
                'code' => 4002,
                'msg'  => 'token不合法'
            ];


            $this->returnJson($return);
        }

        $return['data'] = $res['data'];

        $this->returnJson($return);

    }

}
