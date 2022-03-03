@extends('layouts.app')

@section('content')

    <?php
        include("./php_file/dbConnect.php");

        // get specific product from DB

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
        $PSet->execute();
        $PSetResult3 = $PSet->get_result();

        // add to cart

        $cart_saved = FALSE;
        
        if (isset($_POST['submit'])) {

            
            $cartDetail = mysqli_fetch_array($PSetResult3);

            $customer = Auth::id();

            $product = $cartDetail['productID'];
                            
            $quantity = 1;

            /*
            * Validate posted values.
            */
            
            $product_unique_query = "SELECT productID FROM shoppingcart
                                        WHERE (customerID = '$customer') AND (productID = '$productID')";
            $statement = $dbConnection->prepare($product_unique_query);
            $statement->execute();
            $product_unique_result = mysqli_fetch_array($statement->get_result());

            if ($product_unique_result) {
                $message = 'This product has already been added to your cart.';
            }

            if (!isset($message)) {
                $add_cart_query = "INSERT INTO shoppingcart (
                                        customerID,
                                        productID,
                                        quantity
                                    ) VALUES (
                                        ?, ?, ?
                                    )";
                $statement = $dbConnection->prepare($add_cart_query);
                $statement->bind_param('isi', $customer, $product, $quantity);
                $statement->execute();
                $statement->close();

                $message = 'This product has been added to your cart successfully.';
                $cart_saved = TRUE;
                $cartDetail = $customer = $product = $quantity = $product_unique_query = $product_unique_result = NULL;
            }

        }

    ?>

    <style type="text/css">
        .card-header {
            padding: 0px;
            background: #e0ffe1;
        }
        .card-image {
            margin: auto;
            width: 50%;
        }
        .image_detail {
            display: block;
            width: 100%;
            height: auto;
            margin-left: auto;
            margin-right: auto;
        }
        .card-body, .alert_detail {
            margin: 0px 10px 0px;
        }
        .card-title, .card-subtitle, .alert {
            float: none;
            clear: both;
        }
        .card-subtitle {
            border-bottom: 2px solid darkgreen;
            padding-bottom: 35px;
            padding-top: 5px;
        }
        .card-text, .alert {
            padding-top: 10px;
        }
        .alert {
            padding-right: 15px;
            padding-bottom: 35px;
        }
        .name_detail {
            text-transform: uppercase;
            font-weight: bold;
            color: green;
        }
        .name_detail, .id_detail, .category_detail, .text {
            display: inline-block;
            float: left;
        }
        .id_detail {
            padding-right: 20px;
        }
        .price_detail, .button_detail, .close {
            display: inline-block;
            float: right;
        }
        .description_detail:first-child {
            padding-bottom: 5px;
        }
        .close {
            border: none;
            background: none;
            color: grey;
            font-weight: bold;
        }
    </style>

    <div class="content">

        <!-- print data -->

        <?php
            $detail= mysqli_fetch_array($PSetResult1);
        ?>              
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">    
                            <div class="card-image">
                                <img class="image_detail" src="../<?php echo $detail['image_url'] ?>" alt="Card image cap">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-title">
                                <div class="card-column"><h1 class="name_detail"><?php echo $detail['productName'] ?></h1></div>
                                @auth
                                    @if (Auth::user()->role=='customer')
                                        <form method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="card-column">
                                                <button id="submit" type="submit" name="submit" class="btn btn-primary button_detail">
                                                    {{ __('Add to Cart') }}
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                            <div class="card-subtitle">
                                @auth
                                    @if (Auth::user()->role=='vendor')
                                        <h4 class="mb-2 text-muted id_detail">No. <?php echo $detail['productID'] ?></h4>
                                    @endif
                                @endauth
                                <h4 class="mb-2 text-muted category_detail"><?php echo $detail['categoryName'] ?></h4>
                                <h4 class="mb-2 text-muted price_detail">$ <?php echo $detail['price'] ?></h4>
                            </div>
                            <div class="card-text"><?php
                                while ($detail= mysqli_fetch_array($PSetResult2)){ ?>
                                    <div class="description_detail"><?php echo $detail['detail_description'] ?></div>
                                <?php }
                            ?></div>
                        </div>
                    </div>                                    
                    @if (isset($message))
                        <div class="alert @if($cart_saved==TRUE) alert-success @else alert-warning @endif alert-dismissible fade show" role="alert">
                            <strong class="alert_detail text">{{ $message }}</strong>
                            <button type="button" class="alert_detail close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        <?php $message = $cart_saved = NULL; ?>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>



@endsection
