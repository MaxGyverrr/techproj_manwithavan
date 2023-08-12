<?php
// Start and handle the $_Session
require_once 'functions/session_protection.php';

header("Content-Type: text/html; charset=UTF-8", true);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Database connection
require 'functions/connection.php';

// Function to escape HTML entities for output
function escapeHtml($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Handles the login form
if (isset($_POST['email'])) {


    $email = $_POST['email'];
    $passw = $_POST['passw'];

    try {
        $stmt = $conn->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user !== false && password_verify($passw, $user['passw'])) {
            echo "<script>alert('Login Successful!');window.location='index.php';</script>";
            
            unset($_SESSION['failed_email']);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['usertype'] = $user['usertype'];

        } else {
            $_SESSION['failed_email'] = $email; // Store the entered email
            echo "<script>alert('Login Failed! Incorrect Email or Password');window.location='index.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        // if an exception occurs, it displays on the screen
        echo "Error: " . $e->getMessage();
    }
}


// Check if the form is submitted to update the booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $service_status = protection($_POST['service_status']);
    $estimated_price = protection($_POST['estimated_price']);
    $driver_id = protection($_POST['driver_id']);
    $vehicle_id = protection($_POST['vehicle_id']);
    $release_another_booking = protection($_POST['release_another_booking']);

    // Prepare the UPDATE query
    $stmt = $conn->prepare('UPDATE bookings SET service_status = :service_status, estimated_price = :estimated_price, driver_id = :driver_id, vehicle_id = :vehicle_id, release_another_booking = :release_another_booking WHERE booking_id = :booking_id');
    $stmt->bindValue(':booking_id', $booking_id);
    $stmt->bindValue(':service_status', $service_status);
    $stmt->bindValue(':estimated_price', $estimated_price);
    $stmt->bindValue(':driver_id', $driver_id);
    $stmt->bindValue(':vehicle_id', $vehicle_id);
    $stmt->bindValue(':release_another_booking', $release_another_booking);

    // Execute the update query
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->rowCount() > 0) {
        echo "Booking updated successfully.";
    } else {
        echo "Error updating booking: " . print_r($stmt->errorInfo(), true);
    }
}

// Check if a booking is being edited
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_booking_id'])) {
    $edit_booking_id = $_POST['edit_booking_id'];
    // Retrieve the booking data from the database
    $booking_query = 'SELECT bookings.*, users.firstname, users.phone, users.nationality, vehicles.make, vehicles.model FROM bookings LEFT JOIN users ON bookings.user_id = users.user_id LEFT JOIN vehicles ON bookings.vehicle_id = vehicles.vehicle_id WHERE booking_id = :booking_id';
    $stmt_booking = $conn->prepare($booking_query);
    $stmt_booking->bindValue(':booking_id', $edit_booking_id);
    $stmt_booking->execute();
    $booking_to_edit = $stmt_booking->fetch(PDO::FETCH_ASSOC);

    // data for drivers
    $driver_query = "SELECT * FROM users WHERE usertype = 'driver' AND user_status = 'active'";
    $stmt_drivers = $conn->prepare($driver_query);
    $stmt_drivers->execute();
    $drivers = $stmt_drivers->fetchAll(PDO::FETCH_ASSOC);

    // data for vehicles
    $driver_query = "SELECT * FROM vehicles WHERE vehicle_id = vehicle_id AND vehicle_status = 'active'";
    $stmt_vehicles = $conn->prepare($driver_query);
    $stmt_vehicles->execute();
    $vehicles_v = $stmt_vehicles->fetchAll(PDO::FETCH_ASSOC);
}


// Check if the "Confirm Booking" button was clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking_id'])) {
    $confirm_booking_id = $_POST['confirm_booking_id'];
    // Update the service_status to 'Confirmed' for the corresponding booking in the database
    $stmt = $conn->prepare('UPDATE bookings SET service_status = :service_status WHERE booking_id = :booking_id');
    $stmt->bindValue(':service_status', 'Confirmed');
    $stmt->bindValue(':booking_id', $confirm_booking_id);
    $stmt->execute();
}

// Check if the "Confirm Booking" button was clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_booking_id'])) {
    $complete_booking_id = $_POST['complete_booking_id'];
    // Update the service_status to 'Confirmed' for the corresponding booking in the database
    $stmt = $conn->prepare('UPDATE bookings SET service_status = :service_status WHERE booking_id = :booking_id');
    $stmt->bindValue(':service_status', 'Completed');
    $stmt->bindValue(':booking_id', $complete_booking_id);
    $stmt->execute();
}

// Check if the "Cancel booking" button was clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'])) {
    $cancel_booking_id = $_POST['cancel_booking_id'];
    // Update the service_status to 'Canceled' for the corresponding booking in the database
    $stmt = $conn->prepare('UPDATE bookings SET service_status = :service_status WHERE booking_id = :booking_id');
    $stmt->bindValue(':service_status', 'Canceled');
    $stmt->bindValue(':booking_id', $cancel_booking_id);
    $stmt->execute();
}



