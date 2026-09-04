<?php

require_once __DIR__ . "/../config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE email = ?
        AND role = 'admin'
    ");

    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user'] = $user;

        header("Location: index.php");
        exit;

    } else {

        $error = "Email atau password admin salah.";

    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Mitra</title>

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

        .login-card {
            width: 400px;
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

    </style>

</head>

<body>

<div class="login-card">

    <h3 class="text-center fw-bold mb-2">
        Login Mitra
    </h3>

    <p class="text-center text-muted mb-4">
        Masuk ke Dashboard Mitra
    </p>

    <?php if ($error): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="post">

        <div class="mb-3">

            <label class="form-label">
                Email
            </label>

            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Masukkan email admin"
                required
            >

        </div>

        <div class="mb-4">

            <label class="form-label">
                Password
            </label>

            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Masukkan password"
                required
            >

        </div>

        <button
            type="submit"
            class="btn btn-primary w-100"
        >
            Login sebagai Mitra
        </button>

    </form>

    <div class="text-center mt-4">

        <p class="mb-2">
            Belum punya akun Mitra?
        </p>

        <a
            href="register.php"
            class="btn btn-outline-primary w-100"
        >
            Daftar sebagai Mitra
        </a>

        <div class="mt-3">

            <a href="../login.php">
                Login sebagai Pengguna
            </a>

        </div>

    </div>

</div>

</body>

</html>