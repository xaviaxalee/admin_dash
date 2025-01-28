<?php include('partials/_header.php') ?>
<style>
    table tr td, table tr th{
        color: #a9a9a9;
        padding: 10px;
        text-align: center;
    }
    table tr td:last-child, table tr th:last-child{
        width: 20%;
    }
    .edit-form{
        display: flex;
        z-index: 333;
        padding: 20px;
        flex-direction: column;
        width: 50%;
        background-color: #fff;
        margin: 0 auto;
        justify-content: space-between;
       align-items: center;
    }
    .edit-form form{
        z-index: 333;
        padding: 20px;
        flex-direction: column;
        width: 100%;
        background-color: #fff;
        margin: 0 auto;
        justify-content: space-between;
       align-items: center;
    }
    .edit-form form div{
        display: flex;
        flex-direction: column;
        margin: 10px 0;
        padding: 10px;
    }
    .edit-form form div input{
        padding: 10px;
        border: 1px solid #a9a9a9;
        border-radius: 5px;
    }
</style>
<?php
require_once 'connect.php';

$departments = [];
$sql = "SELECT * FROM departments";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}

$selected_department_id = isset($_GET['department_id']) ? intval($_GET['department_id']) : null;
$selected_department_name = null;
$courses = [];

if ($selected_department_id) {
    $stmt = $conn->prepare("SELECT name FROM departments WHERE id = ?");
    $stmt->bind_param("i", $selected_department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $selected_department_name = $result->fetch_assoc()['name'];
    }
    $stmt->close();

    $programs = [];
    $levels = [];
    
    // Fetch unique programs
    $stmt = $conn->prepare("SELECT DISTINCT program FROM courses WHERE department_id = ?");
    $stmt->bind_param("i", $selected_department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $programs[] = $row['program'];
        }
    }
    $stmt->close();

    // Fetch unique levels
    $stmt = $conn->prepare("SELECT DISTINCT level FROM courses WHERE department_id = ?");
    $stmt->bind_param("i", $selected_department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $levels[] = $row['level'];
        }
    }
    $stmt->close();

    $program_filter = isset($_GET['program_filter']) ? $_GET['program_filter'] : '';
    $level_filter = isset($_GET['level_filter']) ? $_GET['level_filter'] : '';

    $query = "SELECT * FROM courses WHERE department_id = ?";
    $params = [$selected_department_id];
    $types = "i";

    if (!empty($program_filter)) {
        $query .= " AND program = ?";
        $params[] = $program_filter;
        $types .= "s";
    }

    if (!empty($level_filter)) {
        $query .= " AND level = ?";
        $params[] = $level_filter;
        $types .= "s";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $courses = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Handle Delete Action
if (isset($_GET['delete_course_id'])) {
    $delete_course_id = intval($_GET['delete_course_id']);
    
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $delete_course_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Course deleted successfully.'); window.location.href = '?department_id={$selected_department_id}';</script>";
    } else {
        echo "<script>alert('Failed to delete course. Please try again.');</script>";
    }
    
    $stmt->close();
}


// Handle Edit Action
if (isset($_GET['edit_course_id'])) {
    $edit_course_id = intval($_GET['edit_course_id']);
    
    // Fetch course details
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param("i", $edit_course_id);
    $stmt->execute();
    $course_to_edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_course'])) {
        $course_name = $_POST['course_name'];
        $course_code = $_POST['course_code'];
        $credit_load = $_POST['credit_load'];
        $program = $_POST['program'];
        $level = $_POST['level'];
        $semester = $_POST['semester'];
        
        // Update course
        $stmt = $conn->prepare("UPDATE courses SET course_name = ?, course_code = ?, credit_load = ?, program = ?, level = ?, semester = ? WHERE id = ?");
        $stmt->bind_param("ssisssi", $course_name, $course_code, $credit_load, $program, $level, $semester, $edit_course_id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Course updated successfully.'); window.location.href = '?department_id={$selected_department_id}';</script>";
        } else {
            echo "<script>alert('Failed to update course. Please try again.');</script>";
        }
        
        $stmt->close();
    }
}

?>

<!-- Sidebar -->
<?php include('partials/_sidebar.php') ?>
<input type="hidden" value="5" id="checkFileName">
<!-- End of Sidebar -->

