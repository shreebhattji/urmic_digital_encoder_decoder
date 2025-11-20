<?php

include 'header.php';
$url = "https://git.dbhatt.org/ShreeBhattJi/digital_encoder/raw/branch/main/version.json";

$json = file_get_contents($url);
if ($json === false) {
    die("Failed to fetch JSON");
}

// Decode
$data = json_decode($json, true);
if ($data === null) {
    die("Failed to decode JSON");
}

// Access "version"
if (isset($data['version'])) {
    $version = $data['version'];
} else {
    $version =  "Key 'version' not found";
}

if ($_POST['action'] === 'update') {
    update_firmware();
} elseif ($_POST['action'] === 'reset') {
    $files = glob('/var/www/html/*.json');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== 'critical.json') {
            unlink($file);
        }
    }
}
?>

<div class="containerindex">
    <div class="grid">
        <div class="card wide">
            Currunt Firmware Version :- 1.0
        </div>
        <div class="card wide">
            Latest Firmware Version :- <?php echo $version ?>
        </div>
        <div class="card wide">
            <form method="post" class="form-center">
                <button type="submit" name="action" value="update" class="red-btn">Update</button>
            </form>
        </div>
        <div class="card wide">
            <form method="post" class="form-center" onsubmit="return confirm('Are you sure you want to reset all settings?');">
                <button type="submit" name="action" value="reset" class="red-btn">Reset</button>
            </form>
        </div>
        <br>
    </div>
    <br>
</div>
<br><br>

<?php include 'footer.php'; ?>