<?php

require "config/database.php";
require "config/auth.php";

require_login();

$id = (int) ($_GET["id"] ?? 0);

$query = $pdo->prepare(
    "SELECT id, stock
     FROM products
     WHERE id = ?"
);

$query->execute([$id]);

$product = $query->fetch(PDO::FETCH_ASSOC);

if (!$product) {

    header("Location: index.php");
    exit;

}

if ($product["stock"] > 0) {

    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = [];
    }

    if (!isset($_SESSION["cart"][$id])) {

        $_SESSION["cart"][$id] = 1;

    } else {

        $_SESSION["cart"][$id]++;

    }

    if (
        $_SESSION["cart"][$id]
        > $product["stock"]
    ) {

        $_SESSION["cart"][$id]
            = $product["stock"];

    }
}

header("Location: cart.php");

exit;

?>