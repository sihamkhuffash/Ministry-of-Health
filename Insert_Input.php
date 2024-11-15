<!DOCTYPE html>
<html lang="">
<body>

<?php
include 'DB.php';
global $conn;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hospital_ID = isset($_POST['H_ID']) ? $_POST['H_ID'] : '';
    $normal_diseas_male = isset($_POST['Normal_males']) ? $_POST['Normal_males'] : '';
    $chronic_diseas_male = isset($_POST['chronic_males']) ? $_POST['chronic_males'] : '';
    $normal_diseas_female = isset($_POST['Normal_females']) ? $_POST['Normal_females'] : '';
    $chronic_diseas_female = isset($_POST['chronic_females']) ? $_POST['chronic_females'] : '';

    // Prepare the SQL statement
    $sql = "INSERT INTO `inputs` (`hospital_ID`, `normal_diseas_male`, `chronic_diseas_male`, `normal_diseas_female`, `chronic_diseas_female`)
        VALUES (?, ?, ?, ?, ?)";


    // Prepare and bind
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param('sssss', $hospital_ID, $normal_diseas_male, $chronic_diseas_male, $normal_diseas_female, $chronic_diseas_female);
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

<h1>Input information to insert</h1>

<form method="POST" action="">
    <label for="H_ID">Hospital ID:</label>
    <input type="text" id="H_ID" name="H_ID" required><br><br>
    <label for="Normal_males">Normal males:</label>
    <input type="text" id="Normal_males" name="Normal_males" required><br><br>
    <label for="chronic_males">chronic Males:</label>
    <input type="text" id="chronic_males" name="chronic_males" required><br><br>
    <label for="Normal_females">Normal Females:</label>
    <input type="text" id="Normal_females" name="Normal_females" required><br><br>
    <label for="chronic_females">chronic Females:</label>
    <input type="text" id="chronic_females" name="chronic_females" required><br><br>

    <input type="submit" value="Submit">
</form>

</body>
</html>
