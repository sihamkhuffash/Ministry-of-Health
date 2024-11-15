<?php
include 'DB.php';
global $conn;

// Initialize arrays to store results
$total = [];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $month = $_POST['month'];
    $year = $_POST['year'];
    $hospital_id = $_POST['hospital']; // Get selected hospital

    // Query to get total males for the selected hospital, month, and year
   

    $total_date = "
    SELECT 
        i.birth_child_ID, 
        h.hospital_name, 
        SUM(i.`under_1.5_live_females`) AS under1_5LF, 
        SUM(i.`under_1.5_dead_males`) AS under1_5DM, 
        SUM(i.`under_1.5_dead_females`) AS under1_5DF, 
        SUM(i.`under_1.5_live_males`) AS under1_5LM, 
        i.hospital_ID, 
        SUM(i.`from_1.5_to_2.499_live_males`) AS from1_5LM, 
        SUM(i.`from_1.5_to_2.499_live_females`) AS from1_5LF, 
        SUM(i.`from_1.5_to_2.499_dead_males`) AS from1_5DM, 
        SUM(i.`from_1.5_to_2.499_dead_females`) AS from1_5DF, 
        SUM(i.`from_2.5_to_3.999_live_males`) AS from2_5LM, 
        SUM(i.`from_2.5_to_3.999_live_females`) AS from2_5LF, 
        SUM(i.`from_2.5_to_3.999_dead_males`) AS from2_5DM, 
        SUM(i.`from_2.5_to_3.999_dead_females`) AS from2_5DF, 
        SUM(i.`more_than_4_live_meals`) AS more4LM,
        SUM(i.`more_than_4_live_femeals`) AS more4LF, 
        SUM(i.`more_than_4_dead_meals`) AS more4DM, 
        SUM(i.`more_than_4_dead_femeals`) AS more4DF
    FROM `birth child data` i
    JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
    WHERE i.month = ? AND i.year = ? AND i.hospital_ID = ?
";


    $stmt = $conn->prepare($total_date);
    $stmt->bind_param("iii", $month, $year, $hospital_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $total [] = $row;
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Birth Data</title>
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
    <h2 style="padding-top: 20px; padding-left: 600px">Operations Data</h2>
    <table class="fl-table">
        <thead>
        <tr>
            <th>birth_child_ID</th>
            <th>Hospital Name</th>
            <th>under_1.5_live_females</th>
            <th>under_1.5_dead_males</th>
            <th>under_1.5_dead_females</th>
            <th>under_1.5_live_males</th>

            <th>from_1.5_to_2.499_live_males</th>
            <th>from_1.5_to_2.499_live_females</th>
            <th>from_1.5_to_2.499_dead_males</th>
            <th>from_1.5_to_2.499_dead_females</th>

            <th>from_2.5_to_3.999_live_males</th>
            <th>from_2.5_to_3.999_live_females</th>
            <th>from_2.5_to_3.999_dead_males</th>
            <th>from_2.5_to_3.999_dead_females</th>

            <th>more_than_4_live_meals</th>
            <th>more_than_4_live_femeals</th>
            <th>more_than_4_dead_meals</th>
            <th>more_than_4_dead_femeals</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($total as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['birth_child_ID']); ?></td>
                <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                <td><?php echo htmlspecialchars($row['under1_5LF']); ?></td>
                <td><?php echo htmlspecialchars($row['under1_5DM']); ?></td>
                <td><?php echo htmlspecialchars($row['under1_5DF']); ?></td>
                <td><?php echo htmlspecialchars($row['under1_5LM']); ?></td>
                <td><?php echo htmlspecialchars($row['from1_5LM']); ?></td>
                <td><?php echo htmlspecialchars($row['from1_5LF']); ?></td>
                <td><?php echo htmlspecialchars($row['from1_5DM']); ?></td>
                <td><?php echo htmlspecialchars($row['from1_5DF']); ?></td>
                <td><?php echo htmlspecialchars($row['from2_5LM']); ?></td>
                <td><?php echo htmlspecialchars($row['from2_5LF']); ?></td>
                <td><?php echo htmlspecialchars($row['from2_5DM']); ?></td>
                <td><?php echo htmlspecialchars($row['from2_5DF']); ?></td>
                <td><?php echo htmlspecialchars($row['more4LM']); ?></td>
                <td><?php echo htmlspecialchars($row['more4LF']); ?></td>
                <td><?php echo htmlspecialchars($row['more4DM']); ?></td>
                <td><?php echo htmlspecialchars($row['more4DF']); ?></td>
                

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
