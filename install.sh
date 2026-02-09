sudo mkdir /etc/srt;
sudo apt update
sudo apt install -y apache2 php libapache2-mod-php vainfo ufw intel-media-va-driver-non-free libavcodec-extra mesa-utils i965-va-driver libmfx1 certbot intel-gpu-tools python3-certbot-nginx ffmpeg nginx v4l-utils python3-pip mpv libnginx-mod-rtmp alsa-utils vlan git zlib1g-dev php-zip php-curl
sudo pip3 install psutil --break-system-packages

dpkg -i srt-1.5.5-Linux.deb

cat > /etc/sudoers.d/www-data << 'EOL'
www-data     ALL=(ALL) NOPASSWD: ALL
EOL

cat > /etc/apache2/sites-available/000-default.conf << 'EOL'
<VirtualHost *:8080>
    ServerName localhost
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/encoder

    ErrorLog ${APACHE_LOG_DIR}/encoder-error.log
    CustomLog ${APACHE_LOG_DIR}/encoder-access.log combined

    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/ssl-cert-snakeoil.pem
    SSLCertificateKeyFile /etc/ssl/private/ssl-cert-snakeoil.key

    <Directory /var/www/encoder>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOL

cat>/etc/apache2/ports.conf<< 'EOL'
<IfModule mod_ssl.c>
    Listen 8080
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

[Service]
ExecStart=/bin/bash /var/www/encoder-display.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin
RestartSec=30

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-rtmp0.service<< 'EOL'
[Unit]
Description= RTMP Encoder by ShreeBhattJi

[Service]
ExecStart=/bin/bash /var/www/encoder-rtmp0.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin
RestartSec=30

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-rtmp1.service<< 'EOL'
[Unit]
Description= RTMP Encoder by ShreeBhattJi

[Service]
ExecStart=/bin/bash /var/www/encoder-rtmp1.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin
RestartSec=30

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-srt.service<< 'EOL'
[Unit]
Description= SRT Encoder by ShreeBhattJi

[Service]
ExecStart=/bin/bash /var/www/encoder-srt.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin
RestartSec=30

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-udp0.service<< 'EOL'
[Unit]
Description= UDP Encoder by ShreeBhattJi

[Service]
ExecStart=/bin/bash /var/www/encoder-udp0.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin
RestartSec=30

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-udp1.service<< 'EOL'
[Unit]
Description= UDP Encoder by ShreeBhattJi

[Service]
ExecStart=/bin/bash /var/www/encoder-udp1.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin
RestartSec=30

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-udp2.service<< 'EOL'
[Unit]
Description= UDP Encoder by ShreeBhattJi

[Service]
ExecStart=/bin/bash /var/www/encoder-udp2.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin
RestartSec=30

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/encoder-custom.service<< 'EOL'
[Unit]
Description= UDP Encoder by ShreeBhattJi

[Service]
ExecStart=/bin/bash /var/www/encoder-custom.sh
WorkingDirectory=/var/www/
Restart=always
User=root
Environment=PATH=/usr/bin:/usr/local/bin
RestartSec=30

[Install]
WantedBy=multi-user.target
EOL

cat > /etc/systemd/system/drm-key.service<< 'EOL'
[Unit]
Description=HLS Key Generator and Poster
After=network-online.target
Wants=network-online.target

[Service]
Type=key genrator
User=root
Group=root
ExecStart=/var/www/key.sh
WorkingDirectory=/var/www
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
EOL

cat > /var/www/key.sh<< 'EOL'
#!/bin/bash
set -e

# ===== CONFIG =====
STREAM_ID="strem_id_strem_id_strem_id"
KEY_FILE="/var/www/scrambler.key"
# ==================

# Timestamp
TIMESTAMP=$(date +%s)

# Dynamic POST field name
KEY_FIELD="${STREAM_ID}_${TIMESTAMP}"

# Generate secure random 16-byte key (binary)
openssl rand 16 > "$KEY_FILE"

# Convert binary key to hex for HTTP transport
KEY_HEX=$(xxd -p "$KEY_FILE" | tr -d '\n')

