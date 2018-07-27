<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    //
    public function index(Request $request)
    {
        //创建查询构造器
        $products = Product::query()->where('on_sale', true);
        //判断是否有提交search 参数
        if ($search = $request->input('search', '')) {
            $like = '%' . $search . '%';
            $products->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)->orWhere('description', 'like', $like);
                    });
            });
        }
        //是否有排序参数
        if($order=$request->input('order','')){
            //是否已_asc或_desc结尾
            if(preg_match('/^(.+)_(asc|desc)$/',$order,$m)){
              //若果是则是合法的排序
                if(in_array($m[1],['price','sold_count','rating'])){
                  $products->orderBy($m[1],$m[2]);
                }
            }
        }
        $products=$products->paginate(16);
        return view('products.index', ['products' => $products,'filters'  => [
            'search' => $search,
            'order'  => $order,
        ],]);
    }

    public function show(Product $product,Request $request){
       if(!$product->on_sale){
            throw new InvalidRequestException('商品未上架');
       }
        return view('products.show',['product'=>$product]);
    }
}
