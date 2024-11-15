<?php
include 'DB.php';
global $conn;

// Fetch list of hospitals for the dropdown
$hospital_list = [];
$hospital_query = "SELECT hospital_ID, hospital_name FROM `hospitals`";
$hospital_result = $conn->query($hospital_query);

if ($hospital_result->num_rows > 0) {
    while ($row = $hospital_result->fetch_assoc()) {
        $hospital_list[] = $row;
    }
}

$input = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $month = $_POST['month'];
    $year = $_POST['year'];
    $hospital_ID = $_POST['hospital']; // Get selected hospital ID

    // Update SQL query to include hospital filter
    $sql = "SELECT i.*, h.Department_name, d.hospital_name
        FROM `sections` i 
        JOIN `department` h 
        ON i.Department_ID = h.Department_ID
        JOIN `hospitals` d 
        ON h.hospital_ID = d.hospital_ID
        WHERE i.month = ? AND i.year = ? AND d.hospital_ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $month, $year, $hospital_ID);
    $stmt->execute();
    $result1 = $stmt->get_result();
    if ($result1->num_rows > 0) {
        while($row = $result1->fetch_assoc()) {
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
    <title>Sections Data</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        .back-to-home {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 1.2rem;
        }

        .back-to-home a {
            color: #3a937a; /* Your desired color */
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .back-to-home a:hover {
            color: #2c6f5c;
        }

        .back-to-home i {
            margin-right: 8px; /* Space between icon and text */
        }
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
        .table-wrapper{
            margin: 10px 70px 70px;
            box-shadow: 0px 35px 50px rgba( 0, 0, 0, 0.2 );
        }
        . h2
        {
            padding-left: 20px;
        }



        .fl-table td, .fl-table th {
            text-align: center;
            padding: 8px;
        }

        .fl-table td {
            border-right: 1px solid #f8f8f8;
            font-size: 12px;
        }

        .fl-table thead th {
            color: #ffffff;
            background: #4FC3A1;
        }


        .fl-table thead th:nth-child(odd) {
            color: #ffffff;
            background: #324960;
        }

        .fl-table tr:nth-child(even) {
            background: #F8F8F8;
        }

        /* Responsive */

        @media (max-width: 767px) {
            .fl-table {
                display: block;
                width: 100%;
            }
            .table-wrapper:before{
                content: "Scroll horizontally >";
                display: block;
                text-align: right;
                font-size: 11px;
                color: white;
                padding: 0 0 10px;
            }
            .fl-table thead, .fl-table tbody, .fl-table thead th {
                display: block;
            }
            .fl-table thead th:last-child{
                border-bottom: none;
            }
            .fl-table thead {
                float: left;
            }
            .fl-table tbody {
                width: auto;
                position: relative;
                overflow-x: auto;
            }
            .fl-table td, .fl-table th {
                padding: 20px .625em .625em .625em;
                height: 60px;
                vertical-align: middle;
                box-sizing: border-box;
                overflow-x: hidden;
                overflow-y: auto;
                width: 120px;
                font-size: 13px;
                text-overflow: ellipsis;
            }
            .fl-table thead th {
                text-align: left;
                border-bottom: 1px solid #f7f7f9;
            }
            .fl-table tbody tr {
                display: table-cell;
            }
            .fl-table tbody tr:nth-child(odd) {
                background: none;
            }
            .fl-table tr:nth-child(even) {
                background: transparent;
            }
            .fl-table tr td:nth-child(odd) {
                background: #F8F8F8;
                border-right: 1px solid #E6E4E4;
            }
            .fl-table tr td:nth-child(even) {
                border-right: 1px solid #E6E4E4;
            }
            .fl-table tbody td {
                display: block;
                text-align: center;
            }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Your Page</title>

</head>
<body>
<div class="back-to-home">
    <a href="Dashboard.php">
        <i class="fas fa-home"></i> Back to Dashboard
    </a>
</div>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="padding-top: 50px">
    <!-- Hospital Dropdown -->
  <p> <label for="hospital">Select Hospital:</label>
    <select id="hospital" name="hospital" required>
        <option value="">Select a hospital</option>
        <?php foreach ($hospital_list as $hospital): ?>
            <option value="<?php echo $hospital['hospital_ID']; ?>">
                <?php echo htmlspecialchars($hospital['hospital_name']); ?>
            </option>
        <?php endforeach; ?>
    </select></p>

    <!-- Month and Year Inputs -->
    <p><label for="month">Select Month (1-12):</label>
    <input type="number" id="month" name="month" min="1" max="12" required></p>

    <p><label for="year">Select Year (2014-2024):</label>
    <input type="number" id="year" name="year" min="2014" max="2024" required></p>

    <input type="submit" value="Submit" style="border-radius: 24px;width:250px;background-color:#3a937a;height: 40px; font-family: Arial, sans-serif;">
</form>

<div class="table-wrapper">
    <h2 style="padding-left: 450px ; padding-top: 20px">Sections Data</h2>
    <table class="fl-table">
        <thead>
        <tr>
            <th>Section ID</th>
            <th>Department Name</th>
            <th>Section Name</th>
            <th>Hospital Name</th>
            <th>Number of Beds</th>
            <th>Remaining Issue</th>
            <th>Input in Month</th>
            <th>Recovery Cases</th>
            <th>Number of Male Deaths</th>
            <th>Number of Female Deaths</th>
            <th>Hospital Days</th>
            <th>Remaining Patient</th>
            <th>Occupancy Rate (%)</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($input as $row):
            // Calculate the occupancy rate
            $number_of_beds = $row['number_of_beds'];
            $hospital_days = $row['Hspital_days'];
            $occupancy_rate = 0;

            if ($number_of_beds > 0) {
                $occupancy_rate = ($hospital_days / ($number_of_beds * 360)) * 100;
            }
            ?>
            <tr>
                <td><?php echo htmlspecialchars($row['section_ID']); ?></td>
                <td><?php echo htmlspecialchars($row['Department_name']); ?></td>
                <td><?php echo htmlspecialchars($row['section_name']); ?></td>
                <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                <td><?php echo htmlspecialchars($row['number_of_beds']); ?></td>
                <td><?php echo htmlspecialchars($row['remaining_issue']); ?></td>
                <td><?php echo htmlspecialchars($row['input_in_month']); ?></td>
                <td><?php echo htmlspecialchars($row['recovery_cases']); ?></td>
                <td><?php echo htmlspecialchars($row['number_of_male_deaths']); ?></td>
                <td><?php echo htmlspecialchars($row['number_of_female_deaths']); ?></td>
                <td><?php echo htmlspecialchars($row['Hspital_days']); ?></td>
                <td><?php echo htmlspecialchars($row['remaining_patient']); ?></td>
                <td><?php echo number_format($occupancy_rate, 2); ?>%</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
