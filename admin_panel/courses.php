<?php
// Database connection
include ('connect.php');

// Create or edit department
if (isset($_POST['save_department'])) {
    $dept_id = $_POST['dept_id'] ?? null;
    $dept_name = $_POST['dept_name'];

    if ($dept_id) {
        $stmt = $conn->prepare("UPDATE departments SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $dept_name, $dept_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO departments (name) VALUES (?)");
        $stmt->bind_param("s", $dept_name);
    }
    $stmt->execute();
    $stmt->close();
}

// Add or edit course
if (isset($_POST['save_course'])) {
    $course_id = $_POST['course_id'] ?? null;
    $department_id = $_POST['department_id'];
    $course_name = $_POST['course_name'];
    $course_code = $_POST['course_code'];
    $credit_load = $_POST['credit_load'];
    $program = $_POST['program'];
    $level = $_POST['level'];
    $semester = $_POST['semester'];

    if ($course_id) {
        $stmt = $conn->prepare("UPDATE courses SET course_name = ?, course_code = ?, credit_load = ?, program = ?, level = ?, semester = ?, department_id = ? WHERE id = ?");
        $stmt->bind_param("ssisssii", $course_name, $course_code, $credit_load, $program, $level, $semester, $department_id, $course_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO courses (department_id, course_name, course_code, credit_load, program, level, semester) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississs", $department_id, $course_name, $course_code, $credit_load, $program, $level, $semester);
    }
    $stmt->execute();
    $stmt->close();
}

// Fetch all departments
$departments = $conn->query("SELECT * FROM departments ORDER BY name");

// Fetch courses for a specific department
$courses = [];
if (isset($_GET['department_id'])) {
    $department_id = $_GET['department_id'];
    $courses_result = $conn->query("SELECT * FROM courses WHERE department_id = $department_id ORDER BY course_name");
    while ($row = $courses_result->fetch_assoc()) {
        $courses[] = $row;
    }
}
?>

<?php include('partials/_header.php') ?>


<!-- Sidebar -->
<?php include('partials/_sidebar.php') ?>
<input type="hidden" value="4" id="checkFileName">
<!-- End of Sidebar -->



<!-- Main Content -->
<div class="content">
    <!-- Navbar -->
    <?php include("partials/_navbar.php"); ?>

    <!-- End of Navbar -->

    <main>

    <div class="header">
            <div class="left">
                <h1>Manage Departments and Courses</h1>
                <ul class="breadcrumb">
                    <li><a href="#">

                        </a></li>

                </ul>
            </div>

        </div>


  
    <!-- Add/Edit Department Form -->
    <form method="POST" class="needs-validation orders my-4" novalidate>
    <br>
        <div class="header">
            <h3>Add Department </h3>
            <i class='bx filter'></i>
            <div class="limit">
                
            <div class="row g-3 align-items-center">


            </div>
        </div>
    </div>
    <hr>
    <br>


        <input type="hidden" name="dept_id" value="<?= $_GET['edit_dept_id'] ?? '' ?>">
        <div class="mb-3">
            <input type="text" name="dept_name" class="form-control" placeholder="Department Name" required>
            <div class="invalid-feedback">Please enter a department name.</div>
        </div>
        <button type="submit" name="save_department" class="btn btn-primary">Save Department</button>
    </form>

    <!-- Select Department -->
    <br>
        <div class="header">
            <h3>Course Upload </h3>
            <i class='bx filter'></i>
            <div class="limit">
                
            <div class="row g-3 align-items-center">


            </div>
        </div>
    </div>
    <hr>
    <br>
    <form method="GET" class="mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="department" class="col-form-label" style="color:#a9a9a9">Department </label>              
            </div>
            <div class="col-auto">
                <select class="form-select" aria-label="Default select example" name="department_id"onchange="this.form.submit()" required>
                <option value="">-- Select Department --</option>
                <?php while ($row = $departments->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>" <?= (isset($_GET['department_id']) && $_GET['department_id'] == $row['id']) ? 'selected' : '' ?>>
                        <?= $row['name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            </div>
        </div>

    </form>

    <!-- Add/Edit Course Form -->
    <?php if (isset($_GET['department_id'])): ?>
        <form method="POST" class="needs-validation" novalidate>
        <br>
        <div class="header">
            <h3>Add Course </h3>
            <i class='bx filter'></i>
            <div class="limit">
                
            <div class="row g-3 align-items-center">


            </div>
        </div>
    </div>
        <div class="row">
          <div class="col">
            <input  class="form-control" type="hidden" name="course_id" value="<?= $_GET['edit_course_id'] ?? '' ?>">
            <div class="invalid-feedback">Required!</div>
          </div>
          <div class="col">
            <input  class="form-control" type="hidden" name="department_id" value="<?= $_GET['department_id'] ?>">
            <div class="invalid-feedback">Required!</div>
          </div>
        </div>


            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="course_name" class="form-control" placeholder="Course Name" required>
                    <div class="invalid-feedback">Please enter a course name.</div>
                </div>
                <div class="col-md-4">
                    <input type="text" name="course_code" class="form-control" placeholder="Course Code" required>
                    <div class="invalid-feedback">Please enter a course code.</div>
                </div>

                <div class="col-md-4">
                    <input type="number" name="credit_load" class="form-control" placeholder="Credit Load" required>
                    <div class="invalid-feedback">Please enter a credit load.</div>
                </div>
                <div class="col-md-4">
                    <input type="text" name="program" class="form-control" placeholder="Program" required>
                    <div class="invalid-feedback">Please enter the program.</div>
                </div>
                <div class="col-md-4">
                    <input type="text" name="level" class="form-control" placeholder="Level" required>
                    <div class="invalid-feedback">Please enter the level.</div>
                </div>
                <div class="col-md-4">
                    <input type="text" name="semester" class="form-control" placeholder="Semester" required>
                    <div class="invalid-feedback">Please enter the semester.</div>
                </div>
            </div>
            <button type="submit" name="save_course" class="btn btn-primary mt-3">Save Course</button>
        </form>
    <?php endif; ?>

    <!-- List Courses in Selected Department -->
    <?php if (!empty($courses)): ?>
        <div class="header">
            <i class='bx bx-list-ul'></i>
            <h2>Courses in <?= $departments->fetch_assoc()['name'] ?></h2>

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
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        </div>
</div>


<?php endif; ?>
<?php include('partials/_footer.php'); ?>



