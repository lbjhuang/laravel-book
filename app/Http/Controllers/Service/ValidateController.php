<?php

namespace App\Http\Controllers\Service;

use App\Entity\TempPhone;
use App\Entity\TempEmail;
use App\Entity\Member;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BasicReturn;
use App\Tool\Validate\ValidateCode;
use App\Tool\SMS\SendTemplateSMS;
use Illuminate\Foundation\Auth\ResetsPasswords;

//登录验证控制器
class ValidateController extends Controller
{
    //生成验证码和验证码图片
    public function create(Request $request){
        $validate = new ValidateCode();
        //把生成的验证码放到session
        $request->session()->put('validate_code', $validate->getCode());
        $validate->doimg(); //生成验证码图片
    }

    //发送短信
    public function sendMsg(Request $request){
        $basicReturn = new BasicReturn();
        $phone = $request->input('phone', '');
        if($phone == ''){
            return $basicReturn->toJson(1, '手机号码不能为空');
        }
        if(strlen($phone) != 11 || $phone[0] != '1'){
            return $basicReturn->toJson(2, '手机号码格式不正确');
        }
        //发送验证码
        $send = new SendTemplateSMS();
        $code = $this->randCode();
        $basicReturn = $send->sendTemplateSMS($phone, array($code,60), 1);
        if($basicReturn->status == 0){
            //验证码发送成功,插入数据之前查看是否已经有过该号码
            $tempPhone = TempPhone::where('phone', $phone)->first();
            if($tempPhone == null){
                $tempPhone = new TempPhone;
            }
            //插入数据库
            $tempPhone->phone = $phone;
            $tempPhone->code = $code;
            $tempPhone->deadline = date("Y-m-d H:i:s", time()+60*60);
            $tempPhone->save();
        }
        return $basicReturn->toJson(0,'发送成功');
    }

    //随机数字
    public function randCode($num = 6)
    {
        $code = '';
        for ($i = 0; $i < $num; $i++) {
            if ($i == 0) {
                $code .= mt_rand(1, 9);
            } else {
                $code .= mt_rand(0, 9);
            }
        }
        return $code;
    }


    public function validateEmail(Request $request)
    {
        $member_id = $request->input('member_id', '');
        $code = $request->input('code', '');
        if($member_id == '' || $code == '') {
            return '验证异常';
        }

        $tempEmail = TempEmail::where('member_id', $member_id)->first();  //查找邮件注册时的临时存放的uuid是否是空
        if($tempEmail == null) {
            return '验证异常';
        }

        if($tempEmail->code == $code) {
            if(time() > strtotime($tempEmail->deadline)) {
                return '该链接已失效';
            }
            //激活后修改用户的验证状态，返回登录界面
            $member = Member::find($member_id);
            $member->active = 1;
            $member->save();

            return redirect('/login');
        } else {
            return '该链接已失效';
        }
    }
}
