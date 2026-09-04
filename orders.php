<?php

require "config/database.php";
require "config/auth.php";

require_login();

$query = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$query->execute([
    $_SESSION["user"]["id"]
]);

$orders = $query->fetchAll(PDO::FETCH_ASSOC);

$title = "Pesanan Saya";

require "includes/header.php";

?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="mb-1">
                Pesanan Saya
            </h2>

            <p class="text-muted mb-0">
                Daftar semua pesanan kamu
            </p>
        </div>

        <a
            href="index.php"
            class="btn btn-primary">

            Belanja Lagi

        </a>

    </div>

    <?php if (empty($orders)): ?>

        <div class="card shadow-sm">

            <div class="card-body text-center py-5">

                <h4>
                    Belum Ada Pesanan
                </h4>

                <p class="text-muted">
                    Kamu belum melakukan pemesanan.
                </p>

                <a
                    href="index.php"
                    class="btn btn-primary">

                    Mulai Belanja

                </a>

            </div>

        </div>

    <?php else: ?>

        <div class="card shadow-sm">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>
                                    ID
                                </th>

                                <th>
                                    Tanggal
                                </th>

                                <th>
                                    Total
                                </th>

                                <th>
                                    Pembayaran
                                </th>

                                <th>
                                    Status Pembayaran
                                </th>

                                <th>
                                    Status Pesanan
                                </th>

                                <th>
                                    Aksi
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($orders as $order): ?>

                                <tr>

                                    <td>

                                        <strong>
                                            #<?= $order["id"] ?>
                                        </strong>

                                    </td>

                                    <td>

                                        <?= date(
                                            "d-m-Y H:i",
                                            strtotime(
                                                $order["created_at"]
                                            )
                                        ) ?>

                                    </td>

                                    <td>

                                        <strong>

                                            Rp <?= number_format(
                                                $order["total"],
                                                0,
                                                ",",
                                                "."
                                            ) ?>

                                        </strong>

                                    </td>

                                    <td>

                                        <?php if ($order["payment"] === "QRIS"): ?>

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

                                        <?php if (
                                            $order["payment_status"] ===
                                            "Menunggu Pembayaran"
                                        ): ?>

                                            <span class="badge bg-warning text-dark">
                                                Menunggu Pembayaran
                                            </span>

                                        <?php elseif (
                                            $order["payment_status"] ===
                                            "Diproses"
                                        ): ?>

                                            <span class="badge bg-info text-dark">
                                                Diproses
                                            </span>

                                        <?php elseif (
                                            $order["payment_status"] ===
                                            "Berhasil"
                                        ): ?>

                                            <span class="badge bg-success">
                                                Berhasil
                                            </span>

                                        <?php elseif (
                                            $order["payment_status"] ===
                                            "Gagal"
                                        ): ?>

                                            <span class="badge bg-danger">
                                                Gagal
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>

                                        <?php if (
                                            $order["status"] ===
                                            "Menunggu"
                                        ): ?>

                                            <span class="badge bg-warning text-dark">
                                                Menunggu
                                            </span>

                                        <?php elseif (
                                            $order["status"] ===
                                            "Diproses"
                                        ): ?>

                                            <span class="badge bg-primary">
                                                Diproses
                                            </span>

                                        <?php elseif (
                                            $order["status"] ===
                                            "Dikirim"
                                        ): ?>

                                            <span class="badge bg-info text-dark">
                                                Dikirim
                                            </span>

                                        <?php elseif (
                                            $order["status"] ===
                                            "Selesai"
                                        ): ?>

                                            <span class="badge bg-success">
                                                Selesai
                                            </span>

                                        <?php elseif (
                                            $order["status"] ===
                                            "Dibatalkan"
                                        ): ?>

                                            <span class="badge bg-danger">
                                                Dibatalkan
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>

                                        <a
    href="order_detail.php?id=<?= $order["id"] ?>"
    class="btn btn-sm btn-outline-primary">

    Detail

</a>

<a
    href="nota.php?id=<?= $order["id"] ?>"
    class="btn btn-sm btn-success">

    Nota

</a>

                                        <?php if (
                                            $order["payment"] === "QRIS" &&
                                            $order["payment_status"] !==
                                            "Berhasil"
                                        ): ?>

                                            <a
                                                href="payment.php?id=<?= $order["id"] ?>"
                                                class="btn btn-sm btn-primary">

                                                Bayar

                                            </a>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<?php

require "includes/footer.php";

?>