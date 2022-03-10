@extends('layouts.app')

@section('content')

    <?php
    
        include("./php_file/config.php");
        include("./php_file/dbConnect.php");

        $productSaved = FALSE;

        if (isset($_POST['submit'])) {
            /*
            * Read posted values.
            */
            $productName = isset($_POST['name']) ? $_POST['name'] : '';
            $category = isset($_POST['category']) ? $_POST['category'] : '';
            $price = isset($_POST['price']) ? $_POST['price'] : '';
            $stock = isset($_POST['stock']) ? $_POST['stock'] : '';
            $description1 = isset($_POST['description1']) ? $_POST['description1'] : '';
            $description2 = isset($_POST['description2']) ? $_POST['description2'] : '';


            /*
            * Validate posted values.
            */
            
            $productName_unique_query = "SELECT productName FROM product
                                            WHERE productName = '$productName'";
            $statement = $dbConnection->prepare($productName_unique_query);
            $statement->execute();
            $productName_unique_result = mysqli_fetch_array($statement->get_result());

            if ($productName_unique_result) {
                $error_name[] = 'This product name has already been taken. Please try another one.';
                $error_detail[] = 'This product name has already been taken. Please try another one.';
            }
            
            if (empty($productName)) {
                $error_name[] = 'Please provide the product name.';
                $error_detail[] = 'Please provide the product name.';
            }

            if (empty($category)||($category=="0")) {
                $error_category = 'Please select the category.';
                $error_detail[] = $error_category;
            }

            if (empty($price)) {
                $error_price = 'Please provide the price.';
                $error_detail[] = $error_price;
            }

            if (empty($stock)) {
                $error_stock = 'Please provide the stock quantity.';
                $error_detail[] = $error_stock;
            }

            if (empty($description1)) {
                $error_description1 = 'Please provide the descriptions.';
                $error_detail[] = $error_description1;
            }

            if (empty($description2)) {
                $error_description2 = 'Please provide the descriptions.';
                $error_detail[] = $error_description2;
            }

            /*
            * Create "image" directory if it doesn't exist.
            */
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0777, true);
            }

            /*
            * List of file _strings to be filled in by the upload script
            * below and to be saved in the db table "products_images" afterwards.
            */
            $file_stringsToSave = [];

            $allowedMimeTypes = explode(',', UPLOAD_ALLOWED_MIME_TYPES);

            /*
            * Upload files.
            */
            if (!empty($_FILES)) {
                if (isset($_FILES['file']['error'])) {
                    foreach ($_FILES['file']['error'] as $uploadedFileKey => $uploadedFileError) {
                        if ($uploadedFileError === UPLOAD_ERR_NO_FILE) {
                            $error_file[] = 'Please upload the thumbnail image file.';
                            $error_detail[] = 'Please upload the thumbnail image file.';
                        } elseif ($uploadedFileError === UPLOAD_ERR_OK) {
                            $uploadedFileName = basename($_FILES['file']['name'][$uploadedFileKey]);

                            if ($_FILES['file']['size'][$uploadedFileKey] <= UPLOAD_MAX_FILE_SIZE) {
                                $uploadedFileType = $_FILES['file']['type'][$uploadedFileKey];
                                $uploadedFileTempName = $_FILES['file']['tmp_name'][$uploadedFileKey];

                                $uploadedFilePath = rtrim(UPLOAD_DIR, '/') . '/' . $uploadedFileName;

                                if (in_array($uploadedFileType, $allowedMimeTypes)) {
                                    if (!move_uploaded_file($uploadedFileTempName, $uploadedFilePath)) {
                                        $error_file[] = 'The file "' . $uploadedFileName . '" could not be uploaded.';
                                        $error_detail[] = 'The file "' . $uploadedFileName . '" could not be uploaded.';
                                    } else {
                                        $filenamesToSave[] = $uploadedFilePath;
                                    }
                                } else {
                                    $error_file[] = 'The extension of the file "' . $uploadedFileName . '" is not valid. Allowed extensions: JPG, JPEG, PNG, or GIF.';
                                    $error_detail[] = 'The extension of the file "' . $uploadedFileName . '" is not valid. Allowed extensions: JPG, JPEG, PNG, or GIF.';
                                }
                            } else {
                                $error_file[] = 'The size of the file "' . $uploadedFileName . '" must be of max. ' . (UPLOAD_MAX_FILE_SIZE / 1024) . ' KB';
                                $error_detail[] = 'The size of the file "' . $uploadedFileName . '" must be of max. ' . (UPLOAD_MAX_FILE_SIZE / 1024) . ' KB';
                            }
                        }
                    }
                }
            }

            /*
            * Combine descriptions into an array.
            */
            $descriptions[] = $description1;
            $descriptions[] = $description2;
        

            /*
            * Save product and images.
            */
            if (!isset($error_detail)) {
                /*
                * The SQL statement to be prepared. Notice the so-called markers,
                * e.g. the "?" signs. They will be replaced later with the
                * corresponding values when using mysqli_stmt::bind_param.
                *
                * @link http://php.net/manual/en/mysqli.prepare.php
                */
            
                $add_product_query = "INSERT INTO product (
                                        productName,
                                        category,
                                        price,
                                        stock
                                    ) VALUES (
                                        ?, ?, ?, ?
                                    )";

                /*
                * Prepare the SQL statement for execution - ONLY ONCE.
                *
                * @link http://php.net/manual/en/mysqli.prepare.php
                */
                $statement = $dbConnection->prepare($add_product_query);

                /*
                * Bind variables for the parameter markers (?) in the
                * SQL statement that was passed to prepare(). The first
                * argument of bind_param() is a string that contains one
                * or more characters which specify the types for the
                * corresponding bind variables.
                *
                * @link http://php.net/manual/en/mysqli-stmt.bind-param.php
                */
                $statement->bind_param('sisi', $productName, $category, $price, $stock);

                /*
                * Execute the prepared SQL statement.
                * When executed any parameter markers which exist will
                * automatically be replaced with the appropriate data.
                *
                * @link http://php.net/manual/en/mysqli-stmt.execute.php
                */
                $statement->execute();

                // Read the id of the inserted product.
                $lastInsertId = $dbConnection->insert_id;

                /*
                * Close the prepared statement. It also deallocates the statement handle.
                * If the statement has pending or unread results, it cancels them
                * so that the next query can be executed.
                *
                * @link http://php.net/manual/en/mysqli-stmt.close.php
                */
                $statement->close();

                /*
                * Save a record for each uploaded file.
                */

                $image_number = 1;

                foreach ($filenamesToSave as $filename) {
                    
                    $add_image_query = "INSERT INTO productimage (
                                        productID,
                                        image_url,
                                        image_number
                                        ) VALUES (
                                        ?, ?, ?
                                        )";

                    $statement = $dbConnection->prepare($add_image_query);

                    $statement->bind_param('isi', $lastInsertId, $filename, $image_number);

                    $statement->execute();

                    $statement->close();

                    //$image_number++;
                }

                /*
                * Save the descriptions.
                */

                $property_number = 1;
                foreach ($descriptions as $description) {
                    $add_description_query = "INSERT INTO productproperty (
                                                productID,
                                                detail_description,
                                                property_number
                                            ) VALUES (
                                                ?, ?, ?
                                            )";
                    $statement = $dbConnection->prepare($add_description_query);
                    $statement->bind_param('isi', $lastInsertId, $description, $property_number);
                    $statement->execute();
                    $statement->close();
                    $property_number++;
                }

                /*
                * Close the previously opened database connection.
                *
                * @link http://php.net/manual/en/mysqli.close.php
                */
                // $dbConnection->close();

                $productSaved = TRUE;

                /*
                * Reset the posted values, so that the default ones are now showed in the form.
                * See the "value" attribute of each html input.
                */
                $_POST['sumbit'] = $productName = $category = $price = $stock = $descriptions = $description1 = $description2 = $image_number = $property_number = NULL;

                header("location:http://localhost:8000/products/".$lastInsertId); exit;
            }
        }

    ?>
                
    <style type="text/css">
        .container .card-body {
            margin-bottom: 15px;
        }
        .container .form-text, .container .card-body {
            margin-top: 25px;
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Add A Product') }}</div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @if (isset($error_name)) is-invalid @endif" name="name" placeholder="Product name" value="<?php echo isset($productName) ? $productName : ''; ?>" autofocus>
                                    <?php
                                        if (isset($error_name)) {
                                            ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                                echo implode('<br/>', $error_name);
                                            ?> </strong></span> <?php
                                        }
                                    ?>                                   
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="category" class="col-md-4 col-form-label text-md-end">{{ __('Category') }}</label>
                                <div class="col-md-6">
                                    <select id="category" class="form-select @if (isset($error_category)) is-invalid @endif" name="category" value="">
                                        <option value="0">Category select</option>
                                        <?php $category_qurey = "SELECT * FROM category ORDER BY categoryName";
                                                $category_get = $dbConnection->prepare($category_qurey);
                                                $category_get->execute();
                                                $category_result = $category_get->get_result();
                                                while ($category_table = mysqli_fetch_array($category_result)) {
                                                    ?> <option value="{{ $category_table['categoryID'] }}" {{ ( isset($category) ? $category : '' ) == $category_table['categoryID'] ? "selected" : "" }}>{{ $category_table['categoryName'] }}</option> <?php                                                    
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

                            <div class="row mb-3">
                                <label for="price" class="col-md-4 col-form-label text-md-end">{{ __('Price') }}</label>
                                <div class="col-md-6">
                                    <input id="price" type="number" step="any" class="form-control @if (isset($error_price)) is-invalid @endif" name="price" placeholder="Price" min="0" value="<?php echo isset($price) ? $price : ''; ?>">
                                    <?php
                                        if (isset($error_price)) {
                                            ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                            echo $error_price;
                                            ?> </strong></span> <?php
                                        }
                                    ?>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="stock" class="col-md-4 col-form-label text-md-end">{{ __('Stock') }}</label>
                                <div class="col-md-6">
                                    <input id="stock" type="number" class="form-control @if (isset($error_stock)) is-invalid @endif" name="stock" placeholder="Stock quantity" min="0" value="<?php echo isset($stock) ? $stock : ''; ?>">
                                    <?php
                                        if (isset($error_stock)) {
                                            ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                            echo $error_stock;
                                            ?> </strong></span> <?php
                                        }
                                    ?>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="description1" class="col-md-4 col-form-label text-md-end">{{ __('Description 1') }}</label>
                                <div class="col-md-6">
                                    <input id="description1" type="text" class="form-control @if (isset($error_description1)) is-invalid @endif" name="description1" placeholder="Description 1" value="<?php echo isset($description1) ? $description1 : ''; ?>">
                                    <?php
                                        if (isset($error_description1)) {
                                            ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                            echo $error_description1;
                                            ?> </strong></span> <?php
                                        }
                                    ?>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="description2" class="col-md-4 col-form-label text-md-end">{{ __('Description 2') }}</label>
                                <div class="col-md-6">
                                    <input id="description2" type="text" class="form-control @if (isset($error_description2)) is-invalid @endif" name="description2" placeholder="Description 2" value="<?php echo isset($description2) ? $description2 : ''; ?>">
                                    <?php
                                        if (isset($error_description2)) {
                                            ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                            echo $error_description2;
                                            ?> </strong></span> <?php
                                        }
                                    ?>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="file" class="col-md-4 col-form-label text-md-end">{{ __('Thumbnail Image') }}</label>
                                <div class="col-md-6">
                                    <input id="file" type="file" class="form-control @if (isset($error_file)) is-invalid @endif" name="file[]">
                                    <?php
                                        if (isset($error_file)) {
                                            ?> <span class="invalid-feedback" role="alert" style="display:block"><strong> <?php
                                            echo implode('<br/>', $error_file);
                                            ?> </strong></span> <?php
                                        }
                                    ?>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button id="submit" type="submit" name="submit" class="btn btn-primary">
                                        {{ __('Add Product') }}
                                    </button>
                                </div>
                            </div>
                        </form>

                        <?php
                            if ($productSaved) {
                                ?>
                                    <div class="form-text"><a href="<?php echo $lastInsertId; ?>" class="link-to-product-details">
                                        Click me to see the saved product details in <b>productDetailPage.php</b> (product id: <b><?php echo $lastInsertId; ?></b>)
                                    </a></div>
                                <?php
                                $productSaved = FALSE;
                            }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
