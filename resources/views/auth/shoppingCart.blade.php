@extends('layouts.app')

@section('content')

<?php

    include("./php_file/dbConnect.php");


    // List out all products in shopping cart

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
    $cart_set->execute();
    $cart_result2 = $cart_set->get_result();
    $cart_set->execute();
    $purchase_result1 = $cart_set->get_result();
    $cart_set->execute();
    $purchase_result2 = $cart_set->get_result();


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
    $total_result1 = $total_set->get_result();
    $total_set->execute();
    $total_result2 = $total_set->get_result();


    // check out all items

    $purchase_saved = FALSE;
    
    if (isset($_POST['submit'])) {

        
        $purchase_detail1 = mysqli_fetch_array($purchase_result1);
        $total_detail = mysqli_fetch_array($total_result2);


        // create a new purchase order

        $customer = Auth::id();
        $purchase_date = now();
        $total_order_amount = $total_detail['total'];
        $shipping_addr = $purchase_detail1['shipping_address'];

        $add_order_query = "INSERT INTO purchaseorder (
                                customerID,
                                purchase_date,
                                total_order_amount,
                                shipping_addr
                            ) VALUES (
                                ?, ?, ?, ?
                            )";

        $statement = $dbConnection->prepare($add_order_query);
        $statement->bind_param('isis', $customer, $purchase_date, $total_order_amount, $shipping_addr);
        $statement->execute();

        $last_insert_poID = $dbConnection->insert_id;

        $statement->close();


        // create purchase order details

        while ($purchase_detail2 = mysqli_fetch_array($purchase_result2)) {

            $product = $purchase_detail2['productID'];
            $price = $purchase_detail2['price'];
            $quantity = $purchase_detail2['quantity'];
            $sub_order_amount = $purchase_detail2['price']*$purchase_detail2['quantity'];

            $add_order_detail_query = "INSERT INTO purchaseorderdetail (
                                            poID,
                                            productID,
                                            price,
                                            quantity,
                                            sub_order_amount
                                        ) VALUES (
                                            ?, ?, ?, ?, ?
                                        )";

            $statement = $dbConnection->prepare($add_order_detail_query);
            $statement->bind_param('isisi', $last_insert_poID, $product, $price, $quantity, $sub_order_amount);
            $statement->execute();
            $statement->close();

        }                            


        $purchase_saved = TRUE;
        $purchase_detail1 = $purchase_detail2 = $total_detail = $customer = $purchase_date = $total_order_amount = $shipping_addr = NULL;
        $add_order_query = $product = $price = $quantity = $sub_order_amount = $add_order_detail_query = NULL;


        // clear shopping cart

        $delete_cart_query = "DELETE FROM `shoppingcart`
                                WHERE `customerID`=".Auth::id();
        
        $statement = $dbConnection->prepare($delete_cart_query);
        $statement->execute();
        $statement->close();


        header("location:http://localhost:8000/orders/".$last_insert_poID); exit;

    }

    // change quantity of an item

    if (isset($_POST['change-quantity'])) {

        $product = $_POST['change-quantity-item'];
        $oldquantity = $_POST['change-quantity-old'];
        $update_quantity = $_POST['change-quantity'] > 0 ? $_POST['change-quantity'] : $oldquantity;

        $update_quantity_query = "UPDATE `shoppingcart`
                                    SET `quantity`=$update_quantity
                                    WHERE `productID`=$product";

        $update_quantity_set = $dbConnection->prepare($update_quantity_query);
        $update_quantity_set->execute();
        $update_quantity_set->close();

        $product = $update_quantity = NULL;

        header("location:http://localhost:8000/cart"); exit;

    }

    // remove an item

    if (isset($_POST['delete'])) {

        $product = $_POST['delete_cart'];

        $delete_query = "DELETE FROM `shoppingcart`
                        WHERE (`customerID`=".Auth::id().") 
                        AND (`productID`=$product)";
        $delete_set = $dbConnection->prepare($delete_query);
        $delete_set->execute();
        $delete_set->close();

        $product = NULL;

        header("location:http://localhost:8000/cart"); exit;

    }

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
    }
    .cart:not(:last-child) {
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
        /* margin: 0px 10px 0px; */
    }
    .card-body {
        padding: 0px 16px 0px;
    }
    .card-title, .card-subtitle {
        float: none;
        padding-bottom: 35px;
    }
    .card-subtitle, .card-text, .alert {
        padding-top: 10px;
    }
    /* .alert {
        padding-right: 15px;
    } */
    .name_cart a {
        text-transform: uppercase;
        font-weight: bold;
        color: green;
    }
    .price_cart a, .quantity_cart a {
        color: black;
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
        margin-top: 5px;
        margin-bottom: 0px;
        font-weight: bold;
        float: right;
    }
    a {
        text-decoration: none;
    }
    form, .button_cart, .total_cart {
        display: inline-block;
    }
    .alert {
        padding-right: 16px;
        margin: 0px;
    }
    .delete-button {
        right: 0px;
        bottom: 0px;
    }
    .input-group {
        float: right;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('My Shopping Cart') }}</div>
                <div class="card-body">
                    <?php
                    $data_count = 0;
                    while ($detail = mysqli_fetch_array($cart_result)) { ?>
                        <?php $data_count++; ?>
                        <div class="cart cart-<?php echo $detail['productID'] ?>">
                            <div class="image_container">
                                <a href="products/<?php echo $detail['productID'] ?>" class="link-to-product-details">
                                    <img class="image_cart" src="<?php echo $detail['image_url'] ?>" alt="<?php echo $detail['productName'] ?>" width="auto" height="200px">
                                </a>
                            </div>
                            <h4 class="name_cart">
                                <a href="products/<?php echo $detail['productID'] ?>" class="link-to-product-details">
                                    <?php echo $detail['productName'] ?>
                                </a>
                            </h4>
                            <h5 class="price_cart">
                                    $ <?php echo $detail['price'] ?>
                            </h5>
                            <form method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="quantity_cart input-group">
                                    <input type="hidden" class="change-quantity-item[<?php echo $detail['productID'] ?>]" name="change-quantity-item" value="<?php echo $detail['productID'] ?>">
                                    <input type="hidden" class="change-quantity-old[<?php echo $detail['productID'] ?>]" name="change-quantity-old" value="<?php echo $detail['quantity'] ?>">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon">&times;</span>
                                    </div>
                                    <input type="number" class="change-quantity-<?php echo $detail['productID'] ?>" name="change-quantity" placeholder="<?php echo $detail['quantity'] ?>" aria-label="Quantity" aria-describedby="basic-addon" min="1">
                                </div>
                            </form>
                            <form method="POST" enctype="multipart/form-data">
                                @csrf
                                <div>
                                    <button type="submit" name="delete" class="close delete-button cart-<?php echo $detail['productID'] ?>">
                                        <input type="hidden" class="delete_cart" name="delete_cart" value="<?php echo $detail['productID'] ?>">
                                        <span>&times;</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <?php };
                        if ($data_count == 0) { ?>
                            <div class="no_product">No product</div>
                        <?php };
                    ?>
                    <!-- <ul class="list-unstyled">
                        <?php 
                            $data_count = 0;
                            while ($detail = mysqli_fetch_array($cart_result2)) { ?>
                                <?php $data_count++; ?>
                                <li class="media">
                                    <img class="align-self-center mr-3" src="<?php echo $detail['image_url'] ?>" alt="<?php echo $detail['productName'] ?>">
                                    <div class="media-body">
                                        <h5 class="mt-0 mb-1"><?php echo $detail['productName'] ?></h5>
                                        <p>$<?php echo $detail['price'] ?></p>
                                        <p class="mb-0">&times;<?php echo $detail['quantity'] ?></p>
                                    </div>
                                </li>
                            <?php };
                        ?>
                    </ul> -->
                </div>
                <?php 
                    if ($data_count != 0) { ?>
                        <div class="card-footer">
                            <form method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="card-column">
                                    <button id="submit" type="submit" name="submit" class="btn btn-primary button_cart">
                                        {{ __('Check Out') }}
                                    </button>
                                </div>
                            </form>
                            <?php $total= mysqli_fetch_array($total_result1) ?>
                            <h3 class="total_cart">Total: $ <?php echo $total['total'] ?></h3>
                        </div>
                    <?php };
                ?>
            </div>
        </div>
    </div>
</div>
    
@endsection