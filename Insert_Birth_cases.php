<!DOCTYPE html>
<html lang="">
<body>

<?php
include 'DB.php';
global $conn;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hospital_ID = isset($_POST['H_ID']) ? $_POST['H_ID'] : '';
    $normal_operation_male = isset($_POST['normal_birth_males']) ? $_POST['normal_birth_males'] : '';
    $caesera_operation_male = isset($_POST['caesera_birth_males']) ? $_POST['caesera_birth_males'] : '';
    $normal_operation_female = isset($_POST['normal_birth_females']) ? $_POST['normal_birth_females'] : '';
    $caesera_operation_female = isset($_POST['caesera_birth_females']) ? $_POST['caesera_birth_females'] : '';

    // Prepare the SQL statement
    $sql = "INSERT INTO `birth cases` (`hospital_ID`, `normal_birth_males`, `caesera_birth_males`, `normal_birth_females`, `caesera_birth_females`)
        VALUES (?, ?, ?, ?, ?)";


    // Prepare and bind
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param('iiiii', $hospital_ID,   $normal_operation_male, $caesera_operation_male, $normal_operation_female, $caesera_operation_female);
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
    <label for="normal_birth_males">Normal birth case for males</label>
    <input type="number" id="normal_birth_males" name="normal_birth_males" required><br><br>
    <label for="caesera_birth_males">Caesera birth case for males:</label>
    <input type="number" id="caesera_birth_males" name="caesera_birth_males" required><br><br>
    <label for="normal_birth_females">Normal birth case for Females</label>
    <input type="number" id="normal_birth_females" name="normal_birth_females" required><br><br>
    <label for="caesera_birth_females">Caesera birth case for Females:</label>
    <input type="number" id="caesera_birth_females" name="caesera_birth_females" required><br><br>

    <input type="submit" value="Submit">
</form>

</body>
</html>
