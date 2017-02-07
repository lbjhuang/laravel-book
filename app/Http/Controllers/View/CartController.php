<?php

namespace App\Http\Controllers\View;

use App\Entity\Product;
use App\Http\Controllers\Controller;
use App\Models\BasicReturn;
use Illuminate\Http\Request;
use App\Entity\CartItem;


class CartController extends Controller
{
  //根据父id 获取子类
  public function toCart(Request $request)
  {
    $cart_items = array();
    $bk_cart = $request->cookie('bk_cart');  //读取cookie   cookie保存的字符串为 "1:2,2:1" 含义是---- 产品1数量2产品2数量1
    $bk_cart_arr = ($bk_cart != null ? explode(',', $bk_cart) : array());



    foreach ($bk_cart_arr as $key => $value) {
      $index = strpos($value, ':');
      $cart_item = new CartItem;
      $cart_item->id = $key;
      $cart_item->product_id = substr($value, 0, $index);
      $cart_item->count = (int) substr($value, $index+1);
      $cart_item->product = Product::find($cart_item->product_id);  //查找产品的详细信息存入cart_item
      if($cart_item->product != null) {
        array_push($cart_items, $cart_item);
      }
    }
    return view('cart')->with('cart_items', $cart_items);
  }

}
