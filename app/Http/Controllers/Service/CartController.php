<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\BasicReturn;
use Illuminate\Http\Request;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\PdtContent;
use App\Entity\PdtImages;
use App\Entity\CartItem;
use Log;
use Symfony\Component\Yaml\Tests\B;

class CartController extends Controller
{
  //根据父id 获取子类
  public function addCart(Request $request, $product_id)
  {
    $basicReturn = new BasicReturn();
    $bk_cart = $request->cookie('bk_cart');  //读取cookie   cookie保存的字符串为 "1:2,2:1" 含义是---- 产品1数量2产品2数量1
    $bk_cart_arr = ($bk_cart != null ? explode(',', $bk_cart) : array());
    $count = 1;
    foreach ($bk_cart_arr as &$value) {     //必须是引用传递
      $index = strpos($value, ':');  //返回冒号出现的位置
      if(substr($value, 0, $index) == $product_id){
        $count = ((int)substr($value, $index+1)) +1; //冒号后面的数目是书本的数量，点击加入购物车则添加到cookie中
        $value = $product_id. ":" .$count;
        break;
      }
    }

    if($count == 1){
      array_push($bk_cart_arr, $product_id. ':' .$count);
    }

    return response($basicReturn->toJson(0,'添加成功'))->withCookie('bk_cart', implode(',', $bk_cart_arr)); //存cookie
  }

  public function delCart(Request $request)
  {
    $basicReturn = new BasicReturn();
    $product_ids = $request->input('product_ids', '');
    if($product_ids == ''){
      return $basicReturn->toJson(1,'书籍id不能为空');
    }
    $product_ids_arr = explode(',' ,$product_ids);
    //未登录
    $bk_cart = $request->cookie('bk_cart'); //获取购物车书籍信息
    $bk_cart_arr = ($bk_cart!=null? explode(',',$bk_cart) : array());

    foreach($bk_cart_arr as $key=>$value){
      $index = strpos($value, ':');
      $product_id = substr($value, 0, $index);
      if(in_array($product_id, $product_ids_arr)){  //如果该书籍id在cookie的ids数组里面则删除掉它
        array_splice($bk_cart_arr, $key, 1);
        continue;
      }
    }
    //将修改之后的数组重新存放在cookie中
    return response($basicReturn->toJson(0, '删除成功'))->withCookie('bk_cart', implode(',', $bk_cart_arr));
  }


  public function toPdtContent(Request $request, $product_id)
  {

  }
}
