<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Member;
use App\Model\UserBonus;
class MemberController extends Controller
{
    //
    public function list() 
    { 	
    	$member = new Member();

    	$assign['member'] = $this->getPageList($member); 

    	return view('/admin/member/list',$assign);
    }

    public function detail($id)
    {	
    	$member = new Member();
        $userBonus = new UserBonus(); 
    	// dd($member);
    	$assign['info'] = $member->getInfo($id);
    
    	$assign['bonus_list'] = $userBonus->getRecordByUid($id);
    	return view('/admin/member/detail',$assign);
    }

   
}
