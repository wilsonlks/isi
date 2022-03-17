@extends('layouts.app')

@section('content')

    <?php

        include("./php_file/config.php");
        include("./php_file/dbConnect.php");

        // get specific product from DB

        $url=url()->full();
        $split_url=preg_split("#/#", $url);
        $productID=$split_url[count($split_url)-1];

        // product image

        $product_image_query = "SELECT * FROM
            (`product` INNER JOIN `productimage`
            ON product.productID=productimage.productID
            INNER JOIN `productproperty`
            ON product.productID=productproperty.productID)
            LEFT JOIN `category`
            ON `product`.`category`=`category`.`categoryID`
            WHERE `product`.`productID`=$productID
            GROUP BY `productimage`.`image_number`
            ORDER BY `productimage`.`image_number`";
        $product_image_set = $dbConnection->prepare($product_image_query);
        $product_image_set->execute();
        $product_image_result1 = $product_image_set->get_result();
        $product_image_set->execute();
        $product_image_result2 = $product_image_set->get_result();
        $product_image_set->execute();
        $product_detail_result = $product_image_set->get_result();

        // product property

        $product_property_query = "SELECT * FROM
            (`product` INNER JOIN `productimage`
            ON product.productID=productimage.productID
            INNER JOIN `productproperty`
            ON product.productID=productproperty.productID)
            LEFT JOIN `category`
            ON `product`.`category`=`category`.`categoryID`
            WHERE `product`.`productID`=$productID
            GROUP BY `productproperty`.`property_number`
            ORDER BY `productproperty`.`property_number`";
        $product_property_set = $dbConnection->prepare($product_property_query);
        $product_property_set->execute();
        $product_property_result = $product_property_set->get_result();

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
            $rating_review_message = 'No ratings and reviews';

        }


        // add to cart

        $cart_saved = FALSE;

        if (isset($_POST['submit'])) {

            $customer = Auth::id();
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
                $statement->bind_param('isi', $customer, $productID, $quantity);
                $statement->execute();
                $statement->close();

                $message = 'This product has been added to your cart successfully.';
                $cart_saved = TRUE;
                $customer = $quantity = $product_unique_query = $product_unique_result = NULL;
            }

        }

    ?>

    <style type="text/css">
        .product-header {
            padding: 0px;
            background: #e0ffe1;
        }
        .card-image {
            margin: auto;
            /* width: 50%; */
            height: 400px;
        }
        .image_detail {
            display: block;
            width: auto;
            height: 400px;
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
        #rate-review-no {
            padding-top: 16px;
            padding-bottom: 16px;
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
        .out-of-stock, .few-items-left, .in-stock {
            margin-left: 20px;
        }
        .carousel{
            position:relative
        }
        .carousel-inner{
            position:relative;
            width:100%;
            overflow:hidden
        }
        .carousel-item{
            position:relative;
            display:none;
            -webkit-box-align:center;
            -ms-flex-align:center;
            align-items:center;
            width:100%;
            transition:-webkit-transform .6s ease;
            transition:transform .6s ease;
            transition:transform .6s ease,-webkit-transform .6s ease;
            -webkit-backface-visibility:hidden;
            backface-visibility:hidden;
            -webkit-perspective:1000px;
            perspective:1000px
        }
        .carousel-control-next,.carousel-control-prev{
            position:absolute;
            top:0;
            bottom:0;
            display:-webkit-box;
            display:-ms-flexbox;
            display:flex;
            -webkit-box-align:center;
            -ms-flex-align:center;
            align-items:center;
            -webkit-box-pack:center;
            -ms-flex-pack:center;
            justify-content:center;
            width:15%;
            color:#fff;
            text-align:center;
            opacity:.5
        }
        .carousel-control-next:focus,.carousel-control-next:hover,.carousel-control-prev:focus,.carousel-control-prev:hover{
            color:#fff;
            text-decoration:none;
            outline:0;
            opacity:.9
        }
        .carousel-control-prev{
            left:0
        }
        .carousel-control-next{
            right:0
        }
        .carousel-control-next-icon,.carousel-control-prev-icon{
            display:inline-block;
            width:20px;
            height:20px;
            background:transparent no-repeat center center;
            background-size:100% 100%
        }
        .carousel-control-prev-icon{
            background-image:url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 8 8'%3E%3Cpath d='M5.25 0l-4 4 4 4 1.5-1.5-2.5-2.5 2.5-2.5-1.5-1.5z'/%3E%3C/svg%3E")
        }
        .carousel-control-next-icon{
            background-image:url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 8 8'%3E%3Cpath d='M2.75 0l-1.5 1.5 2.5 2.5-2.5 2.5 1.5 1.5 4-4-4-4z'/%3E%3C/svg%3E")
        }
        .carousel-indicators{
            position:absolute;
            right:0;
            bottom:10px;
            left:0;
            z-index:15;
            display:-webkit-box;
            display:-ms-flexbox;
            display:flex;
            -webkit-box-pack:center;
            -ms-flex-pack:center;
            justify-content:center;
            padding-left:0;
            margin-right:15%;
            margin-left:15%;
            list-style:none
        }
        .carousel-indicators li{
            position:relative;
            -webkit-box-flex:0;
            -ms-flex:0 1 auto;
            flex:0 1 auto;
            width:30px;
            height:3px;
            margin-right:3px;
            margin-left:3px;
            text-indent:-999px;
            background-color:rgba(255,255,255,.5)
        }
        .carousel-indicators li::before{
            position:absolute;
            top:-10px;
            left:0;
            display:inline-block;
            width:100%;
            height:10px;
            content:""
        }
        .carousel-indicators li::after{
            position:absolute;
            bottom:-10px;
            left:0;
            display:inline-block;
            width:100%;
            height:10px;
            content:""
        }
        .carousel-indicators .active{
            background-color:#fff
        }
    </style>

    <div class="content">

        <!-- print data -->

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header product-header">
                            <div class="card-image">
                                <div id="carouselIndicators" class="carousel slide" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        <?php 
                                            while ($detail= mysqli_fetch_array($product_image_result1)) { ?>
                                                <li data-target="#carouselIndicators" data-slide-to="<?php echo $detail['image_number']-1 ?>" class="{{ $detail['image_number']==1 ? 'active' : '' }}"></li>
                                            <?php }
                                        ?>
                                    </ol>
                                    <div class="carousel-inner">
                                        <?php 
                                            while ($detail= mysqli_fetch_array($product_image_result2)) { ?>
                                                <div class="carousel-item {{ $detail['image_number']==1 ? 'active' : '' }}">
                                                <img class="image_detail" src="../<?php echo $detail['image_url'] ?>" alt="<?php echo $detail['image_number'] ?> slide">
                                                </div>
                                            <?php }
                                        ?>
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselIndicators" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <!-- <span class="sr-only">Previous</span> -->
                                    </a>
                                    <a class="carousel-control-next" href="#carouselIndicators" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <!-- <span class="sr-only">Next</span> -->
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php $detail= mysqli_fetch_array($product_detail_result); ?>
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
                                        @elseif (($detail['stock'] <= 10))
                                            <span class="few-items-left"><a href="/products/<?php echo $detail['productID'] ?>/edit" class="badge badge-info">Few items left</a></span>
                                        @else
                                            <span class="in-stock"><a href="/products/<?php echo $detail['productID'] ?>/edit" class="badge badge-success">In-stock</a></span>
                                        @endif
                                    @else
                                        @if ($detail['stock'] == 0)
                                            <span class="out-of-stock badge badge-warning" style="visibility:hidden">Out-of-stock</span>
                                        @elseif ($detail['stock'] <= 10)
                                            <span class="few-items-left badge badge-info" style="visibility:hidden">Few items left</span>
                                        @else
                                            <span class="in-stock badge badge-success" style="visibility:hidden">In-stock</span>
                                        @endif
                                    @endif
                                @else
                                    @if ($detail['stock'] == 0)
                                        <span class="out-of-stock badge badge-warning" style="visibility:hidden">Out-of-stock</span>
                                    @elseif ($detail['stock'] <= 10)
                                        <span class="few-items-left badge badge-info" style="visibility:hidden">Few items left</span>
                                    @else
                                        <span class="in-stock badge badge-success" style="visibility:hidden">In-stock</span>
                                    @endif
                                @endauth
                                <h4 class="mb-2 text-muted price_detail">$ <?php echo $detail['price'] ?></h4>
                            </div>
                            <div class="card-text">
                                <?php $detail= mysqli_fetch_array($product_property_result); ?>
                                <div class="description_detail"><?php echo $detail['detail_description'] ?></div>
                                <?php $detail= mysqli_fetch_array($product_property_result); ?>
                                <div class="description_detail"><?php echo $detail['detail_description'] ?></div>
                            </div>
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
                                    <input type="text" readonly class="form-control-plaintext" id="rate-review-no" name="rate-review-no" value="<?php echo $rating_review_message ?>" disabled>
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
