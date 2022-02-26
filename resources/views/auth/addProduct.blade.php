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
    $price = isset($_POST['price']) ? $_POST['price'] : 0;
    $stock = isset($_POST['stock']) ? $_POST['stock'] : 0;
    $description1 = isset($_POST['description1']) ? $_POST['description1'] : '';
    $description2 = isset($_POST['description2']) ? $_POST['description2'] : '';


    /*
     * Validate posted values.
     */
    if (empty($productName)) {
        $error_detail[] = 'Please provide the product name.';
    }

    if (empty($category)) {
        $error_detail[] = 'Please select the category.';
    }

    if ($price == 0) {
        $error_detail[] = 'Please provide the price.';
    }

    if ($stock == 0) {
        $error_detail[] = 'Please provide the stock quantity.';
    }

    if (empty(($description1)&&($description2))) {
        $error_detail[] = 'Please provide the descriptions.';
    }

    /*
     * Create "uploads" directory if it doesn't exist.
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
                    $error_detail[] = 'You did not provide any files.';
                } elseif ($uploadedFileError === UPLOAD_ERR_OK) {
                    $uploadedFileName = basename($_FILES['file']['name'][$uploadedFileKey]);

                    if ($_FILES['file']['size'][$uploadedFileKey] <= UPLOAD_MAX_FILE_SIZE) {
                        $uploadedFileType = $_FILES['file']['type'][$uploadedFileKey];
                        $uploadedFileTempName = $_FILES['file']['tmp_name'][$uploadedFileKey];

                        $uploadedFilePath = rtrim(UPLOAD_DIR, '/') . '/' . $uploadedFileName;

                        if (in_array($uploadedFileType, $allowedMimeTypes)) {
                            if (!move_uploaded_file($uploadedFileTempName, $uploadedFilePath)) {
                                $error_detail[] = 'The file "' . $uploadedFileName . '" could not be uploaded.';
                            } else {
                                $filenamesToSave[] = $uploadedFilePath;
                            }
                        } else {
                            $error_detail[] = 'The extension of the file "' . $uploadedFileName . '" is not valid. Allowed extensions: JPG, JPEG, PNG, or GIF.';
                        }
                    } else {
                        $error_detail[] = 'The size of the file "' . $uploadedFileName . '" must be of max. ' . (UPLOAD_MAX_FILE_SIZE / 1024) . ' KB';
                    }
                }
            }
        }
    }

    /*
     * Combine descriptions into an array.
     */
    // $descriptions[] = array($description1, $description2);
    

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
        $add_description_query = "INSERT INTO productproperty (
                                    productID,
                                    detail_description,
                                    property_number
                                ) VALUES (
                                    ?, ?, ?
                                )";
        $statement = $dbConnection->prepare($add_description_query);
        $statement->bind_param('isi', $lastInsertId, $description1, $property_number);
        $statement->execute();
        $statement->close();

        $property_number = 2;
        $add_description_query = "INSERT INTO productproperty (
                                    productID,
                                    detail_description,
                                    property_number
                                ) VALUES (
                                    ?, ?, ?
                                )";
        $statement = $dbConnection->prepare($add_description_query);
        $statement->bind_param('isi', $lastInsertId, $description2, $property_number);
        $statement->execute();
        $statement->close();


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
        $productName = $category = $price = $stock = $descriptions = $description1 = $description2 = $image_number = $property_number = NULL;
    }
}


?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta _string="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
        <meta charset="UTF-8" />
        <!-- The above 3 meta tags must come first in the head -->

        <title>Save product details</title>

        <script src="https://code.jquery.com/jquery-3.2.1.min.js" type="text/javascript"></script>
        <style type="text/css">
            body {
                padding: 30px;
            }

            .form-container {
                margin-left: 80px;
            }

            .form-container .messages {
                margin-bottom: 15px;
            }

            .form-container input[type="text"],
            .form-container input[type="number"],
            .form-container select {
                display: block;
                margin-bottom: 15px;
                width: 150px;
            }

            .form-container input[type="file"] {
                margin-bottom: 15px;
            }

            .form-container label {
                display: inline-block;
                float: left;
                width: 100px;
            }

            .form-container button {
                display: block;
                padding: 5px 10px;
                background-color: #8daf15;
                color: #fff;
                border: none;
            }

            .form-container .link-to-product-details {
                margin-top: 20px;
                display: inline-block;
            }
        </style>

    </head>
    <body>

        <div class="form-container">
            <h2>Add a product</h2>

            <div class="messages">
                <?php
                if (isset($error_detail)) {
                    // echo $error_detail;
                    echo implode('<br/>', $error_detail);
                } elseif ($productSaved) {
                    echo 'The product details were successfully saved.';
                }
                ?>
            </div>

            <form action="addProduct" method="post" enctype="multipart/form-data">
                @csrf
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo isset($productName) ? $productName : ''; ?>">

                <label for="category">Category: </label>
                <select id="category" name="category" value="<?php echo isset($category) ? $category : ''; ?>">
                    <?php $category_qurey = "SELECT * FROM category";
                            $category_get = $dbConnection->prepare($category_qurey);
                            $category_get->execute();
                            $category_result = $category_get->get_result();
                            while ($category_table = mysqli_fetch_array($category_result)) {
                                echo "<option value='{$category_table['categoryID']}'>{$category_table['categoryName']}</option>";
                            }
                    ?>
                </select>

                <label for="price">Price: </label>
                <input type="number" id="price" name="price" min="0" value="<?php echo isset($price) ? $price : '0'; ?>">

                <label for="stock">Stock: </label>
                <input type="number" id="stock" name="stock" min="0" value="<?php echo isset($stock) ? $stock : '0'; ?>">

                <label for="description1">Description 1: </label>
                <input type="text" id="description1" name="description1" value="<?php echo isset($description1) ? $description1 : ''; ?>">

                <label for="description2">Description 2: </label>
                <input type="text" id="description2" name="description2" value="<?php echo isset($description2) ? $description2 : ''; ?>">

                <label for="file">Image: </label>
                <input type="file" id="file" name="file[]" multiple>

                <button type="submit" id="submit" name="submit" class="button">
                    Submit
                </button>
            </form>

            <?php
            if ($productSaved) {
                ?>
                <a href="products/<?php echo $lastInsertId; ?>" class="link-to-product-details">
                    Click me to see the saved product details in <b>productDetailPage.php</b> (product id: <b><?php echo $lastInsertId; ?></b>)
                </a>
                <?php
            }
            ?>
        </div>

    </body>
</html>
