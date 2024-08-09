<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$building_name = $_SESSION['building'];
$tanker_records = [];
$start_date = '';
$end_date = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['download'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $filename = $building_name . "_tanker_records_" . $start_date . "_to_" . $end_date . ".csv";
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen("php://output", "w");
    
    // Add column headers to the downloaded file
    fputcsv($output, ['Tanker Vendor Name', 'Tanker Capacity', 'Date', 'Time', 'TDS Value', 'TDS Device Photo', 'Was Tanker Full', 'Tanker Full Photo', 'Captured By', 'Building Name']);
    
    $file = fopen('tanker_info.csv', 'r');

    // Filter and write rows that match the building name and date range
    while (($data = fgetcsv($file)) !== FALSE) {
        if ($data[9] == $building_name && $data[2] >= $start_date && $data[2] <= $end_date) {
            fputcsv($output, $data);
        }
    }

    fclose($file);
    fclose($output);
    exit();
} else {
    if (($file = fopen('tanker_info.csv', 'r')) !== FALSE) {
        while (($data = fgetcsv($file)) !== FALSE) {
            if ($data[9] == $building_name) {
                $tanker_records[] = $data;
            }
        }
        fclose($file);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Tanker Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Tanker Records for <?= $building_name ?></h2>
        
        <form method="post" action="" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" name="download" class="btn btn-primary w-100">Download Filtered Data</button>
                </div>
            </div>
        </form>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tanker Vendor Name</th>
                    <th>Tanker Capacity</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>TDS Value</th>
                    <th>TDS Device Photo</th>
                    <th>Was Tanker Full</th>
                    <th>Tanker Full Photo</th>
                    <th>Captured By</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tanker_records)): ?>
                    <tr>
                        <td colspan="9" class="text-center">No records found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tanker_records as $index => $record): ?>
                        <tr>
                            <td><?= htmlspecialchars($record[0]) ?></td>
                            <td><?= htmlspecialchars($record[1]) ?></td>
                            <td><?= htmlspecialchars($record[2]) ?></td>
                            <td><?= htmlspecialchars($record[3]) ?></td>
                            <td><?= htmlspecialchars($record[4]) ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($record[5]) ?>" alt="TDS Photo" style="width: 100px; height: auto; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-image="<?= htmlspecialchars($record[5]) ?>">
                            </td>
                            <td><?= htmlspecialchars($record[6]) ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($record[7]) ?>" alt="Tanker Full Photo" style="width: 100px; height: auto; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-image="<?= htmlspecialchars($record[7]) ?>">
                            </td>
                            <td><?= htmlspecialchars($record[8]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for Image Preview -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Image Preview" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle the image click to show it in a modal
        const imageModal = document.getElementById('imageModal');
        imageModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Button that triggered the modal
            const imageUrl = button.getAttribute('data-bs-image'); // Extract info from data-bs-* attributes
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageUrl; // Update the modal's image source
        });
    </script>
</body>
</html>
