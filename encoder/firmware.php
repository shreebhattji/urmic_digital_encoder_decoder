<?php

exec("sudo chmod 444 /sys/class/dmi/id/product_uuid");
$version = 1.0;

$device_id = trim(file_get_contents('/sys/class/dmi/id/product_uuid'));

$publicKey = "-----BEGIN PUBLIC KEY-----
MIIEIjANBgkqhkiG9w0BAQEFAAOCBA8AMIIECgKCBAEAm6glpTALuc82R+9Mqb5f
HVRC5dScc7USgWoIsYN1tF8YE0d7rhSIFvayVabiyabVmWscjAIlmYf6InSlLsDx
avfmapg32ECd92H49ZbsvXQpLqasyOkN7z6FUcuQ6pEMqfPBmBXKGngHazPp420o
Iki9hLc7IE9EMlDHfozckuJI8mB+bsd2oqua6SSTBYx5HYuSCbootf9GliSd7PVk
H6uir88j49/NFfvmrReicFBiMba959uOdBIhWl9AveZL+iI2NdS5SPw6eWltaMot
PSk7/5Z4Vn5Od7sQA0yUqmCj5XNV5EzRlP1jhP7SDv0D6Mpdf3HuKWdCBqepJBe2
rCHPQ9KrChQau4eEbJb5LIE3gFDpLxTHk9FEp+50evkpFONj0aAjSb3P4wsEGiOk
95Tm56gDRirnbbw/6SzhE7pEvXRUfMl1KO6maYK1z7KNMgEH99C2zCjZhpaXL5Io
rywCw009zoMT2qdKPMGOyQ4KPlCLCJYSF0y/rE07WNgl4BupVZR41B5MbXL5L7+X
OD0jBpbWI7v2ChP9rn5u6Lqpq6ewvc2RJO8lrAyZtzrNJNZNYmUKHrm7qAHJeJZX
Zh7OEf9U/T9JBpzf8l0MzyywlQGUBzb/niG0iZILt3XIpD+Xeyrr6hr+nabeiKXV
jHyUcG84zzLjv7sREzWEGoLBrdztMy69rbfd3d0DpjS90xceKZYBDd3vwjn6h0TN
KssqUb7BMH+zkCe/LQg6EGdXB13+xUSUjFKLLeBKu1VxMPfd/WmV1QumOodidvee
rQAv6yMevq2hVFkiFo7CUpaRv6dvQnQaqX2rHFKZY6zEIzbJXTznl6ZMtCcmcZMk
CYcoWZIAUR5tFP221XzIfJmymVRfJGiKTvt+g/SUUFJt6mq8ettu11XS4KSIxtaA
l8q2SSxpRQa80NUuaBpQc/3eP293wgcf/EOfzhCjxDLjsHSKV1AkSMyjvCzSsCdG
mMEIuT/D7PB7N8vlfhn5qsyt1Sm81/1EZ3u8UqToELhe8j7G26GVl/8ptSxofvZE
X0goYwW18PPhtZvkR8CXpZ7qwjqDcL5cQzcCldufjtqJ5GAwN6SrcmnYjQoo2cu9
XlWo0InPE8BpjR7vJpKLbppQzwUs9GQYx2bMSTbsrduc8zDXlPT5aOfgkJui/NQa
uxttvsXqXd3nNJhbO0BN+wCDT0j4LNRvMlJloWEGrBkY4SA5I1MX8XBL34Csy6Bu
bHWxXNBAGYMchcJKly7XN2hA61V4QCCiFz/MP9l1llw/Mk4D5IUTxcfcEDHx7LO0
To+pc5kuXS6Aps6lKJdwv6h0Bi9SWtBpFi2RtpQpAc+dVPQ9lwq3VTJV5GZz3AgV
KQIDAQAB
-----END PUBLIC KEY-----
";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action']) {
        case 'update':

            $payload['device_id'] = $device_id;
            $payload['project_id'] = "28f27590923d962388f0da125553c5";
            $payload['version'] = 1;
            $payload = json_encode($payload, JSON_UNESCAPED_UNICODE);

            openssl_public_encrypt(
                $payload,
                $encrypted,
                $publicKey,
                OPENSSL_PKCS1_OAEP_PADDING
            );

            $postData = [
                'encrypted' => base64_encode($encrypted)
            ];

            $ch = curl_init('https://account.urmic.org/encoder/update.php');
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $postData,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);

            echo '<script>alert("'
                . htmlspecialchars($data['message'], ENT_QUOTES)
                . '");</script>';
            break;
        case 'reset':
            $files = glob('/var/www/encoder/*.json');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== 'critical.json') {
                    unlink($file);
                }
            }
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
            $jsonFiles = [
                'input.json',
                'output.json',
                'firewall.json',
                'network.json',
                'firmware.json',
            ];

            foreach ($jsonFiles as $json) {
                if (file_exists($json)) {
                    unlink($json);
                }
            }

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
            update_service("display");
            update_service("rtmp0");
            update_service("rtmp1");
            update_service("udp0");
            update_service("udp1");
            update_service("udp2");
            update_service("srt");
            update_service("custom");
            update_service("input");
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
            Software Version :- 1 <br>
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
            <form method="post" class="form-center">
                <button type="submit" name="action" value="update" class="red-btn">Update Firmware</button>
            </form>
        </div>
        <div class="card wide">
            <form method="post" class="form-center" enctype="multipart/form-data"
                onsubmit="return confirm('Are you sure you want to restore using this file ? All settings will be restored as per backup file .')">

                <label>Select restore file (.bin only):</label><br><br>

                <input type="file"
                    name="shree_bhattji_encoder"
                    accept=".bin"
                    required><br><br>

                <button type="submit" name="action" value="restore" class="red-btn">Restore</button>

            </form>
        </div>
        <br>
    </div>
    <br>
</div>
<br><br>

<?php include 'footer.php'; ?>