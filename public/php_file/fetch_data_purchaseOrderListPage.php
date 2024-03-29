

<?php
    set_include_path(__DIR__ . '');

    include("./dbConnect.php");


    $filter = "";
    $page_number = 1;
    $s_status_filter = "";
    $where = "";
    $and = "";
    
    //get data from productList.blade.php
    if(isset($_POST["action"])){

        //get status filter from productList.blade.php
        if(isset($_POST["status_filter"])){
            $_status_filter = implode(",", $_POST["status_filter"]);//get data
            $s_status_filter = strval($_status_filter); //like toString()

            $filter .= "
              (status IN (".$s_status_filter."))
            ";
        }
        if(isset($_POST["searchText"])){
            $searchText = strval($_POST["searchText"]);
            if (strlen($searchText) > 0){
                $searchQ = "(poID = '".$searchText."') ";
            }else {
                $searchQ = "";
            }
        }

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
    }

    if(strlen($searchQ)>0 OR strlen($s_status_filter)>0){
        $where = " WHERE ";
        if(strlen($searchQ)>0 AND strlen($s_status_filter)>0){
            $and = " AND ";
        }
    }

    $sorting = " ORDER BY ".$s_sorting_value." ".$AscDesc.", `poID` ".$AscDesc;

    $order_query = "SELECT * FROM
    (`purchaseorder` INNER JOIN `users`
    ON purchaseorder.customerID=users.id)".$where.$filter.$and.$searchQ.$sorting;

    //echo '<div>'.$order_query.'</div>';

    $order_set = $dbConnection->prepare($order_query);
    $order_set->execute();
    $order_result = $order_set->get_result();
    $data_count = 0;
    $output = '';


    while ($detail= mysqli_fetch_array($order_result)) {
        $data_count++;
        $output .= '<div class="order">
                        <a href="orders/'.$detail["poID"].'">
                            <div class="order-header"><p class="id_order">Purchase Order No.'.$detail['poID'].'</p></div>
                            <div class="order-body">
                                <p class="date_order">'.$detail["purchase_date"].'</p>
                                <p class="customer_name_order">'.$detail["name"].'</p>
                                <p class="total_order">$ '.$detail["total_order_amount"].'</p>
                                <p class="status_order">'.$detail["status"].'</p>
                            </div>
                        </a>
                    </div>';
    };
    if ($data_count == 0) {
        $output .= '<div class="no_order">No order</div>';
    };
    echo $output;

?>


