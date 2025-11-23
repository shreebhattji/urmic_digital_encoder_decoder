<?php

$nginx_bottom = "
";

$sls = "
srt {                
    
    worker_threads  64;
    worker_connections 500;

    log_file /tmp/logs/error.log ; 
    log_level info;
             
    server {
        listen 1937; 
        latency 1000; #ms

        domain_player srt.urmic.org;
        domain_publisher pass1pass1pass1;
        backlog 100;
        idle_streams_timeout 10;#s -1: unlimited
        app {
            app_player onshreeganeshaynamah ;           
            app_publisher pass2pass2pass2 ; 
            
            record_hls off;#on, off 
            record_hls_segment_duration 10; #unit s
            
            #relay {
            #    type push;
            #    mode all; #all; hash
            #    reconnect_interval 10;
            #    idle_streams_timeout 10;#s -1: unlimited
            #    upstreams 192.168.31.106:8080?streamid=uplive.sls.com/live ;
            #}          
        }
    }
}
";

function update_service($which_service)
{

    $input = "";
    $input_source = "";
    $input_rtmp_port = "";
    $input_port_srt = "";
    $input_rtmp_mount = "";
    $input_rtmp_pass = "";
    $output = "";
    $rtmp_multiple[] = [];
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
            'port' => '1935'
        ],
        'srt' => [
            'stream_id_1' => 'har',
            'stream_id_2' => 'har',
            'stream_id_3' => 'mahadev',
            'port' => '1937'
        ],
        'udp' => 'udp://@224.1.1.1:8000',
        'custom' => ''
    ];

    $jsonFile = __DIR__ . '/input.json';
    if (file_exists($jsonFile)) {
        $raw = file_get_contents($jsonFile);
        $data = json_decode($raw, true);
        if (!is_array($data)) $data = $defaults;
    }

    $input_source = $data['input'];
    $input_rtmp_port = $data['rtmp']['port'];
    $input_rtmp_mount = $data['rtmp']['mount'];
    $input_rtmp_pass = $data['rtmp']['password'];
    $input_port_srt = $data['srt']['port'];

    if ($input_rtmp_port === "80" || $input_rtmp_port === "443" || $input_port_srt === "80" || $input_port_srt === "443") {
        echo '<script>alert("80 or 443 port is not allowed .");</script>';
        die();
    }

    switch ($input_source) {
        case "hdmi":
            $input = "ffmpeg -thread_queue_size 512 -f v4l2 -input_format mjpeg -framerate " . $data['hdmi']['framerate'] . " -video_size " . $data['hdmi']['resolution'] . " -i /dev/video0 " .
                "-f alsa -i " . $data['hdmi']['audio_source'] . ' -init_hw_device qsv=hw:/dev/dri/renderD128 -filter_hw_device hw   -fflags +genpts -use_wallclock_as_timestamps 1   -vf "format=nv12,hwupload=extra_hw_frames=64,format=qsv" ';
            break;
        case "url":
            $input .= "ffmpeg -hwaccel auto -stream_loop -1 -re -i " . $data['url'];
            break;
        case "rtmp":
            $input .= "ffmpeg -hwaccel auto -stream_loop -1 -re -i rtmp://127.0.0.1:" . $$input_rtmp_port . "/" . $$input_rtmp_mount . "/" . $input_rtmp_pass;
            break;
        case "srt":
            $input .= "-stream_loop -1 -re -i srt://127.0.0.1:" . $data['srt']['port'] . "/" . $data['srt']['stream_id_1'] . "/" . $data['srt']['stream_id_2'] . "/" . $data['srt']['stream_id_3'];
            $input_port_srt = $data['srt']['port'];
            break;
    }
    $input .= "  ";

    $jsonFile = __DIR__ . '/output.json';

    $defaults = [
        'video' => [
            'resolution' => '1920x1080',
            'format' => 'h264_qsv',
            'framerate' => '25',
            'data_rate' => '3.3M',
            'gop' => '12'
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

    $service_display = $data['service_display'];
    $service_rtmp_multiple = $data['service_rtmp_multiple'];
    $service_rtmp_hls = $data['service_rtmp_hls'];
    $service_rtmp_dash = $data['service_rtmp_dash'];
    $service_srt_multiple = $data['service_srt_multiple'];
    $service_udp = $data['service_udp'];
    $service_custom = $data['service_custom'];
    $rtmp_multiple = $data['rtmp_multiple'];
    $srt_multiple = $data['srt_multiple'];

    $input .=  ' -c:v h264_qsv -b:v ' . $data['video']['data_rate'] . ' -maxrate ' . $data['video']['data_rate'] . ' -bufsize 10M -g ' . $data['video']['gop'] . ' -af "aresample=async=1:first_pts=0" ' .
        ' -c:a ' . $data['audio']['format'] . ' -ar ' . $data['audio']['sample_rate'] . ' -b:a ' . $data['audio']['bit_rate'] . ' -vsync 1 -copytb 1 -f mpegts udp://239.255.255.254:39000?localaddr=127.0.0.1';

    $service = $input;
    $file = "/var/www/encoder-main.sh";
    if (file_put_contents($file, $service) !== false) {
        echo "File saved.";
    } else {
        echo "Error writing file.";
    }

    switch ($which_service) {
        case 'input':
            exec('sudo systemctl restart encoder-main');
            break;
        case 'display';
            break;
        case 'rtmp';
            //if hls service enable add this to nginx
            if ($service_rtmp_hls === "enable") {
                $hls = "
      hls on;
      hls_path /var/www/html/hls/shree;
      hls_fragment 3;
      hls_playlist_length 60;
";
            } else {
                $hls = "
";
            }

            //if dash service enable add this to nginx
            if ($service_rtmp_dash === "enable") {
                $dash = "
      dash on;
      dash_path /var/www/html/dash/shree; 
";
            } else {
                $dash = "
";
            }

            if (empty($input_rtmp_port))
                $input_rtmp_port = "1935";

            $rtmp_push = "";

            for ($i = 1; $i <= 11; $i++) {
                if ($rtmp_multiple[$i]['enabled'] == 1) {
                    $rtmp_push .= "
      push " . $rtmp_multiple[$i]['url'] . ";";
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
";
            if ($input_source === "rtmp") {
                $nginx .= "    
rtmp {
  server {
    listen " . $input_rtmp_port . ";
    chunk_size 4096;

    application " . $input_rtmp_mount . " {
      live on;
      record off;
      meta off;
      wait_video on;
    }

    application shree {
      live on;
      record off;
      meta off;
      wait_video on;
      allow publish 127.0.0.1;
      deny publish all;
      " . $rtmp_push . "
    }
  }
}
    ";
            } else {
                $nginx .= "    
rtmp {
  server {
    listen 1935;
    chunk_size 4096;

    application shree {
      live on;
      record off;
      meta off;
      wait_video on;
      " . $rtmp_push . "
      " . $hls . "
      " . $dash . "
    }
  }
}";
            }
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

            if ($service_rtmp_multiple === "enable") {
                $rtmp = 'ffmpeg -fflags nobuffer -i "udp://239.255.255.254:39000?localaddr=127.0.0.1&fifo_size=5000000&overrun_nonfatal=1" -c:v copy -c:a aac -f flv rtmp://127.0.0.1:1935/shree/bhattji';
                $file = "/var/www/encoder-rtmp.sh";
                file_put_contents($file, $rtmp);
                exec('sudo cp /var/www/nginx.conf /etc/nginx/nginx.conf');
                exec("nginx -t 2>&1", $output, $status);
                if ($status == 0) {
                    error_log("nginx tested fine ");
                    exec("sudo systemctl restart nginx 2>&1", $o, $s);
                    exec('sudo systemctl enable encoder-rtmp');
                    exec('sudo systemctl restart encoder-rtmp');
                } else {
                    error_log("Error Nginx default");
#                    exec('sudo cp /var/www/nginx.conf /etc/nginx/');
#                    exec("sudo systemctl restart nginx");
#                    exec('sudo systemctl stop encoder-rtmp');
#                    exec('sudo systemctl disable encoder-rtmp');
                }
            } {
            }

            break;
        case "srt";
            break;
        case "udp";
            break;
        case "custom";
            break;
        default:
            error_log("Error no input found");
            break;
    }
}


function update_firewall() {}

function update_network() {}

function update_firmware() {}
