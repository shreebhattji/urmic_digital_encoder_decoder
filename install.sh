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
#!/usr/bin/env python3 -u
import time
import json
import os
import subprocess
import threading
import re
import psutil
import shutil
from collections import deque
from datetime import datetime, timezone

# ---------------- CONFIGURATION ----------------
OUT_FILE = "/var/www/encoder/metrics.json"
TMP_FILE = OUT_FILE + ".tmp"
SAMPLE_INTERVAL = 10.0
HISTORY_SECONDS = 15 * 60
MAX_SAMPLES = int(HISTORY_SECONDS / SAMPLE_INTERVAL)

# ---------------- DEPENDENCY CHECK ----------------
if not shutil.which("intel_gpu_top"):
    raise RuntimeError("intel_gpu_top not installed or not in PATH")

# ---------------- HISTORY BUFFERS ----------------
keys = [
    "timestamps", "cpu_percent", "ram_percent", "gpu_total", "gpu_render",
    "gpu_video", "gpu_blitter", "gpu_videoenhance", "net_in_Bps",
    "net_out_Bps", "disk_read_Bps", "disk_write_Bps", "disk_percent"
]
hist = {k: deque(maxlen=MAX_SAMPLES) for k in keys}

_prev_net = psutil.net_io_counters()
_prev_disk = psutil.disk_io_counters()
_prev_time = time.time()

# Prime CPU measurement
psutil.cpu_percent(None)

gpu_data = {"total": 0.0, "render": 0.0, "video": 0.0, "blitter": 0.0, "ve": 0.0}
gpu_lock = threading.Lock()

# ---------------- GPU MONITOR THREAD ----------------
def gpu_monitor():
    global gpu_data

    cmd = ["intel_gpu_top", "-J", "-s", "1000"]

    while True:
        try:
            p = subprocess.Popen(
                cmd,
                stdout=subprocess.PIPE,
                stderr=subprocess.DEVNULL,
                text=True,
                bufsize=1
            )

            buf = ""
            brace = 0

            for chunk in iter(lambda: p.stdout.read(1), ""):
                if chunk == "{":
                    brace += 1

                if brace > 0:
                    buf += chunk

                if chunk == "}":
                    brace -= 1
                    if brace == 0 and buf.strip():
                        try:
                            obj = json.loads(buf)
                            engines = obj.get("engines", {})

                            r = v = b = e = 0.0

                            for name, data in engines.items():
                                busy = float(data.get("busy", 0.0))
                                n = name.lower()

                                if "render" in n or "rcs" in n:
                                    r = max(r, busy)
                                elif "video" in n or "vcs" in n:
                                    v = max(v, busy)
                                elif "blitter" in n or "bcs" in n:
                                    b = max(b, busy)
                                elif "enhance" in n or "vecs" in n:
                                    e = max(e, busy)

                            with gpu_lock:
                                gpu_data["render"] = r
                                gpu_data["video"] = v
                                gpu_data["blitter"] = b
                                gpu_data["ve"] = e
                                gpu_data["total"] = max(r, v, b, e)

                        except Exception:
                            pass

                        buf = ""

            p.wait()

        except Exception:
            time.sleep(2)

# ---------------- SAMPLING ----------------
def sample_once():
    global _prev_net, _prev_disk, _prev_time

    now = time.time()
    elapsed = max(now - _prev_time, 0.1)

    cpu = psutil.cpu_percent()
    ram = psutil.virtual_memory().percent
    net = psutil.net_io_counters()
    disk = psutil.disk_io_counters()

    in_r = (net.bytes_recv - _prev_net.bytes_recv) / elapsed
    out_r = (net.bytes_sent - _prev_net.bytes_sent) / elapsed
    read_r = (disk.read_bytes - _prev_disk.read_bytes) / elapsed
    write_r = (disk.write_bytes - _prev_disk.write_bytes) / elapsed

    with gpu_lock:
        g = gpu_data.copy()

    # stale GPU protection
    if time.time() - _prev_time > SAMPLE_INTERVAL * 2:
        g = {"total": 0, "render": 0, "video": 0, "blitter": 0, "ve": 0}

    hist["timestamps"].append(datetime.now().isoformat(timespec='seconds'))
    hist["cpu_percent"].append(round(cpu, 1))
    hist["ram_percent"].append(round(ram, 1))
    hist["net_in_Bps"].append(int(max(0, in_r)))
    hist["net_out_Bps"].append(int(max(0, out_r)))
    hist["disk_read_Bps"].append(int(max(0, read_r)))
    hist["disk_write_Bps"].append(int(max(0, write_r)))
    hist["disk_percent"].append(round(psutil.disk_usage('/').percent, 1))
    hist["gpu_total"].append(round(g["total"], 1))
    hist["gpu_render"].append(round(g["render"], 1))
    hist["gpu_video"].append(round(g["video"], 1))
    hist["gpu_blitter"].append(round(g["blitter"], 1))
    hist["gpu_videoenhance"].append(round(g["ve"], 1))

    _prev_net, _prev_disk, _prev_time = net, disk, now

# ---------------- MAIN LOOP ----------------
def main():
    threading.Thread(target=gpu_monitor, daemon=True).start()

    while True:
        try:
            sample_once()

            payload = {k: list(v) for k, v in hist.items()}
            payload.update({
                "sample_interval": SAMPLE_INTERVAL,
                "generated_at": datetime.now(timezone.utc).isoformat()
            })

            with open(TMP_FILE, "w") as f:
                json.dump(payload, f)

            os.replace(TMP_FILE, OUT_FILE)

        except Exception:
            pass

        time.sleep(SAMPLE_INTERVAL)

# ---------------- ENTRY ----------------
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
sudo cp /var/www/default_nginx_site /etc/nginx/sites-available/default
sudo cp /var/www/default_nginx.conf /etc/nginx/nginx.conf

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
"/mnt/ramdisk/hls       /var/www/html/hls       none  bind  0  0"
"/mnt/ramdisk/dash      /var/www/html/dash      none  bind  0  0"
"/mnt/ramdisk/scramble  /var/www/html/scramble  none  bind  0  0"
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