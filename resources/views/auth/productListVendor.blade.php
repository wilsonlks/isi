@extends('layouts.app')

@section('content')

    <?php
        include("./php_file/dbConnect.php");
    ?>

<head>
    <style type="text/css">
        .productList{
            padding: auto;
            flex-wrap: wrap;
        }
        .productList .card-body {
            padding: 0px 16px 0px;
        }
        .product {
            display: table;
            clear: both;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
            text-align: center;
        }
        .product:not(:last-child) {
            border-bottom: 2px solid darkgreen;
        }
        .product img {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 200px;
            height: 200px;
            object-fit: scale-down;
        }
        .image_productList {
            padding: 10px 0px 10px;
        }
        .id_productList, .name_productList ,.category_productList ,.price_productList{
            padding: 10px;
        }
        .id_productList, .name_productList ,.category_productList{
            display: inline;
        }
        .name_productList {
            font-weight: bold;
        }
        .list-group-item {
            border: 0px;
        }
        .sorting, .sorting_button, .checkbox {
            padding: 0px 25px 0px 0px;
            display: inline;
        }
        .inputtext {
            padding: 0px;
            display: inline;
        }
        .AscDesc {
            float: right;
            display: inline;
            background: none;
            border: none;
            margin: 0px;
            padding: 0px;
        }
        #search_box {
            width: 100%;
            padding: 1px 5px 1px;
        }
        .no_product {
            /* padding-top: 16px; */
        }
        .page_productList {
            display: flex;
            padding-left: 0;
        }
        .page {
            position: relative;
            display: block;
            color: #0d6efd;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 0.35rem 0.5rem 0.25rem 0.5rem;
            font-size: 0.7875rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .page:hover {
            z-index: 2;
            color: #0a58ca;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        .page:focus {
            z-index: 3;
            color: #0a58ca;
            background-color: #e9ecef;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .page:not(:first-child) {
            margin-left: -1px;
        }
        .page:first-child {
            border-top-left-radius: 0.2rem;
            border-bottom-left-radius: 0.2rem;
        }
        .page:last-child {
            border-top-right-radius: 0.2rem;
            border-bottom-right-radius: 0.2rem;
        }
        .page_active, .page_active:hover {
            z-index: 3;
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .page_disabled {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }
    </style>
</head>

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
                                <a class="nav-item nav-link active" id="nav-sort-tab" data-toggle="tab" href="#nav-sort" role="tab" aria-controls="nav-sort" aria-selected="true">Sort</a>
                                <a class="nav-item nav-link" id="nav-search-tab" data-toggle="tab" href="#nav-search" role="tab" aria-controls="nav-search" aria-selected="false">Search</a>
                                <a class="nav-item nav-link" id="nav-filter-tab" data-toggle="tab" href="#nav-filter" role="tab" aria-controls="nav-filter" aria-selected="false">Filter</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane show active" id="nav-sort" role="tabpanel" aria-labelledby="nav-sort-tab">
                                    <form class="sorting">
                                        <div class="list-group-item sorting_button">
                                            <label><input type="radio" class="sorting_radio" name="sorting_radio" onclick="Click()" value="product.productID" checked> Product ID </label>
                                        </div>
                                        <div class="list-group-item sorting_button">
                                            <label><input type="radio" class="sorting_radio" name="sorting_radio" onclick="Click()" value="productName"> Product Name </label>
                                        </div>
                                        <div class="list-group-item sorting_button">
                                            <label><input type="radio" class="sorting_radio" name="sorting_radio" onclick="Click()" value="categoryName"> Category Name </label>
                                        </div>
                                        <div class="list-group-item sorting_button">
                                            <label><input type="radio" class="sorting_radio" name="sorting_radio" onclick="Click()" value="price"> Price </label>
                                        </div>
                                    </form>
                                    <button onclick = "changeSorting('1')" class="AscDesc" id="asc" value="Asc" style="display:block">ASC&#9650;</button>
                                    <button onclick = "changeSorting('-1')" class="AscDesc" id="desc" value="Desc" style="display:none">DESC&#9660;</button>
                                </div>
                                <div class="tab-pane" id="nav-search" role="tabpanel" aria-labelledby="nav-search-tab">
                                    <div class="list-group-item inputtext">
                                        <input type="text" id="search_box" placeholder="Search by product ID or name" onchange="Click()">
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
            </div>
        <div>

        <button id="current_Page" style="display:none" value="1"></button>

    <!-- ujax will get response from fetch_data_vendor.php and set html code to here -->
    <p id="filter_string"></p>

    <script>


        $(document).ready(function(){
            send_data();
        });

            // send checkbox value by using ajax
            function send_data(){

                $('.filter_data').html('<div id="loading" style="" ></div>');
                var action = 'fetch_data_vendor';
                var category_filter = get_filter('category_checkbox');
                var sorting = get_sorting('sorting_radio');
                var page = get_page();
                var AscDesc = get_AscDesc();
                var searchText = get_searchText();
                $.ajax({
                    url:"./php_file/fetch_data_vendor.php",
                    method:"POST",
                    data:{action:action, category_filter:category_filter, sorting:sorting, page:page, AscDesc:AscDesc, searchText:searchText},
                    success:function(data){
                        $('#filter_string').html(data);
                    }
                });
                console.log("fetched");
            }

            function get_searchText(){
                return $('#search_box').val();
            }
            function get_sorting(class_name){
                var sorting_value = $('.'+class_name+':checked').val();

                return sorting_value;
            }
            function get_page(){
                current_page = $('#current_Page').val();
                console.log('current_page='+ current_page);
                return current_page;
            }

            function get_AscDesc(){
                if (document.getElementById('asc').style.display == "block"){
                    console.log("change 1");
                    return '1';
                }

                if (document.getElementById('desc').style.display == "block"){
                    console.log("change -1");
                    return '-1';
                }
            }

            function changeSorting(val){
                if (val == '1'){
                    document.getElementById('asc').style.display = "none";
                    document.getElementById('desc').style.display = "block";
                }

                if (val == '-1'){
                    document.getElementById('asc').style.display = "block";
                    document.getElementById('desc').style.display = "none";
                }

                Click();
            }

            function set_page(id){
                document.getElementById('current_Page').value = id;
                console.log("run set page");
                send_data();
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
                set_page(1);
                send_data();
                console.log("click");

            };

    </script>

@endsection
