<?php
// Start and handle the $_Session
require_once 'functions/session_protection.php';
// Check admin access
checkAdminAccess();

// database connection
require 'functions/connection.php';

header("Content-Type: text/html; charset=UTF-8", true);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Function to escape HTML entities for output
function escapeHtml($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Check if the form is submitted to update the booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $service_status = protection($_POST['service_status']);
    $service_type = protection($_POST['service_type']);
    $is_home_moving = ucfirst(protection($_POST['is_home_moving'])); //ucfirst is to force the input values start with a capital letter
    $other_service_type = ucfirst(protection($_POST['other_service_type']));
    $address_from = strtoupper(protection($_POST['address_from'])); //strtoupper is to force the input values to be capital letter
    $eircode_from = strtoupper(protection($_POST['eircode_from']));
    $address_to = strtoupper(protection($_POST['address_to']));
    $eircode_to = strtoupper(protection($_POST['eircode_to']));
    $selected_date = protection($_POST['selected_date']);
    $selected_hour = protection($_POST['selected_hour']);
    $notes = ucfirst(protection($_POST['notes'])); //ucfirst is to force the input values start with a capital letter
    $helper = protection($_POST['helper']);
    $release_another_booking = protection($_POST['release_another_booking']);
    $estimated_price = protection($_POST['estimated_price']);
    $driver_id = protection($_POST['driver_id']);
    $vehicle_id = protection($_POST['vehicle_id']);

    // Prepare the UPDATE query
    $stmt = $conn->prepare('UPDATE bookings SET service_status = :service_status, service_type = :service_type, other_service_type = :other_service_type, address_from = :address_from, eircode_from = :eircode_from, address_to = :address_to, eircode_to = :eircode_to, selected_date = :selected_date, selected_hour = :selected_hour, notes = :notes, helper = :helper, release_another_booking = :release_another_booking, estimated_price = :estimated_price, driver_id = :driver_id, is_home_moving = :is_home_moving, vehicle_id = :vehicle_id WHERE booking_id = :booking_id');
    $stmt->bindValue(':booking_id', $booking_id);
    $stmt->bindValue(':service_status', $service_status);
    $stmt->bindValue(':service_type', $service_type);
    $stmt->bindValue(':is_home_moving', $is_home_moving);
    $stmt->bindValue(':other_service_type', $other_service_type);
    $stmt->bindValue(':address_from', $address_from);
    $stmt->bindValue(':eircode_from', $eircode_from);
    $stmt->bindValue(':address_to', $address_to);
    $stmt->bindValue(':eircode_to', $eircode_to);
    $stmt->bindValue(':selected_date', $selected_date);
    $stmt->bindValue(':selected_hour', $selected_hour);
    $stmt->bindValue(':notes', $notes);
    $stmt->bindValue(':helper', $helper);
    $stmt->bindValue(':release_another_booking', $release_another_booking);
    $stmt->bindValue(':estimated_price', $estimated_price);
    $stmt->bindValue(':driver_id', $driver_id);
    $stmt->bindValue(':vehicle_id', $vehicle_id);

    // Execute the update query
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->rowCount() > 0) {
        echo "Booking updated successfully.";
    }
}

// Check if a booking is being deleted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking_id'])) {
    $delete_booking_id = $_POST['delete_booking_id'];
    // Delete the booking from the database
    $stmt = $conn->prepare('DELETE FROM bookings WHERE booking_id = :booking_id');
    $stmt->bindValue(':booking_id', $delete_booking_id);
    $stmt->execute();
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

    // Fetch data for drivers
    $driver_query = "SELECT * FROM users WHERE usertype = 'driver'";
    $stmt_drivers = $conn->prepare($driver_query);
    $stmt_drivers->execute();
    $drivers = $stmt_drivers->fetchAll(PDO::FETCH_ASSOC);

    // Fetch data for drivers
    $driver_query = "SELECT * FROM vehicles WHERE vehicle_id = vehicle_id";
    $stmt_vehicles = $conn->prepare($driver_query);
    $stmt_vehicles->execute();
    $vehicles_v = $stmt_vehicles->fetchAll(PDO::FETCH_ASSOC);
}


