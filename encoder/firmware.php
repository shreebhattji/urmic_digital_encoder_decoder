<?php
exec("sudo chmod 444 /sys/class/dmi/id/product_uuid");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        case 'backup':

            $jsonFiles = [
                'input.json',
                'output.json',
                'firewall.json',
                'network.json',
                'firmware.json',
            ];

            $tmpZip = sys_get_temp_dir() . '/backup.zip';
            $outputFile = __DIR__ . '/universal_encoder_decoder.bin';

            $publicKey = file_get_contents('/var/www/backup_private.pem');
            $publicKey = file_get_contents('/var/www/backup_public.pem');

            $zip = new ZipArchive();
            $zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);


            /* Add JSON files if exist */
            foreach ($jsonFiles as $json) {
                if (file_exists($json)) {
                    $zip->addFile($json, basename($json));
                }
            }

            $zip->close();
            $data = file_get_contents($tmpZip);

            /* Generate AES key */
            $aesKey = random_bytes(32);
            $iv     = random_bytes(16);

            /* Encrypt ZIP */
            $encryptedData = openssl_encrypt(
                $data,
                'AES-256-CBC',
                $aesKey,
                OPENSSL_RAW_DATA,
                $iv
            );

            /* Encrypt AES key using RSA public key */
            openssl_public_encrypt($aesKey, $encryptedKey, $publicKey);

            /* Final binary format */
            $payload = json_encode([
                'key' => base64_encode($encryptedKey),
                'iv'  => base64_encode($iv),
                'data' => base64_encode($encryptedData)
            ]);

            $filename = 'universal_encoder_decoder.bin';

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($payload));
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            echo $payload;
            flush();

            unlink($tmpZip);

            break;

        case 'restore':
            $inputFile  = __DIR__ . '/universal_encoder_decoder.bin';
            $restoreDir = __DIR__ . '/var/www/encoder/';
            $tmpZip     = sys_get_temp_dir() . '/restore.zip';

            $upload = $_FILES['shree_bhattji_encoder'];

            if ($upload['error'] !== UPLOAD_ERR_OK) {
                die('Upload failed');
            }

            if (pathinfo($upload['name'], PATHINFO_EXTENSION) !== 'bin') {
                die('Invalid file type');
            }

            $privateKeyPem = file_get_contents('/var/www/backup_private.pem');
            if (!$privateKeyPem) {
                die('Private key not found');
            }

            $privateKey = openssl_pkey_get_private($privateKeyPem);
            if (!$privateKey) {
                die('Invalid private key');
            }

            $payloadRaw = file_get_contents($upload['tmp_name']);
            $payload    = json_decode($payloadRaw, true);

            if (
                !is_array($payload)
                || !isset($payload['key'], $payload['iv'], $payload['data'])
            ) {
                die('Invalid backup file format');
            }

            $encryptedKey  = base64_decode($payload['key'], true);
            $iv            = base64_decode($payload['iv'], true);
            $encryptedData = base64_decode($payload['data'], true);

            if ($encryptedKey === false || $iv === false || $encryptedData === false) {
                die('Corrupt backup data');
            }

            if (!openssl_private_decrypt($encryptedKey, $aesKey, $privateKey)) {
                die('Key mismatch or wrong private key');
            }

            $zipBinary = openssl_decrypt(
                $encryptedData,
                'AES-256-CBC',
                $aesKey,
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($zipBinary === false) {
                die('Failed to decrypt data');
            }
            $tmpZip = sys_get_temp_dir() . '/restore_' . uniqid() . '.zip';
            file_put_contents($tmpZip, $zipBinary);

            $zip = new ZipArchive();
            if ($zip->open($tmpZip) !== true) {
                unlink($tmpZip);
                die('Invalid ZIP archive');
            }

            $zip->extractTo(__DIR__);   // overwrites existing JSON
            $zip->close();

            unlink($tmpZip);
            break;
    }
}
include 'header.php';

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
        return confirm("Are you sure you want to download backup ? ");
    }
</script>


<div class="containerindex">
    <div class="grid">
        <div class="card wide">
            Device ID :- <?php echo trim(file_get_contents('/sys/class/dmi/id/product_uuid')); ?><br>
            Project Name :- URMI Universal Encoder / Decoder<br>
        </div>
        <div class="card wide">
            <form method="post" class="form-center" enctype="multipart/form-data"
                onsubmit="return confirm('Are you sure you want to restore using this file ? All settings will be restored as per backup file .')">

                <label>Select restore file (.bin only):</label><br><br>

                <input type="file"
                    name="shree_bhattji_encoder"
                    accept=".bin"
                    required><br><br>

                <button type="submit" name="action" value="restore" class="green-btn">Restore</button>

            </form>
        </div>
        <div class="card wide">
            <form method="post" class="form-center">
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
                <button type="submit" name="action" value="update" class="red-btn">Update Firmware</button>
            </form>
        </div>
        <br>
    </div>
    <br>
</div>
<br><br>

<?php include 'footer.php'; ?>