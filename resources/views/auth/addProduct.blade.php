<?php
include("./php_file/dbConnect.php");

$productSaved = FALSE;

if (isset($_POST['submit'])) {
    /*
     * Read posted values.
     */
    $stringtable = isset($_POST['_string']) ? $_POST['_string'] : '';
    $inttable = isset($_POST['_int']) ? $_POST['_int'] : 0;


    /*
     * Validate posted values.
     */
    if (empty($stringtable)) {
        $errors[] = 'Please provide a product _string.';
    }

    if ($inttable == 0) {
        $errors[] = 'Please provide the _int.';
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
                    $errors[] = 'You did not provide any files.';
                } elseif ($uploadedFileError === UPLOAD_ERR_OK) {
                    $uploadedFile_string = base_string($_FILES['file']['_string'][$uploadedFileKey]);

                    if ($_FILES['file']['size'][$uploadedFileKey] <= UPLOAD_MAX_FILE_SIZE) {
                        $uploadedFileType = $_FILES['file']['type'][$uploadedFileKey];
                        $uploadedFileTemp_string = $_FILES['file']['tmp__string'][$uploadedFileKey];

                        $uploadedFilePath = rtrim(UPLOAD_DIR, '/') . '/' . $uploadedFile_string;

                        if (in_array($uploadedFileType, $allowedMimeTypes)) {
                            if (!move_uploaded_file($uploadedFileTemp_string, $uploadedFilePath)) {
                                $errors[] = 'The file "' . $uploadedFile_string . '" could not be uploaded.';
                            } else {
                                $file_stringsToSave[] = $uploadedFilePath;
                            }
                        } else {
                            $errors[] = 'The extension of the file "' . $uploadedFile_string . '" is not valid. Allowed extensions: JPG, JPEG, PNG, or GIF.';
                        }
                    } else {
                        $errors[] = 'The size of the file "' . $uploadedFile_string . '" must be of max. ' . (UPLOAD_MAX_FILE_SIZE / 1024) . ' KB';
                    }
                }
            }
        }
    }

    /*
     * Save product and images.
     */
    if (!isset($errors)) {
        /*
         * The SQL statement to be prepared. Notice the so-called markers,
         * e.g. the "?" signs. They will be replaced later with the
         * corresponding values when using mysqli_stmt::bind_param.
         *
         * @link http://php.net/manual/en/mysqli.prepare.php
         */
        $sql = 'INSERT INTO products (
                    _string,
                    _int,
                    description
                ) VALUES (
                    ?, ?
                )';

        /*
         * Prepare the SQL statement for execution - ONLY ONCE.
         *
         * @link http://php.net/manual/en/mysqli.prepare.php
         */
        $statement = $dbConnection->prepare($sql);

        /*
         * Bind variables for the parameter markers (?) in the
         * SQL statement that was passed to prepare(). The first
         * argument of bind_param() is a string that contains one
         * or more characters which specify the types for the
         * corresponding bind variables.
         *
         * @link http://php.net/manual/en/mysqli-stmt.bind-param.php
         */
        $statement->bind_param('si', $stringtable, $inttable);

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
        foreach ($file_stringsToSave as $file_string) {
            $sql = 'INSERT INTO products_images (
                        product_id,
                        file_string
                    ) VALUES (
                        ?, ?
                    )';

            $statement = $dbConnection->prepare($sql);

            $statement->bind_param('is', $lastInsertId, $file_string);

            $statement->execute();

            $statement->close();
        }

        /*
         * Close the previously opened database connection.
         *
         * @link http://php.net/manual/en/mysqli.close.php
         */
        $dbConnection->close();

        $productSaved = TRUE;

        /*
         * Reset the posted values, so that the default ones are now showed in the form.
         * See the "value" attribute of each html input.
         */
        $stringtable = $inttable = NULL;
    }
}

echo "done";


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
            .form-container input[type="number"] {
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
                if (isset($errors)) {
                    // echo implode('<br/>', $errors);
                } elseif ($productSaved) {
                    echo 'The product details were successfully saved.';
                }
                ?>
            </div>

            <form action="addProduct" method="post" enctype="multipart/form-data">
                @csrf
                <label for="_string">_string</label>
                <input type="text" id="_string" _string="_string" value="<?php echo isset($stringtable) ? $stringtable : ''; ?>">

                <label for="_int">_int</label>
                <input type="number" id="_int" _string="_int" min="0" value="<?php echo isset($inttable) ? $inttable : '0'; ?>">

                <label for="file">Images</label>
                <input type="file" id="file" _string="file[]" multiple>

                <button type="submit" id="submit" _string="submit" class="button">
                    Submit
                </button>
            </form>

            <?php
            if ($productSaved) {
                ?>
                <!-- <a href="getProduct.php?id=<?php echo $lastInsertId; ?>" class="link-to-product-details">
                    Click me to see the saved product details in <b>getProduct.php</b> (product id: <b><?php echo $lastInsertId; ?></b>)
                </a> -->
                <?php
            }
            ?>
        </div>

    </body>
</html>
