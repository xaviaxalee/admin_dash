<?php include('partials/_header.php') ?>

<!-- Sidebar -->
<?php include('partials/_sidebar.php') ?>
<input type="hidden" value="10" id="checkFileName">
<!-- End of Sidebar -->

<!-- Main Content -->
<div class="content">
    <!-- Navbar -->
    <?php include("partials/_navbar.php"); ?>

    <!-- End of Navbar -->

    <main>
        <div class="header">
            <div class="left">
                <h1>Marks</h1>
                <!-- <ul class="breadcrumb">
                    <li><a href="#">
                        </a></li>
                </ul> -->
            </div>

        </div>
        <div class="bottom-data">

        <div class="orders">
                    <!-- Tab panes -->
                    <div class="header">
                <div class="left">
                    <h3>pending</h3>
                </div>
            </div>
                


        </div>

    </main>

</div>
<span id="hiddenBOX" style="display:none;width:0px;height:0px;"></span>



<script src="../assets/js/marks.js"></script>
<?php include('partials/_footer.php'); ?>