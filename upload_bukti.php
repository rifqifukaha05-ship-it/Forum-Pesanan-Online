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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['bukti'])) {
        die("Bukti pembayaran wajib diupload.");
    }

    $file = $_FILES['bukti'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("Gagal mengupload bukti pembayaran.");
    }

    $allowed = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];

    if (!in_array($file['type'], $allowed)) {
        die("Format harus JPG, PNG, atau WEBP.");
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        die("Ukuran file maksimal 2 MB.");
    }

    $extension = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );

    $filename = 'bukti_' . $order['id'] . '_' . time() . '.' . $extension;

    $folder = 'uploads/bukti/';

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $path = $folder . $filename;

    if (!move_uploaded_file($file['tmp_name'], $path)) {
        die("Gagal menyimpan file.");
    }

    $stmt = $pdo->prepare("
        UPDATE orders
        SET payment_proof = ?,
            status = 'Menunggu'
        WHERE id = ?
        AND user_id = ?
    ");

    $stmt->execute([
        $filename,
        $order['id'],
        $_SESSION['user']['id']
    ]);

    header("Location: order_detail.php?id=" . $order['id']);
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Upload Bukti Pembayaran</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card shadow">

                <div class="card-body">

                    <h3>Upload Bukti Pembayaran</h3>

                    <p>
                        Pesanan #<?= $order['id'] ?>
                    </p>

                    <p>
                        Total:
                        <strong>
                            Rp <?= number_format($order['total'], 0, ',', '.') ?>
                        </strong>
                    </p>

                    <form method="POST" enctype="multipart/form-data">

                        <label class="form-label">
                            Bukti Pembayaran
                        </label>

                        <input
                            type="file"
                            name="bukti"
                            class="form-control"
                            accept="image/jpeg,image/png,image/webp"
                            required
                        >

                        <button
                            type="submit"
                            class="btn btn-primary w-100 mt-3"
                        >
                            Upload Bukti
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>