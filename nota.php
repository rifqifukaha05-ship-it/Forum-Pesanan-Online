<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";

require_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT
        orders.*,
        users.name,
        users.email
    FROM orders
    INNER JOIN users ON users.id = orders.user_id
    WHERE orders.id = ?
");

$stmt->execute([$id]);

$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Nota tidak ditemukan.");
}

$stmt = $pdo->prepare("
    SELECT
        order_items.*,
        products.name AS product_name
    FROM order_items
    INNER JOIN products ON products.id = order_items.product_id
    WHERE order_items.order_id = ?
");

$stmt->execute([$id]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nota Pesanan #<?= $order['id'] ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f5f8fc;
            font-family: Arial, Helvetica, sans-serif;
        }

        .nota {
            max-width: 850px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .judul {
            color: #075bc5;
        }

        @media print {

            body {
                background: white;
            }

            .nota {
                margin: 0;
                max-width: none;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }

        }

    </style>

</head>

<body>

<div class="nota">

    <div class="d-flex justify-content-between align-items-start mb-4">

        <div>

            <h2 class="fw-bold judul">
                NOTA PESANAN
            </h2>

            <p class="mb-0">
                Pemesanan Online
            </p>

        </div>

        <div class="text-end">

            <strong>
                #<?= $order['id'] ?>
            </strong>

            <br>

            <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>

        </div>

    </div>

    <hr>

    <div class="row mb-4">

        <div class="col-md-6">

            <h6 class="fw-bold">
                Data Pelanggan
            </h6>

            <p class="mb-1">
                <?= htmlspecialchars($order['name']) ?>
            </p>

            <p class="mb-1">
                <?= htmlspecialchars($order['email']) ?>
            </p>

            <p class="mb-1">
                <?= htmlspecialchars($order['phone']) ?>
            </p>

        </div>

        <div class="col-md-6">

            <h6 class="fw-bold">
                Alamat Pengiriman
            </h6>

            <p class="mb-0">
                <?= nl2br(htmlspecialchars($order['address'])) ?>
            </p>

        </div>

    </div>

    <table class="table table-bordered">

        <thead>

            <tr>

                <th>Produk</th>

                <th>Harga</th>

                <th>Jumlah</th>

                <th>Subtotal</th>

            </tr>

        </thead>

        <tbody>

        <?php foreach ($items as $item): ?>

            <tr>

                <td>
                    <?= htmlspecialchars($item['product_name']) ?>
                </td>

                <td>
                    Rp <?= number_format($item['price'], 0, ',', '.') ?>
                </td>

                <td>
                    <?= $item['qty'] ?>
                </td>

                <td>
                    Rp <?= number_format(
                        $item['price'] * $item['qty'],
                        0,
                        ',',
                        '.'
                    ) ?>
                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

    <div class="row justify-content-end">

        <div class="col-md-5">

            <div class="d-flex justify-content-between">

                <strong>
                    Total
                </strong>

                <strong>
                    Rp <?= number_format($order['total'], 0, ',', '.') ?>
                </strong>

            </div>

            <div class="d-flex justify-content-between mt-2">

                <span>
                    Metode Pembayaran
                </span>

                <span>
                    <?= htmlspecialchars($order['payment']) ?>
                </span>

            </div>

            <div class="d-flex justify-content-between mt-2">

                <span>
                    Status Pembayaran
                </span>

                <span>
                    <?= htmlspecialchars($order['payment_status']) ?>
                </span>

            </div>

            <div class="d-flex justify-content-between mt-2">

                <span>
                    Status Pesanan
                </span>

                <span>
                    <?= htmlspecialchars($order['status']) ?>
                </span>

            </div>

        </div>

    </div>

    <hr>

    <div class="text-center mt-4">

        <p class="text-muted mb-3">
            Nota ini dibuat oleh sistem Pemesanan Online.
        </p>

        <button
            onclick="window.print()"
            class="btn btn-primary no-print"
        >
            Cetak Nota
        </button>

        <a
            href="index.php"
            class="btn btn-secondary no-print"
        >
            Kembali ke Dashboard
        </a>

    </div>

</div>

</body>

</html>