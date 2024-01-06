<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    setcookie('user_id', create_unique_id(), time() + 60 * 60 * 24 * 30);
}

if(isset($_POST['update_cart'])) {

    $cart_id = $_POST['cart_id'];
    $cart_id = filter_var($cart_id);

    $qty = $_POST['qty'];
    $qty = filter_var($qty,);

    $update_cart = $conn->prepare("UPDATE carts SET qty= ? WHERE id = ?");
    $update_cart->execute([$qty, $cart_id]);
    $success_msg[] = "Cart Quantity Updated";
}

if(isset($_POST['delete_cart'])) {

    $cart_id = $_POST['cart_id'];
    $cart_id = filter_var($cart_id);

    $verify_delete_item = $conn->prepare("SELECT * FROM carts WHERE id =?");
    $verify_delete_item->execute([$cart_id]);

    if($verify_delete_item->rowCount() > 0) {
        $delete_item = $conn->prepare("DELETE FROM carts WHERE id = ?");
        $delete_item->execute([$cart_id]);
        $success_msg[] = 'Cart item removed!';
    } else {
        $warning_mg[] = 'Cart already deleted';
    }
}

if(isset($_POST['empty-cart'])) {
    $verify_empty_cart = $conn->prepare("SELECT * FROM carts WHERE user_id = ?");
    $verify_empty_cart->execute([$user_id]);
    if($verify_empty_cart->rowCount() > 0) {
        $empty_cart = $conn->prepare("DELETE FROM carts WHERE user_id=?");
        $empty_cart->execute([$user_id]);
        $success_msg[] = 'Your Cart is Empty Now!';
    } else {
        $warning_mg[] = 'Your Cart is already Empty!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <!-- fontawesome cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- custom css -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'components/header.php'; ?>

    <section class="products">
        <h1 class="heading">shopping cart</h1>
        <div class="box-container">
        <?php
        $grand_total = 0;
        $select_cart = $conn->prepare('SELECT * FROM carts WHERE user_id = ?');
        $select_cart->execute([$user_id]);
        ?>
        <?php if ($select_cart->rowCount() > 0) : ?>
            <?php while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) : ?>
                <?php
                $select_products = $conn->prepare("SELECT * FROM products WHERE id = ?");
                $select_products->execute([$fetch_cart['product_id']]);
                ?>
                <?php if ($select_products->rowCount() > 0) : ?>
                    <?php while ($fetch_product = $select_products->fetch((PDO::FETCH_ASSOC))) : ?>
                
                        <form action="" method="post" class="box">
                            <input type="hidden" name="cart_id" value="<?= $fetch_cart['id'] ?>">
                            <img src="uploaded_files/<?= $fetch_product['image'] ?>" alt="" class="image">
                            <h3 class="name"><?= $fetch_product['name'] ?></h3>
                            <div class="flex">
                                <p class="price"><i class="fa-solid fa-dollar-sign"><?= $fetch_product['price'] ?></i></p>
                                <input type="number" name="qty" maxlength="2" min="1" max="99" class="qty" value="<?= $fetch_cart['qty'] ?>">
                                <button type="submit" class="fa-solid fa-pen" name="update_cart"></button>
                            </div>
                            <p class="sub-total">sub total : <span><i class="fa-solid fa-dollar-sign"></i><?= $sub_total= ($fetch_product['price'] * $fetch_cart['qty']) ?></span></p>
                            <input type="submit" class="delete-btn" value="delete item" name="delete_cart" onclick="return confirm('Are you sure delete this item')">
                        </form>
                        <?php $grand_total += $sub_total; ?>

                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty">No Products!!!</p>        
                <?php endif ?>
            <?php endwhile ?>
        <?php else : ?>
            <p class="empty">shopping cart is empty !</p>
        <?php endif; ?>
        </div>
        <?php
            if($grand_total != 0) {
                ?>
                <div class="grand-total">
                    <p>grand total : <span><?= $grand_total ?></span></p>
                    <a href="checkout.php" class="btn">proceed to checkout</a>
                    <form action="" method="post">
                        <input type="submit" value="empty cart" name="empty-cart" class="delete-btn" onclick="return confirm('Empty Your Cart ?')">
                    </form>
                </div>
                <?php
            }
        ?>
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