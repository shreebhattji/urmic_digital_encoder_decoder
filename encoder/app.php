<?php
/*
Urmi you happy me happy licence

Copyright (c) 2026 shreebhattji

License text:
https://github.com/shreebhatt_ji/Urmi/blob/main/licence.md
*/

include 'header.php';

$json_file = '/var/www/html/app.json';
$saved_data = [];

if (file_exists($json_file)) {
    $json_content = file_get_contents($json_file);
    $saved_data = json_decode($json_content, true) ?? [];
}

function getValue($data, $key)
{
    return $data[$key] ?? '';
}
?>

<form action="" method="POST" enctype="multipart/form-data">
    <div class="containerindex">
        <div class="grid">
            <h2 style="text-align: center; color: white; margin-bottom: 20px;">Company Information Entry</h2>

            <!-- Channel Details -->
            <div class="card wide">
                <div class="input-group">
                    <input type="text" name="channel_name" value="<?php echo htmlspecialchars(getValue($saved_data, 'channel_name')); ?>" >
                    <label for="channel:channel_name">Channel Name</label>
                </div>
                <div class="input-group">
                    <input type="text" name="office_address" value="<?php echo htmlspecialchars(getValue($saved_data, 'office_address')); ?>" >
                    <label for="office_address" style="position: static; margin-top: 5px;">Office Address</label>
                </div>
                <div class="input-group">
                    <input type="text" name="contact_details" value="<?php echo htmlspecialchars(getValue($saved_data, 'contact_details')); ?>" >
                    <label for="contact_details">Contact Details</label>
                </div>
                <div class="input-group">
                    <input type="text" name="enforcement_officer" value="<?php echo htmlspecialchars(getValue($saved_data, 'enforcement_officer')); ?>" >
                    <label for="enforcement_officer">Enforcement Officer</label>
                </div>
                <div class="input-group">
                    <input type="text" name="eo_contact_details" value="<?php echo htmlspecialchars(getValue($saved_data, 'eo_contact_details')); ?>" >
                    <label for="eo_contact_details">EO Contact Details</label>
                </div>
                <div class="input-group">
                    <input type="text" name="company_name" value="<?php echo htmlspecialchars(getValue($saved_data, 'company_name')); ?>" >
                    <label for="company_name">Company Name</label>
                </div>
                <div class="input-group">
                    <input type="text" name="cin_number" value="<?php echo htmlspecialchars(getValue($saved_data, 'cin_number')); ?>" >
                    <label for="cin_number">CIN Number</label>
                </div>
                <div class="input-group">
                    <input type="text" name="gstin_number" value="<?php echo htmlspecialchars(getValue($saved_data, 'gstin_number')); ?>" >
                    <label for="gstin_number">GSTIN Number</label>
                </div>
            </div>


            <div class="card wide">
                <h3 style="margin-bottom: 15px;">Upload Ad (PNG)</h3>
                <div class="input-group">
                    <input type="file" name="app_ad" accept="image/png" style="color: white;">
                    <?php if (file_exists('/var/www/html/app_ad.png')): ?>
                        <div class="mt-2"><small style="color: #aaa;">Current:</small><br><img src="/var/www/html/app_ad.png" class="img-thumbnail" style="max-height: 60px; opacity: 0.7; margin-top: 5px;"></div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="card wide">
                <h3 style="margin-bottom: 15px;">Upload Logo (PNG)</h3>
                <div class="input-group">
                    <input type="file" name="app_logo" accept="image/png" style="color: white;">
                    <?php if (file_exists('/var/www/html/app_logo.png')): ?>
                        <div class="mt-2"><small style="color: #aaa;">Current:</small><br><img src="/var/www/html/app_logo.png" class="img-thumbnail" style="max-height: 60px; opacity: 0.7; margin-top: 5px;"></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="text-align:center; width:100%; margin-top:20px; margin-bottom: 40px;">
            <button type="submit" name="submit" style="background:#c00;color:#fff;padding:12px 60px;border:none;font-weight:bold;border-radius:6px;cursor:pointer;font-size:16px;">Save All Details</button>
        </div>
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

        if (empty($errors)) {
            if (file_put_contents($json_file, json_encode($data_to_save, JSON_PRETTY_PRINT))) {
                echo '<div class="alert alert-success text-center" style="color: #22c55e; text-align: center;">All data and files processed successfully!</div>';
                echo "<script>setTimeout(() => { window.location.reload(); }, 2000);</script>";
            } else {
                $errors[] = "Failed to save JSON data.";
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger text-center' style='color: #ef4444; text-align: center;'>$error</div>";
            }
        }
    }
    ?>
</div>

<?php include 'footer.php'; ?>