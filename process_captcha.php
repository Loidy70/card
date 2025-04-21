<?php
session_start();

if (!isset($_SESSION['verified_login'])) {
    header("Location: student_login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $secret = "6LeSPhwrAAAAAO6mHJDkP5zYTixyLyLBZxCpN5l_";
    $captcha_response = $_POST['g-recaptcha-response'];

    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$captcha_response}");
    $responseData = json_decode($verify);

    if ($responseData->success) {
        unset($_SESSION['verified_login']);
        header("Location: student_dashboard.php");
        exit;
    } else {
        header("Location: verify_captcha.php?error=CAPTCHA failed. Try again.");
        exit;
    }
}
?>

