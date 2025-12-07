<?php include 'header.php'; ?>
<?php
$jsonFile = __DIR__ . '/input.json';
$defaults = [
    'input' => 'url',
    'hdmi' => [
        'resolution' => '1920x1080',
        'audio_source' => 'hw:1,0',
        'framerate' => '30'
    ],
    'url' => 'https://cdn.urmic.org/unavailable.mp4',
    'rtmp' => [
        'mount' => 'channel_name',
        'password' => 'live',
    ],
    'srt' => [
        'stream_id_1' => 'forever',
        'stream_id_2' => 'steaming',
        'stream_id_3' => 'partner',
    ],
    'udp' => 'udp://@224.1.1.1:8000',
    'custom' => ''
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
        ],
        'srt' => [
            'stream_id_1' => $posted('srt_stream_id_1', $defaults['srt']['stream_id_1']),
            'stream_id_2' => $posted('srt_stream_id_2', $defaults['srt']['stream_id_2']),
            'stream_id_3' => $posted('srt_stream_id_3', $defaults['srt']['stream_id_3']),
        ],
        'udp' => $posted('udp', $defaults['udp']),
        'custom' => $posted('custom', $defaults['custom'])

    ];

    // write JSON with exclusive lock and pretty print
    $json = json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if (file_put_contents($jsonFile, $json, LOCK_EX) === false) {
        $saveError = "Failed to write $jsonFile. Check permissions.";
    } else {
        $data = $new; // reload into form
        $saveSuccess = "Saved.";
    }

    update_service("input");
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
                            $opts = ['hdmi', 'url', 'rtmp server', 'srt server', 'udp', 'custom'];
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
                            $res = ['1920x1080', '1600x1200', '1360x768', '1280x1024', '1280x720', '1024x768', '720x576', '640x480'];
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
                            $aopts = ['10', '20', '30', '50', '60'];
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
            </div>
            <div class="card wide">
                <h3>UDP</h3>
                <div class="input-group">
                    <input type="text" id="udp" name="udp" value="<?php echo htmlspecialchars($data['udp']); ?>" placeholder="udp://@224.224.1.1:8000">
                    <label for="udp">UDP</label>
                </div>
            </div>

            <div class="card wide">
                <h3>Custom Input</h3>
                <div class="input-group">
                    <input type="text" id="custom" name="custom" value="<?php echo htmlspecialchars($data['custom']); ?>" placeholder=" ">
                    <label for="custom">custom</label>
                </div>
            </div>

            <div class="card wide">
                <h3>Common BackEnd</h3>

                <div class="grid">
                    <div class="card">
                        <div class="dropdown-container">
                            <span class="dropdown-label">Resolution :</span>
                            <div class="dropdown">
                                <select name="common_input_resolution" id="common_input_resolution">
                                    <option value="720x480" <?php if ($data['common_input']['resolution'] == '720x480') echo 'selected'; ?>>480p 720x480 NTSC DVD</option>
                                    <option value="720x576" <?php if ($data['common_input']['resolution'] == '720x576') echo 'selected'; ?>>576p 720x576 PAL DVD</option>
                                    <option value="1280x720" <?php if ($data['common_input']['resolution'] == '1280x720') echo 'selected'; ?>>720p 1280x720 HD</option>
                                    <option value="1920x1080" <?php if ($data['common_input']['resolution'] == '1920x1080') echo 'selected'; ?>>1080p 1920x1080 FHD</option>
                                    <option value="2560x1440" <?php if ($data['common_input']['resolution'] == '2560x1440') echo 'selected'; ?>>2k 2560x1440 QHD</option>
                                    <option value="2048x1080" <?php if ($data['common_input']['resolution'] == '2048x1080') echo 'selected'; ?>>2k 2048x1080 DCI 2K</option>
                                    <option value="3840x2160" <?php if ($data['common_input']['resolution'] == '3840x2160') echo 'selected'; ?>>4k 3840x2160 UHD</option>
                                    <option value="4096x2160" <?php if ($data['common_input']['resolution'] == '4096x2160') echo 'selected'; ?>>4k 4096x2160 DCI 4K</option>
                                    <option value="7680x4320" <?php if ($data['common_input']['resolution'] == '7680x4320') echo 'selected'; ?>>8k 7680x4320 UHD 8K</option>
                                    <option value="8192x4320" <?php if ($data['common_input']['resolution'] == '8192x4320') echo 'selected'; ?>>8k 8192x4320 DCI 8K</option>
                                </select>
                            </div>
                        </div>
                        <div class="input-group">
                            <input type="text" id="common_input_data_rate" name="common_input_data_rate" placeholder="5M" value="<?php echo htmlspecialchars($data['common_input']['data_rate']); ?>">
                            <label for="common_input_data_rate">Data Rate :</label>
                        </div>
                        <div class="input-group">
                            <input type="text" id="common_input_framerate" name="common_input_framerate" placeholder="25" value="<?php echo htmlspecialchars($data['common_input']['framerate']); ?>">
                            <label for="common_input_framerate">Framerate :</label>
                        </div>
                        <div class="input-group">
                            <input type="text" id="common_input_gop" name="common_input_gop" placeholder="12" value="<?php echo htmlspecialchars($data['common_input']['gop']); ?>">
                            <label for="common_input_gop">GOP :</label>
                        </div>
                    </div>
                    <div class="card">
                        <div class="dropdown-container">
                            <span class="dropdown-label">DB Gain :</span>
                            <div class="dropdown">
                                <select name="common_input_audio_db_gain" id="common_input_audio_db_gain">
                                    <option value="-25dB" <?php if ($data['common_input']['audio_db_gain'] == '-25dB') echo 'selected'; ?>>-25dB</option>
                                    <option value="-20dB" <?php if ($data['common_input']['audio_db_gain'] == '-20dB') echo 'selected'; ?>>-20dB</option>
                                    <option value="-15dB" <?php if ($data['common_input']['audio_db_gain'] == '-15dB') echo 'selected'; ?>>-15dB</option>
                                    <option value="-10dB" <?php if ($data['common_input']['audio_db_gain'] == '-10dB') echo 'selected'; ?>>-10dB</option>
                                    <option value="-6dB" <?php if ($data['common_input']['audio_db_gain'] == '-6dB') echo 'selected'; ?>>-6dB</option>
                                    <option value="-5dB" <?php if ($data['common_input']['audio_db_gain'] == '-5dB') echo 'selected'; ?>>-5dB</option>
                                    <option value="-4dB" <?php if ($data['common_input']['audio_db_gain'] == '-4dB') echo 'selected'; ?>>-4dB</option>
                                    <option value="-3dB" <?php if ($data['common_input']['audio_db_gain'] == '-3dB') echo 'selected'; ?>>-3dB</option>
                                    <option value="-2dB" <?php if ($data['common_input']['audio_db_gain'] == '-2dB') echo 'selected'; ?>>-2dB</option>
                                    <option value="-1dB" <?php if ($data['common_input']['audio_db_gain'] == '-1dB') echo 'selected'; ?>>-1dB</option>
                                    <option value="0dB" <?php if ($data['common_input']['audio_db_gain'] == '0dB') echo 'selected'; ?>>0dB</option>
                                    <option value="1dB" <?php if ($data['common_input']['audio_db_gain'] == '1dB') echo 'selected'; ?>>1dB</option>
                                    <option value="2dB" <?php if ($data['common_input']['audio_db_gain'] == '2dB') echo 'selected'; ?>>2dB</option>
                                    <option value="3dB" <?php if ($data['common_input']['audio_db_gain'] == '3dB') echo 'selected'; ?>>3dB</option>
                                    <option value="4dB" <?php if ($data['common_input']['audio_db_gain'] == '4dB') echo 'selected'; ?>>4dB</option>
                                    <option value="5dB" <?php if ($data['common_input']['audio_db_gain'] == '5dB') echo 'selected'; ?>>5dB</option>
                                    <option value="6dB" <?php if ($data['common_input']['audio_db_gain'] == '6dB') echo 'selected'; ?>>6dB</option>
                                    <option value="10dB" <?php if ($data['common_input']['audio_db_gain'] == '10dB') echo 'selected'; ?>>10dB</option>
                                    <option value="15dB" <?php if ($data['common_input']['audio_db_gain'] == '15dB') echo 'selected'; ?>>15dB</option>
                                    <option value="20dB" <?php if ($data['common_input']['audio_db_gain'] == '20dB') echo 'selected'; ?>>20dB</option>
                                    <option value="25dB" <?php if ($data['common_input']['audio_db_gain'] == '25dB') echo 'selected'; ?>>25dB</option>
                                </select>
                            </div>
                        </div>
                        <p></p>
                        <div class="input-group">
                            <input type="text" id="common_input_audio_data_rate" name="common_input_audio_data_rate" placeholder="96k" value="<?php echo htmlspecialchars($data['common_input']['audio_data_rate']); ?>">
                            <label for="common_input_audio_data_rate">Bit Rate :</label>
                        </div>
                        <div class="input-group">
                            <input type="text" id="common_input_audio_sample_rate" name="common_input_audio_sample_rate" placeholder="48000" value="<?php echo htmlspecialchars($data['common_input']['audio_sample_rate']); ?>">
                            <label for="rtmp1_audio_sampcommon_input_audio_sample_ratele_rate">Sample Rate :</label>
                        </div>
                    </div>
                </div>
                <div class="input-group">
                    <input type="text" id="common_input_extra" name="common_input_extra" value="<?php echo htmlspecialchars($data['common_input']['extra']); ?>">
                    <label for="common_input_extra">Extra :</label>
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
if (!empty($saveError)) echo '<p style="color:red;text-align:center;">' . htmlspecialchars($saveError) . '</p>';
if (!empty($saveSuccess)) echo '<p style="color:green;text-align:center;">' . htmlspecialchars($saveSuccess) . '</p>';
?>

<?php include 'footer.php'; ?>