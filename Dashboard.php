<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Database Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1>Hospital Database Dashboard</h1>
        <p class="department-name">Information Center of Health</p>
    </header>



    <nav class="dashboard-nav">
        <ul>
            <li><a href="Get_Hospitals.php">Hospitals</a></li>
            <li><a href="Get_Input.php">Inputs</a></li>
            <li><a href="Get_operations.php">Operations Information</a></li>
            <li><a href="Get_birth_cases.php">Birth Cases</a></li>
            <li><a href="Get_sections.php">Sections</a></li>
            <li><a href="Get_birth_data.php">Birth data</a></li>
            <li><a href="Insert_Hospital.php">Insert Hospitals</a></li>
            <li><a href="Insert_Input.php">Insert Inputs</a></li>
            <li><a href="Insert_operations.php">Insert Operations</a></li>

        </ul>
    </nav>

    <section id="table1" class="dashboard-section">
        <div class="section-overlay">
            <h2>Hospital Data Overview</h2>
            <p>Information is the lifeblood of medicine and health</p>
        </div>
    </section>

    <footer class="dashboard-footer">
        <p>&copy; 2024 Hospital Database</p>
        <p>Health Information center </p>
    </footer>
</div>

</body>
</html>
