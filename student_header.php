<?php
// student_header.php — Global header for all student interfaces
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$full_name = $_SESSION['full_name'] ?? 'Student';
?>

<header class="bg-gray-800 border-b border-gray-700 text-white py-3 px-4 flex items-center justify-between shadow-lg">
    <div class="flex items-center">
        <div class="mr-4">
            <img src="images/gmi_logo.png" alt="GMI Logo" class="h-[60px] object-contain rounded-lg">
        </div>
        <div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold m-0 mt-4">
                Hostel Dashboard
            </h1>
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <span class="text-gray-300 text-base sm:text-lg">
            Hello, <span class="font-semibold text-white"><?php echo htmlspecialchars($full_name); ?></span>
        </span>
        <button onclick="window.location.href='logout.php'"
            class="text-gray-400 hover:text-white transition-colors duration-200 flex items-center space-x-1">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </button>
    </div>
</header>