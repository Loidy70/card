<?php
session_start();

// Connect to the database
$conn = new mysqli("localhost", "root", "", "student_card_system");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Prepare and execute the statement
    $stmt = $conn->prepare("SELECT id, name, password FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // If email exists
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['admin_id'] = $id;
            $_SESSION['admin_name'] = $name;

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="logo.png">
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-cover bg-center min-h-screen flex items-center justify-center px-4" style="background-image: url('picture.jpg');">
  <form action="admin_login.php" method="POST" class="bg-white bg-opacity-95 backdrop-blur-md p-6 rounded-xl shadow-md w-full max-w-md">
    <div class="flex flex-col items-center mb-4">
    <img src="10yrs.jpg" alt="University of Mpumalanga Logo" class="w-60 h-60 mb-4">
      <h2 class="text-2xl font-bold text-blue-700">Online Student Card Portal</h2>
      <p class="text-sm text-blue-600">Login</p>
    </div>

    <?php if ($error): ?>
      <div class="bg-red-100 text-red-700 p-3 mb-4 rounded text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <input 
      type="email" 
      name="email" 
      placeholder="Email" 
      class="w-full mb-3 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
      required
    >
    <input 
      type="password" 
      name="password" 
      placeholder="Password" 
      class="w-full mb-4 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
      required
    >
    <button 
      type="submit" 
      class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-200"
    >
      Login
    </button>
  </form>
</body>
</html>
