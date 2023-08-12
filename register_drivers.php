<?php
// Start and handle the $_Session
require_once 'functions/session_protection.php';
// Check admin access
checkAdminAccess();

// database conection
require 'functions/connection.php';

header("Content-Type: text/html; charset=UTF-8", true);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Function to escape HTML entities for output
function escapeHtml($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

if (isset($_POST['form_data_input']) && $_POST['form_data_input'] === "register_driver") {
    try {
        // receive form data
        $user_id = "";
        $title = protection($_POST['title']);
        $firstname = ucfirst(protection($_POST['firstname']));
        $lastname = ucfirst(protection($_POST['lastname'])); //ucfirst is to force the input values start with a capital letter
        $email = protection($_POST['email']);
        $passw = password_hash($_POST['passw'], PASSWORD_DEFAULT);
        $phone = protection($_POST['phone']);
        $nationality = protection($_POST['nationality']);
        $aboutus = protection($_POST['aboutus']);
        $usertype = protection($_POST['usertype']);
        $user_status = protection($_POST['user_status']);


        $stmt = $conn->prepare('INSERT INTO users VALUES
	(:user_id,:title,:firstname,:lastname,:email,:passw,:phone,:nationality,:aboutus,:usertype,:user_status)');
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':firstname', $firstname);
        $stmt->bindValue(':lastname', $lastname);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':passw', $passw);
        $stmt->bindValue(':phone', $phone);
        $stmt->bindValue(':nationality', $nationality);
        $stmt->bindValue(':aboutus', $aboutus);
        $stmt->bindValue(':usertype', $usertype);
        $stmt->bindValue(':user_status', $user_status);

        $stmt->execute();
    } catch (PDOException $e) {
        // if an exception occurs, it displays on the screen
        print "Erro!: " . $e->getMessage() . "\n";
        die();
    }
    echo "<script>alert('The driver has been successfully registered!');window.location='register_drivers.php';</script>";
}


// Check if the form is submitted to update the driver
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = "";
    $title = protection($_POST['title']);
    $firstname = ucfirst(protection($_POST['firstname'])); //ucfirst is to force the input values start with a capital letter
    $lastname = ucfirst(protection($_POST['lastname']));
    $email = protection($_POST['email']);
    $passw = password_hash($_POST['passw'], PASSWORD_DEFAULT);
    $phone = protection($_POST['phone']);
    $nationality = protection($_POST['nationality']);
    $usertype = protection($_POST['usertype']);
    $user_status = protection($_POST['user_status']);

    // Prepare the UPDATE query
    $stmt = $conn->prepare('UPDATE users SET user_id = :user_id, title = :title, firstname = :firstname, lastname = :lastname, email = :email, passw = :passw, phone = :phone, nationality = :nationality, user_status = :user_status, usertype = :usertype WHERE user_id = :user_id');
    $stmt->bindValue(':user_id', $user_id);
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':firstname', $firstname);
    $stmt->bindValue(':lastname', $lastname);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':passw', $passw);
    $stmt->bindValue(':phone', $phone);
    $stmt->bindValue(':nationality', $nationality);
    $stmt->bindValue(':usertype', $usertype);
    $stmt->bindValue(':user_status', $user_status);

    // Execute the update query
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->rowCount() > 0) {
        echo "Driver updated successfully.";
    } else {
        echo "Error updating driver: " . print_r($stmt->errorInfo(), true);
    }
}


// Check if the "Delete Driver" button was clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_driver_id'])) {
    $delete_driver_id = $_POST['delete_driver_id'];
    // Update the user_status to 'inactive' for the corresponding user in the database
    $stmt = $conn->prepare('UPDATE users SET user_status = :user_status WHERE user_id = :user_id');
    $stmt->bindValue(':user_status', 'inactive');
    $stmt->bindValue(':user_id', $delete_driver_id);
    $stmt->execute();
}


// Check if the "Recover Driver" button was clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recover_driver_id'])) {
    $recover_driver_id = $_POST['recover_driver_id'];
    // Update the user_status to 'active' for the corresponding user in the database
    $stmt = $conn->prepare('UPDATE users SET user_status = :user_status WHERE user_id = :user_id');
    $stmt->bindValue(':user_status', 'active');
    $stmt->bindValue(':user_id', $recover_driver_id);
    $stmt->execute();
}



// Check if a driver is being edited
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user_id'])) {
    $edit_user_id = $_POST['edit_user_id'];
    // Retrieve the driver data from the database
    $stmt = $conn->prepare('SELECT * FROM users WHERE user_id = :user_id');
    $stmt->bindValue(':user_id', $edit_user_id);
    $stmt->execute();
    $edit_user_id = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Build the SQL query based on the search query
