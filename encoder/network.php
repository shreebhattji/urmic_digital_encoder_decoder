<?php include 'header.php'; ?>
<?php

$jsonFile = __DIR__ . '/network.json';
$iface = find_first_physical_ethernet();

$defaults = [
    'primary' => [
        'mode' => 'dhcp',
        'modev6' => 'auto',
        'network_primary_ip' => '',
        'network_primary_gateway' => '',
        'network_primary_vlan' => '',
        'network_primary_dns1' => '',
        'network_primary_dns2' => '',
        'network_primary_ipv6' => '',
        'network_primary_ipv6_prefix' => '',
        'network_primary_ipv6_gateway' => '',
        'network_primary_ipv6_vlan' => '',
        'network_primary_ipv6_dns1' => '',
        'network_primary_ipv6_dns2' => '',
    ],
    'secondary' => [
        'mode' => 'disabled',
        'modev6' => 'disabled',
        'network_secondary_ip' => '',
        'network_secondary_gateway' => '',
        'network_secondary_vlan' => '',
        'network_secondary_dns1' => '',
        'network_secondary_dns2' => '',
        'network_secondary_ipv6' => '',
        'network_secondary_ipv6_prefix' => '',
        'network_secondary_ipv6_gateway' => '',
        'network_secondary_ipv6_vlan' => '',
        'network_secondary_ipv6_dns1' => '',
        'network_secondary_ipv6_dns2' => '',
    ],
];

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
    $get = function ($k) {
        return isset($_POST[$k]) ? trim((string)$_POST[$k]) : '';
    };

    $primary_mode = in_array($get('primary_mode'), ['dhcp', 'static', 'disabled']) ? $get('primary_mode') : 'dhcp';
    $primary_modev6 = in_array($get('primary_mode'), ['auto', 'dhcpv6', 'static', 'disabled']) ? $get('primary_mode') : 'auto';
    $secondary_mode = in_array($get('secondary_mode'), ['dhcp', 'static', 'disabled']) ? $get('secondary_mode') : 'dhcp';
    $secondary_modev6 = in_array($get('secondary_mode'), ['auto', 'dhcpv6', 'static', 'disabled']) ? $get('secondary_mode') : 'auto';

    $network_primary_ip = $get('network_primary_ip');
    $network_primary_gateway = $get('network_primary_gateway');
    $network_primary_vlan = $get('network_primary_vlan');
    $network_primary_dns1 = $get('network_primary_dns1');
    $network_primary_dns2 = $get('network_primary_dns2');
    $network_primary_ipv6 = $get('network_primary_ipv6');
    $network_primary_ipv6_prefix = $get('network_primary_ipv6_prefix');
    $network_primary_ipv6_gateway = $get('network_primary_ipv6_gateway');
    $network_primary_ipv6_vlan = $get('network_primary_ipv6_vlan');
    $network_primary_ipv6_dns1 = $get('network_primary_ipv6_dns1');
    $network_primary_ipv6_dns2 = $get('network_primary_ipv6_dns2');

    $network_secondary_ip = $get('network_secondary_ip');
    $network_secondary_subnet = $get('network_secondary_subnet');
    $network_secondary_gateway = $get('network_secondary_gateway');
    $network_secondary_vlan = $get('network_secondary_vlan');
    $network_secondary_dns1 = $get('network_secondary_dns1');
    $network_secondary_dns2 = $get('network_secondary_dns2');
    $network_secondary_ipv6 = $get('network_secondary_ipv6');
    $network_secondary_ipv6_prefix = $get('network_secondary_ipv6_prefix');
    $network_secondary_ipv6_gateway = $get('network_secondary_ipv6_gateway');
    $network_secondary_ipv6_vlan = $get('network_secondary_ipv6_vlan');
    $network_secondary_ipv6_dns1 = $get('network_secondary_ipv6_dns1');
    $network_secondary_ipv6_dns2 = $get('network_secondary_ipv6_dns2');

    $new = [
        'primary' => [
            'mode' => 'dhcp',
            'modev6' => 'auto',
            'network_primary_ip' => $network_primary_ip,
            'network_primary_gateway' => $network_primary_gateway,
            'network_primary_vlan' => $network_primary_vlan,
            'network_primary_dns1' => $network_primary_dns1,
            'network_primary_dns2' => $network_primary_dns2,
            'network_primary_ipv6' => $network_primary_ipv6,
            'network_primary_ipv6_prefix' => $network_primary_ipv6_prefix,
            'network_primary_ipv6_gateway' => $network_primary_ipv6_gateway,
            'network_primary_ipv6_vlan' => $network_primary_ipv6_vlan,
            'network_primary_ipv6_dns1' => $network_primary_ipv6_dns1,
            'network_primary_ipv6_dns2' => $network_primary_ipv6_dns2
        ],
        'secondary' => [
            'mode' => 'disabled',
            'modev6' => 'disabled',
            'network_secondary_ip' => $network_secondary_ip,
            'network_secondary_gateway' => $network_secondary_gateway,
            'network_secondary_vlan' => $network_secondary_vlan,
            'network_secondary_dns1' => $network_secondary_dns1,
            'network_secondary_dns2' => $network_secondary_dns2,
            'network_secondary_ipv6' => $network_secondary_ipv6,
            'network_secondary_ipv6_prefix' => $network_secondary_ipv6_prefix,
            'network_secondary_ipv6_gateway' => $network_secondary_ipv6_gateway,
            'network_secondary_ipv6_vlan' => $network_secondary_ipv6_vlan,
            'network_secondary_ipv6_dns1' => $network_secondary_ipv6_dns1,
            'network_secondary_ipv6_dns2' => $network_secondary_ipv6_dns2
        ],
    ];

    $json = json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if (file_put_contents($jsonFile, $json, LOCK_EX) === false) {
        $errors[] = "Failed to write {$jsonFile}. Check permissions.";
    } else {
        $data = $new;
        $success = 'Saved.';

        foreach ($data as $block => &$fields) {
            foreach ($fields as $key => $value) {
                if (isset($_POST[$key])) {
                    $fields[$key] = trim($_POST[$key]);
                }
            }
        }
        unset($fields);


        $netplan = [
            'network' => [
                'version' => 2,
                'renderer' => 'networkd',
                'ethernets' => [],
                'vlans' => []
            ]
        ];

        foreach (['primary', 'secondary'] as $type) {

            if (
                $data[$type]['mode'] === 'disabled' &&
                $data[$type]['modev6'] === 'disabled'
            ) {
                continue;
            }

            $vlan = trim($data[$type]["network_{$type}_vlan"] ?? '');

            if ($vlan === '') {
                $netplan['network']['ethernets'][$iface] =
                    build_interface($data[$type], $type);
            } else {
                $netplan['network']['ethernets'][$iface] = new stdClass();

                $netplan['network']['vlans']["{$iface}.{$vlan}"] =
                    array_merge(
                        ['id' => (int)$vlan, 'link' => $iface],
                        build_interface($data[$type], $type)
                    );
            }
        }

        $yaml = yaml($netplan);
        file_put_contents('/var/www/50-cloud-init.yaml', $yaml);

    }
}

