<?php include 'header.php' ?>
<?php


$file = __DIR__ . '/firewall.json';
$data = [
    '80'   => '',
    '443'  => '',
    '1935' => '',
    '1937' => ''
];

if (file_exists($jsonFile)) {
    $stored = json_decode(file_get_contents($jsonFile), true);
    if (is_array($stored)) {
        $data = array_merge($data, $stored);
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data as $port => $val) {
        $data[$port] = trim($_POST["port_$port"] ?? '');
    }
    file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
}


?>

<style>
    body {
        font-family: system-ui, sans-serif;
        background: #f5f7fa;
    }

    .container {
        max-width: 520px;
        margin: 40px auto;
        background: #fff;
        padding: 24px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
    }

    h2 {
        margin-bottom: 20px;
        font-size: 20px;
    }

    .row {
        margin-bottom: 16px;
    }

    label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
    }

    input[type=text] {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    input[type=text]:invalid {
        border-color: #d33;
    }

    small {
        color: #666;
    }

    button {
        margin-top: 20px;
        padding: 12px 18px;
        border: none;
        border-radius: 8px;
        background: #2563eb;
        color: #fff;
        font-size: 15px;
        cursor: pointer;
    }

    button:hover {
        background: #1e4ed8;
    }
</style>

<script>
    function validateIPs(input) {
        if (!input.value.trim()) return true;

        const ips = input.value.split(',').map(i => i.trim());

        const ipv4 =
            /^(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}$/;

        const ipv6 =
            /^(([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|::1|::)$/;

        for (const ip of ips) {
            if (!(ipv4.test(ip) || ipv6.test(ip))) {
                return false;
            }
        }
        return true;
    }

    function attachValidation() {
        document.querySelectorAll('input[type=text]').forEach(inp => {
            inp.addEventListener('input', () => {
                inp.setCustomValidity(
                    validateIPs(inp) ? '' : 'Invalid IPv4 or IPv6 address'
                );
            });
        });
    }
    window.onload = attachValidation;
</script>
<div class="containerindex">
    <div class="grid">
        <div class="card wide">
            <h2>Firewall Allowed IPs</h2>

            <form method="post">
                <?php foreach ($data as $port => $value): ?>
                    <div class="row">
                        <label>Port <?= htmlspecialchars($port) ?></label>
                        <input
                            type="text"
                            name="port_<?= $port ?>"
                            value="<?= htmlspecialchars($value) ?>"
                            placeholder="IPv4, IPv6 (comma separated)">
                        <small>Example: 192.168.1.10, 2001:db8::1</small>
                    </div>
                <?php endforeach; ?>

                <button type="submit">Save Rules</button>
            </form>
            <br><br>
        </div>
    </div>
</div>

<?php include 'footer.php' ?>