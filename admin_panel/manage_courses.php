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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Manage Courses</h1>

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
                    <h2>Courses in <?= $selected_department_name ?></h2>
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
</body>
</html>
