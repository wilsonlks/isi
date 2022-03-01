@extends('layouts.app')

@section('content')

    <?php
        include("./php_file/dbConnect.php");
    ?>

<head>
    <style type="text/css">
        /* .productList {
            margin: 20px;
            padding: 40px;
            background: #e0ffe1;
            text-align: center;
            height: 300px;
            weight:


        }
        .product {
            background: yellow;
        }*/
        .image_productList{
            /* margin: 20px;
            padding: 20px;
            background: red; */
            /* height: 300px; */

        }

    </style>
</head>

    <div class="filter">

        <!-- get category from DB -->
        <?php
            $categoryQ = "SELECT * FROM category";
            $CSet = $dbConnection->prepare($categoryQ);
            $CSet->execute();
            $CSetResult = $CSet->get_result();
        ?>

        <!-- sorting by price -->
        <h4>sorting</h4>
        <div class="list-group-item sorting_button">
            <form>
                <label><input type="radio" class="sorting_radio" name="sorting_radio" onclick="Click()" value="productName" checked> Product Name </label>
                <label><input type="radio" class="sorting_radio" name="sorting_radio" onclick="Click()" value="price"> Price </label>
            </form>

            <button onclick = "changeSorting('1')" class="AscDesc" id="asc" value="Asc" style="display:block">ASC</button>
            <button onclick = "changeSorting('-1')" class="AscDesc" id="desc" value="Desc" style="display:none">DESC</button>
        </div>

        <!-- checkbox for filter -->
        <h4>filter</h4>
        <?php while ($category = mysqli_fetch_assoc($CSetResult)){ ?>
            <div class="list-group-item checkbox">
                <label><input type="checkbox" class="category_checkbox" onchange="Click()" value="'<?php echo $category['categoryID']; ?>'"  > <?php echo $category['categoryName']; ?></label>
            </div>
        <?php } ?>
    </div>

    <button id="current_Page" style="display:none" value="1"></button>

    <!-- ujax will get response from fetch_data.php and set html code to here -->
    <p id="filter_string"></p>

    <script>


        $(document).ready(function(){
            send_data();
        });

        ;
            // send checkbox value by using ajax
            function send_data(){

                $('.filter_data').html('<div id="loading" style="" ></div>');
                var action = 'fetch_data';
                var category_filter = get_filter('category_checkbox');
                var sorting = get_sorting('sorting_radio');
                var page = get_page();
                var AscDesc = get_AscDesc();
                $.ajax({
                    url:"./php_file/fetch_data.php",
                    method:"POST",
                    data:{action:action, category_filter:category_filter, sorting:sorting, page:page, AscDesc:AscDesc},
                    success:function(data){
                        $('#filter_string').html(data);
                    }
                });
                console.log("fetched");
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

                send_data();
                console.log("click");

            };

    </script>

@endsection
