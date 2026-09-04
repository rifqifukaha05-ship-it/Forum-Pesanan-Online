<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";

require_admin();

$stmt = $pdo->query("
    SELECT
        orders.id,
        users.name,
        orders.total,
        orders.payment,
        orders.payment_status,
        orders.payment_proof,
        orders.status,
        orders.created_at
    FROM orders
    INNER JOIN users ON users.id = orders.user_id
    ORDER BY orders.created_at DESC
");

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pesananBaru = 0;
$pembayaranDiproses = 0;

foreach ($orders as $order) {

    if ($order['status'] === 'Menunggu') {
        $pesananBaru++;
    }

    if ($order['payment_status'] === 'Diproses') {
        $pembayaranDiproses++;
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Mitra</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            margin: 0;
            background: #f5f8fc;
            font-family: Arial, Helvetica, sans-serif;
        }

        .navbar-mitra {
            background: linear-gradient(90deg, #063b82, #075bc5);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .notifikasi {
            position: fixed;
            right: 25px;
            bottom: 25px;
            width: 350px;
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            border-left: 5px solid #075bc5;
        }

    </style>

</head>

<body>

<nav class="navbar navbar-dark navbar-mitra">

    <div class="container">

        <span class="navbar-brand fw-bold">
            Mitra Pemesanan Online
        </span>

        <a
            href="../logout.php"
            class="btn btn-light btn-sm"
        >
            Logout
        </a>

    </div>

</nav>

<div class="container py-4">

    <h2 class="fw-bold">
        Dashboard Mitra
    </h2>

    <p class="text-muted">
        Kelola pesanan dan pembayaran pelanggan
    </p>


    <div class="row g-4 mb-4">

        <div class="col-md-4">

            <div class="card p-4">

                <p class="text-muted mb-1">
                    Pesanan Baru
                </p>

                <h1 class="fw-bold text-primary">
                    <?= $pesananBaru ?>
                </h1>

                <p class="text-muted mb-0">
                    Pesanan menunggu diproses
                </p>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card p-4">

                <p class="text-muted mb-1">
                    Pembayaran Diproses
                </p>

                <h1 class="fw-bold text-warning">
                    <?= $pembayaranDiproses ?>
                </h1>

                <p class="text-muted mb-0">
                    Menunggu verifikasi
                </p>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card p-4">

                <p class="text-muted mb-1">
                    Total Pesanan
                </p>

                <h1 class="fw-bold text-success">
                    <?= count($orders) ?>
                </h1>

                <p class="text-muted mb-0">
                    Semua pesanan pelanggan
                </p>

            </div>

        </div>

    </div>


    <div class="card">

        <div class="card-body">

            <h4 class="fw-bold mb-4">
                Daftar Pesanan
            </h4>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Pelanggan</th>

                            <th>Total</th>

                            <th>Metode</th>

                            <th>Status Pembayaran</th>

                            <th>Status Pesanan</th>

                            <th>Tanggal</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php if (count($orders) > 0): ?>

                        <?php foreach ($orders as $order): ?>

                            <tr>

                                <td>
                                    #<?= $order['id'] ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($order['name']) ?>
                                </td>


                                <td>
                                    Rp <?= number_format($order['total'], 0, ',', '.') ?>
                                </td>


                                <td>

                                    <?php if ($order['payment'] === 'QRIS'): ?>

                                        <span class="badge bg-primary">
                                            QRIS
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-secondary">
                                            COD
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <?php if ($order['payment_status'] === 'Menunggu Pembayaran'): ?>

                                        <span class="badge bg-warning text-dark">
                                            Menunggu Pembayaran
                                        </span>

                                    <?php elseif ($order['payment_status'] === 'Diproses'): ?>

                                        <span class="badge bg-info text-dark">
                                            Diproses
                                        </span>

                                    <?php elseif ($order['payment_status'] === 'Berhasil'): ?>

                                        <span class="badge bg-success">
                                            Berhasil
                                        </span>

                                    <?php elseif ($order['payment_status'] === 'Gagal'): ?>

                                        <span class="badge bg-danger">
                                            Gagal
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <?php if ($order['status'] === 'Menunggu'): ?>

                                        <span class="badge bg-warning text-dark">
                                            Menunggu
                                        </span>

                                    <?php elseif ($order['status'] === 'Diproses'): ?>

                                        <span class="badge bg-primary">
                                            Diproses
                                        </span>

                                    <?php elseif ($order['status'] === 'Dikirim'): ?>

                                        <span class="badge bg-info text-dark">
                                            Dikirim
                                        </span>

                                    <?php elseif ($order['status'] === 'Selesai'): ?>

                                        <span class="badge bg-success">
                                            Selesai
                                        </span>

                                    <?php elseif ($order['status'] === 'Dibatalkan'): ?>

                                        <span class="badge bg-danger">
                                            Dibatalkan
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>
                                    <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                </td>


                                <td>

    <div class="d-flex gap-2">

        <a
            href="order_edit.php?id=<?= $order['id'] ?>"
            class="btn btn-primary btn-sm"
        >
            Status Pesanan
        </a>

        <?php if ($order['payment'] === 'QRIS'): ?>

            <a
                href="payment_verify.php?id=<?= $order['id'] ?>"
                class="btn btn-success btn-sm"
            >
                Verifikasi Pembayaran
            </a>

        <?php endif; ?>

        <a
            href="nota.php?id=<?= $order['id'] ?>"
            class="btn btn-secondary btn-sm"
        >
            Nota
        </a>

    </div>

</td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="8"
                                class="text-center text-muted py-4"
                            >
                                Belum ada pesanan.
                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<?php if ($pesananBaru > 0 || $pembayaranDiproses > 0): ?>

<div class="notifikasi">

    <h5 class="fw-bold">
        🔔 Notifikasi
    </h5>

    <?php if ($pesananBaru > 0): ?>

        <p class="mb-2">
            Ada
            <strong><?= $pesananBaru ?></strong>
            pesanan baru.
        </p>

    <?php endif; ?>


    <?php if ($pembayaranDiproses > 0): ?>

        <p class="mb-3">
            Ada
            <strong><?= $pembayaranDiproses ?></strong>
            pembayaran yang perlu diverifikasi.
        </p>

    <?php endif; ?>

    <button
        class="btn btn-primary btn-sm"
        onclick="document.querySelector('.notifikasi').remove()"
    >
        Mengerti
    </button>

</div>

<?php endif; ?>

</body>

</html>