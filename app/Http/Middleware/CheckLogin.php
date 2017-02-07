<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class CheckLogin
{
   public function handle($request, Closure $next){  //����Ƿ��¼���м��
       $member = $request->session->get('member', '');
       if($member == ''){
           $return_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
           return redirect('/login?return_url=' . urlencode($return_url));
       }

       return $next($request);
   }
}
