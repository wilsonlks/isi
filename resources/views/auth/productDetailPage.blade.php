@extends('layouts.app')

@section('content')

    <?php
        include("./php_file/dbConnect.php");
    ?>

    <div class="content">

        <!-- get specific product from DB -->
        <?php
            $url=url()->full();
            $split_url=preg_split("#/#", $url);
            $productID=$split_url[count($split_url)-1];

            $productQ = "SELECT * FROM
            (`product` INNER JOIN `productimage`
            ON product.productID=productimage.productID
            INNER JOIN `productproperty`
            ON product.productID=productproperty.productID)
            LEFT JOIN `category`
            ON `product`.`category`=`category`.`categoryID` 
            WHERE `product`.`productID`=$productID";                     //need to be modified, get productID from the clicked link
            $PSet = $dbConnection->prepare($productQ);
            $PSet->execute();
            $PSetResult1 = $PSet->get_result();
            $PSet->execute();
            $PSetResult2 = $PSet->get_result();
            $PSet->execute();
            $PSetResult3 = $PSet->get_result();
        ?>

        <?php
            //print data
            $output = '<table class="product"><tr class="image_productDetailPage">';
            while ($detail= mysqli_fetch_array($PSetResult1)){
                $output .=
                    '<td><img src="'.$detail['image_url'].'" width="auto" height="200"></td>';
            };
            $detail= mysqli_fetch_array($PSetResult2);
            $output .= 
                '</tr>
                <tr class="name_productDetailPage"><td>Name:</td><td>'.$detail['productName'].'</td></tr>
                <tr class="category_productDetailPage"><td>Category:</td><td>'.$detail['category'].'</td></tr>
                <tr class="price_productDetailPage"><td>Price:</td><td>$'.$detail['price'].'</td></tr>
                <tr class="description_productDetailPage" rowspan="10"><td>Descriptions:</td>';
            while ($detail= mysqli_fetch_array($PSetResult3)){
                $output .=
                    '<td>'.$detail['detail_description'].'</td>';
            };
            $output .= '</tr></table>';
            echo $output;
        ?>
        
    </div>


@endsection
