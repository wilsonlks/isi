@extends('layouts.app')

@section('content')

    <?php
        include("./php_file/dbConnect.php");
    ?>

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
        </div>

        <!-- checkbox for filter -->
        <h4>filter</h4>
        <?php while ($category = mysqli_fetch_assoc($CSetResult)){ ?>
            <div class="list-group-item checkbox">
                <label><input type="checkbox" class="category_checkbox" onchange="Click()" value="'<?php echo $category['categoryID']; ?>'"  > <?php echo $category['categoryName']; ?></label>
            </div>
        <?php } ?>
    </div>

    <!-- ujax will get response from fetch_data.php and set html code to here -->
    <p id="filter_string"></p>


    <script>


        $(document).ready(function(){
            send_data();
        });

            // send checkbox value by using ajax
            function send_data(){

                    $('.filter_data').html('<div id="loading" style="" ></div>');
                    var action = 'fetch_data';
                    var category_filter = get_filter('category_checkbox');
                    var sorting = get_sorting('sorting_radio');
                    $.ajax({

                        url:"./php_file/fetch_data.php",
                        method:"POST",
                        data:{action:action, category_filter:category_filter, sorting:sorting},
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
