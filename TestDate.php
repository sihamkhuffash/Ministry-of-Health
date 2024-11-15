<?php
include 'DB.php';
global $conn;

// Handle the form submission
$input = [];
$total_males = [];
$total_females = [];
$normal = [];
$chronic = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $month = $_POST['month'];
    $year = $_POST['year'];

    // Query to get total males for the selected month and year
    $total_males_result = "
        SELECT i.hospital_ID, h.hospital_name, 
               SUM(`normal_diseas_male` + `chronic_diseas_male`) AS total_males
        FROM `inputs` i 
        JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
        WHERE i.month = ? AND i.year = ?
        GROUP BY i.hospital_ID, h.hospital_name
    ";
    $stmt = $conn->prepare($total_males_result);
    $stmt->bind_param("ii", $month, $year);
    $stmt->execute();
    $result_males = $stmt->get_result();
    if ($result_males->num_rows > 0) {
        while($row = $result_males->fetch_assoc()) {
            $total_males[] = $row;
        }
    }

    // Query to get total females for the selected month and year
    $total_females_result = "
        SELECT i.hospital_ID, h.hospital_name, 
               SUM(`normal_diseas_female` + `chronic_diseas_female`) AS total_females
        FROM `inputs` i
        JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
        WHERE i.month = ? AND i.year = ?
        GROUP BY i.hospital_ID, h.hospital_name
    ";
    $stmt = $conn->prepare($total_females_result);
    $stmt->bind_param("ii", $month, $year);
    $stmt->execute();
    $result_females = $stmt->get_result();
    if ($result_females->num_rows > 0) {
        while($row = $result_females->fetch_assoc()) {
            $total_females[] = $row;
        }
    }

    // Query to get total normal diseases for the selected month and year
    $total_normal_result = "
        SELECT i.hospital_ID, h.hospital_name, 
               SUM(`normal_diseas_female` + `normal_diseas_male`) AS total_normal
        FROM `inputs` i
        JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
        WHERE i.month = ? AND i.year = ?
        GROUP BY i.hospital_ID, h.hospital_name
    ";
    $stmt = $conn->prepare($total_normal_result);
    $stmt->bind_param("ii", $month, $year);
    $stmt->execute();
    $result_normal = $stmt->get_result();
    if ($result_normal->num_rows > 0) {
        while($row = $result_normal->fetch_assoc()) {
            $normal[] = $row;
        }
    }

    // Query to get total chronic diseases for the selected month and year
    $total_chronic_result = "
        SELECT i.hospital_ID, h.hospital_name, 
               SUM(`chronic_diseas_male` + `chronic_diseas_female`) AS total_chronic
        FROM `inputs` i
        JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
        WHERE i.month = ? AND i.year = ?
        GROUP BY i.hospital_ID, h.hospital_name
    ";
    $stmt = $conn->prepare($total_chronic_result);
    $stmt->bind_param("ii", $month, $year);
    $stmt->execute();
    $result_chronic = $stmt->get_result();
    if ($result_chronic->num_rows > 0) {
        while($row = $result_chronic->fetch_assoc()) {
            $chronic[] = $row;
        }
    }

    // Query to get input data for the selected month and year
    $sql_input = "
        SELECT i.*, h.hospital_name 
        FROM `inputs` i 
        JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
        WHERE i.month = ? AND i.year = ?
    ";
    $stmt = $conn->prepare($sql_input);
    $stmt->bind_param("ii", $month, $year);
    $stmt->execute();
    $result_input = $stmt->get_result();
    if ($result_input->num_rows > 0) {
        while($row = $result_input->fetch_assoc()) {
            $input[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Input Data</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .table-container {
            margin: 20px;
        }
    </style>
</head>
<body>

<!-- Form to select month and year -->
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <label for="month">Select Month (1-12):</label>
    <input type="number" id="month" name="month" min="1" max="12" required>
    <label for="year">Select Year (2014-2024):</label>
    <input type="number" id="year" name="year" min="2014" max="2024" required>
    <input type="submit" value="Submit">
</form>

<!-- Display Total Males -->
<div class="table-container">
    <h2>Total Males</h2>
    <table>
        <thead>
        <tr>
            <th>Hospital Name</th>
            <th>Total Males</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($total_males)): ?>
            <?php foreach ($total_males as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_males']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">No data found for the selected month and year.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Display Total Females -->
<div class="table-container">
    <h2>Total Females</h2>
    <table>
        <thead>
        <tr>
            <th>Hospital Name</th>
            <th>Total Females</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($total_females)): ?>
            <?php foreach ($total_females as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_females']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">No data found for the selected month and year.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Display Total Normal -->
<div class="table-container">
    <h2>Total Normal</h2>
    <table>
        <thead>
        <tr>
            <th>Hospital Name</th>
            <th>Total Normal</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($normal)): ?>
            <?php foreach ($normal as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_normal']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">No data found for the selected month and year.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Display Total Chronic -->
<div class="table-container">
    <h2>Total Chronic</h2>
    <table>
        <thead>
        <tr>
            <th>Hospital Name</th>
            <th>Total Chronic</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($chronic)): ?>
            <?php foreach ($chronic as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_chronic']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">No data found for the selected month and year.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Display Input Data -->
<div class="table-container">
    <h2>Input Data</h2>
    <table>
        <thead>
        <tr>
            <th>Input ID</th>
            <th>Hospital Name</th>
            <th>Normal Diseases Male</th>
            <th>Chronic Diseases Male</th>
            <th>Normal Diseases Female</th>
            <th>Chronic Diseases Female</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($input)): ?>
            <?php foreach ($input as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Input_ID']); ?></td>
                    <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['normal_diseas_male']); ?></td>
                    <td><?php echo htmlspecialchars($row['chronic_diseas_male']); ?></td>
                    <td><?php echo htmlspecialchars($row['normal_diseas_female']); ?></td>
                    <td><?php echo htmlspecialchars($row['chronic_diseas_female']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No data found for the selected month and year.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
