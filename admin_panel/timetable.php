

<?php include("../assets/noSessionRedirect.php"); ?>
<?php include("./verifyRoleRedirect.php"); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/x-icon" href="../images/1.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    

    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <title>School Management</title>
    <link rel="icon" type="image/x-icon" href="images/1.png">

   


</head>

<body>

    <div class='toast-container position-fixed text-success bottom-0 end-0 p-3'>
        <div id='liveToast' class='toast' role='alert' aria-live='assertive' aria-atomic='true' style="color:black;">
            <div class='d-flex'>
                <div class='toast-body' id="toast-alert-message">

                </div>
                <button type='button' class='btn-close me-2 m-auto text-danger' data-bs-dismiss='toast'
                    aria-label='Close'></button>
            </div>
        </div>
    </div>
    <!-- Sidebar -->
    <?php include('partials/_sidebar.php') ?>
    <input type="hidden" value="7" id="checkFileName">
    <!-- End of Sidebar -->

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <?php include("partials/_navbar.php"); ?>

        <!-- End of Navbar -->

        <main>
            <div class="header">
                <div class="left">
                    <h1>Time Table</h1>
                </div>
            </div>

            <!-- Body -->
            <div class="bottom-data">

                <div class="orders">
                    <!-- Tab panes -->
                    <div class="header">
                <div class="left">
                    <h3>pending</h3>
                </div>
            </div>
                </div>

                <!-- end of body -->

        </main>

    </div>




    <script src="../assets/js/timetable.js"></script>
    <?php include('partials/_footer.php'); ?>