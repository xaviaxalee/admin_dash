<?php include('partials/_header.php'); ?>
<style>
    table tr td, table tr th {
        color: #a9a9a9;
        padding: 10px;
        text-align: center;
    }
    table tr td:last-child, table tr th:last-child {
        width: 20%;
    }
    .edit-form {
        display: flex;
        position: absolute;
        top: 0;
        z-index: 333;
        padding: 20px;
        flex-direction: column;
        width: 100%;
        background-color: #fff;
        margin: 0 auto;
        justify-content: space-between;
        align-items: center;
    }
    .edit-form form {
        padding: 20px;
        flex-direction: column;
        width: 60%;
        background-color: #fff;
        margin: 0 auto;
        justify-content: space-between;
        align-items: center;
    }
    .edit-form form div {
        display: flex;
        flex-direction: column;
        margin: 10px 0;
        padding: 10px;
    }
    .edit-form form div input {
        padding: 10px;
        border: 1px solid #a9a9a9;
        border-radius: 5px;
    }
</style>
<?php
require_once 'connect.php';

// Fetch students
$students = [];
$sql = "SELECT * FROM students";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Handle Delete Action
if (isset($_GET['delete_student_id'])) {
    $delete_student_id = intval($_GET['delete_student_id']);
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $delete_student_id);
    if ($stmt->execute()) {
        echo "<script>alert('Student deleted successfully.'); window.location.href = 'student.php';</script>";
    } else {
        echo "<script>alert('Failed to delete student. Please try again.');</script>";
    }
    $stmt->close();
}

// Handle Edit Action
if (isset($_GET['edit_student_id'])) {
    $edit_student_id = intval($_GET['edit_student_id']);
    // Fetch student details
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $edit_student_id);
    $stmt->execute();
    $student_to_edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
        // Update student details
        $full_name = $_POST['full_name'];
        $student_type = $_POST['student_type'];
        $jamb_registration_no = $_POST['jamb_registration_no'];
        $matric_no = $_POST['matric_no'];
        $program = $_POST['program'];
        $department = $_POST['department'];
        $level = $_POST['level'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'];
        $home_address = $_POST['home_address'];
        $campus_address = $_POST['campus_address'];
        $parent_name = $_POST['parent_name'];
        $parent_phone = $_POST['parent_phone'];
        $parent_email = $_POST['parent_email'];
        $parent_address = $_POST['parent_address'];

        $stmt = $conn->prepare(
            "UPDATE students SET full_name = ?, student_type = ?, jamb_registration_no = ?, matric_no = ?, 
             program = ?, department = ?, level = ?, phone_number = ?, email = ?, home_address = ?, campus_address = ?, 
             parent_name = ?, parent_phone = ?, parent_email = ?, parent_address = ? WHERE id = ?"
        );
        $stmt->bind_param(
            "sssssssssssssssi",
            $full_name, $student_type, $jamb_registration_no, $matric_no, $program, $department,
            $level, $phone_number, $email, $home_address, $campus_address, $parent_name, 
            $parent_phone, $parent_email, $parent_address, $edit_student_id
        );
        if ($stmt->execute()) {
            echo "<script>alert('Student updated successfully.'); window.location.href = 'student.php';</script>";
        } else {
            echo "<script>alert('Failed to update student. Please try again.');</script>";
        }
        $stmt->close();
    }
}

// Fetch departments for dropdown
$departments = [];
$sql = "SELECT id, department_name FROM departments"; // Assuming `id` and `department_name` columns exist
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}
?>

<!-- Sidebar -->
<?php include('partials/_sidebar.php'); ?>

<div class="content">
    <!-- Navbar -->
    <?php include("partials/_navbar.php"); ?>
    <!-- End of Navbar -->

    <main>
        <div class="header">
            <h1>Student Management</h1>
        </div>
        <div class="bottom-data">
            <div class="orders">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Student Type</th>
                                <th>JAMB Reg No</th>
                                <th>Matric No</th>
                                <th>Program</th>
                                <th>Department</th>
                                <th>Level</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?= $student['full_name'] ?></td>
                                    <td><?= ucfirst($student['student_type']) ?></td>
                                    <td><?= $student['jamb_registration_no'] ?></td>
                                    <td><?= $student['matric_no'] ?></td>
                                    <td><?= $student['program'] ?></td>
                                    <td><?= $student['department'] ?></td>
                                    <td><?= $student['level'] ?></td>
                                    <td>
                                        <a href="?edit_student_id=<?= $student['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                        <a href="?delete_student_id=<?= $student['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php if (isset($student_to_edit)): ?>
        <div class="edit-form">
            <h3>Edit Student</h3>
            <form method="POST">
                <div><label>Full Name:</label><input type="text" name="full_name" value="<?= $student_to_edit['full_name'] ?>" required></div>
                <div><label>Student Type:</label>
                    <select name="student_type" required>
                        <option value="new" <?= $student_to_edit['student_type'] === 'new' ? 'selected' : '' ?>>New</option>
                        <option value="returning" <?= $student_to_edit['student_type'] === 'returning' ? 'selected' : '' ?>>Returning</option>
                    </select>
                </div>
                <div><label>JAMB Registration No:</label><input type="text" name="jamb_registration_no" value="<?= $student_to_edit['jamb_registration_no'] ?>"></div>
                <div><label>Matric No:</label><input type="text" name="matric_no" value="<?= $student_to_edit['matric_no'] ?>"></div>
                <div><label>Program:</label><input type="text" name="program" value="<?= $student_to_edit['program'] ?>" required></div>
                <div><label>Department:</label><input type="text" name="department" value="<?= $student_to_edit['department'] ?>"></div>
                <div><label>Level:</label><input type="text" name="level" value="<?= $student_to_edit['level'] ?>" required></div>
                <div><label>Phone Number:</label><input type="text" name="phone_number" value="<?= $student_to_edit['phone_number'] ?>" required></div>
                <div><label>Email:</label><input type="email" name="email" value="<?= $student_to_edit['email'] ?>" required></div>
                <div><label>Home Address:</label><textarea name="home_address" required><?= $student_to_edit['home_address'] ?></textarea></div>
                <div><label>Campus Address:</label><input type="text" name="campus_address" value="<?= $student_to_edit['campus_address'] ?>"></div>
                <div><label>Parent Name:</label><input type="text" name="parent_name" value="<?= $student_to_edit['parent_name'] ?>"></div>
                <div><label>Parent Phone:</label><input type="text" name="parent_phone" value="<?= $student_to_edit['parent_phone'] ?>"></div>
                <div><label>Parent Email:</label><input type="email" name="parent_email" value="<?= $student_to_edit['parent_email'] ?>"></div>
                <div><label>Parent Address:</label><textarea name="parent_address"><?= $student_to_edit['parent_address'] ?></textarea></div>
                <button type="submit" name="update_student" class="btn btn-primary">Update Student</button>
                <a href="student.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    <?php endif; ?>
</div>

<script src="../assets/js/attendenceShowToAdmin.js"></script>
<?php include('partials/_footer.php'); ?>
