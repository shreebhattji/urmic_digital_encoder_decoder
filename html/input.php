<?php include 'header.php'; ?>
<?php
// path to JSON file
$jsonFile = __DIR__ . '/input.json';

// load existing data or defaults
$defaults = [
    'input' => 'url',
    'hdmi' => [
        'resolution' => '1920x1080',
        'audio_source' => 'hw:1,0',
        'framerate' => '30'
    ],
    'url' => 'https://cdn.urmic.org/unavailable.mp4',
    'rtmp' => [
        'mount' => '',
        'password' => 'live',
        'port' => '1935'
    ],
    'srt' => [
        'stream_id_1' => 'har',
        'stream_id_2' => 'har',
        'stream_id_3' => 'Mahadev',
        'port' => '1937'
    ]
];

if (file_exists($jsonFile)) {
    $raw = file_get_contents($jsonFile);
    $data = json_decode($raw, true);
    if (!is_array($data)) $data = $defaults;
} else {
    $data = $defaults;
}

// handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // simple sanitizer
    $posted = function ($k, $default = '') {
        return isset($_POST[$k]) ? trim((string)$_POST[$k]) : $default;
    };

    $new = [
        'input' => $posted('input', $defaults['input']),
        'hdmi' => [
            'resolution' => $posted('hdmi_resolution', $defaults['hdmi']['resolution']),
            'audio_source' => $posted('hdmi_audio_source', $defaults['hdmi']['audio_source']),
            'framerate' => $posted('hdmi_framerate', $defaults['hdmi']['framerate'])
        ],
        'url' => $posted('url', $defaults['url']),
        'rtmp' => [
            'mount' => $posted('rtmp_mount', $defaults['rtmp']['mount']),
            'password' => $posted('rtmp_password', $defaults['rtmp']['password']),
            'port' => $posted('rtmp_port', $defaults['rtmp']['port'])
        ],
        'srt' => [
            'stream_id_1' => $posted('srt_stream_id_1', $defaults['srt']['stream_id_1']),
            'stream_id_2' => $posted('srt_stream_id_2', $defaults['srt']['stream_id_2']),
            'stream_id_3' => $posted('srt_stream_id_3', $defaults['srt']['stream_id_3']),
            'port' => $posted('srt_port', $defaults['srt']['port'])
        ]
    ];

    // write JSON with exclusive lock and pretty print
    $json = json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if (file_put_contents($jsonFile, $json, LOCK_EX) === false) {
        $saveError = "Failed to write $jsonFile. Check permissions.";
    } else {
        $data = $new; // reload into form
        $saveSuccess = "Saved.";
    }

    update_service();
}
?>

<form method="POST">
    <div class="containerindex">
        <div class="grid">
            <div class="card wide">
                <div class="dropdown-container">
                    <span class="dropdown-label">Input :</span>
                    <div class="dropdown">
                        <select name="input">
                            <?php
                            $opts = ['hdmi', 'url', 'rtmp', 'srt'];
                            foreach ($opts as $o) {
                                $sel = ($data['input'] === $o) ? 'selected' : '';
                                echo "<option value=\"" . htmlspecialchars($o) . "\" $sel>" . htmlspecialchars($o) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card wide">
                <h3>HDMI Settings</h3>
                <div class="dropdown-container">
                    <span class="dropdown-label">Resolution :</span>
                    <div class="dropdown">
                        <select name="hdmi_resolution">
                            <?php
                            $res = ['1920x1080', '1280x720', '720x576'];
                            foreach ($res as $r) {
                                $sel = ($data['hdmi']['resolution'] === $r) ? 'selected' : '';
                                echo "<option value=\"" . htmlspecialchars($r) . "\" $sel>$r</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="dropdown-container">
                    <span class="dropdown-label">Audio Source :</span>
                    <div class="dropdown">
                        <select name="hdmi_audio_source">
                            <?php
                            $aopts = ['hw:0,0', 'hw:1,0'];
                            foreach ($aopts as $a) {
                                $sel = ($data['hdmi']['audio_source'] === $a) ? 'selected' : '';
                                echo "<option value=\"" . htmlspecialchars($a) . "\" $sel>$a</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="dropdown-container">
                    <span class="dropdown-label">Framerate :</span>
                    <div class="dropdown">
                        <select name="hdmi_framerate">
                            <?php
                            $aopts = ['10','20', '30', '50','60'];
                            foreach ($aopts as $a) {
                                $sel = ($data['hdmi']['framerate'] === $a) ? 'selected' : '';
                                echo "<option value=\"" . htmlspecialchars($a) . "\" $sel>$a</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card wide">
                <h3>URL Setting</h3>
                <div class="input-group">
                    <input type="text" id="url" name="url" value="<?php echo htmlspecialchars($data['url']); ?>" placeholder=" ">
                    <label for="url">URL</label>
                </div>
            </div>

            <div class="card wide">
                <h3>RTMP Server Setting</h3>
                <div class="input-group">
                    <input type="text" id="rtmp_mount" name="rtmp_mount" value="<?php echo htmlspecialchars($data['rtmp']['mount']); ?>" placeholder="Mount Name">
                    <label for="rtmp_mount">Channel name</label>
                </div>
                <div class="input-group">
                    <input type="text" id="rtmp_password" name="rtmp_password" value="<?php echo htmlspecialchars($data['rtmp']['password']); ?>" placeholder="live">
                    <label for="rtmp_password">Password</label>
                </div>
                <div class="input-group">
                    <input type="text" id="rtmp_port" name="rtmp_port" value="<?php echo htmlspecialchars($data['rtmp']['port']); ?>" placeholder="1935">
                    <label for="rtmp_port">Port Number</label>
                </div>
            </div>

            <div class="card wide">
                <h3>SRT Caller Setting</h3>
                <div class="input-group">
                    <input type="text" id="srt_stream_id_1" name="srt_stream_id_1" value="<?php echo htmlspecialchars($data['srt']['stream_id_1']); ?>" placeholder="pass1">
                    <label for="srt_stream_id_1">Stream ID 1</label>
                </div>
                <div class="input-group">
                    <input type="text" id="srt_stream_id_2" name="srt_stream_id_2" value="<?php echo htmlspecialchars($data['srt']['stream_id_2']); ?>" placeholder="pass2">
                    <label for="srt_stream_id_2">Stream ID 2</label>
                </div>
                <div class="input-group">
                    <input type="text" id="srt_stream_id_3" name="srt_stream_id_3" value="<?php echo htmlspecialchars($data['srt']['stream_id_3']); ?>" placeholder="pass3">
                    <label for="srt_stream_id_3">Stream ID 3</label>
                </div>
                <div class="input-group">
                    <input type="text" id="srt_port" name="srt_port" value="<?php echo htmlspecialchars($data['srt']['port']); ?>" placeholder="1937">
                    <label for="srt_port">Port Number</label>
                </div>
            </div>

            <div style="text-align:center; width:100%; margin-top:12px;">
                <button type="submit" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save</button>
            </div>
        </div>
        <br>
        <br>
    </div>
    <br>
    <br>
</form>
<br>
<br>

<?php
// optional status messages
if (!empty($saveError)) echo '<p style="color:red;text-align:center;">' . htmlspecialchars($saveError) . '</p>';
if (!empty($saveSuccess)) echo '<p style="color:green;text-align:center;">' . htmlspecialchars($saveSuccess) . '</p>';
?>

<?php include 'footer.php'; ?>