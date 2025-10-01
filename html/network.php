<?php
// network-form-handler.php
$jsonFile = __DIR__ . '/network.json';
$iface = trim(shell_exec("ip route get 1.1.1.1 | awk '{print $5; exit}'"));

$defaults = [
    'primary' => [
        'mode' => 'primary_dhcp', // primary_static or primary_dhcp
        'ip' => '',
        'subnet' => '',
        'gateway' => '',
        'vlan' => ''
    ],
    'secondary' => [
        'mode' => 'secondary_disable', // secondary_static or secondary_dhcp
        'ip' => '',
        'subnet' => '',
        'gateway' => '',
        'vlan' => ''
    ]
];

// load existing
if (file_exists($jsonFile)) {
    $raw = file_get_contents($jsonFile);
    $data = json_decode($raw, true);
    if (!is_array($data)) $data = $defaults;
} else {
    $data = $defaults;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // helper
    $get = function ($k) {
        return isset($_POST[$k]) ? trim((string)$_POST[$k]) : '';
    };

    $primary_mode = in_array($get('primary_mode'), ['primary_static', 'primary_dhcp']) ? $get('primary_mode') : 'primary_dhcp';
    $secondary_mode = in_array($get('secondary_mode'), ['secondary_static', 'secondary_dhcp','secondary_disable']) ? $get('secondary_mode') : 'secondary_dhcp';

    $primary_ip = $get('network_primary_ip');
    $primary_subnet = $get('network_primary_subnet');
    $primary_gateway = $get('network_primary_gateway');
    $primary_vlan = $get('network_primary_vlan');

    $secondary_ip = $get('network_secondary_ip');
    $secondary_subnet = $get('network_secondary_subnet');
    $secondary_gateway = $get('network_secondary_gateway');
    $secondary_vlan = $get('network_secondary_vlan');

    // Validate IPs (allow empty). Subnet accepted as IP or CIDR like 192.168.1.0/24
    $validate_ip_or_cidr = function ($v) {
        if ($v === '') return true;
        if (strpos($v, '/') !== false) {
            [$ip, $cidr] = explode('/', $v, 2);
            return filter_var($ip, FILTER_VALIDATE_IP) !== false && ctype_digit($cidr) && (int)$cidr >= 0 && (int)$cidr <= 32;
        }
        return filter_var($v, FILTER_VALIDATE_IP) !== false;
    };

    foreach (
        [
            ['field' => 'Primary IP', 'value' => $primary_ip],
            ['field' => 'Primary Subnet', 'value' => $primary_subnet],
            ['field' => 'Primary Gateway', 'value' => $primary_gateway],
            ['field' => 'Secondary IP', 'value' => $secondary_ip],
            ['field' => 'Secondary Subnet', 'value' => $secondary_subnet],
            ['field' => 'Secondary Gateway', 'value' => $secondary_gateway],
        ] as $f
    ) {
        if ($f['value'] !== '') {
            $ok = $f['field'] === 'Primary Subnet' || $f['field'] === 'Secondary Subnet'
                ? $validate_ip_or_cidr($f['value'])
                : filter_var($f['value'], FILTER_VALIDATE_IP) !== false;
            if (!$ok) $errors[] = $f['field'] . ' is invalid: ' . htmlspecialchars($f['value']);
        }
    }

    // VLAN numeric check (allow empty)
    foreach ([['Primary VLAN', $primary_vlan], ['Secondary VLAN', $secondary_vlan]] as $v) {
        if ($v[1] !== '' && (!ctype_digit($v[1]) || (int)$v[1] < 0 || (int)$v[1] > 4094)) {
            $errors[] = $v[0] . ' must be a number 0-4094';
        }
    }

    if (empty($errors)) {
        $new = [
            'primary' => [
                'mode' => $primary_mode,
                'ip' => $primary_ip,
                'subnet' => $primary_subnet,
                'gateway' => $primary_gateway,
                'vlan' => $primary_vlan
            ],
            'secondary' => [
                'mode' => $secondary_mode,
                'ip' => $secondary_ip,
                'subnet' => $secondary_subnet,
                'gateway' => $secondary_gateway,
                'vlan' => $secondary_vlan
            ]
        ];
        $json = json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if (file_put_contents($jsonFile, $json, LOCK_EX) === false) {
            $errors[] = "Failed to write {$jsonFile}. Check permissions.";
        } else {
            $data = $new;
            $success = 'Saved.';
        }
    }
}
?>

