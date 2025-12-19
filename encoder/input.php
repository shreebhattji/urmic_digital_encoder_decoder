<?php include 'header.php'; ?>
<?php
$jsonFile = __DIR__ . '/input.json';
$defaults = [
    'input' => 'url',
    'use_common_backend' => 'use_common_backend',
    'hdmi' => [
        'resolution' => '1920x1080',
        'audio_source' => 'hw:1,0',
        'framerate' => '30',
        'video_delay' => '300',
        'audio_delay' => ''
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
    'custom' => '',
    'common_backend' => [
        'resolution' => '1920x1080',
        'data_rate' => '5M',
        'framerate' => '30',
        'gop' => '30',
        'audio_db_gain' => '0dB',
        'audio_data_rate' => '256k',
        'audio_sample_rate' => '48000',
        'extra' => ''
    ],
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
    global $defaults;

    $new = [
        'input' => $posted('input', $defaults['input']),
        'use_common_backend' => $posted('use_common_backend', $defaults['use_common_backend']),
        'hdmi' => [
            'resolution' => $posted('hdmi_resolution', $defaults['hdmi']['resolution']),
            'audio_source' => $posted('hdmi_audio_source', $defaults['hdmi']['audio_source']),
            'framerate' => $posted('hdmi_framerate', $defaults['hdmi']['framerate']),
            'video_delay' => $posted('hdmi_video_delay', $defaults['hdmi']['video_delay']),
            'audio_delay' => $posted('hdmi_audio_delay', $defaults['hdmi']['audio_delay'])
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
        'custom' => $posted('custom', $defaults['custom']),
        'common_backend' => [
            'resolution' => $posted('common_backend_resolution', $defaults['common_backend']['resolution']),
            'data_rate' => $posted('common_backend_data_rate', $defaults['common_backend']['data_rate']),
            'framerate' => $posted('common_backend_framerate', $defaults['common_backend']['framerate']),
            'gop' => $posted('common_backend_gop', $defaults['common_backend']['gop']),
            'audio_db_gain' => $posted('common_backend_audio_db_gain', $defaults['common_backend']['audio_db_gain']),
            'audio_data_rate' => $posted('common_backend_audio_data_rate', $defaults['common_backend']['audio_data_rate']),
            'audio_sample_rate' => $posted('common_backend_audio_sample_rate', $defaults['common_backend']['audio_sample_rate']),
            'extra' => $posted('common_backend_extra', $defaults['common_backend']['extra']),
        ]
    ];

    if($new['use_common_backend']=="transcode_every_time"){
        switch($new['input']){
            case 'rtmp':
                $new['rtmp']['rtmp_password']="bhattji";
                break;
            case 'srt':
                $new['srt']['srt_stream_id_3']="ji";
                break;
        }
    }

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
                            $opts = ['hdmi', 'url', 'rtmp', 'srt', 'udp', 'custom'];
                            foreach ($opts as $o) {
                                $sel = ($data['input'] === $o) ? 'selected' : '';
                                echo "<option value=\"" . htmlspecialchars($o) . "\" $sel>" . htmlspecialchars($o) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="dropdown-container">
                    <span class="dropdown-label">Input Transcode Settings :</span>
                    <div class="dropdown">
                        <select name="use_common_backend" id="use_common_backend">
                            <option value="copy_input" <?php if ($data['use_common_backend'] == 'copy_input') echo 'selected'; ?>>Copy Input</option>
                            <option value="use_common_backend" <?php if ($data['use_common_backend'] == 'use_common_backend') echo 'selected'; ?>>Use Common Backend</option>
                            <option value="transcode_every_time" <?php if ($data['use_common_backend'] == 'transcode_every_time') echo 'selected'; ?>>Do not transcode input</option>
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
                <br>
                <br>
                <div class="input-group">
                    <input type="text" id="hdmi_video_delay" name="hdmi_video_delay" value="<?php echo htmlspecialchars($data['hdmi']['video_delay']); ?>" placeholder=" ">
                    <label for="hdmi_video_delay">Video Delay in ms : </label>
                </div>
                <div class="input-group">
                    <input type="text" id="hdmi_audio_delay" name="hdmi_audio_delay" value="<?php echo htmlspecialchars($data['hdmi']['audio_delay']); ?>" placeholder=" ">
                    <label for="hdmi_audio_delay">Audio Delay in ms : </label>
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
                                <select name="common_backend_resolution" id="common_backend_resolution">
                                    <option value="720x480" <?php if ($data['common_backend']['resolution'] == '720x480') echo 'selected'; ?>>480p 720x480 NTSC DVD</option>
                                    <option value="720x576" <?php if ($data['common_backend']['resolution'] == '720x576') echo 'selected'; ?>>576p 720x576 PAL DVD</option>
                                    <option value="1280x720" <?php if ($data['common_backend']['resolution'] == '1280x720') echo 'selected'; ?>>720p 1280x720 HD</option>
                                    <option value="1920x1080" <?php if ($data['common_backend']['resolution'] == '1920x1080') echo 'selected'; ?>>1080p 1920x1080 FHD</option>
                                    <option value="2560x1440" <?php if ($data['common_backend']['resolution'] == '2560x1440') echo 'selected'; ?>>2k 2560x1440 QHD</option>
                                    <option value="2048x1080" <?php if ($data['common_backend']['resolution'] == '2048x1080') echo 'selected'; ?>>2k 2048x1080 DCI 2K</option>
                                    <option value="3840x2160" <?php if ($data['common_backend']['resolution'] == '3840x2160') echo 'selected'; ?>>4k 3840x2160 UHD</option>
                                    <option value="4096x2160" <?php if ($data['common_backend']['resolution'] == '4096x2160') echo 'selected'; ?>>4k 4096x2160 DCI 4K</option>
                                    <option value="7680x4320" <?php if ($data['common_backend']['resolution'] == '7680x4320') echo 'selected'; ?>>8k 7680x4320 UHD 8K</option>
                                    <option value="8192x4320" <?php if ($data['common_backend']['resolution'] == '8192x4320') echo 'selected'; ?>>8k 8192x4320 DCI 8K</option>
                                </select>
                            </div>
                        </div>
                        <div class="input-group">
                            <input type="text" id="common_backend_data_rate" name="common_backend_data_rate" placeholder="5M" value="<?php echo htmlspecialchars($data['common_backend']['data_rate']); ?>">
                            <label for="common_backend_data_rate">Data Rate :</label>
                        </div>
                        <div class="input-group">
                            <input type="text" id="common_backend_framerate" name="common_backend_framerate" placeholder="30" value="<?php echo htmlspecialchars($data['common_backend']['framerate']); ?>">
                            <label for="common_backend_framerate">Framerate :</label>
                        </div>
                        <div class="input-group">
                            <input type="text" id="common_backend_gop" name="common_backend_gop" placeholder="30" value="<?php echo htmlspecialchars($data['common_backend']['gop']); ?>">
                            <label for="common_backend_gop">GOP :</label>
                        </div>
                    </div>
                    <div class="card">
                        <div class="dropdown-container">
                            <span class="dropdown-label">DB Gain :</span>
                            <div class="dropdown">
                                <select name="common_backend_audio_db_gain" id="common_backend_audio_db_gain">
                                    <option value="-25dB" <?php if ($data['common_backend']['audio_db_gain'] == '-25dB') echo 'selected'; ?>>-25dB</option>
                                    <option value="-20dB" <?php if ($data['common_backend']['audio_db_gain'] == '-20dB') echo 'selected'; ?>>-20dB</option>
                                    <option value="-15dB" <?php if ($data['common_backend']['audio_db_gain'] == '-15dB') echo 'selected'; ?>>-15dB</option>
                                    <option value="-10dB" <?php if ($data['common_backend']['audio_db_gain'] == '-10dB') echo 'selected'; ?>>-10dB</option>
                                    <option value="-6dB" <?php if ($data['common_backend']['audio_db_gain'] == '-6dB') echo 'selected'; ?>>-6dB</option>
                                    <option value="-5dB" <?php if ($data['common_backend']['audio_db_gain'] == '-5dB') echo 'selected'; ?>>-5dB</option>
                                    <option value="-4dB" <?php if ($data['common_backend']['audio_db_gain'] == '-4dB') echo 'selected'; ?>>-4dB</option>
                                    <option value="-3dB" <?php if ($data['common_backend']['audio_db_gain'] == '-3dB') echo 'selected'; ?>>-3dB</option>
                                    <option value="-2dB" <?php if ($data['common_backend']['audio_db_gain'] == '-2dB') echo 'selected'; ?>>-2dB</option>
                                    <option value="-1dB" <?php if ($data['common_backend']['audio_db_gain'] == '-1dB') echo 'selected'; ?>>-1dB</option>
                                    <option value="0dB" <?php if ($data['common_backend']['audio_db_gain'] == '0dB') echo 'selected'; ?>>0dB</option>
                                    <option value="1dB" <?php if ($data['common_backend']['audio_db_gain'] == '1dB') echo 'selected'; ?>>1dB</option>
                                    <option value="2dB" <?php if ($data['common_backend']['audio_db_gain'] == '2dB') echo 'selected'; ?>>2dB</option>
                                    <option value="3dB" <?php if ($data['common_backend']['audio_db_gain'] == '3dB') echo 'selected'; ?>>3dB</option>
                                    <option value="4dB" <?php if ($data['common_backend']['audio_db_gain'] == '4dB') echo 'selected'; ?>>4dB</option>
                                    <option value="5dB" <?php if ($data['common_backend']['audio_db_gain'] == '5dB') echo 'selected'; ?>>5dB</option>
                                    <option value="6dB" <?php if ($data['common_backend']['audio_db_gain'] == '6dB') echo 'selected'; ?>>6dB</option>
                                    <option value="10dB" <?php if ($data['common_backend']['audio_db_gain'] == '10dB') echo 'selected'; ?>>10dB</option>
                                    <option value="15dB" <?php if ($data['common_backend']['audio_db_gain'] == '15dB') echo 'selected'; ?>>15dB</option>
                                    <option value="20dB" <?php if ($data['common_backend']['audio_db_gain'] == '20dB') echo 'selected'; ?>>20dB</option>
                                    <option value="25dB" <?php if ($data['common_backend']['audio_db_gain'] == '25dB') echo 'selected'; ?>>25dB</option>
                                </select>
                            </div>
                        </div>
                        <p></p>
                        <div class="input-group">
                            <input type="text" id="common_backend_audio_data_rate" name="common_backend_audio_data_rate" placeholder="256k" value="<?php echo htmlspecialchars($data['common_backend']['audio_data_rate']); ?>">
                            <label for="common_backend_audio_data_rate">Bit Rate :</label>
                        </div>
                        <div class="input-group">
                            <input type="text" id="common_backend_audio_sample_rate" name="common_backend_audio_sample_rate" placeholder="48000" value="<?php echo htmlspecialchars($data['common_backend']['audio_sample_rate']); ?>">
                            <label for="common_backend_audio_sample_rate">Sample Rate :</label>
                        </div>
                    </div>
                </div>
                <div class="input-group">
                    <input type="text" id="common_backend_extra" name="common_backend_extra" value="<?php echo htmlspecialchars($data['common_backend']['extra']); ?>">
                    <label for="common_backend_extra">Extra :</label>
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