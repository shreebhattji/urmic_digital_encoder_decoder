<?php
// firewall-form-handler.php
$jsonFile = __DIR__ . '/firewall.json';

// defaults
$defaults = [
    'firewall' => 'disable',
    'ips' => ['', '', '', '', '']
];

// load existing data
if (file_exists($jsonFile)) {
    $raw = file_get_contents($jsonFile);
    $data = json_decode($raw, true);
    if (!is_array($data)) $data = $defaults;
} else {
    $data = $defaults;
}

$errors = [];
$saveSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // read posted values
    $posted_firewall = isset($_POST['firewall']) && in_array($_POST['firewall'], ['enable', 'disable']) ? $_POST['firewall'] : 'disable';
    $posted_ips = [];
    for ($i = 1; $i <= 5; $i++) {
        $key = "ip{$i}";
        $val = isset($_POST[$key]) ? trim((string)$_POST[$key]) : '';
        $posted_ips[] = $val;
    }

    // validate IPs (allow empty)
    foreach ($posted_ips as $index => $ip) {
        if ($ip !== '' && !filter_var($ip, FILTER_VALIDATE_IP)) {
            $errors[] = "IP" . ($index + 1) . " is not a valid IP address: " . htmlspecialchars($ip);
        }
    }

    if (empty($errors)) {
        $new = [
            'firewall' => $posted_firewall,
            'ips' => $posted_ips
        ];
        $json = json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if (file_put_contents($jsonFile, $json, LOCK_EX) === false) {
            $errors[] = "Failed to write {$jsonFile}. Check permissions.";
        } else {
            $data = $new;
            $saveSuccess = "Saved.";
        }
    }
}
?>

<?php include 'header.php'; ?>
<form method="POST" novalidate>
    <div class="containerindex">
        <div class="grid">
            <div class="card wide">
                <h3>Firewall</h3>
                <br>
                <div class="input-container">
                    <input type="radio" id="firewall_enable" name="firewall" value="enable" <?php if ($data['firewall'] === 'enable') echo 'checked'; ?>>
                    <label for="firewall_enable">Enable</label><br>

                    <input type="radio" id="firewall_disable" name="firewall" value="disable" <?php if ($data['firewall'] === 'disable') echo 'checked'; ?>>
                    <label for="firewall_disable">Disable</label><br>
                </div>
                <br>
                <br>
                <br>
                <div class="input-group">
                    <input type="text" id="ip1" name="ip1" placeholder="IP1" value="<?php echo htmlspecialchars($data['ips'][0] ?? ''); ?>">
                    <label for="ip1">IP1</label>
                </div>
                <div class="input-group">
                    <input type="text" id="ip2" name="ip2" placeholder="IP2" value="<?php echo htmlspecialchars($data['ips'][1] ?? ''); ?>">
                    <label for="ip2">IP2</label>
                </div>
                <div class="input-group">
                    <input type="text" id="ip3" name="ip3" placeholder="IP3" value="<?php echo htmlspecialchars($data['ips'][2] ?? ''); ?>">
                    <label for="ip3">IP3</label>
                </div>
                <div class="input-group">
                    <input type="text" id="ip4" name="ip4" placeholder="IP4" value="<?php echo htmlspecialchars($data['ips'][3] ?? ''); ?>">
                    <label for="ip4">IP4</label>
                </div>
                <div class="input-group">
                    <input type="text" id="ip5" name="ip5" placeholder="IP5" value="<?php echo htmlspecialchars($data['ips'][4] ?? ''); ?>">
                    <label for="ip5">IP5</label>
                </div>

                <div style="text-align:center; width:100%; margin-top:12px;">
                    <button type="submit" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save</button>
                </div>

            </div>
        </div>
    </div>
    <br>
    <br>
</form>

<?php
// status output
if (!empty($errors)) {
    echo '<div style="color:#b00;text-align:center;margin-top:12px;">';
    foreach ($errors as $e) echo '<div>' . $e . '</div>';
    echo '</div>';
}
if ($saveSuccess) {
    echo '<div style="color:green;text-align:center;margin-top:12px;">' . htmlspecialchars($saveSuccess) . '</div>';
}
?>

<?php include 'footer.php'; ?>