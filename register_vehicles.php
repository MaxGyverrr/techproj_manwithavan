<?php
// Start and handle the $_Session
require_once 'functions/session_protection.php';
// Check admin access
checkAdminAccess();

header("Content-Type: text/html; charset=UTF-8", true);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// database conection
require 'functions/connection.php';

// Function to escape HTML entities for output
function escapeHtml($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

if (isset($_POST['form_data_input']) && $_POST['form_data_input'] === "register_vehicle") {
    try {
        // receive form data
        $vehicle_id = "";
        $vehicle_reg = strtoupper(protection($_POST['vehicle_reg'])); //strtoupper is to force the input values to be capital letter
        $make = strtoupper(protection($_POST['make']));
        $model = strtoupper(protection($_POST['model']));
        $colour = strtoupper(protection($_POST['colour']));
        $year = protection($_POST['year']);
        $fuel_type = strtoupper(protection($_POST['fuel_type']));
        $vehicle_status = protection($_POST['vehicle_status']);


        $stmt = $conn->prepare('INSERT INTO vehicles VALUES (:vehicle_id,:vehicle_reg,:make,:model,:colour,:year,:fuel_type,:vehicle_status)');
        $stmt->bindValue(':vehicle_id', $vehicle_id);
        $stmt->bindValue(':vehicle_reg', $vehicle_reg);
        $stmt->bindValue(':make', $make);
        $stmt->bindValue(':model', $model);
        $stmt->bindValue(':colour', $colour);
        $stmt->bindValue(':year', $year);
        $stmt->bindValue(':fuel_type', $fuel_type);
        $stmt->bindValue(':vehicle_status', $vehicle_status);


        $stmt->execute();
    } catch (PDOException $e) {
        // if an exception occurs, it displays on the screen
        print "Erro!: " . $e->getMessage() . "\n";
        die();
    }
    echo "<script>alert('The vehicle has been successfully registered!');window.location='register_vehicles.php';</script>";
}


// Check if the form is submitted to update the driver
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vehicle_id'])) {
    $vehicle_id = ($_POST['vehicle_id']);
    $vehicle_reg = strtoupper(protection($_POST['vehicle_reg'])); //strtoupper is to force the input values to be capital letter
    $make = strtoupper(protection($_POST['make']));
    $model = strtoupper(protection($_POST['model']));
    $colour = strtoupper(protection($_POST['colour']));
    $year = protection($_POST['year']);
    $fuel_type = strtoupper(protection($_POST['fuel_type']));

    // Prepare the UPDATE query
    $stmt = $conn->prepare('UPDATE vehicles SET vehicle_id = :vehicle_id, vehicle_reg = :vehicle_reg, make = :make, model = :model, colour = :colour, year = :year, fuel_type = :fuel_type WHERE vehicle_reg = :vehicle_reg');
    $stmt->bindValue(':vehicle_id', $vehicle_id);
    $stmt->bindValue(':vehicle_reg', $vehicle_reg);
    $stmt->bindValue(':make', $make);
    $stmt->bindValue(':model', $model);
    $stmt->bindValue(':colour', $colour);
    $stmt->bindValue(':year', $year);
    $stmt->bindValue(':fuel_type', $fuel_type);

    // Execute the update query
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->rowCount() > 0) {
        echo "Vehicle updated successfully.";
    } else {
        echo "Error updating driver: " . print_r($stmt->errorInfo(), true);
    }
}


// Check if the "Delete Vehicle" button was clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_vehicle_id'])) {
    $delete_vehicle_id = $_POST['delete_vehicle_id'];
    // Update the user_status to 'inactive' for the corresponding vehicle in the database
    $stmt = $conn->prepare('UPDATE vehicles SET vehicle_status = :vehicle_status WHERE vehicle_id = :vehicle_id');
    $stmt->bindValue(':vehicle_status', 'inactive');
    $stmt->bindValue(':vehicle_id', $delete_vehicle_id);
    $stmt->execute();
}


// Check if the "Recover Vehicle" button was clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recover_vehicle_id'])) {
    $recover_vehicle_id = $_POST['recover_vehicle_id'];
    // Update the user_status to 'active' for the corresponding vehicle in the database
    $stmt = $conn->prepare('UPDATE vehicles SET vehicle_status = :vehicle_status WHERE vehicle_id = :vehicle_id');
    $stmt->bindValue(':vehicle_status', 'active');
    $stmt->bindValue(':vehicle_id', $recover_vehicle_id);
    $stmt->execute();
}

// Check if a driver is being edited
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user_id'])) {
    $edit_user_id = $_POST['edit_user_id'];
    // Retrieve the driver data from the database
    $stmt = $conn->prepare('SELECT * FROM vehicles WHERE vehicle_id = :vehicle_id');
    $stmt->bindValue(':vehicle_id', $edit_user_id);
    $stmt->execute();
    $edit_user_id = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Build the SQL query based on the search query
