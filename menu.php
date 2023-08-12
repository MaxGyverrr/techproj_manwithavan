<!-- Main menu items -->
<li>
    <a href="index.php">Home</a>
</li>



<!-- Common menu items -->
<?php if (isset($_SESSION["usertype"]) && $_SESSION["usertype"] != "") { ?>
    <li>
        <a href="booking.php">Book a Service</a>
    </li>
<?php } ?>



<!-- Main menu items -->
<li>
    <a href="vehicles.php">Vehicles</a>
</li>
<li>
    <a href="about.php">About Us</a>
</li>


<!-- Dropdown menu for admin -->
<?php if (isset($_SESSION["usertype"]) && $_SESSION["usertype"] === "admin") { ?>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin <span class="caret"></span></a>
        <ul class="dropdown-menu" style="background-color: #de542fff;">
            <li><a href="manage_bookings.php">Edit Bookings</a></li>
            <li><a href="register_drivers.php">Register Drivers</a></li>
            <li><a href="register_vehicles.php">Register Vehicles</a></li>
            <li><a href="Financial.php">Financial</a></li>
        </ul>
    </li>
<?php } ?>



<!-- Common menu items -->
<?php if (isset($_SESSION["usertype"]) && $_SESSION["usertype"] != "") { ?>
    <li>
        <a style="font-size: smaller" href="logout.php">Logout</a>
    </li>
    <li>
        <a style="font-weight: bolder; font-size: large; color: white; background-color: rgba(217, 217, 217, 0.2);">Hello, <?php echo $_SESSION['firstname']; ?>!</a>
    </li>
<?php } ?>


<!-- User types -->
<?php if (isset($_SESSION["usertype"]) && $_SESSION["usertype"] === "admin") { ?>
    <li>
        <a style="font-weight: bold; background-color: #003cb3; color: white;">Admin</a>
    </li>
<?php } ?>

<?php if (isset($_SESSION["usertype"]) && $_SESSION["usertype"] === "driver") { ?>
    <li>
        <a style="font-weight: bold; background-color: #003cb3; color: white;">Driver</a>
    </li>
<?php } ?>

<?php if (isset($_SESSION["usertype"]) && $_SESSION["usertype"] === "customer") { ?>
    <li>
        <a style="font-weight: bold; background-color: #003cb3; color: white;">Customer</a>
    </li>
<?php } ?>