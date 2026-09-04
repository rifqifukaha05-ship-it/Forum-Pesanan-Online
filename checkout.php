<?php

require "config/database.php";
require "config/auth.php";

require_login();

$cart = $_SESSION["cart"] ?? [];

if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

$ids = array_keys($cart);

$placeholders = implode(
    ",",
    array_fill(0, count($ids), "?")
);

$query = $pdo->prepare(
    "SELECT *
     FROM products
     WHERE id IN ($placeholders)"
);

$query->execute($ids);

$products = $query->fetchAll(PDO::FETCH_ASSOC);

$items = [];
$total = 0;

foreach ($products as $product) {

    $qty = min(
        (int)$cart[$product["id"]],
        (int)$product["stock"]
    );

    if ($qty > 0) {

        $items[] = [
            "product" => $product,
            "qty" => $qty
        ];

        $total +=
            $qty * $product["price"];
    }
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $payment = $_POST["payment"] ?? "";

    $paymentMethods = [
    "QRIS",
    "COD"
];

    if (
        $phone === "" ||
        $address === "" ||
        !in_array($payment, $paymentMethods)
    ) {

        $error =
            "Silakan lengkapi semua data.";

    } else {

        try {

            $pdo->beginTransaction();

            /*
             * Membuat pesanan
             */
            $query = $pdo->prepare(
    "INSERT INTO orders
    (
        user_id,
        total,
        address,
        phone,
        payment,
        status,
        payment_status
    )
    VALUES (?, ?, ?, ?, ?, 'Menunggu', 'Menunggu Pembayaran')"
);

            $query->execute([
                $_SESSION["user"]["id"],
                $total,
                $address,
                $phone,
                $payment
            ]);

            $orderId =
                $pdo->lastInsertId();

            /*
             * Menyimpan detail pesanan
             */
            $detailQuery = $pdo->prepare(
                "INSERT INTO order_items
                (
                    order_id,
                    product_id,
                    qty,
                    price
                )
                VALUES (?, ?, ?, ?)"
            );

            /*
             * Mengurangi stok
             */
            $stockQuery = $pdo->prepare(
                "UPDATE products
                 SET stock = stock - ?
                 WHERE id = ?
                 AND stock >= ?"
            );

            foreach ($items as $item) {

                $product =
                    $item["product"];

                $qty =
                    $item["qty"];

                $detailQuery->execute([
                    $orderId,
                    $product["id"],
                    $qty,
                    $product["price"]
                ]);

                $stockQuery->execute([
                    $qty,
                    $product["id"],
                    $qty
                ]);

                if ($stockQuery->rowCount() !== 1) {

                    throw new Exception(
                        "Stok produk tidak mencukupi."
                    );

                }
            }

            $pdo->commit();

            /*
             * Kosongkan keranjang
             */
            unset($_SESSION["cart"]);

            /*
             * Ke halaman detail pesanan
             */
            header(
    "Location: payment.php?id="
    . $orderId
);
exit;


        } catch (Exception $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $error =
                "Checkout gagal: "
                . $e->getMessage();
        }
    }
}

$title = "Checkout";

require "includes/header.php";

?>

<div class="row g-4">

    <!-- FORM PEMESANAN -->

    <div class="col-md-7">

        <div class="card shadow-sm">

            <div class="card-body p-4">

                <h3 class="mb-4">
                    Checkout
                </h3>

                <?php if ($error): ?>

                    <div class="alert alert-danger">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>

                <form method="POST">

                    <!-- NAMA -->

                    <div class="mb-3">

                        <label class="form-label">
                            Nama Pemesan
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars(
                                $_SESSION["user"]["name"]
                            ) ?>"
                            readonly
                        >

                    </div>


                    <!-- EMAIL -->

                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            class="form-control"
                            value="<?= htmlspecialchars(
                                $_SESSION["user"]["email"]
                            ) ?>"
                            readonly
                        >

                    </div>


                    <!-- NOMOR HP -->

                    <div class="mb-3">

                        <label class="form-label">
                            Nomor HP
                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            placeholder="08xxxxxxxxxx"
                            required
                        >

                    </div>


                    <!-- ALAMAT -->

                    <div class="mb-3">

                        <label class="form-label">
                            Alamat Pengiriman
                        </label>

                        <textarea
                            name="address"
                            class="form-control"
                            rows="4"
                            placeholder="Masukkan alamat lengkap"
                            required
                        ></textarea>

                    </div>


                   <div class="mb-4">

    <label class="form-label">
        Metode Pembayaran
    </label>

    <select
        name="payment"
        class="form-select"
        required
    >

        <option value="">
            -- Pilih Pembayaran --
        </option>

        <option value="QRIS">
            QRIS
        </option>

        <option value="COD">
            COD
        </option>

    </select>

    <div id="qris-box" class="text-center mt-3" style="display: none;">

    <h5>Scan QRIS untuk Membayar</h5>

    <img
        src="assets/qris.jpeg"
        alt="QRIS"
        class="img-fluid"
        style="max-width: 300px;"
    >

    <p class="mt-2">
        Scan menggunakan DANA, GoPay, OVO,
        ShopeePay, atau mobile banking.
    </p>

</div>

</div>


                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        Buat Pesanan

                    </button>

                </form>

            </div>

        </div>

    </div>


    <!-- RINGKASAN PESANAN -->

    <div class="col-md-5">

        <div class="card shadow-sm">

            <div class="card-body">

                <h4 class="mb-3">
                    Ringkasan Pesanan
                </h4>

                <?php foreach ($items as $item): ?>

                    <div
                        class="d-flex justify-content-between
                               border-bottom py-2">

                        <span>

                            <?= htmlspecialchars(
                                $item["product"]["name"]
                            ) ?>

                            ×

                            <?= $item["qty"] ?>

                        </span>

                        <strong>

                            Rp
                            <?= number_format(
                                $item["product"]["price"]
                                * $item["qty"],
                                0,
                                ",",
                                "."
                            ) ?>

                        </strong>

                    </div>

                <?php endforeach; ?>


                <div
                    class="d-flex justify-content-between
                           mt-3">

                    <strong>
                        Total
                    </strong>

                    <strong class="text-primary">

                        Rp
                        <?= number_format(
                            $total,
                            0,
                            ",",
                            "."
                        ) ?>

                    </strong>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
const paymentSelect = document.querySelector('select[name="payment"]');
const qrisBox = document.getElementById('qris-box');

function toggleQris() {
    if (paymentSelect.value === 'QRIS') {
        qrisBox.style.display = 'block';
    } else {
        qrisBox.style.display = 'none';
    }
}

paymentSelect.addEventListener('change', toggleQris);

toggleQris();
</script>

<?php

require "includes/footer.php";

?>