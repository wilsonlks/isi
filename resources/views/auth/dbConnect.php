<?php

// set_include_path("/dbConnect.php");
// echo get_include_path();

$dbConnection = mysqli_connect("localhost", "root", "", "isi");

if(mysqli_connect_errno()){
    echo "fail".mysqli_connect_error();
    exit();
}

echo "success";
?>





