<?php
session_start();

include_once "views/_header.php";
include_once "views/_navbar.php";
include_once 'classes/utils.class.php';
include_once 'classes/order.class.php';
include_once 'classes/auth.class.php';
include_once 'classes/product.class.php';
include_once "classes/vars.php";

$product = new Products();
$utils = new Utils();
$auth = new Auth();
$order = new Orders();

$is_loggedIn = $utils->isLoggedIn();

if(!$is_loggedIn){
    header("Location: index.php");
}

$firstName = $lastName = $email = $address = "";
$firstName_err = $lastName_err = $email_err = $address_err = "";

$username = $_SESSION["username"];
$user = $auth->getUserByUsername($username);

if (isset($_POST['firstName'])) {
    $firstName = htmlspecialchars($_POST["firstName"]);
    $email = htmlspecialchars($_POST["email"]);
    $lastName = htmlspecialchars($_POST["lastName"]);
    $address = htmlspecialchars($_POST["address"]);
    $country = $_POST["country"];
    $state = htmlspecialchars($_POST["state"]);
    $zip = htmlspecialchars($_POST["zip"]);


    if (empty($firstName)) {
        $firstName_err = "You must enter a firstName";
    } elseif (strlen($firstName) < 2 or strlen($firstName) > 95) {
        $firstName_err = "firstName must be at least 2, maximum 95 characters";
    }

    if (empty($email)) {
        $email_err = "You must provide an email address";
    }

    if (empty($lastName)) {
        $lastName_err = "You must enter a password";
    } elseif (strlen($lastName) < 2) {
        $lastName_err = "lastName should be minimum 2 characters";
    }

    if (empty($address)) {
        $address_err = "You must enter address";
    }


    if (empty($firstName_err) && empty($email_err) && empty($lastName_err) && empty($address_err)) {
        if ($order->createAddress($firstName, $lastName, $email, $address, $country, $state, $zip, $user->id)) {
            header('Location: cart.php');
            exit;
        } else {
            echo "Error occurred while adding new address.";
        }
    }
}
?>


<div class="container mt-5">
    <h4 class="mb-3">Billing address</h4>
    <form class="needs-validation" method="POST" action="address-form.php" novalidate="">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="firstName">First name</label>
                <input type="text" class="form-control" id="firstName" name="firstName" placeholder="" value="" required="">
                <div class="invalid-feedback">
                    Valid first name is required.
                    <?php if (!empty($firstName_err)) { ?>
                    <span class="text-danger"><?php echo $firstName_err; ?></span>
                <?php } ?>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="lastName">Last name</label>
                <input type="text" class="form-control" id="lastName" name="lastName" placeholder="" value="" required="">
                <div class="invalid-feedback">
                    Valid last name is required.
                    <?php if (!empty($lastName_err)) { ?>
                    <span class="text-danger"><?php echo $lastName_err; ?></span>
                <?php } ?>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="email">Email </label>
            <input type="email" class="form-control" name="email" id="email" placeholder="you@example.com">
            <div class="invalid-feedback">
                Please enter a valid email address for shipping updates.
                <?php if (!empty($email_err)) { ?>
                    <span class="text-danger"><?php echo $email_err; ?></span>
                <?php } ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="address">Address</label>
            <textarea required="" id="address" name="address" class="form-control" rows="3"></textarea>
            <div class="invalid-feedback">
                Please enter your shipping address.
                <?php if (!empty($address_err)) { ?>
                    <span class="text-danger"><?php echo $address_err; ?></span>
                <?php } ?>
            </div>
        </div>


        <div class="row">
            <div class="col-md-5 mb-3">
                <label for="country">Country</label>
                <select class="custom-select d-block w-100" id="country" required="" name="country">
                    <?php foreach ($countries as $code => $country) : ?>
                        <option value="<?php echo $code; ?>"><?php echo $country; ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">
                    Please select a valid country.
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="state">State</label>
                <input type="text" class="form-control" id="state" placeholder="" name="state" required="">
                <div class="invalid-feedback">
                    Please provide a valid state.
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <label for="zip">Zip</label>
                <input type="number" class="form-control" id="zip" placeholder="" name="zip" required="">
                <div class="invalid-feedback">
                    Zip code required.
                </div>
            </div>
        </div>
        <input type="hidden" name="user_id" value="<?php echo $user->id ?>" required>

        <input class="btn btn-primary btn-lg btn-block mt-3" value="Add Address" type="submit">
    </form>
</div>