// Build the SQL query based on the search query
$ComandoSQL = "SELECT bookings.*, users.firstname, users.phone, users.nationality, vehicles.make, vehicles.model FROM bookings LEFT JOIN users ON bookings.user_id = users.user_id LEFT JOIN vehicles ON bookings.vehicle_id = vehicles.vehicle_id";


// Fetch search query, if any
$searchName = isset($_GET['search_name']) ? $_GET['search_name'] : '';
if (!empty($searchName)) {
    // If a search query is provided, join with users table and filter by user name
    $ComandoSQL .= " INNER JOIN users AS users_search ON bookings.user_id = users_search.user_id WHERE users_search.firstname LIKE :search_name ORDER BY booking_id DESC";
    // Prepare the SQL statement with the possible search condition
    $stmt = $conn->prepare($ComandoSQL);
    // Bind the search parameter, if applicable
    $searchPattern = '%' . $searchName . '%';
    $stmt->bindValue(':search_name', $searchPattern);
} else {
    // Prepare the SQL statement without the search condition
    $ComandoSQL .= " ORDER BY booking_id DESC";

    $stmt = $conn->prepare($ComandoSQL);
}

// Execute the query
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST['check_available_hours']) && $_POST['check_available_hours'] === "true") {
    try {

        $selected_date = protection($_POST['selected_date']);

        // Execute the SQL query to check if the selected hour is not available
        $sql = "SELECT selected_hour FROM bookings WHERE selected_date = :selected_date AND release_another_booking = 'no'";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':selected_date', $selected_date);
        $stmt->execute();
        $unavailableHours = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo json_encode($unavailableHours);
        exit;
    } catch (PDOException $e) {
        // if an exception occurs, return an empty response
        echo json_encode([]);
        exit;
    }
}


