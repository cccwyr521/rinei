<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    //
    const
        USE_ABLE = 1,//可用
        USE_DISABLE = 2,//禁用
        END = TRUE;

    protected $table = "jy_brand";

    public $timestamps = false;

    //获取品牌列表数据
    public static function getList($where=[]){

    	return $list = self::where($where)->get()->toArray();
    }

    //添加商品品牌
    public static function create($data){

    	return self::insert($data);
    }

    //获取品牌详情
    public static function getInfo($id){

    	return self::where('id',$id)->first();
    }

    //执行修改的操作
    public static function doUpdate($data,$id){

    	return self::where('id',$id)->update($data);
    }

    //删除商品
    public static function del($id){

    	return self::where('id',$id)->delete();
    }
}
