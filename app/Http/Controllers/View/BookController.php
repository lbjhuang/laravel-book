<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\PdtContent;
use App\Entity\PdtImages;
use App\Entity\CartItem;
use Log;

class BookController extends Controller
{
  public function toCategory($value='')
  {
    Log::info('进入书籍类别');  //打印日志
    $categorys = Category::whereNull('parent_id')->get();  //获取一级分类
    return view('category')->with('categorys', $categorys);
  }

  public function toProduct($category_id)
  {
    $products = Product::where('category_id', $category_id)->get();  //获取一级分类
    return view('product')->with('products', $products);
  }

  public function toPdtContent(Request $request, $product_id)
  {
    $product = Product::find($product_id);
    $pdt_content = PdtContent::where('product_id', $product_id)->first();
    $pdt_images = PdtImages::where('product_id', $product_id)->get();

    //读取cookie中的购物车信息
    $bk_cart = $request->cookie('bk_cart');  //读取cookie   cookie保存的字符串为 "1:2,2:1" 含义是---- 产品1数量2产品2数量1
    $bk_cart_arr = ($bk_cart != null ? explode(',', $bk_cart) : array());
    $count = 0;
    foreach ($bk_cart_arr as $value) {
      $index = strpos($value, ':');  //返回冒号出现的位置
      if(substr($value, 0, $index) == $product_id){  //如果购物车中有该id的商品，则读取它的购买数量
        $count = ((int)substr($value, $index+1)); //冒号后面的数目是书本的数量
        break;
      }
    }
    return view('pdt_content')->with('product', $product)
                              ->with('pdt_content', $pdt_content)
                              ->with('pdt_images', $pdt_images)
                              ->with('count', $count);

//    $count = 0;
//    $member = $request->session()->get('member', '');
//    if($member !=''){
//      $cart_items = CartItem::where('member_id', $member->id)->get();
//    }
  }
}
