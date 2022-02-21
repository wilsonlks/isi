

<?php

include("./dbConnect.php");
    $filter = "";



    if(isset($_POST["action"])){
        if(isset($_POST["category_filter"])){  //get category filter from productList.blade.php
            $_category_filter = implode(",", $_POST["category_filter"]);//get data
            $s_category_filter = strval($_category_filter); //like toString()
            $filter .= "
            WHERE category IN (".$s_category_filter.")
            ";

        }
        if(isset($_POST["sorting"])){ //get and set sorting value
            $s_sorting_value = strval($_POST["sorting"]);

        }
    }

    $sorting = " ORDER BY product.".$s_sorting_value; //may do sorting requirement?? DONE

    //set query
    $query = "SELECT * FROM
    (`product` INNER JOIN `productimage`
    ON product.productID=productimage.productID)
    LEFT JOIN `category`
    ON `product`.`category`=`category`.`categoryID` ".$filter.$sorting.";";

    //get data
    $statement = $dbConnection->prepare($query);
    $statement->execute();
    $resultSet = $statement->get_result();

    echo $query."<br>";

    // print data
    $output = '';
    $data_count = 0;
    while ($result= mysqli_fetch_assoc($resultSet)){
        $data_count += 1;
        $output .=
            '<div><p class="product">
            <div class="image_productList"><img src="'.$result['image_url'].'" alt="'.$result['productName'].'" width="auto" height="200"></div>
            <div class="name_productList">Name: '.$result['productName'].'</div>
            <div class="category_productList">Category: '.$result['category'].'</div>
            <div class="price_productList">Price: $'.$result['price'].'</div>
            </div><br>';

    };
    if($data_count == 0){
        $output .= "No product";
    }

    echo $output;

   ?>










