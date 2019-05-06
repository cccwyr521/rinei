<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Shipping;
class ShippingController extends Controller
{
    //
	//配送列表页面
    public function list()
    {		
    	$shipping  = new Shipping();

    	$assign['shipping'] = $this->getDataList($shipping);


    	return view('/admin/shipping/list',$assign);
    }
    //添加配送方式页面
    public function add()
    {
    	return view('/admin/shipping/add');
    }
    //执行添加配送方式
    public function doAdd(Request $request)
    {
    	$params = $request->all();

    	$params = $this->delToken($params);

    	$shipping = new Shipping();

    	$res = $this->storeData($shipping,$params);

    	if(!$res){

    		return redirect()->back()->with('msg','添加配送方式失败');
    	}

    	return redirect('/admin/shipping/list');

    }

    public function del($id)
    {
    	$shipping = new Shipping();

    	$this->delData($shipping,$id);

    	return redirect('/admin/shipping/list');
    }
}
