<?php
// PHP file for consistency, although content is largely static documentation.
// Does not require session or database connection.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Documentation - Hostel Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .toc a { transition: color 0.2s; }
        .toc a:hover { color: #4f46e5; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">

    <header class="bg-indigo-800 text-white py-4 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h1 class="text-3xl font-extrabold">Hostel Management System (HMS) Documentation</h1>
            <a href="index.php" class="bg-white text-indigo-800 font-semibold py-2 px-4 rounded-lg hover:bg-gray-200 transition">
                Home
            </a>
        </div>
    </header>

    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-4 gap-10">

            <aside class="lg:col-span-1">
                <div class="sticky top-10 bg-white p-6 rounded-xl shadow-lg">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Contents</h2>
                    <nav class="space-y-2 text-gray-600 toc">
                        <a href="#arch" class="block hover:text-indigo-600">1. System Architecture</a>
                        <a href="#roles" class="block hover:text-indigo-600">2. User Roles & Access</a>
                        <a href="#db" class="block hover:text-indigo-600">3. Database Schema</a>
                        <a href="#functions" class="block hover:text-indigo-600">4. Core Functions (`conn.php`)</a>
                        <a href="#flows" class="block hover:text-indigo-600">5. Key User Flows</a>
                        <a href="#files" class="block hover:text-indigo-600">6. File Reference</a>
                    </nav>
                </div>
            </aside>

            <main class="lg:col-span-3 space-y-12">

                <section id="arch" class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-indigo-500">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">1. System Architecture</h2>
                    <p class="text-gray-600 mb-4">The HMS is built as a monolithic PHP application using a MySQL database and styled with Tailwind CSS via CDN. It follows a functional programming approach, separating database interaction logic from the presentation layer.</p>
                    <h3 class="text-xl font-semibold text-gray-700 mt-6 mb-2">Key Architectural Principles:</h3>
                    <ul class="list-disc list-inside space-y-2 ml-4 text-gray-600">
                        <li>**Database Abstraction:** All functions that directly query the database are contained within `conn.php` to centralize data operations.</li>
                        <li>**State Management:** User authentication and role (`student` / `admin`) are managed via PHP sessions (`$_SESSION`).</li>
                        <li>**Dynamic Capacity:** Room availability is managed dynamically via `available_capacity` and `total_capacity` columns in the `rooms` table, replacing simple status checks.</li>
                    </ul>
                </section>

                <section id="roles" class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-indigo-500">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">2. User Roles & Access</h2>
                    
                    <div class="space-y-6">
                        <div class="border p-4 rounded-lg bg-indigo-50">
                            <h4 class="text-xl font-semibold text-indigo-700">Admin Role (`role = 'admin'`)</h4>
                            <p class="text-sm text-gray-600 mt-1">Admin accounts (e.g., `ADMIN001`) have full supervisory control.</p>
                            <ul class="list-disc list-inside space-y-1 ml-4 text-gray-600 text-sm mt-2">
                                <li>View Capacity Analysis and live Room Status.</li>
                                <li>Approve/Reject Room Change Requests (via the `tickets` table).</li>
                                <li>Manage all Complaint Tickets (update status, delete ticket).</li>
                                <li>Manually Edit/Release Student Assignments.</li>
                                <li>Change Room Status (e.g., to 'Broken', 'Under Maintenance') which unassigns current tenants.</li>
                            </ul>
                        </div>

                        <div class="border p-4 rounded-lg bg-green-50">
                            <h4 class="text-xl font-semibold text-green-700">Student Role (`role = 'student'`)</h4>
                            <p class="text-sm text-gray-600 mt-1">Student accounts manage their own accommodation and issues.</p>
                            <ul class="list-disc list-inside space-y-1 ml-4 text-gray-600 text-sm mt-2">
                                <li>**Auto Room Registration:** Automated assignment based on gender and capacity.</li>
                                <li>**Checkout Room:** Self-release from the room, restoring capacity.</li>
                                <li>**Request Room Change:** Submits a request ticket to the Admin.</li>
                                <li>**File Complaint:** Submits general issue tickets with optional file attachment.</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section id="db" class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-indigo-500">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">3. Database Schema</h2>
                    <p class="text-gray-600 mb-4">The system utilizes five key tables. Note the use of **string primary keys** for `users` and `rooms` to simplify foreign key lookups.</p>
                    
                    <div class="space-y-6">
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <h4 class="bg-gray-800 text-white p-3 font-mono text-sm">TABLE: users (Authentication & Student Details)</h4>
                            <table class="w-full text-sm text-left text-gray-600">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-6 py-3">Column</th><th class="px-6 py-3">Type</th><th class="px-6 py-3">Notes</th></tr></thead>
                                <tbody>
                                    <tr class="border-b"><td class="px-6 py-4 font-medium text-gray-900">student_id</td><td class="px-6 py-4">VARCHAR(50)</td><td class="px-6 py-4">**PRIMARY KEY**, Unique identifier for all users.</td></tr>
                                    <tr class="border-b"><td class="px-6 py-4">email</td><td class="px-6 py-4">VARCHAR(255)</td><td class="px-6 py-4">Unique, used for login.</td></tr>
                                    <tr class="border-b"><td class="px-6 py-4">password</td><td class="px-6 py-4">VARCHAR(255)</td><td class="px-6 py-4">Plain text (Non-secure practice, for functionality focus).</td></tr>
                                    <tr><td class="px-6 py-4">role</td><td class="px-6 py-4">VARCHAR(20)</td><td class="px-6 py-4">`student` or `admin`.</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <h4 class="bg-gray-800 text-white p-3 font-mono text-sm">TABLE: rooms (Room Inventory & Dynamic Capacity)</h4>
                            <table class="w-full text-sm text-left text-gray-600">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-6 py-3">Column</th><th class="px-6 py-3">Type</th><th class="px-6 py-3">Notes</th></tr></thead>
                                <tbody>
                                    <tr class="border-b"><td class="px-6 py-4 font-medium text-gray-900">room_identifier</td><td class="px-6 py-4">VARCHAR(20)</td><td class="px-6 py-4">**PRIMARY KEY** (e.g., A4-01-001).</td></tr>
                                    <tr class="border-b"><td class="px-6 py-4">total_capacity</td><td class="px-6 py-4">INT(11)</td><td class="px-6 py-4">Maximum capacity (static).</td></tr>
                                    <tr class="border-b"><td class="px-6 py-4">available_capacity</td><td class="px-6 py-4">INT(11)</td><td class="px-6 py-4">**Dynamic** tracking of available slots.</td></tr>
                                    <tr><td class="px-6 py-4">status</td><td class="px-6 py-4">VARCHAR(50)</td><td class="px-6 py-4">`Available`, `Occupied`, `Broken`, `Under Maintenance`, etc.</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <h4 class="bg-gray-800 text-white p-3 font-mono text-sm">TABLE: student_rooms (Assignment Records)</h4>
                            <table class="w-full text-sm text-left text-gray-600">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-6 py-3">Column</th><th class="px-6 py-3">Type</th><th class="px-6 py-3">Notes</th></tr></thead>
                                <tbody>
                                    <tr class="border-b"><td class="px-6 py-4">student_id</td><td class="px-6 py-4">VARCHAR(50)</td><td class="px-6 py-4">**FOREIGN KEY** to `users`.</td></tr>
                                    <tr class="border-b"><td class="px-6 py-4">room_identifier</td><td class="px-6 py-4">VARCHAR(20)</td><td class="px-6 py-4">**FOREIGN KEY** to `rooms`.</td></tr>
                                    <tr><td class="px-6 py-4">status</td><td class="px-6 py-4">VARCHAR(50)</td><td class="px-6 py-4">`Active`, `Released`, `Released by Admin`, etc.</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <h4 class="bg-gray-800 text-white p-3 font-mono text-sm">TABLE: tickets (Complaints & Change Requests)</h4>
                            <table class="w-full text-sm text-left text-gray-600">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-6 py-3">Column</th><th class="px-6 py-3">Type</th><th class="px-6 py-3">Notes</th></tr></thead>
                                <tbody>
                                    <tr class="border-b"><td class="px-6 py-4">student_id</td><td class="px-6 py-4">VARCHAR(50)</td><td class="px-6 py-4">**FOREIGN KEY** to `users`.</td></tr>
                                    <tr class="border-b"><td class="px-6 py-4">category</td><td class="px-6 py-4">VARCHAR(100)</td><td class="px-6 py-4">`Room Change`, `Broken Furniture`, etc.</td></tr>
                                    <tr class="border-b"><td class="px-6 py-4">attachment_path</td><td class="px-6 py-4">VARCHAR(255)</td><td class="px-6 py-4">Path to uploaded proof (in `uploads/` directory).</td></tr>
                                    <tr><td class="px-6 py-4">status</td><td class="px-6 py-4">VARCHAR(20)</td><td class="px-6 py-4">For changes: `Pending`/`Approved`/`Rejected`. For complaints: `Open`/`Resolved`/etc.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section id="functions" class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-indigo-500">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">4. Core Functions (`conn.php`)</h2>
                    <p class="text-gray-600 mb-6">These functions encapsulate all database operations and business logic.</p>

                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Room Assignment & Capacity</h3>
                    <ul class="list-disc list-inside space-y-1 ml-4 text-sm text-gray-600 mb-6">
                        <li>`findavailableroom($blocks)`: **RE-ADDED** and crucial for auto-assignment; returns the first room identifier where `available_capacity > 0`.</li>
                        <li>`assignstudenttoroom($student_id, $room_identifier)`: Creates a new `Active` assignment record.</li>
                        <li>`updateRoomCapacityAfterAssignment($room_identifier)`: Decrements `available_capacity` and sets status to `Occupied` if capacity reaches zero.</li>
                        <li>`checkoutStudentFromRoom(...)` / `updateRoomCapacityAfterCheckout(...)`: Handles student release and increments room capacity (+1).</li>
                        <li>`updateRoomStatusAndUnassign(...)`: Used by Admin to set statuses like `Broken` or `Under Maintenance`; automatically unassigns all tenants and resets capacity to `total_capacity`.</li>
                    </ul>
                    
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Tickets & Requests</h3>
                    <ul class="list-disc list-inside space-y-1 ml-4 text-sm text-gray-600 mb-6">
                        <li>`submitComplaint(...)`: Submits both general complaints and room change requests, stores file path. Returns the new `ticket_id` (used for file naming).</li>
                        <li>`approveRoomChange(...)`: Complex Transaction: releases old assignment, increments old room capacity, creates new active assignment, decrements new room capacity, and marks ticket as `Approved`.</li>
                        <li>`deleteTicket($ticket_id)`: Deletes the ticket record (and associated attachment path is noted for manual file deletion).</li>
                        <li>`getRoomChangeRequests()`: Retrieves pending/processed room change tickets for Admin review.</li>
                    </ul>
                </section>

                <section id="flows" class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-indigo-500">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">5. Key User Flows</h2>

                    <h4 class="text-xl font-semibold text-gray-700 mt-6 mb-2">A. Student Flow: Room Registration</h4>
                    <p class="text-sm text-gray-600 ml-4">`student_dashboard.php` > **Register Room** button > `room_register.php` (Auto-assignment).</p>
                    <ul class="list-disc list-inside space-y-1 ml-8 text-xs text-gray-600 mt-2">
                        <li>Checks if student has an active assignment.</li>
                        <li>Determines eligible blocks based on gender (e.g., A4/A5 for male, A1 for female).</li>
                        <li>Assigns the first available room with `available_capacity > 0`.</li>
                    </ul>

                    <h4 class="text-xl font-semibold text-gray-700 mt-6 mb-2">B. Admin Flow: Room Change Approval</h4>
                    <p class="text-sm text-gray-600 ml-4">`admin_dashboard.php` > **Room Changes** link > `admin_change_requests.php`.</p>
                    <ul class="list-disc list-inside space-y-1 ml-8 text-xs text-gray-600 mt-2">
                        <li>Admin views pending requests showing OLD and NEW room IDs.</li>
                        <li>Clicking **Approve** triggers a database transaction (`approveRoomChange` in `conn.php`).</li>
                        <li>Both Admin and Student receive a pop-up confirmation message.</li>
                    </ul>
                </section>

                <section id="files" class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-indigo-500">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">6. File Reference</h2>
                    <table class="w-full text-sm text-left text-gray-600 border-collapse">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-4 py-2">File</th><th class="px-4 py-2">Role</th><th class="px-4 py-2">Purpose</th></tr></thead>
                        <tbody>
                            <tr class="border-b bg-indigo-50"><td class="px-4 py-2 font-medium">conn.php</td><td class="px-4 py-2">Core</td><td class="px-4 py-2">Database connection and ALL back-end logic functions.</td></tr>
                            <tr class="border-b"><td class="px-4 py-2">index.php</td><td class="px-4 py-2">General</td><td class="px-4 py-2">Welcome/Landing Page; handles role-based redirection.</td></tr>
                            <tr class="border-b"><td class="px-4 py-2">login.php / register.php</td><td class="px-4 py-2">Auth</td><td class="px-4 py-2">User authentication and new student account creation.</td></tr>
                            <tr class="border-b bg-green-50"><td class="px-4 py-2 font-medium">student_dashboard.php</td><td class="px-4 py-2">Student</td><td class="px-4 py-2">Main student view; displays current room and all action buttons.</td></tr>
                            <tr class="border-b"><td class="px-4 py-2">room_register.php</td><td class="px-4 py-2">Student</td><td class="px-4 py-2">Executes auto room assignment script.</td></tr>
                            <tr class="border-b"><td class="px-4 py-2">student_file_complaint.php</td><td class="px-4 py-2">Student</td><td class="px-4 py-2">Form for submitting general complaints with attachment upload.</td></tr>
                            <tr class="border-b bg-red-50"><td class="px-4 py-2 font-medium">admin_dashboard.php</td><td class="px-4 py-2">Admin</td><td class="px-4 py-2">Overview and Capacity Analysis (Chart.js graph).</td></tr>
                            <tr class="border-b"><td class="px-4 py-2">admin_nav.php</td><td class="px-4 py-2">Admin</td><td class="px-4 py-2">Reusable navigation bar for admin sub-pages.</td></tr>
                            <tr class="border-b"><td class="px-4 py-2">admin_room_status.php</td><td class="px-4 py-2">Admin</td><td class="px-4 py-2">Live, collapsible list of all rooms with status management.</td></tr>
                            <tr><td class="px-4 py-2">admin_assignment_records.php</td><td class="px-4 py-2">Admin</td><td class="px-4 py-2">Record of all room assignments with sort/edit capabilities.</td></tr>
                        </tbody>
                    </table>
                </section>
                
            </main>
        </div>
    </div>
</body>
</html>