<?php include 'header.php'; ?>
<?php

$jsonFile = __DIR__ . '/output.json';

$defaults = [
  'service_display' => 'disable',
  'service_rtmp0_multiple' => 'disable',
  'service_rtmp0_hls' => 'disable',
  'service_rtmp0_dash' => 'disable',
  'service_rtmp1_multiple' => 'disable',
  'service_rtmp1_hls' => 'disable',
  'service_rtmp1_dash' => 'disable',
  'service_udp0' => 'disable',
  'service_udp1' => 'disable',
  'service_udp2' => 'disable',
  'service_srt_multiple' => 'disable',
  'service_custom' => 'disable',

  'rtmp_multiple0' => [],
  'rtmp_multiple1' => [],
  'srt_multiple'  => [],
  'rtmp0' => [
    'resolution' => '',
    'data_rate' => '',
    'framerate' => '',
    'gop' => '',
    'extra' => '',
    'audio_data_rate' => '',
    'audio_db_gain' => '',
    'audio_sample_rate' => ''
  ],
  'rtmp1' => [
    'resolution' => '',
    'data_rate' => '',
    'framerate' => '',
    'gop' => '',
    'extra' => '',
    'audio_data_rate' => '',
    'audio_db_gain' => '',
    'audio_sample_rate' => ''
  ],
  'udp0' => [
    'udp' => '',
    'format' => '',
    'resolution' => '',
    'data_rate' => '',
    'framerate' => '',
    'gop' => '',
    'extra' => '',
    'audio_format' => '',
    'audio_data_rate' => '',
    'audio_db_gain' => '',
    'audio_sample_rate' => ''
  ],
  'udp1' => [
    'udp' => '',
    'format' => '',
    'resolution' => '',
    'data_rate' => '',
    'framerate' => '',
    'gop' => '',
    'extra' => '',
    'audio_format' => '',
    'audio_data_rate' => '',
    'audio_db_gain' => '',
    'audio_sample_rate' => ''
  ],
  'udp2' => [
    'udp' => '',
    'format' => '',
    'resolution' => '',
    'data_rate' => '',
    'framerate' => '',
    'gop' => '',
    'extra' => '',
    'audio_format' => '',
    'audio_data_rate' => '',
    'audio_db_gain' => '',
    'audio_sample_rate' => ''
  ],
  'srt' => [
    'format' => '',
    'resolution' => '',
    'data_rate' => '',
    'framerate' => '',
    'gop' => '',
    'extra' => '',
    'audio_format' => '',
    'audio_data_rate' => '',
    'audio_db_gain' => '',
    'audio_sample_rate' => ''
  ],

  'display' => '1920x1080@60.00',
  'display_audio' => '0,3',

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
  $new['service_display'] = $get('service_display', $defaults['service_display']);
  $new['service_rtmp0_multiple'] = $get('service_rtmp0_multiple', $defaults['service_rtmp0_multiple']);
  $new['service_rtmp0_hls'] = $get('service_rtmp0_hls', $defaults['service_rtmp0_hls']);
  $new['service_rtmp0_dash'] = $get('service_rtmp0_dash', $defaults['service_rtmp0_dash']);
  $new['service_rtmp1_multiple'] = $get('service_rtmp1_multiple', $defaults['service_rtmp1_multiple']);
  $new['service_rtmp1_hls'] = $get('service_rtmp1_hls', $defaults['service_rtmp1_hls']);
  $new['service_rtmp1_dash'] = $get('service_rtmp1_dash', $defaults['service_rtmp1_dash']);
  $new['service_udp0'] = $get('service_udp0', $defaults['service_udp0']);
  $new['service_udp1'] = $get('service_udp1', $defaults['service_udp1']);
  $new['service_udp2'] = $get('service_udp2', $defaults['service_udp2']);
  $new['service_srt_multiple'] = $get('service_srt_multiple', $defaults['service_srt_multiple']);
  $new['service_custom'] = $get('service_custom', $defaults['service_custom']);

  $new['display'] = $get('display', $defaults['display']);
  $new['display_audio'] = $get('display_audio', $defaults['display_audio']);


  $new['rtmp0']['resolution'] = $get('rtmp0_resolution', $defaults['rtmp0']['resolution']);
  $new['rtmp0']['data_rate'] = $get('rtmp0_data_rate', $defaults['rtmp0']['data_rate']);
  $new['rtmp0']['framerate'] = $get('rtmp0_framerate', $defaults['rtmp0']['framerate']);
  $new['rtmp0']['gop'] = $get('rtmp0_gop', $defaults['rtmp0']['gop']);
  $new['rtmp0']['extra'] = $get('rtmp0_extra', $defaults['rtmp0']['extra']);
  $new['rtmp0']['audio_data_rate'] = $get('rtmp0_audio_data_rate', $defaults['rtmp0']['audio_data_rate']);
  $new['rtmp0']['audio_db_gain'] = $get('rtmp0_audio_db_gain', $defaults['rtmp0']['audio_db_gain']);
  $new['rtmp0']['audio_sample_rate'] = $get('rtmp0_audio_sample_rate', $defaults['rtmp0']['audio_sample_rate']);

  $new['rtmp1']['resolution'] = $get('rtmp1_resolution', $defaults['rtmp1']['resolution']);
  $new['rtmp1']['data_rate'] = $get('rtmp1_data_rate', $defaults['rtmp1']['data_rate']);
  $new['rtmp1']['framerate'] = $get('rtmp1_framerate', $defaults['rtmp1']['framerate']);
  $new['rtmp1']['gop'] = $get('rtmp1_gop', $defaults['rtmp1']['gop']);
  $new['rtmp1']['extra'] = $get('rtmp1_extra', $defaults['rtmp1']['extra']);
  $new['rtmp1']['audio_data_rate'] = $get('rtmp1_audio_data_rate', $defaults['rtmp1']['audio_data_rate']);
  $new['rtmp1']['audio_db_gain'] = $get('rtmp1_audio_db_gain', $defaults['rtmp1']['audio_db_gain']);
  $new['rtmp1']['audio_sample_rate'] = $get('rtmp1_audio_sample_rate', $defaults['rtmp1']['audio_sample_rate']);

  $new['udp0']['format'] = $get('udp0_format', $defaults['udp0']['format']);
  $new['udp0']['resolution'] = $get('udp0_resolution', $defaults['udp0']['resolution']);
  $new['udp0']['data_rate'] = $get('udp0_data_rate', $defaults['udp0']['data_rate']);
  $new['udp0']['framerate'] = $get('udp0_framerate', $defaults['udp0']['framerate']);
  $new['udp0']['gop'] = $get('udp0_gop', $defaults['udp0']['gop']);
  $new['udp0']['extra'] = $get('udp0_extra', $defaults['udp0']['extra']);
  $new['udp0']['audio_format'] = $get('udp0_audio_format', $defaults['udp0']['audio_format']);
  $new['udp0']['audio_data_rate'] = $get('udp0_audio_data_rate', $defaults['udp0']['audio_data_rate']);
  $new['udp0']['audio_db_gain'] = $get('udp0_audio_db_gain', $defaults['udp0']['audio_db_gain']);
  $new['udp0']['audio_sample_rate'] = $get('udp0_audio_sample_rate', $defaults['udp0']['audio_sample_rate']);
  $new['udp0']['udp'] = $get('udp0_ip', $defaults['udp0']['udp']);

  $new['udp1']['format'] = $get('udp1_format', $defaults['udp1']['format']);
  $new['udp1']['resolution'] = $get('udp1_resolution', $defaults['udp1']['resolution']);
  $new['udp1']['data_rate'] = $get('udp1_data_rate', $defaults['udp1']['data_rate']);
  $new['udp1']['framerate'] = $get('udp1_framerate', $defaults['udp1']['framerate']);
  $new['udp1']['gop'] = $get('udp1_gop', $defaults['udp1']['gop']);
  $new['udp1']['extra'] = $get('udp1_extra', $defaults['udp1']['extra']);
  $new['udp1']['audio_format'] = $get('udp1_audio_format', $defaults['udp1']['audio_format']);
  $new['udp1']['audio_data_rate'] = $get('udp1_audio_data_rate', $defaults['udp1']['audio_data_rate']);
  $new['udp1']['audio_db_gain'] = $get('udp1_audio_db_gain', $defaults['udp1']['audio_db_gain']);
  $new['udp1']['audio_sample_rate'] = $get('udp1_audio_sample_rate', $defaults['udp1']['audio_sample_rate']);
  $new['udp1']['udp'] = $get('udp1_ip', $defaults['udp1']['udp']);

  $new['udp2']['format'] = $get('udp2_format', $defaults['udp2']['format']);
  $new['udp2']['resolution'] = $get('udp2_resolution', $defaults['udp2']['resolution']);
  $new['udp2']['data_rate'] = $get('udp2_data_rate', $defaults['udp2']['data_rate']);
  $new['udp2']['framerate'] = $get('udp2_framerate', $defaults['udp2']['framerate']);
  $new['udp2']['gop'] = $get('udp2_gop', $defaults['udp2']['gop']);
  $new['udp2']['extra'] = $get('udp2_extra', $defaults['udp2']['extra']);
  $new['udp2']['audio_format'] = $get('udp2_audio_format', $defaults['udp2']['audio_format']);
  $new['udp2']['audio_data_rate'] = $get('udp2_audio_data_rate', $defaults['udp2']['audio_data_rate']);
  $new['udp2']['audio_db_gain'] = $get('udp2_audio_db_gain', $defaults['udp2']['audio_db_gain']);
  $new['udp2']['audio_sample_rate'] = $get('udp2_audio_sample_rate', $defaults['udp2']['audio_sample_rate']);
  $new['udp2']['udp'] = $get('udp2_ip', $defaults['udp2']['udp']);

  $new['srt0']['format'] = $get('srt0_resolution', $defaults['srt0']['format']);
  $new['srt0']['resolution'] = $get('srt0_resolution', $defaults['srt0']['resolution']);
  $new['srt0']['data_rate'] = $get('srt0_data_rate', $defaults['srt0']['data_rate']);
  $new['srt0']['framerate'] = $get('srt0_framerate', $defaults['srt0']['framerate']);
  $new['srt0']['gop'] = $get('srt0_gop', $defaults['srt0']['gop']);
  $new['srt0']['extra'] = $get('srt0_extra', $defaults['srt0']['extra']);
  $new['srt0']['audio_format'] = $get('srt0_audio_format', $defaults['srt0']['audio_format']);
  $new['srt0']['audio_data_rate'] = $get('srt0_audio_data_rate', $defaults['srt0']['audio_data_rate']);
  $new['srt0']['audio_db_gain'] = $get('srt0_audio_db_gain', $defaults['srt0']['audio_db_gain']);
  $new['srt0']['audio_sample_rate'] = $get('srt0_audio_sample_rate', $defaults['srt0']['audio_sample_rate']);

  $new['custom_output'] = $get('custom_output', '');

  if ($new['rtmp0']['gop'] !== '' && !ctype_digit((string)$new['rtmp0']['gop'])) {
    $errors[] = "RTMP0 GOP must be an integer.";
  }
  if ($new['rtmp1']['gop'] !== '' && !ctype_digit((string)$new['rtmp1']['gop'])) {
    $errors[] = "RTMP1 GOP must be an integer.";
  }
  if ($new['udp0']['gop'] !== '' && !ctype_digit((string)$new['udp0']['gop'])) {
    $errors[] = "UDP0 GOP must be an integer.";
  }
  if ($new['udp1']['gop'] !== '' && !ctype_digit((string)$new['udp1']['gop'])) {
    $errors[] = "UDP1 GOP must be an integer.";
  }
  if ($new['udp2']['gop'] !== '' && !ctype_digit((string)$new['udp2']['gop'])) {
    $errors[] = "UDP2 GOP must be an integer.";
  }
  if ($new['srt0']['gop'] !== '' && !ctype_digit((string)$new['srt0']['gop'])) {
    $errors[] = "SRT0 GOP must be an integer.";
  }


  for ($i = 1; $i <= 11; $i++) {
    $u0 = $get("rtmp0_{$i}", '');
    $n0 = $get("rtmp0_{$i}_name", '');
    $e0 = isset($_POST["rtmp0_{$i}_enable"]) ? true : false;
    $new['rtmp0_multiple'][$i] = ['url' => $u0, 'name' => $n0, 'enabled' => $e0];

    $u1 = $get("rtmp1_{$i}", '');
    $n1 = $get("rtmp1_{$i}_name", '');
    $e1 = isset($_POST["rtmp1_{$i}_enable"]) ? true : false;
    $new['rtmp1_multiple'][$i] = ['url' => $u1, 'name' => $n1, 'enabled' => $e1];

    $u2 = $get("srt_{$i}", '');
    $n2 = $get("srt_{$i}_name", '');
    $e2 = isset($_POST["srt_{$i}_enable"]) ? true : false;
    $new['srt_multiple'][$i] = ['url' => $u2, 'name' => $n2, 'enabled' => $e2];
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

  if (isset($_POST['rtmp0'])) {
    update_service("rtmp0");
  }
  if (isset($_POST['rtmp1'])) {
    update_service("rtmp1");
  }

  if (isset($_POST['udp0'])) {
    update_service("udp0");
  }
  if (isset($_POST['udp1'])) {
    update_service("udp1");
  }
  if (isset($_POST['udp2'])) {
    update_service("udp2");
  }
  if (isset($_POST['srt'])) {
    update_service("srt");
  }
  if (isset($_POST['custom'])) {
    update_service("custom");
  }
}
?>
<form method="POST">
  <div class="containerindex">
    <div class="grid">

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
            <select name="display" id="display">
              <!-- 4K (4096x2160p) -->
              <option value="4096x2160@23.98" <?php if ($data['display'] == '4096x2160@23.98') echo 'selected'; ?>>4096x2160@23.98</option>
              <option value="4096x2160@24.00" <?php if ($data['display'] == '4096x2160@24.00') echo 'selected'; ?>>4096x2160@24.00</option>
              <option value="4096x2160@25.00" <?php if ($data['display'] == '4096x2160@25.00') echo 'selected'; ?>>4096x2160@25.00</option>
              <option value="4096x2160@29.97" <?php if ($data['display'] == '4096x2160@29.97') echo 'selected'; ?>>4096x2160@29.97</option>
              <option value="4096x2160@30.00" <?php if ($data['display'] == '4096x2160@30.00') echo 'selected'; ?>>4096x2160@30.00</option>
              <option value="4096x2160@47.95" <?php if ($data['display'] == '4096x2160@47.95') echo 'selected'; ?>>4096x2160@47.95</option>
              <option value="4096x2160@48.00" <?php if ($data['display'] == '4096x2160@48.00') echo 'selected'; ?>>4096x2160@48.00</option>
              <option value="4096x2160@50.00" <?php if ($data['display'] == '4096x2160@50.00') echo 'selected'; ?>>4096x2160@50.00</option>
              <option value="4096x2160@59.94" <?php if ($data['display'] == '4096x2160@59.94') echo 'selected'; ?>>4096x2160@59.94</option>
              <option value="4096x2160@60.00" <?php if ($data['display'] == '4096x2160@60.00') echo 'selected'; ?>>4096x2160@60.00</option>

              <!-- UltraHD (3840x2160p) -->
              <option value="3840x2160@23.98" <?php if ($data['display'] == '3840x2160@23.98') echo 'selected'; ?>>3840x2160@23.98</option>
              <option value="3840x2160@24.00" <?php if ($data['display'] == '3840x2160@24.00') echo 'selected'; ?>>3840x2160@24.00</option>
              <option value="3840x2160@25.00" <?php if ($data['display'] == '3840x2160@25.00') echo 'selected'; ?>>3840x2160@25.00</option>
              <option value="3840x2160@29.97" <?php if ($data['display'] == '3840x2160@29.97') echo 'selected'; ?>>3840x2160@29.97</option>
              <option value="3840x2160@30.00" <?php if ($data['display'] == '3840x2160@30.00') echo 'selected'; ?>>3840x2160@30.00</option>
              <option value="3840x2160@50.00" <?php if ($data['display'] == '3840x2160@50.00') echo 'selected'; ?>>3840x2160@50.00</option>
              <option value="3840x2160@59.94" <?php if ($data['display'] == '3840x2160@59.94') echo 'selected'; ?>>3840x2160@59.94</option>
              <option value="3840x2160@60.00" <?php if ($data['display'] == '3840x2160@60.00') echo 'selected'; ?>>3840x2160@60.00</option>

              <!-- 2K (2048x1080p) -->
              <option value="2048x1080@23.98" <?php if ($data['display'] == '2048x1080@23.98') echo 'selected'; ?>>2048x1080@23.98</option>
              <option value="2048x1080@24.00" <?php if ($data['display'] == '2048x1080@24.00') echo 'selected'; ?>>2048x1080@24.00</option>
              <option value="2048x1080@25.00" <?php if ($data['display'] == '2048x1080@25.00') echo 'selected'; ?>>2048x1080@25.00</option>
              <option value="2048x1080@29.97" <?php if ($data['display'] == '2048x1080@29.97') echo 'selected'; ?>>2048x1080@29.97</option>
              <option value="2048x1080@30.00" <?php if ($data['display'] == '2048x1080@30.00') echo 'selected'; ?>>2048x1080@30.00</option>
              <option value="2048x1080@47.95" <?php if ($data['display'] == '2048x1080@47.95') echo 'selected'; ?>>2048x1080@47.95</option>
              <option value="2048x1080@48.00" <?php if ($data['display'] == '2048x1080@48.00') echo 'selected'; ?>>2048x1080@48.00</option>
              <option value="2048x1080@50.00" <?php if ($data['display'] == '2048x1080@50.00') echo 'selected'; ?>>2048x1080@50.00</option>
              <option value="2048x1080@59.94" <?php if ($data['display'] == '2048x1080@59.94') echo 'selected'; ?>>2048x1080@59.94</option>
              <option value="2048x1080@60.00" <?php if ($data['display'] == '2048x1080@60.00') echo 'selected'; ?>>2048x1080@60.00</option>

              <!-- FHD (1920x1080p) -->
              <option value="1920x1080@23.98" <?php if ($data['display'] == '1920x1080@23.98') echo 'selected'; ?>>1920x1080@23.98</option>
              <option value="1920x1080@24.00" <?php if ($data['display'] == '1920x1080@24.00') echo 'selected'; ?>>1920x1080@24.00</option>
              <option value="1920x1080@25.00" <?php if ($data['display'] == '1920x1080@25.00') echo 'selected'; ?>>1920x1080@25.00</option>
              <option value="1920x1080@29.97" <?php if ($data['display'] == '1920x1080@29.97') echo 'selected'; ?>>1920x1080@29.97</option>
              <option value="1920x1080@30.00" <?php if ($data['display'] == '1920x1080@30.00') echo 'selected'; ?>>1920x1080@30.00</option>
              <option value="1920x1080@50.00" <?php if ($data['display'] == '1920x1080@50.00') echo 'selected'; ?>>1920x1080@50.00</option>
              <option value="1920x1080@59.94" <?php if ($data['display'] == '1920x1080@59.94') echo 'selected'; ?>>1920x1080@59.94</option>
              <option value="1920x1080@60.00" <?php if ($data['display'] == '1920x1080@60.00') echo 'selected'; ?>>1920x1080@60.00</option>

              <!-- FHD Interlaced (1920x1080i) -->
              <option value="1920x1080i@50.00" <?php if ($data['display'] == '1920x1080i@50.00') echo 'selected'; ?>>1920x1080i@50.00</option>
              <option value="1920x1080i@59.94" <?php if ($data['display'] == '1920x1080i@59.94') echo 'selected'; ?>>1920x1080i@59.94</option>
              <option value="1920x1080i@60.00" <?php if ($data['display'] == '1920x1080i@60.00') echo 'selected'; ?>>1920x1080i@60.00</option>

              <!-- HD (1280x720p) -->
              <option value="1280x720@50.00" <?php if ($data['display'] == '1280x720@50.00') echo 'selected'; ?>>1280x720@50.00</option>
              <option value="1280x720@59.94" <?php if ($data['display'] == '1280x720@59.94') echo 'selected'; ?>>1280x720@59.94</option>
              <option value="1280x720@60.00" <?php if ($data['display'] == '1280x720@60.00') echo 'selected'; ?>>1280x720@60.00</option>

              <!-- SD Progressive -->
              <option value="720x576@50.00" <?php if ($data['display'] == '720x576@50.00') echo 'selected'; ?>>720x576@50.00</option>
              <option value="720x480@59.94" <?php if ($data['display'] == '720x480@59.94') echo 'selected'; ?>>720x480@59.94</option>

              <!-- SD Interlaced -->
              <option value="720x576i@25.00" <?php if ($data['display'] == '720x576i@25.00') echo 'selected'; ?>>720x576i@25.00</option>
              <option value="720x480i@29.97" <?php if ($data['display'] == '720x480i@29.97') echo 'selected'; ?>>720x480i@29.97</option>
            </select>
          </div>
        </div>

        <div class="dropdown-container">
          <span class="dropdown-label">Audio Output :</span>
          <div class="dropdown">
            <select name="display_audio" id="display_audio">
              <option value="0,0" <?php if ($data['display_audio'] == '0,0') echo 'selected'; ?>>0,0</option>
              <option value="0,1" <?php if ($data['display_audio'] == '0,1') echo 'selected'; ?>>0,1</option>
              <option value="0,2" <?php if ($data['display_audio'] == '0,2') echo 'selected'; ?>>0,2</option>
              <option value="0,3" <?php if ($data['display_audio'] == '0,3') echo 'selected'; ?>>0,3</option>
            </select>
          </div>
        </div>
        <div style="text-align:center; width:100%; margin-top:12px;">
          <button type="submit" name="display" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save Display</button>
        </div>

      </div>

      <div class="card wide">
        <h3>RTMP0 Output</h3>
        <div class="dropdown-container">
          <span class="dropdown-label">Service Status :</span>
          <div class="dropdown">
            <select name="service_rtmp0_multiple" id="service_rtmp0_multiple">
              <option value="enable" <?php if ($data['service_rtmp0_multiple'] == 'enable') echo 'selected'; ?>>Enable</option>
              <option value="disable" <?php if ($data['service_rtmp0_multiple'] == 'disable') echo 'selected'; ?>>Disable</option>
            </select>
          </div>
        </div>
        <div class="dropdown-container">
          <span class="dropdown-label">HLS :</span>
          <div class="dropdown">
            <select name="service_rtmp0_hls" id="service_rtmp0_hls">
              <option value="enable" <?php if ($data['service_rtmp0_hls'] == 'enable') echo 'selected'; ?>>Enable</option>
              <option value="disable" <?php if ($data['service_rtmp0_hls'] == 'disable') echo 'selected'; ?>>Disable</option>
            </select>
          </div>
        </div>
        <div class="dropdown-container">
          <span class="dropdown-label">DASH :</span>
          <div class="dropdown">
            <select name="service_rtmp0_dash" id="service_rtmp0_dash">
              <option value="enable" <?php if ($data['service_rtmp0_dash'] == 'enable') echo 'selected'; ?>>Enable</option>
              <option value="disable" <?php if ($data['service_rtmp0_dash'] == 'disable') echo 'selected'; ?>>Disable</option>
            </select>
          </div>
        </div>

        <div class="grid">
          <div class="card">
            <div class="dropdown-container">
              <span class="dropdown-label">Resolution :</span>
              <div class="dropdown">
                <select name="rtmp0_resolution" id="rtmp0_resolution">
                  <option value="720x480" <?php if ($data['rtmp0']['resolution'] == '720x480') echo 'selected'; ?>>480p 720x480 NTSC DVD</option>
                  <option value="720x576" <?php if ($data['rtmp0']['resolution'] == '720x576') echo 'selected'; ?>>576p 720x576 PAL DVD</option>
                  <option value="1280x720" <?php if ($data['rtmp0']['resolution'] == '1280x720') echo 'selected'; ?>>720p 1280x720 HD</option>
                  <option value="1920x1080" <?php if ($data['rtmp0']['resolution'] == '1920x1080') echo 'selected'; ?>>1080p 1920x1080 FHD</option>
                  <option value="2560x1440" <?php if ($data['rtmp0']['resolution'] == '2560x1440') echo 'selected'; ?>>2k 2560x1440 QHD</option>
                  <option value="2048x1080" <?php if ($data['rtmp0']['resolution'] == '2048x1080') echo 'selected'; ?>>2k 2048x1080 DCI 2K</option>
                  <option value="3840x2160" <?php if ($data['rtmp0']['resolution'] == '3840x2160') echo 'selected'; ?>>4k 3840x2160 UHD</option>
                  <option value="4096x2160" <?php if ($data['rtmp0']['resolution'] == '4096x2160') echo 'selected'; ?>>4k 4096x2160 DCI 4K</option>
                  <option value="7680x4320" <?php if ($data['rtmp0']['resolution'] == '7680x4320') echo 'selected'; ?>>8k 7680x4320 UHD 8K</option>
                  <option value="8192x4320" <?php if ($data['rtmp0']['resolution'] == '8192x4320') echo 'selected'; ?>>8k 8192x4320 DCI 8K</option>
                </select>
              </div>
            </div>
            <div class="input-group">
              <input type="text" id="rtmp0_data_rate" name="rtmp0_data_rate" placeholder="4M" value="<?php echo htmlspecialchars($data['rtmp0']['data_rate']); ?>">
              <label for="rtmp0_data_rate">Data Rate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="rtmp0_framerate" name="rtmp0_framerate" placeholder="25" value="<?php echo htmlspecialchars($data['rtmp0']['framerate']); ?>">
              <label for="rtmp0_framerate">Framerate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="rtmp0_gop" name="rtmp0_gop" placeholder="12" value="<?php echo htmlspecialchars($data['rtmp0']['gop']); ?>">
              <label for="rtmp0_gop">GOP :</label>
            </div>
          </div>
          <div class="card">
            <div class="dropdown-container">
              <span class="dropdown-label">DB Gain :</span>
              <div class="dropdown">
                <select name="rtmp0_audio_db_gain" id="rtmp0_audio_db_gain">
                  <option value="-25dB" <?php if ($data['rtmp0']['audio_db_gain'] == '-25dB') echo 'selected'; ?>>-25dB</option>
                  <option value="-20dB" <?php if ($data['rtmp0']['audio_db_gain'] == '-20dB') echo 'selected'; ?>>-20dB</option>
                  <option value="-15dB" <?php if ($data['rtmp0']['audio_db_gain'] == '-15dB') echo 'selected'; ?>>-15dB</option>
                  <option value="-10dB" <?php if ($data['rtmp0']['audio_db_gain'] == '-10dB') echo 'selected'; ?>>-10dB</option>
                  <option value="-6dB" <?php if ($data['rtmp0']['audio_db_gain'] == '-6dB') echo 'selected'; ?>>-6dB</option>
                  <option value="-5dB" <?php if ($data['rtmp0']['audio_db_gain'] == '-5dB') echo 'selected'; ?>>-5dB</option>
                  <option value="-4dB" <?php if ($data['rtmp0']['audio_db_gain'] == '-4dB') echo 'selected'; ?>>-4dB</option>
                  <option value="-3dB" <?php if ($data['rtmp0']['audio_db_gain'] == '-3dB') echo 'selected'; ?>>-3dB</option>
                  <option value="-2dB" <?php if ($data['rtmp0']['audio_db_gain'] == '-2dB') echo 'selected'; ?>>-2dB</option>
                  <option value="-1dB" <?php if ($data['rtmp0']['audio_db_gain'] == '-1dB') echo 'selected'; ?>>-1dB</option>
                  <option value="0dB" <?php if ($data['rtmp0']['audio_db_gain'] == '0dB') echo 'selected'; ?>>0dB</option>
                  <option value="1dB" <?php if ($data['rtmp0']['audio_db_gain'] == '1dB') echo 'selected'; ?>>1dB</option>
                  <option value="2dB" <?php if ($data['rtmp0']['audio_db_gain'] == '2dB') echo 'selected'; ?>>2dB</option>
                  <option value="3dB" <?php if ($data['rtmp0']['audio_db_gain'] == '3dB') echo 'selected'; ?>>3dB</option>
                  <option value="4dB" <?php if ($data['rtmp0']['audio_db_gain'] == '4dB') echo 'selected'; ?>>4dB</option>
                  <option value="5dB" <?php if ($data['rtmp0']['audio_db_gain'] == '5dB') echo 'selected'; ?>>5dB</option>
                  <option value="6dB" <?php if ($data['rtmp0']['audio_db_gain'] == '6dB') echo 'selected'; ?>>6dB</option>
                  <option value="10dB" <?php if ($data['rtmp0']['audio_db_gain'] == '10dB') echo 'selected'; ?>>10dB</option>
                  <option value="15dB" <?php if ($data['rtmp0']['audio_db_gain'] == '15dB') echo 'selected'; ?>>15dB</option>
                  <option value="20dB" <?php if ($data['rtmp0']['audio_db_gain'] == '20dB') echo 'selected'; ?>>20dB</option>
                  <option value="25dB" <?php if ($data['rtmp0']['audio_db_gain'] == '25dB') echo 'selected'; ?>>25dB</option>
                </select>
              </div>
            </div>
            <p></p>
            <div class="input-group">
              <input type="text" id="rtmp0_audio_data_rate" name="rtmp0_audio_data_rate" placeholder="96k" value="<?php echo htmlspecialchars($data['rtmp0']['audio_data_rate']); ?>">
              <label for="rtmp0_audio_data_rate">Bit Rate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="rtmp0_audio_sample_rate" name="rtmp0_audio_sample_rate" placeholder="48000" value="<?php echo htmlspecialchars($data['rtmp0']['audio_sample_rate']); ?>">
              <label for="rtmp0_audio_sample_rate">Sample Rate :</label>
            </div>
          </div>
        </div>
        <div class="input-group">
          <input type="text" id="rtmp0_extra" name="rtmp0_extra" value="<?php echo htmlspecialchars($data['rtmp0']['extra']); ?>">
          <label for="rtmp0_extra">Extra :</label>
        </div>

        <?php for ($i = 1; $i <= 11; $i++):
          $r = $data['rtmp0_multiple'][$i];
        ?>
          <div class="input-container">
            <div class="input-group">
              <input type="text" id="rtmp0_<?php echo $i; ?>" name="rtmp0_<?php echo $i; ?>" placeholder="rtmp" value="<?php echo htmlspecialchars($r['url']); ?>">
              <label for="rtmp0_<?php echo $i; ?>">RTMP URL <?php echo $i; ?></label>
            </div>
            <div class="input-group">
              <input type="text" id="rtmp0_<?php echo $i; ?>_name" name="rtmp0_<?php echo $i; ?>_name" placeholder="Rtmp Name <?php echo $i; ?>" value="<?php echo htmlspecialchars($r['name']); ?>">
              <label for="rtmp0_<?php echo $i; ?>_name">Rtmp Name <?php echo $i; ?></label>
            </div>
            <div class="checkbox-group">
              <input type="checkbox" id="rtmp0_<?php echo $i; ?>_enable" name="rtmp0_<?php echo $i; ?>_enable" <?php if (!empty($r['enabled'])) echo 'checked'; ?>>
              <label for="rtmp0_<?php echo $i; ?>_enable">Enable or Disable</label>
            </div>
          </div>
        <?php endfor; ?>
        <div style="text-align:center; width:100%; margin-top:12px;">
          <button type="submit" name="rtmp0" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save RTMP0</button>
        </div>
      </div>

      <div class="card wide">
        <h3>RTMP1 Output</h3>
        <div class="dropdown-container">
          <span class="dropdown-label">Service Status :</span>
          <div class="dropdown">
            <select name="service_rtmp1_multiple" id="service_rtmp1_multiple">
              <option value="enable" <?php if ($data['service_rtmp1_multiple'] == 'enable') echo 'selected'; ?>>Enable</option>
              <option value="disable" <?php if ($data['service_rtmp1_multiple'] == 'disable') echo 'selected'; ?>>Disable</option>
            </select>
          </div>
        </div>
        <div class="dropdown-container">
          <span class="dropdown-label">HLS :</span>
          <div class="dropdown">
            <select name="service_rtmp1_hls" id="service_rtmp1_hls">
              <option value="enable" <?php if ($data['service_rtmp1_hls'] == 'enable') echo 'selected'; ?>>Enable</option>
              <option value="disable" <?php if ($data['service_rtmp1_hls'] == 'disable') echo 'selected'; ?>>Disable</option>
            </select>
          </div>
        </div>
        <div class="dropdown-container">
          <span class="dropdown-label">DASH :</span>
          <div class="dropdown">
            <select name="service_rtmp1_dash" id="service_rtmp1_dash">
              <option value="enable" <?php if ($data['service_rtmp1_dash'] == 'enable') echo 'selected'; ?>>Enable</option>
              <option value="disable" <?php if ($data['service_rtmp1_dash'] == 'disable') echo 'selected'; ?>>Disable</option>
            </select>
          </div>
        </div>

        <div class="grid">
          <div class="card">
            <div class="dropdown-container">
              <span class="dropdown-label">Resolution :</span>
              <div class="dropdown">
                <select name="rtmp1_resolution" id="rtmp1_resolution">
                  <option value="720x480" <?php if ($data['rtmp1']['resolution'] == '720x480') echo 'selected'; ?>>480p 720x480 NTSC DVD</option>
                  <option value="720x576" <?php if ($data['rtmp1']['resolution'] == '720x576') echo 'selected'; ?>>576p 720x576 PAL DVD</option>
                  <option value="1280x720" <?php if ($data['rtmp1']['resolution'] == '1280x720') echo 'selected'; ?>>720p 1280x720 HD</option>
                  <option value="1920x1080" <?php if ($data['rtmp1']['resolution'] == '1920x1080') echo 'selected'; ?>>1080p 1920x1080 FHD</option>
                  <option value="2560x1440" <?php if ($data['rtmp1']['resolution'] == '2560x1440') echo 'selected'; ?>>2k 2560x1440 QHD</option>
                  <option value="2048x1080" <?php if ($data['rtmp1']['resolution'] == '2048x1080') echo 'selected'; ?>>2k 2048x1080 DCI 2K</option>
                  <option value="3840x2160" <?php if ($data['rtmp1']['resolution'] == '3840x2160') echo 'selected'; ?>>4k 3840x2160 UHD</option>
                  <option value="4096x2160" <?php if ($data['rtmp1']['resolution'] == '4096x2160') echo 'selected'; ?>>4k 4096x2160 DCI 4K</option>
                  <option value="7680x4320" <?php if ($data['rtmp1']['resolution'] == '7680x4320') echo 'selected'; ?>>8k 7680x4320 UHD 8K</option>
                  <option value="8192x4320" <?php if ($data['rtmp1']['resolution'] == '8192x4320') echo 'selected'; ?>>8k 8192x4320 DCI 8K</option>
                </select>
              </div>
            </div>
            <div class="input-group">
              <input type="text" id="rtmp1_data_rate" name="rtmp1_data_rate" placeholder="4M" value="<?php echo htmlspecialchars($data['rtmp1']['data_rate']); ?>">
              <label for="rtmp1_data_rate">Data Rate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="rtmp1_framerate" name="rtmp1_framerate" placeholder="25" value="<?php echo htmlspecialchars($data['rtmp1']['framerate']); ?>">
              <label for="rtmp1_framerate">Framerate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="rtmp1_gop" name="rtmp1_gop" placeholder="12" value="<?php echo htmlspecialchars($data['rtmp1']['gop']); ?>">
              <label for="rtmp1_gop">GOP :</label>
            </div>
          </div>
          <div class="card">
            <div class="dropdown-container">
              <span class="dropdown-label">DB Gain :</span>
              <div class="dropdown">
                <select name="rtmp1_audio_db_gain" id="rtmp1_audio_db_gain">
                  <option value="-25dB" <?php if ($data['rtmp1']['audio_db_gain'] == '-25dB') echo 'selected'; ?>>-25dB</option>
                  <option value="-20dB" <?php if ($data['rtmp1']['audio_db_gain'] == '-20dB') echo 'selected'; ?>>-20dB</option>
                  <option value="-15dB" <?php if ($data['rtmp1']['audio_db_gain'] == '-15dB') echo 'selected'; ?>>-15dB</option>
                  <option value="-10dB" <?php if ($data['rtmp1']['audio_db_gain'] == '-10dB') echo 'selected'; ?>>-10dB</option>
                  <option value="-6dB" <?php if ($data['rtmp1']['audio_db_gain'] == '-6dB') echo 'selected'; ?>>-6dB</option>
                  <option value="-5dB" <?php if ($data['rtmp1']['audio_db_gain'] == '-5dB') echo 'selected'; ?>>-5dB</option>
                  <option value="-4dB" <?php if ($data['rtmp1']['audio_db_gain'] == '-4dB') echo 'selected'; ?>>-4dB</option>
                  <option value="-3dB" <?php if ($data['rtmp1']['audio_db_gain'] == '-3dB') echo 'selected'; ?>>-3dB</option>
                  <option value="-2dB" <?php if ($data['rtmp1']['audio_db_gain'] == '-2dB') echo 'selected'; ?>>-2dB</option>
                  <option value="-1dB" <?php if ($data['rtmp1']['audio_db_gain'] == '-1dB') echo 'selected'; ?>>-1dB</option>
                  <option value="0dB" <?php if ($data['rtmp1']['audio_db_gain'] == '0dB') echo 'selected'; ?>>0dB</option>
                  <option value="1dB" <?php if ($data['rtmp1']['audio_db_gain'] == '1dB') echo 'selected'; ?>>1dB</option>
                  <option value="2dB" <?php if ($data['rtmp1']['audio_db_gain'] == '2dB') echo 'selected'; ?>>2dB</option>
                  <option value="3dB" <?php if ($data['rtmp1']['audio_db_gain'] == '3dB') echo 'selected'; ?>>3dB</option>
                  <option value="4dB" <?php if ($data['rtmp1']['audio_db_gain'] == '4dB') echo 'selected'; ?>>4dB</option>
                  <option value="5dB" <?php if ($data['rtmp1']['audio_db_gain'] == '5dB') echo 'selected'; ?>>5dB</option>
                  <option value="6dB" <?php if ($data['rtmp1']['audio_db_gain'] == '6dB') echo 'selected'; ?>>6dB</option>
                  <option value="10dB" <?php if ($data['rtmp1']['audio_db_gain'] == '10dB') echo 'selected'; ?>>10dB</option>
                  <option value="15dB" <?php if ($data['rtmp1']['audio_db_gain'] == '15dB') echo 'selected'; ?>>15dB</option>
                  <option value="20dB" <?php if ($data['rtmp1']['audio_db_gain'] == '20dB') echo 'selected'; ?>>20dB</option>
                  <option value="25dB" <?php if ($data['rtmp1']['audio_db_gain'] == '25dB') echo 'selected'; ?>>25dB</option>
                </select>
              </div>
            </div>
            <p></p>
            <div class="input-group">
              <input type="text" id="rtmp1_audio_data_rate" name="rtmp1_audio_data_rate" placeholder="96k" value="<?php echo htmlspecialchars($data['rtmp1']['audio_data_rate']); ?>">
              <label for="rtmp1_audio_data_rate">Bit Rate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="rtmp1_audio_sample_rate" name="rtmp1_audio_sample_rate" placeholder="48000" value="<?php echo htmlspecialchars($data['rtmp1']['audio_sample_rate']); ?>">
              <label for="rtmp1_audio_sample_rate">Sample Rate :</label>
            </div>
          </div>
        </div>
        <div class="input-group">
          <input type="text" id="rtmp1_extra" name="rtmp1_extra" value="<?php echo htmlspecialchars($data['rtmp1']['extra']); ?>">
          <label for="rtmp1_extra">Extra :</label>
        </div>

        <?php for ($i = 1; $i <= 11; $i++):
          $r = $data['rtmp1_multiple'][$i];
        ?>
          <div class="input-container">
            <div class="input-group">
              <input type="text" id="rtmp1_<?php echo $i; ?>" name="rtmp1_<?php echo $i; ?>" placeholder="rtmp" value="<?php echo htmlspecialchars($r['url']); ?>">
              <label for="rtmp1_<?php echo $i; ?>">RTMP URL <?php echo $i; ?></label>
            </div>
            <div class="input-group">
              <input type="text" id="rtmp1_<?php echo $i; ?>_name" name="rtmp1_<?php echo $i; ?>_name" placeholder="Rtmp Name <?php echo $i; ?>" value="<?php echo htmlspecialchars($r['name']); ?>">
              <label for="rtmp1_<?php echo $i; ?>_name">Rtmp Name <?php echo $i; ?></label>
            </div>
            <div class="checkbox-group">
              <input type="checkbox" id="rtmp1_<?php echo $i; ?>_enable" name="rtmp1_<?php echo $i; ?>_enable" <?php if (!empty($r['enabled'])) echo 'checked'; ?>>
              <label for="rtmp1_<?php echo $i; ?>_enable">Enable or Disable</label>
            </div>
          </div>
        <?php endfor; ?>
        <div style="text-align:center; width:100%; margin-top:12px;">
          <button type="submit" name="rtmp1" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save rtmp1</button>
        </div>
      </div>

      <div class="card wide">
        <h3>UDP0 Output</h3>
        <div class="dropdown-container">
          <span class="dropdown-label">Service Status :</span>
          <div class="dropdown">
            <select name="service_udp0" id="service_udp0">
              <option value="enable" <?php if ($data['service_udp0'] == 'enable') echo 'selected'; ?>>Enable</option>
              <option value="disable" <?php if ($data['service_udp0'] == 'disable') echo 'selected'; ?>>Disable</option>
            </select>
          </div>
        </div>

        <div class="grid">
          <div class="card">
            <div class="dropdown-container">
              <span class="dropdown-label">format :</span>
              <div class="dropdown">
                <select name="udp0_format" id="udp0_format">
                  <option value="mpeg2video" <?php if ($data['udp0']['format'] == 'mpeg2video') echo 'selected'; ?>>mpeg2</option>
                  <option value="h264_qsv" <?php if ($data['udp0']['format'] == 'h264_qsv') echo 'selected'; ?>>h264</option>
                  <option value="h265" <?php if ($data['udp0']['format'] == 'h265') echo 'selected'; ?>>h265</option>
                </select>
              </div>
            </div>
            <div class="dropdown-container">
              <span class="dropdown-label">Resolution :</span>
              <div class="dropdown">
                <select name="udp0_resolution" id="udp0_resolution">
                  <option value="720x480" <?php if ($data['udp0']['resolution'] == '720x480') echo 'selected'; ?>>480p 720x480 NTSC DVD</option>
                  <option value="720x576" <?php if ($data['udp0']['resolution'] == '720x576') echo 'selected'; ?>>576p 720x576 PAL DVD</option>
                  <option value="1280x720" <?php if ($data['udp0']['resolution'] == '1280x720') echo 'selected'; ?>>720p 1280x720 HD</option>
                  <option value="1920x1080" <?php if ($data['udp0']['resolution'] == '1920x1080') echo 'selected'; ?>>1080p 1920x1080 FHD</option>
                  <option value="2560x1440" <?php if ($data['udp0']['resolution'] == '2560x1440') echo 'selected'; ?>>2k 2560x1440 QHD</option>
                  <option value="2048x1080" <?php if ($data['udp0']['resolution'] == '2048x1080') echo 'selected'; ?>>2k 2048x1080 DCI 2K</option>
                  <option value="3840x2160" <?php if ($data['udp0']['resolution'] == '3840x2160') echo 'selected'; ?>>4k 3840x2160 UHD</option>
                  <option value="4096x2160" <?php if ($data['udp0']['resolution'] == '4096x2160') echo 'selected'; ?>>4k 4096x2160 DCI 4K</option>
                  <option value="7680x4320" <?php if ($data['udp0']['resolution'] == '7680x4320') echo 'selected'; ?>>8k 7680x4320 UHD 8K</option>
                  <option value="8192x4320" <?php if ($data['udp0']['resolution'] == '8192x4320') echo 'selected'; ?>>8k 8192x4320 DCI 8K</option>
                </select>
              </div>
            </div>
            <div class="input-group">
              <input type="text" id="udp0_data_rate" name="udp0_data_rate" placeholder="4M" value="<?php echo htmlspecialchars($data['udp0']['data_rate']); ?>">
              <label for="udp0_data_rate">Data Rate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="udp0_framerate" name="udp0_framerate" placeholder="25" value="<?php echo htmlspecialchars($data['udp0']['framerate']); ?>">
              <label for="udp0_framerate">Framerate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="udp0_gop" name="udp0_gop" placeholder="12" value="<?php echo htmlspecialchars($data['udp0']['gop']); ?>">
              <label for="udp0_gop">GOP :</label>
            </div>
          </div>
          <div class="card">
            <div class="dropdown-container">
              <span class="dropdown-label">format :</span>
              <div class="dropdown">
                <select name="udp0_audio_format" id="udp0_audio_format">
                  <option value="mp2" <?php if ($data['udp0']['format'] == 'mp2') echo 'selected'; ?>>mp2</option>
                  <option value="mp3" <?php if ($data['udp0']['format'] == 'mp3') echo 'selected'; ?>>mp3</option>
                  <option value="aac" <?php if ($data['udp0']['format'] == 'aac') echo 'selected'; ?>>aac</option>
                  <option value="ac3" <?php if ($data['udp0']['format'] == 'ac3') echo 'selected'; ?>>ac3</option>
                </select>
              </div>
            </div>
            <div class="dropdown-container">
              <span class="dropdown-label">DB Gain :</span>
              <div class="dropdown">
                <select name="udp0_audio_db_gain" id="udp0_audio_db_gain">
                  <option value="-25dB" <?php if ($data['udp0']['audio_db_gain'] == '-25dB') echo 'selected'; ?>>-25dB</option>
                  <option value="-20dB" <?php if ($data['udp0']['audio_db_gain'] == '-20dB') echo 'selected'; ?>>-20dB</option>
                  <option value="-15dB" <?php if ($data['udp0']['audio_db_gain'] == '-15dB') echo 'selected'; ?>>-15dB</option>
                  <option value="-10dB" <?php if ($data['udp0']['audio_db_gain'] == '-10dB') echo 'selected'; ?>>-10dB</option>
                  <option value="-6dB" <?php if ($data['udp0']['audio_db_gain'] == '-6dB') echo 'selected'; ?>>-6dB</option>
                  <option value="-5dB" <?php if ($data['udp0']['audio_db_gain'] == '-5dB') echo 'selected'; ?>>-5dB</option>
                  <option value="-4dB" <?php if ($data['udp0']['audio_db_gain'] == '-4dB') echo 'selected'; ?>>-4dB</option>
                  <option value="-3dB" <?php if ($data['udp0']['audio_db_gain'] == '-3dB') echo 'selected'; ?>>-3dB</option>
                  <option value="-2dB" <?php if ($data['udp0']['audio_db_gain'] == '-2dB') echo 'selected'; ?>>-2dB</option>
                  <option value="-1dB" <?php if ($data['udp0']['audio_db_gain'] == '-1dB') echo 'selected'; ?>>-1dB</option>
                  <option value="0dB" <?php if ($data['udp0']['audio_db_gain'] == '0dB') echo 'selected'; ?>>0dB</option>
                  <option value="1dB" <?php if ($data['udp0']['audio_db_gain'] == '1dB') echo 'selected'; ?>>1dB</option>
                  <option value="2dB" <?php if ($data['udp0']['audio_db_gain'] == '2dB') echo 'selected'; ?>>2dB</option>
                  <option value="3dB" <?php if ($data['udp0']['audio_db_gain'] == '3dB') echo 'selected'; ?>>3dB</option>
                  <option value="4dB" <?php if ($data['udp0']['audio_db_gain'] == '4dB') echo 'selected'; ?>>4dB</option>
                  <option value="5dB" <?php if ($data['udp0']['audio_db_gain'] == '5dB') echo 'selected'; ?>>5dB</option>
                  <option value="6dB" <?php if ($data['udp0']['audio_db_gain'] == '6dB') echo 'selected'; ?>>6dB</option>
                  <option value="10dB" <?php if ($data['udp0']['audio_db_gain'] == '10dB') echo 'selected'; ?>>10dB</option>
                  <option value="15dB" <?php if ($data['udp0']['audio_db_gain'] == '15dB') echo 'selected'; ?>>15dB</option>
                  <option value="20dB" <?php if ($data['udp0']['audio_db_gain'] == '20dB') echo 'selected'; ?>>20dB</option>
                  <option value="25dB" <?php if ($data['udp0']['audio_db_gain'] == '25dB') echo 'selected'; ?>>25dB</option>
                </select>
              </div>
            </div>
            <p></p>
            <div class="input-group">
              <input type="text" id="udp0_audio_data_rate" name="udp0_audio_data_rate" placeholder="96k" value="<?php echo htmlspecialchars($data['udp0']['audio_data_rate']); ?>">
              <label for="udp0_audio_data_rate">Bit Rate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="udp0_audio_sample_rate" name="udp0_audio_sample_rate" placeholder="48000" value="<?php echo htmlspecialchars($data['udp0']['audio_sample_rate']); ?>">
              <label for="udp0_audio_sample_rate">Sample Rate :</label>
            </div>
          </div>
        </div>
        <div class="input-group">
          <input type="text" id="udp0_extra" name="udp0_extra" value="<?php echo htmlspecialchars($data['udp0']['extra']); ?>">
          <label for="udp0_extra">Extra :</label>
        </div>
        <div class="input-group">
          <input type="text" id="udp0_ip" name="udp0_ip" placeholder="udp0_ip" value="<?php echo htmlspecialchars($data['udp0']['udp']); ?>">
          <label for="udp0_ip">UDP0 IP</label>
        </div>

        <div style="text-align:center; width:100%; margin-top:12px;">
          <button type="submit" name="udp0" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save udp0</button>
        </div>
      </div>

      <div class="card wide">
        <h3>udp1 Output</h3>
        <div class="dropdown-container">
          <span class="dropdown-label">Service Status :</span>
          <div class="dropdown">
            <select name="service_udp1" id="service_udp1">
              <option value="enable" <?php if ($data['service_udp1'] == 'enable') echo 'selected'; ?>>Enable</option>
              <option value="disable" <?php if ($data['service_udp1'] == 'disable') echo 'selected'; ?>>Disable</option>
            </select>
          </div>
        </div>

        <div class="grid">
          <div class="card">
            <div class="dropdown-container">
              <span class="dropdown-label">format :</span>
              <div class="dropdown">
                <select name="udp1_format" id="udp1_format">
                  <option value="mpeg2video" <?php if ($data['udp1']['format'] == 'mpeg2video') echo 'selected'; ?>>mpeg2</option>
                  <option value="h264_qsv" <?php if ($data['udp1']['format'] == 'h264_qsv') echo 'selected'; ?>>h264</option>
                  <option value="h265" <?php if ($data['udp1']['format'] == 'h265') echo 'selected'; ?>>h265</option>
                </select>
              </div>
            </div>
            <div class="dropdown-container">
              <span class="dropdown-label">Resolution :</span>
              <div class="dropdown">
                <select name="udp1_resolution" id="udp1_resolution">
                  <option value="720x480" <?php if ($data['udp1']['resolution'] == '720x480') echo 'selected'; ?>>480p 720x480 NTSC DVD</option>
                  <option value="720x576" <?php if ($data['udp1']['resolution'] == '720x576') echo 'selected'; ?>>576p 720x576 PAL DVD</option>
                  <option value="1280x720" <?php if ($data['udp1']['resolution'] == '1280x720') echo 'selected'; ?>>720p 1280x720 HD</option>
                  <option value="1920x1080" <?php if ($data['udp1']['resolution'] == '1920x1080') echo 'selected'; ?>>1080p 1920x1080 FHD</option>
                  <option value="2560x1440" <?php if ($data['udp1']['resolution'] == '2560x1440') echo 'selected'; ?>>2k 2560x1440 QHD</option>
                  <option value="2048x1080" <?php if ($data['udp1']['resolution'] == '2048x1080') echo 'selected'; ?>>2k 2048x1080 DCI 2K</option>
                  <option value="3840x2160" <?php if ($data['udp1']['resolution'] == '3840x2160') echo 'selected'; ?>>4k 3840x2160 UHD</option>
                  <option value="4096x2160" <?php if ($data['udp1']['resolution'] == '4096x2160') echo 'selected'; ?>>4k 4096x2160 DCI 4K</option>
                  <option value="7680x4320" <?php if ($data['udp1']['resolution'] == '7680x4320') echo 'selected'; ?>>8k 7680x4320 UHD 8K</option>
                  <option value="8192x4320" <?php if ($data['udp1']['resolution'] == '8192x4320') echo 'selected'; ?>>8k 8192x4320 DCI 8K</option>
                </select>
              </div>
            </div>
            <div class="input-group">
              <input type="text" id="udp1_data_rate" name="udp1_data_rate" placeholder="4M" value="<?php echo htmlspecialchars($data['udp1']['data_rate']); ?>">
              <label for="udp1_data_rate">Data Rate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="udp1_framerate" name="udp1_framerate" placeholder="25" value="<?php echo htmlspecialchars($data['udp1']['framerate']); ?>">
              <label for="udp1_framerate">Framerate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="udp1_gop" name="udp1_gop" placeholder="12" value="<?php echo htmlspecialchars($data['udp1']['gop']); ?>">
              <label for="udp1_gop">GOP :</label>
            </div>
          </div>
          <div class="card">
            <div class="dropdown-container">
              <span class="dropdown-label">format :</span>
              <div class="dropdown">
                <select name="udp1_audio_format" id="udp1_audio_format">
                  <option value="mp2" <?php if ($data['udp1']['format'] == 'mp2') echo 'selected'; ?>>mp2</option>
                  <option value="mp3" <?php if ($data['udp1']['format'] == 'mp3') echo 'selected'; ?>>mp3</option>
                  <option value="aac" <?php if ($data['udp1']['format'] == 'aac') echo 'selected'; ?>>aac</option>
                  <option value="ac3" <?php if ($data['udp1']['format'] == 'ac3') echo 'selected'; ?>>ac3</option>
                </select>
              </div>
            </div>

            <div class="dropdown-container">
              <span class="dropdown-label">DB Gain :</span>
              <div class="dropdown">
                <select name="udp1_audio_db_gain" id="udp1_audio_db_gain">
                  <option value="-25dB" <?php if ($data['udp1']['audio_db_gain'] == '-25dB') echo 'selected'; ?>>-25dB</option>
                  <option value="-20dB" <?php if ($data['udp1']['audio_db_gain'] == '-20dB') echo 'selected'; ?>>-20dB</option>
                  <option value="-15dB" <?php if ($data['udp1']['audio_db_gain'] == '-15dB') echo 'selected'; ?>>-15dB</option>
                  <option value="-10dB" <?php if ($data['udp1']['audio_db_gain'] == '-10dB') echo 'selected'; ?>>-10dB</option>
                  <option value="-6dB" <?php if ($data['udp1']['audio_db_gain'] == '-6dB') echo 'selected'; ?>>-6dB</option>
                  <option value="-5dB" <?php if ($data['udp1']['audio_db_gain'] == '-5dB') echo 'selected'; ?>>-5dB</option>
                  <option value="-4dB" <?php if ($data['udp1']['audio_db_gain'] == '-4dB') echo 'selected'; ?>>-4dB</option>
                  <option value="-3dB" <?php if ($data['udp1']['audio_db_gain'] == '-3dB') echo 'selected'; ?>>-3dB</option>
                  <option value="-2dB" <?php if ($data['udp1']['audio_db_gain'] == '-2dB') echo 'selected'; ?>>-2dB</option>
                  <option value="-1dB" <?php if ($data['udp1']['audio_db_gain'] == '-1dB') echo 'selected'; ?>>-1dB</option>
                  <option value="0dB" <?php if ($data['udp1']['audio_db_gain'] == '0dB') echo 'selected'; ?>>0dB</option>
                  <option value="1dB" <?php if ($data['udp1']['audio_db_gain'] == '1dB') echo 'selected'; ?>>1dB</option>
                  <option value="2dB" <?php if ($data['udp1']['audio_db_gain'] == '2dB') echo 'selected'; ?>>2dB</option>
                  <option value="3dB" <?php if ($data['udp1']['audio_db_gain'] == '3dB') echo 'selected'; ?>>3dB</option>
                  <option value="4dB" <?php if ($data['udp1']['audio_db_gain'] == '4dB') echo 'selected'; ?>>4dB</option>
                  <option value="5dB" <?php if ($data['udp1']['audio_db_gain'] == '5dB') echo 'selected'; ?>>5dB</option>
                  <option value="6dB" <?php if ($data['udp1']['audio_db_gain'] == '6dB') echo 'selected'; ?>>6dB</option>
                  <option value="10dB" <?php if ($data['udp1']['audio_db_gain'] == '10dB') echo 'selected'; ?>>10dB</option>
                  <option value="15dB" <?php if ($data['udp1']['audio_db_gain'] == '15dB') echo 'selected'; ?>>15dB</option>
                  <option value="20dB" <?php if ($data['udp1']['audio_db_gain'] == '20dB') echo 'selected'; ?>>20dB</option>
                  <option value="25dB" <?php if ($data['udp1']['audio_db_gain'] == '25dB') echo 'selected'; ?>>25dB</option>
                </select>
              </div>
            </div>
            <p></p>
            <div class="input-group">
              <input type="text" id="udp1_audio_data_rate" name="udp1_audio_data_rate" placeholder="96k" value="<?php echo htmlspecialchars($data['udp1']['audio_data_rate']); ?>">
              <label for="udp1_audio_data_rate">Bit Rate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="udp1_audio_sample_rate" name="udp1_audio_sample_rate" placeholder="48000" value="<?php echo htmlspecialchars($data['udp1']['audio_sample_rate']); ?>">
              <label for="udp1_audio_sample_rate">Sample Rate :</label>
            </div>
          </div>
        </div>
        <div class="input-group">
          <input type="text" id="udp1_extra" name="udp1_extra" value="<?php echo htmlspecialchars($data['udp1']['extra']); ?>">
          <label for="udp1_extra">Extra :</label>
        </div>
        <div class="input-group">
          <input type="text" id="udp1_ip" name="udp1_ip" placeholder="udp1_ip" value="<?php echo htmlspecialchars($data['udp1']['udp']); ?>">
          <label for="udp1_ip">udp1 IP</label>
        </div>

        <div style="text-align:center; width:100%; margin-top:12px;">
          <button type="submit" name="udp1" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save udp1</button>
        </div>
      </div>
      <div class="card wide">
        <h3>udp2 Output</h3>
        <div class="dropdown-container">
          <span class="dropdown-label">Service Status :</span>
          <div class="dropdown">
            <select name="service_udp2" id="service_udp2">
              <option value="enable" <?php if ($data['service_udp2'] == 'enable') echo 'selected'; ?>>Enable</option>
              <option value="disable" <?php if ($data['service_udp2'] == 'disable') echo 'selected'; ?>>Disable</option>
            </select>
          </div>
        </div>

        <div class="grid">
          <div class="card">
            <div class="dropdown-container">
              <span class="dropdown-label">format :</span>
              <div class="dropdown">
                <select name="udp2_format" id="udp2_format">
                  <option value="mpeg2video" <?php if ($data['udp2']['format'] == 'mpeg2video') echo 'selected'; ?>>mpeg2</option>
                  <option value="h264_qsv" <?php if ($data['udp2']['format'] == 'h264_qsv') echo 'selected'; ?>>h264</option>
                  <option value="h265" <?php if ($data['udp2']['format'] == 'h265') echo 'selected'; ?>>h265</option>
                </select>
              </div>
            </div>
            <div class="dropdown-container">
              <span class="dropdown-label">Resolution :</span>
              <div class="dropdown">
                <select name="udp2_resolution" id="udp2_resolution">
                  <option value="720x480" <?php if ($data['udp2']['resolution'] == '720x480') echo 'selected'; ?>>480p 720x480 NTSC DVD</option>
                  <option value="720x576" <?php if ($data['udp2']['resolution'] == '720x576') echo 'selected'; ?>>576p 720x576 PAL DVD</option>
                  <option value="1280x720" <?php if ($data['udp2']['resolution'] == '1280x720') echo 'selected'; ?>>720p 1280x720 HD</option>
                  <option value="1920x1080" <?php if ($data['udp2']['resolution'] == '1920x1080') echo 'selected'; ?>>1080p 1920x1080 FHD</option>
                  <option value="2560x1440" <?php if ($data['udp2']['resolution'] == '2560x1440') echo 'selected'; ?>>2k 2560x1440 QHD</option>
                  <option value="2048x1080" <?php if ($data['udp2']['resolution'] == '2048x1080') echo 'selected'; ?>>2k 2048x1080 DCI 2K</option>
                  <option value="3840x2160" <?php if ($data['udp2']['resolution'] == '3840x2160') echo 'selected'; ?>>4k 3840x2160 UHD</option>
                  <option value="4096x2160" <?php if ($data['udp2']['resolution'] == '4096x2160') echo 'selected'; ?>>4k 4096x2160 DCI 4K</option>
                  <option value="7680x4320" <?php if ($data['udp2']['resolution'] == '7680x4320') echo 'selected'; ?>>8k 7680x4320 UHD 8K</option>
                  <option value="8192x4320" <?php if ($data['udp2']['resolution'] == '8192x4320') echo 'selected'; ?>>8k 8192x4320 DCI 8K</option>
                </select>
              </div>
            </div>
            <div class="input-group">
              <input type="text" id="udp2_data_rate" name="udp2_data_rate" placeholder="4M" value="<?php echo htmlspecialchars($data['udp2']['data_rate']); ?>">
              <label for="udp2_data_rate">Data Rate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="udp2_framerate" name="udp2_framerate" placeholder="25" value="<?php echo htmlspecialchars($data['udp2']['framerate']); ?>">
              <label for="udp2_framerate">Framerate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="udp2_gop" name="udp2_gop" placeholder="12" value="<?php echo htmlspecialchars($data['udp2']['gop']); ?>">
              <label for="udp2_gop">GOP :</label>
            </div>
          </div>
          <div class="card">
            <div class="dropdown-container">
              <span class="dropdown-label">format :</span>
              <div class="dropdown">
                <select name="udp2_audio_format" id="udp2_audio_format">
                  <option value="mp2" <?php if ($data['udp2']['format'] == 'mp2') echo 'selected'; ?>>mp2</option>
                  <option value="mp3" <?php if ($data['udp2']['format'] == 'mp3') echo 'selected'; ?>>mp3</option>
                  <option value="aac" <?php if ($data['udp2']['format'] == 'aac') echo 'selected'; ?>>aac</option>
                  <option value="ac3" <?php if ($data['udp2']['format'] == 'ac3') echo 'selected'; ?>>ac3</option>
                </select>
              </div>
            </div>

            <div class="dropdown-container">
              <span class="dropdown-label">DB Gain :</span>
              <div class="dropdown">
                <select name="udp2_audio_db_gain" id="udp2_audio_db_gain">
                  <option value="-25dB" <?php if ($data['udp2']['audio_db_gain'] == '-25dB') echo 'selected'; ?>>-25dB</option>
                  <option value="-20dB" <?php if ($data['udp2']['audio_db_gain'] == '-20dB') echo 'selected'; ?>>-20dB</option>
                  <option value="-15dB" <?php if ($data['udp2']['audio_db_gain'] == '-15dB') echo 'selected'; ?>>-15dB</option>
                  <option value="-10dB" <?php if ($data['udp2']['audio_db_gain'] == '-10dB') echo 'selected'; ?>>-10dB</option>
                  <option value="-6dB" <?php if ($data['udp2']['audio_db_gain'] == '-6dB') echo 'selected'; ?>>-6dB</option>
                  <option value="-5dB" <?php if ($data['udp2']['audio_db_gain'] == '-5dB') echo 'selected'; ?>>-5dB</option>
                  <option value="-4dB" <?php if ($data['udp2']['audio_db_gain'] == '-4dB') echo 'selected'; ?>>-4dB</option>
                  <option value="-3dB" <?php if ($data['udp2']['audio_db_gain'] == '-3dB') echo 'selected'; ?>>-3dB</option>
                  <option value="-2dB" <?php if ($data['udp2']['audio_db_gain'] == '-2dB') echo 'selected'; ?>>-2dB</option>
                  <option value="-1dB" <?php if ($data['udp2']['audio_db_gain'] == '-1dB') echo 'selected'; ?>>-1dB</option>
                  <option value="0dB" <?php if ($data['udp2']['audio_db_gain'] == '0dB') echo 'selected'; ?>>0dB</option>
                  <option value="1dB" <?php if ($data['udp2']['audio_db_gain'] == '1dB') echo 'selected'; ?>>1dB</option>
                  <option value="2dB" <?php if ($data['udp2']['audio_db_gain'] == '2dB') echo 'selected'; ?>>2dB</option>
                  <option value="3dB" <?php if ($data['udp2']['audio_db_gain'] == '3dB') echo 'selected'; ?>>3dB</option>
                  <option value="4dB" <?php if ($data['udp2']['audio_db_gain'] == '4dB') echo 'selected'; ?>>4dB</option>
                  <option value="5dB" <?php if ($data['udp2']['audio_db_gain'] == '5dB') echo 'selected'; ?>>5dB</option>
                  <option value="6dB" <?php if ($data['udp2']['audio_db_gain'] == '6dB') echo 'selected'; ?>>6dB</option>
                  <option value="10dB" <?php if ($data['udp2']['audio_db_gain'] == '10dB') echo 'selected'; ?>>10dB</option>
                  <option value="15dB" <?php if ($data['udp2']['audio_db_gain'] == '15dB') echo 'selected'; ?>>15dB</option>
                  <option value="20dB" <?php if ($data['udp2']['audio_db_gain'] == '20dB') echo 'selected'; ?>>20dB</option>
                  <option value="25dB" <?php if ($data['udp2']['audio_db_gain'] == '25dB') echo 'selected'; ?>>25dB</option>
                </select>
              </div>
            </div>
            <p></p>
            <div class="input-group">
              <input type="text" id="udp2_audio_data_rate" name="udp2_audio_data_rate" placeholder="96k" value="<?php echo htmlspecialchars($data['udp2']['audio_data_rate']); ?>">
              <label for="udp2_audio_data_rate">Bit Rate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="udp2_audio_sample_rate" name="udp2_audio_sample_rate" placeholder="48000" value="<?php echo htmlspecialchars($data['udp2']['audio_sample_rate']); ?>">
              <label for="udp2_audio_sample_rate">Sample Rate :</label>
            </div>
          </div>
        </div>
        <div class="input-group">
          <input type="text" id="udp2_extra" name="udp2_extra" value="<?php echo htmlspecialchars($data['udp2']['extra']); ?>">
          <label for="udp2_extra">Extra :</label>
        </div>
        <div class="input-group">
          <input type="text" id="udp2_ip" name="udp2_ip" placeholder="udp2_ip" value="<?php echo htmlspecialchars($data['udp2']['udp']); ?>">
          <label for="udp2_ip">UDP2 IP</label>
        </div>

        <div style="text-align:center; width:100%; margin-top:12px;">
          <button type="submit" name="udp2" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save udp2</button>
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

        <div class="grid">
          <div class="card">
            <div class="dropdown-container">
              <span class="dropdown-label">format :</span>
              <div class="dropdown">
                <select name="srt_format" id="srt_format">
                  <option value="mpeg2video" <?php if ($data['srt']['format'] == 'mpeg2video') echo 'selected'; ?>>mpeg2</option>
                  <option value="h264_qsv" <?php if ($data['srt']['format'] == 'h264_qsv') echo 'selected'; ?>>h264</option>
                  <option value="h265" <?php if ($data['srt']['format'] == 'h265') echo 'selected'; ?>>h265</option>
                </select>
              </div>
            </div>
            <div class="dropdown-container">
              <span class="dropdown-label">Resolution :</span>
              <div class="dropdown">
                <select name="srt_resolution" id="srt_resolution">
                  <option value="720x480" <?php if ($data['srt']['resolution'] == '720x480') echo 'selected'; ?>>480p 720x480 NTSC DVD</option>
                  <option value="720x576" <?php if ($data['srt']['resolution'] == '720x576') echo 'selected'; ?>>576p 720x576 PAL DVD</option>
                  <option value="1280x720" <?php if ($data['srt']['resolution'] == '1280x720') echo 'selected'; ?>>720p 1280x720 HD</option>
                  <option value="1920x1080" <?php if ($data['srt']['resolution'] == '1920x1080') echo 'selected'; ?>>1080p 1920x1080 FHD</option>
                  <option value="2560x1440" <?php if ($data['srt']['resolution'] == '2560x1440') echo 'selected'; ?>>2k 2560x1440 QHD</option>
                  <option value="2048x1080" <?php if ($data['srt']['resolution'] == '2048x1080') echo 'selected'; ?>>2k 2048x1080 DCI 2K</option>
                  <option value="3840x2160" <?php if ($data['srt']['resolution'] == '3840x2160') echo 'selected'; ?>>4k 3840x2160 UHD</option>
                  <option value="4096x2160" <?php if ($data['srt']['resolution'] == '4096x2160') echo 'selected'; ?>>4k 4096x2160 DCI 4K</option>
                  <option value="7680x4320" <?php if ($data['srt']['resolution'] == '7680x4320') echo 'selected'; ?>>8k 7680x4320 UHD 8K</option>
                  <option value="8192x4320" <?php if ($data['srt']['resolution'] == '8192x4320') echo 'selected'; ?>>8k 8192x4320 DCI 8K</option>
                </select>
              </div>
            </div>
            <div class="input-group">
              <input type="text" id="srt_data_rate" name="srt_data_rate" placeholder="4M" value="<?php echo htmlspecialchars($data['srt']['data_rate']); ?>">
              <label for="srt_data_rate">Data Rate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="srt_framerate" name="srt_framerate" placeholder="25" value="<?php echo htmlspecialchars($data['srt']['framerate']); ?>">
              <label for="srt_framerate">Framerate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="srt_gop" name="srt_gop" placeholder="12" value="<?php echo htmlspecialchars($data['srt']['gop']); ?>">
              <label for="srt_gop">GOP :</label>
            </div>
          </div>
          <div class="card">
            <div class="dropdown-container">
              <span class="dropdown-label">format :</span>
              <div class="dropdown">
                <select name="srt_audio_format" id="srt_audio_format">
                  <option value="mp2" <?php if ($data['srt']['format'] == 'mp2') echo 'selected'; ?>>mp2</option>
                  <option value="mp3" <?php if ($data['srt']['format'] == 'mp3') echo 'selected'; ?>>mp3</option>
                  <option value="aac" <?php if ($data['srt']['format'] == 'aac') echo 'selected'; ?>>aac</option>
                  <option value="ac3" <?php if ($data['srt']['format'] == 'ac3') echo 'selected'; ?>>ac3</option>
                </select>
              </div>
            </div>

            <div class="dropdown-container">
              <span class="dropdown-label">DB Gain :</span>
              <div class="dropdown">
                <select name="srt_audio_db_gain" id="srt_audio_db_gain">
                  <option value="-25dB" <?php if ($data['srt']['audio_db_gain'] == '-25dB') echo 'selected'; ?>>-25dB</option>
                  <option value="-20dB" <?php if ($data['srt']['audio_db_gain'] == '-20dB') echo 'selected'; ?>>-20dB</option>
                  <option value="-15dB" <?php if ($data['srt']['audio_db_gain'] == '-15dB') echo 'selected'; ?>>-15dB</option>
                  <option value="-10dB" <?php if ($data['srt']['audio_db_gain'] == '-10dB') echo 'selected'; ?>>-10dB</option>
                  <option value="-6dB" <?php if ($data['srt']['audio_db_gain'] == '-6dB') echo 'selected'; ?>>-6dB</option>
                  <option value="-5dB" <?php if ($data['srt']['audio_db_gain'] == '-5dB') echo 'selected'; ?>>-5dB</option>
                  <option value="-4dB" <?php if ($data['srt']['audio_db_gain'] == '-4dB') echo 'selected'; ?>>-4dB</option>
                  <option value="-3dB" <?php if ($data['srt']['audio_db_gain'] == '-3dB') echo 'selected'; ?>>-3dB</option>
                  <option value="-2dB" <?php if ($data['srt']['audio_db_gain'] == '-2dB') echo 'selected'; ?>>-2dB</option>
                  <option value="-1dB" <?php if ($data['srt']['audio_db_gain'] == '-1dB') echo 'selected'; ?>>-1dB</option>
                  <option value="0dB" <?php if ($data['srt']['audio_db_gain'] == '0dB') echo 'selected'; ?>>0dB</option>
                  <option value="1dB" <?php if ($data['srt']['audio_db_gain'] == '1dB') echo 'selected'; ?>>1dB</option>
                  <option value="2dB" <?php if ($data['srt']['audio_db_gain'] == '2dB') echo 'selected'; ?>>2dB</option>
                  <option value="3dB" <?php if ($data['srt']['audio_db_gain'] == '3dB') echo 'selected'; ?>>3dB</option>
                  <option value="4dB" <?php if ($data['srt']['audio_db_gain'] == '4dB') echo 'selected'; ?>>4dB</option>
                  <option value="5dB" <?php if ($data['srt']['audio_db_gain'] == '5dB') echo 'selected'; ?>>5dB</option>
                  <option value="6dB" <?php if ($data['srt']['audio_db_gain'] == '6dB') echo 'selected'; ?>>6dB</option>
                  <option value="10dB" <?php if ($data['srt']['audio_db_gain'] == '10dB') echo 'selected'; ?>>10dB</option>
                  <option value="15dB" <?php if ($data['srt']['audio_db_gain'] == '15dB') echo 'selected'; ?>>15dB</option>
                  <option value="20dB" <?php if ($data['srt']['audio_db_gain'] == '20dB') echo 'selected'; ?>>20dB</option>
                  <option value="25dB" <?php if ($data['srt']['audio_db_gain'] == '25dB') echo 'selected'; ?>>25dB</option>
                </select>
              </div>
            </div>
            <p></p>
            <div class="input-group">
              <input type="text" id="srt_audio_data_rate" name="srt_audio_data_rate" placeholder="96k" value="<?php echo htmlspecialchars($data['audio']['bit_rate']); ?>">
              <label for="srt_audio_data_rate">Bit Rate :</label>
            </div>
            <div class="input-group">
              <input type="text" id="srt_audio_sample_rate" name="srt_audio_sample_rate" placeholder="48000" value="<?php echo htmlspecialchars($data['srt']['audio_sample_rate']); ?>">
              <label for="srt_audio_sample_rate">Sample Rate :</label>
            </div>
          </div>
        </div>
        <div class="input-group">
          <input type="text" id="srt_extra" name="srt_extra" value="<?php echo htmlspecialchars($data['srt']['extra']); ?>">
          <label for="srt_extra">Extra :</label>
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
          <button type="submit" name="srt" style="background:#c00;color:#fff;padding:10px 20px;border:none;font-weight:bold;border-radius:6px;">Save srt</button>
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