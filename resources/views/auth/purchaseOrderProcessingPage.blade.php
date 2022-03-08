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
                        WHERE `purchaseorder`.`poID`=$orderID";
        $order_set = $dbConnection->prepare($order_query);
        $order_set->execute();
        $order_result1 = $order_set->get_result();
        $order_set->execute();
        $order_result2 = $order_set->get_result();

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
        .order_detail .card-body {
            padding: 0px 16px 0px;
        }
        /* .date_order, .customer_order, .addr_order, .total_order, .status_order {
            width: auto;
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
                                    <div class="input-group">
                                        <label for="status_order" class="col-md-3 col-form-label">Status</label>
                                        <div class="col-md-7 status-select">
                                        <select class="form-select @if (isset($error_name)) is-invalid @endif" id="status" name="status_order">
                                            <option value="" disabled>Status change</option>
                                            @if ($detail['status'] == 'pending')
                                                <option value="pending" selected disabled>Pending</option>
                                                <option value="hold">Hold</option>
                                                <option value="shipped">Shipped</option>
                                                <option value="cancelled">Cancelled</option>
                                            @elseif ($detail['status'] == 'hold')
                                                <option value="hold" selected disabled>Hold</option>
                                                <option value="shipped">Shipped</option>
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
