<?php
/******************************************************
 * firewall_manager.php
 * Simple web UI to manage UFW firewall rules.
 *
 * IMPORTANT:
 * - Protect this file (HTTP auth / VPN / IP allowlist).
 * - PHP must run as root OR allow ufw in sudoers, e.g.:
 *   www-data ALL=(ALL) NOPASSWD:/usr/sbin/ufw
 ******************************************************/

// If ufw is not in this path, adjust:
define('UFW_BIN', '/usr/sbin/ufw');

// Set to true to only preview commands and not actually run them
$DRY_RUN = false;

$messages = [];
$errors   = [];

/**
 * Validate port number (1–65535). Returns int or null.
 */
function sanitize_port($port) {
    $port = trim($port);
    if ($port === '') return null;
    if (!ctype_digit($port)) return null;
    $p = (int)$port;
    if ($p < 1 || $p > 65535) return null;
    return $p;
}

/**
 * Very basic CIDR/subnet validation.
 * Accepts forms like:
 *   10.0.0.0/24
 *   192.168.1.5
 */
function sanitize_subnet($subnet) {
    $subnet = trim($subnet);
    if ($subnet === '') return null;

    // allow plain IP
    if (filter_var($subnet, FILTER_VALIDATE_IP)) {
        return $subnet;
    }

    // allow IP/CIDR
    $parts = explode('/', $subnet);
    if (count($parts) === 2) {
        [$ip, $mask] = $parts;
        $ip   = trim($ip);
        $mask = trim($mask);
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return null;
        }
        if (!ctype_digit($mask)) {
            return null;
        }
        $m = (int)$mask;
        if ($m < 0 || $m > 32) {
            return null;
        }
        return $ip . '/' . $m;
    }

    return null;
}

/**
 * Run a UFW command and capture output.
 */
function run_ufw_command($cmd, $dryRun = false) {
    $cmdline = UFW_BIN . ' ' . $cmd;

    if ($dryRun) {
        return [
            'cmd' => $cmdline,
            'output' => ['[DRY RUN] Command not executed'],
            'exit_code' => 0,
        ];
    }

    $output = [];
    $ret    = 0;
    exec($cmdline . ' 2>&1', $output, $ret);

    return [
        'cmd'       => $cmdline,
        'output'    => $output,
        'exit_code' => $ret,
    ];
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'apply_firewall') {
        $globalPorts       = $_POST['global_ports']       ?? [];
        $restrictedPorts   = $_POST['restricted_port']    ?? [];
        $restrictedSubnets = $_POST['restricted_subnet']  ?? [];
        $dryRunChecked     = isset($_POST['dry_run']) && $_POST['dry_run'] === '1';

        $didAnything = false;

        // 1) Open ports for all IP (allow)
        foreach ($globalPorts as $rawPort) {
            $rawPort = trim($rawPort);
            if ($rawPort === '') {
                continue;
            }

            $port = sanitize_port($rawPort);
            if ($port === null) {
                $errors[] = "Invalid open port: " . htmlspecialchars($rawPort);
                continue;
            }

            $didAnything = true;

            $res = run_ufw_command('allow ' . (int)$port, $dryRunChecked || $DRY_RUN);
            if ($res['exit_code'] !== 0) {
                $errors[] = "Failed to allow port {$port}: " . implode(" ", $res['output']);
            } else {
                $messages[] = "Allowed port {$port} for all IPs.";
            }
        }

        // 2) Restricted ports with subnets
        // Model: Deny port globally, then allow from specified subnets.
        $count = max(count($restrictedPorts), count($restrictedSubnets));

        for ($i = 0; $i < $count; $i++) {
            $rawPort   = $restrictedPorts[$i]   ?? '';
            $rawSubnet = $restrictedSubnets[$i] ?? '';

            $rawPort   = trim($rawPort);
            $rawSubnet = trim($rawSubnet);

            if ($rawPort === '' || $rawSubnet === '') {
                continue;
            }

            $port   = sanitize_port($rawPort);
            $subnet = sanitize_subnet($rawSubnet);

            if ($port === null) {
                $errors[] = "Invalid restricted port: " . htmlspecialchars($rawPort);
                continue;
            }
            if ($subnet === null) {
                $errors[] = "Invalid subnet/CIDR for port {$port}: " . htmlspecialchars($rawSubnet);
                continue;
            }

            $didAnything = true;

            // Deny port from everywhere
            $denyRes = run_ufw_command('deny ' . (int)$port, $dryRunChecked || $DRY_RUN);
            if ($denyRes['exit_code'] !== 0) {
                $errors[] = "Failed to deny port {$port}: " . implode(" ", $denyRes['output']);
            } else {
                $messages[] = "Denied port {$port} for all IPs.";
            }

            // Allow from subnet
            $allowCmd = 'allow from ' . escapeshellarg($subnet) . ' to any port ' . (int)$port;
            // We used escapeshellarg() here, but run_ufw_command expects only the ufw arguments,
            // so we need to build carefully:
            // Rebuild without full path:
            $allowCmdForRun = 'allow from ' . $subnet . ' to any port ' . (int)$port;

            $allowRes = run_ufw_command($allowCmdForRun, $dryRunChecked || $DRY_RUN);
            if ($allowRes['exit_code'] !== 0) {
                $errors[] = "Failed to allow port {$port} from {$subnet}: " . implode(" ", $allowRes['output']);
            } else {
                $messages[] = "Allowed port {$port} only from {$subnet}.";
            }
        }

        if (!$didAnything) {
            $errors[] = "No valid firewall rules submitted.";
        }

        // Optional: reload or enable ufw here
        // $reload = run_ufw_command('reload', $dryRunChecked || $DRY_RUN);

    }
}

