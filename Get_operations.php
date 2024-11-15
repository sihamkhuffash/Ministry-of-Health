<?php
include 'DB.php';
global $conn;

// Initialize arrays to store results
$operation = [];
$total_males = [];
$total_females = [];
$large = [];
$small = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $month = $_POST['month'];
    $year = $_POST['year'];
    $hospital_id = $_POST['hospital']; // Get selected hospital

    // Query to get total males for the selected hospital, month, and year
    $total_males_result = "
        SELECT i.hospital_ID, h.hospital_name, 
               SUM(`small_males` + `large_male`) AS total_males
        FROM `surgical operation` i 
        JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
       WHERE i.month = ? AND i.year = ? AND i.hospital_ID = ?
        GROUP BY i.hospital_ID, h.hospital_name
    ";
    $stmt = $conn->prepare($total_males_result);
    $stmt->bind_param("iii", $month, $year, $hospital_id);
    $stmt->execute();
    $result_males = $stmt->get_result();
    if ($result_males->num_rows > 0) {
        while ($row = $result_males->fetch_assoc()) {
            $total_males[] = $row;
        }
    }

    // Query to get total females for the selected hospital, month, and year
    $total_females_result = "
        SELECT i.hospital_ID, h.hospital_name, 
               SUM(`small_female` + `large_female`) AS total_females
        FROM `surgical operation` i 
        JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
      WHERE i.month = ? AND i.year = ? AND i.hospital_ID = ?
        GROUP BY i.hospital_ID, h.hospital_name
    ";
    $stmt = $conn->prepare($total_females_result);
    $stmt->bind_param("iii", $month, $year, $hospital_id);
    $stmt->execute();
    $result_females = $stmt->get_result();
    if ($result_females->num_rows > 0) {
        while ($row = $result_females->fetch_assoc()) {
            $total_females[] = $row;
        }
    }

    // Query to get total small operations for the selected hospital, month, and year
    $total_small_result = "
        SELECT i.hospital_ID, h.hospital_name, 
               SUM(`small_males` + `small_female`) AS total_small
        FROM `surgical operation` i 
        JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
       WHERE i.month = ? AND i.year = ? AND i.hospital_ID = ?
        GROUP BY i.hospital_ID, h.hospital_name
    ";
    $stmt = $conn->prepare($total_small_result);
    $stmt->bind_param("iii", $month, $year, $hospital_id);
    $stmt->execute();
    $result_small = $stmt->get_result();
    if ($result_small->num_rows > 0) {
        while ($row = $result_small->fetch_assoc()) {
            $small[] = $row;
        }
    }

    // Query to get total large operations for the selected hospital, month, and year
    $total_large_result = "
        SELECT i.hospital_ID, h.hospital_name, 
               SUM(`large_male` + `large_female`) AS total_large
        FROM `surgical operation` i 
        JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
       WHERE i.month = ? AND i.year = ? AND i.hospital_ID = ?
        GROUP BY i.hospital_ID, h.hospital_name
    ";
    $stmt = $conn->prepare($total_large_result);
    $stmt->bind_param("iii", $month, $year, $hospital_id);
    $stmt->execute();
    $result_large = $stmt->get_result();
    if ($result_large->num_rows > 0) {
        while ($row = $result_large->fetch_assoc()) {
            $large[] = $row;
        }
    }

    $total_operation = "
        SELECT i.hospital_ID, h.hospital_name, i.operation_ID,SUM(i.large_male) AS large_male
             ,SUM(i.large_female) AS large_female, SUM(i.small_males) AS small_males ,SUM(i.small_female) AS small_female
        FROM `surgical operation` i 
        JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
       WHERE i.month = ? AND i.year = ? AND i.hospital_ID = ?
        GROUP BY i.hospital_ID, h.hospital_name
    ";
    $stmt = $conn->prepare($total_operation);
    $stmt->bind_param("iii", $month, $year, $hospital_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $operation [] = $row;
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
    <link rel="stylesheet" href="tables_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Your Page</title>
</head>
<body>


<div>
    <div class="back-to-home">
        <a href="Dashboard.php">
            <i class="fas fa-home"></i> Back to Dashboard
        </a>
    </div>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="padding-top: 50px;padding-left: 20px">
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
        <p><label for="month" style="font-size: 20px; color: #324960" >Select Month (1-12):</label>
            <input  type="number" id="month" name="month" min="1" max="12" required ></p>
        <p> <label  for="year" style="font-size: 20px; color: #324960">Select Year (2014-2024):</label>
            <input type="number" id="year" name="year" min="2014" max="2024" required></p>
        <p> <input type="submit" value="Submit" style="border-radius: 24px;width:250px;background-color:#3a937a;height: 40px; font-family: Arial, sans-serif;; color: #324960" "></p>
    </form>

    
</div>

<div class="table-wrapper">
    <h2 style="padding-top: 20px; padding-left: 600px">Total Females</h2>
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
    <h2 style="padding-top: 20px; padding-left: 600px">Total Small Operation</h2>
    <table class="fl-table">
        <thead>
        <tr>
            <th>Hospital Name</th>
            <th>Total Small Operation</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($small as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                <td><?php echo htmlspecialchars($row['total_small']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="table-wrapper">
    <h2 style="padding-top: 20px; padding-left: 600px">Total Large Operation</h2>
    <table class="fl-table">
        <thead>
        <tr>
            <th>Hospital Name</th>
            <th>Total Large Operation</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($large as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                <td><?php echo htmlspecialchars($row['total_large']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="table-wrapper">
    <h2 style="padding-top: 20px; padding-left: 600px">Operations Data</h2>
    <table class="fl-table">
        <thead>
        <tr>
            <th>Operation ID</th>
            <th>Hospital Name</th>
            <th>Small Operation for Male</th>
            <th>Large Operation for Male</th>
            <th>Small Operation for Female</th>
            <th>Large Operation for Female</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($operation as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['operation_ID']); ?></td>
                <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                <td><?php echo htmlspecialchars($row['small_males']); ?></td>
                <td><?php echo htmlspecialchars($row['large_male']); ?></td>
                <td><?php echo htmlspecialchars($row['small_female']); ?></td>
                <td><?php echo htmlspecialchars($row['large_female']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
