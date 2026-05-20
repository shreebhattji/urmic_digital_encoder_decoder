<?php
/*
Urmi you happy me happy licence

Copyright (c) 2026 shreebhattji

License text:
https://github.com/shreebhatt_ji/Urmi/blob/main/licence.md
*/

$json_file = '/var/www/html/app.json';
$error_message = '';

// --- 1. Handle Form Submission BEFORE any HTML is rendered ---
if (isset($_POST['submit'])) {
    $upload_paths = [
        'app_ad' => '/var/www/html/app_ad.png',
        'app_logo' => '/var/www/html/app_logo.png'
    ];
    $secondary_dir = '/var/www/encoder/';

    $errors = [];

    // Handle File Deletions
    $to_delete = $_POST['remove_files'] ?? [];
    foreach ($to_delete as $file_key => $should_delete) {
        if ($should_delete === '1' && isset($upload_paths[$file_key])) {
            $path = $upload_paths[$file_key];
            if (file_exists($path)) {
                unlink($path);
                // Also remove from encoder directory
                $secondary_path = $secondary_dir . basename($path);
                if (file_exists($secondary_path)) {
                    unlink($secondary_path);
                }
            }
        }
    }

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
            $target_width = ($input_name === 'app_logo') ? 128 : 256;
            $target_height = ($input_name === 'app_logo') ? 128 : 256;

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
            imagesavealpha($png_alpha = $dst_img, true);
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
                $filename = basename($destination);
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
        'gstin_number',
        'content_type',
        'content_rating',
        'content_language'
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

<style>
    .containerindex { max-width: 1000px; margin: 0 auto; padding: 20px; font-family: sans-serif; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .card { background: #1a1a1a; padding: 25px; border-radius: 12px; border: 1px solid #333; }
    .card.wide { grid-column: span 2; }
    .input-group { position: relative; margin-bottom: 25px; }
    .input-group input[type="text"] {
        width: 100%; padding: 12px; background: transparent; border: 1px solid #444;
        border-radius: 6px; color: white; outline: none; box-sizing: border-box;
    }
    .input-group label { position: absolute; left: 12px; top: 12px; color: #888; pointer-events: none; transition: 0.2s; }
    .input-group input:focus, .input-group input:not(:placeholder-shown) { border-color: #c00; }
    .input-group input:focus + label, .input-group input:not(:placeholder-shown) + label {
        top: -10px; left: 8px; font-size: 12px; color: #c00; background: #1a1a1a; padding: 0 4px;
    }
    .dropdown-container { position: relative; margin-bottom: 25px; }
    select { width: 100%; padding: 12px; background: #222; color: white; border: 1px solid #444; border-radius: 6px; }
    .img-thumbnail { border-radius: 4px; border: 1px solid #444; }
    .mt-2 { margin-top: 10px; }
</style>

<script>
    function prepareRemoval(inputId, removeInputId, previewImgId) {
        document.getElementById(inputId).value = "";
        document.getElementById(removeInputId).value = "1";
        const imgElement = document.getElementById(previewImgId);
        if (imgElement) {
            imgElement.style.display = 'none';
            const btn = imgElement.nextElementSibling;
            if (btn && btn.tagName === 'BUTTON') btn.style.display = 'none';
        }
        const container = document.getElementById(inputId).parentElement;
        const label = container.querySelector('small');
        if (label) {
            label.innerText = "File marked for removal";
            label.style.color = "#ff4d4d";
        }
    }
</script>

<div class="containerindex">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="remove_files[app_ad]" id="remove_app_ad" value="">
        <input type="hidden" name="remove_files[app_logo]" id="remove_app_logo" value="">

        <div class="grid">
            <h2>Company Information Entry</h2>

            <?php if (!empty($error_message)): ?>
                <div style="background: #ff0000; color: white; padding: 15px; border-radius: 6px; margin-bottom: 20px; grid-column: span 2;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Company Details -->
            <div class="card wide">
                <h3>General Details</h3>
                <div class="grid">
                    <div class="input-wrap">
                        <div class="input-group">
                            <input type="text" name="channel_name" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'channel_name')); ?>" required>
                            <label>Channel Name</label>
                        </div>
                        <div class="input-group">
                            <input type="text" name="office_address" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'office_address')); ?>" required>
                            <label>Office Address</label>
                        </div>
                    </div>
                    <div class="input-wrap">
                        <div class="input-group">
                            <input type="text" name="contact_details" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'contact_details')); ?>" required>
                            <label>Contact Details</label>
                        </div>
                        <div class="input-group">
                            <input type="text" name="enforcement_officer" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'enforcement_officer')); ?>" required>
                            <label>Enforcement Officer</label>
                        </div>
                    </div>
                </div>
                
                <div class="grid">
                    <div class="input-group">
                        <input type="text" name="eo_contact_details" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'eo_contact_details')); ?>" required>
                        <label>EO Contact Details</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="company_name" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'company_name')); ?>" required>
                        <label>Company Name</label>
                    </div>
                </div>

                <div class="grid">
                    <div class="input-group">
                        <input type="text" name="cin_number" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'cin_number')); ?>">
                        <label>CIN Number</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="gstin_number" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'gstin_number')); ?>">
                        <label>GSTIN Number</label>
                    </div>
                </div>
            </div>

            <!-- Content Details -->
            <div class="card wide">
                <h3>Content Details</h3>
                <div class="grid">
                    <div class="input-group">
                        <input type="text" name="content_type" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'content_type')); ?>" required>
                        <label>Content Type</label>
                    </div>
                    <div class="dropdown-container">
                        <select name="content_rating" id="content_rating">
                            <?php
                            $ratings = ['U' => 'Universal', 'UA' => 'Parental Guidance', 'A' => 'Adults Only', 'S' => 'Special'];
                            foreach ($ratings as $code => $desc) {
                               $selected = (getValue($saved_data, 'content_rating') === $code) ? 'selected' : '';
                               echo "<option value=\"$code\" $selected>$code ($desc)</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <input type="text" name="content_language" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'content_language')); ?>" required>
                        <label>Content Language</label>
                    </div>
                </div>
            </div>

            <!-- Upload Ad Section -->
            <div class="card">
                <h3>Upload Ad (PNG)</h3>
                <div class="input-group">
                    <input type="file" name="app_ad" id="file_app_ad" accept="image/png" style="color: white;">
                    <?php if (isset($_FILES['app_ad']) && $_FILES['app_ad']['tmp_name'] != ''): ?>
                        <div class="mt-2"><small style="color: #aaa;">New file selected</small></div>
                    <?php elseif (file_exists('/var/www/html/app_ad.png')): ?>
                        <div class="mt-2">
                            <img src="/app_ad.png" id="preview_app_ad" class="img-thumbnail" style="max-height: 60px; opacity: 0.7;">
                            <button type="button" onclick="prepareRemoval('file_app_ad', 'remove_app_ad', 'preview_app_ad')" style="background:none; border:none; color:#ff4d4d; cursor:pointer; font-size:12px; text-decoration:underline; display:block; margin-top:5px;">Remove Existing</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upload Logo Section -->
            <div class="card">
                <h3>Upload Logo (PNG)</h3>
                <div class="input-group">
                    <input type="file" name="app_logo" id="file_app_logo" accept="image/png" style="color: white;">
                    <?php if (isset($_FILES['app_logo']) && $_FILES['app_logo']['tmp_name'] != ''): ?>
                        <div class="mt-2"><small style="color: #aaa;">New file selected</small></div>
                    <?php elseif (file_exists('/var/www/html/app_logo.png')): ?>
                        <div class="mt-2">
                            <img src="/app_logo.png" id="preview_app_logo" class="img-thumbnail" style="max-height: 60px; opacity: 0.7;">
                            <button type="button" onclick="prepareRemoval('file_app_logo', 'remove_app_logo', 'preview_app_logo')" style="background:none; border:none; color:#ff4d4d; cursor:pointer; font-size:12px; text-decoration:underline; display:block; margin-top:5px;">Remove Existing</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="text-align:center; width:100%; margin: 40px 0;">
            <button type="submit" name="submit" style="background:#c00;color:#fff;padding:15px 80px;border:none;font-weight:bold;border-radius:6px;cursor:pointer;font-size:18px; transition: 0.3s;">Save All Details</button>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>