@extends('layouts.app')

@section('content')


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

    <!-- customer order list page -->

    <div class="content">

        <!-- print data -->

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <!-- filter for state -->
                    <div class="card">

                        <div class="card-header">Status</div>

                            <div class="card-bod list-group-item checkbox">
                                <label><input type="checkbox" class="status_checkbox" onchange="Click()" value="current_purchases"  > Current purchases</label>
                                <label><input type="checkbox" class="status_checkbox" onchange="Click()" value="past_purchases"  > Past purchases</label>
                            </div>

                        </div>
                    </div>
                </div>

            <!-- print data -->
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{ __('My Purchase Orders') }}</div>
                        <div class="card-body">
                            <p id="fetch_data"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button id="userid" style="display:none" value="{{ Auth::id() }}"></button>
<script>
        $(document).ready(function(){
                send_data();
            });


                // send checkbox value by using ajax
                function send_data(){

                    $('.filter_data').html('<div id="loading" style="" ></div>');
                    var action = 'fetch_data';
                    var status_filter = get_filter('status_checkbox');
                    var userid = get_userid();

                    $.ajax({
                        url:"../php_file/fetch_data_purchaseTrackingPage.php",
                        method:"POST",
                        data:{action:action, status_filter:status_filter, userid:userid},
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
                        if ($(this).val() == 'current_purchases'){
                            filter.push('"pending"');
                            filter.push('"hold"');
                        }
                        if ($(this).val() == 'past_purchases'){
                            filter.push('"shipped"');
                            filter.push('"cancelled"');
                        }
                    });
                    return filter;
                }
                function get_userid(){
                    var userid = $('#userid').val();
                    return userid;
                }
                function Click(){
                    send_data();
                    console.log("click");

                };

        </script>



@endsection
