<?php include 'header.php' ?>
<?php
/*
Urmi you happy me happy licence

Copyright (c) 2026 shreebhattji

License text:
https://github.com/shreebhattji/Urmi/blob/main/licence.md
*/

$jsonFile = __DIR__ . '/firewall.json';

$defaults = [
    '80'   => '',
    '443'  => '',
    '1935' => '',
    '1937' => '',
    '8080' => '',
];

$data = $defaults;

if (is_file($jsonFile)) {
    $stored = json_decode(file_get_contents($jsonFile), true);
    if (is_array($stored)) {
        $data =  $stored;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    exec("echo y | sudo ufw reset");
    exec("sudo ufw default allow outgoing");
    exec("sudo ufw default deny incoming");
    exec("sudo ufw allow proto udp to 224.0.0.0/4");
    exec("sudo ufw route allow proto udp to 224.0.0.0/4");
    exec("sudo ufw deny out to 239.255.254.254 port 39000 proto udp");

    foreach ($defaults as $port => $_) {
        $data[$port] = trim($_POST["port_$port"] ?? '');
    }

    $tmp = $jsonFile . '.tmp';
    file_put_contents(
        $tmp,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
    rename($tmp, $jsonFile);

    foreach ($data as $port => $value) {
        $tmp = array_filter(
            array_map('trim', explode(',', (string)$value)),
            'strlen'
        );
        if (count($tmp) > 0) {
            foreach ($tmp as $ip) {
                exec("sudo ufw allow from " . $ip." to any port " . $port . " proto tcp");
            }
        } else {
            exec("sudo ufw allow " . $port);
        }
    }

    exec("sudo ufw allow from 172.16.111.112 to 172.16.111.111 port 8080");
    exec("sudo ufw --force enable");
    exec("sudo ufw reload");
}
?>

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
            <h2>Limit Access</h2>

            <form method="post">
                <?php foreach ($data as $port => $value): ?>
                    <div class="row">
                        <label>Port <?= htmlspecialchars($port) ?></label>
                        <textarea
                            name="port_<?= $port ?>"
                            rows="2"
                            placeholder="IPv4, IPv6 (comma separated)"><?= htmlspecialchars($value) ?></textarea>

                        <small>Example: 192.168.1.10/24, 2001:db8::1</small>
                    </div>
                <?php endforeach; ?>

                <button type="submit">Limit Access</button>
                <br>
                <br>
                <br>
                <br>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php' ?>