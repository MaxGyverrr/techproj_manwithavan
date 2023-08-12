<?php
// Start and handle the $_Session
require_once 'functions/session_protection.php';

// database conection
require 'functions/connection.php';

header("Content-Type: text/html; charset=UTF-8", true);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

if (isset($_POST['form_data_input']) && $_POST['form_data_input'] === "register_user") {
    try {
        // receive form data
        $user_id = protection($_POST['user_id']);
        $title = protection($_POST['title']);
        $firstname = ucfirst(protection($_POST['firstname'])); //ucfirst is to force the input values start with a capital letter
        $lastname = ucfirst(protection($_POST['lastname']));
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
    echo "<script>alert('Your account has been successfully created!');window.location='index.php';</script>";
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
                        <form method="POST" id="form_input" action="register.php" name="form_input" onsubmit="return validateForm()">

                            <h2 class="form-login-heading">Please, enter your details</h2>

                            <input name="user_id" type="hidden" id="user_id" class="form-control" value="0">
                            <input name="usertype" type="hidden" id="usertype" class="form-control" value="customer">
                            <input name="user_status" type="hidden" id="usertype" class="form-control" value="active">
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
                                <input name="email" type="email" id="email" class="form-control" placeholder="Email" maxlength="100" required>
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
                            <div class="col-md-10">
                                <select name="aboutus" id="aboutus" class="form-control" required>
                                    <option value="">How did you hear about us?</option>
                                    <option value="Instagram">Instagram</option>
                                    <option value="Facebook">Facebook</option>
                                    <option value="Email">Email</option>
                                    <option value="Google Search">Google Search</option>
                                    <option value="Radio">Radio</option>
                                    <option value="Television">Television</option>
                                    <option value="Newspaper">Newspaper</option>
                                    <option value="Friend Recommendation">Friend Recommendation</option>
                                    <option value="Outdoor Banner">Outdoor Banner</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            </br></br></br>
                            <input type="hidden" name="form_data_input" value="register_user">
                            <button name="Button" type="submit" class="btn btn-lg btn-primary btn-block">Register</button>


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

</body>

</html>