<?php
// Start and handle the $_Session
require_once 'functions/session_protection.php';
// Check user access
checkUserAccess();

header("Content-Type: text/html; charset=UTF-8", true);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// database conection
require 'functions/connection.php';

if (isset($_POST['form_data_input']) && $_POST['form_data_input'] === "booking_service") {
    try {
        // Get form data
        $user_id = ($_POST['user_id']);
        $booking_id = ($_POST['booking_id']);
        $service_type = protection($_POST['service_type']);
        $other_service_type = ucfirst(protection($_POST['other_service_type'])); //ucfirst is to force the input values start with a capital letter
        $address_from = strtoupper(protection($_POST['address_from'])); //strtoupper is to force the input values to be capital letter
        $eircode_from = strtoupper(protection($_POST['eircode_from']));
        $address_to = strtoupper(protection($_POST['address_to']));
        $eircode_to = strtoupper(protection($_POST['eircode_to']));
        $selected_date = protection($_POST['selected_date']);
        $selected_hour = protection($_POST['selected_hour']);
        $notes = ucfirst(protection($_POST['notes']));
        $helper = protection($_POST['helper']);
        $release_another_booking = protection($_POST['release_another_booking']);
        $service_status = protection($_POST['service_status']);
        $estimated_price = protection($_POST['estimated_price']);
        $driver_id = protection($_POST['driver_id']);

        // Gather checkbox data and store it as a comma-separated string
        $is_home_moving = implode(', ', array_filter(array(
            isset($_POST['Suitcases']) ? 'Suitcases' : '',
            isset($_POST['Boxes']) ? 'Boxes' : '',
            isset($_POST['Bags']) ? 'Bags' : '',
            isset($_POST['Furnitures']) ? 'Furnitures' : '',
            isset($_POST['Television']) ? 'Television' : '',
            isset($_POST['Bicycle']) ? 'Bicycle' : ''
        )));


        // Insert data into bookings table
        $stmt = $conn->prepare('INSERT INTO bookings (user_id, booking_id, service_type, is_home_moving, other_service_type, address_from, eircode_from, address_to, eircode_to, selected_date, selected_hour, notes, helper, release_another_booking, service_status, estimated_price, driver_id)
        VALUES (:user_id, :booking_id, :service_type, :is_home_moving, :other_service_type, :address_from, :eircode_from, :address_to, :eircode_to, :selected_date, :selected_hour, :notes, :helper, :release_another_booking, :service_status, :estimated_price, :driver_id)');

        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':booking_id', $booking_id);
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
        $stmt->bindValue(':service_status', $service_status);
        $stmt->bindValue(':estimated_price', $estimated_price);
        $stmt->bindValue(':driver_id', $driver_id);

        $stmt->execute();
    } catch (PDOException $e) {
        // if an exception occurs, it displays on the screen
        print "Error!: " . $e->getMessage() . "\n";
        die();
    }
    echo "<script>alert('Your booking has been successfully submitted for review!');</script>";
}