<?php include 'header.php'; ?>
<form method="POST" novalidate>
    <div class="containerindex">
        <div class="grid">
            <div class="card wide">
                <h3>Primary Interface</h3>
                <br>
                <div class="input-container">
                    <input type="radio" id="primary_static" name="primary_mode" value="primary_static" <?php if ($data['primary']['mode'] === 'primary_static') echo 'checked'; ?>>
                    <label for="primary_static">STATIC</label><br>

                    <input type="radio" id="primary_dhcp" name="primary_mode" value="primary_dhcp" <?php if ($data['primary']['mode'] === 'primary_dhcp') echo 'checked'; ?>>
                    <label for="primary_dhcp">DHCP</label><br>
                </div>
                <br>
                <div class="input-group">
                    <input type="text" id="network_primary_ip" name="network_primary_ip" placeholder="Address" value="<?php echo htmlspecialchars($data['primary']['ip']); ?>">
                    <label for="network_primary_ip">Address</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_primary_subnet" name="network_primary_subnet" placeholder="Subnet (e.g. 192.168.1.0/24)" value="<?php echo htmlspecialchars($data['primary']['subnet']); ?>">
                    <label for="network_primary_subnet">Subnet</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_primary_gateway" name="network_primary_gateway" placeholder="Gateway" value="<?php echo htmlspecialchars($data['primary']['gateway']); ?>">
                    <label for="network_primary_gateway">Gateway</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_primary_vlan" name="network_primary_vlan" placeholder="Vlan" value="<?php echo htmlspecialchars($data['primary']['vlan']); ?>">
                    <label for="network_primary_vlan">Vlan</label>
                </div>
            </div>

            <div class="card wide">
                <h3>Secondary Vlan Interface</h3>
                <br>
                <div class="input-container">
                    <input type="radio" id="secondary_static" name="secondary_mode" value="secondary_static" <?php if ($data['secondary']['mode'] === 'secondary_static') echo 'checked'; ?>>
                    <label for="secondary_static">STATIC</label><br>

                    <input type="radio" id="secondary_dhcp" name="secondary_mode" value="secondary_dhcp" <?php if ($data['secondary']['mode'] === 'secondary_dhcp') echo 'checked'; ?>>
                    <label for="secondary_dhcp">DHCP</label><br>

                    <input type="radio" id="secondary_disable" name="secondary_mode" value="secondary_disable" <?php if ($data['secondary']['mode'] === 'secondary_disable') echo 'checked'; ?>>
                    <label for="secondary_disable">Disable</label><br>
                </div>
                <br>
                <div class="input-group">
                    <input type="text" id="network_secondary_ip" name="network_secondary_ip" placeholder="Address" value="<?php echo htmlspecialchars($data['secondary']['ip']); ?>">
                    <label for="network_secondary_ip">Address</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_secondary_subnet" name="network_secondary_subnet" placeholder="Subnet (e.g. 10.0.0.0/24)" value="<?php echo htmlspecialchars($data['secondary']['subnet']); ?>">
                    <label for="network_secondary_subnet">Subnet</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_secondary_gateway" name="network_secondary_gateway" placeholder="Gateway" value="<?php echo htmlspecialchars($data['secondary']['gateway']); ?>">
                    <label for="network_secondary_gateway">Gateway</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_secondary_vlan" name="network_secondary_vlan" placeholder="Vlan" value="<?php echo htmlspecialchars($data['secondary']['vlan']); ?>">
                    <label for="network_secondary_vlan">Vlan</label>
                </div>
            </div>
        </div>
    </div>

    <div style="text-align:center; width:100%; margin-top:12px;">
        <button type="submit" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save</button>
    </div>
</form>

<?php
if (!empty($errors)) {
    echo '<div style="color:#b00;text-align:center;margin-top:12px;">';
    foreach ($errors as $e) echo '<div>' . $e . '</div>';
    echo '</div>';
}
if ($success) {
    echo '<div style="color:green;text-align:center;margin-top:12px;">' . htmlspecialchars($success) . '</div>';
}
?>

<?php include 'footer.php'; ?>