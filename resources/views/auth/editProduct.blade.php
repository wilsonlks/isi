@extends('layouts.app')

@section('content')

    <?php

        include("./php_file/config.php");
        include("./php_file/dbConnect.php");

        // get specific product from DB

        $url=url()->full();
        $split_url=preg_split("#/#", $url);
        $productID=$split_url[count($split_url)-2];

        // $previous_url=url()->previous();
        // $split_previous_url=preg_split("#/#", $previous_url);
        // $is_order=$split_previous_url[count($split_previous_url)-2];
        // $order=$split_previous_url[count($split_previous_url)-1];

        // echo $is_order;
        // echo $order;

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
        $PSet->execute();
        $PSetResult4 = $PSet->get_result();

        // edit product

        $editProductSaved = FALSE;

        if (isset($_POST['edit-confirm'])) {

            // read posted values.

            $productID = $_POST['productID'];
            $productName = $_POST['edit-name'];
            $category = $_POST['edit-category'];
            $price = $_POST['edit-price'];
            $stock = $_POST['edit-stock'];
            $descriptions[0] = $_POST['edit-description-1'];
            $descriptions[1] = $_POST['edit-description-2'];

            // validate posted values.

            $productName_unique_query = "SELECT COUNT(productName) AS uniqueCount FROM product
                                            WHERE productName = '$productName'
                                            AND productID != '$productID'";
            $statement = $dbConnection->prepare($productName_unique_query);
            $statement->execute();
            $productName_unique_result = mysqli_fetch_array($statement->get_result());

            $old_detail = mysqli_fetch_array($PSetResult3);

            if (empty($productName)) {
                $productName = $old_detail['productName'];
            }

            if ($productName_unique_result['uniqueCount'] > 0) {
                $error_name[] = 'This product name has already been taken. Please try another one.';
                $error_detail[] = 'This product name has already been taken. Please try another one.';
                $productName = $old_detail['productName'];
            }
            
            // if ($category=="0") {
            //     $error_category = 'Please select the category.';
            //     $error_detail[] = $error_category;
            //     $category = $old_detail['category'];
            // }

            if (empty($price)) {
                $price = $old_detail['price'];
            }

            if ($price==0) {
                $error_price = 'The price cannot be 0.';
                $error_detail[] = $error_price;
                $price = $old_detail['price'];
            }

            if (empty($stock)) {
                $stock = $old_detail['stock'];
            }

            if ($stock==0) {
                $error_stock = 'The stock quantity cannot be zero.';
                $error_detail[] = $error_stock;
                $stock = $old_detail['stock'];
            }

            while ($old_detail_description = mysqli_fetch_array($PSetResult4)) {
                if (empty($descriptions[$old_detail_description['property_number']-1])) {
                    $descriptions[$old_detail_description['property_number']-1] = $old_detail_description['detail_description'];
                }
            }

            // update product details.

            if (!isset($error_detail)) {
                
                $edit_product_query = "UPDATE `product`
                                        SET `productName`=\"$productName\", `category`=$category, `price`=$price, `stock`=$stock
                                        WHERE `productID`=$productID";
                $statement = $dbConnection->prepare($edit_product_query);
                $statement->execute();
                $statement->close();

                $property_number = 1;
                foreach ($descriptions as $description) {
                    $edit_description_query = "UPDATE `productproperty`
                                                SET `detail_description`=\"$description\"
                                                WHERE `productID`=$productID
                                                AND `property_number`=$property_number";
                    $statement = $dbConnection->prepare($edit_description_query);
                    $statement->execute();
                    $statement->close();
                    $property_number++;
                }

                $editProductSaved = TRUE;

                $productName = $category = $price = $stock = $descriptions[] = $descriptions[0] = $descriptions[1] = NULL;

                // if ($is_order == "orders") {
                //     header("location:http://localhost:8000/".$is_order."/".$order); exit;
                // } else {
                //     echo $is_order;
                //     echo $order;
                header("location:http://localhost:8000/products/".$productID); exit;
                // }

            }

        }

        if (isset($_POST['edit-cancel'])) {

            $productID = $_POST['productID'];
            header("location:http://localhost:8000/products/".$productID); exit;

        }

            // /*
            // * Create "image" directory if it doesn't exist.
            // */
            // if (!is_dir(UPLOAD_DIR)) {
            //     mkdir(UPLOAD_DIR, 0777, true);
            // }

            // /*
            // * List of file _strings to be filled in by the upload script
            // * below and to be saved in the db table "products_images" afterwards.
            // */
            // $file_stringsToSave = [];

            // $allowedMimeTypes = explode(',', UPLOAD_ALLOWED_MIME_TYPES);

            // /*
            // * Upload files.
            // */
            // if (!empty($_FILES)) {
            //     if (isset($_FILES['file']['error'])) {
            //         foreach ($_FILES['file']['error'] as $uploadedFileKey => $uploadedFileError) {
            //             if ($uploadedFileError === UPLOAD_ERR_NO_FILE) {
            //                 $error_file[] = 'Please upload the thumbnail image file.';
            //                 $error_detail[] = 'Please upload the thumbnail image file.';
            //             } elseif ($uploadedFileError === UPLOAD_ERR_OK) {
            //                 $uploadedFileName = basename($_FILES['file']['name'][$uploadedFileKey]);

            //                 if ($_FILES['file']['size'][$uploadedFileKey] <= UPLOAD_MAX_FILE_SIZE) {
            //                     $uploadedFileType = $_FILES['file']['type'][$uploadedFileKey];
            //                     $uploadedFileTempName = $_FILES['file']['tmp_name'][$uploadedFileKey];

            //                     $uploadedFilePath = rtrim(UPLOAD_DIR, '/') . '/' . $uploadedFileName;

            //                     if (in_array($uploadedFileType, $allowedMimeTypes)) {
            //                         if (!move_uploaded_file($uploadedFileTempName, $uploadedFilePath)) {
            //                             $error_file[] = 'The file "' . $uploadedFileName . '" could not be uploaded.';
            //                             $error_detail[] = 'The file "' . $uploadedFileName . '" could not be uploaded.';
            //                         } else {
            //                             $filenamesToSave[] = $uploadedFilePath;
            //                         }
            //                     } else {
            //                         $error_file[] = 'The extension of the file "' . $uploadedFileName . '" is not valid. Allowed extensions: JPG, JPEG, PNG, or GIF.';
            //                         $error_detail[] = 'The extension of the file "' . $uploadedFileName . '" is not valid. Allowed extensions: JPG, JPEG, PNG, or GIF.';
            //                     }
            //                 } else {
            //                     $error_file[] = 'The size of the file "' . $uploadedFileName . '" must be of max. ' . (UPLOAD_MAX_FILE_SIZE / 1024) . ' KB';
            //                     $error_detail[] = 'The size of the file "' . $uploadedFileName . '" must be of max. ' . (UPLOAD_MAX_FILE_SIZE / 1024) . ' KB';
            //                 }
            //             }
            //         }
            //     }
            // }

            /*
            * Combine descriptions into an array.
            */
            // $descriptions[] = $description1;
            // $descriptions[] = $description2;
        

            /*
            * Save product and images.
            */
            // if (!isset($error_detail)) {
                // $lastInsertId = $dbConnection->insert_id;


                // /*
                // * Save a record for each uploaded file.
                // */

                // $image_number = 1;

                // foreach ($filenamesToSave as $filename) {
                    
                //     $add_image_query = "INSERT INTO productimage (
                //                         productID,
                //                         image_url,
                //                         image_number
                //                         ) VALUES (
                //                         ?, ?, ?
                //                         )";

                //     $statement = $dbConnection->prepare($add_image_query);

                //     $statement->bind_param('isi', $lastInsertId, $filename, $image_number);

                //     $statement->execute();

                //     $statement->close();

                //     //$image_number++;
                

            //     $_POST['sumbit'] = $productName = $category = $price = $stock = $descriptions = $description1 = $description2 = $image_number = $property_number = NULL;

            // }
        

    ?>

    <style type="text/css">
        .product-header {
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
            height: 100%;
            object-fit: cover;
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
        .edit-card-body {
            margin-bottom: 15px;
            margin-top: 25px;
        }
        .edit-cancel-box {
            padding-right: 0px;
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
        a {
        text-decoration: none;
        color: black;
        }
    </style>

    <div class="content">

        <!-- print data -->

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <?php
                            $detail = mysqli_fetch_array($PSetResult1);
                        ?>
                        <form method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-header">
                                Product No.<?php echo $detail['productID'] ?>
                                <input type="hidden" id="productID" name="productID" value="<?php echo $detail['productID'] ?>">
                            </div>
                            <div class="card-body edit-card-body">
                                <div class="form-group row mb-3">
                                    <label for="edit-name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>
                                    <div class="col-sm-6">
                                        <input type="hidden" id="old-name" name="old-name" value="<?php echo $detail['productName'] ?>">
                                        <input id="edit-name" type="text" class="form-control @if (isset($error_name)) is-invalid @endif" name="edit-name" placeholder="<?php echo $detail['productName'] ?>" autofocus>
                                        <?php
                                            if (isset($error_name)) {
                                                ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                                    echo implode('<br/>', $error_name);
                                                ?> </strong></span> <?php
                                            }
                                        ?>                                   
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="edit-category" class="col-md-4 col-form-label text-md-end">{{ __('Category') }}</label>
                                    <div class="col-sm-6">
                                        <input type="hidden" id="old-category" name="old-category" value="<?php echo $detail['category'] ?>">
                                        <select id="edit-category" class="form-select @if (isset($error_category)) is-invalid @endif" name="edit-category">
                                            <option value="0" disabled>Category select</option>
                                            <?php $category_qurey = "SELECT * FROM category ORDER BY categoryName";
                                                    $category_get = $dbConnection->prepare($category_qurey);
                                                    $category_get->execute();
                                                    $category_result = $category_get->get_result();
                                                    while ($category_table = mysqli_fetch_array($category_result)) {
                                                        ?> <option value="{{ $category_table['categoryID'] }}" {{ $detail['category'] == $category_table['categoryID'] ? "selected" : "" }}>{{ $category_table['categoryName'] }}</option> <?php                                                    
                                                    }
                                            ?>
                                        </select>
                                        <?php
                                            if (isset($error_category)) {
                                                ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                                echo $error_category;
                                                ?> </strong></span> <?php
                                            }
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="edit-price" class="col-md-4 col-form-label text-md-end">{{ __('Price') }}</label>
                                    <div class="col-md-6">
                                        <input type="hidden" id="old-price" name="old-price" value="<?php echo $detail['price'] ?>">
                                        <input id="edit-price" type="number" step="any" class="form-control @if (isset($error_price)) is-invalid @endif" name="edit-price" placeholder="<?php echo $detail['price'] ?>" min="0">
                                        <?php
                                            if (isset($error_price)) {
                                                ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                                echo $error_price;
                                                ?> </strong></span> <?php
                                            }
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label for="edit-stock" class="col-md-4 col-form-label text-md-end">{{ __('Stock') }}</label>
                                    <div class="col-md-6">
                                        <input type="hidden" id="old-stock" name="old-stock" value="<?php echo $detail['stock'] ?>">
                                        <input id="edit-stock" type="number" class="form-control @if (isset($error_stock)) is-invalid @endif" name="edit-stock" placeholder="<?php echo $detail['stock'] ?>" min="0">
                                        <?php
                                            if (isset($error_stock)) {
                                                ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                                echo $error_stock;
                                                ?> </strong></span> <?php
                                            }
                                        ?>
                                    </div>
                                </div>
                                <?php
                                    while ($detail = mysqli_fetch_array($PSetResult2)){ ?>
                                        <div class="form-group row mb-3">
                                            <label for="edit-description-<?php echo $detail['property_number'] ?>" class="col-md-4 col-form-label text-md-end">Description <?php echo $detail['property_number'] ?></label>
                                            <div class="col-md-6">
                                                <input type="hidden" id="old-description-<?php echo $detail['property_number'] ?>" name="old-description-<?php echo $detail['property_number'] ?>" value="<?php echo $detail['detail_description'] ?>">
                                                <input id="edit-description-<?php echo $detail['property_number'] ?>" type="text" class="form-control @if (isset($error_description1)) is-invalid @endif" name="edit-description-<?php echo $detail['property_number'] ?>" placeholder="<?php echo $detail['detail_description'] ?>">
                                                <?php
                                                    if (isset($error_description[$detail['property_number']])) {
                                                        ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                                        echo $error_description[$detail['property_number']];
                                                        ?> </strong></span> <?php
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    <?php };
                                ?>
                                <div class="row mb-0 justify-content-end">
                                    <div class="col-md-auto edit-cancel-box">
                                        <button id="submit" type="submit" name="edit-cancel" class="btn btn-primary edit edit-cancel">
                                            {{ __('Cancel') }}
                                        </button>
                                    </div>
                                    <div class="col-md-auto">
                                        <button id="submit" type="submit" name="edit-confirm" class="btn btn-primary edit edit-confirm">
                                            {{ __('Confirm') }}
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>                                    
                </div>
            </div>
        </div>
    </div>



@endsection
