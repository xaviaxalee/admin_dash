<?php
include('connect.php');
 include('partials/_header.php') ;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department = $_POST['department'];
    $level = $_POST['level'];
    $fee_amount = $_POST['fee_amount'];

    $query = $conn->prepare("INSERT INTO school_fees (department, level, fee_amount) 
                             VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE fee_amount = ?");
    $query->bind_param('ssds', $department, $level, $fee_amount, $fee_amount);
    if ($query->execute()) {
        echo "Fees set successfully!";
    } else {
        echo "Error setting fees.";
    }
}
?>



<?php include('partials/_header.php') ?>

<!-- Sidebar -->
<?php include('partials/_sidebar.php') ?>
<input type="hidden" value="8" id="checkFileName">
<!-- End of Sidebar -->



<!-- Main Content -->
<div class="content">
    <!-- Navbar -->
    <?php include("partials/_navbar.php"); ?>

    <!-- End of Navbar -->

    <main>
        <div class="header">
            <div class="left">
                <h1>Fees</h1>
            </div>
        </div>
        <div class="bottom-data">

        <form method="POST">
        <input type="text" name="department" placeholder="Department" required>
        <input type="text" name="level" placeholder="Level" required>
        <input type="number" name="fee_amount" placeholder="Fee Amount" step="0.01" required>
        <button type="submit">Set Fees</button>
        </form>


        </div>

    </main>

</div>

<script src="../assets/js/sllyabus.js"></script>
<?php include('partials/_footer.php'); ?>