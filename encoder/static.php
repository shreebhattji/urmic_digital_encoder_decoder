<?php

function generateRandomString($length = 16)
{
    $bytes = random_bytes(ceil($length / 2));
    $randomString = bin2hex($bytes);
    return substr($randomString, 0, $length);
}
function setptsFromMs($ms)
{
    // convert ms → seconds
    $sec = $ms / 1000;

    // format with up to 3 decimals (avoid scientific notation)
    $secFormatted = number_format($sec, 3, '.', '');

    return 'setpts=PTS+' . $secFormatted . '/TB';
}

function adelayFromMs($ms, $channels = 2)
{
    // build "ms|ms|ms..." pattern for each audio channel
    $parts = array_fill(0, $channels, (string)$ms);
    $pattern = implode('|', $parts);

    return 'adelay=' . $pattern;
}


function update_service($which_service)
{

    $input = "";
    $input_source = "";
    $input_rtmp_mount = "";
    $input_rtmp_pass = "";
    $output = "";
    $srt_pass1 = "";
    $srt_pass2 = "";
    $srt_pass3 = "";
    $rtmp0_multiple[] = [];
    $rtmp1_multiple[] = [];
    $srt_multiple[] = [];

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


    $jsonFile = __DIR__ . '/input.json';
    if (file_exists($jsonFile)) {
        $raw = file_get_contents($jsonFile);
        $data = json_decode($raw, true);
        if (!is_array($data)) $data = $defaults;
    }

    $input_source = $data['input'];
    $use_common_backend = $data['use_common_backend'];
    $input_rtmp_mount = $data['rtmp']['mount'];
    $input_rtmp_pass = $data['rtmp']['password'];
    $srt_pass1 = $data['srt']['stream_id_1'];
    $srt_pass2 = $data['srt']['stream_id_2'];
    $srt_pass3 = $data['srt']['stream_id_3'];
    $common_backend_resolution = $data['common_backend']['resolution'];
    $common_backend_data_rate = $data['common_backend']['data_rate'];
    $common_backend_framerate = $data['common_backend']['framerate'];
    $common_backend_gop = $data['common_backend']['gop'];
    $common_backend_audio_db_gain = $data['common_backend']['audio_db_gain'];
    $common_backend_audio_data_rate = $data['common_backend']['audio_data_rate'];
    $common_backend_audio_sample_rate = $data['common_backend']['audio_sample_rate'];
    $common_backend_extra = $data['common_backend']['extra'];
    $common_backend_resolution = str_replace("x", ":", $common_backend_resolution);
    $hdmi_delay_video = $data['hdmi']['video_delay'];
    $hdmi_delay_audio = $data['hdmi']['audio_delay'];

    switch ($use_common_backend) {
        case "copy_input":
            switch ($input_source) {
                case "hdmi":
                    $input .= "ffmpeg -hwaccel auto -hide_banner -f v4l2 -thread_queue_size 512 -input_format mjpeg "
                        . " -video_size " . $data['hdmi']['resolution']
                        . " -framerate " . $data['hdmi']['framerate']
                        . " -f alsa -i " . $data['hdmi']['audio_source']
                        . " -c:v h264_qsv -b:v 5M -maxrate 5M -bufsize 12M -c:a aac -b:a 265k -ar 48000 -tune zerolatency ";
                    if ($hdmi_delay_video != "")
                        $input .= "-vf " . setptsFromMs($hdmi_delay_video);

                    if ($hdmi_delay_audio != "")
                        $input .= adelayFromMs($hdmi_delay_audio, 2);

                    $input .= " -f mpegts " . ' "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1&ttl=1"';
                    break;
                case "url":
                    $input .= "ffmpeg -hide_banner -stream_loop -1 -re -i " . $data['url'] . " -c:v copy -c:a copy -f mpegts " . ' "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1&ttl=1"';
                    break;
                case "udp":
                    $input .= 'ffmpeg -hide_banner -stream_loop -1 -re -i "' . $data['udp'] . " -c:v copy -c:a copy -f mpegts " . ' "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1&ttl=1"';
                    break;
                case "rtmp":
                    $input .= "ffmpeg -hide_banner -stream_loop -1 -re -i rtmp://127.0.0.1:1935/" . $$input_rtmp_mount . "/" . $input_rtmp_pass .  " -c:v copy -c:a copy -f mpegts " . ' "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1&ttl=1"';
                    break;
                case "srt":
                    $input .= "ffmpeg -hide_banner -stream_loop -1 -re -i srt://127.0.0.1:1937/shree/bhatt/" . $srt_pass3 . " -c:v copy -c:a copy -f mpegts " . ' "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1&ttl=1"';;
                    break;
            }
            break;
        case "use_common_backend":
            switch ($input_source) {
                case "hdmi":
                    $input .= "ffmpeg -hwaccel auto -hide_banner -f v4l2 -thread_queue_size 512 -input_format mjpeg -video_size " . $data['hdmi']['resolution']
                        . " -framerate " . $data['hdmi']['framerate'] . " -i /dev/video0 -f alsa -i " . $data['hdmi']['audio_source']
                        . " -c:v h264_qsv ";
                    if ($hdmi_delay_video != "")
                        $input .= ' -vf "scale=' . $common_backend_resolution . ',' . setptsFromMs($hdmi_delay_video) . '"';
                    else
                        $input .= ' -vf "scale=' . $common_backend_resolution . '"';
                    $input .= " -b:v " . $common_backend_data_rate
                        . " -maxrate " . $common_backend_data_rate
                        . " -bufsize 12M"
                        . " -r " . $common_backend_framerate
                        . " -g " . $common_backend_gop
                        . " -c:a aac "
                        . " -b:a " . $common_backend_audio_data_rate
                        . ' -ar ' . $common_backend_audio_sample_rate
                        . ' ' . $common_backend_extra;
                    if ($hdmi_delay_audio != "")
                        $input .= ' -af "volume=' . $common_backend_audio_db_gain . ',' . adelayFromMs($hdmi_delay_audio, 2) . '"';
                    else
                        $input .= ' -af "volume=' . $common_backend_audio_db_gain . '"';
                    $input .= " -tune zerolatency -f mpegts "
                        . ' "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1&ttl=1"';
                    break;
                case "url":
                    $input .= "ffmpeg -hide_banner -stream_loop -1 -re -i " . $data['url']
                        . " -c:v h264_qsv "
                        . ' -vf "scale=' . $common_backend_resolution . '"'
                        . " -b:v " . $common_backend_data_rate
                        . " -maxrate " . $common_backend_data_rate
                        . " -bufsize 12M"
                        . " -r " . $common_backend_framerate
                        . " -g " . $common_backend_gop
                        . " -c:a aac "
                        . " -b:a " . $common_backend_audio_data_rate
                        . ' -af "volume=' . $common_backend_audio_db_gain . '"'
                        . ' -ar ' . $common_backend_audio_sample_rate
                        . ' ' . $common_backend_extra . " -f mpegts "
                        . ' "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1&ttl=1"';
                    break;
                case "udp":
                    $input .= 'ffmpeg -hide_banner -stream_loop -1 -re -i "' . $data['udp']
                        . " -c:v h264_qsv "
                        . ' -vf "scale=' . $common_backend_resolution . '"'
                        . " -b:v " . $common_backend_data_rate
                        . " -maxrate " . $common_backend_data_rate
                        . " -bufsize 12M"
                        . " -r " . $common_backend_framerate
                        . " -g " . $common_backend_gop
                        . " -c:a aac "
                        . " -b:a " . $common_backend_audio_data_rate
                        . ' -af "volume=' . $common_backend_audio_db_gain . '"'
                        . ' -ar ' . $common_backend_audio_sample_rate
                        . ' ' . $common_backend_extra . " -f mpegts "
                        . ' "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1&ttl=1"';
                    break;
                case "rtmp":
                    $input .= "ffmpeg -hide_banner -stream_loop -1 -re -i rtmp://127.0.0.1:1935/" . $$input_rtmp_mount . "/" . $input_rtmp_pass
                        . " -c:v h264_qsv "
                        . ' -vf "scale=' . $common_backend_resolution . '"'
                        . " -b:v " . $common_backend_data_rate
                        . " -maxrate " . $common_backend_data_rate
                        . " -bufsize 12M"
                        . " -r " . $common_backend_framerate
                        . " -g " . $common_backend_gop
                        . " -c:a aac "
                        . " -b:a " . $common_backend_audio_data_rate
                        . ' -af "volume=' . $common_backend_audio_db_gain . '"'
                        . ' -ar ' . $common_backend_audio_sample_rate
                        . ' ' . $common_backend_extra . " -f mpegts "
                        . ' "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1&ttl=1"';

                    break;
                case "srt":
                    $input .= "ffmpeg -hide_banner -stream_loop -1 -re -i srt://127.0.0.1:1937/shree/bhatt/" . $srt_pass3
                        . " -c:v h264_qsv "
                        . ' -vf "scale=' . $common_backend_resolution . '"'
                        . " -b:v " . $common_backend_data_rate
                        . " -maxrate " . $common_backend_data_rate
                        . " -bufsize 12M"
                        . " -r " . $common_backend_framerate
                        . " -g " . $common_backend_gop
                        . " -c:a aac "
                        . " -b:a " . $common_backend_audio_data_rate
                        . ' -af "volume=' . $common_backend_audio_db_gain . '"'
                        . ' -ar ' . $common_backend_audio_sample_rate
                        . ' ' . $common_backend_extra . " -f mpegts "
                        . ' "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1&ttl=1"';

                    break;
            }
            break;
        case "transcode_every_time":
            switch ($input_source) {
                case "hdmi":
                    echo "<script>alert('HDMI can no use same input multiple time');</script>";
                    break;
                case "url":
                    $input_transcode_every_time =  $data['url'];
                    break;
                case "udp":
                    $input_transcode_every_time =  $data['udp'];
                    break;
                case "rtmp":
                    $input_transcode_every_time =  "rtmp://127.0.0.1:1935/" . $$input_rtmp_mount . "/" . $input_rtmp_pass;
                    break;
                case "srt":
                    $input_transcode_every_time =  "srt://127.0.0.1:1937/shree/bhatt/" . $srt_pass3;
                    break;
            }
            break;
    }

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

        'rtmp0_multiple' => [],
        'rtmp1_multiple' => [],
        'srt_multiple'  => [],
        'rtmp0' => [
            'common_backend' => 'enable',
            'resolution' => '1920x1080',
            'data_rate' => '6M',
            'framerate' => '30',
            'gop' => '30',
            'extra' => '',
            'audio_data_rate' => '128k',
            'audio_db_gain' => '0dB',
            'audio_sample_rate' => '48000'
        ],
        'rtmp1' => [
            'common_backend' => 'disable',
            'resolution' => '720x576',
            'data_rate' => '1.5M',
            'framerate' => '25',
            'gop' => '25',
            'extra' => '',
            'audio_data_rate' => '96k',
            'audio_db_gain' => '0dB',
            'audio_sample_rate' => '48000'
        ],
        'udp0' => [
            'common_backend' => 'disable',
            'udp' => 'udp://@224.1.1.1:8001',
            'format' => 'h264_qsv',
            'resolution' => '1280x720',
            'data_rate' => '2.2M',
            'framerate' => '25',
            'gop' => '25',
            'extra' => '',
            'audio_format' => 'aac',
            'audio_data_rate' => '128k',
            'audio_db_gain' => '0dB',
            'audio_sample_rate' => '48000'
        ],
        'udp1' => [
            'common_backend' => 'disable',
            'udp' => 'udp://@224.1.1.1:8001',
            'format' => 'h264_qsv',
            'resolution' => '720x576',
            'data_rate' => '1.5M',
            'framerate' => '25',
            'gop' => '25',
            'extra' => '',
            'audio_format' => 'mp2',
            'audio_data_rate' => '128k',
            'audio_db_gain' => '0dB',
            'audio_sample_rate' => '48000'
        ],
        'udp2' => [
            'common_backend' => 'disable',
            'udp' => 'udp://@224.1.1.1:8002',
            'format' => 'mpeg2video',
            'resolution' => '720x576',
            'data_rate' => '3M',
            'framerate' => '25',
            'gop' => '25',
            'extra' => '',
            'audio_format' => 'mp2',
            'audio_data_rate' => '96k',
            'audio_db_gain' => '0dB',
            'audio_sample_rate' => '48000'
        ],
        'srt' => [
            'common_backend' => 'enable',
            'format' => 'mpeg2video',
            'resolution' => '1920x1080',
            'data_rate' => '6M',
            'framerate' => '50',
            'gop' => '50',
            'extra' => '',
            'audio_format' => 'aac',
            'audio_data_rate' => '256k',
            'audio_db_gain' => '0dB',
            'audio_sample_rate' => '48000'
        ],

        'display' => '1920x1080@60.00',
        'display_audio' => '0,3',

        'custom_output' => ''
    ];

    for ($i = 1; $i <= 11; $i++) {
        $defaults['rtmp0_multiple'][$i] = ['url' => '', 'name' => '', 'enabled' => false];
        $defaults['rtmp1_multiple'][$i] = ['url' => '', 'name' => '', 'enabled' => false];
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

    $service_display = $data['service_display'];
    $service_rtmp0_multiple = $data['service_rtmp0_multiple'];
    $service_rtmp0_hls = $data['service_rtmp0_hls'];
    $service_rtmp0_dash = $data['service_rtmp0_dash'];
    $service_rtmp1_multiple = $data['service_rtmp1_multiple'];
    $service_rtmp1_hls = $data['service_rtmp1_hls'];
    $service_rtmp1_dash = $data['service_rtmp1_dash'];
    $service_udp0 = $data['service_udp0'];
    $service_udp1 = $data['service_udp1'];
    $service_udp2 = $data['service_udp2'];
    $service_custom = $data['service_custom'];
    $service_srt_multiple = $data['service_srt_multiple'];
    $rtmp0_multiple = $data['rtmp0_multiple'];
    $rtmp1_multiple = $data['rtmp1_multiple'];
    $srt_multiple = $data['srt_multiple'];
    $input_transcode_every_time = 'https://cdn.urmic.org/unavailable.mp4';
    $use_common_backend_rtmp0 = $data['rtmp0']['common_backend'];
    $use_common_backend_rtmp1 = $data['rtmp1']['common_backend'];
    $use_common_backend_udp0 = $data['udp0']['common_backend'];
    $use_common_backend_udp1 = $data['udp1']['common_backend'];
    $use_common_backend_udp2 = $data['udp2']['common_backend'];
    $use_common_backend_srt = $data['srt']['common_backend'];

    switch ($which_service) {
        case 'input':
            $input .= "  ";
            $file = "/var/www/encoder-main.sh";
            if (file_put_contents($file, $input) !== false) {
                echo "File saved.";
            } else {
                echo "Error writing file.";
            }
            exec("sudo systemctl restart encoder-main");
            exec("sudo reboot");
            break;
        case 'display';
            break;
        case 'rtmp0';
        case 'rtmp1';
            if ($service_rtmp0_hls === "enable") {
                $hls0 = "
      hls on;
      hls_path /var/www/html/hls/shree;
      hls_fragment 3;
      hls_playlist_length 60;
";
            } else {
                $hls0 = "
";
            }
            if ($service_rtmp0_dash === "enable") {
                $dash0 = "
      dash on;
      dash_path /var/www/html/dash/shree; 
";
            } else {
                $dash0 = "
";
            }
            if ($service_rtmp1_hls === "enable") {
                $hls1 = "
      hls on;
      hls_path /var/www/html/hls/shreeshree;
      hls_fragment 3;
      hls_playlist_length 60;
";
            } else {
                $hls1 = "
";
            }
            if ($service_rtmp1_dash === "enable") {
                $dash1 = "
      dash on;
      dash_path /var/www/html/dash/shreeshree; 
";
            } else {
                $dash1 = "
";
            }

            $rtmp_push0 = "";
            for ($i = 1; $i <= 11; $i++) {
                if ($rtmp0_multiple[$i]['enabled'] == 'true') {
                    $rtmp_push0 .= "
      push " . $rtmp0_multiple[$i]['url'] . ";";
                }
            }
            $rtmp_push1 = "";
            for ($i = 1; $i <= 11; $i++) {
                if ($rtmp1_multiple[$i]['enabled'] == 'true') {
                    $rtmp_push1 .= "
      push " . $rtmp1_multiple[$i]['url'] . ";";
                }
            }

            $nginx = "
user www-data;
worker_processes auto;
pid /run/nginx.pid;
include /etc/nginx/modules-enabled/*.conf;

events {
    worker_connections 2048;
    multi_accept on;
}

rtmp {
  server {
    listen 1935;
    chunk_size 4096;

    ";
            if ($input_source === "rtmp") {
                $nginx .= "    

    application " . $input_rtmp_mount . " {
      live on;
      record off;
      meta off;
      wait_video on;
    }

";
            }
            $nginx .= "    

    application shree {
      live on;
      record off;
      meta off;
      wait_video on;
      " . $hls0 . "
      " . $dash0 . "
      " . $rtmp_push0 . "
    }
    application shreeshree {
      live on;
      record off;
      meta off;
      wait_video on;
      " . $hls1 . "
      " . $dash1 . "
      " . $rtmp_push1 . "
    }
  }
}";

            $nginx .= "

http {
  sendfile on;
  tcp_nopush on;
  types_hash_max_size 2048;

  include /etc/nginx/mime.types;
  default_type application/octet-stream;
        
  ssl_protocols TLSv1 TLSv1.1 TLSv1.2 TLSv1.3; # Dropping SSLv3, ref: POODLE
  ssl_prefer_server_ciphers on;

  access_log /var/log/nginx/access.log;
  error_log /var/log/nginx/error.log warn;

  gzip on;
  include /etc/nginx/conf.d/*.conf;
  include /etc/nginx/sites-enabled/*;

}            
            ";

            $file = "/var/www/nginx.conf";
            file_put_contents($file, $nginx);
            exec('sudo cp /var/www/nginx.conf /etc/nginx/nginx.conf');
            exec("sudo nginx -t 2>&1", $output, $status);
            if ($status === 0) {
                exec("sudo systemctl restart nginx 2>&1", $o, $s);
            } else {
                exec('sudo cp /var/www/default_nginx.conf.conf /etc/nginx/nginx.conf');
                exec("sudo systemctl restart nginx");
            }

            if ($service_rtmp0_multiple === "enable") {

                switch ($use_common_backend_rtmp0) {
                    case "enable":
                        $rtmp = 'ffmpeg -hwaccel auto -hide_banner -fflags nobuffer -analyzeduration 3000000 -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                            . ' -c:v copy '
                            . ' -c:a copy '
                            . ' -f flv rtmp://127.0.0.1/shree/bhattji';
                        break;

                    case "disable":
                        $rtmp = 'ffmpeg -hwaccel auto -hide_banner -fflags nobuffer -analyzeduration 3000000 -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                            . ' -c:v h264_qsv '
                            . ' -vf "scale=' . str_replace("x", ":", $data['rtmp0']['resolution']) . '"'
                            . '" -b:v ' . $data['rtmp0']['data_rate']
                            . ' -maxrate ' . $data['rtmp0']['data_rate']
                            . ' -bufsize ' . $data['rtmp0']['data_rate']
                            . ' -r ' . $data['rtmp0']['framerate']
                            . ' -g ' . $data['rtmp0']['gop']
                            . ' -c:a aac -b:a ' . $data['rtmp0']['audio_data_rate']
                            . ' -af "volume=' . $data['rtmp0']['audio_db_gain'] . '"'
                            . ' -ar ' . $data['rtmp0']['audio_sample_rate']
                            . ' ' . $data['rtmp0']['extra']
                            . ' -f flv rtmp://127.0.0.1/shree/bhattji';
                        break;
                    default:
                        error_log("service_rtmp0_multiple");
                        break;
                }

                $file = "/var/www/encoder-rtmp0.sh";
                file_put_contents($file, $rtmp);
                exec('sudo systemctl enable encoder-rtmp0');
                exec('sudo systemctl restart encoder-rtmp0');
            } else {
                exec('sudo systemctl stop encoder-rtmp0');
                exec('sudo systemctl disable encoder-rtmp0');
            }

            if ($service_rtmp1_multiple === "enable") {

                switch ($use_common_backend_rtmp1) {
                    case "enable":
                        $rtmp = 'ffmpeg -hwaccel auto -hide_banner -fflags nobuffer -analyzeduration 3000000 -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                            . ' -c:v copy '
                            . ' -c:a copy '
                            . ' -f flv rtmp://127.0.0.1/shreeshree/bhattji';
                        break;
                    case "disable":
                        $rtmp = 'ffmpeg -hwaccel auto -hide_banner -fflags nobuffer -analyzeduration 3000000 -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                            . ' -c:v h264_qsv '
                            . ' -vf "scale=' . str_replace("x", ":", $data['rtmp1']['resolution']) . '"'
                            . '" -b:v ' . $data['rtmp1']['data_rate']
                            . ' -maxrate ' . $data['rtmp1']['data_rate']
                            . ' -bufsize ' . $data['rtmp1']['data_rate']
                            . ' -r ' . $data['rtmp1']['framerate']
                            . ' -g ' . $data['rtmp1']['gop']
                            . ' -c:a aac -b:a ' . $data['rtmp1']['audio_data_rate']
                            . ' -af "volume=' . $data['rtmp1']['audio_db_gain'] . '"'
                            . ' -ar ' . $data['rtmp1']['audio_sample_rate']
                            . ' ' . $data['rtmp1']['extra']
                            . ' -f flv rtmp://127.0.0.1/shreeshree/bhattji';
                        break;
                    default:
                        error_log("service_rtmp1_multiple");
                        break;
                }

                $file = "/var/www/encoder-rtmp1.sh";
                file_put_contents($file, $rtmp);
                exec('sudo systemctl enable encoder-rtmp1');
                exec('sudo systemctl restart encoder-rtmp1');
            } else {
                exec('sudo systemctl stop encoder-rtmp1');
                exec('sudo systemctl disable encoder-rtmp1');
            }
            break;
        case "srt";
            if ($service_srt_multiple) {

                $srt_push = "";

                for ($i = 1; $i <= 11; $i++) {
                    if ($srt_multiple[$i]['enabled'] == 1) {
                        $srt_push .= " " . $srt_multiple[$i]['url'];
                    }
                }

                $sls = "
srt {                
    
    worker_threads  64;
    worker_connections 500;

    log_file /tmp/logs/error.log ; 
    log_level info;
             
    server {
        listen 1937; 
        latency 1000; #ms

        domain_player shree;
        domain_publisher " . $srt_pass1 . " ;
        backlog 100;
        idle_streams_timeout 10;
        app {
            app_player bhatt ;           
            app_publisher " . $srt_pass2 . " ; 
            
            record_hls off;
            record_hls_segment_duration 10;
            ";
                if ($srt_push != "")
                    $sls .= "
            relay {
                type push;
                mode all; #all; hash
                reconnect_interval 10;
                idle_streams_timeout -1;
                upstreams " . $srt_push . " ;
            }";
                $sls .= "
        }
    }
}
";
                switch ($use_common_backend_srt) {
                    case "enable":
                        $service = 'ffmpeg -hide_banner   -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" ' .
                            ' -c:v copy' .
                            ' -c:a copy -g 30 -pkt_size 1316 -flush_packets 0 ' .
                            ' -f mpegts srt://127.0.0.1:1937?streamid=' . $srt_pass1 . '/' . $srt_pass2 . '/ji';
                        break;
                    case "disable":
                        $service = 'ffmpeg -hide_banner   -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                            . ' -c:v ' . $data['srt']['formate']
                            . ' -vf "scale=' . str_replace("x", ":", $data['srt']['resolution']) . '"'
                            . '" -b:v ' . $data['srt']['data_rate']
                            . ' -maxrate ' . $data['srt']['data_rate']
                            . ' -bufsize ' . $data['udp0']['data_rate']
                            . ' -r ' . $data['srt']['srt']
                            . ' -g ' . $data['srt']['gop']
                            . ' -c:a ' . $data['srt']['audio_formate']
                            . ' -b:a ' . $data['srt']['audio_data_rate']
                            . ' -af "volume=' . $data['srt']['audio_db_gain'] . '"'
                            . ' -ar ' . $data['srt']['audio_sample_rate']
                            . ' ' . $data['srt']['extra']
                            . ' -pkt_size 1316 -flush_packets 0 -f mpegts srt://127.0.0.1:1937?streamid=' . $srt_pass1 . '/' . $srt_pass2 . '/ji';
                        break;
                }
                $file = "/var/www/encoder-srt.sh";
                file_put_contents($file, $service);

                $file = "/var/www/sls.conf";
                file_put_contents($file, $sls);
                exec('sudo systemctl enable srt');
                exec('sudo systemctl restart srt');
                exec('sudo systemctl enable encoder-srt');
                exec('sudo systemctl restart encoder-srt');
            } else {
                exec('sudo systemctl disable srt');
                exec('sudo systemctl stop srt');
                exec('sudo systemctl disable encoder-srt');
                exec('sudo systemctl stop encoder-srt');
            }

            break;
        case "udp0";
            if ($service_udp0 === "enable") {
                switch ($use_common_backend_udp0) {
                    case "enable":
                        $udp0 = 'ffmpeg -hwaccel auto -hide_banner   -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                            . ' -c:v copy '
                            . ' -c:a copy '
                            . ' -f mpegts ' . $data['udp0']['udp'];
                        break;
                    case "disable":
                        $udp0 = 'ffmpeg -hwaccel auto -hide_banner   -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                            . ' -c:v ' . $data['udp0']['formate']
                            . ' -vf "scale=' . str_replace("x", ":", $data['udp0']['resolution']) . '"'
                            . '" -b:v ' . $data['udp0']['data_rate']
                            . ' -maxrate ' . $data['udp0']['data_rate']
                            . ' -bufsize ' . $data['udp0']['data_rate']
                            . ' -r ' . $data['udp0']['framerate']
                            . ' -g ' . $data['udp0']['gop']
                            . ' -c:a ' . $data['udp0']['audio_formate']
                            . ' -b:a ' . $data['udp0']['audio_data_rate']
                            . ' -af "volume=' . $data['udp0']['audio_db_gain'] . '"'
                            . ' -ar ' . $data['udp0']['audio_sample_rate']
                            . ' ' . $data['udp0']['extra']
                            . ' -f mpegts ' . $data['udp0']['udp'];
                        break;
                }
                $file = "/var/www/encoder-udp0.sh";
                file_put_contents($file, $udp0);
                exec('sudo systemctl enable encoder-udp0');
                exec('sudo systemctl restart encoder-udp0');
            } else {
                exec('sudo systemctl stop encoder-udp0');
                exec('sudo systemctl disable encoder-udp0');
            }
            break;
        case "udp1";
            if ($service_udp1 === "enable") {
                switch ($use_common_backend_udp1) {
                    case "enable":
                        $udp1 = 'ffmpeg -hwaccel auto -hide_banner   -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                            . ' -c:v copy '
                            . ' -c:a copy '
                            . ' -f mpegts ' . $data['udp1']['udp'];
                        break;
                    case "disable":
                        $udp1 = 'ffmpeg -hwaccel auto -hide_banner   -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                            . ' -c:v ' . $data['udp1']['formate']
                            . ' -vf "scale=' . str_replace("x", ":", $data['udp1']['resolution']) . '"'
                            . '" -b:v ' . $data['udp1']['data_rate']
                            . ' -maxrate ' . $data['udp1']['data_rate']
                            . ' -bufsize ' . $data['udp1']['data_rate']
                            . ' -r ' . $data['udp1']['framerate']
                            . ' -g ' . $data['udp1']['gop']
                            . ' -c:a ' . $data['udp1']['audio_formate']
                            . ' -b:a ' . $data['udp1']['audio_data_rate']
                            . ' -af "volume=' . $data['udp1']['audio_db_gain'] . '"'
                            . ' -ar ' . $data['udp1']['audio_sample_rate']
                            . ' ' . $data['udp1']['extra']
                            . ' -f mpegts ' . $data['udp1']['udp'];
                        break;
                }
                $file = "/var/www/encoder-udp1.sh";
                file_put_contents($file, $udp1);
                exec('sudo systemctl enable encoder-udp1');
                exec('sudo systemctl restart encoder-udp1');
            } else {
                exec('sudo systemctl stop encoder-udp1');
                exec('sudo systemctl disable encoder-udp1');
            }
            break;
        case "udp2";
            if ($service_udp2 === "enable") {
                switch ($use_common_backend_udp2) {
                    case "enable":
                        $udp2 = 'ffmpeg -hwaccel auto -hide_banner   -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                            . ' -c:v copy '
                            . ' -c:a copy '
                            . ' -f mpegts ' . $data['udp2']['udp'];
                        break;
                    case "disable":
                        $udp2 = 'ffmpeg -hwaccel auto -hide_banner   -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                            . ' -c:v ' . $data['udp2']['formate']
                            . ' -vf "scale=' . str_replace("x", ":", $data['udp2']['resolution']) . '"'
                            . '" -b:v ' . $data['udp2']['data_rate']
                            . ' -maxrate ' . $data['udp2']['data_rate']
                            . ' -bufsize ' . $data['udp2']['data_rate']
                            . ' -r ' . $data['udp2']['framerate']
                            . ' -g ' . $data['udp2']['gop']
                            . ' -c:a ' . $data['udp2']['audio_formate']
                            . ' -b:a ' . $data['udp2']['audio_data_rate']
                            . ' -af "volume=' . $data['udp2']['audio_db_gain'] . '"'
                            . ' -ar ' . $data['udp2']['audio_sample_rate']
                            . ' ' . $data['udp2']['extra']
                            . ' -f mpegts ' . $data['udp2']['udp'];
                        break;
                }
                $file = "/var/www/encoder-udp2.sh";
                file_put_contents($file, $udp2);
                exec('sudo systemctl enable encoder-udp2');
                exec('sudo systemctl restart encoder-udp2');
            } else {
                exec('sudo systemctl stop encoder-udp2');
                exec('sudo systemctl disable encoder-udp2');
            }
            break;
        case "custom";
            if ($service_custom === "enable") {
                $custom = 'ffmpeg -hwaccel auto -hide_banner   -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                    . $data['custom_output'];
                $file = "/var/www/encoder-custom.sh";
                file_put_contents($file, $custom);
                exec('sudo systemctl enable encoder-custom');
                exec('sudo systemctl restart encoder-custom');
            } else {
                exec('sudo systemctl stop encoder-custom');
                exec('sudo systemctl disable encoder-custom');
            }
            break;
        default:
            error_log("Error no input found");
            break;
    }
}


function update_firewall() {}

function update_network() {}

function update_firmware() {}
