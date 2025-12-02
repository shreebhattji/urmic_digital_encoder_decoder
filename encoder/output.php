<?php include 'header.php'; ?>
<?php

$jsonFile = __DIR__ . '/output.json';

$defaults = [
  'video' => [
    'resolution' => '1920x1080',
    'format' => 'h264_qsv',
    'framerate' => '25',
    'data_rate' => '3.3M',
    'gop' => '25'
  ],
  'audio' => [
    'format' => 'aac',
    'sample_rate' => '48000',
    'bit_rate' => '96k',
    'db_gain' => '0dB'
  ],
  'service_display' => 'disable',
  'output_display' => '1920x1080@60.00',
  'output_display_audio' => '0,3',
  'service_rtmp_multiple' => 'disable',
  'service_rtmp_hls' => 'disable',
  'service_rtmp_dash' => 'disable',
  'service_srt_multiple' => 'disable',
  'service_udp' => 'disable',
  'service_custom' => 'disable',
  'rtmp_multiple' => [],
  'srt_multiple'  => [],
  'udp' => '',
  'custom_output' => ''
];

for ($i = 1; $i <= 11; $i++) {
  $defaults['rtmp_multiple'][$i] = ['url' => '', 'name' => '', 'enabled' => false];
  $defaults['srt_multiple'][$i]  = ['url' => '', 'name' => '', 'enabled' => false];
}

