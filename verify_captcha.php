<?php
session_start();

if (!isset($_SESSION['verified_login'])) {
    header("Location: student_login.php");
    exit;
}

$error = $_GET['error'] ?? "";
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="captcha.png">
  <title>Verify CAPTCHA</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">

  <form action="process_captcha.php" method="POST" class="bg-white p-8 rounded-xl shadow-md w-full max-w-sm">
    <h2 class="text-xl font-semibold text-center mb-4 text-blue-700">Verify You're Human</h2>

    <?php if ($error): ?>
      <div class="bg-red-100 text-red-700 p-3 mb-4 rounded text-sm text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="g-recaptcha mb-4" data-sitekey="6LeSPhwrAAAAAJLMScagjJvOoUjJPdEFeXR0YPG4"></div>

    <button 
      type="submit" 
      class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-200"
    >
      Continue
    </button>
  </form>

</body>
</html>
