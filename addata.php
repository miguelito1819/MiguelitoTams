<!-- add_data.php -->
<?php
require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize($_POST['name']);
    $age = sanitize($_POST['age']);
    $address = sanitize($_POST['address']);
    $gender = sanitize($_POST['gender']);
    $status = sanitize($_POST['status']);

    // Insert new record into the database
    $sql = "INSERT INTO resident (name, age, address, gender, status) VALUES ('$name', '$age', '$address', '$gender', '$status')";
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
