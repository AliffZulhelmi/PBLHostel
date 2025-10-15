<?php
session_start();

// If user is not logged in or not a student, redirect to login page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

// Extract session information
// Note: Keeping the space in 'full _name' as per your original code
$full_name = isset($_SESSION['full _name']) ? $_SESSION['full _name'] : 'Name not found! DB issue';
$student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : 'StudentID not found! DB issue';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    
    <!-- Use the standard and reliable Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom configuration for the font -->
    <style>
        body {
            /* Using Inter as a modern, clean font, similar to Segoe UI */
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 m-0 p-0">

    <!-- Header Section (Tailwind: bg-gray-800, flex, justify-between) -->
    <header class="bg-gray-800 text-white py-3 px-4 flex items-center justify-between shadow-lg">
        
        <!-- Left Header (Tailwind: flex items-center) -->
        <div class="flex items-center">
            
            <!-- Logo Container (Tailwind: mr-4 for margin-right) -->
            <div class="mr-4">
                <!-- Using a placeholder image for the logo -->
                <img src="https://placehold.co/60x60/374151/FFFFFF?text=GMI" alt="GMI Logo" class="h-[60px] rounded-lg">
            </div>
            
            <!-- Title Container -->
            <div>
                <!-- Styling h1 with large font, bold, and minimal margins -->
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold m-0">
                    Hostel Dashboard
                </h1>
            </div>
        </div>

        <!-- User Info / Header Text (Tailwind: text-right, uses responsive classes) -->
        <!-- This replaces the original .bg-gray-400 div and ensures it sits on the right -->
        <div class="hidden sm:block text-right p-2 rounded-lg bg-gray-700 ml-4">
            <p class="text-base sm:text-lg font-semibold text-indigo-400">
                Hello, <strong><?php echo htmlspecialchars($full_name); ?></strong>
            </p>
            <p class="text-sm text-gray-300">
                ID: <strong><?php echo htmlspecialchars($student_id); ?></strong>
            </p>
        </div>
    </header>

    <!-- Main Container (Tailwind: max-w, mx-auto, my-10, padding, bg-white, rounded-xl, shadow) -->
    <div class="max-w-[900px] mx-auto my-10 p-5 bg-white rounded-xl shadow-2xl">

        <!-- Current Room Assignment Section (Tailwind: mb-8 for margin-bottom) -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">Current Room Assignment</h2>
            
            <!-- Room Info Card (Tailwind: bg-indigo-50, p-4, rounded-lg, space-y-2) -->
            <div class="bg-indigo-50 p-4 rounded-lg space-y-2 border border-indigo-200">
                <!-- Replace values dynamically in your implementation -->
                <p><strong class="text-indigo-700">Block:</strong> A2</p>
                <p><strong class="text-indigo-700">Floor:</strong> 3</p>
                <p><strong class="text-indigo-700">Room Number:</strong> 07</p>
                <p><strong class="text-indigo-700">Semester:</strong> Semester 4</p>
            </div>
            
            <!-- Note (Tailwind: text-sm, text-gray-500, mt-3) -->
            <p class="text-sm text-gray-500 mt-3 italic">
                If no room is assigned, please use the registration button below.
            </p>
        </section>

        <!-- Actions Section (Tailwind: mb-8) -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">Actions</h2>
            
            <!-- Actions Block (Tailwind: bg-gray-200, p-4, rounded-lg, gap-3 for spacing) -->
            <div class="bg-gray-200 p-4 rounded-lg flex flex-wrap gap-3">
                
                <!-- Button: Register Room (Tailwind: blue styling, hover/focus effects) -->
                <button onclick="window.location.href='register_room.php'"
                        class="py-2.5 px-4 text-sm bg-blue-600 text-white font-medium rounded-lg cursor-pointer transition duration-300 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transform hover:scale-[1.01]">
                    Register Room
                </button>

                <!-- Button: Request Room Change -->
                <button onclick="window.location.href='#blank'"
                        class="py-2.5 px-4 text-sm bg-blue-600 text-white font-medium rounded-lg cursor-pointer transition duration-300 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transform hover:scale-[1.01]">
                    Request Room Change
                </button>
                
                <!-- Button: Logout (Tailwind: red styling, hover/focus effects) -->
                <button onclick="window.location.href='logout.php'"
                        class="py-2.5 px-4 text-sm bg-red-500 text-white font-medium rounded-lg cursor-pointer transition duration-300 hover:bg-red-600 focus:ring-4 focus:ring-red-300 transform hover:scale-[1.01]">
                    Logout
                </button>
            </div>
        </section>
    </div>
</body>
</html>
