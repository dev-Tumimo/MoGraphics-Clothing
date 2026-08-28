<?php
require_once 'config/database.php';

if (empty($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}

$destination = $_SESSION['post_login_destination'] ?? 'shop.php';
unset($_SESSION['post_login_transition'], $_SESSION['post_login_destination']);

// Only allow local customer-facing destinations.
$parts = parse_url($destination);
$destinationPath = basename($parts['path'] ?? 'shop.php');

$allowedPages = [
    'index.php',
    'shop.php',
    'product.php',
    'cart.php',
    'checkout.php',
    'orders.php',
    'login.php',
    'register.php'
];

if (!in_array($destinationPath, $allowedPages, true)) {
    $destination = 'shop.php';
    $destinationPath = 'shop.php';
}

$pageNames = [
    'index.php' => 'Home',
    'shop.php' => 'Shop',
    'product.php' => 'Product',
    'cart.php' => 'Cart',
    'checkout.php' => 'Checkout',
    'orders.php' => 'My Account',
    'login.php' => 'Login',
    'register.php' => 'Register'
];

$destinationName = $pageNames[$destinationPath] ?? 'Shop';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | MoGraphics</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        html,
        body {
            margin: 0;
            min-height: 100%;
            background: #0c0c0c;
        }

        body.login-success-transition {
            opacity: 1;
            transform: none;
            filter: none;
            overflow: hidden;
            font-family: "Times New Roman", Times, serif;
        }

        .login-success-screen {
            position: fixed;
            inset: 0;
            z-index: 999999;
            display: grid;
            place-items: center;
            overflow: hidden;
            background: #0c0c0c;
            color: #fff;
        }

        .login-success-screen::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(
                    180deg,
                    rgba(0, 0, 0, 0.16),
                    rgba(0, 0, 0, 0.52)
                );
            z-index: 1;
        }

        .login-success-video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.38;
        }

        .login-success-curtain {
            position: absolute;
            inset: 0;
            z-index: 2;
            background: #0c0c0c;
            transform: translateY(0);
            animation: loginCurtainExit 1s cubic-bezier(0.76, 0, 0.24, 1) 1.05s forwards;
        }

        .login-success-copy {
            position: relative;
            z-index: 4;
            width: min(92vw, 1100px);
            text-align: center;
        }

        .login-success-kicker {
            display: block;
            margin-bottom: 14px;
            font-size: clamp(13px, 1vw, 17px);
            text-transform: uppercase;
            letter-spacing: 0.22em;
            opacity: 0;
            transform: translateY(16px);
            animation: loginTextIn 0.5s ease 0.15s forwards;
        }

        .login-success-title {
            display: block;
            font-size: clamp(58px, 10vw, 150px);
            font-weight: 400;
            line-height: 0.86;
            letter-spacing: -0.055em;
            opacity: 0;
            transform: translateY(42px);
            animation: loginTextIn 0.7s cubic-bezier(0.22, 1, 0.36, 1) 0.22s forwards;
        }

        .login-success-line {
            position: absolute;
            z-index: 5;
            left: 0;
            top: 0;
            width: 100%;
            height: 2px;
            background: var(--lime, #d7e600);
            transform: scaleX(0);
            transform-origin: left center;
            animation: loginLineIn 0.55s cubic-bezier(0.76, 0, 0.24, 1) 0.38s forwards;
        }

        .login-success-screen.exit .login-success-copy {
            animation: loginCopyOut 0.45s ease forwards;
        }

        @keyframes loginTextIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes loginLineIn {
            to {
                transform: scaleX(1);
            }
        }

        @keyframes loginCurtainExit {
            to {
                transform: translateY(-100%);
            }
        }

        @keyframes loginCopyOut {
            to {
                opacity: 0;
                transform: translateY(-30px);
            }
        }

        @media (max-width: 768px) {
            .login-success-copy {
                width: calc(100vw - 36px);
            }

            .login-success-title {
                font-size: clamp(48px, 17vw, 82px);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .login-success-kicker,
            .login-success-title,
            .login-success-line,
            .login-success-curtain,
            .login-success-copy {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }
        }
    </style>
</head>
<body class="login-success-transition">
    <section class="login-success-screen" id="loginSuccessScreen">
        <video class="login-success-video" autoplay muted loop playsinline>
            <source src="assets/video/hero.mp4" type="video/mp4">
        </video>

        <div class="login-success-curtain"></div>
        <div class="login-success-line"></div>

        <div class="login-success-copy">
            <span class="login-success-kicker">Welcome back · Going to</span>
            <strong class="login-success-title"><?= htmlspecialchars($destinationName) ?></strong>
        </div>
    </section>

    <script>
        const destination = <?= json_encode($destination) ?>;
        const destinationName = <?= json_encode($destinationName) ?>;
        const screen = document.getElementById("loginSuccessScreen");

        sessionStorage.setItem("mgPageTransition", "1");
        sessionStorage.setItem("mgDestinationName", destinationName);

        window.setTimeout(() => {
            screen.classList.add("exit");
        }, 1150);

        window.setTimeout(() => {
            window.location.href = destination;
        }, 1850);
    </script>
</body>
</html>