// Get current UFW status
$currentStatus = [];
$statusExit    = 0;
if (file_exists(UFW_BIN)) {
    exec(UFW_BIN . ' status numbered 2>&1', $currentStatus, $statusExit);
} else {
    $currentStatus[] = "UFW binary not found at " . UFW_BIN;
    $statusExit = 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Firewall Rule Manager</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f4f5f7;
            margin: 0;
            padding: 0;
        }
        .page {
            max-width: 1100px;
            margin: 24px auto;
            padding: 0 16px 32px;
        }
        h1 {
            margin-bottom: 4px;
        }
        .subtitle {
            color: #555;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .card {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12);
        }
        .card h2 {
            margin-top: 0;
            font-size: 18px;
            margin-bottom: 8px;
        }
        .card p {
            margin-top: 0;
            font-size: 13px;
            color: #555;
        }
        .messages {
            margin-bottom: 16px;
        }
        .msg {
            padding: 8px 10px;
            border-radius: 4px;
            margin-bottom: 6px;
            font-size: 13px;
        }
        .msg.success {
            background: #e6f4ea;
            border: 1px solid #9ad0a6;
            color: #225c2e;
        }
        .msg.error {
            background: #fdecea;
            border: 1px solid #f5c2c0;
            color: #611a15;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 8px;
            margin-bottom: 8px;
        }
        th, td {
            border-bottom: 1px solid #e2e8f0;
            padding: 6px 8px;
            text-align: left;
            font-size: 13px;
        }
        th {
            background: #f9fafb;
            font-weight: 600;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            box-sizing: border-box;
            padding: 6px 8px;
            font-size: 13px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }
        input[type="checkbox"] {
            transform: scale(1.1);
        }
        .btn {
            display: inline-block;
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            background: #2563eb;
            color: #ffffff;
        }
        .btn.secondary {
            background: #e2e8f0;
            color: #111827;
        }
        .btn.sm {
            padding: 4px 10px;
            font-size: 12px;
        }
        .btn + .btn {
            margin-left: 6px;
        }
        .row-actions {
            text-align: right;
        }
        pre {
            background: #0b1120;
            color: #e2e8f0;
            padding: 10px;
            border-radius: 4px;
            font-size: 12px;
            overflow-x: auto;
        }
        .flex {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .flex-right {
            margin-left: auto;
        }
        .small-note {
            font-size: 12px;
            color: #64748b;
            margin-top: 6px;
        }
        .section-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 8px;
        }
    </style>