$ComandoSQL = "SELECT * FROM users WHERE usertype = 'driver'";

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/libphonenumber-js/1.9.0/libphonenumber-js.min.js"></script>

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
                        <form method="POST" id="form_input" action="register_drivers.php" name="form_input" onsubmit="return validateForm()">

                            <h2 class="form-login-heading">Please, enter the driver details</h2>

                            <input name="user_id" type="hidden" id="user_id" class="form-control" value="0">
                            <input name="usertype" type="hidden" id="usertype" class="form-control" value="driver">
                            <input name="user_status" type="hidden" id="user_status" class="form-control" value="active">
                            <div class="col-md-2">
                                <select name="title" id="title" class="form-control" required>
                                    <option value="">Title:</option>
                                    <option value="Mr. ">Mr.</option>
                                    <option value="Mrs. ">Mrs.</option>
                                    <option value="Miss. ">Miss.</option>
                                    <option value="Ms. ">Ms.</option>
                                    <option value="Dr. ">Dr.</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input name="firstname" type="text" id="firstname" class="form-control" placeholder="First name" maxlength="50" required>
                            </div>
                            <div class="col-md-5">
                                <input name="lastname" type="text" id="lastname" class="form-control" placeholder="Last name" maxlength="50" required>
                            </div>
                            <div class="col-md-7">
                                <input name="email" type="email" id="email" class="form-control" placeholder="Email" maxlength="50" required>
                            </div>
                            <div class="col-md-6">
                                <input name="passw" type="password" id="passw" class="form-control" placeholder="Enter a password" maxlength="16" required>
                            </div>
                            <div class="col-md-6">
                                <input name="confirm_password" type="password" id="confirm_password" class="form-control" placeholder="Confirm your password" maxlength="16" required>
                            </div>

                            <script>
                                function validateForm() {
                                    var password = document.getElementById("passw").value;
                                    var confirm_password = document.getElementById("confirm_password").value;

                                    if (password !== confirm_password) {
                                        alert("Passwords do not match!");
                                        return false; // Prevent form submission
                                    }

                                    var email = document.getElementById("email").value;

                                    // Check if the email already exists in the database
                                    var xhr = new XMLHttpRequest();
                                    xhr.open("POST", "functions/check_email.php", true);
                                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                    xhr.onreadystatechange = function() {
                                        if (xhr.readyState === 4 && xhr.status === 200) {
                                            var response = xhr.responseText;
                                            if (response === "exists") {
                                                alert("Email already in use!");
                                                return false; // Prevent form submission
                                            } else {
                                                // Continue with form submission
                                                document.getElementById("form_input").submit();
                                            }
                                        }
                                    };
                                    xhr.send("email=" + email);

                                    return false; // Prevent default form submission
                                }
                            </script>


                            <div class="col-md-5">
                                <input type="phone" name="phone" id="phone" class="form-control" placeholder="Phone number" maxlength="30" required>
                            </div>

                            <script>
                                let typingTimer;
                                const doneTypingInterval = 500; // Time refreshing 0.5 second

                                const phoneInput = document.getElementById('phone');

                                phoneInput.addEventListener('input', () => {
                                    clearTimeout(typingTimer);
                                    typingTimer = setTimeout(() => {
                                        const inputValue = phoneInput.value;
                                        const formattedValue = formatPhoneNumber(inputValue);
                                        phoneInput.value = formattedValue;
                                    }, doneTypingInterval);
                                });

                                function formatPhoneNumber(value) {
                                    // List of region codes
                                    const phoneRegionCodes = [
                                        'IE', 'BR', 'PT', 'US',
                                        'AL', 'AD', 'AT', 'BY', 'BE', 'BA', 'BG', 'HR', 'CY', 'CZ', 'DK',
                                        'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IS', 'IT', 'LV', 'LI', 'GB',
                                        'LT', 'LU', 'MT', 'MD', 'MC', 'ME', 'NL', 'MK', 'NO', 'PL', 'UA',
                                        'RO', 'RU', 'SM', 'RS', 'SK', 'SI', 'ES', 'SE', 'CH',
                                    ];

                                    for (const regionCode of phoneRegionCodes) {
                                        // phone number using libphonenumber-js
                                        const phoneNumber = new libphonenumber.parsePhoneNumberFromString(value, regionCode);

                                        if (phoneNumber) {
                                            // Format the phone number
                                            return phoneNumber.formatInternational();
                                        }
                                    }

                                    return value; // Return the original value if not a valid phone number
                                }
                            </script>


                            <div class="col-md-10">
                                <select name="nationality" id="nationality" class="form-control" required>
                                    <option value="">Select your nationality</option>
                                    <option value="afghan">Afghan</option>
                                    <option value="albanian">Albanian</option>
                                    <option value="algerian">Algerian</option>
                                    <option value="american">American</option>
                                    <option value="andorran">Andorran</option>
                                    <option value="angolan">Angolan</option>
                                    <option value="antiguans">Antiguans</option>
                                    <option value="argentinean">Argentinean</option>
                                    <option value="armenian">Armenian</option>
                                    <option value="australian">Australian</option>
                                    <option value="austrian">Austrian</option>
                                    <option value="azerbaijani">Azerbaijani</option>
                                    <option value="bahamian">Bahamian</option>
                                    <option value="bahraini">Bahraini</option>
                                    <option value="bangladeshi">Bangladeshi</option>
                                    <option value="barbadian">Barbadian</option>
                                    <option value="barbudans">Barbudans</option>
                                    <option value="batswana">Batswana</option>
                                    <option value="belarusian">Belarusian</option>
                                    <option value="belgian">Belgian</option>
                                    <option value="belizean">Belizean</option>
                                    <option value="beninese">Beninese</option>
                                    <option value="bhutanese">Bhutanese</option>
                                    <option value="bolivian">Bolivian</option>
                                    <option value="bosnian">Bosnian</option>
                                    <option value="brazilian">Brazilian</option>
                                    <option value="british">British</option>
                                    <option value="bruneian">Bruneian</option>
                                    <option value="bulgarian">Bulgarian</option>
                                    <option value="burkinabe">Burkinabe</option>
                                    <option value="burmese">Burmese</option>
                                    <option value="burundian">Burundian</option>
                                    <option value="cambodian">Cambodian</option>
                                    <option value="cameroonian">Cameroonian</option>
                                    <option value="canadian">Canadian</option>
                                    <option value="cape verdean">Cape Verdean</option>
                                    <option value="central african">Central African</option>
                                    <option value="chadian">Chadian</option>
                                    <option value="chilean">Chilean</option>
                                    <option value="chinese">Chinese</option>
                                    <option value="colombian">Colombian</option>
                                    <option value="comoran">Comoran</option>
                                    <option value="congolese">Congolese</option>
                                    <option value="costa rican">Costa Rican</option>
                                    <option value="croatian">Croatian</option>
                                    <option value="cuban">Cuban</option>
                                    <option value="cypriot">Cypriot</option>
                                    <option value="czech">Czech</option>
                                    <option value="danish">Danish</option>
                                    <option value="djibouti">Djibouti</option>
                                    <option value="dominican">Dominican</option>
                                    <option value="dutch">Dutch</option>
                                    <option value="east timorese">East Timorese</option>
                                    <option value="ecuadorean">Ecuadorean</option>
                                    <option value="egyptian">Egyptian</option>
                                    <option value="emirian">Emirian</option>
                                    <option value="equatorial guinean">Equatorial Guinean</option>
                                    <option value="eritrean">Eritrean</option>
                                    <option value="estonian">Estonian</option>
                                    <option value="ethiopian">Ethiopian</option>
                                    <option value="fijian">Fijian</option>
                                    <option value="filipino">Filipino</option>
                                    <option value="finnish">Finnish</option>
                                    <option value="french">French</option>
                                    <option value="gabonese">Gabonese</option>
                                    <option value="gambian">Gambian</option>
                                    <option value="georgian">Georgian</option>
                                    <option value="german">German</option>
                                    <option value="ghanaian">Ghanaian</option>
                                    <option value="greek">Greek</option>
                                    <option value="grenadian">Grenadian</option>
                                    <option value="guatemalan">Guatemalan</option>
                                    <option value="guinea-bissauan">Guinea-Bissauan</option>
                                    <option value="guinean">Guinean</option>
                                    <option value="guyanese">Guyanese</option>
                                    <option value="haitian">Haitian</option>
                                    <option value="herzegovinian">Herzegovinian</option>
                                    <option value="honduran">Honduran</option>
                                    <option value="hungarian">Hungarian</option>
                                    <option value="icelander">Icelander</option>
                                    <option value="indian">Indian</option>
                                    <option value="indonesian">Indonesian</option>
                                    <option value="iranian">Iranian</option>
                                    <option value="iraqi">Iraqi</option>
                                    <option value="irish">Irish</option>
                                    <option value="israeli">Israeli</option>
                                    <option value="italian">Italian</option>
                                    <option value="ivorian">Ivorian</option>
                                    <option value="jamaican">Jamaican</option>
                                    <option value="japanese">Japanese</option>
                                    <option value="jordanian">Jordanian</option>
                                    <option value="kazakhstani">Kazakhstani</option>
                                    <option value="kenyan">Kenyan</option>
                                    <option value="kittian and nevisian">Kittian and Nevisian</option>
                                    <option value="kuwaiti">Kuwaiti</option>
                                    <option value="kyrgyz">Kyrgyz</option>
                                    <option value="laotian">Laotian</option>
                                    <option value="latvian">Latvian</option>
                                    <option value="lebanese">Lebanese</option>
                                    <option value="liberian">Liberian</option>
                                    <option value="libyan">Libyan</option>
                                    <option value="liechtensteiner">Liechtensteiner</option>
                                    <option value="lithuanian">Lithuanian</option>
                                    <option value="luxembourger">Luxembourger</option>
                                    <option value="macedonian">Macedonian</option>
                                    <option value="malagasy">Malagasy</option>
                                    <option value="malawian">Malawian</option>
                                    <option value="malaysian">Malaysian</option>
                                    <option value="maldivan">Maldivan</option>
                                    <option value="malian">Malian</option>
                                    <option value="maltese">Maltese</option>
                                    <option value="marshallese">Marshallese</option>
                                    <option value="mauritanian">Mauritanian</option>
                                    <option value="mauritian">Mauritian</option>
                                    <option value="mexican">Mexican</option>
                                    <option value="micronesian">Micronesian</option>
                                    <option value="moldovan">Moldovan</option>
                                    <option value="monacan">Monacan</option>
                                    <option value="mongolian">Mongolian</option>
                                    <option value="moroccan">Moroccan</option>
                                    <option value="mosotho">Mosotho</option>
                                    <option value="motswana">Motswana</option>
                                    <option value="mozambican">Mozambican</option>
                                    <option value="namibian">Namibian</option>
                                    <option value="nauruan">Nauruan</option>
                                    <option value="nepalese">Nepalese</option>
                                    <option value="new zealander">New Zealander</option>
                                    <option value="ni-vanuatu">Ni-Vanuatu</option>
                                    <option value="nicaraguan">Nicaraguan</option>
                                    <option value="nigerien">Nigerien</option>
                                    <option value="north korean">North Korean</option>
                                    <option value="northern irish">Northern Irish</option>
                                    <option value="norwegian">Norwegian</option>
                                    <option value="omani">Omani</option>
                                    <option value="pakistani">Pakistani</option>
                                    <option value="palauan">Palauan</option>
                                    <option value="panamanian">Panamanian</option>
                                    <option value="papua new guinean">Papua New Guinean</option>
                                    <option value="paraguayan">Paraguayan</option>
                                    <option value="peruvian">Peruvian</option>
                                    <option value="polish">Polish</option>
                                    <option value="portuguese">Portuguese</option>
                                    <option value="qatari">Qatari</option>
                                    <option value="romanian">Romanian</option>
                                    <option value="russian">Russian</option>
                                    <option value="rwandan">Rwandan</option>
                                    <option value="saint lucian">Saint Lucian</option>
                                    <option value="salvadoran">Salvadoran</option>
                                    <option value="samoan">Samoan</option>
                                    <option value="san marinese">San Marinese</option>
                                    <option value="sao tomean">Sao Tomean</option>
                                    <option value="saudi">Saudi</option>
                                    <option value="scottish">Scottish</option>
                                    <option value="senegalese">Senegalese</option>
                                    <option value="serbian">Serbian</option>
                                    <option value="seychellois">Seychellois</option>
                                    <option value="sierra leonean">Sierra Leonean</option>
                                    <option value="singaporean">Singaporean</option>
                                    <option value="slovakian">Slovakian</option>
                                    <option value="slovenian">Slovenian</option>
                                    <option value="solomon islander">Solomon Islander</option>
                                    <option value="somali">Somali</option>
                                    <option value="south african">South African</option>
                                    <option value="south korean">South Korean</option>
                                    <option value="spanish">Spanish</option>
                                    <option value="sri lankan">Sri Lankan</option>
                                    <option value="sudanese">Sudanese</option>
                                    <option value="surinamer">Surinamer</option>
                                    <option value="swazi">Swazi</option>
                                    <option value="swedish">Swedish</option>
                                    <option value="swiss">Swiss</option>
                                    <option value="syrian">Syrian</option>
                                    <option value="taiwanese">Taiwanese</option>
                                    <option value="tajik">Tajik</option>
                                    <option value="tanzanian">Tanzanian</option>
                                    <option value="thai">Thai</option>
                                    <option value="togolese">Togolese</option>
                                    <option value="tongan">Tongan</option>
                                    <option value="trinidadian or tobagonian">Trinidadian or Tobagonian</option>
                                    <option value="tunisian">Tunisian</option>
                                    <option value="turkish">Turkish</option>
                                    <option value="tuvaluan">Tuvaluan</option>
                                    <option value="ugandan">Ugandan</option>
                                    <option value="ukrainian">Ukrainian</option>
                                    <option value="uruguayan">Uruguayan</option>
                                    <option value="uzbekistani">Uzbekistani</option>
                                    <option value="venezuelan">Venezuelan</option>
                                    <option value="vietnamese">Vietnamese</option>
                                    <option value="welsh">Welsh</option>
                                    <option value="yemenite">Yemenite</option>
                                    <option value="zambian">Zambian</option>
                                    <option value="zimbabwean">Zimbabwean</option>
                                </select>
                            </div>

                            </br></br></br>
                            <input name="aboutus" type="hidden" id="aboutus" class="form-control" value="Other" maxlength="11">
                            <input type="hidden" name="form_data_input" value="register_driver">
                            <button name="Button" type="submit" class="btn btn-lg btn-primary btn-block">Register Driver</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-title">
            <h2>Active Drivers</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>Title</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Nationality</th>

                    </tr>
                </thead>

                <?php
                // Loop through each booking and display its data
                foreach ($result as $row) {
                    if ($row['user_status'] === 'active') {
                        echo "<tr";
                        echo " style='background: rgba(51, 204, 51, 0.2);'";
                        echo ">";
                        echo "<td>";
                        // Edit button opens the editing form for the corresponding driver
                        echo "<div style='display: inline-block; margin-bottom: 5px;'>";
                        echo "<form method='POST' style='display: inline;'>";
                        echo "<input type='hidden' name='edit_user_id' value='" . $row['user_id'] . "'>";
                        echo "<button type='submit' class='btn btn-xs btn-primary btn-block edit-button'>Edit</button>";
                        echo "</form>";
                        echo "</div>";

                        // Button to change user status to 'inactive'
                        if (escapeHtml($row['user_status']) == 'active' && $row['user_id']) {
                            echo "<div style='display: inline-block; margin-bottom: 5px;'>";
                            echo "<form method='POST' style='display: inline;'>";
                            echo "<input type='hidden' name='delete_driver_form' value='" . $row['user_id'] . "'>";
                            echo "<button type='submit' name='delete_driver_request' class='btn btn-xs btn-danger btn-block'>Delete Driver</button>";
                            echo "</form>";
                            echo "</div>";
                        }

                        // Check the booking confirmation before submition on specific booking
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_driver_request']) && $_POST['delete_driver_form'] == $row['user_id']) {
                            echo '<div class="alert alert-warning">';
                            echo '<strong>Delete this driver?</strong><br>';
                            echo '<form method="POST" style="display: inline;">';
                            echo '<input type="hidden" name="delete_driver_id" value="' . $row['user_id'] . '">';
                            echo '<button type="submit" class="btn btn-xs btn-success">Yes</button>';
                            echo '</form>';
                            echo '<form method="POST" style="display: inline;">';
                            echo '<button type="submit" class="btn btn-xs btn-danger">No</button>';
                            echo '</form>';
                            echo '</div>';
                        }
                        echo "</td>";

                        echo "<td>" . escapeHtml($row['title']) . "</td>";
                        echo "<td>" . escapeHtml($row['firstname']) . "</td>";
                        echo "<td>" . escapeHtml($row['lastname']) . "</td>";
                        echo "<td>" . escapeHtml($row['email']) . "</td>";
                        echo "<td>" . escapeHtml($row['phone']) . "</td>";
                        echo "<td>" . escapeHtml($row['nationality']) . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
                </tbody>
            </table>
        </div>


        <?php
        // Check if has any inactive Driver
        $hasInactiveDrivers = false;
        foreach ($result as $row) {
            if ($row['user_status'] === 'inactive') {
                $hasInactiveDrivers = true;
                break;
            }
        }
        ?>

        <?php if ($hasInactiveDrivers) : ?>
            <div class="panel-title">
                <h2>Deleted Drivers</h2>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Actions</th>
                            <th>Title</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Nationality</th>

                        </tr>
                    </thead>

                    <?php
                    // Loop through each booking and display its data
                    foreach ($result as $row) {
                        if ($row['user_status'] === 'inactive') {
                            echo "<tr";
                            echo " style='background: rgba(213, 84, 84, 0.7);'";
                            echo ">";

                            echo "<td>";
                            // Button to change user status to 'active'
                            if (escapeHtml($row['user_status']) == 'inactive' && $row['user_id']) {
                                echo "<div style='display: inline-block; margin-bottom: 5px;'>";
                                echo "<form method='POST' style='display: inline;'>";
                                echo '<input type="hidden" name="recover_driver_id" value="' . $row['user_id'] . '">';
                                echo "<button type='submit' class='btn btn-xs btn-success btn-block'>Recover Driver</button>";
                                echo "</form>";
                                echo "</div>";
                            }
                            echo "</td>";

                            echo "<td>" . escapeHtml($row['title']) . "</td>";
                            echo "<td>" . escapeHtml($row['firstname']) . "</td>";
                            echo "<td>" . escapeHtml($row['lastname']) . "</td>";
                            echo "<td>" . escapeHtml($row['email']) . "</td>";
                            echo "<td>" . escapeHtml($row['phone']) . "</td>";
                            echo "<td>" . escapeHtml($row['nationality']) . "</td>";
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
                        <h2>Editing Driver</h2>
                        <form method="POST">
                            <!-- Input fields for editing the booking -->
                            <input type="hidden" name="user_id" value="<?php echo escapeHtml($edit_user_id['user_id']); ?>">
                            <div class="form-group">
                                <select name="title" id="title" class="form-control" value="<?php echo escapeHtml($edit_user_id['title']); ?>" required>
                                    <option value="">Title:</option>
                                    <option value="Mr. " <?php if ($edit_user_id['title'] === 'Mr. ') echo 'selected'; ?>>Mr.</option>
                                    <option value="Mrs. " <?php if ($edit_user_id['title'] === 'Mrs. ') echo 'selected'; ?>>Mrs.</option>
                                    <option value="Miss. " <?php if ($edit_user_id['title'] === 'Miss. ') echo 'selected'; ?>>Miss.</option>
                                    <option value="Ms. " <?php if ($edit_user_id['title'] === 'Ms. ') echo 'selected'; ?>>Ms.</option>
                                    <option value="Dr. " <?php if ($edit_user_id['title'] === 'Dr. ') echo 'selected'; ?>>Dr.</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>First Name:</label>
                                <input type="text" class="form-control" name="firstname" value="<?php echo escapeHtml($edit_user_id['firstname']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name:</label>
                                <input type="text" class="form-control" name="lastname" value="<?php echo escapeHtml($edit_user_id['lastname']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="text" class="form-control" name="email" value="<?php echo escapeHtml($edit_user_id['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Password:</label>
                                <input type="password" class="form-control" name="passw" value="<?php echo escapeHtml($edit_user_id['passw']); ?>" maxlength="16" required>
                            </div>
                            <div class="form-group">
                                <label>Phone Number:</label>
                                <input type="phone" class="form-control" name="phone" value="<?php echo escapeHtml($edit_user_id['phone']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Nationality:</label>
                                <select name="nationality" id="nationality" class="form-control" required>
                                    <option value="">Select your nationality</option>
                                    <option value="afghan" <?php if ($edit_user_id['nationality'] === 'afghan') echo 'selected'; ?>>Afghan</option>
                                    <option value="albanian" <?php if ($edit_user_id['nationality'] === 'albanian') echo 'selected'; ?>>Albanian</option>
                                    <option value="algerian" <?php if ($edit_user_id['nationality'] === 'algerian') echo 'selected'; ?>>Algerian</option>
                                    <option value="american" <?php if ($edit_user_id['nationality'] === 'american') echo 'selected'; ?>>American</option>
                                    <option value="andorran" <?php if ($edit_user_id['nationality'] === 'andorran') echo 'selected'; ?>>Andorran</option>
                                    <option value="angolan" <?php if ($edit_user_id['nationality'] === 'angolan') echo 'selected'; ?>>Angolan</option>
                                    <option value="antiguans" <?php if ($edit_user_id['nationality'] === 'antiguans') echo 'selected'; ?>>Antiguans</option>
                                    <option value="argentinean" <?php if ($edit_user_id['nationality'] === 'argentinean') echo 'selected'; ?>>Argentinean</option>
                                    <option value="armenian" <?php if ($edit_user_id['nationality'] === 'armenian') echo 'selected'; ?>>Armenian</option>
                                    <option value="australian" <?php if ($edit_user_id['nationality'] === 'australian') echo 'selected'; ?>>Australian</option>
                                    <option value="austrian" <?php if ($edit_user_id['nationality'] === 'austrian') echo 'selected'; ?>>Austrian</option>
                                    <option value="azerbaijani" <?php if ($edit_user_id['nationality'] === 'azerbaijani') echo 'selected'; ?>>Azerbaijani</option>
                                    <option value="bahamian" <?php if ($edit_user_id['nationality'] === 'bahamian') echo 'selected'; ?>>Bahamian</option>
                                    <option value="bahraini" <?php if ($edit_user_id['nationality'] === 'bahraini') echo 'selected'; ?>>Bahraini</option>
                                    <option value="bangladeshi" <?php if ($edit_user_id['nationality'] === 'bangladeshi') echo 'selected'; ?>>Bangladeshi</option>
                                    <option value="barbadian" <?php if ($edit_user_id['nationality'] === 'barbadian') echo 'selected'; ?>>Barbadian</option>
                                    <option value="barbudans" <?php if ($edit_user_id['nationality'] === 'barbudans') echo 'selected'; ?>>Barbudans</option>
                                    <option value="batswana" <?php if ($edit_user_id['nationality'] === 'batswana') echo 'selected'; ?>>Batswana</option>
                                    <option value="belarusian" <?php if ($edit_user_id['nationality'] === 'belarusian') echo 'selected'; ?>>Belarusian</option>
                                    <option value="belgian" <?php if ($edit_user_id['nationality'] === 'belgian') echo 'selected'; ?>>Belgian</option>
                                    <option value="belizean" <?php if ($edit_user_id['nationality'] === 'belizean') echo 'selected'; ?>>Belizean</option>
                                    <option value="beninese" <?php if ($edit_user_id['nationality'] === 'beninese') echo 'selected'; ?>>Beninese</option>
                                    <option value="bhutanese" <?php if ($edit_user_id['nationality'] === 'bhutanese') echo 'selected'; ?>>Bhutanese</option>
                                    <option value="bolivian" <?php if ($edit_user_id['nationality'] === 'bolivian') echo 'selected'; ?>>Bolivian</option>
                                    <option value="bosnian" <?php if ($edit_user_id['nationality'] === 'bosnian') echo 'selected'; ?>>Bosnian</option>
                                    <option value="brazilian" <?php if ($edit_user_id['nationality'] === 'brazilian') echo 'selected'; ?>>Brazilian</option>
                                    <option value="british" <?php if ($edit_user_id['nationality'] === 'british') echo 'selected'; ?>>British</option>
                                    <option value="bruneian" <?php if ($edit_user_id['nationality'] === 'bruneian') echo 'selected'; ?>>Bruneian</option>
                                    <option value="bulgarian" <?php if ($edit_user_id['nationality'] === 'bulgarian') echo 'selected'; ?>>Bulgarian</option>
                                    <option value="burkinabe" <?php if ($edit_user_id['nationality'] === 'burkinabe') echo 'selected'; ?>>Burkinabe</option>
                                    <option value="burmese" <?php if ($edit_user_id['nationality'] === 'burmese') echo 'selected'; ?>>Burmese</option>
                                    <option value="burundian" <?php if ($edit_user_id['nationality'] === 'burundian') echo 'selected'; ?>>Burundian</option>
                                    <option value="cambodian" <?php if ($edit_user_id['nationality'] === 'cambodian') echo 'selected'; ?>>Cambodian</option>
                                    <option value="cameroonian" <?php if ($edit_user_id['nationality'] === 'cameroonian') echo 'selected'; ?>>Cameroonian</option>
                                    <option value="canadian" <?php if ($edit_user_id['nationality'] === 'canadian') echo 'selected'; ?>>Canadian</option>
                                    <option value="cape verdean" <?php if ($edit_user_id['nationality'] === 'cape verdean') echo 'selected'; ?>>Cape Verdean</option>
                                    <option value="central african" <?php if ($edit_user_id['nationality'] === 'central african') echo 'selected'; ?>>Central African</option>
                                    <option value="chadian" <?php if ($edit_user_id['nationality'] === 'chadian') echo 'selected'; ?>>Chadian</option>
                                    <option value="chilean" <?php if ($edit_user_id['nationality'] === 'chilean') echo 'selected'; ?>>Chilean</option>
                                    <option value="chinese" <?php if ($edit_user_id['nationality'] === 'chinese') echo 'selected'; ?>>Chinese</option>
                                    <option value="colombian" <?php if ($edit_user_id['nationality'] === 'colombian') echo 'selected'; ?>>Colombian</option>
                                    <option value="comoran" <?php if ($edit_user_id['nationality'] === 'comoran') echo 'selected'; ?>>Comoran</option>
                                    <option value="congolese" <?php if ($edit_user_id['nationality'] === 'congolese') echo 'selected'; ?>>Congolese</option>
                                    <option value="costa rican" <?php if ($edit_user_id['nationality'] === 'costa rican') echo 'selected'; ?>>Costa Rican</option>
                                    <option value="croatian" <?php if ($edit_user_id['nationality'] === 'croatian') echo 'selected'; ?>>Croatian</option>
                                    <option value="cuban" <?php if ($edit_user_id['nationality'] === 'cuban') echo 'selected'; ?>>Cuban</option>
                                    <option value="cypriot" <?php if ($edit_user_id['nationality'] === 'cypriot') echo 'selected'; ?>>Cypriot</option>
                                    <option value="czech" <?php if ($edit_user_id['nationality'] === 'czech') echo 'selected'; ?>>Czech</option>
                                    <option value="danish" <?php if ($edit_user_id['nationality'] === 'danish') echo 'selected'; ?>>Danish</option>
                                    <option value="djibouti" <?php if ($edit_user_id['nationality'] === 'djibouti') echo 'selected'; ?>>Djibouti</option>
                                    <option value="dominican" <?php if ($edit_user_id['nationality'] === 'dominican') echo 'selected'; ?>>Dominican</option>
                                    <option value="dutch" <?php if ($edit_user_id['nationality'] === 'dutch') echo 'selected'; ?>>Dutch</option>
                                    <option value="east timorese" <?php if ($edit_user_id['nationality'] === 'east timorese') echo 'selected'; ?>>East Timorese</option>
                                    <option value="ecuadorean" <?php if ($edit_user_id['nationality'] === 'ecuadorean') echo 'selected'; ?>>Ecuadorean</option>
                                    <option value="egyptian" <?php if ($edit_user_id['nationality'] === 'egyptian') echo 'selected'; ?>>Egyptian</option>
                                    <option value="emirian" <?php if ($edit_user_id['nationality'] === 'emirian') echo 'selected'; ?>>Emirian</option>
                                    <option value="equatorial guinean" <?php if ($edit_user_id['nationality'] === 'equatorial guinean') echo 'selected'; ?>>Equatorial Guinean</option>
                                    <option value="eritrean" <?php if ($edit_user_id['nationality'] === 'eritrean') echo 'selected'; ?>>Eritrean</option>
                                    <option value="estonian" <?php if ($edit_user_id['nationality'] === 'estonian') echo 'selected'; ?>>Estonian</option>
                                    <option value="ethiopian" <?php if ($edit_user_id['nationality'] === 'ethiopian') echo 'selected'; ?>>Ethiopian</option>
                                    <option value="fijian" <?php if ($edit_user_id['nationality'] === 'fijian') echo 'selected'; ?>>Fijian</option>
                                    <option value="filipino" <?php if ($edit_user_id['nationality'] === 'filipino') echo 'selected'; ?>>Filipino</option>
                                    <option value="finnish" <?php if ($edit_user_id['nationality'] === 'finnish') echo 'selected'; ?>>Finnish</option>
                                    <option value="french" <?php if ($edit_user_id['nationality'] === 'french') echo 'selected'; ?>>French</option>
                                    <option value="gabonese" <?php if ($edit_user_id['nationality'] === 'gabonese') echo 'selected'; ?>>Gabonese</option>
                                    <option value="gambian" <?php if ($edit_user_id['nationality'] === 'gambian') echo 'selected'; ?>>Gambian</option>
                                    <option value="georgian" <?php if ($edit_user_id['nationality'] === 'georgian') echo 'selected'; ?>>Georgian</option>
                                    <option value="german" <?php if ($edit_user_id['nationality'] === 'german') echo 'selected'; ?>>German</option>
                                    <option value="ghanaian" <?php if ($edit_user_id['nationality'] === 'ghanaian') echo 'selected'; ?>>Ghanaian</option>
                                    <option value="greek" <?php if ($edit_user_id['nationality'] === 'greek') echo 'selected'; ?>>Greek</option>
                                    <option value="grenadian" <?php if ($edit_user_id['nationality'] === 'grenadian') echo 'selected'; ?>>Grenadian</option>
                                    <option value="guatemalan" <?php if ($edit_user_id['nationality'] === 'guatemalan') echo 'selected'; ?>>Guatemalan</option>
                                    <option value="guinea-bissauan" <?php if ($edit_user_id['nationality'] === 'guinea-bissauan') echo 'selected'; ?>>Guinea-Bissauan</option>
                                    <option value="guinean" <?php if ($edit_user_id['nationality'] === 'guinean') echo 'selected'; ?>>Guinean</option>
                                    <option value="guyanese" <?php if ($edit_user_id['nationality'] === 'guyanese') echo 'selected'; ?>>Guyanese</option>
                                    <option value="haitian" <?php if ($edit_user_id['nationality'] === 'haitian') echo 'selected'; ?>>Haitian</option>
                                    <option value="herzegovinian" <?php if ($edit_user_id['nationality'] === 'herzegovinian') echo 'selected'; ?>>Herzegovinian</option>
                                    <option value="honduran" <?php if ($edit_user_id['nationality'] === 'honduran') echo 'selected'; ?>>Honduran</option>
                                    <option value="hungarian" <?php if ($edit_user_id['nationality'] === 'hungarian') echo 'selected'; ?>>Hungarian</option>
                                    <option value="icelander" <?php if ($edit_user_id['nationality'] === 'icelander') echo 'selected'; ?>>Icelander</option>
                                    <option value="indian" <?php if ($edit_user_id['nationality'] === 'indian') echo 'selected'; ?>>Indian</option>
                                    <option value="indonesian" <?php if ($edit_user_id['nationality'] === 'indonesian') echo 'selected'; ?>>Indonesian</option>
                                    <option value="iranian" <?php if ($edit_user_id['nationality'] === 'iranian') echo 'selected'; ?>>Iranian</option>
                                    <option value="iraqi" <?php if ($edit_user_id['nationality'] === 'iraqi') echo 'selected'; ?>>Iraqi</option>
                                    <option value="irish" <?php if ($edit_user_id['nationality'] === 'irish') echo 'selected'; ?>>Irish</option>
                                    <option value="israeli" <?php if ($edit_user_id['nationality'] === 'israeli') echo 'selected'; ?>>Israeli</option>
                                    <option value="italian" <?php if ($edit_user_id['nationality'] === 'italian') echo 'selected'; ?>>Italian</option>
                                    <option value="ivorian" <?php if ($edit_user_id['nationality'] === 'ivorian') echo 'selected'; ?>>Ivorian</option>
                                    <option value="jamaican" <?php if ($edit_user_id['nationality'] === 'jamaican') echo 'selected'; ?>>Jamaican</option>
                                    <option value="japanese" <?php if ($edit_user_id['nationality'] === 'japanese') echo 'selected'; ?>>Japanese</option>
                                    <option value="jordanian" <?php if ($edit_user_id['nationality'] === 'jordanian') echo 'selected'; ?>>Jordanian</option>
                                    <option value="kazakhstani" <?php if ($edit_user_id['nationality'] === 'kazakhstani') echo 'selected'; ?>>Kazakhstani</option>
                                    <option value="kenyan" <?php if ($edit_user_id['nationality'] === 'kenyan') echo 'selected'; ?>>Kenyan</option>
                                    <option value="kittian and nevisian" <?php if ($edit_user_id['nationality'] === 'kittian and nevisian') echo 'selected'; ?>>Kittian and Nevisian</option>
                                    <option value="kuwaiti" <?php if ($edit_user_id['nationality'] === 'kuwaiti') echo 'selected'; ?>>Kuwaiti</option>
                                    <option value="kyrgyz" <?php if ($edit_user_id['nationality'] === 'kyrgyz') echo 'selected'; ?>>Kyrgyz</option>
                                    <option value="laotian" <?php if ($edit_user_id['nationality'] === 'laotian') echo 'selected'; ?>>Laotian</option>
                                    <option value="latvian" <?php if ($edit_user_id['nationality'] === 'latvian') echo 'selected'; ?>>Latvian</option>
                                    <option value="lebanese" <?php if ($edit_user_id['nationality'] === 'lebanese') echo 'selected'; ?>>Lebanese</option>
                                    <option value="liberian" <?php if ($edit_user_id['nationality'] === 'liberian') echo 'selected'; ?>>Liberian</option>
                                    <option value="libyan" <?php if ($edit_user_id['nationality'] === 'libyan') echo 'selected'; ?>>Libyan</option>
                                    <option value="liechtensteiner" <?php if ($edit_user_id['nationality'] === 'liechtensteiner') echo 'selected'; ?>>Liechtensteiner</option>
                                    <option value="lithuanian" <?php if ($edit_user_id['nationality'] === 'lithuanian') echo 'selected'; ?>>Lithuanian</option>
                                    <option value="luxembourger" <?php if ($edit_user_id['nationality'] === 'luxembourger') echo 'selected'; ?>>Luxembourger</option>
                                    <option value="macedonian" <?php if ($edit_user_id['nationality'] === 'macedonian') echo 'selected'; ?>>Macedonian</option>
                                    <option value="malagasy" <?php if ($edit_user_id['nationality'] === 'malagasy') echo 'selected'; ?>>Malagasy</option>
                                    <option value="malawian" <?php if ($edit_user_id['nationality'] === 'malawian') echo 'selected'; ?>>Malawian</option>
                                    <option value="malaysian" <?php if ($edit_user_id['nationality'] === 'malaysian') echo 'selected'; ?>>Malaysian</option>
                                    <option value="maldivan" <?php if ($edit_user_id['nationality'] === 'maldivan') echo 'selected'; ?>>Maldivan</option>
                                    <option value="malian" <?php if ($edit_user_id['nationality'] === 'malian') echo 'selected'; ?>>Malian</option>
                                    <option value="maltese" <?php if ($edit_user_id['nationality'] === 'maltese') echo 'selected'; ?>>Maltese</option>
                                    <option value="marshallese" <?php if ($edit_user_id['nationality'] === 'marshallese') echo 'selected'; ?>>Marshallese</option>
                                    <option value="mauritanian" <?php if ($edit_user_id['nationality'] === 'mauritanian') echo 'selected'; ?>>Mauritanian</option>
                                    <option value="mauritian" <?php if ($edit_user_id['nationality'] === 'mauritian') echo 'selected'; ?>>Mauritian</option>
                                    <option value="mexican" <?php if ($edit_user_id['nationality'] === 'mexican') echo 'selected'; ?>>Mexican</option>
                                    <option value="micronesian" <?php if ($edit_user_id['nationality'] === 'micronesian') echo 'selected'; ?>>Micronesian</option>
                                    <option value="moldovan" <?php if ($edit_user_id['nationality'] === 'moldovan') echo 'selected'; ?>>Moldovan</option>
                                    <option value="monacan" <?php if ($edit_user_id['nationality'] === 'monacan') echo 'selected'; ?>>Monacan</option>
                                    <option value="mongolian" <?php if ($edit_user_id['nationality'] === 'mongolian') echo 'selected'; ?>>Mongolian</option>
                                    <option value="moroccan" <?php if ($edit_user_id['nationality'] === 'moroccan') echo 'selected'; ?>>Moroccan</option>
                                    <option value="mosotho" <?php if ($edit_user_id['nationality'] === 'mosotho') echo 'selected'; ?>>Mosotho</option>
                                    <option value="motswana" <?php if ($edit_user_id['nationality'] === 'motswana') echo 'selected'; ?>>Motswana</option>
                                    <option value="mozambican" <?php if ($edit_user_id['nationality'] === 'mozambican') echo 'selected'; ?>>Mozambican</option>
                                    <option value="namibian" <?php if ($edit_user_id['nationality'] === 'namibian') echo 'selected'; ?>>Namibian</option>
                                    <option value="nauruan" <?php if ($edit_user_id['nationality'] === 'nauruan') echo 'selected'; ?>>Nauruan</option>
                                    <option value="nepalese" <?php if ($edit_user_id['nationality'] === 'nepalese') echo 'selected'; ?>>Nepalese</option>
                                    <option value="new zealander" <?php if ($edit_user_id['nationality'] === 'new zealander') echo 'selected'; ?>>New Zealander</option>
                                    <option value="ni-vanuatu" <?php if ($edit_user_id['nationality'] === 'ni-vanuatu') echo 'selected'; ?>>Ni-Vanuatu</option>
                                    <option value="nicaraguan" <?php if ($edit_user_id['nationality'] === 'nicaraguan') echo 'selected'; ?>>Nicaraguan</option>
                                    <option value="nigerian" <?php if ($edit_user_id['nationality'] === 'nigerian') echo 'selected'; ?>>Nigerian</option>
                                    <option value="nigerien" <?php if ($edit_user_id['nationality'] === 'nigerien') echo 'selected'; ?>>Nigerien</option>
                                    <option value="north korean" <?php if ($edit_user_id['nationality'] === 'north korean') echo 'selected'; ?>>North Korean</option>
                                    <option value="northern irish" <?php if ($edit_user_id['nationality'] === 'northern irish') echo 'selected'; ?>>Northern Irish</option>
                                    <option value="norwegian" <?php if ($edit_user_id['nationality'] === 'norwegian') echo 'selected'; ?>>Norwegian</option>
                                    <option value="omani" <?php if ($edit_user_id['nationality'] === 'omani') echo 'selected'; ?>>Omani</option>
                                    <option value="pakistani" <?php if ($edit_user_id['nationality'] === 'pakistani') echo 'selected'; ?>>Pakistani</option>
                                    <option value="palauan" <?php if ($edit_user_id['nationality'] === 'palauan') echo 'selected'; ?>>Palauan</option>
                                    <option value="panamanian" <?php if ($edit_user_id['nationality'] === 'panamanian') echo 'selected'; ?>>Panamanian</option>
                                    <option value="papua new guinean" <?php if ($edit_user_id['nationality'] === 'papua new guinean') echo 'selected'; ?>>Papua New Guinean</option>
                                    <option value="paraguayan" <?php if ($edit_user_id['nationality'] === 'paraguayan') echo 'selected'; ?>>Paraguayan</option>
                                    <option value="peruvian" <?php if ($edit_user_id['nationality'] === 'peruvian') echo 'selected'; ?>>Peruvian</option>
                                    <option value="polish" <?php if ($edit_user_id['nationality'] === 'polish') echo 'selected'; ?>>Polish</option>
                                    <option value="portuguese" <?php if ($edit_user_id['nationality'] === 'portuguese') echo 'selected'; ?>>Portuguese</option>
                                    <option value="qatari" <?php if ($edit_user_id['nationality'] === 'qatari') echo 'selected'; ?>>Qatari</option>
                                    <option value="romanian" <?php if ($edit_user_id['nationality'] === 'romanian') echo 'selected'; ?>>Romanian</option>
                                    <option value="russian" <?php if ($edit_user_id['nationality'] === 'russian') echo 'selected'; ?>>Russian</option>
                                    <option value="rwandan" <?php if ($edit_user_id['nationality'] === 'rwandan') echo 'selected'; ?>>Rwandan</option>
                                    <option value="saint lucian" <?php if ($edit_user_id['nationality'] === 'saint lucian') echo 'selected'; ?>>Saint Lucian</option>
                                    <option value="salvadoran" <?php if ($edit_user_id['nationality'] === 'salvadoran') echo 'selected'; ?>>Salvadoran</option>
                                    <option value="samoan" <?php if ($edit_user_id['nationality'] === 'samoan') echo 'selected'; ?>>Samoan</option>
                                    <option value="san marinese" <?php if ($edit_user_id['nationality'] === 'san marinese') echo 'selected'; ?>>San Marinese</option>
                                    <option value="sao tomean" <?php if ($edit_user_id['nationality'] === 'sao tomean') echo 'selected'; ?>>Sao Tomean</option>
                                    <option value="saudi" <?php if ($edit_user_id['nationality'] === 'saudi') echo 'selected'; ?>>Saudi</option>
                                    <option value="scottish" <?php if ($edit_user_id['nationality'] === 'scottish') echo 'selected'; ?>>Scottish</option>
                                    <option value="senegalese" <?php if ($edit_user_id['nationality'] === 'senegalese') echo 'selected'; ?>>Senegalese</option>
                                    <option value="serbian" <?php if ($edit_user_id['nationality'] === 'serbian') echo 'selected'; ?>>Serbian</option>
                                    <option value="seychellois" <?php if ($edit_user_id['nationality'] === 'seychellois') echo 'selected'; ?>>Seychellois</option>
                                    <option value="sierra leonean" <?php if ($edit_user_id['nationality'] === 'sierra leonean') echo 'selected'; ?>>Sierra Leonean</option>
                                    <option value="singaporean" <?php if ($edit_user_id['nationality'] === 'singaporean') echo 'selected'; ?>>Singaporean</option>
                                    <option value="slovakian" <?php if ($edit_user_id['nationality'] === 'slovakian') echo 'selected'; ?>>Slovakian</option>
                                    <option value="slovenian" <?php if ($edit_user_id['nationality'] === 'slovenian') echo 'selected'; ?>>Slovenian</option>
                                    <option value="solomon islander" <?php if ($edit_user_id['nationality'] === 'solomon islander') echo 'selected'; ?>>Solomon Islander</option>
                                    <option value="somali" <?php if ($edit_user_id['nationality'] === 'somali') echo 'selected'; ?>>Somali</option>
                                    <option value="south african" <?php if ($edit_user_id['nationality'] === 'south african') echo 'selected'; ?>>South African</option>
                                    <option value="south korean" <?php if ($edit_user_id['nationality'] === 'south korean') echo 'selected'; ?>>South Korean</option>
                                    <option value="south sudanese" <?php if ($edit_user_id['nationality'] === 'south sudanese') echo 'selected'; ?>>South Sudanese</option>
                                    <option value="spanish" <?php if ($edit_user_id['nationality'] === 'spanish') echo 'selected'; ?>>Spanish</option>
                                    <option value="sri lankan" <?php if ($edit_user_id['nationality'] === 'sri lankan') echo 'selected'; ?>>Sri Lankan</option>
                                    <option value="sudanese" <?php if ($edit_user_id['nationality'] === 'sudanese') echo 'selected'; ?>>Sudanese</option>
                                    <option value="surinamer" <?php if ($edit_user_id['nationality'] === 'surinamer') echo 'selected'; ?>>Surinamer</option>
                                    <option value="swazi" <?php if ($edit_user_id['nationality'] === 'swazi') echo 'selected'; ?>>Swazi</option>
                                    <option value="swedish" <?php if ($edit_user_id['nationality'] === 'swedish') echo 'selected'; ?>>Swedish</option>
                                    <option value="swiss" <?php if ($edit_user_id['nationality'] === 'swiss') echo 'selected'; ?>>Swiss</option>
                                    <option value="syrian" <?php if ($edit_user_id['nationality'] === 'syrian') echo 'selected'; ?>>Syrian</option>
                                    <option value="taiwanese" <?php if ($edit_user_id['nationality'] === 'taiwanese') echo 'selected'; ?>>Taiwanese</option>
                                    <option value="tajik" <?php if ($edit_user_id['nationality'] === 'tajik') echo 'selected'; ?>>Tajik</option>
                                    <option value="tanzanian" <?php if ($edit_user_id['nationality'] === 'tanzanian') echo 'selected'; ?>>Tanzanian</option>
                                    <option value="thai" <?php if ($edit_user_id['nationality'] === 'thai') echo 'selected'; ?>>Thai</option>
                                    <option value="togolese" <?php if ($edit_user_id['nationality'] === 'togolese') echo 'selected'; ?>>Togolese</option>
                                    <option value="tongan" <?php if ($edit_user_id['nationality'] === 'tongan') echo 'selected'; ?>>Tongan</option>
                                    <option value="trinidadian or tobagonian" <?php if ($edit_user_id['nationality'] === 'trinidadian or tobagonian') echo 'selected'; ?>>Trinidadian or Tobagonian</option>
                                    <option value="tunisian" <?php if ($edit_user_id['nationality'] === 'tunisian') echo 'selected'; ?>>Tunisian</option>
                                    <option value="turkish" <?php if ($edit_user_id['nationality'] === 'turkish') echo 'selected'; ?>>Turkish</option>
                                    <option value="turkmen" <?php if ($edit_user_id['nationality'] === 'turkmen') echo 'selected'; ?>>Turkmen</option>
                                    <option value="tuvaluan" <?php if ($edit_user_id['nationality'] === 'tuvaluan') echo 'selected'; ?>>Tuvaluan</option>
                                    <option value="ugandan" <?php if ($edit_user_id['nationality'] === 'ugandan') echo 'selected'; ?>>Ugandan</option>
                                    <option value="ukrainian" <?php if ($edit_user_id['nationality'] === 'ukrainian') echo 'selected'; ?>>Ukrainian</option>
                                    <option value="uruguayan" <?php if ($edit_user_id['nationality'] === 'uruguayan') echo 'selected'; ?>>Uruguayan</option>
                                    <option value="uzbekistani" <?php if ($edit_user_id['nationality'] === 'uzbekistani') echo 'selected'; ?>>Uzbekistani</option>
                                    <option value="vanuatuan" <?php if ($edit_user_id['nationality'] === 'vanuatuan') echo 'selected'; ?>>Vanuatuan</option>
                                    <option value="vatican citizen" <?php if ($edit_user_id['nationality'] === 'vatican citizen') echo 'selected'; ?>>Vatican citizen</option>
                                    <option value="venezuelan" <?php if ($edit_user_id['nationality'] === 'venezuelan') echo 'selected'; ?>>Venezuelan</option>
                                    <option value="vietnamese" <?php if ($edit_user_id['nationality'] === 'vietnamese') echo 'selected'; ?>>Vietnamese</option>
                                    <option value="welsh" <?php if ($edit_user_id['nationality'] === 'welsh') echo 'selected'; ?>>Welsh</option>
                                    <option value="yemenite" <?php if ($edit_user_id['nationality'] === 'yemenite') echo 'selected'; ?>>Yemenite</option>
                                    <option value="zambian" <?php if ($edit_user_id['nationality'] === 'zambian') echo 'selected'; ?>>Zambian</option>
                                    <option value="zimbabwean" <?php if ($edit_user_id['nationality'] === 'zimbabwean') echo 'selected'; ?>>Zimbabwean</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-lg btn-primary btn-block">Save Changes</button>
                            <a href="register_drivers.php" class="btn btn-lg btn-danger btn-block">Cancel</a>
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