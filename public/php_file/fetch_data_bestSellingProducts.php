

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
            category IN (".$s_category_filter.") ";
        }

        if(isset($_POST["from_date"])){
            $from_date = strval("'".$_POST["from_date"]."'");
        }

        if(isset($_POST["to_date"])){
            $to_date = strval("'".$_POST["to_date"]."'");
        }

    }


    if(strlen($s_category_filter)>0){
        $and1 = " AND ";
    }



    //count how many best selling products
    //set query
    $query_for_count = "SELECT `product`.`productID`, `image_url`, `productName`, sum(`quantity`) AS `totalQuantity`, sum(`sub_order_amount`) AS `totalAmount`
                FROM (((`purchaseorderdetail` INNER JOIN `purchaseorder` ON `purchaseorder`.`poID`= `purchaseorderdetail`.`poID`)
                INNER JOIN `product` ON `product`.`productID`=`purchaseorderdetail`.`productID` )
                INNER JOIN `productimage` ON `productimage`.`productID`=`purchaseorderdetail`.`productID`)
                WHERE `purchaseorder`.`status` = 'shipped'
                AND  `purchaseorder`.`purchase_date` BETWEEN ".$from_date.' AND '.$to_date.$and1.$filter."
                GROUP BY `productID`
                ORDER BY `totalQuantity` DESC";

    //get data
    $statement = $dbConnection->prepare($query_for_count);
    $statement->execute();
    $resultSet1 = $statement->get_result();

    // echo $query_for_count;

    //print data
    $n_of_BSQ = 0;
    $row_for_count= mysqli_fetch_array($resultSet1);
    if ($row_for_count){
        $bestSellingQuantity = $row_for_count['totalQuantity'];
        $n_of_BSQ += 1;
        while ($row_for_count= mysqli_fetch_array($resultSet1)){
            if ($row_for_count['totalQuantity'] == $bestSellingQuantity){
                $n_of_BSQ = $n_of_BSQ +1;
            }
        }
    }




    $query = "SELECT `product`.`productID`, `image_url`, `productName`, sum(`quantity`) AS `totalQuantity`, sum(`sub_order_amount`) AS `totalAmount`
                FROM (((`purchaseorderdetail` INNER JOIN `purchaseorder` ON `purchaseorder`.`poID`= `purchaseorderdetail`.`poID`)
                INNER JOIN `product` ON `product`.`productID`=`purchaseorderdetail`.`productID` )
                INNER JOIN `productimage` ON `productimage`.`productID`=`purchaseorderdetail`.`productID`)
                WHERE `purchaseorder`.`status` = 'shipped'
                AND  `purchaseorder`.`purchase_date` BETWEEN ".$from_date.' AND '.$to_date.$and1.$filter."
                GROUP BY `productID`
                ORDER BY `totalQuantity` DESC";

    // echo $query;

    $statement = $dbConnection->prepare($query);
    $statement->execute();
    $resultSet2 = $statement->get_result();

    $data_count = 0;
    $output = '';
    $title = '';
    if ($n_of_BSQ > 1){
        $title = 'Best Selling Products';
    }else {
        $title = 'Best Selling Product';
    }
    echo '<div class="card-header">'.$title.'</div>
    <div class="card-body">';

    while ($row= mysqli_fetch_array($resultSet2)){
        $data_count .= 1;
        $output .='
                    <div class="best-products-box">
                        <a href="../products/'.$row['productID'].'" class="link-to-product-details">
                            <table class="best-products">
                                <tr>
                                    <td rowspan="4" class="image_container">
                                        <img class="image-best" src="../'.$row['image_url'].'" alt="'.$row['productName'].'" width="auto" height="200px">
                                    </td>
                                    <td colspan="2" class="name-best best">
                                        '.$row['productName'].'
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class="total-quantity-best best sm-detail">
                                        &times;'.$row['totalQuantity'].'
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="total-amount-best best">
                                        Total sales amount: $ '.$row['totalAmount'].'
                                    </td>
                                </tr>
                                <tr>
                                    <td</td><td</td>
                                </tr>
                            </table>
                        </a>
                    </div>'
                ;
        $n_of_BSQ = $n_of_BSQ-1;
        //echo 'no = '.$n_of_BSQ;
        if ($n_of_BSQ == 0){
            //echo "break";
            break;
        }
    };
    //echo '</div>';
    echo '';
    if($data_count == 0){
        $output .= '<div class="no_product">No best products</div>';
    }
    echo $output;




?>


