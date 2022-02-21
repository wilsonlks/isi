@extends('layouts.app')

@section('content')

    <?php

    $dbConnection = mysqli_connect("localhost", "root", "", "isi");

    ?>



    <div class="filter">

        <!-- get category from DB -->
        <?php
            $categoryQ = "SELECT * FROM category";
            $CSet = $dbConnection->prepare($categoryQ);
            $CSet->execute();
            $CSetResult = $CSet->get_result();
        ?>

        <!-- checkbox for filter -->
        <?php while ($category = mysqli_fetch_assoc($CSetResult)){ ?>
            <div class="list-group-item checkbox">
                <label><input type="checkbox" class="category_checkbox" onchange="Click(this)" value="'<?php echo $category['categoryID']; ?>'"  > <?php echo $category['categoryName']; ?></label>
            </div>
        <?php } ?>
    </div>
    <p id="filter_string"></p>

    <div class="row filter_data">

    </div>
            <p id='table'> </p>

    <script>


        $(document).ready(function(){
            filter_data();
        });
            //filter
            var filter = [];
            // <?php

            //     $categoryID_filter="";


            // ?>


            function filter_data()
                {
                    $('.filter_data').html('<div id="loading" style="" ></div>');
                    var action = 'fetch_data';

                    var category_filter = get_filter('category_checkbox');
                    $.ajax({
                        url:"fetch_data.php",
                        method:"POST",
                        data:{action:action, category_filter:category_filter},
                        success:function(data){
                            $('#table').html(data);
                        }
                    });
                    console.log("fetched");
                }
            console.log(filter.length);

            function set_filterrrrrrr(checkbox) {
                if (checkbox.checked=true){
                    filter.push(checkbox.value);
                    console.log("added "+checkbox.value);
                    filter.forEach(element => {
                        console.log(element);
                    });
                }

            };

            function get_filterrrrrr(){
                return filter;
            }

            function get_filter(class_name)
            {
                var filter = [];
                $('.'+class_name+':checked').each(function(){
                    filter.push($(this).val());
                });
                return filter;
            }
            function Click(checkbox){
                        if (checkbox.checked == true){
                            filter_data();
                            console.log("click");
                        }
                    };



    </script>
        <?php
        // $query = "SELECT * FROM `product` WHERE `category` in (".$categoryID_filter.")";


        // ?>



<?php






//include (dirname(__FILE__)."\auth\dbConnect.php");


    // $productList = mysqli_query($dbConnection, "SELECT * FROM `product");

    // while ($product = mysqli_fetch_assoc($productList)){
    //     echo
    //     '<div class="product">'
    //     .$product['productID'].'<br>'
    //     .$product['productName'].
    //     '</div><br>';

    // }
?>












@endsection
