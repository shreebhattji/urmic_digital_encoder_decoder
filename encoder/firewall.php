<?php include 'header.php' ?>
<?php

exec("sudo chmod 444 /sys/class/dmi/id/product_uuid");


$file = __DIR__ . '/firewall.json';
$rules = [];

if (file_exists($file)) {
    $json = file_get_contents($file);
    $rules = json_decode($json, true) ?: [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rules = [];

    if (!empty($_POST['ip_version'])) {
        foreach ($_POST['ip_version'] as $i => $v) {
            $rules[] = [
                'ip_version'  => $_POST['ip_version'][$i] ?? '',
                'ip_address'  => $_POST['ip_address'][$i] ?? '',
                'port'        => $_POST['port'][$i] ?? '',
                'protocol'    => $_POST['protocol'][$i] ?? '',
                'description' => $_POST['description'][$i] ?? ''
            ];
        }
    }

    file_put_contents($file, json_encode($rules, JSON_PRETTY_PRINT));
}
?>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f5f7fa;
        padding: 20px;
    }

    .container {
        max-width: 1100px;
        margin: auto;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    th {
        background: #f0f2f5;
    }

    input,
    select {
        width: 100%;
        padding: 6px;
    }

    button {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-add {
        background: #2e7d32;
        color: #fff;
    }

    .btn-remove {
        background: #c62828;
        color: #fff;
    }

    .btn-save {
        background: #1565c0;
        color: #fff;
        margin-top: 15px;
    }

    .actions {
        text-align: right;
    }
</style>
<div class="containerindex">
    <div class="grid">
        <div class="card wide">
            <h2>Allow Rules</h2>
            <form method="post">
                <table id="rulesTable">
                    <thead>
                        <tr>
                            <th>IP Address</th>
                            <th>Port</th>
                            <th>Protocol</th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if ($rules): foreach ($rules as $r): ?>
                                <tr>
                                    <td><input type="text" name="ip_address[]" value="<?= htmlspecialchars($r['ip_address']) ?>"></td>
                                    <td><input type="number" name="port[]" value="<?= htmlspecialchars($r['port']) ?>"></td>
                                    <td>
                                        <select name="protocol[]">
                                            <option value="tcp" <?= $r['protocol'] == 'tcp' ? 'selected' : '' ?>>TCP</option>
                                            <option value="udp" <?= $r['protocol'] == 'udp' ? 'selected' : '' ?>>UDP</option>
                                            <option value="any" <?= $r['protocol'] == 'any' ? 'selected' : '' ?>>ANY</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="description[]" value="<?= htmlspecialchars($r['description']) ?>"></td>
                                    <td class="actions">
                                        <button type="button" class="btn-remove" onclick="removeRow(this)">Remove</button>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td><input type="text" name="ip_address[]" placeholder="192.168.1.0/24 or 2001:db8::/64"></td>
                                <td><input type="text" name="port[]" placeholder="1-65535 or any"></td>
                                <td>
                                    <select name="protocol[]">
                                        <option value="tcp">TCP</option>
                                        <option value="udp">UDP</option>
                                        <option value="any">ANY</option>
                                    </select>
                                </td>
                                <td><input type="text" name="description[]"></td>
                                <td class="actions">
                                    <button type="button" class="btn-remove" onclick="removeRow(this)">Remove</button>
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
                <br>
                <button type="button" class="btn-add" onclick="addRow()">Add Rule</button>
                <br><br>
                <button type="submit" class="btn-save">Save Rules</button>
                <br><br>
                <br><br>
            </form>
            <br><br>
        </div>
    </div>
</div>

<script>
    function addRow() {
        const tbody = document.querySelector('#rulesTable tbody');
        const row = tbody.rows[0].cloneNode(true);
        row.querySelectorAll('input').forEach(i => i.value = '');
        row.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        tbody.appendChild(row);
    }

    function removeRow(btn) {
        const tbody = document.querySelector('#rulesTable tbody');
        if (tbody.rows.length > 1) {
            btn.closest('tr').remove();
        }
    }
</script>
<?php include 'footer.php' ?>