<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    setcookie('user_id', create_unique_id(), time() + 60 * 60 * 24 * 30);
}

if(isset($_POST['add_to_cart'])) {

    $id = create_unique_id();

    $product_id = $_POST['product_id'];
    $product_id = filter_var($product_id);

    $qty = $_POST['qty'];
    $qty = filter_var($qty);

    $verify_cart = $conn->prepare("SELECT * FROM carts WHERE user_id = ? AND product_id = ?");
    $verify_cart->execute([$user_id, $product_id]);

    $max_cart_items = $conn->prepare('SELECT * FROM carts WHERE user_id = ?');
    $max_cart_items->execute([$user_id]);
    
    if($verify_cart->rowCount() > 0) {
        print_r('more than 10');
        $warning_msg[] = 'Already added to Cart!';
    } elseif ($max_cart_items->rowCount() == 10) {
        print_r('equal to 0');
        $warning_msg[] = 'Cart is Full';
    } else {
        $select_p = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
        $select_p->execute([$product_id]);
        $fetch_p = $select_p->fetch(PDO::FETCH_ASSOC);
        print_r($fetch_p['price']);
        
        $insert_cart = $conn->prepare("INSERT INTO carts (id, user_id, product_id, price, qty) VALUES (?, ?, ?, ?, ?)");
        $insert_cart->execute([$id, $user_id, $product_id, $fetch_p['price'], $qty]);
        
        $success_msg[] = 'Added to cart!';
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Product</title>
    <!-- fontawesome cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- custom css -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'components/header.php'; ?>

    <section class="products">
        <h1 class="heading">all products</h1>
        <div class="box-container">
            <?php
            $select_products = $conn->prepare("SELECT * FROM products");
            $select_products->execute();
            ?>
            <?php if ($select_products->rowCount() > 0) : ?>
                <?php while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) : ?>
                    <form action="" method="post" class="box">
                        <input type="hidden" name="product_id" value="<?= $fetch_product['id']; ?>">
                        <img src="uploaded_files/<?= $fetch_product['image'] ?>" alt="" class="image">
                        <h3 class="name"><?= $fetch_product['name']; ?></h3>
                        <div class="flex">
                            <p class="price"><i class="fa-solid fa-dollar-sign"><?= $fetch_product['price']; ?></i></p>
                            <input type="number" name="qty" maxlength="2" min="1" max="99" class="qty" value="1">
                        </div>
                        <a href="checkout.php?get_id=<?= $fetch_product['id']; ?>" class="delete-btn">Buy Now</a>
                        <input type="submit" value="add to cart" name="add_to_cart" class="btn">
                    </form>
                <?php endwhile; ?>
            <?php else : ?>
                <p class="empty">No Products Found</p>
            <?php endif; ?>
        </div>
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