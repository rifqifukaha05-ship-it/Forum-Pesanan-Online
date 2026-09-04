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

$statuses = [
    "Menunggu",
    "Diproses",
    "Dikirim",
    "Selesai",
    "Dibatalkan"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $status = $_POST['status'];

    if (!in_array($status, $statuses)) {
        die("Status tidak valid.");
    }

    $stmt = $pdo->prepare("
        UPDATE orders
        SET status = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $status,
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

    <title>Ubah Status Pesanan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body style="background:#f5f8fc;">

<div class="container py-5">

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <h3 class="fw-bold mb-4">
                Ubah Status Pesanan #<?= $order['id'] ?>
            </h3>

            <p>
                <strong>Pelanggan:</strong>
                <?= htmlspecialchars($order['name']) ?>
            </p>

            <p>
                <strong>Total:</strong>
                Rp <?= number_format($order['total'], 0, ',', '.') ?>
            </p>

            <p>
                <strong>Pembayaran:</strong>
                <?= htmlspecialchars($order['payment']) ?>
            </p>

            <p>
                <strong>Status Pembayaran:</strong>
                <?= htmlspecialchars($order['payment_status']) ?>
            </p>

            <form method="post">

                <label class="form-label fw-bold">
                    Status Pesanan
                </label>

                <select name="status" class="form-select mb-4">

                    <?php foreach ($statuses as $status): ?>

                        <option
                            value="<?= $status ?>"
                            <?= $order['status'] === $status ? 'selected' : '' ?>
                        >
                            <?= $status ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <button class="btn btn-primary">
                    Simpan Status
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