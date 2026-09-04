<?php

require "config/database.php";
require "config/auth.php";

require_login();

if (isset($_GET["remove"])) {

    $id = (int) $_GET["remove"];

    unset($_SESSION["cart"][$id]);

    header("Location: cart.php");

    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    foreach ($_POST["qty"] ?? [] as $id => $qty) {

        $id = (int) $id;
        $qty = (int) $qty;

        if ($qty <= 0) {

            unset($_SESSION["cart"][$id]);

        } else {

            $_SESSION["cart"][$id] = $qty;

        }
    }

    header("Location: cart.php");

    exit;
}

$cart = $_SESSION["cart"] ?? [];

$items = [];

$total = 0;

if (!empty($cart)) {

    $ids = array_keys($cart);

    $placeholders =
        implode(
            ",",
            array_fill(
                0,
                count($ids),
                "?"
            )
        );

    $query = $pdo->prepare(
        "SELECT *
         FROM products
         WHERE id IN ($placeholders)"
    );

    $query->execute($ids);

    $products =
        $query->fetchAll(
            PDO::FETCH_ASSOC
        );

    foreach ($products as $product) {

        $qty = min(
            $cart[$product["id"]],
            $product["stock"]
        );

        $product["qty"] = $qty;

        $items[] = $product;

        $total +=
            $qty * $product["price"];
    }
}

$title = "Keranjang";

require "includes/header.php";

?>

<h2 class="mb-4">
    Keranjang Belanja
</h2>

<?php if (empty($items)): ?>

    <div class="alert alert-info">

        Keranjang masih kosong.

    </div>

<?php else: ?>

<form method="POST">

<div class="table-responsive">

<table class="table bg-white shadow-sm">

<thead>

<tr>

    <th>Produk</th>
    <th>Harga</th>
    <th>Jumlah</th>
    <th>Subtotal</th>
    <th>Aksi</th>

</tr>

</thead>

<tbody>

<?php foreach ($items as $item): ?>

<tr>

<td>
    <?= htmlspecialchars($item["name"]) ?>
</td>

<td>

Rp
<?= number_format(
    $item["price"],
    0,
    ",",
    "."
) ?>

</td>

<td>

<input
    type="number"
    class="form-control"
    name="qty[<?= $item["id"] ?>]"
    value="<?= $item["qty"] ?>"
    min="0"
    max="<?= $item["stock"] ?>"
>

</td>

<td>

Rp
<?= number_format(
    $item["qty"] * $item["price"],
    0,
    ",",
    "."
) ?>

</td>

<td>

<a
    href="cart.php?remove=<?= $item["id"] ?>"
    class="btn btn-danger btn-sm">

    Hapus

</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

<tfoot>

<tr>

<th colspan="3" class="text-end">
    Total
</th>

<th>

Rp
<?= number_format(
    $total,
    0,
    ",",
    "."
) ?>

</th>

<th></th>

</tr>

</tfoot>

</table>

</div>

<button
    type="submit"
    class="btn btn-outline-primary">

    Update Keranjang

</button>

<a
    href="checkout.php"
    class="btn btn-primary">

    Checkout

</a>

</form>

<?php endif; ?>

<?php

require "includes/footer.php";

?>