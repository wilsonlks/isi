

<?php
    set_include_path(__DIR__ . '');

    include("./dbConnect.php");


    $filter = "";
    $page_number = 1;
    //get data from productList.blade.php
    if(isset($_POST["action"])){

        //get category filter from productList.blade.php
        if(isset($_POST["category_filter"])){
            $_category_filter = implode(",", $_POST["category_filter"]);//get data
            $s_category_filter = strval($_category_filter); //like toString()
            $filter .= "
            WHERE category IN (".$s_category_filter.")
            ";
        }

        //get and set sorting value
        if(isset($_POST["sorting"])){
            $s_sorting_value = strval($_POST["sorting"]);
        }

        if (isset($_POST["AscDesc"])) {

            $AscDesc  = intval($_POST["AscDesc"]);
            if ($AscDesc == 1){
                $AscDesc = "ASC";
            }elseif ($AscDesc == -1) {
                $AscDesc = "DESC";

            }

        }

        if (isset($_POST["page"])) {

            $page_number  = intval($_POST["page"]);

        }

        else {

          $page_number=1;

        }

    }



    //may do sorting requirement?? DONE
    $sorting = " ORDER BY product.".$s_sorting_value." ".$AscDesc;

    //echo "page = ".$page_number;


    //NP = Number_of_Products
    $NP_limit = 2; //limit products for each page

    // update the active page number


    // get the initial page number

    $initial_page = ($page_number-1) * $NP_limit;

    // get data of selected rows per page
    $limitQ = " LIMIT ".$initial_page.", ".$NP_limit;

    //set query
    $query = "SELECT * FROM
    (`product` INNER JOIN `productimage`
    ON product.productID=productimage.productID)
    LEFT JOIN `category`
    ON `product`.`category`=`category`.`categoryID` ".$filter.$sorting.$limitQ.";";

    //get data
    $statement = $dbConnection->prepare($query);
    $statement->execute();
    $resultSet = $statement->get_result();

    //print Query
    //echo $query."<br>";

    //print data
    $output = '';
    $data_count = 0;
    while ($row= mysqli_fetch_array($resultSet)){
        $data_count += 1;
        $output .=
            '<div class="productList"><p>

            <div class="product"><a href="products/'.$row['productID'].'" class="link-to-product-details" style="text-decoration: none; color:black;">
            <div class="image_productList"><img src="'.$row['image_url'].'" alt="'.$row['productName'].'" width="auto" height="200px"></div>
            <div class="name_productList">Name: '.$row['productName'].'</div>
            <div class="category_productList">Category: '.$row['categoryName'].'</div>
            <div class="price_productList">Price: $'.$row['price'].'</div>
            </a></div></p></div><br>';
    };
    if($data_count == 0){
        $output .= '<div> No product </div><br>';
    }
    echo $output;



    //NP = Number_of_Products
    //get total product of list
    $NP_query = "SELECT COUNT(*) FROM
    (`product` INNER JOIN `productimage`
    ON product.productID=productimage.productID)
    LEFT JOIN `category`
    ON `product`.`category`=`category`.`categoryID` ".$filter.$sorting.";";

    $NP_result = mysqli_query($dbConnection, $NP_query);
    $NP_row = mysqli_fetch_row($NP_result);
    $NP_total = $NP_row[0];

    $total_pages = ceil($NP_total / $NP_limit);


    //print button of pagination
    $pageURL = "";


    if($page_number>=2){

        echo '<button onclick="set_page('.($page_number-1).')" class="page" value="'.($page_number-1).'">Prev</button>';

    };

    for ($i=1; $i<=$total_pages; $i++) {

        if ($i == $page_number) {

            $pageURL .= '<button onclick="set_page('.$i.')" class="page" value="'.$i.'" style="color: red">'.$i.'</button>';

        }

        else  {

            $pageURL .= '<button onclick="set_page('.$i.')" class="page" value="'.$i.'">'.$i.'</button>';

        }
    };

    echo $pageURL;

    if($page_number<$total_pages){

        echo '<button onclick="set_page('.($page_number+1).')" class="page" value="'.($page_number+1).'">Next</button>';

    };
    // echo $total_pages.'<br>';

    // echo $NP_total;
?>










