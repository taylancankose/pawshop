<?php
session_start();

include_once "views/_header.php";
include_once "views/_navbar.php";
include_once "classes/vars.php";

$product = new Products();

$is_admin = $product->isAdmin();

if(!$is_admin){
    header("Location: index.php");
}

$firstName = $lastName = $email = $address = "";
$firstName_err = $lastName_err = $email_err = $address_err = "";

$username = $_SESSION["username"];
$user = $product->getUserByUsername($username);

if (isset($_POST['firstName'])) {
    $firstName = $_POST["firstName"];
    $email = $_POST["email"];
    $lastName = $_POST["lastName"];
    $address = $_POST["address"];
    $country = $_POST["country"];
    $state = $_POST["state"];
    $zip = $_POST["zip"];


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
        if ($product->createAddress($firstName, $lastName, $email, $address, $country, $state, $zip, $user->id)) {
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
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="lastName">Last name</label>
                <input type="text" class="form-control" id="lastName" name="lastName" placeholder="" value="" required="">
                <div class="invalid-feedback">
                    Valid last name is required.
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="email">Email </label>
            <input type="email" class="form-control" name="email" id="email" placeholder="you@example.com">
            <div class="invalid-feedback">
                Please enter a valid email address for shipping updates.
            </div>
        </div>

        <div class="mb-3">
            <label for="address">Address</label>
            <textarea required="" id="address" name="address" class="form-control" rows="3"></textarea>
            <div class="invalid-feedback">
                Please enter your shipping address.
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