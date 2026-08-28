<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    $loginDestination = 'orders.php';
        $_SESSION['post_login_transition'] = true;
        $_SESSION['post_login_destination'] = $loginDestination;

        header('Location: login_transition.php');
        exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $customer = $stmt->fetch();

    if ($customer && password_verify($password, $customer['password'])) {
        $_SESSION['customer_id'] = (int)$customer['customer_id'];
        $_SESSION['customer_name'] = $customer['first_name'];

        $redirect = $_SESSION['redirect_after_login'] ?? 'shop.php';
        unset($_SESSION['redirect_after_login']);

        // Play the cinematic transition after a successful login
        // before continuing to the requested page.
        $_SESSION['post_login_destination'] = $redirect;

        header('Location: login_transition.php');
        exit;
    }

    $error = 'Incorrect email address or password.';
}

$videoAuthPage = true;
$pageTitle = 'Login | MoGraphics';
include 'includes/header.php';
?>

<section class="video-bg-page auth-video-page">
    <video class="page-bg-video" autoplay muted loop playsinline>
        <source src="assets/video/hero.mp4" type="video/mp4">
    </video>
    <div class="page-bg-overlay"></div>

    <div class="video-page-content">

    <div class="auth-card">
        <p class="eyebrow">WELCOME BACK</p>
        <h1>Log in</h1>

        <?php if ($error): ?>
            <div class="form-message error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" class="stack-form">
            <div>
                <label>Email address</label>
                <input type="email" name="email" required>
            </div>

            <div>
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button class="pill lime form-button" type="submit">Log in</button>
        </form>

        <p class="auth-switch">
            New to MoGraphics? <a href="register.php">Create an account</a>
        </p>
    </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