</head>
<body>
<div class="page">
    <h1>Firewall Rule Manager</h1>
    <div class="subtitle">
        Configure simple UFW rules to open ports for all IPs and restrict ports to specific subnets.
        Ensure this interface is accessible only to trusted administrators.
    </div>

    <div class="messages">
        <?php foreach ($messages as $m): ?>
            <div class="msg success"><?php echo htmlspecialchars($m); ?></div>
        <?php endforeach; ?>
        <?php foreach ($errors as $e): ?>
            <div class="msg error"><?php echo htmlspecialchars($e); ?></div>
        <?php endforeach; ?>
    </div>

    <form method="post">
        <input type="hidden" name="action" value="apply_firewall">

        <div class="card">
            <div class="section-header">
                <h2>Open Ports (All IPs)</h2>
                <button type="button" class="btn sm secondary" onclick="addGlobalPortRow()">+ Add Port</button>
            </div>
            <p>Ports in this list will be <strong>allowed from any IP</strong>.</p>

            <table id="globalPortsTable">
                <thead>
                <tr>
                    <th style="width: 140px;">Port</th>
                    <th>Comment (optional)</th>
                    <th style="width: 80px;"></th>
                </tr>
                </thead>
                <tbody>
                <!-- Default rows (80, 443, 1935) -->
                <tr>
                    <td><input type="number" name="global_ports[]" value="80" min="1" max="65535"></td>
                    <td><input type="text" name="global_comment[]" placeholder="HTTP"></td>
                    <td class="row-actions">
                        <button type="button" class="btn sm secondary" onclick="removeRow(this)">Remove</button>
                    </td>
                </tr>
                <tr>
                    <td><input type="number" name="global_ports[]" value="443" min="1" max="65535"></td>
                    <td><input type="text" name="global_comment[]" placeholder="HTTPS"></td>
                    <td class="row-actions">
                        <button type="button" class="btn sm secondary" onclick="removeRow(this)">Remove</button>
                    </td>
                </tr>
                <tr>
                    <td><input type="number" name="global_ports[]" value="1935" min="1" max="65535"></td>
                    <td><input type="text" name="global_comment[]" placeholder="RTMP"></td>
                    <td class="row-actions">
                        <button type="button" class="btn sm secondary" onclick="removeRow(this)">Remove</button>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="small-note">
                Example: 80, 443, 1935, 22 etc. Empty rows will be ignored.
            </div>
        </div>

        <div class="card">
            <div class="section-header">
                <h2>Restricted Ports (Only From Subnets)</h2>
                <button type="button" class="btn sm secondary" onclick="addRestrictedRow()">+ Add Restricted Rule</button>
            </div>
            <p>
                For each row: port is first globally <strong>denied</strong>, then <strong>allowed only from the subnet</strong>.
                Typical use: restrict 8080/8443 to internal networks.
            </p>

            <table id="restrictedTable">
                <thead>
                <tr>
                    <th style="width: 140px;">Port</th>
                    <th>Subnet / CIDR (e.g. 192.168.1.0/24)</th>
                    <th style="width: 80px;"></th>
                </tr>
                </thead>
                <tbody>
                <!-- Example rows -->
                <tr>
                    <td><input type="number" name="restricted_port[]" value="8080" min="1" max="65535"></td>
                    <td><input type="text" name="restricted_subnet[]" value="192.168.0.0/16"></td>
                    <td class="row-actions">
                        <button type="button" class="btn sm secondary" onclick="removeRow(this)">Remove</button>
                    </td>
                </tr>
                <tr>
                    <td><input type="number" name="restricted_port[]" value="8443" min="1" max="65535"></td>
                    <td><input type="text" name="restricted_subnet[]" value="10.0.0.0/8"></td>
                    <td class="row-actions">
                        <button type="button" class="btn sm secondary" onclick="removeRow(this)">Remove</button>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="small-note">
                Subnet examples: <code>192.168.1.0/24</code>, <code>10.0.0.0/8</code>, or a single IP like <code>203.0.113.5</code>.
            </div>
        </div>

        <div class="card">
            <div class="flex">
                <label>
                    <input type="checkbox" name="dry_run" value="1">
                    Preview only (do not execute commands)
                </label>
                <div class="flex-right">
                    <button type="submit" class="btn">Apply Firewall Rules</button>
                </div>
            </div>
            <div class="small-note">
                Use preview first to verify behaviour. Always test on a non-production system before applying to live servers.
            </div>
        </div>
    </form>

    <div class="card">
        <h2>Current UFW Status</h2>
        <p>Output of <code>ufw status numbered</code>:</p>
        <pre><?php echo htmlspecialchars(implode("\n", $currentStatus)); ?></pre>
    </div>
</div>

<script>
    function removeRow(button) {
        const tr = button.closest('tr');
        const tbody = tr.parentNode;
        tbody.removeChild(tr);
    }

    function addGlobalPortRow() {
        const tableBody = document.querySelector('#globalPortsTable tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="number" name="global_ports[]" min="1" max="65535" placeholder="Port"></td>
            <td><input type="text" name="global_comment[]" placeholder="Comment (optional)"></td>
            <td class="row-actions">
                <button type="button" class="btn sm secondary" onclick="removeRow(this)">Remove</button>
            </td>
        `;
        tableBody.appendChild(tr);
    }

    function addRestrictedRow() {
        const tableBody = document.querySelector('#restrictedTable tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="number" name="restricted_port[]" min="1" max="65535" placeholder="Port"></td>
            <td><input type="text" name="restricted_subnet[]" placeholder="192.168.1.0/24"></td>
            <td class="row-actions">
                <button type="button" class="btn sm secondary" onclick="removeRow(this)">Remove</button>
            </td>
        `;
        tableBody.appendChild(tr);
    }
</script>
</body>
</html>
