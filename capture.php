<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$vendors = [];
if (($handle = fopen("vendor_list.txt", "r")) !== FALSE) {
    while (($vendor = fgets($handle)) !== FALSE) {
        $vendors[] = trim($vendor);
    }
    fclose($handle);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vendor_name = $_POST['vendor_name'];
    $capacity = $_POST['capacity'];
    $tds_value = $_POST['tds_value'];
    $tanker_full = $_POST['tanker_full'];
    $captured_by = $_SESSION['username'];
    $building_name = $_SESSION['building'];
    $date = date('Y-m-d');
    $time = date('H:i:s');

    $tds_photo = $_FILES['tds_photo']['name'];
    $tds_photo_tmp = $_FILES['tds_photo']['tmp_name'];
    $tanker_full_photo = $_FILES['tanker_full_photo']['name'];
    $tanker_full_photo_tmp = $_FILES['tanker_full_photo']['tmp_name'];

    move_uploaded_file($tds_photo_tmp, "uploads/tds_photos/" . $tds_photo);
    move_uploaded_file($tanker_full_photo_tmp, "uploads/tanker_full_photos/" . $tanker_full_photo);

    $file_path = 'tanker_info.csv';
    $file_exists = file_exists($file_path);

    // Open the file for appending and create if not exists
    $file = fopen($file_path, 'a');

    // Add headers if the file is being created for the first time
    if (!$file_exists) {
        fputcsv($file, ['Tanker Vendor Name', 'Tanker Capacity', 'Date', 'Time', 'TDS Value', 'TDS Device Photo', 'Was Tanker Full', 'Tanker Full Photo', 'Captured By', 'Building Name']);
    }

    // Add the captured data to the CSV file
    fputcsv($file, [$vendor_name, $capacity, $date, $time, $tds_value, "uploads/tds_photos/$tds_photo", $tanker_full, "uploads/tanker_full_photos/$tanker_full_photo", $captured_by, $building_name]);

    fclose($file);

    header("Location: done.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanker Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="row justify-content-center w-100">
            <div class="col-md-8 col-lg-6">
                <h2 class="text-center mb-4">Tanker Tracker</h2>
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="vendor_name" class="form-label">Tanker Vendor Name</label>
                                <select id="vendor_name" name="vendor_name" class="form-select" required>
                                    <?php foreach ($vendors as $vendor): ?>
                                        <option value="<?= $vendor ?>"><?= $vendor ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="capacity" class="form-label">Tanker Capacity</label>
                                <input type="number" id="capacity" name="capacity" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="tds_value" class="form-label">TDS Value</label>
                                <input type="number" id="tds_value" name="tds_value" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="tds_photo" class="form-label">TDS Device Photo</label>
                                <input type="file" id="tds_photo" name="tds_photo" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="tanker_full" class="form-label">Was Tanker Full?</label>
                                <select id="tanker_full" name="tanker_full" class="form-select" required>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tanker_full_photo" class="form-label">Tanker Full Photo</label>
                                <input type="file" id="tanker_full_photo" name="tanker_full_photo" class="form-control" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
