<?php
/*
Urmi you happy me happy licence

Copyright (c) 2026 shreebhattji

License text:
https://github.com/shreebhatt_ji/Urmi/blob/main/licence.md
*/

$json_file = '/var/www/html/app.json';
$error_message = '';

// --- 1. Handle Form Submission BEFORE any HTML is sent ---
if (isset($_POST['submit'])) {
    $upload_paths = [
        'app_und' => '/var/www/html/app_ad.png', 
        'app_ad' => '/var/www/html/app_ad.png',
        'app_logo' => '/var/www/html/app_logo.png'
    ];
    $secondary_dir = '/var/www/encoder/';

    $errors = [];

    // Handle File Uploads
    foreach ($upload_paths as $input_name => $destination) {
        if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
            $tmp_path = $_FILES[$input_name]['tmp_name'];
            
            // 1. Verify it is actually a PNG using GD/getimagesize
            $image_info = @getimagesize($tmp_path);
            if (!$image_info || $image_info[2] !== IMAGETYPE_PNG) {
                $errors[] = "File for $input_name must be a valid PNG image.";
                continue;
            }

            // 2. Determine target dimensions
            // Logo: 512x512, Ad: 1080x1080
            $target_width = 1080;
            $target_height = 1080;
            if ($input_name === 'app_logo') {
                $target_width = 512;
                $target_height = 512;
            }

            // 3. Load the source image
            $src_img = @imagecreatefrompng($tmp_path);
            if (!$src_img) {
                $errors[] = "Failed to process $input_name (Invalid PNG data).";
                continue;
            }

            // 4. Create a blank truecolor canvas for the new size
            $dst_img = imagecreatetruecolor($target_width, $target_height);

            // 5. Preserve transparency for PNG
            imagealphablending($dst_img, false);
            imagesavealpha($dst_img, true);
            $transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
            imagefill($dst_img, 0, 0, $transparent);

            // 6. Resize (Resample) using interpolation for high quality
            imagecopyresampled(
                $dst_img, $src_img, 
                0, 0, 0, 0, 
                $target_width, $target_height, 
                imagesx($src_img), imagesy($src_img)
            );

            // 7. Save to primary destination with compression (level 7)
            if (imagepng($dst_img, $destination, 7)) {
                // Create a copy in the encoder directory (/var/www/encoder/)
                $filename = basename($destination);
                $secondary_destination = $secondary_dir . $filename;
                
                if (!copy($destination, $secondary_destination)) {
                    $errors[] = "Failed to create secondary copy for $input_name in encoder folder.";
                }
            } else {
                $errors[] = "Failed to save processed $input_name.";
            }

            // 8. Free memory
            imagedestroy($src_img);
            imagedestroy($dst_img);
        }
    }

    // Handle Text Fields
    $text_fields = [
        'channel_name',
        'office_address',
        'contact_details',
        'enforcement_officer',
        'eo_contact_details',
        'company_name',
        'cin_number',
        'gstin_number'
    ];

    $data_to_save = [];
    foreach ($text_fields as $field) {
        $data_to_save[$field] = $_POST[$field] ?? '';
    }

    // Save if no errors occurred
    if (empty($errors)) {
        file_put_contents($json_file, json_encode($data_to_save, JSON_PRETTY_PRINT));
        
        // Redirect to the same page to refresh the data and clear POST state
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = implode("<br>", $errors);
    }
}

// --- 2. Load Data for Display ---
$saved_data = [];
if (file_exists($json_file)) {
    $json_content = file_get_contents($json_file);
    $saved_data = json_decode($json_content, true) ?? [];
}

function getValue($data, $key)
{
    return $data[$key] ?? '';
}

// Now we can safely start sending HTML
include 'header.php';
?>

<form method="POST" enctype="multipart/form-data">
    <div class="containerindex">
        <div class="grid">
            <h2 style="color: #ff4d4d;">Company Information Entry</h2>

            <?php if (!empty($error_message)): ?>
                <div style="background: #ff0000; color: white; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Channel Details -->
            <div class="card wide">
                <div class="input-group">
                    <input type="text" name="channel_name" value="<?php echo htmlspecialchars(getValue($saved_data, 'channel_name')); ?>" required>
                    <label for="channel_name">Channel Name</label>
                </div>
                <div class="input-group">
                    <input type="text" name="office_address" value="<?php echo htmlspecialchars(getValue($saved_data, 'office_address')); ?>" required>
                    <label for="office_address">Office Address</label>
                </div>
                <div class="input-group">
                    <input type="text" name="contact_details" value="<?php echo htmlspecialchars(getValue($saved_data, 'contact_details')); ?>" required>
                    <label for="contact_details">Contact Details</label>
                </div>
                <div class="input-group">
                    <input type="text" name="enforcement_officer" value="<?php echo htmlspecialchars(getValue($saved_data, 'enforcement_officer')); ?>" required>
                    <label for="enforcement_officer">Enforcement Officer</label>
                </div>
                <div class="input-group">
                    <input type="text" name="eo_contact_details" value="<?php echo htmlspecialchars(getValue($saved_data, 'eo_contact_details')); ?>" required>
                    <label for="eo_contact_details">EO Contact Details</label>
                </div>
                <div class="input-group">
                    <input type="text" name="company_name" value="<?php echo htmlspecialchars(getValue($saved_data, 'company_name')); ?>" required>
                    <label for="company_name">Company Name</label>
                </div>
                <div class="input-group">
                    <input type="text" name="cin_number" value="<?php echo htmlspecialchars(getValue($saved_data, 'cin_number')); ?>">
                    <label for="cin_number">CIN Number</label>
                </div>
                <div class="input-group">
                    <input type="text" name="gstin_number" value="<?php echo htmlspecialchars(getValue($saved_data, 'gstin_number')); ?>">
                    <label for="gstin_number">GSTIN Number</label>
                </div>
            </div>

            <div class="card wide">
                <h3 style="margin-bottom: 15px;">Upload Ad (PNG)</h3>
                <div class="input-group">
                    <input type="file" name="app_ad" accept="image/png" style="color: white;">
                    <?php if (file_exists('/var/www/html/app_ad.png')): ?>
                        <div class="mt-2"><small style="color: #aaa;">Current:</small><br><img src="/app_ad.png" class="img-thumbnail" style="max-height: 60px; opacity: 0.7; margin-top: 5px;"></div >
                    <?php endif; ?>
                </div>
            </div>

            <div class="card wide">
                <h3 style="margin-bottom: 15px;">Upload Logo (PNG)</h3>
                <div class="input-group">
                    <input type="file" name="app_logo" accept="image/png" style="color: white;">
                    <?php if (file_exists('/var/www/html/app_logo.png')): ?>
                        <div class="mt-2"><small style="color: #aaa;">Current:</small><br><img src="/app_logo.png" class="img-thumbnail" style="max-height: 60px; opacity: 0.7; margin-top: 5px;"></div >
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="text-align:center; width:100%; margin-top:20px; margin-bottom: 40px;">
            <button type="submit" name="submit" style="background:#c00;color:#fff;padding:12px 60px;border:none;font-weight:bold;border-radius:6px;cursor:pointer;font-size:16px;">Save All Details</button>
        </div>
    </div>
</form>

<?php include 'footer.php'; ?>