?>

<form method="POST" novalidate>
    <div class="containerindex">
        <div class="grid">
            <div class="card">
                <h3>Primary Interface</h3>
                <br>
                <div class="dropdown-container">
                    <span class="dropdown-label">IPv4 mode :</span>
                    <div class="dropdown">
                        <select name="primary_mode" id="primary_mode">
                            <option value="dhcp" <?php if ($data['primary']['mode'] == 'dhcp') echo 'selected'; ?>>DHCP</option>
                            <option value="static" <?php if ($data['primary']['mode'] == 'static') echo 'selected'; ?>>Static</option>
                            <option value="disabled" <?php if ($data['primary']['mode'] == 'disabled') echo 'selected'; ?>>Disabled</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="input-group">
                    <input
                        type="text"
                        id="network_primary_ip"
                        name="network_primary_ip"
                        placeholder="192.168.2.111/24"
                        pattern="^(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.){3}(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\/(?:8|16|20|24|28)$"
                        value="<?php echo htmlspecialchars($data['primary']['network_primary_ip']); ?>">
                    <label for="network_primary_ip">Address</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_primary_gateway" name="network_primary_gateway" pattern="^([0-9a-fA-F]{1,4}:){2,7}[0-9a-fA-F]{1,4}$" placeholder="Gateway" value="<?php echo htmlspecialchars($data['primary']['network_primary_gateway']); ?>">
                    <label for="network_primary_gateway">Gateway</label>
                </div>
                <div class="input-group">
                    <input type="number" min="1" max="4094" id="network_primary_vlan" name="network_primary_vlan" placeholder="Vlan" value="<?php echo htmlspecialchars($data['primary']['network_primary_vlan']); ?>">
                    <label for="network_primary_vlan">Vlan</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_primary_dns1" name="network_primary_dns1" placeholder="1.1.1.1" value="<?php echo htmlspecialchars($data['primary']['network_primary_dns1']); ?>">
                    <label for="network_primary_dns1">DNS1</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_primary_dns2" name="network_primary_dns2" placeholder="8.8.8.8" value="<?php echo htmlspecialchars($data['primary']['network_primary_dns2']); ?>">
                    <label for="network_primary_dns2">DNS2</label>
                </div>
                <div class="dropdown-container">
                    <span class="dropdown-label">IPv6 mode :</span>
                    <div class="dropdown">
                        <select name="primary_ipv6" id="primary_ipv6">
                            <option value="auto" <?php if ($data['primary']['modev6'] == 'auto') echo 'selected'; ?>>SLAAC / Auto</option>
                            <option value="dhcpv6" <?php if ($data['primary']['modev6'] == 'dhcpv6') echo 'selected'; ?>>DHCPv6</option>
                            <option value="static" <?php if ($data['primary']['modev6'] == 'static') echo 'selected'; ?>>Static</option>
                            <option value="disabled" <?php if ($data['primary']['modev6'] == 'disabled') echo 'selected'; ?>>Disabled</option>
                        </select>
                    </div>
                </div>
                <div class="input-group">
                    <input type="text" id="network_primary_ipv6" name="network_primary_ipv6" placeholder="Address" pattern="^(?:(?:25[0-5]|2[0-4]\d|1?\d{1,2})\.){3}(?:25[0-5]|2[0-4]\d|1?\d{1,2})$" value="<?php echo htmlspecialchars($data['primary']['network_primary_ipv6']); ?>">
                    <label for="network_primary_ipv6">Address</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_primary_ipv6_prefix" name="network_primary_ipv6_prefix" placeholder="Address" pattern="^(?:(?:25[0-5]|2[0-4]\d|1?\d{1,2})\.){3}(?:25[0-5]|2[0-4]\d|1?\d{1,2})$" value="<?php echo htmlspecialchars($data['primary']['network_primary_ipv6_prefix']); ?>">
                    <label for="network_primary_ipv6_prefix">Prefix</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_primary_ipv6_gateway" name="network_primary_ipv6_gateway" placeholder="Address" pattern="^(?:(?:25[0-5]|2[0-4]\d|1?\d{1,2})\.){3}(?:25[0-5]|2[0-4]\d|1?\d{1,2})$" value="<?php echo htmlspecialchars($data['primary']['network_primary_ipv6_gateway']); ?>">
                    <label for="network_primary_ipv6_gateway">Gateway</label>
                </div>
                <div class="input-group">
                    <input type="number" min="1" max="4094" id="network_primary_ipv6_vlan" name="network_primary_ipv6_vlan" placeholder="Vlan" value="<?php echo htmlspecialchars($data['primary']['network_primary_ipv6_vlan']); ?>">
                    <label for="network_primary_ipv6_vlan">Vlan</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_primary_ipv6_dns1" name="network_primary_ipv6_dns1" placeholder="2606:4700:4700::1111" pattern="^(?:(?:25[0-5]|2[0-4]\d|1?\d{1,2})\.){3}(?:25[0-5]|2[0-4]\d|1?\d{1,2})$" value="<?php echo htmlspecialchars($data['primary']['network_primary_ipv6_dns1']); ?>">
                    <label for="network_primary_ipv6_dns1">DNS1</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_primary_ipv6_dns2" name="network_primary_ipv6_dns2" placeholder="2001:4860:4860::8888" pattern="^(?:(?:25[0-5]|2[0-4]\d|1?\d{1,2})\.){3}(?:25[0-5]|2[0-4]\d|1?\d{1,2})$" value="<?php echo htmlspecialchars($data['primary']['network_primary_ipv6_dns2']); ?>">
                    <label for="network_primary_ipv6_dns2">DNS2</label>
                </div>

                <br>
            </div>
            <div class="card">
                <h3>Vlan Secondary Interface</h3>
                <br>
                <div class="dropdown-container">
                    <span class="dropdown-label">IPv4 mode :</span>
                    <div class="dropdown">
                        <select name="secondary_ipv4" id="secondary_ipv4">
                            <option value="dhcp" <?php if ($data['secondary']['mode'] == 'dhcp') echo 'selected'; ?>>DHCP</option>
                            <option value="static" <?php if ($data['secondary']['mode'] == 'static') echo 'selected'; ?>>Static</option>
                            <option value="disabled" <?php if ($data['secondary']['mode'] == 'disabled') echo 'selected'; ?>>Disabled</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="input-group">
                    <input
                        type="text"
                        id="network_secondary_ip"
                        name="network_secondary_ip"
                        placeholder="192.168.1.111/24"
                        pattern="^(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\.){3}(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)\/(?:8|16|20|24|28)$"
                        value="<?php echo htmlspecialchars($data['secondary']['network_secondary_ip']); ?>">
                    <label for="network_secondary_ip">Address</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_secondary_gateway" name="network_secondary_gateway" pattern="^([0-9a-fA-F]{1,4}:){2,7}[0-9a-fA-F]{1,4}$" placeholder="Gateway" value="<?php echo htmlspecialchars($data['secondary']['network_secondary_gateway']); ?>">
                    <label for="network_secondary_gateway">Gateway</label>
                </div>
                <div class="input-group">
                    <input type="number" min="1" max="4094" id="network_secondary_vlan" name="network_secondary_vlan" placeholder="Vlan" value="<?php echo htmlspecialchars($data['secondary']['network_secondary_vlan']); ?>">
                    <label for="network_secondary_vlan">Vlan</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_secondary_dns1" name="network_secondary_dns1" placeholder="1.1.1.1" value="<?php echo htmlspecialchars($data['secondary']['network_secondary_dns1']); ?>">
                    <label for="network_secondary_dns1">DNS1</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_secondary_dns2" name="network_secondary_dns2" placeholder="8.8.8.8" value="<?php echo htmlspecialchars($data['secondary']['network_secondary_dns2']); ?>">
                    <label for="network_secondary_dns2">DNS2</label>
                </div>
                <div class="dropdown-container">
                    <span class="dropdown-label">IPv6 mode :</span>
                    <div class="dropdown">
                        <select name="secondary_ipv6" id="secondary_ipv6">
                            <option value="auto" <?php if ($data['secondary']['modev6'] == 'auto') echo 'selected'; ?>>SLAAC / Auto</option>
                            <option value="dhcpv6" <?php if ($data['secondary']['modev6'] == 'dhcpv6') echo 'selected'; ?>>DHCPv6</option>
                            <option value="static" <?php if ($data['secondary']['modev6'] == 'static') echo 'selected'; ?>>Static</option>
                            <option value="disabled" <?php if ($data['secondary']['modev6'] == 'disabled') echo 'selected'; ?>>Disabled</option>
                        </select>
                    </div>
                </div>
                <div class="input-group">
                    <input type="text" id="network_secondary_ipv6" name="network_secondary_ipv6" placeholder="Address" pattern="^(?:(?:25[0-5]|2[0-4]\d|1?\d{1,2})\.){3}(?:25[0-5]|2[0-4]\d|1?\d{1,2})$" value="<?php echo htmlspecialchars($data['secondary']['network_secondary_ipv6']); ?>">
                    <label for="network_secondary_ipv6">Address</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_secondary_ipv6_prefix" name="network_secondary_ipv6_prefix" placeholder="Address" pattern="^(?:(?:25[0-5]|2[0-4]\d|1?\d{1,2})\.){3}(?:25[0-5]|2[0-4]\d|1?\d{1,2})$" value="<?php echo htmlspecialchars($data['secondary']['network_secondary_ipv6_prefix']); ?>">
                    <label for="network_secondary_ipv6_prefix">Prefix</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_secondary_ipv6_gateway" name="network_secondary_ipv6_gateway" placeholder="Address" pattern="^(?:(?:25[0-5]|2[0-4]\d|1?\d{1,2})\.){3}(?:25[0-5]|2[0-4]\d|1?\d{1,2})$" value="<?php echo htmlspecialchars($data['secondary']['network_secondary_ipv6_gateway']); ?>">
                    <label for="network_secondary_ipv6_gateway">Gateway</label>
                </div>
                <div class="input-group">
                    <input type="number" min="1" max="4094" id="network_secondary_ipv6_vlan" name="network_secondary_ipv6_vlan" placeholder="Vlan" value="<?php echo htmlspecialchars($data['secondary']['network_secondary_ipv6_vlan']); ?>">
                    <label for="network_secondary_ipv6_vlan">Vlan</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_secondary_ipv6_dns1" name="network_secondary_ipv6_dns1" placeholder="2606:4700:4700::1111" pattern="^(?:(?:25[0-5]|2[0-4]\d|1?\d{1,2})\.){3}(?:25[0-5]|2[0-4]\d|1?\d{1,2})$" value="<?php echo htmlspecialchars($data['secondary']['network_secondary_ipv6_dns1']); ?>">
                    <label for="network_secondary_ipv6_dns1">DNS1</label>
                </div>
                <div class="input-group">
                    <input type="text" id="network_secondary_ipv6_dns2" name="network_secondary_ipv6_dns2" placeholder="2001:4860:4860::8888" pattern="^(?:(?:25[0-5]|2[0-4]\d|1?\d{1,2})\.){3}(?:25[0-5]|2[0-4]\d|1?\d{1,2})$" value="<?php echo htmlspecialchars($data['secondary']['network_secondary_ipv6_dns2']); ?>">
                    <label for="network_secondary_ipv6_dns2">DNS2</label>
                </div>
                <br>
            </div>
        </div>
        <div style="text-align:center; width:100%; margin-top:12px;">
            <button type="submit" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save</button>
        </div>
        <br>
        <br>
        <br>
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