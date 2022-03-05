

<?php
    set_include_path(__DIR__ . '');

    include("./dbConnect.php");


    $filter = "";
    $page_number = 1;
    //get data from productList.blade.php
    if(isset($_POST["action"])){

        //get status filter from productList.blade.php
        if(isset($_POST["status_filter"])){
            $_status_filter = implode(",", $_POST["status_filter"]);//get data
            $s_status_filter = strval($_status_filter); //like toString()

            $filter .= "
             AND (status IN (".$s_status_filter."))
            ";
        }
        if(isset($_POST["userid"])){
            $userid = strval($_POST["userid"]);

        }
    }

    $order_query = "SELECT * FROM
                        (`purchaseorder` INNER JOIN `users`
                        ON purchaseorder.customerID=users.id)
                        WHERE `purchaseorder`.`customerID`=".$userid.$filter;

    // echo '<div>'.$order_query.'</div>';

    $order_set = $dbConnection->prepare($order_query);
    $order_set->execute();
    $order_result = $order_set->get_result();
    $data_count = 0;
    $output = '';


    while ($detail= mysqli_fetch_array($order_result)) {
        $data_count++;
        $output .= '<div class="order">
                        <a href="orders/'.$detail["poID"].'">
                            <div class="order-header"><p class="mb-2 id_order">Purchase Order No.'.$detail['poID'].'</p></div>
                            <div class="order-body">
                                <p class="mb-2 date_order">'.$detail["purchase_date"].'</p>
                                <p class="mb-2 total_order">$ '.$detail["total_order_amount"].'</p>
                                <p class="mb-2 status_order">'.$detail["status"].'</p>
                            </div>
                        </a>
                    </div>';
    };
    if ($data_count == 0) {
        $output .= '<div>No order</div>';
    };
    echo $output;

?>

