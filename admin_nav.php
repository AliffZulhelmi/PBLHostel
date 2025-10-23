<?php
// admin_nav.php - Reusable Admin Navigation Bar

$current_page = basename($_SERVER['PHP_SELF']);
?>

<header class="bg-indigo-800 text-white py-3 px-4 shadow-lg">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <h1 class="text-3xl font-extrabold m-0">Admin Panel</h1>
        <nav class="flex items-center space-x-4">
            <a href="admin_dashboard.php" class="py-1 px-3 rounded <?php echo ($current_page == 'admin_dashboard.php') ? 'bg-indigo-600' : 'hover:bg-indigo-700'; ?>">Dashboard</a>
            <a href="admin_change_requests.php" class="py-1 px-3 rounded <?php echo ($current_page == 'admin_change_requests.php') ? 'bg-indigo-600' : 'hover:bg-indigo-700'; ?>">Room Changes</a>
            <a href="admin_complaint_tickets.php" class="py-1 px-3 rounded <?php echo ($current_page == 'admin_complaint_tickets.php') ? 'bg-indigo-600' : 'hover:bg-indigo-700'; ?>">Complaints</a>
            <a href="admin_room_status.php" class="py-1 px-3 rounded <?php echo ($current_page == 'admin_room_status.php') ? 'bg-indigo-600' : 'hover:bg-indigo-700'; ?>">Room Status</a>
            <a href="admin_assignment_records.php" class="py-1 px-3 rounded <?php echo ($current_page == 'admin_assignment_records.php') ? 'bg-indigo-600' : 'hover:bg-indigo-700'; ?>">Assignments</a>
            <span class="text-indigo-300">|</span>
            <span class="text-sm">Hello, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?></span>
            <a href="logout.php" class="text-sm py-1 px-3 rounded bg-red-600 hover:bg-red-700">Logout</a>
        </nav>
    </div>
</header>