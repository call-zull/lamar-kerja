<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture and sanitize input data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Perform validation checks
    if (!$email) {
        die('Invalid email');
    }

    // Store in database
    // Assuming a connection to the database has been established
    $stmt = $db->prepare("INSERT INTO users (name, id, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $id, $email, $password, $role);

    if ($stmt->execute()) {
        echo "Registration successful";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
