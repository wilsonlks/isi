@extends('layouts.app')

@section('content')


<!-- <?php
    // $productList = DB::select('select * from product');
?>
    <div class="productList">
    <?php
        // foreach ($productList as $product){
        //     echo '<div class="product">'.$product->productID.'<br>'
        //     .$product->productName.'</div>';
        //}
    ?>
    </div> -->


<?php
    $dbConnection = mysqli_connect("localhost", "root", "", "isi");
    $productList = mysqli_query($dbConnection, "SELECT * FROM `product");

    while ($product = mysqli_fetch_assoc($productList)){
        echo
        '<div class="product">'
        .$product['productID'].'<br>'
        .$product['productName'].
        '</div><br>';

    }
?>


@endsection
