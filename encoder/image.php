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
if (isset($_POST['submit']) || isset($_POST['display'])) {
    $upload_paths = [
        'app_ad' => '/var/www/html/app_ad.png',
        'app_logo' => '/var/www/html/app_logo.png',
        'vertical_ad' => '/var/www/html/vertical_ad.png',
        'horizontal_ad' => '/var/www/html/horizontal_ad.png'
    ];
    $secondary_dir = '/var/www/encoder/';

    if (isset($_POST['app_ad_url'])) {
        $current_json = [];
        if (file_exists($json_file)) {
            $current_json = json_decode(file_get_contents($json_file), true) ?? [];
        }
        $current_json['app_ad_url'] = $_POST['app_ad_url'];
        if (!file_put_contents($json_file, json_encode($current_json, JSON_PRETTY_PRINT))) {
            $errors[] = "Failed to update app.json with the new URL.";
        }
    }
    
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

            // Define dimensions based on input name
            $dimensions = [
                'app_logo' => [128, 128],
                'app_ad' => [256, 256],
                'vertical_ad' => [288, 840],
                'horizontal_ad' => [1920, 240]
            ];

            $target_width = $dimensions[$input_name][0];
            $target_height = $dimensions[$input_name][1];

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
            $transparent = imagecolorallocatealpha($dst_img, 0, 0, 0, 127);
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

                if (!@copy($destination, $secondary_destination)) {
                    $errors[] = "Failed to create secondary copy for $input_name.";
                }
            } else {
                $errors[] = "Failed to save processed $input_name.";
            }

            imagedestroy($src_img);
            imagedestroy($dst_img);
        }
    }

    // Save if no errors occurred
    if (empty($errors)) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = implode("<br>", $errors);
    }
}

include 'header.php';
?>

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

<style>
    .preview-container {
        max-height: none;
        /* Removed height restriction */
        overflow: visible;
        /* Allow content to be seen */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: #222;
        border-radius: 4px;
        padding: 5px;
    }

    .img-thumbnail {
        max-width: 100%;
        height: auto;
        /* Maintain aspect ratio */
        object-fit: contain;
        opacity: 0.7;
    }
</style>

<form method="POST" enctype="multipart/form-data">
    <div class="containerindex">
        <input type="hidden" name="remove_files[app_ad]" id="remove_app_ad" value="">
        <input type="hidden" name="remove_files[app_logo]" id="remove_app_logo" value="">
        <input type="hidden" name="remove_files[vertical_ad]" id="remove_vertical_ad" value="">
        <input type="hidden" name="remove_files[horizontal_ad]" id="remove_horizontal_ad" value="">

        <div class="grid">
            <h2 style="grid-column: span 2;">Image Assets Management</h2>

            <?php if (!empty($error_message)): ?>
                <div style="background: #ff0000; color: white; padding: 15px; border-radius: 6px; margin-bottom: 20px; grid-column: span 2;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Logo Section -->
            <div class="card wide">
                <h3>App Logo (128x128)</h3>
                <div class="input-group">
                    <input type="file" name="app_logo" id="file_app_logo" accept="image/png" style="color: white;">
                    <?php if (file_exists('/var/www/html/app_logo.png')): ?>
                        <div class="mt-2 preview-container">
                            <img src="/app_logo.png" id="preview_app_logo" class="img-thumbnail">
                            <button type="button" onclick="prepareRemoval('file_app_logo', 'remove_app_logo', 'preview_app_logo')" style="background:none; border:none; color:#ff4d4d; cursor:pointer; font-size:12px; text-decoration:underline; margin-top:5px;">Remove Existing</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- App Ad Section -->
            <div class="card wide">
                <h3>App Ad (256x256)</h3>

                <div class="input-group" style="margin-bottom: 15px; border-bottom: 1px solid #444; padding-bottom: 10px;">
                    <label style="display:block; margin-bottom:5px; font-size:12px; color:#aaa;">App Ad URL</label>
                    <input type="text" name="app_ad_url" value="<?php $current_json = file_exists($json_file) ? json_decode(file_get_contents($json_file), true) : [];
                                                                echo htmlspecialchars($current_json['app_ad_url'] ?? '');
                                                                ?>" style="width:100%; background:#333; border:1px solid #555; color:white; padding:8px; border-radius:4px; box-sizing:border-box;">
                </div>
                <div class="input-group">
                    <input type="file" name="app_ad" id="app_ad_file" accept="image/png" style="color: white;">
                    <?php if (file_exists('/var/www/html/app_ad.png')): ?>
                        <div class="mt-2 preview-container">
                            <img src="/app_ad.png" id="preview_app_ad" class="img-thumbnail">
                            <button type="button" onclick="prepareRemoval('app_ad_file', 'remove_app_ad', 'preview_app_ad')" style="background:none; border:none; color:#ff4d4d; cursor:pointer; font-size:12px; text-decoration:underline; margin-top:5px;">Remove Existing</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Vertical Ad Section -->
            <div class="card wide">
                <h3>Vertical Ad (288x840)</h3>
                <div class="input-group">
                    <input type="file" name="vertical_ad" id="file_vertical_ad" accept="image/png" style="color: white;">
                    <?php if (file_exists('/var/www/html/vertical_ad.png')): ?>
                        <div class="mt-2 preview-container">
                            <img src="/vertical_ad.png" id="preview_vertical_ad" class="img-thumbnail">
                            <button type="button" onclick="prepareRemoval('file_vertical_ad', 'remove_vertical_ad', 'preview_vertical_ad')" style="background:none; border:none; color:#ff4d4d; cursor:pointer; font-size:12px; text-decoration:underline; margin-top:5px;">Remove Existing</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Horizontal Ad Section -->
            <div class="card wide">
                <h3>Horizontal Ad (1920x240)</h3>
                <div class="input-group">
                    <input type="file" name="horizontal_ad" id="file_horizontal_ad" accept="image/png" style="color: white;">
                    <?php if (file_exists('/var/www/html/horizontal_ad.png')): ?>
                        <div class="mt-2 preview-container">
                            <img src="/horizontal_ad.png" id="preview_horizontal_ad" class="img-thumbnail">
                            <button type="button" onclick="prepareRemoval('file_horizontal_ad', 'remove_horizontal_ad', 'preview_horizontal_ad')" style="background:none; border:none; color:#ff4d4d; cursor:pointer; font-size:12px; text-decoration:underline; margin-top:5px;">Remove Existing</string>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="text-align:center; width:100%; margin-top:12px;">
            <button type="submit" name="submit" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Update All Images</button>
        </div>
    </div>
</form>

<?php include 'footer.php'; ?>