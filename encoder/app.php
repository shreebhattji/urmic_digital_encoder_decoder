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
        'app_ad' => '/var/www/html/app_ad.png',
        'app_logo' => '/var/www/html/app_logo.png'
    ];
    $secondary_dir = '/var/www/encoder/';

    $errors = [];

    // Handle File Deletions (Remove feature)
    $to_delete = $_POST['remove_files'] ?? [];
    foreach ($to_delete as $file_key => $should_delete) {
        if ($should_delete === '1' && isset($upload_paths[$file_key])) {
            $path = $upload_paths[$file_key];
            if (file_exists($path)) {
                unlink($path);
                // Also remove from encoder directory
                $secondary_path = $secondary_dir . basename($path);
                if (file_exists($secondary_path)) unlink($secondary_path);
            }
        }
    }

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
            $target_width = ($input_name === 'app_logo') ? 512 : 1080;
            $target_height = ($input_name === 'app_logo') ? 512 : 1080;

            // 3. Load the source image
            $src_img = @imagecreatefrompng($tmp_path);
            if (!$src_img) {
                $errors[] = "Failed to process $input_name (Invalid PNG data).";
                continue;
            }

            // 4. Create a blank truecolor canvas
            $dst_img = imagecreatetruecolor($target_width, $target_height);

            // 5. Preserve transparency for PNG
            imagealphablending($dst_img, false);
            imagesavealpha($dst_img, true);
            $transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
            imagefill($dst_img, 0, 0, $transparent);

            // 6. Resize (Resample)
            imagecopyresampled(
                $dst_img,
                $src_img,
                0,
                0,
                0,
                0,
                $target_width,
                $target_height,
                imagesx($src_img),
                imagesy($src_img)
            );

            // 7. Save to primary destination with compression (level 7)
            if (imagepng($dst_img, $destination, 7)) {
                $filename = basename($int_name);
                $secondary_destination = $secondary_dir . $filename;

                if (!copy($destination, $secondary_destination)) {
                    $errors[] = "Failed to create secondary copy for $input_name.";
                }
            } else {
                $errors[] = "Failed to save processed $input_name.";
            }

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

include 'header.php';
?>

<script>
    // Function to clear file input and set the hidden removal flag
    function prepareRemoval(inputId, removeInputId) {
        document.getElementById(inputId).value = "";
        document.getElementById(removeInputId).value = "1";
    }
</script>

<div class="containerindex">
    <form method="POST" enctype="multipart/form-data">
        <!-- Hidden inputs to handle deletion -->
        <input type="hidden" name="remove_files[app_ad]" id="remove_app_ad" value="">
        <input type="hidden" name="remove_files[app_logo]" id="remove_app_logo" value="">

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
                    <label>Channel Name</label>
                </div>
                <div class="input-group">
                    <input type="text" name="office_address" value="<?php echo htmlspecialchars(getValue($saved_data, 'office_address')); ?>" required>
                    <label>Office Address</label>
                </div>
                <div class="input-group">
                    <input type="text" name="contact_details" value="<?php echo htmlspecialchars(getValue($saved_data, 'contact_details')); ?>" required>
                    <label>Contact Details</label>
                </div>
                <div class="input-group">
                    <input type="text" name="enforcement_officer" value="<?php echo htmlspecialchars(getValue($saved_data, 'enforcement_officer')); ?>" required>
                    <label>Enforcement Officer</label>
                </div>
                <div class="input-group">
                    <input type="text" name="eo_contact_details" value="<?php echo htmlspecialchars(getValue($saved_data, 'eo_contact_details')); ?>" required>
                    <label>EO Contact Details</label>
                </div>
                <div class="input-group">
                    <input type="text" name="company_name" value="<?php echo htmlspecialchars(getValue($saved_data, 'company_name')); ?>" required>
                    <label>Company Name</label>
                </div>
                <div class="input-group">
                    <input type="text" name="cin_number" value="<?php echo htmlspecialchars(getValue($saved_data, 'cin_number')); ?>">
                    <label>CIN Number</label>
                </div>
                <div class="input-group">
                    <input type="text" name="gstin_number" value="<?php echo htmlspecialchars(getValue($saved_data, 'gstin_number')); ?>">
                    <label>GSTIN Number</label>
                </div>
            </div>

            <!-- Upload Ad Section -->
            <div class="card wide">
                <h3 style="margin-bottom: 15px;">Upload Ad (PNG)</h3>
                <div class="input-group">
                    <input type="file" name="app_ad" id="file_app_int" accept="image/png" style="color: white;">

                    <?php if (isset($_FILES['app_ad']) && $_FILES['app_ad']['tmp_name'] != ''): ?>
                        <div class="mt-2"><small style="color: #aaa;">New file selected (will upload on save)</small></div>
                    <?php elseif (file_exists('/var/www/html/app_ad.png')): ?>
                        <div class="mt-2">
                            <small style="color: #aaa;">Current:</small><br>
                            <img src="/app_ad.png" class="img-thumbnail" style="max-height: 60px; opacity: 0.7; margin-top: 5px;">
                            <button type="button" onclick="prepareRemoval('file_app_int', 'remove_app_ad')" style="background:none; border:none; color:#ff4d4d; cursor:pointer; font-size:12px; text-decoration:underline; display:block; margin-top:5px;">Remove Existing</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upload Logo Section -->
            <div class="card wide">
                <h3 style="margin-bottom: 15px;">Upload Logo (PNG)</h3>
                <div class="input-group">
                    <input type="file" name="app_logo" id="file_app_logo" accept="image/png" style="color: white;">

                    <?php if (isset($_FILES['app_logo']) && $_FILES['app_logo']['tmp_name'] != ''): ?>
                        <div class="mt-2"><small style="color: #aaa;">New file selected (will upload on save)</small></div>
                    <?php elseif (file_exists('/var/www/html/app_logo.png')): ?>
                        <div class="mt-2">
                            <small style="color: #aaa;">Current:</small><br>
                            <img src="/app_logo.png" class="img-thumbnail" style="max-height: 60px; opacity: 0.7; margin-top: 5px;">
                            <button type="button" onclick="prepareRemoval('file_app_logo', 'remove_app_logo')" style="background:none; border:none; color:#ff4d4d; cursor:pointer; font-size:12px; text-decoration:underline; display:block; margin-top:5px;">Remove Existing</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="text-align:center; width:100%; margin-top:20px; margin-bottom: 40px;">
            <button type="submit" name="submit" style="background:#c00;color:#fff;padding:12px 60px;border:none;font-weight:bold;border-radius:6px;cursor:pointer;font-size:16px;">Save All Details</button>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>