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
#!/usr/bin/env python3
import time, json, os, subprocess
from collections import deque
from datetime import datetime
import psutil

OUT_FILE="/var/www/encoder/metrics.json"
TMP_FILE=OUT_FILE+".tmp"
SAMPLE_INTERVAL=10.0
HISTORY_SECONDS=15*60
MAX_SAMPLES=int(HISTORY_SECONDS/SAMPLE_INTERVAL)

timestamps=deque(maxlen=MAX_SAMPLES)
cpu_hist=deque(maxlen=MAX_SAMPLES)
ram_hist=deque(maxlen=MAX_SAMPLES)
gpu_hist=deque(maxlen=MAX_SAMPLES)
net_in_hist=deque(maxlen=MAX_SAMPLES)
net_out_hist=deque(maxlen=MAX_SAMPLES)
disk_read_hist=deque(maxlen=MAX_SAMPLES)
disk_write_hist=deque(maxlen=MAX_SAMPLES)
disk_percent_hist=deque(maxlen=MAX_SAMPLES)

_prev_net=psutil.net_io_counters()
_prev_disk=psutil.disk_io_counters()
_prev_time=time.time()

def igpu_percent():
    # fastest method (modern kernels)
    for card in ("card0","card1","card2"):
        p=f"/sys/class/drm/{card}/gt_busy_percent"
        if os.path.exists(p):
            try:
                return float(open(p).read().strip())
            except:
                pass

    # fallback: intel_gpu_top JSON snapshot
    try:
        out=subprocess.check_output(
            ["intel_gpu_top","-J","-s","100","-o","-"],
            stderr=subprocess.DEVNULL,
            timeout=1
        )
        j=json.loads(out.splitlines()[0])
        return float(j["engines"]["Render/3D/0"]["busy"])
    except:
        return 0.0

def sample_once():
    global _prev_net,_prev_disk,_prev_time
    now=time.time()
    iso=datetime.fromtimestamp(now).isoformat(timespec='seconds')

    cpu=psutil.cpu_percent(interval=None)
    ram=psutil.virtual_memory().percent
    gpu=igpu_percent()

    net=psutil.net_io_counters()
    disk=psutil.disk_io_counters()

    try:
        disk_percent=psutil.disk_usage("/").percent
    except:
        disk_percent=0.0

    elapsed=now-_prev_time if _prev_time else SAMPLE_INTERVAL
    if elapsed<=0: elapsed=SAMPLE_INTERVAL

    in_rate=int(((net.bytes_recv-_prev_net.bytes_recv)/elapsed))
    out_rate=int(((net.bytes_sent-_prev_net.bytes_sent)/elapsed))

    read_rate=(disk.read_bytes-_prev_disk.read_bytes)/elapsed
    write_rate=(disk.write_bytes-_prev_disk.write_bytes)/elapsed

    timestamps.append(iso)
    cpu_hist.append(round(cpu,2))
    ram_hist.append(round(ram,2))
    gpu_hist.append(round(gpu,2))
    net_in_hist.append(int(in_rate))
    net_out_hist.append(int(out_rate))
    disk_read_hist.append(int(read_rate))
    disk_write_hist.append(int(write_rate))
    disk_percent_hist.append(round(disk_percent,2))

    _prev_net=net
    _prev_disk=disk
    _prev_time=now

def write_json_atomic():
    payload={
        "timestamps":list(timestamps),
        "cpu_percent":list(cpu_hist),
        "ram_percent":list(ram_hist),
        "igpu_percent":list(gpu_hist),
        "net_in_Bps":list(net_in_hist),
        "net_out_Bps":list(net_out_hist),
        "disk_read_Bps":list(disk_read_hist),
        "disk_write_Bps":list(disk_write_hist),
        "disk_percent":list(disk_percent_hist),
        "sample_interval":SAMPLE_INTERVAL,
        "generated_at":datetime.utcnow().isoformat(timespec='seconds')+"Z"
    }
    with open(TMP_FILE,"w") as f: json.dump(payload,f)
    os.replace(TMP_FILE,OUT_FILE)

def main():
    global _prev_net,_prev_disk,_prev_time
    _prev_net=psutil.net_io_counters()
    _prev_disk=psutil.disk_io_counters()
    _prev_time=time.time()
    time.sleep(0.2)

    while True:
        try:
            sample_once()
            write_json_atomic()
        except Exception as e:
            print("Sampler error:",e)
        time.sleep(SAMPLE_INTERVAL)

if __name__=="__main__":
    main()
EOL

sudo systemctl enable --now system-monitor.service
sudo systemctl restart system-monitor.service --no-pager


sudo reboot