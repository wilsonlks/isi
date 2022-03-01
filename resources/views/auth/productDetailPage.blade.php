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
            WHERE `product`.`productID`=$productID";
            $PSet = $dbConnection->prepare($productQ);
            $PSet->execute();
            $PSetResult1 = $PSet->get_result();
            $PSet->execute();
            $PSetResult2 = $PSet->get_result();
        ?>

        <?php
            //print data
            $detail= mysqli_fetch_array($PSetResult1);
            ?>  <table class="product" style="margin: 30px 110px">
                <tr class="image_productDetailPage"><td style="padding: 5px 15px"><img src="../<?php echo $detail['image_url'] ?>" width="auto" height="200"></td></tr>
                <tr class="name_productDetailPage"><td style="padding: 5px 15px">Name:</td><td style="padding: 5px 15px"><?php echo $detail['productName'] ?></td></tr>
                <tr class="category_productDetailPage"><td style="padding: 5px 15px">Category:</td><td style="padding: 5px 15px"><?php echo $detail['categoryName'] ?></td></tr>
                <tr class="price_productDetailPage"><td style="padding: 5px 15px">Price:</td><td style="padding: 5px 15px">$ <?php echo $detail['price'] ?></td></tr>
                <tr class="description_productDetailPage"><td style="padding: 5px 15px">Descriptions:</td> <?php
            while ($detail= mysqli_fetch_array($PSetResult2)){ ?>
                <td style="padding: 5px 15px"><?php echo $detail['detail_description'] ?></td></tr><tr><td></td>
            <?php };
            ?> </tr></table> <?php
            //echo $output;

        ?>

    </div>


@endsection
