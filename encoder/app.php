<?php 
/*
Urmi you happy me happy licence

Copyright (/c) 2026 shreebhattji

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

<div class="containerindex">
    <h2 style="text-align: center; color: white; margin-bottom: 20px;">Company Information Entry</h2>
    
    <form action="" method="POST" enctype="multipart/mask" class="shadow p-4 bg-dark rounded" style="max-width: 900px; margin: 0 auto; color: white; border: 1px solid #333;">
        <div class="grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            
            <!-- Channel Details -->
            <div class="card" style="background: #111827; padding: 15px; border-radius: 8px; border: 1px solid #333;">
                <label class="form-label">Channel Name</label>
                <input type="text" name="channel_name" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars(getValue($saved_data, 'channel_name')); ?>" required>
            </div>

            <div class="card" style="background: #111827; padding: 15px; border-radius: 8px; border: 1px solid #333;">
                <label class="form-label">Office Address</label>
                <textarea name="office_address" class="form-control bg-dark text-white border-secondary" rows="1" required><?php echo htmlspecialchars(getValue($saved_data, 'office_address')); ?></textarea>
            </div>

            <!-- Contact Details -->
            <div class="card" style="background: #111827; padding: 15px; border-radius: 8px; border: 1px solid #333;">
                <label class="form-label">Contact Details</label>
                <input type="text" name="contact_details" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars(getValue($saved_data, 'contact_details')); ?>" required>
            </div>

            <!-- Enforcement Officer Details -->
            <div class="card" style="background: #111827; padding: 15px; border-radius: 8px; border: 1px solid #333;">
                <label class="form-label">Enforcement Officer</label>
                <input type="text" name="enforcement_officer" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars(getValue($saved_data, 'enforcement_officer')); ?>" required>
            </div>

            <div class="card" style="background: #111827; padding: 15px; border-radius: 8px; border: 1px solid #333;">
                <label class="form-label">EO Contact Details</label>
                <input type="text" name="eo_contact_details" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars(getValue($saved_data, 'eo_contact_details')); ?>" required>
            </div>

            <!-- Company Details -->
            <div class="card" style="background: #111827; padding: 15px; border-radius: 8px; border: 1px solid #333;">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars(getValue($saved_data, 'company_name')); ?>" required>
            </div>

            <div class="card" style="background: #111827; padding: 15px; border-radius: 8px; border: 1px solid #333;">
                <label class="form-label">CIN Number</label>
                <input type="text" name="cin_number" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars(getValue($saved_data, 'cin_number')); ?>" required>
            </div>

            <div class="card" style="background: #111827; padding: 15px; border-radius: 8px; border: 1px solid #333;">
                <label class="form-label">GSTIN Number</label>
                <input type="text" name="gstin_number" class="form-control bg-dark text-white border-secondary" value="<?php echo htmlspecialchars(getValue($saved_data, 'gstin_number')); ?>" required>
            </div>

            <!-- File Uploads -->
            <div class="card" style="background: #111827; padding: 15px; border-radius: 8px; border: 1px solid #333;">
                <label class="form-label">Upload Ad (PNG)</label>
                <input type="file" name="app_ad" class="form-control bg-dark text-white border-secondary" accept="image/png">
                <?php if (file_exists('/var/www/html/app_ad.png')): ?>
                    <img src="/var/www/html/app_ad.png" class="mt-2 img-thumbnail" style="max-height: 60px;">
                <?php endif; ?>
            </div>

            <div class="card" style="background: #111827; padding: 15px; border-radius: 8px; border: 1px solid #333;">
                <label class="form-label">Upload Logo (PNG)</label>
                <input type="file" name="app_logo" class="form-control bg-dark text-white border-secondary" accept="image/png">
                <?php if (file_exists('/var/www/html/app_logo.png')): ?>
                    <img src="/var/www/html/app_logo.png" class="mt-2 img-thumbnail" style="max-height: 60px;">
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4 text-center">
            <button type="submit" name="submit" class="green-btn" style="padding: 10px 40px;">Save All Details</button>
        </div>
    </form>

    <div class="mt-4">
    <?php
    if (isset($_POST['submit'])) {
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
                        // Success
                    } else {
                        $errors[] = "Failed to upload $input_name.";
                    }
                }
            }
        }

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
                echo '<div class="alert alert-success text-center">All data and files processed successfully!</div>';
                echo "<script>setTimeout(() => { window.location.reload(); }, 2000);</script>";
            } else {
                $errors[] = "Failed to save JSON data.";
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger text-center'>$error</div>";
            }
        }
    }
    ?>
    </div>
</div>

<?php include 'footer.php'; ?>