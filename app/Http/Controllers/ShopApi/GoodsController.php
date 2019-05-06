<?php

namespace App\Http\Controllers\ShopApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Goods;
use App\Model\GoodsGallery;

class GoodsController extends Controller
{
    //商品详情接口
    public function detail($goodsId)
    {
    	//商品基本详情
        // var_dump($goodsId);die;
        $redis = new \Redis();

        $redis->connect('127.0.0.1',6379);

        $key = 'GoodsInfo_data';

        $data = $redis->get($key);
        
    	$return = [
    		'code' => 2000,
    		'msg'  => '获取商品详情成功'
    	];
    	
        if (empty($data) && $data['goods']['id'] != $goodsId) {
            
            // {
                // dd($data->id);die;
                $goods = new Goods;

                $goodsInfo = $this->getDataInfo($goods,$goodsId)->toArray();
                // dd($goodsInfo);
                //商品相册信息
                $goodsGallery = new GoodsGallery;

                $gallerys = $this->getDataList($goodsGallery,['goods_id'=>$goodsId]);

                // dump($gallerys);die;

                //商品的详情信息接口
                // $goods

                // dd($gallery);

                $return['data'] =[
                    'goods' => $goodsInfo,
                    'gallery' => $gallerys 
                ];

                
                $redis->setex($key, 600, json_encode($return['data']));

                $this->returnJson($return);
            // }
            
          
        }else{

            $return = $data;

            $this->returnJson(json_decode($return),true);
        }
    	

    	
    }
}
