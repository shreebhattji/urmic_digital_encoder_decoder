sudo mkdir /etc/srt;
sudo apt update
sudo apt install -y apache2 php libapache2-mod-php vainfo ufw intel-media-va-driver-non-free i965-va-driver libmfx1 certbot intel-gpu-tools python3-certbot-nginx ffmpeg nginx v4l-utils python3-pip mpv libnginx-mod-rtmp alsa-utils vlan git zlib1g-dev
sudo pip3 install psutil --break-system-packages

dpkg -i srt-1.5.5-Linux.deb

cat > /etc/sudoers.d/www-data << 'EOL'
www-data     ALL=(ALL) NOPASSWD: ALL
EOL

cat > /etc/apache2/sites-available/000-default.conf << 'EOL'
<VirtualHost *:8080>

    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/encoder

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

</VirtualHost>
EOL
cat > /etc/apache2/sites-available/default-ssl.conf << 'EOL'
<VirtualHost *:8443>
	
    ServerAdmin webmaster@localhost
	DocumentRoot /var/www/encoder

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined
	SSLEngine on

	SSLCertificateFile      /etc/ssl/certs/ssl-cert-snakeoil.pem
	SSLCertificateKeyFile   /etc/ssl/private/ssl-cert-snakeoil.key

	<FilesMatch "\.(?:cgi|shtml|phtml|php)$">
		SSLOptions +StdEnvVars
	</FilesMatch>
	<Directory /usr/lib/cgi-bin>
		SSLOptions +StdEnvVars
	</Directory>
</VirtualHost>
EOL

cat>/etc/apache2/ports.conf<< 'EOL'
Listen 8080

<IfModule ssl_module>
	Listen 8443
</IfModule>

<IfModule mod_gnutls.c>
	Listen 8443
</IfModule>
EOL

cat > /etc/systemd/system/encoder-main.service<< 'EOL'
[Unit]
Description=Main Encoder by ShreeBhattJi

[Service]
ExecStart=/bin/bash /var/www/encoder-main.sh
WorkingDirectory=/var/www/
Restart=always
RestartSec=10
User=root
Environment=PATH=/usr/bin:/usr/local/bin

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-display.service<< 'EOL'
[Unit]
Description= Display Encoder by ShreeBhattJi
Requires=encoder-main.service

[Service]
ExecStart=/bin/bash /var/www/encoder-display.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-rtmp0.service<< 'EOL'
[Unit]
Description= RTMP Encoder by ShreeBhattJi
Requires=encoder-main.service

[Service]
ExecStart=/bin/bash /var/www/encoder-rtmp0.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-rtmp1.service<< 'EOL'
[Unit]
Description= RTMP Encoder by ShreeBhattJi
Requires=encoder-main.service

[Service]
ExecStart=/bin/bash /var/www/encoder-rtmp1.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-srt.service<< 'EOL'
[Unit]
Description= SRT Encoder by ShreeBhattJi
Requires=encoder-main.service

[Service]
ExecStart=/bin/bash /var/www/encoder-srt.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-udp0.service<< 'EOL'
[Unit]
Description= UDP Encoder by ShreeBhattJi
Requires=encoder-main.service

[Service]
ExecStart=/bin/bash /var/www/encoder-udp0.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-udp1.service<< 'EOL'
[Unit]
Description= UDP Encoder by ShreeBhattJi
Requires=encoder-main.service

[Service]
ExecStart=/bin/bash /var/www/encoder-udp1.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-udp2.service<< 'EOL'
[Unit]
Description= UDP Encoder by ShreeBhattJi
Requires=encoder-main.service

[Service]
ExecStart=/bin/bash /var/www/encoder-udp2.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-custom.service<< 'EOL'
[Unit]
Description= UDP Encoder by ShreeBhattJi
Requires=encoder-main.service

[Service]
ExecStart=/bin/bash /var/www/encoder-custom.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/mediamtx.service<< 'EOL'
[Unit]
Description= UDP Encoder by ShreeBhattJi


[Service]
WorkingDirectory=/var/lib/mediamtx
ExecStart=/usr/local/bin/mediamtx -f /etc/mediamtx.yml
Restart=on-failure
RestartSec=5
WatchdogSec=30
LimitNOFILE=65536
User=root

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/ustreamer.service<< 'EOL'
[Unit]
Description= UDP Encoder by ShreeBhattJi


[Service]
ExecStart=/bin/bash /var/www/ustreamer.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin

[Install]
WantedBy=multi-user.target
EOL


sudo mv mediamtx /usr/local/bin/mediamtx
sudo chmod +x /usr/local/bin/mediamtx
sudo mkdir -p /var/lib/mediamtx

# /etc/mediamtx.yml
cat > /etc/mediamtx.yml<< 'EOL'
paths:
  mystream:
    publish: yes
EOL

# graph monitor setup
cat > /etc/systemd/system/system-monitor.service<< 'EOL'
[Unit]
Description=Lightweight System Monitor Sampler by ShreeBhattJi
After=network.target

[Service]
Type=simple
ExecStart=/usr/bin/python3 /usr/local/bin/nginx_system_monitor_sampler.py
Restart=always
RestartSec=2
User=root
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
EOL

cat > /usr/local/bin/nginx_system_monitor_sampler.py<< 'EOL'
#!/usr/bin/env python3
"""
Lightweight sampler for nginx static frontend.
"""

import time, json, os
from collections import deque
from datetime import datetime
import psutil

OUT_FILE = "/var/www/encoder/metrics.json"
TMP_FILE = OUT_FILE + ".tmp"
SAMPLE_INTERVAL = 10.0               # seconds between samples
HISTORY_SECONDS = 15 * 60           # 15 minutes
MAX_SAMPLES = int(HISTORY_SECONDS / SAMPLE_INTERVAL)

