

<?php
    set_include_path(__DIR__ . '');

    include("./dbConnect.php");


    $filter = "";

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
    }

    //may do sorting requirement?? DONE
    $sorting = " ORDER BY product.".$s_sorting_value;

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

    //print Query
    echo $query."<br>";

    // print data
    $output = '';
    $data_count = 0;
    while ($row= mysqli_fetch_array($resultSet)){
        $data_count += 1;
        $output .=
            '<div><p class="product">
            <div class="image_productList"><img src="'.$row['image_url'].'" alt="'.$row['productName'].'" width="auto" height="200"></div>
            <div class="name_productList">Name: '.$row['productName'].'</div>
            <div class="category_productList">Category: '.$row['category'].'</div>
            <div class="price_productList">Price: $'.$row['price'].'</div>
            </div><br>';
    };
    if($data_count == 0){
        $output .= "No product";
    }
    echo $output;





    //testing

    //NP = Number_of_Products
    $NP_limit = 2; //limit productsfor each page

    // update the active page number

    if (isset($_GET["page"])) {

        $page_number  = $_GET["page"];

    }

    else {

      $page_number=1;

    }

    // get the initial page number

    $initial_page = ($page_number-1) * $limit;

    // get data of selected rows per page
    $limitQ = "LIMIT ".$initial_page.", ".$limit;

    //NP = Number_of_Products

    $NP_query = "SELECT COUNT(*) FROM
    (`product` INNER JOIN `productimage`
    ON product.productID=productimage.productID)
    LEFT JOIN `category`
    ON `product`.`category`=`category`.`categoryID` ".$filter.$sorting.";";

    $NP_result = mysqli_query($dbConnection, $NP_query);
    $NP_row = mysqli_fetch_row($NP_result);
    $NP_total = $NP_row[0];

    $total_pages = ceil($NP_total / $NP_limit);


    // echo '<input type="button" onclick="set_page()" class="page" value="2"></button><br>';


    $pageURL = "";


    if($page_number>=2){

        echo "<a href='productList?page=".($page_number-1)."'>  Prev </a>";

    }

    for ($i=1; $i<=$total_pages; $i++) {

        if ($i == $page_number) {

            $pageURL .= "<a class = 'active' href='productList?page=".$i."'>".$i." </a>";

        }

        else  {

            $pageURL .= "<a href='productList?page=".$i."'>".$i." </a>";

        }
    };

    echo $pageURL;

    if($page_number<$total_pages){

        echo "<a href='productList?page=".($page_number+1)."'>  Next </a>";

    }

    echo '


        <div class="inline">
            <input id="page" type="number" min="1" max="'.$total_pages.'"
                placeholder="'.$page_number.'/'.$total_pages.'" required>
            <button onClick="go2Page();">Go</button>
        </div>



        <script>
        function go2Page()

        {

            var page = document.getElementById("page").value;

            page = ((page>'.$total_pages.')?'.$total_pages.':((page<1)?1:page));

            window.location.href = "productList?page=+page";

        }
        </script>



        ';






?>










