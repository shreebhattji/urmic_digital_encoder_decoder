sudo cp -r encoder/* /var/www/encoder/
sudo cp -r html/* /var/www/html/
sudo cp attempts.json /var/www/attempts.json
DEVICE_ID="$(sudo cat /sys/class/dmi/id/product_uuid | tr -d '\n')"
sudo sed -i 's/certificatecertificatecertificatecertificate/'$DEVICE_ID'/g' /var/www/html/certification.html

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


SOURCE_FILE="users.json"
TARGET_FILE="/var/www/users.json"

if [ ! -f "$TARGET_FILE" ]; then
    cp "$SOURCE_FILE" "$TARGET_FILE"
fi

SOURCE_FILE="users.json"
TARGET_FILE="/var/www/users.json"

if [ ! -f "$TARGET_FILE" ]; then
    cp "$SOURCE_FILE" "$TARGET_FILE"
fi


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

sudo mkdir -p /etc/systemd/system/nginx.service.d

sudo cat > /etc/systemd/system/nginx.service.d/override.conf << 'EOF'
[Service]
Restart=always
RestartSec=30
EOF

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

sudo cp default_nginx_site /var/www/default_nginx_site
sudo cp default_nginx.conf /var/www/default_nginx.conf

sudo systemctl unmask systemd-networkd-wait-online.service
sudo systemctl enable systemd-networkd-wait-online.service
sudo systemctl daemon-reload
sudo systemctl restart nginx

sudo a2enmod ssl
sudo a2ensite 000-default

sudo chown -R www-data:www-data /var/www/*

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

sudo systemctl enable --now system-monitor.service
sudo systemctl restart system-monitor.service --no-pager
