<?php

include_once(dirname(__FILE__) .  '/../../includes/config.php');
include_once(dirname(__FILE__) .  '/../functions/validator.php');
include_once(dirname(__FILE__) .  '/../functions/client.php');
include_once(dirname(__FILE__) .  '/../functions/main.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['submitProfile'])) {

        $conn = connect();

        check($_POST, [
            'dob' => ['required' => TRUE],
            'marital-state' => ['required' => TRUE],
            'gender' => ['required' => TRUE],
            'state' => ['required' => TRUE],
            'city' => ['required' => TRUE],
            'street' => ['required' => TRUE],
            'postal-code' => ['required' => TRUE],
        ]);
        if (!passed()) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        if (!isset($_SESSION["id"])) {
            forceLogoutWithMsg();
        }

        $userId = $_SESSION["id"];
        $agent = assignAgent();
        $agentId = $agent ? $agent["id"] : null;

        $state = htmlspecialchars($_POST['state']);
        $city = htmlspecialchars($_POST['city']);
        $street = htmlspecialchars($_POST['street']);
        $postalCode = htmlspecialchars($_POST['postal-code']);
        $dob = $_POST['dob'];
        $maritalState = $_POST['marital-state'];
        $gender = $_POST['gender'];

        $createQuery = "INSERT INTO `client` (`user_id`, `agent_id`, `state`, `city`, `street`, `postal_code`, `dob`, `marital_state`, `gender`) VALUES ('$userId', '$agentId', '$state', '$city', '$street', '$postalCode', '$dob', '$maritalState', '$gender')";
        $result = readQuery($conn, $createQuery);
        
        if ($result) {
            $last_id = $conn->insert_id;
            addError("Your Client Profile successfully created.", 'success');
            header('Location: ' . BASE_URL . '/index.php');
            exit();
        } else {
            addError(mysqli_error($conn), 'danger');
        }
    }
}

addError("403: Access denied!", 'danger');
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit(); 