// Build the SQL query
$ComandoSQL = "SELECT bookings.*, users.firstname, users.phone, vehicles.make, vehicles.model FROM bookings LEFT JOIN users ON bookings.user_id = users.user_id LEFT JOIN vehicles ON bookings.vehicle_id = vehicles.vehicle_id";

// Get the selected date from the form submission
$filterDate = $_POST['filterDate'] ?? '';

// If a date is selected, add the date filter condition
if (!empty($filterDate)) {
    $ComandoSQL .= " WHERE selected_date = :selected_date ORDER BY selected_hour;";
} else if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] === "admin") {
    // ORDER BY to sort by booking_id in descending order if the filter is not in use
    $ComandoSQL .= " ORDER BY booking_id DESC";
} else {
    // ORDER BY to sort by booking_id in descending order if the filter is not in use
    $ComandoSQL .= " ORDER BY selected_date DESC, selected_hour";
}


// SQL statement with the possible search condition
$stmt = $conn->prepare($ComandoSQL);

// If a date is selected, bind the parameter to the query
if (!empty($filterDate)) {
    $stmt->bindParam(':selected_date', $filterDate);
}

// Execute the query
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);




?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Maxwell William Ferreria">

    <title>Man With a Van</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>

</head>

<body>


    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">

            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Man With a Van</a>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">

                    <?php include_once './menu.php'; ?>

                </ul>
            </div>
        </div>
    </nav>

    <header id="myCarousel" class="carousel slide">

        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
        </ol>

        <div class="carousel-inner">
            <div class="item active">
                <div class="fill" style="background-image: url(images/manwithavan.jpg); background-size: contain; background-repeat:no-repeat;"></div>
            </div>
            <div class="item">
                <div class="fill" style="background-image: url(images/Van.jpeg); background-size:contain; background-repeat:no-repeat;"></div>
                <div class="carousel-caption">
                    <h3 style="background: #de542fff;">Good Vehicles</h3>
                </div>
            </div>
            <div class="item">
                <div class="fill" style="background-image: url(images/man_boxes.jpeg); background-size:contain; background-repeat:no-repeat;"></div>
                <div class="carousel-caption">
                    <h3 style="background: #de542fff;">Choose the service that suits you</h3>
                </div>
            </div>
        </div>

        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
            <span class="icon-prev"></span>
        </a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">
            <span class="icon-next"></span>
        </a>
    </header>

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="page-header">
                    Welcome to Man With a Van!
                </h2>
            </div>
        </div>

        <div class="row center-block">
            <div class="panel panel-default">
                <div class="panel-body">

                    <?php
                    if (!isset($_SESSION["usertype"]) && $_SESSION["usertype"] = "") {
                    } else if ($_SESSION["usertype"] != "") {; ?>
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3 style="color: black; margin:0%;">Booking List</h3><span class="glyphicon glyphicon-calendar"></span>
                            </div>
                        </div>

                        <div class="alert alert-info" style="color: #4d4d4d;">
                            <strong>Service Status Colors:</strong><br>
                            <span style="background: rgba(191, 191, 191, 0.7); font-weight: bold; margin-left: 5px;">Analyzing</span>
                            <span style="background: rgba(255, 218, 121, 0.7); font-weight: bold; margin-left: 5px;">Waiting for Customer Approval</span>
                            <span style="background: rgba(64, 128, 191, 0.7); font-weight: bold; margin-left: 5px;">Confirmed</span>
                            <span style="background: rgba(119, 197, 124, 0.7); font-weight: bold; margin-left: 5px;">Completed</span>
                            <span style="background: rgba(213, 84, 84, 0.7); font-weight: bold; margin-left: 5px;">Canceled</span>
                        </div>

                        <form action="" method="POST" class="form-inline" id="filterForm" style="margin-bottom: 10px;">
                            <label for="filterDate">Filter by Date:</label>
                            <input type="date" id="filterDate" style="margin-bottom: 3px;" name="filterDate" class="form-control" required>
                            <button type="button" id="filterButton" style="margin-bottom: 3px;" class="btn btn-sm btn-primary">Filter</button>
                            <button type="button" id="clearFilter" style="margin-bottom: 3px;" class="btn btn-sm btn-secondary">Clear Filter</button>
                        </form>

                    <?php
                    }
                    ?>


                    <?php
                    if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] == "") { // If is not logged shows logging div
                    ?>
                        <div class="row center-block">
                            <div class="form-style">
                                <div class="panel panel-primary col-xs-8 col-xs-offset-2 text-center">

                                    <form action="" method="POST">
                                        <h2 class="form-login-heading">Login for access our services and bookings!</h2>
                                        <input name="email" type="email" class="form-control" placeholder="Email" required autofocus value="<?php echo isset($_SESSION['failed_email']) ? $_SESSION['failed_email'] : ''; ?>">
                                        <input name="passw" type="password" class="form-control" placeholder="Password" required>
                                        </br>
                                        <button type="submit" class="btn btn-lg btn-primary btn-block" style="margin-bottom: 10px;">Login</button>
                                    </form>
                                    <a href="register.php">
                                        <button value="Register" class="btn btn-secondary" style="margin-bottom: 10px;">Register</button>
                                    </a>
                                </div>

                            </div>
                        </div>
                    <?php
                    }
                    ?>




                    <!-- Display the booking editing form if a booking is being edited -->
                    <?php if (isset($booking_to_edit)) : ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h2>Booking Nº <?php echo escapeHtml($booking_to_edit['booking_id']); ?></h2>
                                <form method="POST">
                                    <!-- Input fields for editing the booking -->
                                    <input type="hidden" name="booking_id" value="<?php echo escapeHtml($booking_to_edit['booking_id']); ?>">
                                    <input type="hidden" name="service_status" value="Pending">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>Customer Name:</label>
                                            <input type="text" class="form-control" name="firstname" value="<?php echo escapeHtml($booking_to_edit['firstname']); ?>" readonly>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Nationality:</label>
                                            <input type="text" class="form-control" name="nationality" value="<?php echo escapeHtml($booking_to_edit['nationality']); ?>" readonly>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Phone Number:</label>
                                            <input type="text" class="form-control" name="phone" value="<?php echo escapeHtml($booking_to_edit['phone']); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label>Service Type:</label>
                                            <input type="text" class="form-control" name="service_type" value="<?php echo escapeHtml($booking_to_edit['service_type']); ?>" readonly>
                                        </div>
                                        <div class="form-group col-md-9">
                                            <label>Home Moving Items (If applicable):</label>
                                            <input type="text" class="form-control" name="is_home_moving" value="<?php echo escapeHtml($booking_to_edit['is_home_moving']); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label>Other type of service (If applicable):</label>
                                            <textarea type="text" class="form-control" name="other_service_type" rows="5" style="resize: none;" readonly><?php echo escapeHtml($booking_to_edit['other_service_type']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-9">
                                            <label>Address From:</label>
                                            <input type="text" class="form-control" name="address_from" value="<?php echo escapeHtml($booking_to_edit['address_from']); ?>" readonly>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label>Eircode From:</label>
                                            <input type="text" class="form-control" name="eircode_from" value="<?php echo escapeHtml($booking_to_edit['eircode_from']); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-9">
                                            <label>Address To:</label>
                                            <input type="text" class="form-control" name="address_to" value="<?php echo escapeHtml($booking_to_edit['address_to']); ?>" readonly>
                                        </div>
                                        <div class="form-group col-md-2 ">
                                            <label>Eircode To:</label>
                                            <input type="text" class="form-control" name="eircode_to" value="<?php echo escapeHtml($booking_to_edit['eircode_to']); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label>Notes (If applicable):</label>
                                            <textarea type="text" class="form-control" name="notes" rows="5" style="resize: none;" readonly><?php echo escapeHtml($booking_to_edit['notes']); ?></textarea>
                                        </div>
                                    </div>


                                    <?php if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] != "" && $_SESSION["usertype"] != "admin") : // If is logged
                                    ?>

                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label>Price:</label>
                                                <input type="text" class="form-control" value="<?php echo escapeHtml($booking_to_edit['estimated_price']); ?>" readonly>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label>Driver:</label>
                                                <?php
                                                // select the driver first name based on the driver_id in booking_to_edit
                                                $driver_Id = $booking_to_edit['driver_id'];
                                                $driver_Query = "SELECT firstname FROM users WHERE user_id = :driver_id";
                                                $driver_Stmt = $conn->prepare($driver_Query);
                                                $driver_Stmt->bindParam(':driver_id', $driver_Id, PDO::PARAM_INT);
                                                $driver_Stmt->execute();
                                                $driver_Result = $driver_Stmt->fetch(PDO::FETCH_ASSOC);

                                                // Print the driver firstname if it exists, if doesnt exist print the driver_id directly
                                                echo "<input type='text' class='form-control' value='" . (isset($driver_Result['firstname']) ? escapeHtml($driver_Result['firstname']) : escapeHtml($driver_Id)) . "' readonly>";
                                                ?>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label>Vehicle:</label>
                                                <input type="text" class="form-control" value="<?php echo escapeHtml($booking_to_edit['make']) . " / " . escapeHtml($booking_to_edit['model']); ?>" readonly>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label>Need Helper:</label>
                                                <input type="text" class="form-control" value="<?php echo escapeHtml($booking_to_edit['helper']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label>Date:</label>
                                                <input type="text" class="form-control" style="text-align: center;" value="<?php echo escapeHtml(date('d-m-Y', strtotime($booking_to_edit['selected_date']))); ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Time:</label>
                                                <input type="text" class="form-control" style="text-align: center;" value="<?php echo escapeHtml($booking_to_edit['selected_hour']) . ' Hrs'; ?>" readonly>
                                            </div>
                                        </div>
                                        <a href="index.php" style="margin-bottom: 25px;" class="btn btn-lg btn-danger btn-block">Close Informations</a>
                                </form>
                            </div>
                        </div>


                    <?php endif; ?>


                    <?php if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] == "admin") : // If is logged as a Admin 
                    ?>
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label>Previous Price:</label>
                                <input type="text" class="form-control" value="<?php echo escapeHtml($booking_to_edit['estimated_price']); ?>" readonly>
                            </div>
                            <div class="form-group col-md-2">
                                <label>New Price:</label>
                                <input type="text" class="form-control" name="estimated_price" value="<?php echo (escapeHtml($booking_to_edit['estimated_price']) !== '0.00') ? escapeHtml($booking_to_edit['estimated_price']) : ''; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Select a Driver:</label>
                                <select class="form-control" name="driver_id" required>
                                    <option value="">Select Driver</option>
                                    <?php foreach ($drivers as $driver) : ?>
                                        <option value="<?php echo escapeHtml($driver['user_id']); ?>">
                                            <?php echo escapeHtml($driver['firstname'] . ' ' . $driver['lastname']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Select a Vehicle:</label>
                                <select class="form-control" name="vehicle_id" required>
                                    <option value="">Select Vehicle</option>
                                    <?php foreach ($vehicles_v as $vehicle_v) : ?>
                                        <option value="<?php echo escapeHtml($vehicle_v['vehicle_id']); ?>">
                                            <?php echo escapeHtml($vehicle_v['make'] . ' / ' . $vehicle_v['model']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-2">
                                <label>Date:</label>
                                <input type="text" class="form-control" style="text-align: center;" value="<?php echo escapeHtml(date('d-m-Y', strtotime($booking_to_edit['selected_date']))); ?>" readonly>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Time:</label>
                                <input type="text" class="form-control" style="text-align: center;" value="<?php echo escapeHtml($booking_to_edit['selected_hour']) . ' Hrs'; ?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4" style="text-align: center;">
                                <label for="release_another_booking">Do you want to release another booking for this date/time?</label></br>
                                <label>Yes: <input name="release_another_booking" type="radio" id="release_another_booking" value="Yes" required> / </label>
                                <label>No: <input name="release_another_booking" type="radio" id="release_another_booking" value="No" required></label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-lg btn-primary btn-block">Save Analyze</button>
                        <a href="index.php" class="btn btn-lg btn-danger btn-block">Cancel</a>
                        </form>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] == "admin") { // If is logged as a Admin
    ?>

        <div class="table-responsive" id="bookingTable">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th></th>
                        <th>Service Status</th>
                        <th>Customer Name</th>
                        <th>Service Type</th>
                        <th>Address From</th>
                        <th>Address To</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Need Help</th>
                        <th>Price</th>
                        <th>Driver</th>
                        <th>Vehicle</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    // Loop through each booking and display its data
                    foreach ($result as $row) {
                        echo "<tr";
                        if (escapeHtml($row['service_status']) === 'Analyzing') { // This if makes the row color to grey where service_status equal Analyzing
                            echo " style='background: rgba(191, 191, 191, 0.7);'";
                        }
                        if (escapeHtml($row['service_status']) === 'Pending') { // This if makes the row color to green where service_status equal Pending
                            echo " style='background: rgba(255, 218, 121, 0.7);'";
                        }
                        if (escapeHtml($row['service_status']) === 'Confirmed') { // This if makes the row color to blue where service_status equal Confirmed
                            echo " style='background: rgba(64, 128, 191, 0.7);'";
                        }
                        if (escapeHtml($row['service_status']) === 'Completed') { // This if makes the row color to green where service_status equal Completed
                            echo " style='background: rgba(119, 197, 124, 0.7);'";
                        }
                        if (escapeHtml($row['service_status']) === 'Canceled') { // This if makes the row color to red where service_status equal Canceled
                            echo " style='background: rgba(213, 84, 84, 0.7);'";
                        }
                        echo ">";


                        // Button to analyze booking
                        echo "<td style='background-color: rgba(204, 204, 204);'>";
                        if (escapeHtml($row['service_status']) == 'Analyzing') { // Hide the button if the status has been analyzed
                            // button opens the editing form for the corresponding booking
                            echo "<div style='display: inline-block;'>";
                            echo "<form method='POST' style='display: inline;'>";
                            echo "<input type='hidden' name='edit_booking_id' value='" . $row['booking_id'] . "'>";
                            echo "<button type='submit' class='btn btn-xs btn-primary btn-block'>Analyze</button>";
                            echo "</form>";
                            echo "</div>";
                        }



                        // Button to change book status to 'Confirmed'
                        if (escapeHtml($row['service_status']) == 'Pending' && $row['user_id'] == $_SESSION["user_id"]) {
                            echo "<div style='display: inline-block; margin-bottom: 1px;'>";
                            echo "<form method='POST' style='display: inline;'>";
                            echo "<input type='hidden' name='confirm_booking_form' value='" . $row['booking_id'] . "'>";
                            echo "<button type='submit' name='confirm_booking_request' class='btn btn-xs btn-primary btn-block'>Confirm Booking</button>";
                            echo "</form>";
                            echo "</div>";
                        }
                        // Check the booking confirmation before submition on specific booking
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking_request']) && $_POST['confirm_booking_form'] == $row['booking_id']) {
                            echo '<div class="alert alert-warning">';
                            echo '<strong>Confirm this booking?</strong><br>';
                            echo '<form method="POST" style="display: inline;">';
                            echo '<input type="hidden" name="confirm_booking_id" value="' . $row['booking_id'] . '">';
                            echo '<button type="submit" class="btn btn-xs btn-success">Yes</button>';
                            echo '</form>';
                            echo '<form method="POST" style="display: inline;">';
                            echo '<button type="submit" class="btn btn-xs btn-danger">No</button>';
                            echo '</form>';
                            echo '</div>';
                        }



                        // Button to change book status to 'Canceled'
                        if (escapeHtml($row['service_status']) == 'Pending' && $row['user_id'] == $_SESSION["user_id"]) {
                            echo "<div style='display: inline-block;'>";
                            echo "<form method='POST' style='display: inline;'>";
                            echo "<input type='hidden' name='cancel_booking_form' value='" . $row['booking_id'] . "'>";
                            echo "<button type='submit' name='cancel_booking_request' class='btn btn-xs btn-danger btn-block'>Cancel</button>";
                            echo "</form>";
                            echo "</div>";
                        }

                        // Check the booking confirmation before submition on specific booking
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_request']) && $_POST['cancel_booking_form'] == $row['booking_id']) {
                            echo '<div class="alert alert-warning">';
                            echo '<strong>Cancel this booking?</strong><br>';
                            echo '<form method="POST" style="display: inline;">';
                            echo '<input type="hidden" name="cancel_booking_id" value="' . $row['booking_id'] . '">';
                            echo '<button type="submit" class="btn btn-xs btn-success">Yes</button>';
                            echo '</form>';
                            echo '<form method="POST" style="display: inline;">';
                            echo '<button type="submit" class="btn btn-xs btn-danger">No</button>';
                            echo '</form>';
                            echo '</div>';
                        }
                        echo "</td>";

                        echo "<td>";
                        echo "<form method='POST' style='display: inline;'>";
                        echo "<input type='hidden' name='edit_booking_id' value='" . $row['booking_id'] . "'>";
                        echo "<button type='submit' class='btn btn-xs btn-primary btn-block'>Details</button>";
                        echo "</form>";
                        echo "</td>";

                        echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['service_status']) . "</td>";
                        echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['firstname']) . "</td>";
                        echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['service_type']) . "</td>";
                        echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['address_from']) . ", " . escapeHtml($row['eircode_from']) . "</td>";
                        echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['address_to']) . ", " . escapeHtml($row['eircode_to']) . "</td>";
                        echo "<td style='white-space: nowrap; color: #4d4d4d; font-weight: bold;'>" . date('d/m/Y', strtotime($row['selected_date'])) . "</td>"; //changes the date formatting
                        echo "<td style='white-space: nowrap; color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['selected_hour']) . " Hrs</td>"; //the style is to not allow break lines
                        echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['helper']) . "</td>";
                        echo "<td style='background-color: #4080bf; color: white; font-weight: bold;'>€" . escapeHtml($row['estimated_price']) . "</td>";

                        // Get the driver_id from the row
                        $driverId = $row['driver_id'];

                        // Get firstname from users table where user_id is equal to driver_id
                        $driverQuery = "SELECT firstname FROM users WHERE user_id = :driver_id";
                        $driverStmt = $conn->prepare($driverQuery);
                        $driverStmt->bindParam(':driver_id', $driverId, PDO::PARAM_INT);
                        $driverStmt->execute();
                        $driverResult = $driverStmt->fetch(PDO::FETCH_ASSOC);

                        // Print the driver firstname if it exists, if doesnt exist print the driver_id directly
                        if ($driverResult && isset($driverResult['firstname'])) {
                            echo "<td style='background-color: #4080bf; color: white;'>" . escapeHtml($driverResult['firstname']) . "</td>";
                        } else {
                            echo "<td style='background-color: #4080bf; color: white;'>" . escapeHtml($driverId) . "</td>";
                        }

                        echo "<td style='background-color: #4080bf; color: white;'>" . escapeHtml($row['make']) . " " . escapeHtml($row['model']) . "</td>";

                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    <?php
    } else if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] == "customer") { // If is logged as a Costumer
    ?>

        <div class="table-responsive" id="bookingTable">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th></th>
                        <th>Service Status</th>
                        <th>Service Type</th>
                        <th>Address From</th>
                        <th>Address To</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Need Help</th>
                        <th>Driver</th>
                        <th>Price</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    // Loop through each booking and display its data
                    foreach ($result as $row) {
                        // Check if the user_id of the booking is the same of the current user's session user_id
                        if ($row['user_id'] == $_SESSION["user_id"]) {
                            echo "<tr";
                            if (escapeHtml($row['service_status']) === 'Analyzing') { // This if makes the row color to grey where service_status equal Analyzing
                                echo " style='background: rgba(191, 191, 191, 0.7);'";
                            }
                            if (escapeHtml($row['service_status']) === 'Pending') { // This if makes the row color to green where service_status equal Pending
                                echo " style='background: rgba(255, 218, 121, 0.7);'";
                            }
                            if (escapeHtml($row['service_status']) === 'Confirmed') { // This if makes the row color to blue where service_status equal Confirmed
                                echo " style='background: rgba(64, 128, 191, 0.7);'";
                            }
                            if (escapeHtml($row['service_status']) === 'Completed') { // This if makes the row color to green where service_status equal Completed
                                echo " style='background: rgba(119, 197, 124, 0.7);'";
                            }
                            if (escapeHtml($row['service_status']) === 'Canceled') { // This if makes the row color to red where service_status equal Canceled
                                echo " style='background: rgba(213, 84, 84, 0.7);'";
                            }
                            echo ">";

                            // Button to change book status to 'Confirmed'
                            echo "<td style='background-color: rgba(204, 204, 204);'>";
                            if (escapeHtml($row['service_status']) == 'Pending') {
                                echo "<div style='display: inline-block; margin-bottom: 5px;'>";
                                echo "<form method='POST' style='display: inline;'>";
                                echo "<input type='hidden' name='confirm_booking_form' value='" . $row['booking_id'] . "'>";
                                echo "<button type='submit' name='confirm_booking_request' class='btn btn-xs btn-primary btn-block'>Confirm Booking</button>";
                                echo "</form>";
                                echo "</div>";
                            }

                            // Check the booking confirmation before submition on specific booking
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking_request']) && $_POST['confirm_booking_form'] == $row['booking_id']) {
                                echo '<div class="alert alert-warning">';
                                echo '<strong>Confirm this booking?</strong><br>';
                                echo '<form method="POST" style="display: inline;">';
                                echo '<input type="hidden" name="confirm_booking_id" value="' . $row['booking_id'] . '">';
                                echo '<button type="submit" class="btn btn-xs btn-success">Yes</button>';
                                echo '</form>';
                                echo '<form method="POST" style="display: inline;">';
                                echo '<button type="submit" class="btn btn-xs btn-danger">No</button>';
                                echo '</form>';
                                echo '</div>';
                            }



                            // Button to change book status to 'Canceled'
                            if (escapeHtml($row['service_status']) == 'Pending' && $row['user_id'] == $_SESSION["user_id"]) {
                                echo "<div style='display: inline-block; margin-bottom: 5px;'>";
                                echo "<form method='POST' style='display: inline;'>";
                                echo "<input type='hidden' name='cancel_booking_form' value='" . $row['booking_id'] . "'>";
                                echo "<button type='submit' name='cancel_booking_request' class='btn btn-xs btn-danger btn-block'>Cancel</button>";
                                echo "</form>";
                                echo "</div>";
                            }

                            // Check the booking confirmation before submition on specific booking
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_request']) && $_POST['cancel_booking_form'] == $row['booking_id']) {
                                echo '<div class="alert alert-warning">';
                                echo '<strong>Cancel this booking?</strong><br>';
                                echo '<form method="POST" style="display: inline;">';
                                echo '<input type="hidden" name="cancel_booking_id" value="' . $row['booking_id'] . '">';
                                echo '<button type="submit" class="btn btn-xs btn-success">Yes</button>';
                                echo '</form>';
                                echo '<form method="POST" style="display: inline;">';
                                echo '<button type="submit" class="btn btn-xs btn-danger">No</button>';
                                echo '</form>';
                                echo '</div>';
                            }
                            echo "</td>";

                            echo "<td>";
                            echo "<form method='POST' style='display: inline;'>";
                            echo "<input type='hidden' name='edit_booking_id' value='" . $row['booking_id'] . "'>";
                            echo "<button type='submit' class='btn btn-xs btn-primary btn-block'>Details</button>";
                            echo "</form>";
                            echo "</td>";

                            echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['service_status']) . "</td>";
                            echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['service_type']) . "</td>";
                            echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['address_from']) . ", " . escapeHtml($row['eircode_from']) . "</td>";
                            echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['address_to']) . ", " . escapeHtml($row['eircode_to']) . "</td>";
                            echo "<td style='white-space: nowrap; color: #4d4d4d; font-weight: bold;'>" . date('d/m/Y', strtotime($row['selected_date'])) . "</td>"; //changes the date formatting
                            echo "<td style='white-space: nowrap; color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['selected_hour']) . " Hrs</td>"; //the style is to not allow break lines
                            echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['helper']) . "</td>";

                            // Get the driver_id from the row
                            $driverId = $row['driver_id'];

                            // Get firstname from users table where user_id is equal to driver_id
                            $driverQuery = "SELECT firstname FROM users WHERE user_id = :driver_id";
                            $driverStmt = $conn->prepare($driverQuery);
                            $driverStmt->bindParam(':driver_id', $driverId, PDO::PARAM_INT);
                            $driverStmt->execute();
                            $driverResult = $driverStmt->fetch(PDO::FETCH_ASSOC);

                            // Print the driver firstname if it exists, if doesnt exist print the driver_id directly
                            if ($driverResult && isset($driverResult['firstname'])) {
                                echo "<td style='background-color: #4080bf; color: white;'>" . escapeHtml($driverResult['firstname']) . "</td>";
                            } else {
                                echo "<td style='background-color: #4080bf; color: white;'>" . escapeHtml($driverId) . "</td>";
                            }

                            echo "<td style='background-color: #4080bf; color: white;'>€" . escapeHtml($row['estimated_price']) . "</td>";
                            echo "</tr>";
                        }
                    }

                    ?>
                </tbody>
            </table>
        </div>
    <?php
    } else if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] == "driver") { // If is logged as a Driver
    ?>

        <div class="table-responsive" id="bookingTable">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th></th>
                        <th>Customer Name</th>
                        <th>Phone Number</th>
                        <th>Service Type</th>
                        <th>Address From</th>
                        <th>Address To</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Need Help</th>
                        <th>Price</th>
                        <th>Vehicle</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    // Loop through each booking and display its data
                    foreach ($result as $row) {
                        // Check if the user_id of the booking is the same of the current user's session user_id
                        if ($row['driver_id'] == $_SESSION["user_id"] || $row['user_id'] == $_SESSION["user_id"]) {
                            echo "<tr";
                            if (escapeHtml($row['service_status']) === 'Analyzing') { // This if makes the row color to grey where service_status equal Analyzing
                                echo " style='background: rgba(191, 191, 191, 0.7);'";
                            }
                            if (escapeHtml($row['service_status']) === 'Pending') { // This if makes the row color to green where service_status equal Pending
                                echo " style='background: rgba(255, 218, 121, 0.7);'";
                            }
                            if (escapeHtml($row['service_status']) === 'Confirmed') { // This if makes the row color to blue where service_status equal Confirmed
                                echo " style='background: rgba(64, 128, 191, 0.7);'";
                            }
                            if (escapeHtml($row['service_status']) === 'Completed') { // This if makes the row color to green where service_status equal Completed
                                echo " style='background: rgba(119, 197, 124, 0.7);'";
                            }
                            if (escapeHtml($row['service_status']) === 'Canceled') { // This if makes the row color to red where service_status equal Canceled
                                echo " style='background: rgba(213, 84, 84, 0.7);'";
                            }
                            echo ">";

                            echo "<td style='background-color: rgba(204, 204, 204);'>";

                            // Button to change book status to 'Confirmed'
                            if (escapeHtml($row['service_status']) == 'Pending' && $row['user_id'] == $_SESSION["user_id"]) {
                                echo "<div style='display: inline-block; margin-bottom: 5px;'>";
                                echo "<form method='POST' style='display: inline;'>";
                                echo "<input type='hidden' name='confirm_booking_form' value='" . $row['booking_id'] . "'>";
                                echo "<button type='submit' name='confirm_booking_request' class='btn btn-xs btn-primary btn-block'>Confirm Booking</button>";
                                echo "</form>";
                                echo "</div>";
                            }
                            // Check the booking confirmation before submition on specific booking
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking_request']) && $_POST['confirm_booking_form'] == $row['booking_id']) {
                                echo '<div class="alert alert-warning">';
                                echo '<strong>Confirm this booking?</strong><br>';
                                echo '<form method="POST" style="display: inline;">';
                                echo '<input type="hidden" name="confirm_booking_id" value="' . $row['booking_id'] . '">';
                                echo '<button type="submit" class="btn btn-xs btn-success">Yes</button>';
                                echo '</form>';
                                echo '<form method="POST" style="display: inline;">';
                                echo '<button type="submit" class="btn btn-xs btn-danger">No</button>';
                                echo '</form>';
                                echo '</div>';
                            }


                            // Button to change book status to 'Completed'
                            if (escapeHtml($row['service_status']) == 'Confirmed') {
                                echo "<div style='display: inline-block; margin-bottom: 5px;'>";
                                echo "<form method='POST' style='display: inline;'>";
                                echo "<input type='hidden' name='complete_booking_form' value='" . $row['booking_id'] . "'>";
                                echo "<button type='submit' name='complete_booking_request' class='btn btn-xs btn-success btn-block'>Finish</button>";
                                echo "</form>";
                                echo "</div>";
                            }
                            // Check the booking confirmation before submition on specific booking
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_booking_request']) && $_POST['complete_booking_form'] == $row['booking_id']) {
                                echo '<div class="alert alert-warning">';
                                echo '<strong>Finish this booking?</strong><br>';
                                echo '<form method="POST" style="display: inline;">';
                                echo '<input type="hidden" name="complete_booking_id" value="' . $row['booking_id'] . '">';
                                echo '<button type="submit" class="btn btn-xs btn-success">Yes</button>';
                                echo '</form>';
                                echo '<form method="POST" style="display: inline;">';
                                echo '<button type="submit" class="btn btn-xs btn-danger">No</button>';
                                echo '</form>';
                                echo '</div>';
                            }


                            // Button to change book status to 'Canceled'
                            if (escapeHtml($row['service_status']) == 'Pending' && $row['user_id'] == $_SESSION["user_id"]) {
                                // button opens the cancel form for the corresponding booking
                                echo "<div style='display: inline-block; margin-bottom: 5px;'>";
                                echo "<form method='POST' style='display: inline;'>";
                                echo "<input type='hidden' name='cancel_booking_form' value='" . $row['booking_id'] . "'>";
                                echo "<button type='submit' name='cancel_booking_request' class='btn btn-xs btn-danger btn-block'>Cancel</button>";
                                echo "</form>";
                                echo "</div>";
                            }
                            // Check the booking confirmation before submition on specific booking
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_request']) && $_POST['cancel_booking_form'] == $row['booking_id']) {
                                echo '<div class="alert alert-warning">';
                                echo '<strong>Cancel this booking?</strong><br>';
                                echo '<form method="POST" style="display: inline;">';
                                echo '<input type="hidden" name="cancel_booking_id" value="' . $row['booking_id'] . '">';
                                echo '<button type="submit" class="btn btn-xs btn-success">Yes</button>';
                                echo '</form>';
                                echo '<form method="POST" style="display: inline;">';
                                echo '<button type="submit" class="btn btn-xs btn-danger">No</button>';
                                echo '</form>';
                                echo '</div>';
                            }
                            echo "</td>";

                            echo "<td>";
                            echo "<form method='POST' style='display: inline;'>";
                            echo "<input type='hidden' name='edit_booking_id' value='" . $row['booking_id'] . "'>";
                            echo "<button type='submit' class='btn btn-xs btn-primary btn-block'>Details</button>";
                            echo "</form>";
                            echo "</td>";

                            echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['firstname']) . "</td>";
                            echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['phone']) . "</td>";
                            echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['service_type']) . "</td>";
                            echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['address_from']) . ", " . escapeHtml($row['eircode_from']) . "</td>";
                            echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['address_to']) . ", " . escapeHtml($row['eircode_to']) . "</td>";
                            echo "<td style='white-space: nowrap; color: #4d4d4d; font-weight: bold;'>" . date('d/m/Y', strtotime($row['selected_date'])) . "</td>"; //changes the date formatting
                            echo "<td style='white-space: nowrap; color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['selected_hour']) . " Hrs</td>"; //the style is to not allow break lines
                            echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['helper']) . "</td>";
                            echo "<td style='background-color: #4080bf; color: white;'>€" . escapeHtml($row['estimated_price']) . "</td>";
                            echo "<td style='background-color: #4080bf; color: white;'>" . escapeHtml($row['make']) . " " . escapeHtml($row['model']) . "</td>";
                            echo "</tr>";
                        }
                    }

                    ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
        </div>
    </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class="glyphicon glyphicon-road"></i> Book a Service</h4>
                </div>
                <div class="panel-body text-center">

                        <p>We offer different types of cargo transport services such as, Delivery and Collection, Home Moving, Furniture Removal, Motorcycle Transportation, etc.</p>
                        <p>Book your service with us!</p>

                    <a href="booking.php" class="btn btn-lg btn-primary btn-block">Book a Service</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class="glyphicon glyphicon-earphone"></i> Need a Help? Contact Us!</h4>
                </div>
                <div class="panel-body text-center">

                        <p>Any question? Feel free to contact us. Our service is available from Monday to Friday from 7:00am to 6:00pm.</p>
                        <p>+353 (83) 897 7534</p>

                    <a href="tel:+3530838977534" class="btn btn-lg btn-primary btn-block">Call Us</a>
                </div>
            </div>
        </div>

    </div>


    <div class="row">
        <div class="col-md-6 col-sm-6">
            <img class="img-thumbnail img-responsive img-portfolio img-hover" src="images/Van_3.jpg" alt="">
        </div>

        <div class="col-md-6 col-sm-6">
            <img class="img-thumbnail img-responsive img-portfolio img-hover" src="images/Van_4.jpg" alt="">
        </div>
    </div>

    <footer>
        <div class="row">
            <div class="col-lg-12 text-center">             
                    <p>Copyright &copy;Max William 2023</p>
            </div>
        </div>
    </footer>

    </div>

    <!-- Script to timing the Slides -->
    <script>
        $('.carousel').carousel({
            interval: 5000 //Timing of the slides
        });
    </script>

    <script>
        let refreshInterval; // store the auto-refresh interval ID

        $(document).ready(function() {
            function refreshTable() {
                $('#bookingTable').load(location.href + ' #bookingTable');
            }

            function startAutoRefresh() {
                refreshInterval = setInterval(refreshTable, 20000); // Start the auto-refresh
            }

            // Call the startAutoRefresh
            startAutoRefresh();

            // Reload the page or restart auto-refresh when Clear Filter is clicked
            $('#clearFilter').on('click', function() {
                if ($('#filterDate').val() === '') {
                    startAutoRefresh(); // Restart the auto-refresh
                } else {
                    location.reload(); // Reload the page to clear the filter
                }
            });

            // when the filter button is clicked
            $('#filterButton').on('click', function() {
                clearInterval(refreshInterval); // Stop the auto-refresh
                const filterDate = $('#filterDate').val();

                // request to filter the data
                $.ajax({
                    type: 'POST',
                    url: '',
                    data: {
                        filterDate: filterDate
                    },
                    success: function(response) {
                        // Update the bookingTable with the filtered results
                        $('#bookingTable').html($(response).find('#bookingTable').html());
                    },
                    error: function() {
                        console.log('Error occurred while filtering data.');
                    }
                });
            });

        });
    </script>

</body>

</html>