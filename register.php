<?php

require_once __DIR__ . "/../config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($name === "" || $email === "" || $password === "") {

        $error = "Semua data harus diisi.";

    } elseif ($password !== $confirmPassword) {

        $error = "Konfirmasi password tidak sesuai.";

    } else {

        try {

            $stmt = $pdo->prepare("
                SELECT id
                FROM users
                WHERE email = ?
            ");

            $stmt->execute([$email]);

            if ($stmt->fetch()) {

                $error = "Email sudah terdaftar.";

            } else {

                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $stmt = $pdo->prepare("
                    INSERT INTO users
                    (name, email, password, role)
                    VALUES (?, ?, ?, 'admin')
                ");

                $stmt->execute([
                    $name,
                    $email,
                    $hashedPassword
                ]);

                $success = "Akun Mitra berhasil dibuat. Silakan login.";

            }

        } catch (PDOException $e) {

            $error = "Gagal membuat akun: " . $e->getMessage();

        }

    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register Mitra</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #063b82, #075bc5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, Helvetica, sans-serif;
        }

        .register-card {
            width: 420px;
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

    </style>

</head>

<body>

<div class="register-card">

    <h3 class="text-center fw-bold mb-2">
        Register Mitra
    </h3>

    <p class="text-center text-muted mb-4">
        Buat akun untuk mengelola pesanan
    </p>

    <?php if ($error !== ""): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <?php if ($success !== ""): ?>

        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>

            <div class="mt-2">
                <a href="login.php">
                    Login sebagai Mitra
                </a>
            </div>
        </div>

    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">

            <label class="form-label">
                Nama
            </label>

            <input
                type="text"
                name="name"
                class="form-control"
                required
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Email
            </label>

            <input
                type="email"
                name="email"
                class="form-control"
                required
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Password
            </label>

            <input
                type="password"
                name="password"
                class="form-control"
                required
            >

        </div>

        <div class="mb-4">

            <label class="form-label">
                Konfirmasi Password
            </label>

            <input
                type="password"
                name="confirm_password"
                class="form-control"
                required
            >

        </div>

        <button
            type="submit"
            class="btn btn-primary w-100"
        >
            Daftar sebagai Mitra
        </button>

    </form>

    <div class="text-center mt-4">

        <a href="login.php">
            Sudah punya akun? Login
        </a>

    </div>

</div>

</body>

</html>