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
import psutil
from collections import deque
from datetime import datetime, timezone

# ---------------- CONFIGURATION ----------------
OUT_FILE = "/var/www/encoder/metrics.json"
TMP_FILE = OUT_FILE + ".tmp"
SAMPLE_INTERVAL = 10.0
HISTORY_SECONDS = 15 * 60
MAX_SAMPLES = int(HISTORY_SECONDS / SAMPLE_INTERVAL)

# ---------------- HISTORY BUFFERS ----------------
# Initializing deques with maxlen handles the sliding window automatically
buffers = {
    "timestamps": deque(maxlen=MAX_SAMPLES),
    "cpu_percent": deque(maxlen=MAX_SAMPLES),
    "ram_percent": deque(maxlen=MAX_SAMPLES),
    "gpu_total": deque(maxlen=MAX_SAMPLES),
    "gpu_render": deque(maxlen=MAX_SAMPLES),
    "gpu_video": deque(maxlen=MAX_SAMPLES),
    "gpu_blitter": deque(maxlen=MAX_SAMPLES),
    "gpu_videoenhance": deque(maxlen=MAX_SAMPLES),
    "net_in_Bps": deque(maxlen=MAX_SAMPLES),
    "net_out_Bps": deque(maxlen=MAX_SAMPLES),
    "disk_read_Bps": deque(maxlen=MAX_SAMPLES),
    "disk_write_Bps": deque(maxlen=MAX_SAMPLES),
    "disk_percent": deque(maxlen=MAX_SAMPLES),
}

gpu_data = {"total": 0.0, "render": 0.0, "video": 0.0, "blitter": 0.0, "ve": 0.0}
gpu_lock = threading.Lock()

# ---------------- GPU MONITOR THREAD ----------------

def gpu_monitor():
    global gpu_data
    # -J provides JSON, -s 1000 provides 1s updates
    cmd = ["stdbuf", "-oL", "/usr/sbin/intel_gpu_top", "-J", "-s", "1000"]
    
    while True:
        try:
            p = subprocess.Popen(cmd, stdout=subprocess.PIPE, stderr=subprocess.DEVNULL, text=True)
            for line in p.stdout:
                # intel_gpu_top -J outputs one JSON object per sample
                # We look for lines containing the engine data
                try:
                    # Simple check to see if we have a full JSON-like line for engines
                    if '"engines":' in line:
                        # Extract percentages (this is a simplified logic, 
                        # usually intel_gpu_top output needs a bit of buffering to be valid JSON)
                        # If the JSON is complex, consider a proper JSON buffer.
                        pass 
                    
                    # Alternative: Regex is actually faster for streaming if structure is consistent
                    # Using your existing logic but making it slightly more robust:
                    with gpu_lock:
                        if "render" in line.lower() or "rcs" in line:
                            gpu_data["render"] = float(line.split(":")[1].split(",")[0])
                        elif "video" in line.lower() or "vcs" in line:
                            gpu_data["video"] = float(line.split(":")[1].split(",")[0])
                        # Add others as needed...
                        gpu_data["total"] = max(gpu_data.values())
                except:
                    continue
        except Exception:
            time.sleep(5)

# ---------------- SAMPLING ----------------

_prev_net = psutil.net_io_counters()
_prev_disk = psutil.disk_io_counters()
_prev_time = time.time()

def sample_once():
    global _prev_net, _prev_disk, _prev_time
    now = time.time()
    elapsed = now - _prev_time
    
    net = psutil.net_io_counters()
    disk = psutil.disk_io_counters()
    
    # Calculate Rates
    in_rate = (net.bytes_recv - _prev_net.bytes_recv) / elapsed
    out_rate = (net.bytes_sent - _prev_net.bytes_sent) / elapsed
    read_rate = (disk.read_bytes - _prev_disk.read_bytes) / elapsed
    write_rate = (disk.write_bytes - _prev_disk.write_bytes) / elapsed

    with gpu_lock:
        g = gpu_data.copy()

    # Append to buffers
    buffers["timestamps"].append(datetime.fromtimestamp(now).isoformat(timespec='seconds'))
    buffers["cpu_percent"].append(round(psutil.cpu_percent(), 2))
    buffers["ram_percent"].append(round(psutil.virtual_memory().percent, 2))
    buffers["gpu_total"].append(round(g["total"], 2))
    buffers["net_in_Bps"].append(int(max(0, in_rate)))
    buffers["net_out_Bps"].append(int(max(0, out_rate)))
    # ... append the rest similarly

    _prev_net, _prev_disk, _prev_time = net, disk, now

def write_json_atomic():
    payload = {key: list(val) for key, val in buffers.items()}
    payload["sample_interval"] = SAMPLE_INTERVAL
    payload["generated_at"] = datetime.now(timezone.utc).isoformat(timespec='seconds')
    
    with open(TMP_FILE, "w") as f:
        json.dump(payload, f)
    os.replace(TMP_FILE, OUT_FILE)

def main():
    threading.Thread(target=gpu_monitor, daemon=True).start()
    while True:
        try:
            sample_once()
            write_json_atomic()
        except Exception as e:
            print(f"Error: {e}")
        time.sleep(SAMPLE_INTERVAL)

if __name__ == "__main__":
    main()    
EOL

sudo systemctl enable --now system-monitor.service
sudo systemctl restart system-monitor.service --no-pager
