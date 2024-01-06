<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    setcookie('user_id', create_unique_id(), time() + 60 * 60 * 24 * 30);
}

if(isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
} else {
    $get_id = '';
}

if(isset($_POST['place-order'])) {

    $name = $_POST['name'];
    $name = filter_var($name);

    $number = $_POST['number'];
    $number = filter_var($number);

    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    $method = $_POST['method'];
    $method = filter_var($method);

    $address = $_POST['home-no'].",".$_POST['street'].",".$_POST['quarter'].",".$_POST['township'].",".$_POST['city'];
    $address = filter_var($address);

    $verify_cart = $conn->prepare("SELECT * FROM carts WHERE user_id=?");
    $verify_cart->execute([$user_id]);

    if(isset($_GET['get_id'])) {
        $get_product = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $get_product->execute([$_GET['get_id']]);
        
        if($get_product ->rowCount() > 0) {
            while($fetch_p = $get_product->fetch(PDO::FETCH_ASSOC)) {
                $insert_order = $conn->prepare("INSERT INTO orders (id, user_id, name, number, email, address, method, product_id, price, qty, date, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?) " );    
                $insert_order->execute([create_unique_id(), $user_id, $name, $number, $email, $address, $method, $fetch_p['id'], $fetch_p['price'], 1, date('m/d/y'), 'in delivering']);
                $success_msg[] = 'Your order is received now!';
                header('location: orders.php');
            }
        } else {
            $warning_msg[] = "Something went wrong!";
        }
    } elseif ($verify_cart->rowCount() > 0) {
        while($f_cart = $verify_cart->fetch(PDO::FETCH_ASSOC)) {

            $get_price = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $get_price->execute([$f_cart['product_id']]);

            if($get_price->rowCount() > 0) {
                while($f_price = $get_price->fetch(PDO::FETCH_ASSOC)) {
                    $insert_order = $conn->prepare("INSERT INTO orders (id, user_id, name, number, email, address, method, product_id, price, qty, date, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?) " );    
                    $insert_order->execute([create_unique_id(), $user_id, $name, $number, $email, $address, $method, $f_cart['id'], $f_price['price'], $f_cart['qty'], date('m/d/y'), 'in delivering']);
                    $success_msg[] = 'Your order is received now!';
                    header('location: orders.php');

                }
                if($insert_order) {
                    $empty_cart = $conn->prepare("DELETE FROM carts WHERE user_id = ?");
                    $empty_cart->execute([$user_id]);
                }
            } else {
                $warning_msg[] = 'Somthing went wrong!';
            }
        }
    } else {
        $warning_msg = "Your cart is empty";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <!-- fontawesome cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- custom css -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'components/header.php'; ?>

    <section class="checkout" id="checkout">
        <h1 class="heading">Checkout summary</h1>
        <div class="row">
            <form action="" method="post">
                <h3>billing detail</h3>
                <div class="flex">
                    <div class="box">
                        <p>your name <span>*</span></p>
                        <input type="text" class="input" name="name" maxlength="50" placeholder="enter your name"
                            required>
                        <p>your email <span>*</span></p>
                        <input type="email" class="input" name="email" maxlength="50"
                            placeholder="enter your email" required>
                        <p>your number <span>*</span></p>
                        <input type="number" name="number" maxlength="50" placeholder="enter your phone number" required class="input">
                        <p>payment method <span>*</span></p>
                        <select name="method" id="method" class="input" required>
                            <option value="cod">Cash on delivery</option>
                            <option value="prepaid">Prepaid</option>
                        </select>
                    </div>
                    <div class="box">
                        <p for="home-no">အိမ်အမှတ်: </p>
                        <input type="text" name="home-no" placeholder="your building number" class="input">
                        <p for="street">လမ်းအမည်: </p>
                        <input type="text" name="street" placeholder="your street" class="input">
                        <p for="quarter">ရပ်ကွက်: </p>
                        <input type="text" name="quarter" placeholder="your quarter" class="input">
                        <p for="township">မြို့နယ်: </p>
                        <input type="text" name="township" placeholder="your township" class="input">
                        <p for="city">မြို့: </p>
                        <input type="text" name="city" placeholder="your city" class="input">
                    </div>
                </div>
                <input type="submit" value="place order" name="place-order" class="btn">
            </form>
            <div class="summary">
                <p class="title">total items</p>
                <?php
                    $grand_total = 0;
                ?>
                <?php if($get_id != '') : ?>
                <?php
                    $select_product = $conn->prepare("SELECT * FROM products WHERE id = ?");
                    $select_product->execute([$get_id]);
                ?>
                    <?php if($select_product->rowCount() > 0) : ?>
                        <?php while($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)): ?>
                            <?php $grand_total = $fetch_product['price'] ?>
                            <div class="flex">
                                <img src="uploaded_image/".<?= $fetch_product['image']; ?> alt="">
                                <div>
                                    <h3 class="name"><?= $fetch_product['name'] ?></h3>
                                    <p class="price">
                                        <i class="fa-solid fa-dollar"></i><?= $fetch_product['price'] ?> x 
                                    </p>
                                </div>
                            </div>
                        <?php endwhile ?>
                    <?php else: ?>
                        <p class="empty">product was not found!</p>
                    <?php endif; ?>
                <?php else: ?>
                    <?php
                    $select_cart = $conn->prepare("SELECT * FROM carts WHERE user_id = ?");
                    $select_cart->execute([$user_id]);
                    if($select_cart->rowCount() > 0) {
                        while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                            $select_p = $conn->prepare("SELECT * FROM products WHERE id=?");
                            $select_p->execute([$fetch_cart['product_id']]);
                            if($select_p->rowCount() > 0) {
                                while($f_product = $select_p->fetch(PDO::FETCH_ASSOC)) {
                                    $sub_total = $f_product['price'] * $fetch_cart['qty'];
                                    $grand_total += $sub_total;
                                    ?>
                                    <div class="flex">
                                        <img src="uploaded_files/<?= $f_product['image'] ?>" alt="">
                                        <div>
                                            <h3 class="name"><?= $f_product['name'] ?></h3>
                                            <p class="price">
                                                <i class="fa-solid fa-dollar"></i>
                                                <?= $f_product['price'] ?> x <?= $fetch_cart['qty'] ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php

                                }
                            } else {
?>
<p class="empty">Your cart is empty !</p>
<?php
                            }
                        }
                    }
                    ?>
                <?php endif ?>
                
                <p class="grand-total">grand total : <span><i class="fa-solid fa-dollar"></i><?= $grand_total ?></span></p>
            </div>
            
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
