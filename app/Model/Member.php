<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    //会员表
    protected $table = 'jy_user';
    public $timestamps = true;
    public function getInfo($id)
    {
    	$info = self::select('*')
    			->leftJoin('jy_user_info','jy_user.id','=','jy_user_info.user_id')
    			->where('jy_user.id',$id)
    			->first();

    	return $info;
    }

}
