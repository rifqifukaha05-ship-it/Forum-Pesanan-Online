<?php

require "config/database.php";
require "config/auth.php";

require_login();

$id = (int)($_GET["id"] ?? 0);

$query = $pdo->prepare("
    SELECT
        orders.*,
        users.name,
        users.email
    FROM orders
    JOIN users
        ON users.id = orders.user_id
    WHERE orders.id = ?
    AND orders.user_id = ?
");

$query->execute([
    $id,
    $_SESSION["user"]["id"]
]);

$order = $query->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

$query = $pdo->prepare("
    SELECT
        order_items.*,
        products.name
    FROM order_items
    JOIN products
        ON products.id = order_items.product_id
    WHERE order_items.order_id = ?
");

$query->execute([$id]);

$items = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<?php

$title = "Detail Pesanan";

require "includes/header.php";

?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>
            Detail Pesanan #<?= $order["id"] ?>
        </h2>

        <a
            href="orders.php"
            class="btn btn-outline-secondary">

            Kembali

        </a>

    </div>

    <div class="row g-4">

        <div class="col-md-7">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h4 class="mb-4">
                        Informasi Pesanan
                    </h4>

                    <div class="mb-3">

                        <strong>
                            Nama Pemesan
                        </strong>

                        <p class="mb-0">
                            <?= htmlspecialchars($order["name"]) ?>
                        </p>

                    </div>

                    <div class="mb-3">

                        <strong>
                            Email
                        </strong>

                        <p class="mb-0">
                            <?= htmlspecialchars($order["email"]) ?>
                        </p>

                    </div>

                    <div class="mb-3">

                        <strong>
                            Nomor HP
                        </strong>

                        <p class="mb-0">
                            <?= htmlspecialchars($order["phone"]) ?>
                        </p>

                    </div>

                    <div class="mb-3">

                        <strong>
                            Alamat Pengiriman
                        </strong>

                        <p class="mb-0">
                            <?= nl2br(
                                htmlspecialchars($order["address"])
                            ) ?>
                        </p>

                    </div>

                    <div class="mb-3">

                        <strong>
                            Metode Pembayaran
                        </strong>

                        <p class="mb-0">
                            <?= htmlspecialchars($order["payment"]) ?>
                        </p>

                    </div>

                    <div class="mb-3">

                        <strong>
                            Status Pesanan
                        </strong>

                        <div class="mt-1">

                            <?php if ($order["status"] === "Menunggu"): ?>

                                <span class="badge bg-warning text-dark">
                                    Menunggu
                                </span>

                            <?php elseif ($order["status"] === "Diproses"): ?>

                                <span class="badge bg-primary">
                                    Diproses
                                </span>

                            <?php elseif ($order["status"] === "Dikirim"): ?>

                                <span class="badge bg-info text-dark">
                                    Dikirim
                                </span>

                            <?php elseif ($order["status"] === "Selesai"): ?>

                                <span class="badge bg-success">
                                    Selesai
                                </span>

                            <?php elseif ($order["status"] === "Dibatalkan"): ?>

                                <span class="badge bg-danger">
                                    Dibatalkan
                                </span>

                            <?php endif; ?>

                        </div>

                    </div>

                    <div class="mb-3">

                        <strong>
                            Status Pembayaran
                        </strong>

                        <div class="mt-1">

                            <?php if ($order["payment_status"] === "Menunggu Pembayaran"): ?>

                                <span class="badge bg-warning text-dark">
                                    Menunggu Pembayaran
                                </span>

                            <?php elseif ($order["payment_status"] === "Diproses"): ?>

                                <span class="badge bg-primary">
                                    Pembayaran Diproses
                                </span>

                            <?php elseif ($order["payment_status"] === "Berhasil"): ?>

                                <span class="badge bg-success">
                                    Pembayaran Berhasil
                                </span>

                            <?php elseif ($order["payment_status"] === "Gagal"): ?>

                                <span class="badge bg-danger">
                                    Pembayaran Gagal
                                </span>

                            <?php endif; ?>

                        </div>

                    </div>

                    <div>

                        <strong>
                            Tanggal Pesanan
                        </strong>

                        <p class="mb-0">
                            <?= htmlspecialchars(
                                $order["created_at"]
                            ) ?>
                        </p>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-5">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h4 class="mb-4">
                        Produk
                    </h4>

                    <?php foreach ($items as $item): ?>

                        <div class="d-flex justify-content-between border-bottom py-3">

                            <div>

                                <strong>
                                    <?= htmlspecialchars(
                                        $item["name"]
                                    ) ?>
                                </strong>

                                <div class="text-muted">

                                    <?= $item["qty"] ?> ×

                                    Rp <?= number_format(
                                        $item["price"],
                                        0,
                                        ",",
                                        "."
                                    ) ?>

                                </div>

                            </div>

                            <strong>

                                Rp <?= number_format(
                                    $item["qty"] * $item["price"],
                                    0,
                                    ",",
                                    "."
                                ) ?>

                            </strong>

                        </div>

                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between mt-4">

                        <h5>
                            Total
                        </h5>

                        <h5 class="text-primary">

                            Rp <?= number_format(
                                $order["total"],
                                0,
                                ",",
                                "."
                            ) ?>

                        </h5>

                    </div>

                    <?php if (
                        $order["payment"] === "QRIS" &&
                        $order["payment_status"] !== "Berhasil"
                    ): ?>

                        <a
                            href="payment.php?id=<?= $order["id"] ?>"
                            class="btn btn-primary w-100 mt-3">

                            Lihat Pembayaran QRIS

                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

require "includes/footer.php";

?>