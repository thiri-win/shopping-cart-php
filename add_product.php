<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    setcookie('user_id', create_unique_id(), time() + 60 * 60 * 24 * 30);
}

if (isset($_POST['add_product'])) {

    $id = create_unique_id();

    $name = $_POST['name'];
    // $name = filter_var($name);

    $price = $_POST['price'];
    // $price = filter_var($price);

    $image = $_FILES['image'];
    $image = filter_var($image);
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename = create_unique_id() . '.' . $ext;
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_folder = 'uploaded_files/' . $rename;

    if ($image_size > 2000000) {
        
        $warning_msg[] = "Image size is more than 2000000";
        // print_r($warning_msg);
    } else {
        // echo 'ok';
        $insert_product = $conn->prepare("INSERT INTO products (id, name, price, image) VALUES (?, ?, ?, ?)");
        $insert_product->execute([$id, $name, $price, $rename]);
        $success_msg[] = 'Product uploaded!';
        // print_r($success_msg);
        move_uploaded_file($image_tmp_name, $image_folder);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Products</title>
    <!-- fontawesome cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- custom css -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'components/header.php'; ?>

    <section class="add-product">
        <form action="" method="post" enctype="multipart/form-data">

            <h3>product details</h3>

            <p>product name <span>*</span></p>
            <input type="text" class="box" name="name" maxlength="50" placeholder="enter product name" required>

            <p>product price <span>*</span></p>
            <input type="number" class="box" name="price" min="0" max="99999" placeholder="enter product price" required>

            <p>product image <span>*</span></p>
            <input type="file" name="image" class="box" accept="image/*">

            <input type="submit" value="add product" class="btn" name="add_product">

        </form>
    </section>

    <!-- sweet alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- custom js -->
    <script src="js/script.js"></script>

    <?php
    include 'components/alert.php';
    ?>

</body>

</html>