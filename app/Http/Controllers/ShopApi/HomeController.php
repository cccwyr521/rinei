<?php

namespace App\Http\Controllers\ShopApi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Category; 
use App\Tools\ToolsAdmin;
use App\Tools\ToolsOss;
use App\Model\Brand;
use App\Model\Article;
class HomeController extends Controller
{
    //商品分类接口
    public function category()
    {
    	$category = new Category();

    	$data = $this->getDataList($category);

    	$data = ToolsAdmin::buildTree($data,0,"f_id" );

    	$return = [ 
    		'code' => 2000,
    		'msg'  => '成功',
    		'data' => $data
    	];

    	$this->returnJson($return);
    }

    //首页广告位的接口 
    public function ad(Request $request)
    {	
    	//广告位的接口
    	$position = $request->input('position_id',1);

    	$nums = $request->input('nums',1);

    	$time = date("Y-m-d H:i:s");

    	$ad = \DB::table('jy_ad')->select('id','ad_name','image_url','ad_link')
    					->where('position_id',$position)
    					->where('start_time','<',$time)
    					->where('end_time','>',$time)
    					->limit($nums)
    					->get();

    	// print_r($ad);exit;
    	//组装广告的数据
    	$ad_data = [];


    	foreach ($ad as $k => $v) {
    		$ad_data[$k] = [

    			'id' => $v->id,
    			'ad_name' => $v->ad_name,
    			'image_url' => $v->image_url,
    			'ad_link' => $v->ad_link   			
    		];
    	}

    		$return = [
    			'code' => 2000,
    			'msg'  => '成功',
    			'data' => $ad_data
    		];
            // dd($return);

    	$this->returnJson($return);
    	
    }  		

    //商品类型列表
    public function goodsList(Request $request)
    {
        //获取商品类型
        $type = $request->input('type',1);
        // dd($type);
        //商品的数量
        $nums = $request->input('nums',5);

        if($type == 1){//热卖商品
            $goods = \DB::table('jy_goods')->select('id','goods_name','market_price')
                        ->where('is_hot',1)
                        ->where('is_shop',1)
                        ->limit($nums)
                        ->get();

                        // dd($goods);die;
        }elseif($type == 2){//推荐商品
            $goods = \DB::table('jy_goods')->select('id','goods_name','market_price')
                        ->where('is_recommand',1)
                        ->where('is_shop',1)
                        ->limit($nums)
                        ->get();
        }else{//新品
            $goods = \DB::table('jy_goods')->select('id','goods_name','market_price')
                        ->where('is_new',1)
                        ->where('is_shop',1)
                        ->limit($nums)
                        ->get();
        } 
        // dd($goods);
        
        $goodsList = [];
            foreach ($goods as $k => $v) {
                $gallery = \DB::table('jy_goods_gallery')->where('goods_id',$v->id)->first();
                // dd($gallery);
                $goodsList[$k] = [
                    'id' => $v->id,
                    'goods_name' => $v->goods_name,
                    'market_price' => $v->market_price,
                    'image_url' => $gallery->image_url
                ];
                $return = [
                    'code' => 2000,
                    'msg'  => '成功',
                    'data' => $goodsList
                ];
                
            }
           $this->returnJson($return);
            
    }

    //品牌列表	
    public function brand(Request $request)
    {
        $nums = $request->input('nums',5);

        $object = new Brand();

        $brand = $this->getLimitList($object,$nums,['status'=>1]);
        // dd($brand);
        $return = [
            'code' => 2000,
            'msg'  => '成功',
            'data' => $brand
        ];

        $this->returnJson($return);
    }

    //最新文章
    public function newsArticle(Request $request)
    {
        $nums = $request->input('nums',5);

        $article = new Article();

        $news = $article->getNewArticles($nums,['status'=>3]);

        $return = [
            'code' => 2000,
            'msg'  => '成功',
            'data' => $news
        ];

        $this->returnJson($return);
    }
}
