<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['spoc_flag'] != '1') {
    header("Location: login.php");
    exit();
}

$building_name = $_SESSION['building'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filename = $building_name . "_tanker_records.csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen("php://output", "w");

    // Add column headers to the downloaded file
    fputcsv($output, ['Tanker Vendor Name', 'Tanker Capacity', 'Date', 'Time', 'TDS Value', 'TDS Device Photo', 'Was Tanker Full', 'Tanker Full Photo', 'Captured By', 'Building Name']);

    $file = fopen('tanker_info.csv', 'r');

    // Write rows that match the building name
    while (($data = fgetcsv($file)) !== FALSE) {
        if ($data[9] == $building_name) {
            fputcsv($output, $data);
        }
    }

    fclose($file);
    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-4 text-center">
                <h2 class="mb-4">Download Tanker Records</h2>
                <form method="post" action="">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Download</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