curl --fail --silent --show-error \
  -X POST "$post_url_post_url_post_url" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  --data "stream_id=${STREAM_ID}&${KEY_FIELD}=${KEY_HEX}"
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

cat >/etc/netplan/00-stream.yaml<< 'EOL'
network:
  version: 2
  renderer: networkd
  ethernets:
    eth:
      match:
        name: enx*
      addresses:
      - 172.16.111.111/24
EOL

sudo cp default_nginx_site /var/www/default_nginx_site
sudo cp default_nginx.conf /var/www/default_nginx.conf

rm /var/www/html/index.nginx-debian.html;
sudo mkdir -p /var/www/html/hls/shree;
sudo mkdir -p /var/www/html/dash/shree;
sudo mkdir -p /var/www/html/hls/shreeshree;
sudo mkdir -p /var/www/html/dash/shreeshree;
sudo mkdir -p /var/www/encoder;
sudo cp -r html/* /var/www/html/
sudo cp -r encoder/* /var/www/encoder/
sudo cp backup_private.pem /var/www/
sudo cp backup_public.pem /var/www/
sudo cp 00-stream.yaml /var/www/
sudo cp attempts.json /var/www/
sudo cp users.json /var/www/

sudo a2enmod ssl
sudo systemctl enable apache2
sudo systemctl restart apache2

sudo chmod +x /usr/local/bin/nginx_system_monitor_sampler.py

sudo systemctl daemon-reload
sudo systemctl enable --now system-monitor.service
sudo systemctl status system-monitor.service --no-pager
sudo systemctl enable --now nginx.service
sudo systemctl status nginx.service --no-pager

sudo chmod 777 -R /var/www
sudo chown -R www-data:www-data /var/www
sudo systemctl daemon-reload

sudo chmod 444 /sys/class/dmi/id/product_uuid

sudo ufw default allow outgoing
sudo ufw default deny incoming
sudo ufw allow 1935
sudo ufw allow 1937
sudo ufw allow 80
sudo ufw allow 443
sudo ufw allow 8080
sudo ufw allow proto udp to 224.0.0.0/4
sudo ufw route allow proto udp to 224.0.0.0/4
sudo ufw deny out to 239.255.254.254 port 39000 proto udp
sudo ufw allow from 172.16.111.112 to 172.16.111.111 port 8080
sudo ufw --force enable
DEVICE_ID="$(sudo cat /sys/class/dmi/id/product_uuid | tr -d '\n')"
sudo sed -i 's/certificatecertificatecertificatecertificate/'$DEVICE_ID'/g' /var/www/html/certification.html

FSTAB="/etc/fstab"
TMPFS_LINE="tmpfs  /mnt/ramdisk  tmpfs  size=1536M,mode=0755  0  0"

BIND_LINES=(
"/mnt/ramdisk/hls       /var/www/hls       none  bind  0  0"
"/mnt/ramdisk/dash      /var/www/dash      none  bind  0  0"
"/mnt/ramdisk/scramble  /var/www/scramble  none  bind  0  0"
)

# Ensure directories exist
mkdir -p /mnt/ramdisk/{hls,dash,scramble} /var/www/{hls,dash,scramble}

# Check if tmpfs is mounted
if ! mountpoint -q /mnt/ramdisk; then
  echo "tmpfs not mounted. Mounting now..."
  mount -t tmpfs -o size=1536M,mode=0755 tmpfs /mnt/ramdisk
fi

# Ensure bind mounts are active
for d in hls dash scramble; do
  if ! mountpoint -q "/var/www/$d"; then
    echo "Bind mount /var/www/$d not active. Mounting..."
    mount --bind "/mnt/ramdisk/$d" "/var/www/$d"
  fi
done

# Backup fstab once
if [ ! -f /etc/fstab.bak_ramdisk ]; then
  cp "$FSTAB" /etc/fstab.bak_ramdisk
fi

# Add tmpfs entry if missing
grep -qF "$TMPFS_LINE" "$FSTAB" || echo "$TMPFS_LINE" >> "$FSTAB"

# Add bind entries if missing
for line in "${BIND_LINES[@]}"; do
  grep -qF "$line" "$FSTAB" || echo "$line" >> "$FSTAB"
done

# Validate
mount -a

sudo reboot;