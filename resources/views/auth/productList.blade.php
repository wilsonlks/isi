@extends('layouts.app')

@section('content')


    <?php
        $productList = DB::select('select * from product');
    ?>
        <div class="productList">
        <?php
            foreach ($productList as $product){
                echo '<div class="product">'.$product->productID.'<br>'
                .$product->productName.'</div>';
            }
        ?>
        </div>





@endsection