if (isset($_POST['check_available_hours']) && $_POST['check_available_hours'] === "true") {
    try {
        // database conection
        require_once 'functions/connection.php';

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

function build_calendar()
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
            $weekdays[] = $date;
        }
    }

    // Create the HTML select element with an id attribute
    $select = '<select name="selected_date" id="selected_date" class="form-control" required>';
    $select .= '<option value="" selected disabled>Select a weekday</option>';
    foreach ($weekdays as $index => $weekday) {
        $optionValue = $weekday->format('Y-m-d');
        $optionText = $weekday->format('l, F j, Y');
        $select .= "<option value=\"$optionValue\">$optionText</option>";
    }
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
    <script src="js/valida_login.js"></script>
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
            <div class="form-style">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <form method="POST" action="booking.php" name="form_booking" id="form_booking" class="form-style">

                            <h2 class="form-login-heading">Booking a Service</h2><br>

                            <input name="booking_id" type="hidden" id="booking_id" class="form-control" value="0">
                            <input name="user_id" type="hidden" id="user_id" class="form-control" value="<?php echo $_SESSION['user_id']; ?>">
                            <input name="release_another_booking" type="hidden" id="release_another_booking" class="form-control" value="no">
                            <input name="service_status" type="hidden" id="service_status" class="form-control" value="Analyzing">
                            <input name="driver_id" type="hidden" id="driver_id" class="form-control" value="N/A">

                            <div class="col-md-12">
                                <label for="service_type">Pick up a service:</label>
                                <select name="service_type" id="service_type" class="form-control" onchange="updateEstimatedPrice()" required>
                                    <option value="">Select a service:</option>
                                    <option value="Delivery and Collection">Delivery and Collection</option>
                                    <option value="Furniture Removal">Furniture Removal</option>
                                    <option value="Home Moving">Home Moving</option>
                                    <option value="Motorcycle Transportation">Motorcycle Transportation</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>

                            <div id="othersOptions" class="col-md-12" style="display: none;">
                                <label for="othersServices">Please, describe more about the service:</label>
                                <textarea name="other_service_type" class="form-control" rows="5"></textarea><br><br>
                            </div>

                            <div id="additionalOptions" style="display: none;">
                                <div class="col-md-12">
                                    <label for="additionalOptions">Please, select the items for transportation!</label><br>
                                </div>
                                <div class="col-md-6">
                                    <label for="Suitcases">Suitcases: </label>
                                    <input type="checkbox" name="Suitcases" id="suitcases"><br>
                                </div>
                                <div class="col-md-6">
                                    <label for="Boxes">Boxes: </label>
                                    <input type="checkbox" name="Boxes" id="boxes"><br>
                                </div>
                                <div class="col-md-6">
                                    <label for="Bags">Bags: </label>
                                    <input type="checkbox" name="Bags" id="bags"><br>
                                </div>
                                <div class="col-md-6">
                                    <label for="Furniture">Furniture: </label>
                                    <input type="checkbox" name="Furniture" id="furniture" onchange="updateEstimatedPrice()"><br>
                                </div>
                                <div class="col-md-6">
                                    <label for="Television">Television: </label>
                                    <input type="checkbox" name="Television" id="television"><br>
                                </div>
                                <div class="col-md-6">
                                    <label for="Bicycle">Bicycles: </label>
                                    <input type="checkbox" name="Bicycle" id="bicycle"><br><br><br>
                                </div>
                            </div>


                            <div class="col-md-9">
                                <label for="address_from">Address From:</label>
                                <input name="address_from" type="address" id="address_from" class="form-control" placeholder="Address 1" maxlength="50" required>
                            </div>
                            <div class="col-md-2">
                                <label for="eircode_from">Zip 1</label>
                                <input name="eircode_from" type="text" id="eircode_from" class="form-control" style="text-transform: uppercase;" placeholder="Eircode" maxlength="50" required>
                            </div>
                            <div class="col-md-9">
                                <label for="address_to">Address To:</label>
                                <input name="address_to" type="address" id="address_to" class="form-control" placeholder="Address 2" maxlength="50" required>
                            </div>
                            <div class="col-md-2">
                                <label for="eircode_to">Zip 2</label>
                                <input name="eircode_to" type="text" id="eircode_to" class="form-control" style="text-transform: uppercase;" placeholder="Eircode" maxlength="50" required><br>
                            </div>


                            <div class="col-md-12">
                                <label>Pick up a Date</label>
                                <?php echo build_calendar(); ?>
                            </div>

                            <div id="hoursContainer" class="col-md-12">
                                <label>Select an Hour</label>
                                <div id="hoursRadio">
                                    <input type="radio" id="hour_0" name="selected_hour" value="07:00 - 09:00" required>
                                    <label for="hour_0">07:00 - 09:00</label><br>

                                    <input type="radio" id="hour_1" name="selected_hour" value="09:00 - 11:00" required>
                                    <label for="hour_1">09:00 - 11:00</label><br>

                                    <input type="radio" id="hour_2" name="selected_hour" value="11:00 - 13:00" required>
                                    <label for="hour_2">11:00 - 13:00</label><br>

                                    <input type="radio" id="hour_3" name="selected_hour" value="14:00 - 16:00" required>
                                    <label for="hour_3">14:00 - 16:00</label><br>

                                    <input type="radio" id="hour_4" name="selected_hour" value="16:00 - 18:00" required>
                                    <label for="hour_4">16:00 - 18:00</label><br>
                                </div>
                            </div>

                            <div id="notes" class="col-md-12">
                                <br><label for="notes">Notes or Instructions (Optional):</label>
                                <textarea name="notes" id="notes" class="form-control" rows="5"></textarea><br>
                            </div>

                            <div class="col-md-9">
                                <label for="helper">Do you need a helper to load and unload?</label></br>
                                <label>Yes: <input name="helper" type="radio" id="helper" value="Yes" onchange="updateEstimatedPrice()" required> / </label>
                                <label>No: <input name="helper" type="radio" id="helper" value="No" onchange="updateEstimatedPrice()" required></label><br><br>
                            </div>

                            <div class="form-login-heading col-md-12">
                                <label style="font-size: 20px;" for="price"><label style="color: red;">*</label>Estimated Price: <label style="font-size: 30px; color: green">€<span id="estimatedPrice">0</span></label></label><br>
                                <label style="color: red; font-size: larger;">*</label><label style="font-size: smaller;">The estimated price may change depending on the circumstances of the service.</label><br><br>
                            </div>

                            <input type="hidden" name="estimated_price" id="estimated_price" value="0">
                            <input type="hidden" name="form_data_input" value="booking_service">
                            <button name="Button" type="submit" class="btn btn-lg btn-primary btn-block">Booking Now</button>

                        </form>
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
            // Fetch the select element for the date
            const dateSelect = document.getElementById('selected_date');
            // Fetch the container for the radio buttons
            const hoursContainer = document.getElementById('hoursContainer');

            // Function to update the radio buttons based on available hours
            function updateRadioButtons(availableHours) {
                // Fetch all the radio buttons for the hours
                const radioButtons = hoursContainer.querySelectorAll('input[type="radio"]');

                // Reset all radio buttons to their default state (unchecked)
                radioButtons.forEach(function(radio) {
                    radio.disabled = false;
                    radio.nextSibling.textContent = ''; // Reset the text content
                    radio.checked = false; // Uncheck the radio button
                });

                // Mark the unavailable hours as red and display the message
                availableHours.forEach(function(hour) {
                    const radio = hoursContainer.querySelector(`input[value="${hour}"]`);
                    if (radio) {
                        radio.disabled = true;
                        // Display the message Unavailable and change the radio to red when the radio is set disable using style.css
                    }
                });
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

            // Add an event listener to the date select to fetch available hours
            dateSelect.addEventListener('change', fetchAvailableHours);
        });
    </script>

    <script>
        var selectedService = ""; // Global variable to store the current selected service

        function updateEstimatedPrice() {
            var selectedValue = document.getElementById('service_type').value;
            var estimatedPrice = document.getElementById('estimatedPrice');

            // Define prices for each service type as variables
            var deliveryCollectionPrice = 55;
            var furnitureRemovalPrice = 120;
            var homeMovingPrice = 60;
            var homeMovingFurniturePrice = 85;
            var motocycleTransportationPrice = 60;
            var othersPrice = "To be analyzed!";

            // Function to calculate the price based on the selected service type
            function calculatePrice(service_type) {
                if (service_type === 'Delivery and Collection') {
                    return deliveryCollectionPrice;
                } else if (service_type === 'Furniture Removal') {
                    return furnitureRemovalPrice;
                } else if (service_type === 'Home Moving') {
                    return homeMovingPrice;
                } else if (service_type === 'Furniture') {
                    return homeMovingFurniturePrice;
                } else if (service_type === 'Motorcycle Transportation') {
                    return motocycleTransportationPrice;
                } else if (service_type === 'Others') {
                    return othersPrice;
                }
                return 0;
            }

            // Check if "Furniture" checkbox is checked
            var furnitureCheckbox = document.getElementById('furniture');
            var selectedService = furnitureCheckbox.checked ? 'Furniture' : selectedValue;

            // Check if "helper" radio button is selected as 'yes' add 30 to the price
            var helperRadioButton = document.querySelector('input[name="helper"]:checked');
            var needHelp = helperRadioButton ? helperRadioButton.value : 'No';
            var price = calculatePrice(selectedService);

            // Add 30 if "helper" is selected as 'yes'
            if (needHelp === 'Yes' && selectedValue != 'Others' && selectedValue != '') {
                price += 30;
            }

            if (selectedValue === 'Others') {
                estimatedPrice.textContent = price;
            } else {
                estimatedPrice.textContent = price + ".00";
            }

            // Update the hidden input field with the calculated price
            document.getElementById('estimated_price').value = price;
        }

        document.getElementById('service_type').addEventListener('change', function() {
            var selectedValue = this.value;
            var additionalOptionsDiv = document.getElementById('additionalOptions');
            var checkboxes = additionalOptionsDiv.querySelectorAll('input[type="checkbox"]');
            var othersOptionsDiv = document.getElementById('othersOptions');
            var textarea = othersOptionsDiv.querySelector('textarea');

            // Function to clear all input values
            function clearInputValues() {
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = false;
                });

                textarea.value = '';
            }

            if (selectedValue === 'Others') {
                additionalOptionsDiv.style.display = 'none';
                othersOptionsDiv.style.display = 'block';
                textarea.setAttribute('required', 'required');
                clearInputValues();
            } else if (selectedValue === 'Home Moving') {
                additionalOptionsDiv.style.display = 'block';
                othersOptionsDiv.style.display = 'none';
                clearInputValues();
            } else {
                additionalOptionsDiv.style.display = 'none';
                othersOptionsDiv.style.display = 'none';
                textarea.removeAttribute('required');
                clearInputValues();
            }

            updateEstimatedPrice(); // Update the estimated price when the service type is changed
        });
    </script>

</body>

</html>