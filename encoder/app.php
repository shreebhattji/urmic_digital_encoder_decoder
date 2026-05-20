<?php 
/*
Urmi you happy me happy licence

Copyright (c) 2026 shreebhattji

License text:
https://github.com/shreebhattji/Urmi/blob/main/licence.md
*/

include 'header.php'; 

$json_file = '/var/www/html/app.json';
$saved_data = [];

if (file_exists($json_file)) {
    $json_content = file_get_contents($json_file);
    $saved_data = json_decode($json_content, true) ?? [];
}

function getValue($data, $key) {
    return $data[$key] ?? '';
}
?>

<div class="container mt-5">
    <h2>Company Information Entry</h2>
    <form action="" method="POST" enctype="multipart/form-data" class="shadow p-4 bg-light rounded">
        <div class="row">
            <!-- Channel Details -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Channel Name</label>
                <input type="text" name="channel_name" class="form-control" value="<?php echo htmlspecialchars(getValue($saved_data, 'channel_name')); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Office Address</label>
                <textarea name="office_address" class="form-control" rows="1" required><?php echo htmlspecialchars(getValue($saved_data, 'office_address')); ?></textarea>
            </div>

            <!-- Contact Details -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Contact Details</label>
                <input type="text" name="contact_details" class="form-control" value="<?php echo htmlspecialchars(getValue($saved_data, 'contact_details')); ?>" required>
            </div>

            <!-- Enforcement Officer Details -->
            <div class="col-md-6 mb-3">
                <lag class="form-label">Enforcement Officer</label>
                <input type="text" name="enforcement_officer" class="form-control" value="<?php echo htmlspecialchars(getValue($saved_data, 'enforcement_officer')); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Contact Details of Enforcement Officer</label>
                <input type="text" name="eo_contact_details" class="form-control" value="<?php echo htmlspecialchars(getValue($saved_data, 'eo_contact_details')); ?>" required>
            </div>

            <!-- Company Details -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars(getValue($saved_data, 'company_name')); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">CIN Number</label>
                <input type="text" name="cin_number" class="form-control" value="<?php echo htmlspecialchars(getValue($saved_data, 'cin_number')); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">GSTIN Number</label>
                <input type="text" name="gstin_number" class="form-control" value="<?php echo htmlspecialchars(getValue($saved_data, 'gstin_number')); ?>" required>
            </div>

            <!-- File Uploads -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Upload Ad (PNG only)</label>
                <input type="file" name="app_ad" class="form-control" accept="image/png">
                <?php if (file_exists('/var/www/html/app_ad.png')): ?>
                    <div class="mt-2">
                        <small class="text-muted d-block">Current Ad:</small>
                        <img src="/var/www/html/app_ad.png" alt="Current Ad" class="img-thumbnail" style="max-height: 100px;">
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Upload Logo (PNG only)</label>
                <input type="file" name="app_logo" class="form-control" accept="image/png">
                <?php if (file_exists('/var/www/html/app_logo.png')): ?>
                    <div class="mt-2">
                        <small class="text-muted d-block">Current Logo:</small>
                        <img src="/php/var/www/html/app_logo.png" alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" name="submit" class="btn btn-primary">Submit Details</button>
        </div>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        // Handle File Uploads
        $upload_paths = [
            'app_ad' => '/var/www/html/app_ad.png',
            'app_logo' => '/var/www/html/app_logo.png'
        ];

        $errors = [];

        foreach ($upload_paths as $input_name => $destination) {
            if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
                $ext = pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION);
                if (strtolower($ext) !== 'png') {
                    $errors[] = "File for $input_name must be a PNG.";
                } else {
                    if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $destination)) {
                        echo "<div class='alert alert-success mt-2'>$input_name uploaded successfully.</div>";
                    } else {
                        $errors[] = "Failed to upload $input_name. Check folder permissions.";
                    }
                }
            }
        }

        // Save text data to JSON
        $text_fields = [
            'channel_name', 'office_address', 'contact_details', 'enforcement_officer', 
            'eo_contact_details', 'company_name', 'cin_number', 'gstin_number'
        ];
        
        $data_to_save = [];
        foreach ($text_fields as $field) {
            $data_to_save[$field] = $_POST[$field] ?? '';
        }

        if (empty($errors)) {
            if (file_put_contents($json_file, json_encode($data_to_save, JSON_PRETTY_PRINT))) {
                echo "<div class='alert alert-success mt-2'>All data and files processed successfully!</div>";
                // Refresh to show updated data in form
                echo "<script>setTimeout(() => { window.location.reload(); }, 2000);</script>";
            } else {
                $errors[] = "Failed to save JSON data. Check folder permissions.";
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger mt-2'>$error</div>";
            }
        }
    }
    ?>
</div>

<?php include 'footer.php'; ?>