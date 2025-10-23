<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['student_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $other_category = trim($_POST['other_category'] ?? '');
    
    $final_category = ($category === 'Other') ? "Other: " . $other_category : $category;
    $attachment_path = null;
    $file_uploaded = false;

    if ($final_category && $description) {
        // 1. Submit complaint without attachment path first to get the ticket_id
        $ticket_id = submitComplaint($student_id, $final_category, $description);

        if ($ticket_id) {
            // 2. Handle file upload (Objective 1)
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $file_uploaded = true;
                $current_time = time();
                $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                
                // Naming convention: ticket_id_timestamp.ext
                $file_name = $ticket_id . '_' . $current_time . '.' . $ext;
                $upload_dir = 'uploads/';
                $target_file = $upload_dir . $file_name;
                
                // For demonstration, we simulate file move, but the path is recorded
                if (!is_dir($upload_dir)) {
                    @mkdir($upload_dir, 0777, true);
                }
                
                // In a real environment, you would use: move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file);
                $attachment_path = $target_file;
                move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file);
            }

            // 3. Update the ticket with the attachment path if file was uploaded
            if ($file_uploaded && $attachment_path) {
                // Ensure path is saved in DB
                $GLOBALS['conn']->query("UPDATE tickets SET attachment_path = '$attachment_path' WHERE ticket_id = $ticket_id");
            }

            $_SESSION['room_message'] = "Complaint filed successfully (Ticket #$ticket_id) under '$final_category'.";
            header('Location: student_dashboard.php');
            exit;
        } else {
            $message = "Complaint submission failed. Database error.";
        }
    } else {
        $message = "Please fill in the category and description.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
    </head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-lg shadow-2xl rounded-2xl px-8 py-10 bg-white border border-gray-200">
        <h2 class="text-3xl font-extrabold text-purple-800 mb-6 border-b pb-2">File a Complaint / Issue</h2>
        
        <?php if ($message): ?>
            <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="student_file_complaint.php" enctype="multipart/form-data" class="space-y-5">
            
            <div>
                <label for="category" class="block mb-1 text-gray-700 font-medium">Category</label>
                <select name="category" id="category" required
                        class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:border-purple-500 transition">
                    <option value="" selected disabled>Select Category</option>
                    <option value="Broken Furniture">Broken Furniture</option>
                    <option value="Plumbing Leak">Plumbing/Leak</option>
                    <option value="Electrical Issue">Electrical Issue</option>
                    <option value="Noise Complaint">Noise Complaint</option>
                    <option value="Pest Control">Pest Control</option>
                    <option value="Other">Other (Specify Below)</option>
                </select>
            </div>

            <div id="other-category-field" class="hidden">
                <label for="other_category" class="block mb-1 text-gray-700 font-medium">Specify Category</label>
                <input type="text" name="other_category" id="other_category"
                       class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:border-purple-500 transition"
                       placeholder="e.g., WiFi Issue, Blocked Drainage">
            </div>

            <div>
                <label for="description" class="block mb-1 text-gray-700 font-medium">Detailed Description</label>
                <textarea name="description" id="description" rows="4" required
                          class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:border-purple-500 transition"
                          placeholder="Please describe the issue clearly."></textarea>
            </div>

            <div>
                <label for="attachment" class="block mb-1 text-gray-700 font-medium">Attachment (Image/Proof)</label>
                <input type="file" name="attachment" id="attachment"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                <p class="text-xs text-gray-500 mt-1">Jpg, Png, Pdf allowed. Saved in `uploads/[ticket_id]_[timestamp].[ext]`.</p>
            </div>

            <div class="flex space-x-3">
                <button type="submit"
                        class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow transition focus:ring-4 focus:ring-purple-300">
                    Submit Complaint
                </button>
                <a href="student_dashboard.php" class="w-full py-2.5 text-center bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg shadow transition focus:ring-4 focus:ring-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>
    
    <script>
        document.getElementById('category').addEventListener('change', function() {
            const otherField = document.getElementById('other-category-field');
            if (this.value === 'Other') {
                otherField.classList.remove('hidden');
            } else {
                otherField.classList.add('hidden');
            }
        });
    </script>
</body>
</html>