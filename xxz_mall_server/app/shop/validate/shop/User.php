<?php

namespace app\shop\validate\shop;

use think\Validate;

 
class User extends Validate
{
   protected $rule = [
        'mobile'  => 'require|mobile',
        'password'  => 'require|length:8,20',
        'code'  => 'require|length:6|number',
        'type'  => 'require|in:1,2,3'
    ];

    protected $message  =[
        'mobile' => '手机号不正确' ,
        'password'  => '密码长度8到20位',
        'code'  => '验证码请输入6位数字',
        'type' => '参数错误!'
    ];
    
    protected $scene = [
        'mobile'  =>  ['mobile','type'],
        'code'  =>  ['mobile','code'],
        'password'  =>  ['mobile','code','password']
    ];  
            
}
