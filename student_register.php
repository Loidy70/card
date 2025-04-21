<?php
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "student_card_system");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $student_number = trim($_POST['student_number']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($name) < 3 || strlen($surname) < 3) {
        $error = "Name and Surname must be at least 3 letters.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email or student number already exists
        $stmt = $conn->prepare("SELECT id FROM students WHERE email = ? OR student_number = ?");
        if ($stmt === false) {
            die('Error preparing the query: ' . $conn->error);
        }

        $stmt->bind_param("ss", $email, $student_number);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email or Student Number already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO students (name, surname, student_number, email, password) VALUES (?, ?, ?, ?, ?)");
            if ($insert === false) {
                die('Error preparing insert query: ' . $conn->error);
            }

            $insert->bind_param("sssss", $name, $surname, $student_number, $email, $hashed_password);

            if ($insert->execute()) {
                $success = "Registration successful! You can now log in.";

                // Redirect to student login page after successful registration
                header("Location: student_login.php");
                exit; // Ensure no further code is executed
            } else {
                $error = "Error registering student: " . $insert->error;
            }

            $insert->close();
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" href="logo.png">
  <title>Student Registration</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-cover bg-center min-h-screen flex items-center justify-center px-4" style="background-image: url('picture.jpg');">

  <form action="student_register.php" method="POST"
    class="bg-white bg-opacity-85 backdrop-blur-md border border-blue-100 p-6 rounded-xl shadow-lg w-full max-w-md"
    style="background-color: rgba(240, 249, 255, 0.85);"
  >
    <div class="flex flex-col items-center mb-4">
      <img src="10yrs.jpg" alt="University of Mpumalanga Logo" class="w-60 h-60 mb-4">
      <h1 class="text-xl font-bold text-blue-700 text-center">Online Student Card Portal</h1>
      <p class="text-sm text-blue-600">Register</p>
    </div>

    <?php if ($error): ?>
      <div class="bg-red-100 text-red-700 p-3 mb-4 rounded text-sm"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="bg-green-100 text-green-700 p-3 mb-4 rounded text-sm"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <input 
      type="text" 
      id="name"
      name="name" 
      placeholder="Name" 
      class="w-full mb-3 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
      required pattern="[A-Za-z]{3,}" 
      title="Name must be at least 3 letters"
    >

    <input 
      type="text" 
      id="surname"
      name="surname" 
      placeholder="Surname" 
      class="w-full mb-3 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
      required pattern="[A-Za-z]{3,}" 
      title="Surname must be at least 3 letters"
    >

    <input 
      type="text" 
      id="student_number"
      name="student_number" 
      placeholder="Student Number" 
      class="w-full mb-3 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
      required
    >

    <input 
      type="email" 
      id="email"
      name="email" 
      placeholder="Student Email" 
      class="w-full mb-3 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
      required
    >

    <input 
      type="password" 
      id="password"
      name="password" 
      placeholder="Password (8+ characters)" 
      class="w-full mb-3 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
      required minlength="8"
    >

    <input 
      type="password" 
      id="confirm_password"
      name="confirm_password" 
      placeholder="Confirm Password" 
      class="w-full mb-4 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" 
      required minlength="8"
    >

    <button 
      type="submit" 
      class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-200"
    >
      Register
    </button>
     
    <p class="mt-4 text-sm text-center text-gray-600">
      Already have an account?
      <a href="student_login.php" class="text-blue-600 hover:underline">login</a>
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