$ComandoSQL = "SELECT * FROM vehicles";

// Prepare the SQL statement with the possible search condition
$stmt = $conn->prepare($ComandoSQL);

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
    <script src="js/valida_login.js"></script>

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
                        <form method="POST" id="form_input" action="register_vehicles.php" name="form_input">

                            <input name="vehicle_status" type="hidden" id="usertype" class="form-control" value="active">

                            <h2 class="form-login-heading">Please, enter the vehicle details</h2>

                            <div class="row">
                                <div class="col-md-12">
                                    <input name="vehicle_reg" type="text" id="vehicle_reg" class="form-control" placeholder="Vehicle Registration" maxlength="16" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <input name="make" type="text" id="make" class="form-control" placeholder="Make" maxlength="50" required>
                                </div>
                                <div class="col-md-6">
                                    <input name="model" type="text" id="model" class="form-control" placeholder="Model" maxlength="50" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <select name="year" id="year" class="form-control" required>
                                        <option value="">Select Year</option>
                                        <?php
                                        $start_year = 1995;
                                        $end_year = 2050;

                                        for ($year = $start_year; $year <= $end_year; $year++) {
                                            echo "<option value=\"$year\">$year</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-7">
                                    <input name="colour" type="text" id="colour" class="form-control" placeholder="Colour" maxlength="50" required>
                                </div>
                                <div class="col-md-2">
                                    <select name="fuel_type" id="fuel_type" class="form-control" required>
                                        <option value="">Fuel Type:</option>
                                        <option value="Petrol">Petrol</option>
                                        <option value="Diesel">Diesel</option>
                                        <option value="Electric">Electric</option>
                                        <option value="Hybrid">Hybrid</option>
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" name="form_data_input" value="register_vehicle">
                            <button name="Button" type="submit" class="btn btn-lg btn-primary btn-block">Register Vehicle</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="panel-title">
            <h2>Active Vehicles</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>Vehicle Registration</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Year</th>
                        <th>Colour</th>
                        <th>Fuel</th>

                    </tr>
                </thead>

                <?php
                // Loop each vehicle and display the data
                foreach ($result as $row) {
                    if ($row['vehicle_status'] === 'active') {
                        echo "<tr";
                        echo " style='background: rgba(51, 204, 51, 0.2);'";
                        echo ">";
                        echo "<td>";
                        // Edit button opens the editing form for the corresponding vehicle
                        echo "<div style='display: inline-block; margin-bottom: 5px;'>";
                        echo "<form method='POST' style='display: inline;'>";
                        echo "<input type='hidden' name='edit_user_id' value='" . $row['vehicle_id'] . "'>";
                        echo "<button type='submit' class='btn btn-xs btn-primary btn-block edit-button'>Edit</button>";
                        echo "</form>";
                        echo "</div>";

                        // Button to change vehicle status to 'inactive'
                        if (escapeHtml($row['vehicle_status']) == 'active' && $row['vehicle_id']) {
                            echo "<div style='display: inline-block; margin-bottom: 5px;'>";
                            echo "<form method='POST' style='display: inline;'>";
                            echo "<input type='hidden' name='delete_vehicle_form' value='" . $row['vehicle_id'] . "'>";
                            echo "<button type='submit' name='delete_vehicle_request' class='btn btn-xs btn-danger btn-block'>Delete Vehicle</button>";
                            echo "</form>";
                            echo "</div>";
                        }

                        // Check the booking confirmation before submition on specific booking
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_vehicle_request']) && $_POST['delete_vehicle_form'] == $row['vehicle_id']) {
                            echo '<div class="alert alert-warning">';
                            echo '<strong>Delete this vehicle?</strong><br>';
                            echo '<form method="POST" style="display: inline;">';
                            echo '<input type="hidden" name="delete_vehicle_id" value="' . $row['vehicle_id'] . '">';
                            echo '<button type="submit" class="btn btn-xs btn-success">Yes</button>';
                            echo '</form>';
                            echo '<form method="POST" style="display: inline;">';
                            echo '<button type="submit" class="btn btn-xs btn-danger">No</button>';
                            echo '</form>';
                            echo '</div>';
                        }
                        echo "</td>";

                        echo "<td>" . escapeHtml($row['vehicle_reg']) . "</td>";
                        echo "<td>" . escapeHtml($row['make']) . "</td>";
                        echo "<td>" . escapeHtml($row['model']) . "</td>";
                        echo "<td>" . escapeHtml($row['year']) . "</td>";
                        echo "<td>" . escapeHtml($row['colour']) . "</td>";
                        echo "<td>" . escapeHtml($row['fuel_type']) . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
                </tbody>
            </table>
        </div>


        <?php
        // Check if has any inactive Driver
        $hasInactiveVehicles = false;
        foreach ($result as $row) {
            if ($row['vehicle_status'] === 'inactive') {
                $hasInactiveVehicles = true;
                break;
            }
        }
        ?>


        <?php if ($hasInactiveVehicles) : ?>
            <div class="panel-title">
                <h2>Deleted Vehicles</h2>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Actions</th>
                            <th>Vehicle Registration</th>
                            <th>Make</th>
                            <th>Model</th>
                            <th>Year</th>
                            <th>Colour</th>
                            <th>Fuel</th>

                        </tr>
                    </thead>

                    <?php
                    // Loop each vehicle and display the data
                    foreach ($result as $row) {
                        if ($row['vehicle_status'] === 'inactive') {
                            echo "<tr";
                            echo " style='background: rgba(213, 84, 84, 0.7);'";
                            echo ">";

                            echo "<td>";
                            // Button to change vehicle status to 'inactive'
                            if (escapeHtml($row['vehicle_status']) == 'inactive' && $row['vehicle_id']) {
                                echo "<div style='display: inline-block; margin-bottom: 5px;'>";
                                echo "<form method='POST' style='display: inline;'>";
                                echo '<input type="hidden" name="recover_vehicle_id" value="' . $row['vehicle_id'] . '">';
                                echo "<button type='submit' class='btn btn-xs btn-success btn-block'>Recover Vehicle</button>";
                                echo "</form>";
                                echo "</div>";
                            }
                            echo "</td>";

                            echo "<td>" . escapeHtml($row['vehicle_reg']) . "</td>";
                            echo "<td>" . escapeHtml($row['make']) . "</td>";
                            echo "<td>" . escapeHtml($row['model']) . "</td>";
                            echo "<td>" . escapeHtml($row['year']) . "</td>";
                            echo "<td>" . escapeHtml($row['colour']) . "</td>";
                            echo "<td>" . escapeHtml($row['fuel_type']) . "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                    </tbody>
                </table>
            <?php endif; ?>



            <!-- Display the booking editing form if a booking is being edited -->
            <?php if (isset($edit_user_id)) : ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2>Editing Vehicle</h2>
                        <form method="POST">
                            <!-- Input fields for editing the booking -->
                            <input type="hidden" name="vehicle_id" value="<?php echo escapeHtml($edit_user_id['vehicle_id']); ?>">
                            <div class="form-group">
                                <label>Vehicle Registration:</label>
                                <input type="text" class="form-control" name="vehicle_reg" value="<?php echo escapeHtml($edit_user_id['vehicle_reg']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Make:</label>
                                <input type="text" class="form-control" name="make" value="<?php echo escapeHtml($edit_user_id['make']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Model:</label>
                                <input type="text" class="form-control" name="model" value="<?php echo escapeHtml($edit_user_id['model']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Year:</label>
                                <select name="year" id="year" class="form-control" value="<?php echo escapeHtml($edit_user_id['year']); ?>" required>
                                    <option value="">Select Year</option>
                                    <?php
                                    $start_year = 1995;
                                    $end_year = 2050;

                                    for ($year = $start_year; $year <= $end_year; $year++) {
                                        $selected = ($edit_user_id['year'] == $year) ? 'selected' : '';
                                        echo "<option value=\"$year\" $selected>$year</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Colour:</label>
                                <input type="text" class="form-control" name="colour" value="<?php echo escapeHtml($edit_user_id['colour']); ?>">
                            </div>

                            <div class="form-group">
                                <label>Fuel:</label>
                                <select name="fuel_type" id="fuel_type" class="form-control" value="<?php echo escapeHtml($edit_user_id['fuel_type']); ?>" required>
                                    <option value="">Fuel Type:</option>
                                    <option value="PETROL" <?php if ($edit_user_id['fuel_type'] === 'PETROL') echo 'selected'; ?>>Petrol</option>
                                    <option value="DIESEL" <?php if ($edit_user_id['fuel_type'] === 'DIESEL') echo 'selected'; ?>>Diesel</option>
                                    <option value="ELECTRIC" <?php if ($edit_user_id['fuel_type'] === 'ELECTRIC') echo 'selected'; ?>>Electric</option>
                                    <option value="HYBRID" <?php if ($edit_user_id['fuel_type'] === 'HYBRID') echo 'selected'; ?>>Hybrid</option>
                                </select>
                            </div>


                            <button type="submit" class="btn btn-lg btn-primary btn-block">Save Changes</button>
                            <a href="register_vehicles.php" class="btn btn-lg btn-danger btn-block">Cancel</a>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
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

</body>

</html>