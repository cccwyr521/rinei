<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Region; 
use App\Tools\ToolsAdmin;

class RegionController extends Controller
{
    //列表页面
    public function list($fid=1)
    {	
    	$region = new Region();
    	$assign['region_list'] = $this->getDataList($region,['f_id'=>$fid]);

    	// dd($assign);

    	return view('/admin/region/list',$assign);
    }

    //添加页面
    public function add()
    {	
    	$region = new Region();

    	$regions = $this->getDataList($region);

    	$assign['region_list'] = ToolsAdmin::buildTreeString($regions,0,0,'f_id');

    	return view('/admin/region/add',$assign);
    }

    //执行添加页面
    public function store(Request $request)
    {
    	$params = $request->all();

    	$params = $this->delToken($params);

    	//当前添加地区的详细信息
    	$region = new Region();

    	$info = $this->getDataInfo($region,$params['f_id']);

    	$params['level']  = $info->level + 1;

    	$data = $this->storeData($region,$params);

    	if(!$data){

    		return redirect()->back()->with('msg','添加失败');
    	}

    	return redirect('/admin/region/list/'.$params['f_id']);

    }


    public function del($id)
    {

    	// dd($id);
    	$region = new Region();

    	$info = $info = $this->getDataInfo($region,$id);

    	$res = $this->delData($region,$id);

    	return redirect('/admin/region/list/'.$info->f_id);
    }

}
