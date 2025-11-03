<?php
// admin_nav.php - Reusable Admin Navigation Bar

$current_page = basename($_SERVER['PHP_SELF']);

$nav_items = [
    'DASHBOARD'     => 'admin_dashboard.php',
    'ROOM CHANGES'  => 'admin_change_requests.php',
    'COMPLAINTS'    => 'admin_complaint_tickets.php',
    'ROOM STATUS'   => 'admin_room_status.php',
    'ASSIGNMENTS'   => 'admin_assignment_records.php',
];
?>

<header class="w-full h-[148px] bg-gray-800 font-inter text-white shadow-lg relative">
    <div class="h-full flex items-center justify-between px-6 lg:px-12 relative">

        <a href="admin_dashboard.php" class="h-full flex items-center z-10">
            <div class="flex flex-col items-center justify-center mb-4">
                <img src="images/gmi_logo.png" alt="GMI Logo" class="h-[90px] object-contain mb-1">
                
                <span class="text-[11px] font-extrabold text-gray-300 uppercase tracking-tight leading-none">
                    German-Malaysian Institute
                </span>
            </div>
        </a>
        
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 
                    flex flex-col items-center justify-center h-full">

            <div class="mb-1 mt-4">
                <span class="text-4xl font-extrabold uppercase tracking-widest text-indigo-400">
                    ADMIN PANEL
                </span>
            </div>

            <nav class="flex items-center space-x-8 mt-4">
                <?php foreach ($nav_items as $label => $file_name): ?>
                    <?php 
                        $is_active = ($current_page == $file_name);
                        // Active link styling: Non-rounded indigo border-bottom
                        // Default link text is light (text-gray-300)
                        $active_class = $is_active 
                            ? 'text-indigo-400 border-b-4 border-indigo-400' // Brighter indigo for better contrast
                            : 'text-gray-300 hover:text-indigo-400 hover:border-b-4 hover:border-indigo-700 transition-colors duration-200';
                    ?>
                    <a href="<?php echo htmlspecialchars($file_name); ?>" 
                       class="h-full flex items-center uppercase text-sm font-semibold tracking-wide py-1 <?php echo $active_class; ?>">
                        <?php echo htmlspecialchars($label); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <div class="h-full flex items-center z-10">
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