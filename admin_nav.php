<?php
// admin_nav.php - Reusable Admin Navigation Bar

$current_page = basename($_SERVER['PHP_SELF']);
$full_name = $_SESSION['full_name'] ?? 'Admin';
?>

<header class="bg-gray-800 border-b border-gray-700 text-white py-3 px-4 flex items-center justify-between shadow-lg">
    <div class="flex items-center">
    <div class="mr-4">
        <img src="images/gmi_logo.png" alt="GMI Logo" class="h-[60px] object-contain rounded-lg">
    </div>
    <div>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold m-0 mt-4">Admin Panel</h1>
    </div>
    </div>
        <nav class="flex items-center space-x-4">
            <a href="admin_dashboard.php" class="py-1 px-3 rounded <?php echo ($current_page == 'admin_dashboard.php') ? 'bg-indigo-600' : 'hover:bg-indigo-700'; ?>">Dashboard</a>
            <a href="admin_change_requests.php" class="py-1 px-3 rounded <?php echo ($current_page == 'admin_change_requests.php') ? 'bg-indigo-600' : 'hover:bg-indigo-700'; ?>">Room Changes</a>
            <a href="admin_complaint_tickets.php" class="py-1 px-3 rounded <?php echo ($current_page == 'admin_complaint_tickets.php') ? 'bg-indigo-600' : 'hover:bg-indigo-700'; ?>">Complaints</a>
            <a href="admin_room_status.php" class="py-1 px-3 rounded <?php echo ($current_page == 'admin_room_status.php') ? 'bg-indigo-600' : 'hover:bg-indigo-700'; ?>">Room Status</a>
            <a href="admin_assignment_records.php" class="py-1 px-3 rounded <?php echo ($current_page == 'admin_assignment_records.php') ? 'bg-indigo-600' : 'hover:bg-indigo-700'; ?>">Assignments</a>
            <span class="text-indigo-300">|</span>
            <span class="ext-indigo-200 text-base sm:text-lg">
                Hello, <span class="font-semibold text-white"><?php echo htmlspecialchars($full_name); ?></span>
            </span>
            <button onclick="window.location.href='logout.php'"
                    class="text-gray-400 hover:text-white transition-colors duration-200 flex items-center space-x-1">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
            </button>
        </nav>
    </div>
</header>