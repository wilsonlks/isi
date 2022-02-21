

<?php


    $dbConnection = mysqli_connect("localhost", "root", "", "isi");
    $query = "SELECT * FROM product";
    if(isset($_POST["action"])){
        if(isset($_POST["category_filter"])){
            $_category_filter = implode(",", $_POST["category_filter"]);
            $s_category_filter = strval($_category_filter);
            $query .= "
            WHERE category IN (".$s_category_filter.");
            ";
        }
    }



    $statement = $dbConnection->prepare($query);
    $statement->execute();
    $resultSet = $statement->get_result();

    echo $query."<br>";

    echo $_POST["category_filter"]."<br>";

    foreach($_POST["category_filter"] as $a){
        echo $a."<br>";
        echo "done"."<br>";
    }

    echo "<br>"."<br>"."<br>";



    $output = '';

    while ($result= mysqli_fetch_assoc($resultSet)){
        $output .=
            '<div><p class="product">'
            .$result['productID'].'<br>'
            .$result['productName'].
            '</p><br></div>';
    };

    echo $output;

    echo "123";

    // if($total_row > 0)
    // {
    //  foreach($result as $row)
    //  {
    //   $output .= '
    //   <div class="col-sm-4 col-lg-3 col-md-3">
    //    <div style="border:1px solid #ccc; border-radius:5px; padding:16px; margin-bottom:16px; height:450px;">
    //     <img src="image/'. $row['product_image'] .'" alt="" class="img-responsive" >
    //     <p align="center"><strong><a href="#">'. $row['product_name'] .'</a></strong></p>
    //     <h4 style="text-align:center;" class="text-danger" >'. $row['product_price'] .'</h4>
    //     <p>Camera : '. $row['product_camera'].' MP<br />
    //     Brand : '. $row['product_brand'] .' <br />
    //     RAM : '. $row['product_ram'] .' GB<br />
    //     Storage : '. $row['product_storage'] .' GB </p>
    //    </div>

    //   </div>
    //   ';
    //  }
    // }
    // else
    // {
    //  $output = '<h3>No Data Found</h3>';
    // }
    // echo $output;



   ?>










