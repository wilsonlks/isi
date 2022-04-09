@extends('layouts.app')

@section('content')

    <?php

        include("./php_file/dbConnect.php");

    ?>

<style type="text/css">

        /* .best-products {
            margin: 0px;
            padding: 0px;
            font-size: 0.85rem;
        } */
        .list-group-item {
            border: 0px;
        }
        .checkbox {
            padding: 0px 25px 0px 0px;
            display: inline;
        }
        .inputdate {
            padding: 0px;
            display: inline;
        }
        #fromDate {
            width: 100%;
            padding: 1px 5px 1px;
        }
        #toDate {
            width: 100%;
            padding: 1px 5px 1px;
        }
        .card {
            width: 100%;
            margin: auto;
        }
        .card-body {
            padding: 16px;
        }
        .best-products-detail .card-body {
            padding: 0px 16px 0px;
        }
        .best-products {
            width: 100%;
            margin: auto;
            padding: 15px 0px 15px;
        }
        .best-products-box:not(:last-child) {
            border-bottom: 2px solid darkgreen;
        }
        .best-products-box {
            padding-top: 7px;
            padding-bottom: 7px;
        }
        .name-best {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 1.5rem;
            color: green;
            text-align: right;
        }
        .total-quantity-best, .total-amount-best, .stock-best, .detail-best {
            font-size: 1rem;
            text-align: right;
        }
        .product-id-best, .category-best, .rating-best {
            padding-left: 10px;
        }
        .product-id-best, .category-best {
            padding-right: 10px;
        }
        .image-best {
            width: 150px;
            height: 150px;
            object-fit: scale-down;
            display: inline-block;
            float: left;
            clear: both;
        }
        .sm-detail {
            width: 177px;
        }
        a:link, a:hover, a:active, a:visited {
            text-decoration: none;
            color: black;
        }
        .inputdate {
            padding-left: 16px;
            padding-right: 16px;
        }
        #fetch_data {
            margin: 0px;
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
        .low_rating_productList {
            color: red;
        }
        .mid_rating_productList {
            color: orange;
        }
        .high_rating_productList {
            color: limegreen;
        }
        .no_product {
            padding-top: 16px;
            padding-bottom: 16px;
        }
    </style>

    <div class="content">

        <!-- print data -->

        <div class="container">

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <!-- get category from DB -->
                    <?php
                        $categoryQ = "SELECT * FROM category ORDER BY categoryName";
                        $CSet = $dbConnection->prepare($categoryQ);
                        $CSet->execute();
                        $CSetResult = $CSet->get_result();
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <div class="nav nav-tabs card-header-tabs" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-date-tab" data-toggle="tab" href="#nav-date" role="tab" aria-controls="nav-date" aria-selected="true">Date</a>
                                <a class="nav-item nav-link" id="nav-filter-tab" data-toggle="tab" href="#nav-filter" role="tab" aria-controls="nav-filter" aria-selected="false">Filter</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="nav-tabContent">


                                <div class="tab-pane show active" id="nav-date" role="tabpanel" aria-labelledby="nav-date-tab">
                                    <div class="row">
                                        <div class="list-group-item inputdate col-md-6">
                                            From <input type="date" id="fromDate" value="" onchange="Click()">
                                        </div>
                                        <div class="list-group-item inputdate col-md-6">
                                            To <input type="date" id="toDate" value="" onchange="Click()">
                                        </div>
                                    </div>
                                </div>


                                <div class="tab-pane" id="nav-filter" role="tabpanel" aria-labelledby="nav-filter-tab">
                                    <?php while ($category = mysqli_fetch_assoc($CSetResult)){ ?>
                                        <div class="list-group-item checkbox">
                                            <label><input type="checkbox" class="category_checkbox" onchange="Click()" value="'<?php echo $category['categoryID']; ?>'"> <?php echo $category['categoryName']; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- print data -->


            <p id="fetch_data"></p>


    <script>
        $(document).ready(function(){
            set_default_date();
            //get_date('#fromDate');

            send_data();

        });


                // send checkbox value by using ajax



                function send_data(){
                    $('.filter_data').html('<div id="loading" style="" ></div>');
                    var action = 'fetch_data';
                    var dateText = get_dateText();
                    var category_filter = get_filter('category_checkbox');
                    var from_date = get_date('fromDate');
                    var to_date = get_date('toDate');

                    $.ajax({
                        url:"../php_file/fetch_data_bestSellingProducts.php",
                        method:"POST",
                        data:{action:action, dateText:dateText, category_filter:category_filter, from_date:from_date, to_date:to_date},
                        success:function(data){
                            $('#fetch_data').html(data);
                        }
                    });
                }

                function get_dateText(){
                    return $('#fromDate').val();
                }

                function get_filter(class_name)
                {
                    var filter = [];
                    $('.'+class_name+':checked').each(function(){
                        filter.push($(this).val());
                    });
                    return filter;
                }

                function Click(){
                    send_data();
                    console.log("click");

                }

                function get_date(inputid){
                    console.log($('#'+inputid).val());
                    return $('#'+inputid).val();
                }

                function changeDateFormat(date){
                    var y = date.getFullYear();
                    var m = date.getMonth()+1;
                    if (m<10){
                        m = '0' + m;
                    }
                    var d = date.getDate();
                    if (d<10){
                        d = '0' + d;
                    }
                    var format = y + '-' + m + '-' + d;
                    return format;
                }

                function getLastdate(date, days){
                    var last = new Date(date.getTime() - (days * 24 * 60 * 60 * 1000));
                    return last;
                }

                function set_default_date(){
                    var lastDate = getLastdate(new Date(), 30);
                    $('#fromDate').val(changeDateFormat(lastDate));
                    var today = changeDateFormat(new Date());
                    $('#toDate').val(today);
                    document.getElementById('toDate').max = today;
                    document.getElementById('fromDate').max = today;
                }

        </script>
@endsection
