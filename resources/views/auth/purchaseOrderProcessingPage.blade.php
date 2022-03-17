@extends('layouts.app')

@section('content')

    <?php

        include("./php_file/dbConnect.php");

        // get specific purchase order from DB

        $url=url()->full();
        $split_url=preg_split("#/#", $url);
        $orderID=$split_url[count($split_url)-1];

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
        $order_set->execute();
        $order_result3 = $order_set->get_result();
        $order_set->execute();
        $order_result4 = $order_set->get_result();

        $out_of_stock = FALSE;
        while ($detail=mysqli_fetch_array($order_result3)) {
            if ($detail['quantity'] > $detail['stock']) {
                $out_of_stock = TRUE;
            }
        }

        if (isset($_POST['submit'])) {

            $update_status = isset($_POST['status_order']) ? $_POST['status_order'] : '';
            $date = now();
            if (empty($update_status)) {
                $error = 'Please select an available status.';
            }

            if (!isset($error)) {

                if ($update_status == 'hold') {

                    $update_status_query = "UPDATE `purchaseorder`
                                            SET `status`=\"".$update_status."\"
                                            WHERE `poID`=$orderID";

                } elseif ($update_status == 'shipped') {

                    $update_status_query = "UPDATE `purchaseorder`
                                            SET `status`=\"".$update_status."\", `shipment_date`=\"".$date."\"
                                            WHERE `poID`=$orderID";

                    while ($detail=mysqli_fetch_array($order_result4)) {

                        $productID = $detail['productID'];
                        $update_stock = $detail['stock'] - $detail['quantity'];
                        $update_stock_query = "UPDATE `product`
                                                SET `stock`=$update_stock
                                                WHERE `productID`=$productID";
                        $update_stock_set = $dbConnection->prepare($update_stock_query);
                        $update_stock_set->execute();
                        $update_stock_set->close();

                    }

                } elseif ($update_status == 'cancelled') {

                    $update_status_query = "UPDATE `purchaseorder`
                                            SET `status`=\"".$update_status."\", `cancel_date`=\"".$date."\", `cancel_by`=\"vendor\"
                                            WHERE `poID`=$orderID";

                }

                $update_status_set = $dbConnection->prepare($update_status_query);
                $update_status_set->execute();
                $update_status_set->close();

                header("location:http://localhost:8000/orders/".$orderID); exit;

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
        #status {
            border-top-left-radius: 0.2rem;
            border-bottom-left-radius: 0.2rem;
            border-top-right-radius: 0rem;
            border-bottom-right-radius: 0rem;
        }
        .button-process {
            border-top-left-radius: 0rem;
            border-bottom-left-radius: 0rem;
            border-top-right-radius: 0.2rem;
            border-bottom-right-radius: 0.2rem;
        }
        .status-select {
            padding: 0px;
        }
        .input-group-append {
            padding: 0px;
        }
        #button-box {
            margin: 0px;
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
        .price-order, .quantity-order, .sub-total-order, .stock-order {
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
            width: 110px;
        }
        .link-to-product-details:link, .link-to-product-details:hover, .link-to-product-details:active, .link-to-product-details:visited {
            text-decoration: none;
            color: black;
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
            margin: 0px;
        }
    </style>

    <!-- vendor order detail page -->

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
                                    <div class="form-group row mb-1">
                                        <label for="status_order" class="col-sm-3 col-form-label">Status</label>
                                        <div class="col-sm-7 status-select">
                                        <select class="form-select @if (isset($error)) is-invalid @endif" id="status" name="status_order">
                                            <option value="" disabled>Status change</option>
                                            @if ($detail['status'] == 'pending')
                                                <option value="pending" selected disabled>Pending</option>
                                                <option value="hold">Hold</option>
                                                <option value="shipped" {{ ($out_of_stock==TRUE) ? "disabled" : "" }}>Shipped</option>
                                                <option value="cancelled">Cancelled</option>
                                            @elseif ($detail['status'] == 'hold')
                                                <option value="hold" selected disabled>Hold</option>
                                                <option value="shipped" {{ ($out_of_stock==TRUE) ? "disabled" : "" }}>Shipped</option>
                                                <option value="cancelled">Cancelled</option>
                                            @elseif ($detail['status'] == 'shipped')
                                                <option value="shipped" selected disabled>Shipped</option>
                                            @else
                                                <option value="cancelled" selected disabled>Cancelled</option>
                                            @endif
                                        </select>
                                        <?php
                                            if (isset($error)) {
                                                ?>
                                                <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                                echo $error;
                                                ?> </strong></span> <?php
                                            }
                                        ?>
                                        </div>
                                        <div class="input-group-append col-md-auto" id="button-box">
                                            <button class="btn btn-primary button-process" type="submit" id="submit" name="submit">Process</button>
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
                                                    <td colspan="2" class="stock-order order">
                                                        @if (((($detail['status'] == 'pending') || ($detail['status'] == 'hold')) && ($detail['quantity'] > $detail['stock'])) || ($detail['stock'] == 0))
                                                            <span class="out-of-stock"><a href="../products/<?php echo $detail['productID'] ?>/edit" class="badge badge-warning">Out-of-stock</a></span>
                                                        @elseif (($detail['stock'] <= 10))
                                                            <span class="few-items-left"><a href="../products/<?php echo $detail['productID'] ?>/edit" class="badge badge-info">Few items left</a></span>
                                                        @else
                                                            <span class="in-stock"><a href="../products/<?php echo $detail['productID'] ?>/edit" class="badge badge-success">In-stock</a></span>
                                                        @endif
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
