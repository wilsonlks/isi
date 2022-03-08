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
    
    
        // if (isset($_POST['submit'])) {

        //     $update_status = $_POST['cancel_order'];
        //     $date = now();

        //         $update_status_query = "UPDATE `purchaseorder`
        //                                 SET `status`=\"".$update_status."\", `cancel_date`=\"".$date."\", `cancel_by`=\"customer\"
        //                                 WHERE `poID`=$orderID";

        //         $update_status_set = $dbConnection->prepare($update_status_query);
        //         $update_status_set->execute();
        //         $update_status_set->close();

        //         header("location:http://localhost:8000/orders/".$orderID); exit;

        // }
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
        #button-box {
            float: right;
            display: inline-block;
        }
        .order_detail .card-body {
            padding: 0px 16px 0px;
        }
        /* .date_order, .customer_order, .addr_order, .total_order, .status_order {
            margin-bottom: 0px;
            font-size: 0.85rem;
        } */
        .order {
            width: 100%;
            display: table;
            clear: both;
        }
        .order:not(:last-child) {
            border-bottom: 2px solid darkgreen;
        }
        .name_order, .price_order, .quantity_order, .sub_total_order {
            text-align: right;
        }
        .status_order, #cancel_by_order {
            text-transform: uppercase;
        }
        .image_order {
            width: 150px;
            height: 150px;
            object-fit: scale-down;
            display: inline-block;
            float: left;
            clear: both;
        }
        a {
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
                                            @if (($detail['status'] == 'pending') || ($detail['status'] == 'hold'))
                                                <div class="input-group-append" id="button-box">
                                                    <!-- <input type="hidden" class="cancel_order" name="cancel_order" value="cancelled"> -->
                                                    <button class="btn btn-primary button-cancel" type="submit" id="submit" name="cancel">Cancel Order</button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($detail['status'] == 'shipped')
                                        <div class="form-group row">
                                            <label for="shipment_order" class="col-sm-3 col-form-label">Shipment Date</label>
                                            <div class="col-sm-9">
                                                <input type="text" readonly class="form-control-plaintext" id="shipment_order" name="shipment_order" value="<?php echo $detail['shipment_date'] ?>" disabled>
                                            </div>
                                        </div>
                                    @elseif ($detail['status'] == 'cancelled')
                                        <div class="form-group row">
                                            <label for="cancel_order" class="col-sm-3 col-form-label">Cancel Date</label>
                                            <div class="col-sm-9">
                                                <input type="text" readonly class="form-control-plaintext" id="cancel_order" name="cancel_order" value="<?php echo $detail['cancel_date'] ?>" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group row">
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
                    <div class="card order_detail mt-2">
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
                                                $ <?php echo $detail['oldprice'] ?>
                                            </a>
                                        </h5>
                                        <h6 class="quantity_order">
                                            <a href="../products/<?php echo $detail['productID'] ?>" class="link-to-product-details">
                                                &times;<?php echo $detail['quantity'] ?>
                                            </a>
                                        </h6>
                                        <h5 class="sub_total_order">
                                            <a href="../products/<?php echo $detail['productID'] ?>" class="link-to-product-details">
                                                Sub order amount: $ <?php echo ($detail['sub_order_amount']) ?>
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
