<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="logo.png">
    <title>Student Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Navigation Bar -->
    <nav class="bg-blue-600 text-white p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="student_dashboard.php" class="text-xl font-semibold">Welcome!</a>
            <div class="space-x-4">
                <a href="student_dashboard.php" class="hover:text-gray-200">Dashboard</a>
                <a href="status.html" class="hover:text-gray-200">Request Status</a>
                <a href="student_card.php" class="hover:text-gray-200">Student Card</a>
            </div>
            <!-- Logout Button at the top right -->
            <a href="homepage.html" class="bg-red-600 text-white py-2 px-4 rounded-lg text-sm hover:bg-red-700 transition duration-200">
                Logout
            </a>
        </div>
    </nav>

    <?php
    session_start();
    include 'db_connection.php';

    if (!isset($_SESSION['student_id'])) {
        header("Location: student_login.php");
        exit;
    }

    $student_id = $_SESSION['student_id'];
    $error = "";
    $success_message = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $title = $_POST['title'];
        $qualification_code = $_POST['qualification_code'];
        $phone_number = $_POST['phone_number'];

        $photo = $_FILES['photo'];
        $photo_name = $photo['name'];
        $photo_tmp_name = $photo['tmp_name'];
        $photo_error = $photo['error'];
        $photo_size = $photo['size'];

        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if ($photo_error === 0 && in_array($photo['type'], $allowed_types) && $photo_size < 5000000) {
            $new_photo_name = uniqid('', true) . '.' . pathinfo($photo_name, PATHINFO_EXTENSION);
            $photo_dest = 'uploads/' . $new_photo_name;
            move_uploaded_file($photo_tmp_name, $photo_dest);
        } else {
            $new_photo_name = 'default_photo.jpg';
        }

        $sql = "SELECT * FROM student_details WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $sql = "UPDATE student_details SET name = ?, surname = ?, title = ?, qualification_code = ?, preferred_phone = ?, photo = ? WHERE student_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $name, $surname, $title, $qualification_code, $phone_number, $new_photo_name, $student_id);
        } else {
            $sql = "INSERT INTO student_details (student_id, name, surname, title, qualification_code, preferred_phone, photo) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssss", $student_id, $name, $surname, $title, $qualification_code, $phone_number, $new_photo_name);
        }

        if ($stmt->execute()) {
            $success_message = "Details updated successfully.";
        } else {
            $error = "Error updating details: " . $conn->error;
        }

        $stmt->close();
        $conn->close();
    }
    ?>

    <div class="w-full max-w-7xl bg-white rounded-lg shadow-lg overflow-hidden flex flex-row space-x-8 mt-4">
        <!-- Dashboard Content -->
        <div class="w-2/3 p-6 space-y-6">
            <form action="" method="POST" enctype="multipart/form-data" class="bg-gray-50 p-4 rounded-lg shadow-md space-y-4">
                <h2 class="text-xl font-semibold text-gray-800">Update Your Details</h2>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" id="name" name="name" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div>
                    <label for="surname" class="block text-sm font-medium text-gray-700">Surname</label>
                    <input type="text" id="surname" name="surname" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <select name="title" id="title" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                        <option value="Mr">Mr</option>
                        <option value="Ms">Ms</option>
                    </select>
                </div>

                <div>
                    <label for="qualification_code" class="block text-sm font-medium text-gray-700">Qualification Code</label>
                    <input type="text" id="qualification_code" name="qualification_code" placeholder="e.g BSC" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" placeholder="e.g +2734567890" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700">Preferred Photo</label>
                    <input type="file" id="photo" name="photo" accept="image/*" class="w-full p-2 border rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                    Update Details
                </button>
            </form>

            <!-- Display Success or Error Message -->
            <?php if ($success_message): ?>
                <div class="bg-green-100 text-green-700 p-3 mt-4 rounded text-sm"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 p-3 mt-4 rounded text-sm"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
        </div>

        <!-- Calendar Section -->
        <div class="flex flex-col items-center bg-white bg-opacity-90 backdrop-blur-sm border border-blue-100 rounded-xl shadow-lg p-5 max-w-sm mx-auto h-fit">
            <h2 class="text-xl font-semibold text-center text-gray-800 mb-4">Calendar</h2>
            <div id="calendar" class="bg-white rounded-lg shadow-lg p-5 mb-4"></div>
        </div>
    </div>

    <script>
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth();
        const currentYear = currentDate.getFullYear();
        const todayDate = currentDate.getDate();

        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        function createCalendar(month, year) {
            const firstDay = new Date(year, month).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            let calendarHtml = `<div class="text-center text-lg font-bold">${months[month]} ${year}</div>`;
            calendarHtml += `<div class="grid grid-cols-7 gap-1 mt-4">`;

            const dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
            dayNames.forEach(day => {
                calendarHtml += `<div class="text-center font-semibold">${day}</div>`;
            });

            for (let i = 0; i < firstDay; i++) {
                calendarHtml += `<div class="text-center"></div>`;
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const isToday = day === todayDate && month === currentMonth && year === currentYear;
                const todayClass = isToday ? 'bg-blue-500 text-white' : '';
                calendarHtml += `<div class="text-center py-2 hover:bg-blue-200 rounded-lg ${todayClass}">${day}</div>`;
                if ((firstDay + day) % 7 === 0) {
                    calendarHtml += `</div><div class="grid grid-cols-7 gap-1 mt-4">`;
                }
            }

            calendarHtml += `</div>`;
            document.getElementById('calendar').innerHTML = calendarHtml;
        }

        createCalendar(currentMonth, currentYear);
    </script>
</body>
</html>