<!-- Main Content -->
<div class="content">
    <!-- Navbar -->
    <?php include("partials/_navbar.php"); ?>

    <!-- End of Navbar -->

    <main>
        <div class="header">
            <div class="left">
                <h1>Manage Courses</h1>
                <ul class="breadcrumb">
                    <li><a href="#">

                        </a></li>

                </ul>
            </div>

        </div>
        <div class="bottom-data">

            <div class="orders">
                <!-- Nav tabs -->
   
        <!-- Department Selection -->
        <form method="GET" class="mb-4">
            <label for="department_id" class="form-label" style="color:#a9a9a9">Select Department:</label>
            <select class="form-select" id="department_id" name="department_id" onchange="this.form.submit()">
                <option value="">-- Select Department --</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?= $department['id'] ?>" <?= $selected_department_id == $department['id'] ? 'selected' : '' ?>>
                        <?= $department['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Filter by Program and Level -->
        <?php if (!empty($courses)): ?>
            <form method="GET" class="mb-4 mt-4">
                <input type="hidden" name="department_id" value="<?= $selected_department_id ?>">
                
                <div class="row g-3 align-items-center">
                    <!-- Program Filter -->
                    <div class="col-md-6">
                        <label for="program_filter" class="form-label" style="color:#a9a9a9">Filter by Program:</label>
                        <select class="form-select" name="program_filter" onchange="this.form.submit()">
                            <option value="">-- All Programs --</option>
                            <?php foreach ($programs as $program): ?>
                                <option value="<?= $program ?>" <?= isset($_GET['program_filter']) && $_GET['program_filter'] == $program ? 'selected' : '' ?>>
                                    <?= $program ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Level Filter -->
                    <div class="col-md-6">
                        <label for="level_filter" class="form-label" style="color:#a9a9a9">Filter by Level:</label>
                        <select class="form-select" name="level_filter" onchange="this.form.submit()">
                            <option value="">-- All Levels --</option>
                            <?php foreach ($levels as $level): ?>
                                <option value="<?= $level ?>" <?= isset($_GET['level_filter']) && $_GET['level_filter'] == $level ? 'selected' : '' ?>>
                                    <?= $level ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>
        <?php endif; ?>

        <!-- Display Courses -->
        <?php if ($selected_department_id): ?>
            <?php if (!empty($courses)): ?>
                <div class="header">
                    <i class='bx bx-list-ul'></i>
                    <h3>Courses in <?= $selected_department_name ?></h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Course Code</th>
                                <th>Credit Load</th>
                                <th>Program</th>
                                <th>Level</th>
                                <th>Semester</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?= $course['course_name'] ?></td>
                                    <td><?= $course['course_code'] ?></td>
                                    <td><?= $course['credit_load'] ?></td>
                                    <td><?= $course['program'] ?></td>
                                    <td><?= $course['level'] ?></td>
                                    <td><?= $course['semester'] ?></td>
                                    <td>
                                        <a href="?department_id=<?= $course['department_id'] ?>&edit_course_id=<?= $course['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                                        <a href="?department_id=<?= $course['department_id'] ?>&delete_course_id=<?= $course['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No courses found for the selected department, program, or level.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

                 
           </div>


    </main>

    <?php if (isset($course_to_edit)): ?>
    <div class="edit-form">
        <h3>Edit Course</h3>
        <form method="POST">
            <div>
                <label>Course Name:</label>
                <input type="text" name="course_name" value="<?= $course_to_edit['course_name'] ?>" required>
            </div>
            <div>
                <label>Course Code:</label>
                <input type="text" name="course_code" value="<?= $course_to_edit['course_code'] ?>" required>
            </div>
            <div>
                <label>Credit Load:</label>
                <input type="number" name="credit_load" value="<?= $course_to_edit['credit_load'] ?>" required>
            </div>
            <div>
                <label>Program:</label>
                <input type="text" name="program" value="<?= $course_to_edit['program'] ?>" required>
            </div>
            <div>
                <label>Level:</label>
                <input type="text" name="level" value="<?= $course_to_edit['level'] ?>" required>
            </div>
            <div>
                <label>Semester:</label>
                <input type="text" name="semester" value="<?= $course_to_edit['semester'] ?>" required>
            </div>
            <button type="submit" name="update_course" class="btn btn-primary">Update Course</button>
            <a href="?department_id=<?= $selected_department_id ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
<?php endif; ?>


</div>

<script src="../assets/js/attendenceShowToAdmin.js"></script>
<!-- <script src="../assets/js/attendence.js"></script> -->

<?php include('partials/_footer.php'); ?>