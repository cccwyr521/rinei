<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tools\ToolsAdmin;
use App\Model\Batch;

class BatchController extends Controller
{
    //批次的列表页面
    public function list()
    {
    	$batch = new Batch();
    	$assign['batch_list'] = $this->getPageList($batch);
    	return view('admin.batch.list', $assign);
    }

    public function add()
    {
    	return view('/admin/batch/add');
    }

    public function doAdd(Request $request)
    {
    	$params = $request->all();
    	$params = $this->delToken($params);
    	$params = ToolsAdmin::uploadFile($params['file_path'], false);
    	$params['status'] = 2;
    	$batch = new Batch();
    	$res = $this->storeData($batch, $params);
    	if(!$res){
    		return redirect()->back()->with('msg','添加批次失败');
    	}
    	return redirect('/admin/batch/list');
    }
}