function build_calendar($selectedDate)
{
    // Set the timezone
    date_default_timezone_set('Your/Timezone');

    // Get the current date
    $currentDate = new DateTime();

    // Set the start date as one day after the current date
    $startDate = clone $currentDate;
    $startDate->modify('+1 day');

    // Set the end date as one month from the start date
    $endDate = clone $startDate;
    $endDate->modify('+1 month');

    // Create an empty array to store the weekday dates
    $weekdays = array();

    // Iterate over each day within the date range
    $interval = new DateInterval('P1D');
    $dateRange = new DatePeriod($startDate, $interval, $endDate);
    foreach ($dateRange as $date) {
        // Check if the current date is a weekday (Monday to Friday)
        $dayOfWeek = $date->format('N');
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
            $formattedDate = $date->format('Y-m-d');
            $optionText = $date->format('l, F j, Y');
            $selectedAttribute = ($formattedDate === $selectedDate) ? 'selected' : '';
            $weekdays[] = "<option value=\"$formattedDate\" $selectedAttribute>$optionText</option>";
        }
    }

    // Create the HTML select element with an id attribute
    $select = '<select name="selected_date" id="selected_date" class="form-control">';
    $select .= '<option value="" disabled>Select a weekday</option>';
    $select .= implode('', $weekdays);
    $select .= '</select>';

    // Return the HTML select element
    return $select;
}


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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



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
        <!-- Indicators -->
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
        <div class="row center-block">
            <div class="panel panel-default">

                <div class="panel-body">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 style="color: black; margin:0%;">Edit Booking</h3><span class="glyphicon glyphicon-calendar"></span>
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

                    <div class="panel-heading">
                        <form method="GET" class="form-inline">
                            <div class="form-group">
                                <label for="search_name">Search by Name:</label>
                                <input type="text" class="form-control" style="margin-bottom: 3px;" id="search_name" name="search_name" placeholder="Enter name">

                                <button type="submit" style="margin-bottom: 3px;" class="btn btn-primary">Search</button>
                                <a href="manage_bookings.php" style="margin-bottom: 3px;" class="btn btn-secondary">Clear Filter</a>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Actions</th>
                                    <th>Booking Nº</th>
                                    <th>Customer Name</th>
                                    <th>Phone Number</th>
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

                                <!-- Display the booking editing form if a booking is being edited -->
                                <?php if (isset($booking_to_edit)) : ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h2>Editing Booking Nº <?php echo escapeHtml($booking_to_edit['booking_id']); ?></h2>
                                            <form method="POST">
                                                <!-- Input fields for editing the booking -->
                                                <input type="hidden" name="booking_id" value="<?php echo escapeHtml($booking_to_edit['booking_id']); ?>">
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
                                                        <label>Service Status:</label>
                                                        <select class="form-control" name="service_status">
                                                            <option value="Analyzing" <?php echo ($booking_to_edit['service_status'] === 'Analyzing') ? 'selected' : ''; ?>>Analyzing</option>
                                                            <option value="Pending" <?php echo ($booking_to_edit['service_status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                            <option value="Confirmed" <?php echo ($booking_to_edit['service_status'] === 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                                            <option value="Completed" <?php echo ($booking_to_edit['service_status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                            <option value="Canceled" <?php echo ($booking_to_edit['service_status'] === 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-5">
                                                        <label>Service Type:</label>
                                                        <input type="text" class="form-control" name="service_type" value="<?php echo escapeHtml($booking_to_edit['service_type']); ?>">
                                                    </div>
                                                    <div class="form-group col-md-7">
                                                        <label>Home Moving Items (If applicable):</label>
                                                        <input type="text" class="form-control" name="is_home_moving" value="<?php echo escapeHtml($booking_to_edit['is_home_moving']); ?>">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label>Other type of service (If applicable):</label>
                                                        <textarea type="text" class="form-control" name="other_service_type" rows="5" style="resize: none;"><?php echo escapeHtml($booking_to_edit['other_service_type']); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-9">
                                                        <label>Address From:</label>
                                                        <input type="text" class="form-control" name="address_from" value="<?php echo escapeHtml($booking_to_edit['address_from']); ?>">
                                                    </div>
                                                    <div class="form-group col-md-2">
                                                        <label>Eircode From:</label>
                                                        <input type="text" class="form-control" name="eircode_from" value="<?php echo escapeHtml($booking_to_edit['eircode_from']); ?>">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-9">
                                                        <label>Address To:</label>
                                                        <input type="text" class="form-control" name="address_to" value="<?php echo escapeHtml($booking_to_edit['address_to']); ?>">
                                                    </div>
                                                    <div class="form-group col-md-2">
                                                        <label>Eircode To:</label>
                                                        <input type="text" class="form-control" name="eircode_to" value="<?php echo escapeHtml($booking_to_edit['eircode_to']); ?>">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label>Notes (If applicable):</label>
                                                        <textarea type="text" class="form-control" name="notes" rows="5" style="resize: none;"><?php echo escapeHtml($booking_to_edit['notes']); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-4">
                                                        <label>Select a Driver:</label>
                                                        <select class="form-control" name="driver_id" required>
                                                            <option value="">Select Driver</option>
                                                            <?php foreach ($drivers as $driver) : ?>
                                                                <option value="<?php echo escapeHtml($driver['user_id']); ?>" <?php echo ($driver['user_id'] === $booking_to_edit['driver_id']) ? 'selected' : ''; ?>>
                                                                    <?php echo escapeHtml($driver['firstname'] . ' ' . $driver['lastname']); ?>
                                                                    <span><?php echo ' (' . $driver['user_status'] . ')'; ?></span>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label>Select a Vehicle:</label>
                                                        <select class="form-control" name="vehicle_id" required>
                                                            <option value="">Select Vehicle</option>
                                                            <?php foreach ($vehicles_v as $vehicle_v) : ?>
                                                                <option value="<?php echo escapeHtml($vehicle_v['vehicle_id']); ?>" <?php echo ($vehicle_v['vehicle_id'] === $booking_to_edit['vehicle_id']) ? 'selected' : ''; ?>>
                                                                    <?php echo escapeHtml($vehicle_v['make'] . ' / ' . $vehicle_v['model']); ?>
                                                                    <span><?php echo ' (' . $vehicle_v['vehicle_status'] . ')'; ?></span>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-md-2">
                                                        <label>Previous Time:</label>
                                                        <input type="text" class="form-control" style="text-align: center;" value="<?php echo escapeHtml($booking_to_edit['selected_hour']) . ' Hrs'; ?>" readonly>
                                                    </div>
                                                    <div class="form-group col-md-2">
                                                        <label>Previous Date:</label>
                                                        <input type="text" class="form-control" style="text-align: center;" value="<?php echo escapeHtml(date('d-m-Y', strtotime($booking_to_edit['selected_date']))); ?>" readonly>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>New Date</label>
                                                        <?php
                                                        $selectedDate = (!empty($booking_to_edit['selected_date'])) ? $booking_to_edit['selected_date'] : null;
                                                        echo build_calendar($selectedDate);
                                                        ?>
                                                    </div>
                                                </div>

                                                <!-- Hidden input to store the value of the disabled radio -->
                                                <input type="hidden" id="hidden_selected_hour" name="selected_hour">

                                                <div class="row">
                                                    <div class="form-group col-md-3">
                                                        <div id="hoursContainer" class="col-md-12">
                                                            <label>Select a New Time</label>
                                                            <div id="hoursRadio">
                                                                <input type="radio" id="hour_0" name="selected_hour" value="07:00 - 09:00">
                                                                <label for="hour_0">07:00 - 09:00</label><br>

                                                                <input type="radio" id="hour_1" name="selected_hour" value="09:00 - 11:00">
                                                                <label for="hour_1">09:00 - 11:00</label><br>

                                                                <input type="radio" id="hour_2" name="selected_hour" value="11:00 - 13:00">
                                                                <label for="hour_2">11:00 - 13:00</label><br>

                                                                <input type="radio" id="hour_3" name="selected_hour" value="14:00 - 16:00">
                                                                <label for="hour_3">14:00 - 16:00</label><br>

                                                                <input type="radio" id="hour_4" name="selected_hour" value="16:00 - 18:00">
                                                                <label for="hour_4">16:00 - 18:00</label><br>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>



                                                <div class="row">
                                                    <div class="form-group col-md-2">
                                                        <label>Price:</label>
                                                        <input type="text" class="form-control" name="estimated_price" value="<?php echo escapeHtml($booking_to_edit['estimated_price']); ?>">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-5">
                                                        <label for="helper">Do you need a helper to load and unload?</label><br>
                                                        <label>Yes: <input name="helper" type="radio" value="Yes" <?php if (escapeHtml($booking_to_edit['helper']) === 'Yes') echo 'checked'; ?> required> / </label>
                                                        <label>No: <input name="helper" type="radio" value="No" <?php if (escapeHtml($booking_to_edit['helper']) === 'No') echo 'checked'; ?> required></label><br><br>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-5">
                                                        <label for="release_another_booking">Do you want to release another booking for this date/time?</label></br>
                                                        <label>Yes: <input name="release_another_booking" type="radio" id="release_another_booking" value="Yes" required> / </label>
                                                        <label>No: <input name="release_another_booking" type="radio" id="release_another_booking" value="No" checked required></label>
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-lg btn-primary btn-block">Save Changes</button>
                                                <a href="manage_bookings.php" class="btn btn-lg btn-danger btn-block">Cancel</a>
                                            </form>
                                        </div>
                                    </div>
                                <?php endif; ?>

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
                                    echo "<td style='background-color: rgba(204, 204, 204);'>";
                                    // Edit button opens the editing form for the corresponding booking
                                    echo "<div style='display: inline-block; margin-bottom: 5px;'>";
                                    echo "<form method='POST' style='display: inline;'>";
                                    echo "<input type='hidden' name='edit_booking_id' value='" . $row['booking_id'] . "'>";
                                    echo "<button type='submit' class='btn btn-xs btn-primary btn-block edit-button'>Edit</button>";
                                    echo "</form>";
                                    echo "</div>";
                                    // Delete button for each booking
                                    echo "<div style='display: inline-block;'>";
                                    echo "<form method='POST' style='display: inline;'>";
                                    echo "<input type='hidden' name='delete_booking_form' value='" . $row['booking_id'] . "'>";
                                    echo "<button type='submit' name='delete_booking_id_confirmation' class='btn btn-xs btn-danger btn-block'>Delete</button>";
                                    echo "</form>";
                                    echo "</div>";


                                    // Check the booking confirmation before submition on specific booking
                                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking_id_confirmation']) && $_POST['delete_booking_form'] == $row['booking_id']) {
                                        echo '<div class="alert alert-warning">';
                                        echo '<strong>Are you sure you want to DELETE this booking?</strong><br>';
                                        echo '<form method="POST" style="display: inline;">';
                                        echo '<input type="hidden" name="delete_booking_id" value="' . $row['booking_id'] . '">';
                                        echo '<button type="submit" class="btn btn-xs btn-success">Yes</button>';
                                        echo '</form>';
                                        echo '<form method="POST" style="display: inline;">';
                                        echo '<button type="submit" class="btn btn-xs btn-danger">No</button>';
                                        echo '</form>';
                                        echo '</div>';
                                    }
                                    echo "</td>";



                                    echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['booking_id']) . "</td>";
                                    echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['firstname']) . "</td>";
                                    echo "<td style='color: #4d4d4d; font-weight: bold;'>" . escapeHtml($row['phone']) . "</td>";
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

                                    // Print the driver firstname if it exists, otherwise print the driver_id directly
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
                </div>
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

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <!-- Script to timing the Slides -->
    <script>
        $('.carousel').carousel({
            interval: 5000 //Timing of the slides
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hoursContainer = document.getElementById('hoursContainer');
            const radioButtons = hoursContainer.querySelectorAll('input[type="radio"]');
            const hiddenInput = document.getElementById('hidden_selected_hour');

            // Set the initial value of hidden input only if the booking_to_edit is set
            <?php if (isset($booking_to_edit)) : ?>
                hiddenInput.value = "<?php echo escapeHtml($booking_to_edit['selected_hour']); ?>";
            <?php endif; ?>

            radioButtons.forEach(function(radio) {
                radio.addEventListener('click', function() {
                    if (radio.disabled) {
                        hiddenInput.value = radio.value;
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch the select element for the date
            const dateSelect = document.getElementById('selected_date');
            // Fetch the container for the radio buttons
            const hoursContainer = document.getElementById('hoursContainer');

            // Function to update the radio buttons based on available hours
            function updateRadioButtons(availableHours) {
                // Fetch all the radio buttons for the hours
                const radioButtons = hoursContainer.querySelectorAll('input[type="radio"]');

                // Get the currently selected value
                let selectedValue = '';
                radioButtons.forEach(function(radio) {
                    if (radio.checked) {
                        selectedValue = radio.value;
                    }
                    radio.disabled = false;
                    radio.nextSibling.textContent = ''; // Reset the text content
                    radio.checked = false; // Uncheck the radio button
                });

                // Clear the selected radio buttons when selecting a date
                dateSelect.addEventListener('change', function() {
                    radioButtons.forEach(function(radio) {
                        radio.checked = false;
                    });
                });

                // Mark the unavailable hours as red and display the message
                availableHours.forEach(function(hour) {
                    const radio = hoursContainer.querySelector(`input[value="${hour}"]`);
                    if (radio) {
                        radio.disabled = true;
                        // Display the message Unavailable and change the radio to red when the radio is set disable using style.css
                    }
                });
                // Set the previously selected value back
                const selectedRadio = hoursContainer.querySelector(`input[value="${selectedValue}"]`);
                if (selectedRadio) {
                    selectedRadio.checked = true;
                }
            }

            // Function to fetch available hours from the server
            function fetchAvailableHours() {
                const selectedDate = dateSelect.value;
                if (!selectedDate) {
                    return; // Do nothing if no date is selected
                }

                // Use fetch API to get unavailable hours
                fetch('booking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `check_available_hours=true&selected_date=${encodeURIComponent(selectedDate)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        updateRadioButtons(data); // Update the radio buttons with available hours
                    });
            }

            // Function to set the default value of the radio buttons
            function setDefaultRadioValue(defaultValue) {
                // Fetch all the radio buttons for the hours
                const radioButtons = hoursContainer.querySelectorAll('input[type="radio"]');

                // Loop through radio buttons and set the default checked value
                radioButtons.forEach(function(radio) {
                    if (radio.value === defaultValue) {
                        radio.checked = true;
                    }
                });
            }

            // Set the default value for radio buttons
            <?php if (isset($booking_to_edit)) : ?>
                const defaultHourValue = "<?php echo escapeHtml($booking_to_edit['selected_hour']); ?>";
                setDefaultRadioValue(defaultHourValue);
            <?php endif; ?>

            // Fetch available hours when the form loads
            fetchAvailableHours();

            // Add an event listener to the date select to fetch available hours
            dateSelect.addEventListener('change', fetchAvailableHours);
        });
    </script>


</body>

</html>