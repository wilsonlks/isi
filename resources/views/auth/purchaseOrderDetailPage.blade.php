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
                            AND `productimage`.`image_number`=1";
            $order_set = $dbConnection->prepare($order_query);
            $order_set->execute();
            $order_result1 = $order_set->get_result();
            $order_set->execute();
            $order_result2 = $order_set->get_result();

        } else {

            // return redirect('orders')->with('alert', 'Sorry, You Are Not Allowed to Access This Page.');
            header("location:http://localhost:8000/orders"); exit;
        }


        if (isset($_POST['cancel'])) {

            $message = 'Do you really want to cancel this order?';

        }

        if (isset($_POST['keep-confirm'])) {

            $message = '';

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

    ?>

    <style type="text/css">
        .card {
            width: 100%;
            margin: auto;
        }
        .card-body {
            padding: 16px;
        }
        .status-box {
            /* display: table; */
            clear: both;
        }
        .status_order {
            float: left;
            display: inline-block;
            width: 50%;
        }
        #button-cancel-box {
            float: right;
            display: inline-block;
        }
        .order-detail .card-body {
            padding: 0px 16px 0px;
        }
        .order-product {
            width: 100%;
            display: table;
        }
        .order-product-box:not(:last-child) {
            border-bottom: 2px solid darkgreen;
        }
        .status_order, #cancel_by_order {
            text-transform: uppercase;
        }
        .image-order {
            width: 150px;
            height: 150px;
            object-fit: scale-down;
            display: inline-block;
            float: left;
            clear: both;
        }
        .name-order {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 1.5rem;
            color: green;
            text-align: right;
        }
        .price-order, .quantity-order, .sub-total-order, .rate-review-order, .buy-again-order {
            font-size: 1rem;
            text-align: right;
        }
        .sub-total-order {
            border-top: 1px solid black;
        }
        .order-product-box {
            padding-top: 7px;
            padding-bottom: 7px;
        }
        .sm-detail {
            width: 100px;
        }
        .link-to-product-details:link, .link-to-product-details:hover, .link-to-product-details:active, .link-to-product-details:visited {
            text-decoration: none;
            color: black;
        }
        .alert {
            margin: 0px;
            padding-right: 16px;
        }
        .alert button {
            background: none;
            border: 0px;
            font-weight: bold;
            float: right;
        }
        .cancel-order:hover {
            color: blue;
        }
        .keep-order:hover {
            color: grey;
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
                                <form method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group row mb-1">
                                        <label for="date_order" class="col-sm-3 col-form-label">Purchase Date</label>
                                        <div class="col-sm-9">
                                            <input type="text" readonly class="form-control-plaintext" id="date_order" name="date_order" value="<?php echo $detail['purchase_date'] ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="customer_order" class="col-sm-3 col-form-label">Customer Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" readonly class="form-control-plaintext" id="customer_order" name="customer_order" value="<?php echo $detail['name'] ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="addr_order" class="col-sm-3 col-form-label">Shipping Address</label>
                                        <div class="col-sm-9">
                                            <input type="text" readonly class="form-control-plaintext" id="addr_order" name="addr_order" value="<?php echo $detail['shipping_addr'] ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1">
                                        <label for="total_order" class="col-sm-3 col-form-label">Total Order Amounts</label>
                                        <div class="col-sm-9">
                                            <input type="text" readonly class="form-control-plaintext" id="total_order" name="total_order" value="$ <?php echo $detail['total_order_amount'] ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-1 status-box">
                                        <label for="status_order" class="col-sm-3 col-form-label">Status</label>
                                        <div class="col-sm-9">
                                            <input type="text" readonly class="form-control-plaintext status_order" id="status" name="status_order" value="<?php echo $detail['status'] ?>" disabled>
                                            @if (($detail['status'] == 'pending') || ($detail['status'] == 'hold'))
                                                <div class="input-group-append" id="button-cancel-box">
                                                    <button class="btn btn-primary button-cancel" type="submit" id="submit" name="cancel">Cancel Order</button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($detail['status'] == 'shipped')
                                        <div class="form-group row mb-1 shipped-box">
                                            <label for="shipment_order" class="col-sm-3 col-form-label">Shipment Date</label>
                                            <div class="col-sm-9">
                                                <input type="text" readonly class="form-control-plaintext shipment_order" id="shipment" name="shipment_order" value="<?php echo $detail['shipment_date'] ?>" disabled>
                                            </div>
                                        </div>
                                    @elseif ($detail['status'] == 'cancelled')
                                        <div class="form-group row mb-1">
                                            <label for="cancel_order" class="col-sm-3 col-form-label">Cancel Date</label>
                                            <div class="col-sm-9">
                                                <input type="text" readonly class="form-control-plaintext" id="cancel_order" name="cancel_order" value="<?php echo $detail['cancel_date'] ?>" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-1">
                                            <label for="cancel_by_order" class="col-sm-3 col-form-label">Cancel By</label>
                                            <div class="col-sm-9">
                                                <input type="text" readonly class="form-control-plaintext" id="cancel_by_order" name="cancel_by_order" value="<?php echo $detail['cancel_by'] ?>" disabled>
                                            </div>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @if (isset($message))
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card mt-2">
                                <form method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="alert alert-warning" role="alert">
                                        <strong class="alert_detail text">{{ $message }}</strong>
                                        <button type="submit" name="cancel-confirm" class="close cancel-order">
                                            <span>Cancel</span>
                                        </button>
                                        <button type="submit" class="keep-order" name="keep-order">
                                            <span>Keep</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card order-detail mt-2">
                        <div class="card-header">{{ __('Order Details') }}</div>
                        <div class="card-body">
                            <?php
                                while ($detail= mysqli_fetch_array($order_result2)) { ?>
                                    <div class="order-product-box">
                                        <a href="../products/<?php echo $detail['productID'] ?>" class="link-to-product-details">
                                            <table class="order-product">
                                                <tr>
                                                    <td rowspan="5" class="image_container">
                                                        <img class="image-order" src="../<?php echo $detail['image_url'] ?>" alt="<?php echo $detail['productName'] ?>" width="auto" height="200px">
                                                    </td>
                                                    <td colspan="2" class="name-order order">
                                                        <?php echo $detail['productName'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="price-order order">
                                                            $ <?php echo $detail['oldprice'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td class="quantity-order order sm-detail">
                                                            &times;<?php echo $detail['quantity'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td class="sub-total-order order">
                                                            = $ <?php echo $detail['sub_order_amount'] ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    @if ($detail['status'] == 'shipped')
                                                        <td colspan="2" class="rate-review-order order">
                                                            <span class="rate-review"><a href="<?php echo $detail['poID'] ?>/products/<?php echo $detail['productID'] ?>/reviews" class="badge badge-success">Rate and Review</a></span>
                                                        </td>
                                                    @else
                                                        <td colspan="2" class="buy-again-order order">
                                                            <span class="buy-again badge badge-info">Buy Again</span>
                                                        </td>
                                                    @endif
                                                </tr>
                                            </table>
                                        </a>
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