if (file_exists($jsonFile)) {
  $raw = file_get_contents($jsonFile);
  $data = json_decode($raw, true);
  if (!is_array($data)) $data = $defaults;

  $data = array_replace_recursive($defaults, $data);
} else {
  $data = $defaults;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $get = function ($k, $d = '') {
    return isset($_POST[$k]) ? trim((string)$_POST[$k]) : $d;
  };

  $new = $data;
  $new['video']['resolution'] = $get('output_resolution', $defaults['video']['resolution']);
  $new['video']['format'] = $get('output_video_formate', $defaults['video']['format']);
  $new['video']['framerate'] = $get('output_video_framerate', $defaults['video']['framerate']);
  $new['video']['data_rate'] = $get('output_data_rate', $defaults['video']['data_rate']);
  $new['video']['gop'] = $get('output_gop', $defaults['video']['gop']);

  $new['audio']['format'] = $get('output_audio_formate', $defaults['audio']['format']);
  $new['audio']['sample_rate'] = $get('output_audio_sample_rate', $defaults['audio']['sample_rate']);
  $new['audio']['bit_rate'] = $get('output_audio_bit_rate', $defaults['audio']['bit_rate']);
  $new['audio']['db_gain'] = $get('output_audio_db_gain', $defaults['audio']['db_gain']);

  $new['output_display'] = $get('output_display', $defaults['output_display']);
  $new['output_display_audio'] = $get('output_display_audio', $defaults['output_display_audio']);
  $new['service_display'] = $get('service_display', $defaults['service_display']);
  $new['service_rtmp_multiple'] = $get('service_rtmp_multiple', $defaults['service_rtmp_multiple']);
  $new['service_rtmp_hls'] = $get('service_rtmp_hls', $defaults['service_rtmp_hls']);
  $new['service_rtmp_dash'] = $get('service_rtmp_dash', $defaults['service_rtmp_dash']);
  $new['service_srt_multiple'] = $get('service_srt_multiple', $defaults['service_srt_multiple']);
  $new['service_display'] = $get('service_display', $defaults['service_display']);
  $new['service_custom'] = $get('service_custom', $defaults['service_custom']);

  $new['udp'] = $get('udp', '');
  $new['custom_output'] = $get('custom_output', '');

  for ($i = 1; $i <= 11; $i++) {
    $u = $get("rtmp_{$i}", '');
    $n = $get("rtmp_{$i}_name", '');
    $e = isset($_POST["rtmp_{$i}_enable"]) ? true : false;
    $new['rtmp_multiple'][$i] = ['url' => $u, 'name' => $n, 'enabled' => $e];

    $u2 = $get("srt_{$i}", '');
    $n2 = $get("srt_{$i}_name", '');
    $e2 = isset($_POST["srt_{$i}_enable"]) ? true : false;
    $new['srt_multiple'][$i] = ['url' => $u2, 'name' => $n2, 'enabled' => $e2];
  }

  if ($new['video']['gop'] !== '' && !ctype_digit((string)$new['video']['gop'])) {
    $errors[] = "GOP must be an integer.";
  }

  if (empty($errors)) {
    $json = json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if (file_put_contents($jsonFile, $json, LOCK_EX) === false) {
      $errors[] = "Failed to write {$jsonFile}. Check permissions.";
    } else {
      $data = $new;
      $success = "Saved.";
    }
  }
  if (isset($_POST['display'])) {
    update_service("display");
  }

  if (isset($_POST['rtmp'])) {
    update_service("rtmp");
  }

  if (isset($_POST['srt'])) {
    update_service("srt");
  }

  if (isset($_POST['udo'])) {
    update_service("udo");
  }

  if (isset($_POST['custom'])) {
    update_service("custom");
  }
}
?>
<form method="POST">
  <div class="containerindex">
    <div class="grid">
      <div class="card">
        <div class="dropdown-container">
          <span class="dropdown-label">Formate :</span>
          <div class="dropdown">
            <select name="output_video_formate" id="output_video_formate">
              <option value="mpeg2video" <?php if ($data['video']['format'] == 'mpeg2video') echo 'selected'; ?>>mpeg2</option>
              <option value="h264_qsv" <?php if ($data['video']['format'] == 'h264_qsv') echo 'selected'; ?>>h264</option>
              <option value="h265" <?php if ($data['video']['format'] == 'h265') echo 'selected'; ?>>h265</option>
            </select>
          </div>
        </div>
        <div class="input-group">
          <input type="text" id="output_video_framerate" name="output_video_framerate" placeholder="25" value="<?php echo htmlspecialchars($data['video']['framerate']); ?>">
          <label for="output_video_framerate">Framerate :</label>
        </div>
        <div class="input-group">
          <input type="text" id="output_data_rate" name="output_data_rate" placeholder="4M" value="<?php echo htmlspecialchars($data['video']['data_rate']); ?>">
          <label for="output_data_rate">OutPut Data Rate :</label>
        </div>
        <div class="input-group">
          <input type="text" id="output_gop" name="output_gop" placeholder="12" value="<?php echo htmlspecialchars($data['video']['gop']); ?>">
          <label for="output_gop">GOP :</label>
        </div>
      </div>

      <div class="card">
        <h3>Audio Setting</h3>
        <div class="dropdown-container">
          <span class="dropdown-label">Output Audio Formate :</span>
          <div class="dropdown">
            <select name="output_audio_formate" id="output_audio_formate">
              <option value="mp2" <?php if ($data['audio']['format'] == 'mp2') echo 'selected'; ?>>mp2</option>
              <option value="mp3" <?php if ($data['audio']['format'] == 'mp3') echo 'selected'; ?>>mp3</option>
              <option value="aac" <?php if ($data['audio']['format'] == 'aac') echo 'selected'; ?>>aac</option>
              <option value="ac3" <?php if ($data['audio']['format'] == 'ac3') echo 'selected'; ?>>ac3</option>
            </select>
          </div>
        </div>
        <div class="dropdown-container">
          <span class="dropdown-label">DB Gain :</span>
          <div class="dropdown">
            <select name="output_audio_db_gain" id="output_audio_db_gain">
              <option value="mp2" <?php if ($data['audio']['db_gain'] == '-6dB') echo 'selected'; ?>>-6dB</option>
              <option value="mp2" <?php if ($data['audio']['db_gain'] == '-5dB') echo 'selected'; ?>>-5dB</option>
              <option value="mp2" <?php if ($data['audio']['db_gain'] == '-4dB') echo 'selected'; ?>>-4dB</option>
              <option value="mp2" <?php if ($data['audio']['db_gain'] == '-3dB') echo 'selected'; ?>>-3dB</option>
              <option value="mp2" <?php if ($data['audio']['db_gain'] == '-2dB') echo 'selected'; ?>>-2dB</option>
              <option value="mp2" <?php if ($data['audio']['db_gain'] == '-1dB') echo 'selected'; ?>>-1dB</option>
              <option value="mp2" <?php if ($data['audio']['db_gain'] == '0dB') echo 'selected'; ?>>0dB</option>
              <option value="mp2" <?php if ($data['audio']['db_gain'] == '1dB') echo 'selected'; ?>>1dB</option>
              <option value="mp2" <?php if ($data['audio']['db_gain'] == '2dB') echo 'selected'; ?>>2dB</option>
              <option value="mp2" <?php if ($data['audio']['db_gain'] == '3dB') echo 'selected'; ?>>3dB</option>
              <option value="mp2" <?php if ($data['audio']['db_gain'] == '4dB') echo 'selected'; ?>>4dB</option>
              <option value="mp2" <?php if ($data['audio']['db_gain'] == '5dB') echo 'selected'; ?>>5dB</option>
              <option value="mp2" <?php if ($data['audio']['db_gain'] == '6dB') echo 'selected'; ?>>6dB</option>
            </select>
          </div>
        </div>
        <p></p>
        <div class="input-group">
          <input type="text" id="output_audio_sample_rate" name="output_audio_sample_rate" placeholder="48000" value="<?php echo htmlspecialchars($data['audio']['sample_rate']); ?>">
          <label for="output_audio_sample_rate">Sample Rate :</label>
        </div>
        <div class="input-group">
          <input type="text" id="output_audio_bit_rate" name="output_audio_bit_rate" placeholder="96k" value="<?php echo htmlspecialchars($data['audio']['bit_rate']); ?>">
          <label for="output_audio_bit_rate">Bit Rate :</label>
        </div>
      </div>

      <div class="card wide">
        <h3>DISPLAY - HDMI - VGA - PORTS</h3>
        <div class="dropdown-container">
          <span class="dropdown-label">Service Status :</span>
          <div class="dropdown">
            <select name="service_display" id="service_display">
              <option value="enable" <?php if ($data['service_display'] == 'enable') echo 'selected'; ?>>Enable</option>
              <option value="disable" <?php if ($data['service_display'] == 'disable') echo 'selected'; ?>>Disable</option>
            </select>
          </div>
        </div>
        <div class="dropdown-container">
          <span class="dropdown-label">Resolution :</span>
          <div class="dropdown">
            <select name="output_display" id="output_display">
              <!-- 4K (4096x2160p) -->
              <option value="4096x2160@23.98" <?php if ($data['output_display'] == '4096x2160@23.98') echo 'selected'; ?>>4096x2160@23.98</option>
              <option value="4096x2160@24.00" <?php if ($data['output_display'] == '4096x2160@24.00') echo 'selected'; ?>>4096x2160@24.00</option>
              <option value="4096x2160@25.00" <?php if ($data['output_display'] == '4096x2160@25.00') echo 'selected'; ?>>4096x2160@25.00</option>
              <option value="4096x2160@29.97" <?php if ($data['output_display'] == '4096x2160@29.97') echo 'selected'; ?>>4096x2160@29.97</option>
              <option value="4096x2160@30.00" <?php if ($data['output_display'] == '4096x2160@30.00') echo 'selected'; ?>>4096x2160@30.00</option>
              <option value="4096x2160@47.95" <?php if ($data['output_display'] == '4096x2160@47.95') echo 'selected'; ?>>4096x2160@47.95</option>
              <option value="4096x2160@48.00" <?php if ($data['output_display'] == '4096x2160@48.00') echo 'selected'; ?>>4096x2160@48.00</option>
              <option value="4096x2160@50.00" <?php if ($data['output_display'] == '4096x2160@50.00') echo 'selected'; ?>>4096x2160@50.00</option>
              <option value="4096x2160@59.94" <?php if ($data['output_display'] == '4096x2160@59.94') echo 'selected'; ?>>4096x2160@59.94</option>
              <option value="4096x2160@60.00" <?php if ($data['output_display'] == '4096x2160@60.00') echo 'selected'; ?>>4096x2160@60.00</option>

              <!-- UltraHD (3840x2160p) -->
              <option value="3840x2160@23.98" <?php if ($data['output_display'] == '3840x2160@23.98') echo 'selected'; ?>>3840x2160@23.98</option>
              <option value="3840x2160@24.00" <?php if ($data['output_display'] == '3840x2160@24.00') echo 'selected'; ?>>3840x2160@24.00</option>
              <option value="3840x2160@25.00" <?php if ($data['output_display'] == '3840x2160@25.00') echo 'selected'; ?>>3840x2160@25.00</option>
              <option value="3840x2160@29.97" <?php if ($data['output_display'] == '3840x2160@29.97') echo 'selected'; ?>>3840x2160@29.97</option>
              <option value="3840x2160@30.00" <?php if ($data['output_display'] == '3840x2160@30.00') echo 'selected'; ?>>3840x2160@30.00</option>
              <option value="3840x2160@50.00" <?php if ($data['output_display'] == '3840x2160@50.00') echo 'selected'; ?>>3840x2160@50.00</option>
              <option value="3840x2160@59.94" <?php if ($data['output_display'] == '3840x2160@59.94') echo 'selected'; ?>>3840x2160@59.94</option>
              <option value="3840x2160@60.00" <?php if ($data['output_display'] == '3840x2160@60.00') echo 'selected'; ?>>3840x2160@60.00</option>

              <!-- 2K (2048x1080p) -->
              <option value="2048x1080@23.98" <?php if ($data['output_display'] == '2048x1080@23.98') echo 'selected'; ?>>2048x1080@23.98</option>
              <option value="2048x1080@24.00" <?php if ($data['output_display'] == '2048x1080@24.00') echo 'selected'; ?>>2048x1080@24.00</option>
              <option value="2048x1080@25.00" <?php if ($data['output_display'] == '2048x1080@25.00') echo 'selected'; ?>>2048x1080@25.00</option>
              <option value="2048x1080@29.97" <?php if ($data['output_display'] == '2048x1080@29.97') echo 'selected'; ?>>2048x1080@29.97</option>
              <option value="2048x1080@30.00" <?php if ($data['output_display'] == '2048x1080@30.00') echo 'selected'; ?>>2048x1080@30.00</option>
              <option value="2048x1080@47.95" <?php if ($data['output_display'] == '2048x1080@47.95') echo 'selected'; ?>>2048x1080@47.95</option>
              <option value="2048x1080@48.00" <?php if ($data['output_display'] == '2048x1080@48.00') echo 'selected'; ?>>2048x1080@48.00</option>
              <option value="2048x1080@50.00" <?php if ($data['output_display'] == '2048x1080@50.00') echo 'selected'; ?>>2048x1080@50.00</option>
              <option value="2048x1080@59.94" <?php if ($data['output_display'] == '2048x1080@59.94') echo 'selected'; ?>>2048x1080@59.94</option>
              <option value="2048x1080@60.00" <?php if ($data['output_display'] == '2048x1080@60.00') echo 'selected'; ?>>2048x1080@60.00</option>

              <!-- HD (1920x1080p) -->
              <option value="1920x1080@23.98" <?php if ($data['output_display'] == '1920x1080@23.98') echo 'selected'; ?>>1920x1080@23.98</option>
              <option value="1920x1080@24.00" <?php if ($data['output_display'] == '1920x1080@24.00') echo 'selected'; ?>>1920x1080@24.00</option>
              <option value="1920x1080@25.00" <?php if ($data['output_display'] == '1920x1080@25.00') echo 'selected'; ?>>1920x1080@25.00</option>
              <option value="1920x1080@29.97" <?php if ($data['output_display'] == '1920x1080@29.97') echo 'selected'; ?>>1920x1080@29.97</option>
              <option value="1920x1080@30.00" <?php if ($data['output_display'] == '1920x1080@30.00') echo 'selected'; ?>>1920x1080@30.00</option>
              <option value="1920x1080@50.00" <?php if ($data['output_display'] == '1920x1080@50.00') echo 'selected'; ?>>1920x1080@50.00</option>
              <option value="1920x1080@59.94" <?php if ($data['output_display'] == '1920x1080@59.94') echo 'selected'; ?>>1920x1080@59.94</option>
              <option value="1920x1080@60.00" <?php if ($data['output_display'] == '1920x1080@60.00') echo 'selected'; ?>>1920x1080@60.00</option>

              <!-- HD Interlaced (1920x1080i) -->
              <option value="1920x1080i@50.00" <?php if ($data['output_display'] == '1920x1080i@50.00') echo 'selected'; ?>>1920x1080i@50.00</option>
              <option value="1920x1080i@59.94" <?php if ($data['output_display'] == '1920x1080i@59.94') echo 'selected'; ?>>1920x1080i@59.94</option>
              <option value="1920x1080i@60.00" <?php if ($data['output_display'] == '1920x1080i@60.00') echo 'selected'; ?>>1920x1080i@60.00</option>

              <!-- HD (1280x720p) -->
              <option value="1280x720@50.00" <?php if ($data['output_display'] == '1280x720@50.00') echo 'selected'; ?>>1280x720@50.00</option>
              <option value="1280x720@59.94" <?php if ($data['output_display'] == '1280x720@59.94') echo 'selected'; ?>>1280x720@59.94</option>
              <option value="1280x720@60.00" <?php if ($data['output_display'] == '1280x720@60.00') echo 'selected'; ?>>1280x720@60.00</option>

              <!-- SD Progressive -->
              <option value="720x576@50.00" <?php if ($data['output_display'] == '720x576@50.00') echo 'selected'; ?>>720x576@50.00</option>
              <option value="720x480@59.94" <?php if ($data['output_display'] == '720x480@59.94') echo 'selected'; ?>>720x480@59.94</option>

              <!-- SD Interlaced -->
              <option value="720x576i@25.00" <?php if ($data['output_display'] == '720x576i@25.00') echo 'selected'; ?>>720x576i@25.00</option>
              <option value="720x480i@29.97" <?php if ($data['output_display'] == '720x480i@29.97') echo 'selected'; ?>>720x480i@29.97</option>
            </select>
          </div>
        </div>

        <div class="dropdown-container">
          <span class="dropdown-label">Audio Output :</span>
          <div class="dropdown">
            <select name="output_display_audio" id="output_display_audio">
              <option value="0,0" <?php if ($data['output_display_audio'] == '0,0') echo 'selected'; ?>>0,0</option>
              <option value="0,1" <?php if ($data['output_display_audio'] == '0,1') echo 'selected'; ?>>0,1</option>
              <option value="0,2" <?php if ($data['output_display_audio'] == '0,2') echo 'selected'; ?>>0,2</option>
              <option value="0,3" <?php if ($data['output_display_audio'] == '0,3') echo 'selected'; ?>>0,3</option>
            </select>
          </div>
        </div>
        <div style="text-align:center; width:100%; margin-top:12px;">
          <button type="submit" name="display" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save Display</button>
        </div>

      </div>

      <div class="card wide">
        <h3>RTMP Output</h3>
        <div class="dropdown-container">
          <span class="dropdown-label">Service Status :</span>
          <div class="dropdown">
            <select name="service_rtmp_multiple" id="service_rtmp_multiple">
              <option value="enable" <?php if ($data['service_rtmp_multiple'] == 'enable') echo 'selected'; ?>>Enable</option>
              <option value="disable" <?php if ($data['service_rtmp_multiple'] == 'disable') echo 'selected'; ?>>Disable</option>
            </select>
          </div>
        </div>
        <div class="dropdown-container">
          <span class="dropdown-label">HLS :</span>
          <div class="dropdown">
            <select name="service_rtmp_hls" id="service_rtmp_hls">
              <option value="enable" <?php if ($data['service_rtmp_hls'] == 'enable') echo 'selected'; ?>>Enable</option>
              <option value="disable" <?php if ($data['service_rtmp_hls'] == 'disable') echo 'selected'; ?>>Disable</option>
            </select>
          </div>
        </div>
        <div class="dropdown-container">
          <span class="dropdown-label">DASH :</span>
          <div class="dropdown">
            <select name="service_rtmp_dash" id="service_rtmp_dash">
              <option value="enable" <?php if ($data['service_rtmp_dash'] == 'enable') echo 'selected'; ?>>Enable</option>
              <option value="disable" <?php if ($data['service_rtmp_dash'] == 'disable') echo 'selected'; ?>>Disable</option>
            </select>
          </div>
        </div>

        <div class="grid">
          <div class="card">

            <div class="dropdown-container">
              <span class="dropdown-label">Resolution :</span>
              <div class="dropdown">
                <select name="output_resolution" id="rtmp_output_resolution">
                  <option value="720x480" <?php if ($data['video']['resolution'] == '720x480') echo 'selected'; ?>>480p 720x480 NTSC DVD</option>
                  <option value="720x576" <?php if ($data['video']['resolution'] == '720x576') echo 'selected'; ?>>576p 720x576 PAL DVD</option>
                  <option value="1280x720" <?php if ($data['video']['resolution'] == '1280x720') echo 'selected'; ?>>720p 1280x720 HD</option>
                  <option value="1920x1080" <?php if ($data['video']['resolution'] == '1920x1080') echo 'selected'; ?>>1080p 1920x1080 FHD</option>
                  <option value="2560x1440" <?php if ($data['video']['resolution'] == '2560x1440') echo 'selected'; ?>>2k 2560x1440 QHD</option>
                  <option value="2048x1080" <?php if ($data['video']['resolution'] == '2048x1080') echo 'selected'; ?>>2k 2048x1080 DCI 2K</option>
                  <option value="3840x2160" <?php if ($data['video']['resolution'] == '3840x2160') echo 'selected'; ?>>4k 3840x2160 UHD</option>
                  <option value="4096x2160" <?php if ($data['video']['resolution'] == '4096x2160') echo 'selected'; ?>>4k 4096x2160 DCI 4K</option>
                  <option value="7680x4320" <?php if ($data['video']['resolution'] == '7680x4320') echo 'selected'; ?>>8k 7680x4320 UHD 8K</option>
                  <option value="8192x4320" <?php if ($data['video']['resolution'] == '8192x4320') echo 'selected'; ?>>8k 8192x4320 DCI 8K</option>
                </select>
              </div>
            </div>
            <div class="input-group">
              <input type="text" id="rtmp_video_framerate" name="rtmp_video_framerate" placeholder="25" value="<?php echo htmlspecialchars($data['video']['framerate']); ?>">
              <label for="rtmp_video_framerate">Framerate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="rtmp_data_rate" name="rtmp_data_rate" placeholder="4M" value="<?php echo htmlspecialchars($data['video']['data_rate']); ?>">
              <label for="rtmp_data_rate">Data Rate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="rtmp_gop" name="rtmp_gop" placeholder="12" value="<?php echo htmlspecialchars($data['video']['gop']); ?>">
              <label for="rtmp_gop">GOP :</label>
            </div>
          </div>
          <div class="card">
            <div class="dropdown-container">
              <span class="dropdown-label">Output Audio Formate :</span>
              <div class="dropdown">
                <select name="output_audio_formate" id="output_audio_formate">
                  <option value="mp2" <?php if ($data['audio']['format'] == 'mp2') echo 'selected'; ?>>mp2</option>
                  <option value="mp3" <?php if ($data['audio']['format'] == 'mp3') echo 'selected'; ?>>mp3</option>
                  <option value="aac" <?php if ($data['audio']['format'] == 'aac') echo 'selected'; ?>>aac</option>
                  <option value="ac3" <?php if ($data['audio']['format'] == 'ac3') echo 'selected'; ?>>ac3</option>
                </select>
              </div>
            </div>
            <div class="dropdown-container">
              <span class="dropdown-label">DB Gain :</span>
              <div class="dropdown">
                <select name="output_audio_db_gain" id="output_audio_db_gain">
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '-25dB') echo 'selected'; ?>>-25dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '-20dB') echo 'selected'; ?>>-20dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '-6dB') echo 'selected'; ?>>-15dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '-6dB') echo 'selected'; ?>>-10dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '-6dB') echo 'selected'; ?>>-6dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '-5dB') echo 'selected'; ?>>-5dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '-4dB') echo 'selected'; ?>>-4dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '-3dB') echo 'selected'; ?>>-3dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '-2dB') echo 'selected'; ?>>-2dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '-1dB') echo 'selected'; ?>>-1dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '0dB') echo 'selected'; ?>>0dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '1dB') echo 'selected'; ?>>1dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '2dB') echo 'selected'; ?>>2dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '3dB') echo 'selected'; ?>>3dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '4dB') echo 'selected'; ?>>4dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '5dB') echo 'selected'; ?>>5dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '6dB') echo 'selected'; ?>>6dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '6dB') echo 'selected'; ?>>10dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '6dB') echo 'selected'; ?>>15dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '6dB') echo 'selected'; ?>>20dB</option>
                  <option value="mp2" <?php if ($data['audio']['db_gain'] == '25dB') echo 'selected'; ?>>25dB</option>
                </select>
              </div>
            </div>
            <p></p>
            <div class="input-group">
              <input type="text" id="output_audio_sample_rate" name="output_audio_sample_rate" placeholder="48000" value="<?php echo htmlspecialchars($data['audio']['sample_rate']); ?>">
              <label for="output_audio_sample_rate">Sample Rate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="output_audio_bit_rate" name="output_audio_bit_rate" placeholder="96k" value="<?php echo htmlspecialchars($data['audio']['bit_rate']); ?>">
              <label for="output_audio_bit_rate">Bit Rate :</label>
            </div>
          </div>
        </div>

        <?php for ($i = 1; $i <= 11; $i++):
          $r = $data['rtmp_multiple'][$i];
        ?>
          <div class="input-container">
            <div class="input-group">
              <input type="text" id="rtmp_<?php echo $i; ?>" name="rtmp_<?php echo $i; ?>" placeholder="rtmp" value="<?php echo htmlspecialchars($r['url']); ?>">
              <label for="rtmp_<?php echo $i; ?>">RTMP URL <?php echo $i; ?></label>
            </div>
            <div class="input-group">
              <input type="text" id="rtmp_<?php echo $i; ?>_name" name="rtmp_<?php echo $i; ?>_name" placeholder="Rtmp Name <?php echo $i; ?>" value="<?php echo htmlspecialchars($r['name']); ?>">
              <label for="rtmp_<?php echo $i; ?>_name">Rtmp Name <?php echo $i; ?></label>
            </div>
            <div class="checkbox-group">
              <input type="checkbox" id="rtmp_<?php echo $i; ?>_enable" name="rtmp_<?php echo $i; ?>_enable" <?php if (!empty($r['enabled'])) echo 'checked'; ?>>
              <label for="rtmp_<?php echo $i; ?>_enable">Enable or Disable</label>
            </div>
          </div>
        <?php endfor; ?>
        <div style="text-align:center; width:100%; margin-top:12px;">
          <button type="submit" name="rtmp" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save RTMP</button>
        </div>

      </div>

      <div class="card wide">
        <h3>SRT Output</h3>
        <div class="dropdown-container">
          <span class="dropdown-label">Service Status :</span>
          <div class="dropdown">
            <select name="service_srt_multiple" id="service_srt_multiple">
              <option value="enable" <?php if ($data['service_srt_multiple'] == 'enable') echo 'selected'; ?>>Enable</option>
              <option value="disable" <?php if ($data['service_srt_multiple'] == 'disable') echo 'selected'; ?>>Disable</option>
            </select>
          </div>
        </div>
        <div class="card">

          <div class="dropdown-container">
            <span class="dropdown-label">Resolution :</span>
            <div class="dropdown">
              <select name="srt_output_resolution" id="srt_output_resolution">
                <option value="720x480" <?php if ($data['video']['resolution'] == '720x480') echo 'selected'; ?>>480p 720x480 NTSC DVD</option>
                <option value="720x576" <?php if ($data['video']['resolution'] == '720x576') echo 'selected'; ?>>576p 720x576 PAL DVD</option>
                <option value="1280x720" <?php if ($data['video']['resolution'] == '1280x720') echo 'selected'; ?>>720p 1280x720 HD</option>
                <option value="1920x1080" <?php if ($data['video']['resolution'] == '1920x1080') echo 'selected'; ?>>1080p 1920x1080 FHD</option>
                <option value="2560x1440" <?php if ($data['video']['resolution'] == '2560x1440') echo 'selected'; ?>>2k 2560x1440 QHD</option>
                <option value="2048x1080" <?php if ($data['video']['resolution'] == '2048x1080') echo 'selected'; ?>>2k 2048x1080 DCI 2K</option>
                <option value="3840x2160" <?php if ($data['video']['resolution'] == '3840x2160') echo 'selected'; ?>>4k 3840x2160 UHD</option>
                <option value="4096x2160" <?php if ($data['video']['resolution'] == '4096x2160') echo 'selected'; ?>>4k 4096x2160 DCI 4K</option>
                <option value="7680x4320" <?php if ($data['video']['resolution'] == '7680x4320') echo 'selected'; ?>>8k 7680x4320 UHD 8K</option>
                <option value="8192x4320" <?php if ($data['video']['resolution'] == '8192x4320') echo 'selected'; ?>>8k 8192x4320 DCI 8K</option>
              </select>
            </div>
          </div>
          <div class="input-group">
            <input type="text" id="srt_video_framerate" name="srt_video_framerate" placeholder="25" value="<?php echo htmlspecialchars($data['video']['framerate']); ?>">
            <label for="srt_video_framerate">Framerate :</label>
          </div>
          <div class="input-group">
            <input type="text" id="srt_data_rate" name="srt_data_rate" placeholder="4M" value="<?php echo htmlspecialchars($data['video']['data_rate']); ?>">
            <label for="srt_data_rate">Data Rate :</label>
          </div>
          <div class="input-group">
            <input type="text" id="srt_gop" name="srt_gop" placeholder="12" value="<?php echo htmlspecialchars($data['video']['gop']); ?>">
            <label for="srt_gop">GOP :</label>
          </div>
          <div class="dropdown-container">
            <span class="dropdown-label">Output Audio Formate :</span>
            <div class="dropdown">
              <select name="output_audio_formate" id="output_audio_formate">
                <option value="mp2" <?php if ($data['audio']['format'] == 'mp2') echo 'selected'; ?>>mp2</option>
                <option value="mp3" <?php if ($data['audio']['format'] == 'mp3') echo 'selected'; ?>>mp3</option>
                <option value="aac" <?php if ($data['audio']['format'] == 'aac') echo 'selected'; ?>>aac</option>
                <option value="ac3" <?php if ($data['audio']['format'] == 'ac3') echo 'selected'; ?>>ac3</option>
              </select>
            </div>
          </div>
          <div class="dropdown-container">
            <span class="dropdown-label">DB Gain :</span>
            <div class="dropdown">
              <select name="output_audio_db_gain" id="output_audio_db_gain">
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '-25dB') echo 'selected'; ?>>-25dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '-20dB') echo 'selected'; ?>>-20dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '-6dB') echo 'selected'; ?>>-15dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '-6dB') echo 'selected'; ?>>-10dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '-6dB') echo 'selected'; ?>>-6dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '-5dB') echo 'selected'; ?>>-5dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '-4dB') echo 'selected'; ?>>-4dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '-3dB') echo 'selected'; ?>>-3dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '-2dB') echo 'selected'; ?>>-2dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '-1dB') echo 'selected'; ?>>-1dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '0dB') echo 'selected'; ?>>0dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '1dB') echo 'selected'; ?>>1dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '2dB') echo 'selected'; ?>>2dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '3dB') echo 'selected'; ?>>3dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '4dB') echo 'selected'; ?>>4dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '5dB') echo 'selected'; ?>>5dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '6dB') echo 'selected'; ?>>6dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '6dB') echo 'selected'; ?>>10dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '6dB') echo 'selected'; ?>>15dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '6dB') echo 'selected'; ?>>20dB</option>
                <option value="mp2" <?php if ($data['audio']['db_gain'] == '25dB') echo 'selected'; ?>>25dB</option>
              </select>
            </div>
          </div>
          <p></p>
          <div class="input-group">
            <input type="text" id="output_audio_sample_rate" name="output_audio_sample_rate" placeholder="48000" value="<?php echo htmlspecialchars($data['audio']['sample_rate']); ?>">
            <label for="output_audio_sample_rate">Sample Rate :</label>
          </div>
          <div class="input-group">
            <input type="text" id="output_audio_bit_rate" name="output_audio_bit_rate" placeholder="96k" value="<?php echo htmlspecialchars($data['audio']['bit_rate']); ?>">
            <label for="output_audio_bit_rate">Bit Rate :</label>
          </div>

          <?php for ($i = 1; $i <= 11; $i++):
            $s = $data['srt_multiple'][$i];
          ?>
            <div class="input-container">
              <div class="input-group">
                <input type="text" id="srt_<?php echo $i; ?>" name="srt_<?php echo $i; ?>" placeholder="srt" value="<?php echo htmlspecialchars($s['url']); ?>">
                <label for="srt_<?php echo $i; ?>">SRT URL <?php echo $i; ?></label>
              </div>
              <div class="input-group">
                <input type="text" id="srt_<?php echo $i; ?>_name" name="srt_<?php echo $i; ?>_name" placeholder="Srt Name <?php echo $i; ?>" value="<?php echo htmlspecialchars($s['name']); ?>">
                <label for="srt_<?php echo $i; ?>_name">SRT Name <?php echo $i; ?></label>
              </div>
              <div class="checkbox-group">
                <input type="checkbox" id="srt_<?php echo $i; ?>_enable" name="srt_<?php echo $i; ?>_enable" <?php if (!empty($s['enabled'])) echo 'checked'; ?>>
                <label for="srt_<?php echo $i; ?>_enable">Enable or Disable</label>
              </div>
            </div>
          <?php endfor; ?>
          <div style="text-align:center; width:100%; margin-top:12px;">
            <button type="submit" name="srt" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save SRT</button>
          </div>
        </div>

        <div class="card wide">
          <h3>UDP</h3>
          <div class="dropdown-container">
            <span class="dropdown-label">Service Status :</span>
            <div class="dropdown">
              <select name="service_udp" id="service_udp">
                <option value="enable" <?php if ($data['service_udp'] == 'enable') echo 'selected'; ?>>Enable</option>
                <option value="disable" <?php if ($data['service_udp'] == 'disable') echo 'selected'; ?>>Disable</option>
              </select>
            </div>
          </div>
          <div class="input-group">
            <input type="text" id="udp" name="udp" placeholder="udp" value="<?php echo htmlspecialchars($data['udp']); ?>">
            <label for="udp">UDP Primary URL</label>
          </div>
          <div style="text-align:center; width:100%; margin-top:12px;">
            <button type="submit" name="udp" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save UDP</button>
          </div>
        </div>

        <div class="card wide">
          <h3>CUSTOM OUTPUT</h3>
          <div class="dropdown-container">
            <span class="dropdown-label">Service Status :</span>
            <div class="dropdown">
              <select name="service_custom" id="service_custom">
                <option value="enable" <?php if ($data['service_custom'] == 'enable') echo 'selected'; ?>>Enable</option>
                <option value="disable" <?php if ($data['service_custom'] == 'disable') echo 'selected'; ?>>Disable</option>
              </select>
            </div>
          </div>
          <div class="input-group">
            <input type="text" id="custom_output" name="custom_output" placeholder="custom" value="<?php echo htmlspecialchars($data['custom_output']); ?>">
            <label for="custom_output">Custom Output</label>
          </div>
          <div style="text-align:center; width:100%; margin-top:12px;">
            <button type="submit" name="custom" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save Custom</button>
          </div>
        </div>
      </div>

      <br><br><br>
</form>

<?php
if (!empty($errors)) {
  echo '<div style="color:#b00;text-align:center;margin-top:12px;">';
  foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>';
  echo '</div>';
}
if ($success) {
  echo '<div style="color:green;text-align:center;margin-top:12px;">' . htmlspecialchars($success) . '</div>';
}

?>

<?php include 'footer.php'; ?>