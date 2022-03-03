@extends('layouts.app')

@section('content')

<?php
        include("./php_file/dbConnect.php");

        $cart_query = "SELECT * FROM
                        (`shoppingcart` INNER JOIN `users`
                        ON shoppingcart.customerID=users.id
                        INNER JOIN `product`
                        ON shoppingcart.productID=product.productID
                        INNER JOIN `productimage`
                        ON shoppingcart.productID=productimage.productID)
                        WHERE `shoppingcart`.`customerID`=".Auth::id();
        $cart_set = $dbConnection->prepare($cart_query);
        $cart_set->execute();
        $cart_result = $cart_set->get_result();

        $total_query = "SELECT SUM(price) AS total FROM
                        (`shoppingcart` INNER JOIN `users`
                        ON shoppingcart.customerID=users.id
                        INNER JOIN `product`
                        ON shoppingcart.productID=product.productID
                        INNER JOIN `productimage`
                        ON shoppingcart.productID=productimage.productID)
                        WHERE `shoppingcart`.`customerID`=".Auth::id();
        $total_set = $dbConnection->prepare($total_query);
        $total_set->execute();
        $total_result = $total_set->get_result();
?>

<style type="text/css">
    .card {
        width: 100%;
        margin: auto;
    }
    .cart {
        width: 100%;
        margin: auto;
        display: table;
        clear: both;
        padding: 15px 0px 15px;
        border-bottom: 2px solid darkgreen;
    }
    .image_cart {
        width: 150px;
        height: auto;
        display: inline-block;
        float: left;
        clear: both;
    }
    .card-body, .alert_cart {
        margin: 0px 10px 0px;
    }
    .card-title, .card-subtitle, .alert {
        float: none;
        padding-bottom: 35px;
    }
    .card-subtitle, .card-text, .alert {
        padding-top: 10px;
    }
    /* .alert {
        padding-right: 15px;
    } */
    .name_cart {
        text-transform: uppercase;
        font-weight: bold;
        color: green;
    }
    .name_cart, .price_cart, .quantity_cart, .total_cart, .close {
        text-align: right;
    }
    .close {
        border: none;
        background: none;
        color: grey;
        font-weight: bold;
    }
    .total_cart {
        padding-top: 10px;
        font-weight: bold;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{ __('My Shopping Cart') }}</div>
            <div class="card-body">
                <?php
                    while ($detail= mysqli_fetch_array($cart_result)) { ?>
                        <div class="cart">
                            <a href="products/<?php echo $detail['productID'] ?>" class="link-to-product-details" style="text-decoration: none; color:black;">
                                <div class="image_container"><img class="image_cart" src="<?php echo $detail['image_url'] ?>" alt="<?php echo $detail['productName'] ?>" width="auto" height="200px"></div>
                                <h4 class="name_cart"><?php echo $detail['productName'] ?></h4>
                                <h5 class="price_cart">$<?php echo $detail['price'] ?></h5>
                                <h6 class="quantity_cart">&times;<?php echo $detail['quantity'] ?></h6>
                            </a>
                        </div>
                    <?php };
                ?>
            </div>
            <div class="card-footer">
                <?php $total= mysqli_fetch_array($total_result) ?>
                <h2 class="total_cart">Total: $<?php echo $total['total'] ?></h2>
            </div>
        </div>
    </div>
</div>

@endsection