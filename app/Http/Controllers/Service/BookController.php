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

class BookController extends Controller
{
  //根据父id 获取子类
  public function getCategoryByParentId($parent_id)
  {
    $categorys = Category::where('parent_id', $parent_id)->get();
    $basiceReturn = new BasicReturn();
    return $basiceReturn->toJson(0, '返回成功', $categorys);
  }

  public function toProduct($category_id)
  {

  }

  public function toPdtContent(Request $request, $product_id)
  {

  }
}
