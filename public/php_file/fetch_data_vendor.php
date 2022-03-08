

<?php
    set_include_path(__DIR__ . '');

    include("./dbConnect.php");

    echo "<div class='container'>
             <div class='row justify-content-center'>
                <div class='col-md-8'>";
    $filter = "";
    $page_number = 1;
    //get data from productList.blade.php
    if(isset($_POST["action"])){

        //get category filter from productList.blade.php
        if(isset($_POST["category_filter"])){
            $_category_filter = implode(",", $_POST["category_filter"]);//get data
            $s_category_filter = strval($_category_filter); //like toString()

            $filter .= "
            product.category IN (".$s_category_filter.")
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

        if(isset($_POST["searchText"])){
            $searchText = strval($_POST["searchText"]);
            if (strlen($searchText) > 0){
                $searchQ = "((product.productName LIKE '%".$searchText."%') OR (product.productID='".$searchText."')) ";
            }else {
                $searchQ = "";
            }
        }

        if (isset($_POST["page"])) {

            $page_number  = intval($_POST["page"]);

        }

        else {

          $page_number=1;

        }

    }

    if(strlen($searchQ)>0 OR strlen($s_category_filter)>0){
        $where = " WHERE ";
        if(strlen($searchQ)>0 AND strlen($s_category_filter)>0){
            $and = " AND ";
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
    ON `product`.`category`=`category`.`categoryID` ".$where.$searchQ.$and.$filter.$sorting.$limitQ.";";

    //print Query
    //echo $query."<br>";

    //get data
    $statement = $dbConnection->prepare($query);
    $statement->execute();
    $resultSet = $statement->get_result();




    //print data
    $output = '';
    $data_count = 0;
    echo '<div class="productList mt-2">
            <div class="card">
                <div class="card-header">Products</div>
                <div class="card-body">';

    while ($row= mysqli_fetch_array($resultSet)){
        $data_count += 1;
        $output .=
            '

                    <div class="product">
                        <a href="products/'.$row['productID'].'" class="link-to-product-details" style="text-decoration: none; color:black;">
                            <div class="image_productList"><img src="'.$row['image_url'].'" alt="'.$row['productName'].'" ></div>
                            <div class="id_productList">No.'.$row['productID'].'</div> |
                            <div class="name_productList">'.$row['productName'].'</div> |
                            <div class="category_productList">'.$row['categoryName'].'</div>
                            <div class="price_productList">$ '.$row['price'].'</div>
                        </a>
                    </div>';
    };

    echo '';
    if($data_count == 0){
        $output .= '<div class="no_product">No product</div>';
    }
    echo $output;
    echo "</div></div></div></div>";



    //NP = Number_of_Products
    //get total product of list
    $NP_query = "SELECT COUNT(*) FROM
    (`product` INNER JOIN `productimage`
    ON product.productID=productimage.productID)
    LEFT JOIN `category`
    ON `product`.`category`=`category`.`categoryID` ".$where.$searchQ.$and.$filter.$sorting.";";
    //echo $NP_query;
    $NP_result = mysqli_query($dbConnection, $NP_query);
    $NP_row = mysqli_fetch_row($NP_result);
    $NP_total = $NP_row[0];

    $total_pages = ceil($NP_total / $NP_limit);


    //print button of pagination
    $pageURL = "";

    // echo "pagenumber".$page_number."   ini".$initial_page."    nptotal".$NP_total;

    if ($total_pages != 0) {

            $pageURL .= '<div class="page_productList justify-content-center mt-2">';

        if($page_number==1){
            $pageURL .= '<span class="page page_disabled">Previous</span>';
        } else {
            $pageURL .= '<button onclick="set_page('.($page_number-1).')" class="page" value="'.($page_number-1).'">Previous</button>';
        };

        for ($i=1; $i<=$total_pages; $i++) {
            if ($i == $page_number) {
                $pageURL .= '<span class="page page_active">'.$i.'</span>';
            } else {
                $pageURL .= '<button onclick="set_page('.$i.')" class="page" value="'.$i.'">'.$i.'</button>';
            };
        };

        if($page_number!=$total_pages){
            $pageURL .= '<button onclick="set_page('.($page_number+1).')" class="page" value="'.($page_number+1).'">Next</button>';
        } else {
            $pageURL .= '<span class="page page_disabled" value="'.($page_number+1).'">Next</span>';
        }

        $pageURL .= '</div>';

        echo $pageURL;

        // echo $total_pages.'<br>';

        // echo $NP_total;

    }
?>


