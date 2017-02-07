<?php

namespace App\Http\Controllers\Service;

use App\Entity\Member;
use App\Entity\TempPhone;
use App\Entity\TempEmail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BasicReturn;
use App\Models\M3Email;
use App\Tool\UUID;
use Mail;
//登录验证控制器
class MemberController extends Controller
{
    //登录
    public function login(Request $request){
        $username = $request->get('username', '');
        $password = $request->get('password', '');
        $validate_code = $request->get('validate_code', '');
        $basicReturn = new BasicReturn();
        //校验验证码(手机号码邮箱格式密码校验暂时省略)
        $validate_code_session = $request->session()->get('validate_code');
        if($validate_code != $validate_code_session){
            return $basicReturn->toJson(1, '验证码不正确');
        }
        //查询数据库判断用户名和密码
        $member = null;
        if(strpos($username, '@') == true){
            $member = Member::where('email', $username)->first(); //查询邮箱
        }else{
            $member = Member::where('phone', $username)->first();
        }

        if($member == null){
            return $basicReturn->toJson(2, '该用户不存在');
        }else{
            if(md5('bk'.$password) != $member->password){
                return $basicReturn->toJson(2, '密码不正确');
            }
        }
        //登录成功，写入用户对象到session
        $request->session()->put('member', $member);
        return $basicReturn->toJson(0, '登录成功');
    }

    public function register(Request $request){
        $email = $request->input('email', '');
        $phone = $request->input('phone', '');
        $password = $request->input('password', '');
        $confirm = $request->input('confirm', '');
        $phone_code = $request->input('phone_code', '');
        $validate_code = $request->input('validate_code', '');
        $basicReturn = new BasicReturn();
        if($email == '' && $phone == ''){
            return $basicReturn->toJson(1, '手机号或邮箱不能为空');
        }

        if($password == '' && strlen($password) < 6){
            return $basicReturn->toJson(2, '密码不能少于6位');
        }
        if($confirm == '' &&  strlen($confirm) < 6){
            return $basicReturn->toJson(3, '确认密码为空或者少于6位');
        }
        if($confirm != $password){
            return $basicReturn->toJson(4, '两次密码不一致');
        }

        ##########手机号注册#########
        if($phone != '') {
            if ($phone_code == '' || strlen($phone_code) != 6) {
                return $basicReturn->toJson(5, '验证码应该为6位数');
            }
            $tempPhone = TempPhone::where('phone', $phone)->first();
            if ($tempPhone->code == $phone_code) {
                if (time() > strtotime($tempPhone->deadline)) {
                    return $basicReturn->toJson(7, '验证码已经过期');
                }

                $member = new Member();
                $member->phone = $phone;
                $member->password = md5('bk' . $password);
                $member->save();
                return $basicReturn->toJson(0, '恭喜！注册成功！');
            } else {
                return $basicReturn->toJson(7, '验证码不正确');
            }
        }
        #########邮箱注册########
        if($email != '') {
            if ($validate_code == '' || strlen($validate_code) != 4) {
                return $basicReturn->toJson(6, '验证码应该为4位数');
            }
            $validate_code_session = $request->session()->get('validate_code', ''); //查找该手机号在验证码保存表中的信息(返回的是一个对象)
            //验证码正确
            if ($validate_code != $validate_code_session) {
                return $basicReturn->toJson(8, '验证码不正确');
            }
            $member = new Member();
            $member->email = $email;
            $member->password = md5('bk' . $password);
            //写数据
            $member->save();

            $uuid = UUID::create();  //生成一个uuid

            $m3_email = new M3Email();
            $m3_email->to = $email;
            $m3_email->cc = 'dddawang@163.com';
            $m3_email->subject = '雨后晴天的书房书店验证';
            $m3_email->content = '请于24小时点击该链接完成验证. http://www.book.com/service/validate_email' . '?member_id=' . $member->id . '&code=' . $uuid;

            $tempEmail = new TempEmail;
            $tempEmail->member_id = $member->id;
            $tempEmail->code = $uuid;
            $tempEmail->deadline = date('Y-m-d H-i-s', time() + 24 * 60 * 60);
            $tempEmail->save();
            Mail::send('email_register', ['m3_email' => $m3_email], function ($m) use ($m3_email) {
                // $m->from('hello@app.com', 'Your Application');
                $m->to($m3_email->to, '用户您好')
                   ->cc($m3_email->cc)
                   ->subject($m3_email->subject);
            });
            return $basicReturn->toJson(0, '通过邮箱注册成功');
        }
    }
}
