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
    exec("sudo chmod 444 /sys/class/dmi/id/product_uuid");
    $errors = [];

    // Dynamically build the list based on what is actually posted
    $data_to_save = [];
    foreach ($_POST as $key => $value) {
        if ($key !== 'submit') {
            $data_to_save[$key] = $value;
        }
    }

    $data_to_save['device_id'] = trim(file_get_contents('/sys/class/dmi/id/product_uuid'));

    // Save if no errors occurred
    if (empty($errors)) {
        if (file_put_contents($json_file, json_encode($data_to_save, JSON_PRETTY_PRINT)) === false) {
            $errors[] = "Failed to write to file. Check permissions.";
        }
    }

    if (!empty($errors)) {
        $error_message = implode("<br>", $errors);
    } else {
        // Redirect to prevent form resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
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

<form method="POST">
    <div class="containerindex">
        <div class="grid">
            <h2 style="grid-column: span 2;">Broadcasting Information</h2>

            <?php if (!empty($error_message)): ?>
                <div style="background: #ff0000; color: white; padding: 15px; border-radius: 6px; margin-bottom: 20px; grid-column: span 2;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Company Details -->
            <div class="card wide">
                <h3 style="margin-top:0;">General Details</h3>
                <div class="input-wrap">
                    <div class="input-group">
                        <input type="text" name="channel_name" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'channel_name')); ?>" required>
                        <label>Channel Name</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="office_address" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'office_address')); ?>" required>
                        <label>Office Address</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="office_address_1" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'office_address_1')); ?>" required>
                        <label>Office Address 1</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="office_address_2" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'office_address_2')); ?>" required>
                        <label>Office Address 2</label>
                    </div>
                </div>
            </div>
            <div class="card wide">
                <h3 style="margin-top:0;">Contact Details</h3>

                <!-- Contact 1 -->
                <div class="input-group">
                    <input type="text" name="contact_name_1" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'contact_name_1')); ?>" required>
                    <label>Contact Name 1</label>
                </div>
                <div class="input-group">
                    <input type="text" name="contact_number_1" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'contact_number_1')); ?>" required>
                    <label>Contact Number 1</label>
                </div>

                <!-- Contact 2 -->
                <div class="input-group">
                    <input type="text" name="contact_name_2" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'contact_name_2')); ?>">
                    <label>Contact Name 2</label>
                </div>
                <div class="input-group">
                    <input type="text" name="contact_number_2" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'contact_number_2')); ?>">
                    <label>Contact Number 2</label>
                </div>

                <!-- Contact 3 -->
                <div class="input-group">
                    <input type="text" name="contact_name_3" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'contact_name_3')); ?>">
                    <label>Contact Name 3</label>
                </div>
                <div class="input-group">
                    <input type="text" name="contact_number_3" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'contact_number_3')); ?>">
                    <label>Contact Number 3</label>
                </div>
            </div>
            <div class="card wide">
                <h3 style="margin-top:0;">Enforcement</h3>

                <div class="input-group">
                    <input type="text" name="enforcement_officer" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'enforcement_officer')); ?>" required>
                    <label>Enforcement Officer</label>
                </div>
                <div class="input-group">
                    <input type="text" name="enforcement_officer_contact" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'enforcement_officer_contact')); ?>" required>
                    <label>EO Contact Details</label>
                </div>
            </div>
            <div class="card wide">
                <h3 style="margin-top:0;">Company Details</h3>
                <div class="input-group">
                    <input type="text" name="company_name" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'company_name')); ?>" required>
                    <label>Company Name</label>
                </div>
                <div class="input-group">
                    <input type="text" name="cin_number" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'cin_number')); ?>">
                    <label>CIN Number</label>
                </div>
                <div class="input-group">
                    <input type="text" name="gstin_number" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'gstin_number')); ?>">
                    <label>GSTIN Number</label>
                </div>
            </div>

            <div class="card wide">
                <h3 style="margin-top:0;">Content Details</h3>
                <div class="grid">
                    <div class="dropdown-container">
                        <span class="dropdown-label">Content Rating</span>
                        <div class="dropdown">
                            <select name="content_rating" id="content_rating">
                                <?php
                                $ratings = ['`U` ( Universal )' => 'Universal', '`UA` ( Parental Guidance )' => 'Parental Guidance', '`A` ( Adults Only )' => 'Adults Only', '`S` ( Special )' => 'Special'];
                                foreach ($ratings as $code => $desc) {
                                    $selected = (getValue($saved_data, 'content_rating') === $code) ? 'selected' : '';
                                    echo "<option value=\"" . htmlspecialchars($code) . "\" $selected>" . htmlspecialchars($code) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="dropdown-container">
                        <span class="dropdown-label">Content Type</span>
                        <div class="dropdown">
                            <select name="content_type" id="content_type">
                                <?php
                                $types = ['News & Information', 'Entertainment', 'Religious & Spiritual', 'Sports & Recreation'];
                                foreach ($types as $desc) {
                                    $selected = (getValue($saved_data, 'content_type') === $desc) ? 'selected' : '';
                                    echo "<option value=\"" . htmlspecialchars($desc) . "\" $selected>" . htmlspecialchars($desc) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="input-group">
                        <input type="text" name="content_language" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'content_language')); ?>" required>
                        <label>Content Language</label>
                    </div>
                </div>
            </div>

            <!-- External Links Card -->
            <div class="card wide">
                <h3 style="margin-top:0;">External Links</h3>
                <div class="input-wrap">
                    <div class="input-group">
                        <input type="text" name="website_url" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'website_url')); ?>">
                        <label>Website URL</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="facebook_url" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'facebook_url')); ?>">
                        <label>Facebook URL</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="youtube_url" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'youtube_url')); ?>">
                        <label>YouTube URL</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="telegram_url" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'telegram_url')); ?>">
                        <label>Telegram URL</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="linkedin_url" placeholder=" " value="<?php echo htmlspecialchars(getValue($saved_data, 'linkedin_url')); ?>">
                        <label>LinkedIn URL</label>
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align:center; width:100%; margin: 40px 0;">
            <button type="submit" name="submit" style="background:#c00;color:#fff;padding:15px 80px;border:none;font-weight:bold;border-radius:6px;cursor:pointer;font-size:18px; transition: 0.3s;">Save All Details</button>
        </div>
    </div>
</form>

<?php include 'footer.php'; ?>