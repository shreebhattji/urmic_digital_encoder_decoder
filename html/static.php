<?php

$nginx_top = "
user www-data;
worker_processes auto;
pid /run/nginx.pid;
include /etc/nginx/modules-enabled/*.conf;

events {
    worker_connections 2048;
    multi_accept on;
}

";

$nginx_bottom = "

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

function update_service()
{

    shell_exec("sudo /bin/systemctl stop main-encoder");

    $candidate = '/var/www/html/nginx.conf';
    $fallback  = '/var/www/html/default_nginx.conf';
    $target    = '/etc/nginx/nginx.conf';

    $cp_cmd = function (string $src, string $dst): string {
        return 'sudo /bin/cp ' . escapeshellarg($src) . ' ' . escapeshellarg($dst);
    };

    $test_cmd    = 'sudo /usr/sbin/nginx -t -q';
    $restart_cmd = 'sudo /bin/systemctl reload nginx';


    $input = "";
    $input_link = "";
    $input_source = "";
    $input_rtmp_port = "";
    $input_port_srt = "";
    $input_rtmp_mount = "";
    $input_rtmp_pass = "";
    $output = "";
    global $nginx_top;
    global $nginx_bottom;
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
                "-f alsa -i " . $data['hdmi']['audio_source'] . ' -init_hw_device qsv=hw:/dev/dri/renderD128 -filter_hw_device hw   -fflags +genpts -use_wallclock_as_timestamps 1   -vf "format=nv12,hwupload=extra_hw_frames=64,format=qsv"   -c:v h264_qsv';
            break;
        case "url":
            $input .= "ffmpeg -stream_loop -1 -re -i " . $data['url'];
            break;
        case "rtmp":
            $input .= "-stream_loop -1 -re -i rtmp://127.0.0.1:" . $data['rtmp']['port'] . "/" . $data['rtmp']['mount'] . "/" . $data['rtmp']['password'];
            break;
        case "srt":
            $input .= "-stream_loop -1 -re -i srt://127.0.0.1:" . $data['srt']['port'] . "/" . $data['srt']['stream_id_1'] . "/" . $data['srt']['stream_id_2'] . "/" . $data['srt']['stream_id_3'];
            $input_port_srt = $data['srt']['port'];
            break;
    }
    $input .= "  ";

    $jsonFile = __DIR__ . '/output.json';

    // default structure
    $defaults = [
        'output' => 'display',
        'video' => [
            'resolution' => '1920x1080',
            'format' => 'h264_h264',
            'data_rate' => '4M',
            'gop' => '12'
        ],
        'audio' => [
            'format' => 'aac',
            'sample_rate' => '48000',
            'bit_rate' => '96k'
        ],
        'output_display' => '1920x1080@60.00',
        'output_display_audio' => '0,3',
        'rtmp_single' => '',
        'srt_single' => '',
        'rtmp_multiple' => [],
        'srt_multiple'  => [],
        'udp_primary' => '',
        'udp_vlan' => '',
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

    $rtmp_multiple = $data['rtmp_multiple'];
    $srt_multiple = $data['srt_multiple'];

    $input .= ' -b:v ' . $data['video']['data_rate'] . ' -maxrate ' . $data['video']['data_rate'] . ' -bufsize 10M -g ' . $data['video']['gop'] .' -af "aresample=async=1:first_pts=0" '.
        ' -c:a ' . $data['audio']['format'] . ' -ar ' . $data['audio']['sample_rate'] . ' -b:a ' . $data['audio']['bit_rate'] . ' -vsync 1 -copytb 1 -f mpegts udp://239.255.255.254:39000?localaddr=127.0.0.1';

    $service = $input;
    $file = "/var/www/main-encoder.sh";
    if (file_put_contents($file, $service) !== false) {
        echo "File saved.";
    } else {
        echo "Error writing file.";
    }


    switch ($data['output']) {
        case "display":
            $output_display_audio = $data['output_display_audio'];
            $input = "mpv --fs --loop --hwdec=auto --audio-device=alsa/plughw:" . $output_display_audio . " udp://239.255.255.254:39000";
            break;
        case "rtmp_single":
            $input .= '-vf "scale=' . $data['video']['resolution'] . '" -c:v ' . $data['video']['format'] . ' -b:v ' . $data['video']['data_rate']
                . ' -minrate ' . $data['video']['data_rate'] . ' -maxrate ' . $data['video']['data_rate'] . ' -bufsize ' . $data['video']['data_rate'] . ' -g ' . $data['video']['gop'] .
                ' -c:a ' . $data['audio']['format'] . ' -ar ' . $data['audio']['sample_rate'] . ' -b:a ' . $data['audio']['bit_rate'] . ' -f flv ' . $data['rtmp_single'];
            break;
        case "srt_single":
            $input .= '-vf "scale=' . $data['video']['resolution'] . '" -c:v ' . $data['video']['format'] . ' -b:v ' . $data['video']['data_rate'] . ' -minrate ' . $data['video']['data_rate'] . ' -maxrate ' . $data['video']['data_rate'] . ' -bufsize ' . $data['video']['data_rate'] . ' -g ' . $data['video']['gop'] .
                ' -c:a ' . $data['audio']['format'] . ' -ar ' . $data['audio']['sample_rate'] . ' -b:a ' . $data['audio']['bit_rate'] . ' -f mpegts ' . $data['srt_single'];
            break;
        case "rtmp_multiple":
            $input .= '-vf "scale=' . $data['video']['resolution'] . '" -c:v ' . $data['video']['format'] . ' -b:v ' . $data['video']['data_rate']
                . ' -minrate ' . $data['video']['data_rate'] . ' -maxrate ' . $data['video']['data_rate'] . ' -bufsize ' . $data['video']['data_rate'] . ' -g ' . $data['video']['gop'] .
                ' -c:a ' . $data['audio']['format'] . ' -ar ' . $data['audio']['sample_rate'] . ' -b:a ' . $data['audio']['bit_rate'] . ' -f flv rtmp://127.0.0.1:'
                . $input_rtmp_port . '/shree/bhattji';
            break;
        case "srt_multiple":
            if (empty($input_port))
                $input_port = "1937";
            $input .= '-vf "scale=' . $data['video']['resolution'] . '" -c:v ' . $data['video']['format'] . ' -b:v ' . $data['video']['data_rate']
                . ' -minrate ' . $data['video']['data_rate'] . ' -maxrate ' . $data['video']['data_rate'] . ' -bufsize ' . $data['video']['data_rate'] . ' -g ' . $data['video']['gop'] .
                ' -c:a ' . $data['audio']['format'] . ' -ar ' . $data['audio']['sample_rate'] . ' -b:a ' . $data['audio']['bit_rate'] . ' -f mpegts srt://127.0.0.1:'
                . $input_port . '/shree/bhatt/ji';
            break;
        case "udp_primary":
            break;
        case "udp_vlan":
            break;
        case "custom_output":
            break;
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

    $nginx = $nginx_top;
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
      " .
            $rtmp_push
            . "
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
      " .
            $rtmp_push
            . "
    }
  }
}
    ";
    }
    $nginx .= $nginx_bottom;
    $file = "/var/www/html/nginx.conf";
    if (file_put_contents($file, $nginx) !== false) {
        echo "File saved.";
    } else {
        echo "Error writing file.";
    }

    exec($cp_cmd($candidate, $target), $out, $rc);
    // if nginx config test OK, restart and exit
    exec($test_cmd, $out, $rc);
    if ($rc === 0) {
        exec($restart_cmd, $out, $rc2);
        return;
    }

    // fallback copy
    exec($cp_cmd($fallback, $target), $out, $rc);
    exec($test_cmd, $out, $rc);
    if ($rc === 0) {
        exec($restart_cmd, $out, $rc2);
    }

    sleep(10);
    shell_exec("sudo /bin/systemctl restart main-encoder");
}


function update_firewall() {}

function update_network() {}

function update_firmware() {}
