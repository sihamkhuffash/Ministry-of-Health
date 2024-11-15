<?php
include 'DB.php';
global $conn;

$input = [];
$total_males = [];
$total_females = [];
$normal = [];
$chronic = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $month = $_POST['month'];
    $year = $_POST['year'];
    $hospital_id = $_POST['hospital']; // Get selected hospital
$sql = "SELECT i.*, h.hospital_name FROM `birth cases` i JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID";
$input_data_result = "
        SELECT i.Birth_case_ID, h.hospital_name, i.normal_birth_males, i.caesera_birth_males, 
               i.normal_birth_females, i.caesera_birth_females
        FROM `birth cases` i
        JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
        WHERE i.month = ? AND i.year = ? AND i.hospital_ID = ?
    ";
$stmt = $conn->prepare($input_data_result);
$stmt->bind_param("iii", $month, $year, $hospital_id);
$stmt->execute();
$result_input = $stmt->get_result();
if ($result_input->num_rows > 0) {
    while($row = $result_input->fetch_assoc()) {
        $input[] = $row;
    }
}
$total_females_result = "
    SELECT i.hospital_ID, h.hospital_name, 
           SUM(`normal_birth_females` + `caesera_birth_females`) AS total_females
    FROM `birth cases` i
     JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
        WHERE i.month = ? AND i.year = ? AND i.hospital_ID = ?
";
$stmt = $conn->prepare($total_females_result);
$stmt->bind_param("iii", $month, $year, $hospital_id);
$stmt->execute();
$result_female = $stmt->get_result();
if ($result_female->num_rows > 0) {
    while($row = $result_female->fetch_assoc()) {
       $total_females[] = $row;
    }
}
$total_males_result = "
    SELECT i.hospital_ID, h.hospital_name, 
           SUM(`normal_birth_males` + `caesera_birth_males`) AS total_males
    FROM `birth cases` i
    JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
        WHERE i.month = ? AND i.year = ? AND i.hospital_ID = ?
";
$stmt = $conn->prepare($total_males_result);
$stmt->bind_param("iii", $month, $year, $hospital_id);
$stmt->execute();
$result_male = $stmt->get_result();
if ($result_male->num_rows > 0) {
    while($row = $result_male->fetch_assoc()) {
       $total_males[] = $row;
    }
}
$total_normal_result = "
    SELECT i.hospital_ID, h.hospital_name, 
           SUM(`normal_birth_males` + `normal_birth_females`) AS total_normal
    FROM `birth cases` i
    JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
        WHERE i.month = ? AND i.year = ? AND i.hospital_ID = ?
";
$stmt = $conn->prepare($total_normal_result);
$stmt->bind_param("iii", $month, $year, $hospital_id);
$stmt->execute();
$result_normal = $stmt->get_result();
if ($result_normal->num_rows > 0) {
    while($row = $result_normal->fetch_assoc()) {
       $normal[] = $row;
    }
}
$total_caesera_result = "
    SELECT i.hospital_ID, h.hospital_name, 
           SUM(`caesera_birth_males` + `caesera_birth_females`) AS total_caesera
    FROM `birth cases` i
     JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
        WHERE i.month = ? AND i.year = ? AND i.hospital_ID = ?
";
$stmt = $conn->prepare($total_caesera_result);
$stmt->bind_param("iii", $month, $year, $hospital_id);
$stmt->execute();
$result_chronic = $stmt->get_result();
if ($result_chronic->num_rows > 0) {
    while($row = $result_chronic->fetch_assoc()) {
       $chronic[] = $row;
    }
}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Operations Data</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Your Page</title>
    <link rel="stylesheet" href="tables_styles.css">
</head>
<body>

<div class="back-to-home" style="padding-bottom: 50px">
    <a href="Dashboard.php">
        <i class="fas fa-home"></i> Back to Dashboard
    </a>
</div>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="padding-top: 50px">
    <p>
        <label for="hospital" style="font-size: 20px">Select Hospital:</label>
        <select id="hospital" name="hospital" required>
            <option value="" disabled selected>Select Hospital</option>
            <?php
            // Fetch hospital names from the database
            $hospital_query = "SELECT hospital_ID, hospital_name FROM hospitals";
            $hospital_result = $conn->query($hospital_query);
            if ($hospital_result->num_rows > 0) {
                while($hospital = $hospital_result->fetch_assoc()) {
                    echo '<option value="' . $hospital['hospital_ID'] . '">' . $hospital['hospital_name'] . '</option>';
                }
            }
            ?>
        </select>
    </p>
    <p>
        <label for="month" style="font-size: 20px">Select Month (1-12):</label>
        <input type="number" id="month" name="month" min="1" max="12" required>
    </p>
    <p>
        <label for="year" style="font-size: 20px">Select Year (2014-2024):</label>
        <input type="number" id="year" name="year" min="2014" max="2024" required>
    </p>

    <p>
        <input type="submit" value="Submit" style="border-radius: 24px; width: 250px; background-color: #3a937a; height: 40px; font-family: Arial, sans-serif;">
    </p>
</form>

<div class="table-wrapper" style="padding-top: 50px">
    <h2>Total Males</h2>
    <table class="fl-table">
        <thead>
        <tr>
            <th>Hospital Name</th>
            <th>Total Males</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($total_males as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                <td><?php echo htmlspecialchars($row['total_males']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="table-wrapper">
    <h2>Total Females</h2>
    <table class="fl-table">
        <thead>
        <tr>
            <th>Hospital Name</th>
            <th>Total Females</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($total_females as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                <td><?php echo htmlspecialchars($row['total_females']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="table-wrapper">
    <h2>Total Small Operation</h2>
    <table class="fl-table">
        <thead>
        <tr>
            <th>Hospital Name</th>
            <th>Total Small Operation</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($normal as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                <td><?php echo htmlspecialchars($row['total_normal']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="table-wrapper">
    <h2>Total Large Operation</h2>
    <table class="fl-table">
        <thead>
        <tr>
            <th>Hospital Name</th>
            <th>Total Large Operation</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($chronic as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                <td><?php echo htmlspecialchars($row['total_caesera']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="table-wrapper">
    <h2>Birth Cases Data</h2>
    <table class="fl-table">
        <thead>
        <tr>
            <th>Birth_case ID</th>
            <th>Hospital Name</th>
            <th>Small Operation for Male</th>
            <th>Large Operation for Male</th>
            <th>Small Operation for Female</th>
            <th>Large Operation for Female</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($input as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['Birth_case_ID']); ?></td>
                <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                <td><?php echo htmlspecialchars($row['normal_birth_males']); ?></td>
                <td><?php echo htmlspecialchars($row['caesera_birth_males']); ?></td>
                <td><?php echo htmlspecialchars($row['normal_birth_females']); ?></td>
                <td><?php echo htmlspecialchars($row['caesera_birth_females']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
