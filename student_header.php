<?php
// student_header.php — Global header for all student interfaces
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$full_name = $_SESSION['full_name'] ?? 'Student';
?>

<header class="w-full h-[96px] bg-gray-800 font-inter text-white shadow-lg relative">
    <div class="h-full flex items-center justify-between px-6 lg:px-12 relative">

        <a href="student_dashboard.php" class="h-full flex items-center z-10">
            <div class="flex flex-col items-center justify-center">
                <img src="images/gmi_logo.png" alt="GMI Logo" class="h-[60px] object-contain mb-0">
                
                <span class="text-[9px] font-extrabold text-gray-300 uppercase tracking-tight leading-none mb-2">
                    German-Malaysian Institute
                </span>
            </div>
        </a>
        
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 
                    flex items-center justify-center">

            <span class="text-3xl font-extrabold uppercase tracking-widest text-white">
                HOSTEL DASHBOARD
            </span>
        </div>

        <div class="h-full flex items-center z-10 space-x-6">

            <div class="text-right text-sm text-gray-300 hidden sm:block">
                Hello,
                <span class="block font-semibold text-white text-lg">
                    <?php echo htmlspecialchars($full_name); ?>
                </span>
            </div>
            
            <a href="logout.php"
                class="inline-flex items-center gap-2 rounded-full border border-indigo-500 bg-indigo-700 px-5 py-2.5 
                       text-[15px] font-medium text-white hover:bg-indigo-600 hover:border-indigo-400 transition-colors shadow-sm">
                
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 17l1.41-1.41L14.83 13H21v-2h-6.17l2.58-2.59L16 7l-5 5 5 5z"/>
                    <path d="M3 5h8v2H5v10h6v2H3z"/>
                </svg>
                
                LOGOUT
            </a>
        </div>
    </div>
</header>