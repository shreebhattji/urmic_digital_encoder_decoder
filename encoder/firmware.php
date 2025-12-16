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
        deleteDir('/var/www/encoder/setup');
        break;
    case 'reboot':
        exec('sudo reboot');
        break;
}

$board_id = trim(@file_get_contents('/sys/class/dmi/id/board_serial'));

?>
<script>
    function confirmReboot() {
        return confirm("Are you sure you want to reboot?");
    }

    function confirmReset() {
        return confirm("All settings will be gone . Are you sure you want to reset ?");
    }

    function confirmUpdate() {
        return confirm("Newer version will be downloaded and installed Do not turn off power , this is irreversible are you sure to continue ? ");
    }

    function confirmbackup() {
        return confirm("Newer version will be downloaded and installed Do not turn off power , this is irreversible are you sure to continue ? ");
    }
</script>


<div class="containerindex">
    <div class="grid">
        <div class="card wide">
            Device Licence Info :- <br>
            Device ID :- <?php global $board_id;
                            echo $board_id ?><br>
            Reseller ID :- <br>
            Project Name :- <br>
        </div>
        <div class="card wide">
            <form method="post" class="form-center" enctype="multipart/form-data"
                onsubmit="return confirm('Are you sure you want to restore using this file ? All settings will be restored as per backup file .')">

                <label>Select restore file (.bin only):</label><br><br>

                <input type="file"
                    name="shree_bhattji_encoder.bin"
                    accept=".bin"
                    required><br><br>

                <button type="submit" name="action" value="restore" class="green-btn">Restore</button>

            </form>
        </div>
        <div class="card wide">
            <form method="post" class="form-center" onsubmit="return confirmbackup();">
                <button type="submit" name="action" value="backup" class="green-btn">Download Backup File</button>
            </form>
        </div>
        <div class="card wide">
            <form method="post" class="form-center" onsubmit="return confirmReboot();">
                <button type="submit" name="action" value="reboot" class="green-btn">Reboot</button>
            </form>
        </div>
        <div class="card wide">
            <form method="post" class="form-center" onsubmit="return confirmReset();">
                <button type="submit" name="action" value="reset" class="red-btn">Reset Settings</button>
            </form>
        </div>
        <div class="card wide">
            <form method="post" class="form-center" onsubmit="return confirmUpdate();">
                <button type="submit" name="action" value="update" class="red-btn">Update or Reset Firmware</button>
            </form>
        </div>
        <br>
    </div>
    <br>
</div>
<br><br>

<?php include 'footer.php'; ?>