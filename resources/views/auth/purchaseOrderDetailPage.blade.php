@extends('layouts.app')

@section('content')

    <?php

        include("./php_file/dbConnect.php");


        $url=url()->full();
        $split_url=preg_split("#/#", $url);
        $orderID=$split_url[count($split_url)-1];

        // check authorization

        $check_auth_query = "SELECT `customerID` FROM `purchaseorder`
                                WHERE `poID`=$orderID";
        $check_auth_set = $dbConnection->prepare($check_auth_query);
        $check_auth_set->execute();
        $check_auth_result = $check_auth_set->get_result();

        $check_auth = mysqli_fetch_array($check_auth_result);
        
        if ($check_auth['customerID']==Auth::id()) {

            // get specific purchase order from DB

            $order_query = "SELECT * FROM
                            (`purchaseorder` INNER JOIN `purchaseorderdetail`
                            ON purchaseorder.poID=purchaseorderdetail.poID
                            INNER JOIN `product`
                            ON purchaseorderdetail.productID=product.productID
                            INNER JOIN `users`
                            ON purchaseorder.customerID=users.id
                            INNER JOIN `productimage`
                            ON product.productID=productimage.productID)
                            WHERE `purchaseorder`.`poID`=$orderID";
            $order_set = $dbConnection->prepare($order_query);
            $order_set->execute();
            $order_result1 = $order_set->get_result();
            $order_set->execute();
            $order_result2 = $order_set->get_result();

        } else {

            // return redirect('orders')->with('alert', 'Sorry, You Are Not Allowed to Access This Page.');
            header("location:http://localhost:8000/orders"); exit;
        }

    ?>

    <style type="text/css">
        .card {
            width: 100%;
            margin: auto;
        }
        .card-body {
            margin: 0px 30px 0px 10px;
        }
        .id_order, .date_order, .total_order, .status_order {
        }
        .order {
            width: 100%;
            margin: auto;
            display: table;
            clear: both;
            padding: 15px 0px 15px;
            border-bottom: 2px solid darkgreen;
            margin: 10px;
        }
        .name_order, .price_order, .quantity_order, .sub_total_order {
            text-align: right;
        }
        .status_order::first-letter {
            text-transform: uppercase;
        }
        .image_order {
            width: 150px;
            height: auto;
            display: inline-block;
            float: left;
            clear: both;
        }
        a {
            text-decoration: none;
            color: black;
        }

    </style>

    <!-- customer order detail page -->

    <div class="content">

        <!-- print data -->

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <?php
                            $detail= mysqli_fetch_array($order_result1);
                        ?>
                        <div class="card-header">Purchase Order No.<?php echo $detail['poID'] ?></div>
                            <div class="card-body">
                                <p class="mb-2 date_order"><?php echo $detail['purchase_date'] ?></p>
                                <p class="mb-2 customer_order"><?php echo $detail['name'] ?></p>
                                <p class="mb-2 addr_order"><?php echo $detail['shipping_addr'] ?></p>
                                <p class="mb-2 total_order">Total order amount: $<?php echo $detail['total_order_amount'] ?></p>
                                <p class="mb-2 status_order"><?php echo $detail['status'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{ __('Order Details') }}</div>
                        <div class="card-body">
                            <?php
                                while ($detail= mysqli_fetch_array($order_result2)) { ?>
                                    <div class="order">
                                        <div class="image_container">
                                            <a href="../products/<?php echo $detail['productID'] ?>" class="link-to-product-details">
                                                <img class="image_order" src="../<?php echo $detail['image_url'] ?>" alt="<?php echo $detail['productName'] ?>" width="auto" height="200px">
                                            </a>
                                        </div>
                                        <h4 class="name_order">
                                            <a href="../products/<?php echo $detail['productID'] ?>" class="link-to-product-details">
                                                <?php echo $detail['productName'] ?>
                                            </a>
                                        </h4>
                                        <h5 class="price_order">
                                            <a href="../products/<?php echo $detail['productID'] ?>" class="link-to-product-details">
                                                $<?php echo $detail['price'] ?>
                                            </a>
                                        </h5>
                                        <h6 class="quantity_order">
                                            <a href="../products/<?php echo $detail['productID'] ?>" class="link-to-product-details">
                                                &times;<?php echo $detail['quantity'] ?>
                                            </a>
                                        </h6>
                                        <h5 class="sub_total_order">
                                            <a href="../products/<?php echo $detail['productID'] ?>" class="link-to-product-details">
                                                Sub order amount: $<?php echo ($detail['price']*$detail['quantity']) ?>
                                            </a>
                                        </h5>
                                    </div>
                                <?php };
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
