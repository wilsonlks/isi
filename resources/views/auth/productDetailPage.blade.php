@extends('layouts.app')

@section('content')

    <?php

        include("./php_file/config.php");
        include("./php_file/dbConnect.php");

        // get specific product from DB

        $url=url()->full();
        $split_url=preg_split("#/#", $url);
        $productID=$split_url[count($split_url)-1];

        $productQ = "SELECT * FROM
            (`product` INNER JOIN `productimage`
            ON product.productID=productimage.productID
            INNER JOIN `productproperty`
            ON product.productID=productproperty.productID)
            LEFT JOIN `category`
            ON `product`.`category`=`category`.`categoryID`
            WHERE `product`.`productID`=$productID";
        $PSet = $dbConnection->prepare($productQ);
        $PSet->execute();
        $PSetResult1 = $PSet->get_result();
        $PSet->execute();
        $PSetResult2 = $PSet->get_result();
        $PSet->execute();
        $PSetResult3 = $PSet->get_result();

        // get rate and review

        $count_review_query = "SELECT COUNT(*) AS reviews FROM `productreview`
                                WHERE `productID`=$productID";
        $count_review_set = $dbConnection->prepare($count_review_query);
        $count_review_set->execute();
        $count_review_result = $count_review_set->get_result();

        $count_review = mysqli_fetch_array($count_review_result);

        if ($count_review['reviews'] != 0) {

            $total_review = $count_review['reviews'];

            $get_review_query = "SELECT * FROM `productreview`
                                    WHERE `productID`=$productID";
            $get_review_set = $dbConnection->prepare($get_review_query);
            $get_review_set->execute();
            $get_review_result1 = $get_review_set->get_result();
            $get_review_set->execute();
            $get_review_result2 = $get_review_set->get_result();

            $total_rating = 0;

            while ($get_review = mysqli_fetch_array($get_review_result1)) {
                if (empty($get_review['review_date_new'])) {
                    $total_rating += $get_review['rating'];
                } else {
                    $total_rating += $get_review['rating_new'];
                }
            }

            $avg_rating = round(($total_rating / $total_review), 1);

        } else {

            $total_review = 0;
            $rating_message = 'No ratings';
            $review_message = 'No reviews';

        }


        // add to cart

        $cart_saved = FALSE;
        
        if (isset($_POST['submit'])) {

            
            $cartDetail = mysqli_fetch_array($PSetResult3);

            $customer = Auth::id();

            $product = $cartDetail['productID'];
                            
            $quantity = 1;

            /*
            * Validate posted values.
            */
            
            $product_unique_query = "SELECT productID FROM shoppingcart
                                        WHERE (customerID = '$customer') AND (productID = '$productID')";
            $statement = $dbConnection->prepare($product_unique_query);
            $statement->execute();
            $product_unique_result = mysqli_fetch_array($statement->get_result());

            if ($product_unique_result) {
                $message = 'This product has already been added to your cart.';
            }

            if (!isset($message)) {
                $add_cart_query = "INSERT INTO shoppingcart (
                                        customerID,
                                        productID,
                                        quantity
                                    ) VALUES (
                                        ?, ?, ?
                                    )";
                $statement = $dbConnection->prepare($add_cart_query);
                $statement->bind_param('isi', $customer, $product, $quantity);
                $statement->execute();
                $statement->close();

                $message = 'This product has been added to your cart successfully.';
                $cart_saved = TRUE;
                $cartDetail = $customer = $product = $quantity = $product_unique_query = $product_unique_result = NULL;
            }

        }

        // // edit product

        // if (isset($_POST['edit'])) {

        //     $productID = $_POST['edit'];
        //     header("location:http://localhost:8000/products/".$productID."/edit"); exit;

        // }

    ?>

    <style type="text/css">
        .product-header {
            padding: 0px;
            background: #e0ffe1;
        }
        .card-image {
            margin: auto;
            width: 50%;
        }
        .image_detail {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            margin-left: auto;
            margin-right: auto;
        }
        /* .card-body, .alert_detail {
            margin: 0px 10px 0px;
        } */
        .card-title, .card-subtitle, .alert {
            float: none;
            clear: both;
        }
        .card-subtitle {
            border-bottom: 2px solid darkgreen;
            padding-bottom: 15px;
            padding-top: 5px;
        }
        .card-text, .alert {
            padding-top: 10px;
        }
        .alert {
            padding-right: 15px;
            padding-bottom: 35px;
        }
        .name_detail {
            text-transform: uppercase;
            font-weight: bold;
            color: green;
        }
        .name_detail, .id_detail, .category_detail, .text {
            display: inline-block;
            float: left;
        }
        .id_detail {
            padding-right: 20px;
        }
        .price_detail, .button_detail, .close {
            display: inline-block;
            float: right;
        }
        .description_detail:first-child {
            padding-bottom: 5px;
        }
        .close {
            border: none;
            background: none;
            color: grey;
            font-weight: bold;
        }
        a {
            text-decoration: none;
            color: black;
        }
        .rate-review-product .card-body {
            padding: 0px 16px 0px;
        }
        .ratings {
            border-bottom: 1px solid black;
        }
        .ratings, .reviews:not(:last-child) .row:last-child {
            border-bottom: 1px solid black;
        }
        .current-review {
            border-top: 1px dashed black;
        }
        .badge-warning {
            color: black;
            background: orange;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-warning:hover {
            color: black;
            background: darkorange;
        }
        .out-of-stock {
            margin-left: 10px;
        }
        .badge-info {
            color: black;
            background: deepskyblue;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-info:hover {
            color: black;
            background: dodgerblue;
        }
        .few-items-left {
            margin-left: 10px;
        }
        .badge-success {
            color: black;
            background: lime;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success:hover {
            color: black;
            background: limegreen;
        }
        .in-stock {
            margin-left: 10px;
        }
    </style>

    <div class="content">

        <!-- print data -->

        <?php
            $detail= mysqli_fetch_array($PSetResult1);
        ?>              
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header product-header">    
                            <div class="card-image">
                                <img class="image_detail" src="../<?php echo $detail['image_url'] ?>" alt="Card image cap">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-title">
                                <div class="card-column"><h1 class="name_detail"><?php echo $detail['productName'] ?></h1></div>
                                @auth
                                    @if (Auth::user()->role=='customer')
                                        <form method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="card-column">
                                                <button id="submit" type="submit" name="submit" class="btn btn-primary button_detail">
                                                    {{ __('Add to Cart') }}
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <div class="card-column">
                                            <div class="button_detail" id="button-edit-box">
                                                <a href="<?php echo $detail['productID'] ?>/edit" class="btn btn-primary" role="button">Edit</a>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <form method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="card-column">
                                            <button id="submit" type="submit" name="submit" class="btn btn-primary button_detail">
                                                {{ __('Add to Cart') }}
                                            </button>
                                        </div>
                                    </form>
                                @endauth
                            </div>
                            <div class="card-subtitle">
                                @auth
                                    @if (Auth::user()->role=='vendor')
                                        <h4 class="mb-2 text-muted id_detail">No.<?php echo $detail['productID'] ?></h4>
                                    @endif
                                @endauth
                                <h4 class="mb-2 text-muted category_detail"><?php echo $detail['categoryName'] ?></h4>
                                @auth
                                    @if (Auth::user()->role=='vendor')
                                        @if ($detail['stock'] == 0)
                                            <span class="out-of-stock"><a href="/products/<?php echo $detail['productID'] ?>/edit" class="badge badge-warning">Out-of-stock</a></span>
                                        @elseif (($detail['stock'] <= 5))
                                            <span class="few-items-left"><a href="/products/<?php echo $detail['productID'] ?>/edit" class="badge badge-info">Few items left</a></span>
                                        @else
                                            <span class="in-stock"><a href="/products/<?php echo $detail['productID'] ?>/edit" class="badge badge-success">In-stock</a></span>
                                        @endif
                                    @else
                                        @if ($detail['stock'] == 0)
                                            <span class="out-of-stock badge badge-warning">Out-of-stock</span>
                                        @elseif ($detail['stock'] <= 5)
                                            <span class="few-items-left badge badge-info">Few items left</span>
                                        @else
                                            <span class="in-stock badge badge-success">In-stock</span>
                                        @endif
                                    @endif
                                @else
                                    @if ($detail['stock'] == 0)
                                        <span class="out-of-stock badge badge-warning">Out-of-stock</span>
                                    @elseif ($detail['stock'] <= 5)
                                        <span class="few-items-left badge badge-info">Few items left</span>
                                    @else
                                        <span class="in-stock badge badge-success">In-stock</span>
                                    @endif
                                @endauth
                                <h4 class="mb-2 text-muted price_detail">$ <?php echo $detail['price'] ?></h4>
                            </div>
                            <div class="card-text"><?php
                                while ($detail= mysqli_fetch_array($PSetResult2)){ ?>
                                    <div class="description_detail"><?php echo $detail['detail_description'] ?></div>
                                <?php }
                            ?></div>
                        </div>
                    </div>                                    
                    @if (isset($message))
                        <div class="alert @if($cart_saved==TRUE) alert-success @else alert-warning @endif alert-dismissible fade show" role="alert">
                            <strong class="alert_detail text"><a href="../../cart">{{ $message }}</a></strong>
                            <button type="button" class="alert_detail close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        <?php $message = $cart_saved = NULL; ?>
                        </div>
                    @endif
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card rate-review-product mt-2">
                        <div class="card-header">{{ __('Ratings and Reviews') }}</div>
                            <div class="card-body">
                                @if ($total_review==0)
                                    <div class="form-group row">
                                        <label for="rate-no" class="col-sm-3 col-form-label">Average Ratings</label>
                                        <div class="col-sm-9">
                                            <input type="text" readonly class="form-control-plaintext" id="rate-no" name="rate-no" value="<?php echo $rating_message ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="review-no" class="col-sm-3 col-form-label">Reviews</label>
                                        <div class="col-sm-9">
                                            <input type="text" readonly class="form-control-plaintext" id="review-no" name="review-no" value="<?php echo $review_message ?>" disabled>
                                        </div>
                                    </div>
                                @else
                                    <div class="form-group row ratings">
                                        <label for="rate-avg" class="col-sm-3 col-form-label">Average Ratings</label>
                                        <div class="col-sm-9">
                                            <input type="text" readonly class="form-control-plaintext" id="rate-avg" name="rate-avg" value="<?php echo $avg_rating ?> / 5" disabled>
                                        </div>
                                    </div>
                                    <?php 
                                        while ($rate_review_detail=mysqli_fetch_array($get_review_result2)) { ?>
                                            <div class="reviews">
                                                <div class="form-group row">
                                                    <label for="rate-old" class="col-sm-3 col-form-label">Rating</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" readonly class="form-control-plaintext" id="rate-old" name="rate-old" value="<?php echo $rate_review_detail['rating'] ?> / 5" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="review-old" class="col-sm-3 col-form-label">Review</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" readonly class="form-control-plaintext" id="review-old" name="review-old" value="<?php echo $rate_review_detail['review'] ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="review-date-old" class="col-sm-3 col-form-label">Review Date</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" readonly class="form-control-plaintext" id="review-date-old" name="review-date-old" value="<?php echo $rate_review_detail['review_date'] ?>" disabled>
                                                    </div>
                                                </div>
                                                @if (!empty($rate_review_detail['review_date_new']))
                                                    <div class="form-group row current-review">
                                                        <label for="rate-new" class="col-sm-3 col-form-label">Current Rating</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" readonly class="form-control-plaintext" id="rate-new" name="rate-new" value="<?php echo $rate_review_detail['rating_new'] ?> / 5" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="review-new" class="col-sm-3 col-form-label">Current Review</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" readonly class="form-control-plaintext" id="review-new" name="review-new" value="<?php echo $rate_review_detail['review_new'] ?>" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="review-date-new" class="col-sm-3 col-form-label">Current Review Date</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" readonly class="form-control-plaintext" id="review-date-new" name="review-date-new" value="<?php echo $rate_review_detail['review_date_new'] ?>" disabled>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        <?php };
                                    ?>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
