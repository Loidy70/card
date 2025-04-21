<?php
// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "student_card_system");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Basic validations
    if (strlen($name) < 3) {
        $errors[] = "Name must be at least 3 characters.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        $errors[] = "Email is already registered.";
    }

    $checkEmail->close();

    // If no errors, insert into DB
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $success = "Admin registered successfully!";
        } else {
            $errors[] = "Registration failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/png" href="logo.png">
  <title>Admin Registration - University of Mpumalanga</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-cover bg-center min-h-screen flex items-center justify-center px-4" style="background-image: url('picture.jpg');">

<body class="bg-gradient-to-br from-blue-100 via-green-100 to-blue-200 min-h-screen flex items-center justify-center px-4">
  <div class="bg-white shadow-xl rounded-xl w-full max-w-md p-8">
    <div class="flex flex-col items-center mb-6">
    <img src="10yrs.jpg" alt="University of Mpumalanga Logo" class="w-60 h-60 mb-4">
      <h1 class="text-xl font-bold text-blue-700 text-center">Online Student Card Portal</h1>
      <p class="text-sm text-blue-600">Admin Registration</p>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <ul class="list-disc list-inside">
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php elseif ($success): ?>
      <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="admin_register.php" class="space-y-4">
      <input 
        type="text" 
        id="admin_name"
        name="name" 
        placeholder="Full Name (min 3 letters)" 
        class="w-full p-3 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
        required 
        minlength="3"
      >

      <input 
        type="email" 
        id="admin_email"
        name="email" 
        placeholder="Email address" 
        class="w-full p-3 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
        required
      >

      <input 
        type="password" 
        id="admin_password"
        name="password" 
        placeholder="Password (min 8 characters)" 
        class="w-full p-3 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
        required 
        minlength="8"
      >

      <button 
        type="submit" 
        id="register_admin_btn"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition duration-200"
      >
        Register
      </button>
    </form>
  </div>
</body>
</html>
