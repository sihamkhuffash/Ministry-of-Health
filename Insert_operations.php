<!DOCTYPE html>
<html lang="">
<body>

<?php
include 'DB.php';
global $conn;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hospital_ID = isset($_POST['H_ID']) ? $_POST['H_ID'] : '';
    $small_operation_male = isset($_POST['small_males']) ? $_POST['small_males'] : '';
    $large_operation_male = isset($_POST['large_males']) ? $_POST['large_males'] : '';
    $small_operation_female = isset($_POST['small_females']) ? $_POST['small_females'] : '';
    $large_operation_female = isset($_POST['large_females']) ? $_POST['large_females'] : '';

    // Prepare the SQL statement
    $sql = "INSERT INTO `surgical operation` (`hospital_ID`, `small_males`, `large_male`, `small_female`, `large_female`)
        VALUES (?, ?, ?, ?, ?)";


    // Prepare and bind
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param('iiiii', $hospital_ID,  $small_operation_male, $large_operation_male, $small_operation_female, $large_operation_female);
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

<h1>Operation information to insert</h1>

<form method="POST" action="">
    <label for="H_ID">Hospital ID:</label>
    <input type="number" id="H_ID" name="H_ID" required><br><br>
    <label for="small_males">Small operations for males:</label>
    <input type="number" id="small_males" name="small_males" required><br><br>
    <label for="large_males">Large operations for Males:</label>
    <input type="number" id="large_males" name="large_males" required><br><br>
    <label for="small_females">Small operations for Females:</label>
    <input type="number" id="small_females" name="small_females" required><br><br>
    <label for="large_females">Large operations for Females:</label>
    <input type="number" id="large_females" name="large_females" required><br><br>

    <input type="submit" value="Submit">
</form>

</body>
</html>
