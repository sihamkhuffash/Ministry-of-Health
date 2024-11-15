<?php
include 'DB.php';
global $conn;

$sql = "SELECT h.hospital_ID, h.hospital_name, h.hospital_location, h.hospital_phone_num,
       COALESCE(i.total_normal_diseas_male, 0) AS total_normal_diseas_male,
       COALESCE(i.total_chronic_diseas_male, 0) AS total_chronic_diseas_male,
       COALESCE(i.total_normal_diseas_female, 0) AS total_normal_diseas_female,
       COALESCE(i.total_chronic_diseas_female, 0) AS total_chronic_diseas_female,
       COALESCE(s.total_small_males, 0) AS total_small_males,
       COALESCE(s.total_large_males, 0) AS total_large_males,
       COALESCE(s.total_small_females, 0) AS total_small_females,
       COALESCE(s.total_large_females, 0) AS total_large_females,
       COALESCE(b.total_normal_birth_males, 0) AS total_normal_birth_males,
       COALESCE(b.total_caesera_birth_males, 0) AS total_caesera_birth_males,
       COALESCE(b.total_normal_birth_females, 0) AS total_normal_birth_females,
       COALESCE(b.total_caesera_birth_females, 0) AS total_caesera_birth_females,
       COALESCE(d.total_male_deaths, 0) AS total_male_deaths,
       COALESCE(d.total_female_deaths, 0) AS total_female_deaths

FROM `hospitals` h

LEFT JOIN (
    SELECT hospital_ID,
           SUM(normal_diseas_male) AS total_normal_diseas_male,
           SUM(chronic_diseas_male) AS total_chronic_diseas_male,
           SUM(normal_diseas_female) AS total_normal_diseas_female,
           SUM(chronic_diseas_female) AS total_chronic_diseas_female
    FROM `inputs`
    GROUP BY hospital_ID
) i ON h.hospital_ID = i.hospital_ID

LEFT JOIN (
    SELECT hospital_ID,
           SUM(small_males) AS total_small_males,
           SUM(large_male) AS total_large_males,
           SUM(small_female) AS total_small_females,
           SUM(large_female) AS total_large_females
    FROM `surgical operation`
    GROUP BY hospital_ID
) s ON h.hospital_ID = s.hospital_ID

LEFT JOIN (
    SELECT hospital_ID,
           SUM(normal_birth_males) AS total_normal_birth_males,
           SUM(caesera_birth_males) AS total_caesera_birth_males,
           SUM(normal_birth_females) AS total_normal_birth_females,
           SUM(caesera_birth_females) AS total_caesera_birth_females
    FROM `birth cases`
    GROUP BY hospital_ID
) b ON h.hospital_ID = b.hospital_ID

LEFT JOIN (
    SELECT d.hospital_ID,
           SUM(s.number_of_male_deaths) AS total_male_deaths,
           SUM(s.number_of_female_deaths) AS total_female_deaths
    FROM `sections` s
    JOIN `department` d ON s.Department_ID = d.Department_ID
    GROUP BY d.hospital_ID
) d ON h.hospital_ID = d.hospital_ID

ORDER BY h.hospital_ID;";


$total_table1="
SELECT i.hospital_ID, h.hospital_name,
SUM(`normal_diseas_male`+`normal_diseas_female`) AS Normal,
SUM(`chronic_diseas_male`+`chronic_diseas_female`) AS Chronic,
SUM(`chronic_diseas_male`+`normal_diseas_male`) AS Males,
SUM(`chronic_diseas_female`+`normal_diseas_female`) AS Females
FROM `inputs` i
    JOIN `hospitals` h ON i.hospital_ID = h.hospital_ID
    GROUP BY i.hospital_ID, h.hospital_name

";

$result = $conn->query($sql);
$result2 = $conn->query($total_table1);

if ($result === false) {
    die("Error in SQL query: " . $conn->error);
}

if ($result2 === false) {
    die("Error in SQL query: " . $conn->error);
}

$hospital_data =[];
$total=[];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $hospital_data[] = $row;
    }
}
if($result2->num_rows > 0) {
    while($row = $result2->fetch_assoc()) {
        $total[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Data</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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

<div class="table-container">
    <h2>Hospital Data</h2>
    <table>
        <thead>
        <tr>
            <th>Hospital ID</th>
            <th>Hospital Name</th>
            <th>Location</th>
            <th>Contact</th>
            <th>Normal Diseases Male</th>
            <th>Chronic Diseases Male</th>
            <th>Normal Diseases Female</th>
            <th>Chronic Diseases Female</th>
            <th>Small Operations Male</th>
            <th>Large Operations Male</th>
            <th>Small Operations Female</th>
            <th>Large Operations Female</th>
            <th>Normal Birth Operations Male</th>
            <th>Caesera Birth Operations Male</th>
            <th>Normal Birth Operations Female</th>
            <th>Caesera Birth Operations Female</th>
            <th>Number of Male Deaths</th>
            <th>Number of Female Deaths</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($hospital_data as $hospital): ?>
            <tr>
                <td><?php echo htmlspecialchars($hospital['hospital_ID']); ?></td>
                <td><?php echo htmlspecialchars($hospital['hospital_name']); ?></td>
                <td><?php echo htmlspecialchars($hospital['hospital_location']); ?></td>
                <td><?php echo htmlspecialchars($hospital['hospital_phone_num']); ?></td>
                <td><?php echo htmlspecialchars($hospital['total_normal_diseas_male']); ?></td>
                <td><?php echo htmlspecialchars($hospital['total_chronic_diseas_male']); ?></td>
                <td><?php echo htmlspecialchars($hospital['total_normal_diseas_female']); ?></td>
                <td><?php echo htmlspecialchars($hospital['total_chronic_diseas_female']); ?></td>
                <td><?php echo htmlspecialchars($hospital['total_small_males']); ?></td>
                <td><?php echo htmlspecialchars($hospital['total_large_males']); ?></td>
                <td><?php echo htmlspecialchars($hospital['total_small_females']); ?></td>
                <td><?php echo htmlspecialchars($hospital['total_large_females']); ?></td>
                <td><?php echo htmlspecialchars($hospital['total_normal_birth_males']); ?></td>
                <td><?php echo htmlspecialchars($hospital['total_caesera_birth_males']); ?></td>
                <td><?php echo htmlspecialchars($hospital['total_normal_birth_females']); ?></td>
                <td><?php echo htmlspecialchars($hospital['total_caesera_birth_females']); ?></td>
                <td><?php echo htmlspecialchars($hospital['total_male_deaths']); ?></td>
                <td><?php echo htmlspecialchars($hospital['total_female_deaths']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="table-container">
    <h2>Total from </h2>
    <table>
        <thead>
        <tr>
            <th>Hospital Name</th>
            <th>Normal</th>
            <th>Chronic</th>
            <th>Males</th>
            <th>Females</th>

        </tr>
        </thead>
        <tbody>
        <?php foreach ($total as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                <td><?php echo htmlspecialchars($row['Normal']); ?></td>
                <td><?php echo htmlspecialchars($row['Chronic']); ?></td>
                <td><?php echo htmlspecialchars($row['Males']); ?></td>
                <td><?php echo htmlspecialchars($row['Females']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
