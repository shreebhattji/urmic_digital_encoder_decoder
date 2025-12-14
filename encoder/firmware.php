<?php

include 'header.php';

switch ($_POST['action']) {
    case 'update':
        update_firmware();
        break;
    case 'reset':
        $files = glob('/var/www/html/*.json');
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== 'critical.json') {
                unlink($file);
            }
        }
        break;
    case 'reboot':
        exec('sudo reboot');
        break;
}
?>

<div class="containerindex">
    <div class="grid">
        <div class="card wide">
            Currunt Firmware Version :- 1.0
        </div>
        <div class="card wide">
            <form method="post" class="form-center">
                <button type="submit" name="action" value="reboot" class="blueviolet-btn">Reboot</button>
            </form>
        </div>
        <div class="card wide">
            <form method="post" class="form-center">
                <button type="submit" name="action" value="update" class="red-btn">Update or Reset Firmware</button>
            </form>
        </div>
        <div class="card wide">
            <form method="post" class="form-center" onsubmit="return confirm('Are you sure you want to reset all settings?');">
                <button type="submit" name="action" value="reset" class="red-btn">Reset Settings</button>
            </form>
        </div>
        <br>
    </div>
    <br>
</div>
<br><br>

<?php include 'footer.php'; ?>