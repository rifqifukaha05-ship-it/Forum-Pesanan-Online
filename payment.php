<?php

require "config/database.php";
require "config/auth.php";

require_login();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE id = ?
    AND user_id = ?
");

$stmt->execute([
    $id,
    $_SESSION['user']['id']
]);

$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pembayaran QRIS</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="assets/style.css">

</head>

<body>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card shadow">

                <div class="card-body text-center">

                    <h2 class="mb-3">
                        Pembayaran
                    </h2>

                    <p>
                        Pesanan #<?= $order['id'] ?>
                    </p>

                    <h4 class="mb-4">
                        Rp <?= number_format($order['total'], 0, ',', '.') ?>
                    </h4>

                    <?php if ($order['payment'] === 'QRIS'): ?>

                        <h5 class="mb-3">
                            Scan QRIS
                        </h5>

                        <img
                            src="assets/qris.jpeg"
                            alt="QRIS"
                            class="img-fluid"
                            style="max-width:300px;"
                        >

                        <p class="mt-3">
                            Scan menggunakan DANA, GoPay, OVO,
                            ShopeePay, mobile banking, atau aplikasi
                            pembayaran yang mendukung QRIS.
                        </p>

                        <a
                            href="upload_bukti.php?id=<?= $order['id'] ?>"
                            class="btn btn-primary w-100 mt-3"
                        >
                            Saya Sudah Bayar
                        </a>

                    <?php elseif ($order['payment'] === 'COD'): ?>

                        <div class="alert alert-info">
                            Pembayaran dilakukan saat barang diterima.
                        </div>

                    <?php elseif ($order['payment'] === 'Transfer Bank'): ?>

                        <div class="alert alert-info">

                            <h5>Transfer Bank</h5>

                            <p class="mb-1">
                                Bank BCA
                            </p>

                            <strong>
                                1234567890
                            </strong>

                            <p class="mt-2">
                                Atas Nama: Pemesanan Online
                            </p>

                        </div>

                        <a
                            href="upload_bukti.php?id=<?= $order['id'] ?>"
                            class="btn btn-primary w-100"
                        >
                            Saya Sudah Transfer
                        </a>

                    <?php endif; ?>

                    <a
                        href="orders.php"
                        class="btn btn-outline-secondary w-100 mt-2"
                    >
                        Lihat Pesanan
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>