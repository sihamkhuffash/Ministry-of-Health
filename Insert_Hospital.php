<!DOCTYPE html>
<html lang="">
<body>

<?php
include 'DB.php';
global $conn;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Hospital_name = isset($_POST['Hname']) ? $_POST['Hname'] : '';
    $Hospital_address = isset($_POST['Hlocation']) ? $_POST['Hlocation'] : '';
    $Hospital_contact = isset($_POST['Hphone']) ? $_POST['Hphone'] : '';

    // Prepare the SQL statement
    $sql = "INSERT INTO `hospitals`(`hospital_name`, `hospital_location`, `hospital_phone_num`)
            VALUES (?, ?, ?)";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $Hospital_name, $Hospital_address, $Hospital_contact);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<h1>Hospital information to insert</h1>

<form method="POST" action="">
    <label for="Hname">Hospital name:</label>
    <input type="text" id="Hname" name="Hname" required><br><br>
    <label for="Hlocation">Hospital location:</label>
    <input type="text" id="Hlocation" name="Hlocation" required><br><br>
    <label for="Hphone">Hospital phone:</label>
    <input type="text" id="Hphone" name="Hphone" required><br><br>
    <input type="submit" value="Submit">
</form>

</body>
</html>
