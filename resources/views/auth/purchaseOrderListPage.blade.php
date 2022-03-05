@extends('layouts.app')

@section('content')

    <?php

        include("./php_file/dbConnect.php");

        // get purchase orders of a specific customer from DB

        $order_query = "SELECT * FROM
                        (`purchaseorder` INNER JOIN `users`
                        ON purchaseorder.customerID=users.id)";
        $order_set = $dbConnection->prepare($order_query);
        $order_set->execute();
        $order_result = $order_set->get_result();

    ?>

    <style type="text/css">
        .id_order, .date_order, .total_order, .status_order {
        }
        .order {
            border-bottom: 2px solid darkgreen;
            margin: 10px;
        }
        .status_order::first-letter {
            text-transform: uppercase;
        }
        a {
            text-decoration: none;
            color: black;
        }
    </style>

    <!-- vendor order list page -->

    <div class="content">


        <!-- print data -->

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{ __('Purchase Orders') }}</div>
                        <div class="card-body">
                            <?php
                                $data_count = 0;
                                while ($detail= mysqli_fetch_array($order_result)) { ?>
                                    <?php $data_count++; ?>
                                    <div class="order">
                                        <a href="orders/<?php echo $detail['poID'] ?>">
                                            <div class="order-header"><p class="mb-2 id_order">Purchase Order No.<?php echo $detail['poID'] ?></p></div>
                                            <div class="order-body">
                                                <p class="mb-2 date_order"><?php echo $detail['purchase_date'] ?></p>
                                                <p class="mb-2 total_order">$ <?php echo $detail['total_order_amount'] ?></p>
                                                <p class="mb-2 status_order"><?php echo $detail['status'] ?></p>
                                            </div>
                                        </a>
                                    </div>
                                <?php };
                                if ($data_count == 0) { ?>
                                    <div>No order.</div>
                                <?php };
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
