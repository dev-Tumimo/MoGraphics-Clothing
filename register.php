<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header('Location: orders.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if ($firstName === '' || $lastName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter valid personal details.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $check = $pdo->prepare("SELECT customer_id FROM customers WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = 'An account with that email already exists.';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO customers (first_name, last_name, email, password)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $firstName,
                $lastName,
                $email,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            $_SESSION['customer_id'] = (int)$pdo->lastInsertId();
            $_SESSION['customer_name'] = $firstName;

            header('Location: shop.php');
            exit;
        }
    }
}

$videoAuthPage = true;
$pageTitle = 'Create Account | MoGraphics';
include 'includes/header.php';
?>

<section class="video-bg-page auth-video-page">
    <video class="page-bg-video" autoplay muted loop playsinline>
        <source src="assets/video/hero.mp4" type="video/mp4">
    </video>
    <div class="page-bg-overlay"></div>

    <div class="video-page-content">

    <div class="auth-card">
        <p class="eyebrow">MOGRAPHICS ACCOUNT</p>
        <h1>Create account</h1>

        <?php if ($error): ?>
            <div class="form-message error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" class="stack-form">
            <div class="form-two">
                <div>
                    <label>First name</label>
                    <input type="text" name="first_name" required>
                </div>

                <div>
                    <label>Last name</label>
                    <input type="text" name="last_name" required>
                </div>
            </div>

            <div>
                <label>Email address</label>
                <input type="email" name="email" required>
            </div>

            <div>
                <label>Password</label>
                <input type="password" name="password" minlength="6" required>
            </div>

            <div>
                <label>Confirm password</label>
                <input type="password" name="confirm_password" minlength="6" required>
            </div>

            <button class="pill lime form-button" type="submit">Create account</button>
        </form>

        <p class="auth-switch">
            Already registered? <a href="login.php">Log in</a>
        </p>
    </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
