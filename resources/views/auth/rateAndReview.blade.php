@extends('layouts.app')

@section('content')

    <?php

        include("./php_file/dbConnect.php");


        $url=url()->full();
        $split_url=preg_split("#/#", $url);
        $orderID=$split_url[count($split_url)-4];
        $productID=$split_url[count($split_url)-2];

        // check authorization

        $check_auth_query = "SELECT * FROM `purchaseorder`
                                WHERE `poID`=$orderID";
        $check_auth_set = $dbConnection->prepare($check_auth_query);
        $check_auth_set->execute();
        $check_auth_result = $check_auth_set->get_result();

        $check_auth = mysqli_fetch_array($check_auth_result);

        if (($check_auth['customerID'] == Auth::id()) && ($check_auth['status'] == 'shipped')) {

            // get specific purchase order product from DB

            $order_query = "SELECT *, purchaseorderdetail.price AS oldprice FROM
                            (`purchaseorder` INNER JOIN `purchaseorderdetail`
                            ON purchaseorder.poID=purchaseorderdetail.poID
                            INNER JOIN `product`
                            ON purchaseorderdetail.productID=product.productID
                            INNER JOIN `users`
                            ON purchaseorder.customerID=users.id
                            INNER JOIN `productimage`
                            ON product.productID=productimage.productID)
                            WHERE `purchaseorder`.`poID`=$orderID
                            AND `purchaseorderdetail`.`productID`=$productID";
            $order_set = $dbConnection->prepare($order_query);
            $order_set->execute();
            $order_result = $order_set->get_result();

            $check_review_query = "SELECT *, COUNT(*) AS reviews FROM `productreview`
                                    WHERE `poID`=$orderID
                                    AND `productID`=$productID";
            $check_review_set = $dbConnection->prepare($check_review_query);
            $check_review_set->execute();
            $check_review_result = $check_review_set->get_result();

            $check_review = mysqli_fetch_array($check_review_result);

            if ($check_review['reviews'] != 0) {
                if (empty($check_review['review_date_new'])) {
                    $review_status = 'old';
                } else {
                    $review_status = 'new';
                }
            } else {
                $review_status = 'none';
            }

        } else {

            // return redirect('orders')->with('alert', 'Sorry, You Are Not Allowed to Access This Page.');
            header("location:http://localhost:8000/orders"); exit;
        }

        if (isset($_POST['cancel-confirm'])) {

            $date = now();

            $update_status_query = "UPDATE `purchaseorder`
                                    SET `status`=\"cancelled\", `cancel_date`=\"".$date."\", `cancel_by`=\"customer\"
                                    WHERE `poID`=$orderID";

            $update_status_set = $dbConnection->prepare($update_status_query);
            $update_status_set->execute();
            $update_status_set->close();

            header("location:http://localhost:8000/orders/".$orderID); exit;

        }


        if (isset($_POST['review'])) {

            $rate = isset($_POST['rate-product']) ? $_POST['rate-product'] : '';
            $review = isset($_POST['review-product']) ? $_POST['review-product'] : '';
            $date = now();

            if (empty($rate)||($rate=="0")) {
                $error_rate = 'Please rate at 1 to 5 stars.';
                $error_detail[] = 'Please rate at 1 to 5 stars.';
            }
            if (empty($review)) {
                $error_review = 'Please comment a short review.';
                $error_detail[] = 'Please comment a short review.';
            }

            if (!isset($error_detail)) {

                if ($review_status=='none') {

                    $rate_review_query = "INSERT INTO `productreview`
                                            SET `productID`=$productID, `poID`=$orderID, `rating`=$rate, `review`=\"".$review."\", `review_date`=\"".$date."\"";

                } elseif ($review_status=='old') {

                    $rate_review_query = "UPDATE `productreview`
                                            SET `rating_new`=$rate, `review_new`=\"".$review."\", `review_date_new`=\"".$date."\"
                                            WHERE `productID`=$productID
                                            AND `poID`=$orderID";

                }

                $statement = $dbConnection->prepare($rate_review_query);
                $statement->execute();
                $statement->close();

                $rate = $review = $date = $error_rate = $error_review = $error_detail[] = NULL;

                header("location:http://localhost:8000/orders/".$orderID."/products/".$productID."/reviews"); exit;

            }

        }
    ?>

    <style type="text/css">
        .card {
            width: 100%;
            margin: auto;
        }
        .card-body {
            padding: 16px;
        }
        .product {
            width: 100%;
            margin: auto;
            /* display: table;
            clear: both; */
            padding: 15px 0px 15px;
        }
        #button-box {
            float: right;
            display: inline-block;
        }
        .product-detail .card-body {
            padding: 7px 16px 7px;
        }
        .status_order {
            text-transform: uppercase;
        }
        .image-review {
            width: 150px;
            height: 150px;
            object-fit: scale-down;
            display: inline-block;
            float: left;
            clear: both;
        }
        a:link, a:hover, a:active, a:visited {
            text-decoration: none;
            color: black;
        }
        .name-review {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 1.5rem;
            color: green;
            text-align: right;
        }
        .price-review, .quantity-review, .sub-total-review, .buy-again-review {
            font-size: 1rem;
            text-align: right;
        }
        .sm-detail {
            width: 100px;
        }
        .sub-total-review {
            border-top: 1px solid black;
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
    </style>

    <!-- customer order product rate and review page -->

    <div class="content">

        <!-- print data -->
        <?php
            $detail= mysqli_fetch_array($order_result);
        ?>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card order-detail">
                        <div class="card-header">Purchase Order No.<?php echo $detail['poID'] ?></div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="date_order" class="col-sm-3 col-form-label">Purchase Date</label>
                                    <div class="col-sm-9">
                                        <input type="text" readonly class="form-control-plaintext" id="date_order" name="date_order" value="<?php echo $detail['purchase_date'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="customer_order" class="col-sm-3 col-form-label">Customer Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" readonly class="form-control-plaintext" id="customer_order" name="customer_order" value="<?php echo $detail['name'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="addr_order" class="col-sm-3 col-form-label">Shipping Address</label>
                                    <div class="col-sm-9">
                                        <input type="text" readonly class="form-control-plaintext" id="addr_order" name="addr_order" value="<?php echo $detail['shipping_addr'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="total_order" class="col-sm-3 col-form-label">Total Order Amounts</label>
                                    <div class="col-sm-9">
                                        <input type="text" readonly class="form-control-plaintext" id="total_order" name="total_order" value="$ <?php echo $detail['total_order_amount'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-group row status-box">
                                    <label for="status_order" class="col-sm-3 col-form-label">Status</label>
                                    <div class="col-sm-9">
                                        <input type="text" readonly class="form-control-plaintext status_order" id="status" name="status_order" value="<?php echo $detail['status'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="shipment_order" class="col-sm-3 col-form-label">Shipment Date</label>
                                    <div class="col-sm-9">
                                        <input type="text" readonly class="form-control-plaintext" id="shipment_order" name="shipment_order" value="<?php echo $detail['shipment_date'] ?>" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card product-detail mt-2">
                        <div class="card-header">Product No.<?php echo $detail['productID'] ?></div>
                        <div class="card-body">
                            <a href="../../../../products/<?php echo $detail['productID'] ?>" class="link-to-product-details">
                                <table class="product">
                                    <tr>
                                        <td rowspan="5" class="image_container">
                                            <img class="image-review" src="../../../../<?php echo $detail['image_url'] ?>" alt="<?php echo $detail['productName'] ?>" width="auto" height="200px">
                                        </td>
                                        <td colspan="2" class="name-review review">
                                            <?php echo $detail['productName'] ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="price-review review">
                                                $ <?php echo $detail['oldprice'] ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td class="quantity-review review sm-detail">
                                                &times;<?php echo $detail['quantity'] ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td class="sub-total-review review">
                                                = $ <?php echo $detail['sub_order_amount'] ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="buy-again-review review">
                                            <span class="buy-again"><a href="../../../../products/<?php echo $detail['productID'] ?>" class="badge badge-info">Buy Again</a></span>
                                        </td>
                                    </tr>
                                </table>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card rate-review-product mt-2">
                        <div class="card-header">{{ __('Rate and Review') }}</div>
                            <div class="card-body">
                                @if (($review_status=='old')||($review_status=='new'))
                                    <div class="form-group row">
                                        <label for="rate-old" class="col-sm-3 col-form-label">Rating</label>
                                        <div class="col-sm-9">
                                            <input type="text" readonly class="form-control-plaintext" id="rate-old" name="rate-old" value="<?php echo $check_review['rating'] ?> / 5" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="review-old" class="col-sm-3 col-form-label">Review</label>
                                        <div class="col-sm-9">
                                            <input type="text" readonly class="form-control-plaintext" id="review-old" name="review-old" value="<?php echo $check_review['review'] ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="review-date-old" class="col-sm-3 col-form-label">Review Date</label>
                                        <div class="col-sm-9">
                                            <input type="text" readonly class="form-control-plaintext" id="review-date-old" name="review-date-old" value="<?php echo $check_review['review_date'] ?>" disabled>
                                        </div>
                                    </div>
                                @endif
                                @if ($review_status=='new')
                                    <div class="form-group row">
                                        <label for="rate-new" class="col-sm-3 col-form-label">Current Rating</label>
                                        <div class="col-sm-9">
                                            <input type="text" readonly class="form-control-plaintext" id="rate-new" name="rate-new" value="<?php echo $check_review['rating_new'] ?> / 5" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="review-new" class="col-sm-3 col-form-label">Current Review</label>
                                        <div class="col-sm-9">
                                            <input type="text" readonly class="form-control-plaintext" id="review-new" name="review-new" value="<?php echo $check_review['review_new'] ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="review-date-new" class="col-sm-3 col-form-label">Current Review Date</label>
                                        <div class="col-sm-9">
                                            <input type="text" readonly class="form-control-plaintext" id="review-date-new" name="review-date-new" value="<?php echo $check_review['review_date_new'] ?>" disabled>
                                        </div>
                                    </div>
                                @endif
                                @if ($review_status=='none')
                                    <form method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group row mb-1">
                                            <label for="rate-product" class="col-sm-3 col-form-label">Rating</label>
                                            <div class="col-sm-9">
                                                <input type="number" class="form-control @if (isset($error_rate)) is-invalid @endif" id="rate-product" name="rate-product" min="1" max="5" placeholder="Rate from 1 to 5 stars" value="<?php echo isset($rate) ? $rate : ''; ?>" autofocus>
                                                <?php
                                                    if (isset($error_rate)) {
                                                        ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                                            echo $error_rate;
                                                        ?> </strong></span> <?php
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label for="review-product" class="col-sm-3 col-form-label">Review</label>
                                            <div class="col-sm-9">
                                                <textarea type="text" class="form-control @if (isset($error_review)) is-invalid @endif" id="review-product" name="review-product" placeholder="Short review" value="<?php echo isset($review) ? $review : ''; ?>"></textarea>
                                                <?php
                                                    if (isset($error_review)) {
                                                        ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                                            echo $error_review;
                                                        ?> </strong></span> <?php
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-1 justify-content-end">
                                            <div class="col-md-auto justify-content-end" id="button-review-box">
                                                <button class="btn btn-primary button-review" type="submit" id="submit" name="review">Post</button>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                                @if ($review_status=='old')
                                    <form method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group row mb-1">
                                            <label for="rate-product" class="col-sm-3 col-form-label">Current Rating</label>
                                            <div class="col-sm-9">
                                                <input type="number" class="form-control @if (isset($error_rate)) is-invalid @endif" id="rate-product" name="rate-product" min="1" max="5" placeholder="Rate from 1 to 5 stars" value="<?php echo isset($rate) ? $rate : ''; ?>" autofocus>
                                                <?php
                                                    if (isset($error_rate)) {
                                                        ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                                            echo $error_rate;
                                                        ?> </strong></span> <?php
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label for="review-product" class="col-sm-3 col-form-label">Current Review</label>
                                            <div class="col-sm-9">
                                                <textarea type="text" class="form-control @if (isset($error_review)) is-invalid @endif" id="review-product" name="review-product" placeholder="Short review" value="<?php echo isset($review) ? $review : ''; ?>"></textarea>
                                                <?php
                                                    if (isset($error_review)) {
                                                        ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                                            echo $error_review;
                                                        ?> </strong></span> <?php
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-1 justify-content-end">
                                            <div class="col-md-auto justify-content-end" id="button-review-box">
                                                <button class="btn btn-primary button-review" type="submit" id="submit" name="review">Post</button>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