# circular buffers
timestamps = deque(maxlen=MAX_SAMPLES)
cpu_hist = deque(maxlen=MAX_SAMPLES)
ram_hist = deque(maxlen=MAX_SAMPLES)
net_in_hist = deque(maxlen=MAX_SAMPLES)
net_out_hist = deque(maxlen=MAX_SAMPLES)
disk_read_hist = deque(maxlen=MAX_SAMPLES)
disk_write_hist = deque(maxlen=MAX_SAMPLES)
disk_percent_hist = deque(maxlen=MAX_SAMPLES)

_prev_net = psutil.net_io_counters()
_prev_disk = psutil.disk_io_counters()
_prev_time = time.time()

def sample_once():
    global _prev_net, _prev_disk, _prev_time
    now = time.time()
    iso = datetime.fromtimestamp(now).isoformat(timespec='seconds')
    cpu = psutil.cpu_percent(interval=None)
    ram = psutil.virtual_memory().percent

    net = psutil.net_io_counters()
    disk = psutil.disk_io_counters()
    try:
        disk_percent = psutil.disk_usage("/").percent
    except Exception:
        disk_percent = 0.0

    elapsed = now - _prev_time if _prev_time else SAMPLE_INTERVAL
    if elapsed <= 0:
        elapsed = SAMPLE_INTERVAL

    in_rate = int(((net.bytes_recv - _prev_net.bytes_recv) / elapsed) * 8)
    out_rate = int(((net.bytes_sent - _prev_net.bytes_sent) / elapsed) * 8)

    read_rate = (disk.read_bytes - _prev_disk.read_bytes) / elapsed
    write_rate = (disk.write_bytes - _prev_disk.write_bytes) / elapsed

    timestamps.append(iso)
    cpu_hist.append(round(cpu, 2))
    ram_hist.append(round(ram, 2))
    net_in_hist.append(int(in_rate))
    net_out_hist.append(int(out_rate))
    disk_read_hist.append(int(read_rate))
    disk_write_hist.append(int(write_rate))
    disk_percent_hist.append(round(disk_percent, 2))

    _prev_net = net
    _prev_disk = disk
    _prev_time = now

def write_json_atomic():
    payload = {
        "timestamps": list(timestamps),
        "cpu_percent": list(cpu_hist),
        "ram_percent": list(ram_hist),
        "net_in_Bps": list(net_in_hist),
        "net_out_Bps": list(net_out_hist),
        "disk_read_Bps": list(disk_read_hist),
        "disk_write_Bps": list(disk_write_hist),
        "disk_percent": list(disk_percent_hist),
        "sample_interval": SAMPLE_INTERVAL,
        "generated_at": datetime.utcnow().isoformat(timespec='seconds') + "Z"
    }
    with open(TMP_FILE, "w") as f:
        json.dump(payload, f)
    os.replace(TMP_FILE, OUT_FILE)

def main():
    global _prev_net, _prev_disk, _prev_time
    _prev_net = psutil.net_io_counters()
    _prev_disk = psutil.disk_io_counters()
    _prev_time = time.time()
    time.sleep(0.2)  # warm-up

    while True:
        try:
            sample_once()
            write_json_atomic()
        except Exception as e:
            # systemd journal will capture prints
            print("Sampler error:", e)
        time.sleep(SAMPLE_INTERVAL)

if __name__ == "__main__":
    main()
EOL


sudo mkdir -p /etc/srt/;
cat > /etc/srt/srt.sh<< 'EOL'
/etc/srt/srt -c /var/www/sls.conf
EOL

sudo chmod +x /etc/srt/srt.sh
sudo cp sls /etc/srt/srt
cat > /etc/systemd/system/srt.service<< 'EOL'
[Unit]
Description=Srt by ShreeBhattJi
Documentation=https://dbhatt.org

[Service]
Type=simple
User=root
Group=root
TimeoutStartSec=0
Restart=always
RestartSec=30s
Requires=srt
ExecStart=/bin/bash /etc/srt/srt.sh
SyslogIdentifier=srt
#ExecStop=

[Install]
WantedBy=multi-user.target
EOL

EOL

cat > /etc/nginx/sites-available/default<< 'EOL'
server {
	listen 80 default_server;
	listen [::]:80 default_server;
	server_name _;

	root /var/www/html;
	index index.php index.html;

	location / {
        try_files $uri $uri/ =404;
	}
}
EOL

rm /var/www/html/index.nginx-debian.html;
sudo mkdir -p /var/lib/mediamtx;
sudo mkdir -p /var/www/html/hls/shree;
sudo mkdir -p /var/www/html/dash/shree;
sudo mkdir -p /var/www/html/hls/shreeshree;
sudo mkdir -p /var/www/html/dash/shreeshree;
sudo mkdir -p /var/www/encoder;
cp -r html/* /var/www/html/
sudo cp -r encoder/* /var/www/encoder/

sudo a2enmod ssl
sudo systemctl enable apache2
sudo systemctl restart apache2

sudo chmod +x /usr/local/bin/nginx_system_monitor_sampler.py

sudo systemctl daemon-reload
sudo systemctl enable --now system-monitor.service
sudo systemctl status system-monitor.service --no-pager
sudo systemctl enable --now nginx.service
sudo systemctl status nginx.service --no-pager
sudo systemctl enable --now mediamtx.service
sudo systemctl restart mediamtx.service --no-pager

sudo chmod 777 -R /var/www
sudo chown -R www-data:www-data /var/www
sudo ufw allow proto udp to 224.0.0.0/4
sudo ufw route allow proto udp to 224.0.0.0/4
sudo ufw deny out to 239.255.254.254 port 39000 proto udp
sudo systemctl daemon-reload