<?php
session_start();
include 'db.php';

// Redirect function
function redirect($url) {
    header("Location: $url");
    exit;
}

// Ensure the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    redirect("index.php");
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        // Update course outline entry
        $id = $_POST['id'];
        $topic = $_POST['topic'];
        $category = $_POST['category'];
        $description = $_POST['description'];

        $updateQuery = "UPDATE course_outline SET topic = ?, category = ?, description = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssi", $topic, $category, $description, $id);
        
        if ($stmt->execute()) {
            redirect("admin_course_outline_edit.php");
        } else {
            echo "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    }

    if (isset($_POST['add'])) {
        // Add new course outline entry
        $topic = $_POST['new_topic'];
        $category = $_POST['new_category'];
        $description = $_POST['new_description'];

        $insertQuery = "INSERT INTO course_outline (topic, category, description) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sss", $topic, $category, $description);
        
        if ($stmt->execute()) {
            redirect("admin_accounts.php");
        } else {
            echo "Error adding record: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch course outline data for display
$result = $conn->query("SELECT * FROM course_outline");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> 
    <link rel="stylesheet" href="modal.css"> <!-- Include separate CSS for modals -->
    <title>Admin Accounts</title>
</head>
<body>
<div class="top-bar">
    <div class="user-info">
        <img class="logo-img logo" src="images/agronomy_logo.png" alt="Logo">
        <div class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
    </div>
    <div class="button-group" style="margin-left: auto;">
        <button class="btn" onclick="window.location.href='admin_dashboard.php'">Dashboard</button>
        <button class="btn" style="background-color: #888;" onclick="window.location.href='admin_course_outline_edit.php'">Course</button>
        <button class="btn" onclick="window.location.href='admin_accounts.php'">Accounts</button>
        <button class="btn" onclick="window.location.href='admin_question_pool.php'">Questions Pool</button>
        <button class="btn" onclick="window.location.href='admin_archive_questions.php'">Archive Questions</button>
        <button class="btn" onclick="window.location.href='admin_question_stat.php'">Questions Statistics</button>
        <button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
        </div>
</div>

<div class="admin-container">
    <h2>Course Outline</h2>
    <table>
        <thead>
            <tr>
                <th>Topic</th>
                <th>Category</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['topic']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <button class="info-btn" onclick="openEditPopup(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['topic']); ?>', '<?php echo htmlspecialchars($row['category']); ?>', '<?php echo htmlspecialchars($row['description']); ?>')">Edit</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <button class="btn" onclick="openAddPopup()">Add Course Topic</button>
</div>

<!-- Edit Popup -->
<div id="editPopup" class="popup">
    <div class="popup-content">
        <span class="popup-close" onclick="closeEditPopup()">&times;</span>
        <h3>Edit Course Outline</h3>
        <form method="POST" action="">
            <input type="hidden" name="id" id="editId">
            <label for="editTopic">Topic:</label>
            <input type="text" name="topic" id="editTopic" required>
            
            <label for="editCategory">Category:</label>
            <!-- Dropdown for category selection -->
            <select name="category" id="editCategory" required>
                <option value="Category 1">Category 1</option>
                <option value="Category 2">Category 2</option>
                <option value="Category 3">Category 3</option>
                <option value="Category 4">Category 4</option>
            </select>
            
            <label for="editDescription">Description:</label>
            <textarea name="description" id="editDescription" required></textarea>
            <div class="popup-buttons">
                <button type="submit" name="update" class="btn-primary">Update</button>
                <button type="button" class="btn-secondary" onclick="closeEditPopup()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Popup -->
<div id="addPopup" class="popup">
    <div class="popup-content">
        <span class="popup-close" onclick="closeAddPopup()">&times;</span>
        <h3>Add New Course Outline</h3>
        <form method="POST" action="">
            <label for="newTopic">Topic:</label>
            <input type="text" name="new_topic" id="newTopic" required>
            
            <label for="newCategory">Category:</label>
            <!-- Dropdown for category selection -->
            <select name="new_category" id="newCategory" required>
                <option value="Category 1">Category 1</option>
                <option value="Category 2">Category 2</option>
                <option value="Category 3">Category 3</option>
                <option value="Category 4">Category 4</option>
            </select>
            
            <label for="newDescription">Description:</label>
            <textarea name="new_description" id="newDescription" required></textarea>
            <div class="popup-buttons">
                <button type="submit" name="add" class="btn-primary">Add</button>
                <button type="button" class="btn-secondary" onclick="closeAddPopup()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditPopup(id, topic, category, description) {
    document.getElementById('editId').value = id;
    document.getElementById('editTopic').value = topic;
    document.getElementById('editCategory').value = category; // Make sure this is selecting the correct option
    document.getElementById('editDescription').value = description;
    document.getElementById('editPopup').style.display = 'block';
}

function closeEditPopup() {
    document.getElementById('editPopup').style.display = 'none';
}

function openAddPopup() {
    document.getElementById('addPopup').style.display = 'block';
}

function closeAddPopup() {
    document.getElementById('addPopup').style.display = 'none';
}
</script>

</body>
</html>
