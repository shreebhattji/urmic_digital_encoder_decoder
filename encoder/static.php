<?php

function generateRandomString($length = 16)
{
    $bytes = random_bytes(ceil($length / 2));
    $randomString = bin2hex($bytes);
    return substr($randomString, 0, $length);
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
        'custom' => '',
    ];

    $jsonFile = __DIR__ . '/input.json';
    if (file_exists($jsonFile)) {
        $raw = file_get_contents($jsonFile);
        $data = json_decode($raw, true);
        if (!is_array($data)) $data = $defaults;
    }

    $input_source = $data['input'];
    $input_rtmp_mount = $data['rtmp']['mount'];
    $input_rtmp_pass = $data['rtmp']['password'];
    $srt_pass1 = $data['srt']['stream_id_1'];
    $srt_pass2 = $data['srt']['stream_id_2'];
    $srt_pass3 = $data['srt']['stream_id_3'];

    switch ($input_source) {
        case "hdmi":
            $input = "ffmpeg -hwaccel auto -hide_banner -f v4l2 -thread_queue_size 512 -input_format mjpeg -framerate " . $data['hdmi']['framerate'] . " -video_size " . $data['hdmi']['resolution'] . " -i /dev/video0 " .
                "-f alsa -i " . $data['hdmi']['audio_source'];
            break;
        case "url":
            $input .= "ffmpeg -hide_banner -stream_loop -1 -re -i " . $data['url'];
            break;
        case "udp":
            $input .= 'ffmpeg -hide_banner -stream_loop -1 -re -i "' . $data['udp'];
            break;
        case "rtmp":
            $input .= "ffmpeg -hide_banner -stream_loop -1 -re -i rtmp://127.0.0.1:1935/" . $$input_rtmp_mount . "/" . $input_rtmp_pass;
            break;
        case "srt":
            $input .= "ffmpeg -hide_banner -stream_loop -1 -re -i srt://127.0.0.1:1937/shree/bhatt/" . $srt_pass3;
            break;
    }
    $input .= "  ";

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
            'udp' => 'udp://@224.1.1.1:8001',
            'formate' => 'h264_qsv',
            'resolution' => '1280x720',
            'data_rate' => '2.2M',
            'framerate' => '25',
            'gop' => '25',
            'extra' => '',
            'audio_formate' => 'aac',
            'audio_data_rate' => '128k',
            'audio_db_gain' => '0dB',
            'audio_sample_rate' => '48000'
        ],
        'udp1' => [
            'udp' => 'udp://@224.1.1.1:8001',
            'formate' => 'h264_qsv',
            'resolution' => '720x576',
            'data_rate' => '1.5M',
            'framerate' => '25',
            'gop' => '25',
            'extra' => '',
            'audio_formate' => 'mp2',
            'audio_data_rate' => '128k',
            'audio_db_gain' => '0dB',
            'audio_sample_rate' => '48000'
        ],
        'udp2' => [
            'udp' => 'udp://@224.1.1.1:8002',
            'formate' => 'mpeg2video',
            'resolution' => '720x576',
            'data_rate' => '3M',
            'framerate' => '25',
            'gop' => '25',
            'extra' => '',
            'audio_formate' => 'mp2',
            'audio_data_rate' => '96k',
            'audio_db_gain' => '0dB',
            'audio_sample_rate' => '48000'
        ],
        'srt' => [
            'formate' => 'mpeg2video',
            'resolution' => '1920x1080',
            'data_rate' => '6M',
            'framerate' => '50',
            'gop' => '50',
            'extra' => '',
            'audio_formate' => 'aac',
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


    switch ($which_service) {
        case 'input':
            $input .=  " -c:v copy -c:a aac -b:a 128k -f matroska udp://@239.255.254.254:39000?localaddr=127.0.0.1";
            $service = $input;
            $file = "/var/www/encoder-main.sh";
            if (file_put_contents($file, $service) !== false) {
                echo "File saved.";
            } else {
                echo "Error writing file.";
            }
            exec('sudo reboot');
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
                $rtmp = 'ffmpeg -hwaccel auto -hide_banner -fflags nobuffer -analyzeduration 3000000  -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                    . ' -c:v h264_qsv '
                    . ' -vf "scale=' . str_replace("x", ":", $data['rtmp0']['resolution'])
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

                $file = "/var/www/encoder-rtmp0.sh";
                file_put_contents($file, $rtmp);
                exec('sudo systemctl enable encoder-rtmp0');
                exec('sudo systemctl restart encoder-rtmp0');
            } else {
                exec('sudo systemctl stop encoder-rtmp0');
                exec('sudo systemctl disable encoder-rtmp0');
            }

            if ($service_rtmp1_multiple === "enable") {
                $rtmp = 'ffmpeg -hwaccel auto -hide_banner -fflags nobuffer -analyzeduration 3000000  -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                    . ' -c:v h264_qsv '
                    . ' -vf "scale=' . str_replace("x", ":", $data['rtmp1']['resolution'])
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
            
            relay {
                type push;
                mode all; #all; hash
                reconnect_interval 10;
                idle_streams_timeout -1;
                upstreams " . $srt_push . " ;
            }
        }
    }
}
";
                $service = 'ffmpeg -hwaccel auto -fflags nobuffer -analyzeduration 3000000  -i udp://@239.255.254.254:39000 -c copy -f mpegts srt://127.0.0.1/' . $srt_pass1 . '/' . $srt_pass2 . '/ji';
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
                $udp0 = 'ffmpeg -hwaccel auto -hide_banner -fflags nobuffer -analyzeduration 3000000  -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                    . ' -c:v ' . $data['udp0']['formate']
                    . ' -vf "scale=' . str_replace("x", ":", $data['udp0']['resolution'])
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
                $udp1 = 'ffmpeg -hwaccel auto -hide_banner -fflags nobuffer -analyzeduration 3000000  -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                    . ' -c:v ' . $data['udp1']['formate']
                    . ' -vf "scale=' . str_replace("x", ":", $data['udp1']['resolution'])
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
                $udp2 = 'ffmpeg -hwaccel auto -hide_banner -fflags nobuffer -analyzeduration 3000000  -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
                    . ' -c:v ' . $data['udp2']['formate']
                    . ' -vf "scale=' . str_replace("x", ":", $data['udp2']['resolution'])
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
                $custom = 'ffmpeg -hwaccel auto -hide_banner -fflags nobuffer -analyzeduration 3000000  -i "udp://@239.255.254.254:39000?fifo_size=5000000&overrun_nonfatal=1&localaddr=127.0.0.1" '
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
