

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
    //set query_quantity
    $query_quantity_for_count = "SELECT `product`.`productID`, `image_url`, `productName`, sum(`quantity`) AS `totalQuantity`, sum(`sub_order_amount`) AS `totalAmount`
                FROM (((`purchaseorderdetail` INNER JOIN `purchaseorder` ON `purchaseorder`.`poID`= `purchaseorderdetail`.`poID`)
                INNER JOIN `product` ON `product`.`productID`=`purchaseorderdetail`.`productID` )
                INNER JOIN `productimage` ON `productimage`.`productID`=`purchaseorderdetail`.`productID`)
                WHERE `purchaseorder`.`status` = 'shipped'
                AND  `purchaseorder`.`purchase_date` BETWEEN ".$from_date.' AND '.$to_date.$and1.$filter."
                GROUP BY `productID`
                ORDER BY `totalQuantity` DESC";

    //get data
    $statement = $dbConnection->prepare($query_quantity_for_count);
    $statement->execute();
    $resultSet1_quantity = $statement->get_result();

    // echo $query_quantity_for_count;

    //print data
    $n_of_BSQ_quantity = 0;
    $row_for_count= mysqli_fetch_array($resultSet1_quantity);
    if ($row_for_count){
        $bestSellingQuantity = $row_for_count['totalQuantity'];
        $n_of_BSQ_quantity += 1;
        while ($row_for_count= mysqli_fetch_array($resultSet1_quantity)){
            if ($row_for_count['totalQuantity'] == $bestSellingQuantity){
                $n_of_BSQ_quantity = $n_of_BSQ_quantity +1;
            }
        }
    }




    $query_quantity = "SELECT `product`.`productID`, `image_url`, `productName`, sum(`quantity`) AS `totalQuantity`, sum(`sub_order_amount`) AS `totalAmount`
                FROM (((`purchaseorderdetail` INNER JOIN `purchaseorder` ON `purchaseorder`.`poID`= `purchaseorderdetail`.`poID`)
                INNER JOIN `product` ON `product`.`productID`=`purchaseorderdetail`.`productID` )
                INNER JOIN `productimage` ON `productimage`.`productID`=`purchaseorderdetail`.`productID`)
                WHERE `purchaseorder`.`status` = 'shipped'
                AND  `purchaseorder`.`purchase_date` BETWEEN ".$from_date.' AND '.$to_date.$and1.$filter."
                GROUP BY `productID`
                ORDER BY `totalQuantity` DESC";

    // echo $query_quantity;

    $statement = $dbConnection->prepare($query_quantity);
    $statement->execute();
    $resultSet2_quantity = $statement->get_result();

    $data_count_quantity = 0;
    $output_quantity = '';
    $title_quantity = '';
    if ($n_of_BSQ_quantity > 1){
        $title_quantity = 'Best Selling Products';
    }else {
        $title_quantity = 'Best Selling Product';
    }
    echo '<div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card best-products-detail mt-2">
                    <div class="card-header">'.$title_quantity.'-Quantity</div>
                        <div class="card-body">';

    while ($row= mysqli_fetch_array($resultSet2_quantity)){
        $data_count_quantity .= 1;
        $output_quantity .='
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
        $n_of_BSQ_quantity = $n_of_BSQ_quantity-1;
        //echo 'no = '.$n_of_BSQ_quantity;
        if ($n_of_BSQ_quantity == 0){
            //echo "break";
            break;
        }
    };
    //echo '</div>';
    echo '';
    if($data_count_quantity == 0){
        $output_quantity .= '<div class="no_product">No best products</div>';
    }

    echo $output_quantity;

    echo '</div></div></div></div></div>';


    //count how many best selling products
    //set query_quantity
    $query_amount_for_count = "SELECT `product`.`productID`, `image_url`, `productName`, sum(`quantity`) AS `totalQuantity`, sum(`sub_order_amount`) AS `totalAmount`
                FROM (((`purchaseorderdetail` INNER JOIN `purchaseorder` ON `purchaseorder`.`poID`= `purchaseorderdetail`.`poID`)
                INNER JOIN `product` ON `product`.`productID`=`purchaseorderdetail`.`productID` )
                INNER JOIN `productimage` ON `productimage`.`productID`=`purchaseorderdetail`.`productID`)
                WHERE `purchaseorder`.`status` = 'shipped'
                AND  `purchaseorder`.`purchase_date` BETWEEN ".$from_date.' AND '.$to_date.$and1.$filter."
                GROUP BY `productID`
                ORDER BY `totalQuantity` DESC";

    //get data
    $statement = $dbConnection->prepare($query_amount_for_count);
    $statement->execute();
    $resultSet1_amount = $statement->get_result();

    // echo $query_quantity_for_count;

    //print data
    $n_of_BSQ_amount = 0;
    $row_for_count= mysqli_fetch_array($resultSet1_amount);
    if ($row_for_count){
        $bestSellingAmount = $row_for_count['totalAmount'];
        $n_of_BSQ_amount += 1;
        while ($row_for_count= mysqli_fetch_array($resultSet1_amount)){
            if ($row_for_count['totalAmount'] == $bestSellingAmount){
                $n_of_BSQ_amount = $n_of_BSQ_amount +1;
            }
        }
    }

    // echo $bestSellingAmount.' and '.$n_of_BSQ_amount;


    $query_amount = "SELECT `product`.`productID`, `image_url`, `productName`, sum(`quantity`) AS `totalQuantity`, sum(`sub_order_amount`) AS `totalAmount`
                FROM (((`purchaseorderdetail` INNER JOIN `purchaseorder` ON `purchaseorder`.`poID`= `purchaseorderdetail`.`poID`)
                INNER JOIN `product` ON `product`.`productID`=`purchaseorderdetail`.`productID` )
                INNER JOIN `productimage` ON `productimage`.`productID`=`purchaseorderdetail`.`productID`)
                WHERE `purchaseorder`.`status` = 'shipped'
                AND  `purchaseorder`.`purchase_date` BETWEEN ".$from_date.' AND '.$to_date.$and1.$filter."
                GROUP BY `productID`
                ORDER BY `totalAmount` DESC";

    // echo $query_quantity;

    $statement = $dbConnection->prepare($query_amount);
    $statement->execute();
    $resultSet2_amount = $statement->get_result();

    $data_count_amount = 0;
    $output_amount = '';
    $title_amount = '';
    if ($n_of_BSQ_amount > 1){
        $title_amount = 'Best Selling Products';
    }else {
        $title_amount = 'Best Selling Product';
    }
    echo '<div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card best-products-detail mt-2">
                    <div class="card-header">'.$title_amount.'-Amount</div>
                        <div class="card-body">';

    while ($row= mysqli_fetch_array($resultSet2_amount)){
        $data_count_amount .= 1;
        $output_amount .='
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
        $n_of_BSQ_amount = $n_of_BSQ_amount-1;
        //echo 'no = '.$n_of_BSQ_quantity;
        if ($n_of_BSQ_amount == 0){
            //echo "break";
            break;
        }
    };
    //echo '</div>';
    echo '';
    if($data_count_amount == 0){
        $output_amount .= '<div class="no_product">No best products</div>';
    }

    echo $output_amount;

    echo '</div></div></div></div></div>';




?>


