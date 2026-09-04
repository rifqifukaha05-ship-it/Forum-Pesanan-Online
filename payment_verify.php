<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";

require_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT
        orders.*,
        users.name
    FROM orders
    INNER JOIN users ON users.id = orders.user_id
    WHERE orders.id = ?
");

$stmt->execute([$id]);

$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

$paymentStatuses = [
    "Menunggu Pembayaran",
    "Diproses",
    "Berhasil",
    "Gagal"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $paymentStatus = $_POST['payment_status'];

    if (!in_array($paymentStatus, $paymentStatuses)) {
        die("Status pembayaran tidak valid.");
    }

    $stmt = $pdo->prepare("
        UPDATE orders
        SET payment_status = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $paymentStatus,
        $id
    ]);

    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Verifikasi Pembayaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body style="background:#f5f8fc;">

<div class="container py-5">

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <h3 class="fw-bold mb-4">
                Verifikasi Pembayaran
            </h3>

            <p>
                <strong>Pesanan:</strong>
                #<?= $order['id'] ?>
            </p>

            <p>
                <strong>Pelanggan:</strong>
                <?= htmlspecialchars($order['name']) ?>
            </p>

            <p>
                <strong>Total:</strong>
                Rp <?= number_format($order['total'], 0, ',', '.') ?>
            </p>

            <p>
                <strong>Metode:</strong>
                <?= htmlspecialchars($order['payment']) ?>
            </p>

            <?php if (!empty($order['payment_proof'])): ?>

                <p>
                    <strong>Bukti Pembayaran:</strong>
                </p>

                <img
                    src="../uploads/bukti/<?= htmlspecialchars($order['payment_proof']) ?>"
                    style="max-width:350px;"
                    class="img-fluid rounded mb-4"
                >

            <?php else: ?>

                <div class="alert alert-warning">
                    Belum ada bukti pembayaran.
                </div>

            <?php endif; ?>

            <form method="post">

                <label class="form-label fw-bold">
                    Status Pembayaran
                </label>

                <select
                    name="payment_status"
                    class="form-select mb-4"
                >

                    <?php foreach ($paymentStatuses as $status): ?>

                        <option
                            value="<?= $status ?>"
                            <?= $order['payment_status'] === $status ? 'selected' : '' ?>
                        >
                            <?= $status ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <button class="btn btn-success">
                    Simpan Pembayaran
                </button>

                <a href="index.php" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>

    </div>

</div>

</body>

</html>