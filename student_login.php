<?php
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "student_card_system");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $student_number = trim($_POST['student_number']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM students WHERE student_number = ?");
    $stmt->bind_param("s", $student_number);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['student_id'] = $id;
            $_SESSION['verified_login'] = true;
            header("Location: verify_captcha.php");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Student number not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Login</title>
  <link rel="icon" type="image/png" href="logo.png">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-cover bg-center min-h-screen flex items-center justify-center px-4" style="background-image: url('picture.jpg');">

  <form action="student_login.php" method="POST"
    class="bg-white bg-opacity-85 backdrop-blur-md border border-blue-100 p-6 rounded-xl shadow-lg w-full max-w-md"
    style="background-color: rgba(240, 249, 255, 0.85);"
  >
    <div class="flex flex-col items-center mb-4">
      <img src="10yrs.jpg" alt="University of Mpumalanga Logo" class="w-60 h-60 mb-4">
      <h1 class="text-xl font-bold text-blue-700 text-center">Online Student Card Portal</h1>
      <p class="text-sm text-blue-600">Login</p>
    </div>

    <?php if ($error): ?>
      <div class="bg-red-100 text-red-700 p-3 mb-4 rounded text-sm"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <input 
      type="text" 
      name="student_number" 
      placeholder="Student Number" 
      required 
      class="w-full mb-3 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
    >

    <input 
      type="password" 
      name="password" 
      placeholder="Password" 
      required 
      class="w-full mb-4 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
    >

    <button 
      type="submit" 
      class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-200"
    >
      Login
    </button>

    <p class="mt-4 text-sm text-center text-gray-600">
      Don't have an account?
      <a href="student_register.php" class="text-blue-600 hover:underline">Register</a>
    </p>
  </form>

  <a 
    href="homepage.html" 
    class="absolute bottom-4 left-4 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200 text-sm"
  >
    ← Back to Home
  </a>

</body>
</html>
