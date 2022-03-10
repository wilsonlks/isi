@extends('layouts.app')

@section('content')

    <?php

        include("./php_file/dbConnect.php");

        // $categoryQ = "SELECT * FROM category ORDER BY categoryName";
        // $CSet = $dbConnection->prepare($categoryQ);
        // $CSet->execute();
        // $CSetResult = $CSet->get_result();
    ?>

<style type="text/css">
        /* .id_best_products, .date_best_products, .customer_name_best_products, .total_best_products, .status_best_products {
            margin-bottom: 0px;
            font-size: 0.85rem;
        } */
        .purchase_best_products_list .card-body {
            padding: 0px 16px 0px;
        }
        .best_products {
            margin: 0px;
            padding: 0px;
            font-size: 0.85rem;
        }
        .best_products:not(:last-child) {
            bbest_products-bottom: 2px solid darkgreen;
        }
        /* .status_best_products::first-letter {
            text-transform: uppercase;
        } */
        a {
            text-decoration: none;
            color: black;
        }
        .list-group-item {
            bbest_products: 0px;
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
        /* .status-box {
            /* display: table; */
            /* clear: both; */
        /* } */ */
        /* .status_best_products {
            float: left;
            display: inline-block;
            width: 50%;
        } */
        #button-box {
            float: right;
            display: inline-block;
        }
        .best_products_detail .card-body {
            padding: 0px 16px 0px;
        }
        /* .date_best_products, .customer_best_products, .addr_best_products, .total_best_products, .status_best_products {
            margin-bottom: 0px;
            font-size: 0.85rem;
        } */
        .best_products {
            width: 100%;
            display: table;
            clear: both;
        }
        .best_products:not(:last-child) {
            bbest_products-bottom: 2px solid darkgreen;
        }
        .name_best_products, .total_quantity_best_products, .total_amount_best_products {
            text-align: right;
        }
        .status_best_products, #cancel_by_best_products {
            text-transform: uppercase;
        }
        .image_best_products {
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

    </style>

    <div class="content">

        <!-- print data -->

        <div class="container">

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <!-- get category from DB -->
                    <?php
                        $categoryQ = "SELECT * FROM category";
                        $CSet = $dbConnection->prepare($categoryQ);
                        $CSet->execute();
                        $CSetResult = $CSet->get_result();
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <div class="nav nav-tabs card-header-tabs" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-date-tab" data-toggle="tab" href="#nav-date" role="tab" aria-controls="nav-date" aria-selected="true">date</a>
                                <a class="nav-item nav-link" id="nav-filter-tab" data-toggle="tab" href="#nav-filter" role="tab" aria-controls="nav-filter" aria-selected="false">Filter</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane active" id="nav-date" role="tabpanel" aria-labelledby="nav-date-tab">
                                    <div class="list-group-item inputdate">
                                        From <input type="date" id="fromDate" value="" onchange="Click()">
                                    </div>
                                    <div class="list-group-item inputdate">
                                        To <input type="date" id="toDate" value="" onchange="Click()">
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
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card purchase_best_products_list mt-2">

                            <p id="fetch_data"></p>

                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    console.log("fetched");
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
                }

        </script>
@endsection