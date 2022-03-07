@extends('layouts.app')

@section('content')


    <style type="text/css">
        .id_order, .date_order, .customer_name_order, .total_order, .status_order {
            margin-bottom: 0px;
            font-size: 0.85rem;
        }
        .purchase_order_list .card-body {
            padding: 0px 16px 0px;
        }
        .order {
            margin: 0px;
            padding: 0px;
            font-size: 0.85rem;
        }
        .order:not(:last-child) {
            border-bottom: 2px solid darkgreen;
        }
        .status_order::first-letter {
            text-transform: uppercase;
        }
        a {
            text-decoration: none;
            color: black;
        }
        .list-group-item {
            border: 0px;
        }
        .checkbox {
            padding: 0px 25px 0px 0px;
            display: inline;
        }
        .inputtext {
            padding: 0px;
            display: inline;
        }
        #search_box {
            width: 100%;
            padding: 1px 5px 1px;
        }
    </style>

    <!-- vendor order list page -->

    <div class="content">

        <!-- print data -->

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="nav nav-tabs card-header-tabs" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-status-tab" data-toggle="tab" href="#nav-status" role="tab" aria-controls="nav-status" aria-selected="true">Status</a>
                                <a class="nav-item nav-link" id="nav-search-tab" data-toggle="tab" href="#nav-search" role="tab" aria-controls="nav-search" aria-selected="false">Search</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane show active" id="nav-status" role="tabpanel" aria-labelledby="nav-status-tab">
                                    <div class="list-group-item checkbox">
                                        <label><input type="checkbox" class="status_checkbox" onchange="Click()" value="pending_orders"> Pending orders</label>
                                    </div>
                                    <div class="list-group-item checkbox">
                                        <label><input type="checkbox" class="status_checkbox" onchange="Click()" value="orders_on_hold"> Orders on hold</label>
                                    </div>
                                    <div class="list-group-item checkbox">
                                        <label><input type="checkbox" class="status_checkbox" onchange="Click()" value="past_orders"> Past orders</label>
                                    </div>
                                </div>
                                <div class="tab-pane" id="nav-search" role="tabpanel" aria-labelledby="nav-search-tab">
                                    <div class="list-group-item inputtext">
                                        <input type="text" id="search_box" placeholder="Search by purchase order number" onchange="Click()">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- print data -->
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card purchase_order_list mt-2">
                        <div class="card-header">{{ __('Purchase Orders') }}</div>
                        <div class="card-body">
                            <p id="fetch_data"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
                send_data();
            });


                // send checkbox value by using ajax
                function send_data(){

                    $('.filter_data').html('<div id="loading" style="" ></div>');
                    var action = 'fetch_data';
                    var status_filter = get_filter('status_checkbox');
                    var searchText = get_searchText();


                    $.ajax({
                        url:"../php_file/fetch_data_purchaseOrderListPage.php",
                        method:"POST",
                        data:{action:action, status_filter:status_filter, searchText:searchText},
                        success:function(data){
                            $('#fetch_data').html(data);
                        }
                    });
                    console.log("fetched");
                }

                function get_filter(class_name)
                {
                    var filter = [];

                    $('.'+class_name+':checked').each(function(){
                        if ($(this).val() == 'pending_orders'){
                            filter.push('"pending"');
                        }
                        if ($(this).val() == 'orders_on_hold'){
                            filter.push('"hold"');
                        }
                        if ($(this).val() == 'past_orders'){
                            filter.push('"shipped"');
                            filter.push('"cancelled"');
                        }
                    });
                    return filter;
                }

                function get_searchText(){
                    return $('#search_box').val();
                }
                function Click(){
                    send_data();
                    console.log("click");

                };

        </script>

@endsection
