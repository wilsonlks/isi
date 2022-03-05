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
        .product {
            display: table;
            clear: both;
            border-bottom: 2px solid darkgreen;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
            text-align: center;
        }

        .product img {
            padding: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            max-height: 200px;
            min-width: 0;
            min-height: 0;
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

    </style>
</head>

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

        <!-- sorting by price -->
        <div class="card">
            <!-- sorting by price -->
                        <div class="card-header">Sorting</div>
                        <div class="card-body list-group-item sorting_button">
            <form>
                <label><input type="radio" class="sorting_radio" name="sorting_radio" onclick="Click()" value="productName" checked> Product Name </label>
                <label><input type="radio" class="sorting_radio" name="sorting_radio" onclick="Click()" value="price"> Price </label>
            </form>

            <button onclick = "changeSorting('1')" class="AscDesc" id="asc" value="Asc" style="display:block">ASC</button>
            <button onclick = "changeSorting('-1')" class="AscDesc" id="desc" value="Desc" style="display:none">DESC</button>
        </div>


        <div class="card-header">Search</div>
            <div class="card-body inputtext">
                <input type="text" id="search_box" onchange="Click()">
            </div>
        </div>
        <!-- checkbox for filter -->
        <div class="card-header">Filter</div>
                            <?php while ($category = mysqli_fetch_assoc($CSetResult)){ ?>
                                <div class="card-bod list-group-item checkbox">
                                    <label><input type="checkbox" class="category_checkbox" onchange="Click()" value="'<?php echo $category['categoryID']; ?>'"  > <?php echo $category['categoryName']; ?></label>
                                </div>
                            <?php } ?>
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

        ;
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
