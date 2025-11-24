<?php include 'header.php'; ?>

<style>
    .card-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .card-left,
    .card-right {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .card-left {
        flex: 1 1 55%;
    }

    .card-right {
        flex: 1 1 40%;
        align-items: flex-end;
        text-align: right;
    }

    .input-wrapper {
        position: relative;
        width: 100%;
    }

    .input-wrapper input {
        width: 100%;
        padding: 10px 40px 10px 12px;
        border-radius: 25px;
        border: 1px solid #ccc;
        font-size: 0.95rem;
        outline: none;
        background: #f9fafb;
    }

    .copy-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.1rem;
        color: #444;
        pointer-events: none;
        /* visual only */
    }

    .service-label {
        font-size: 0.9rem;
        color: #4b5563;
    }

    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-left: 6px;
    }

    .badge-enabled {
        background: #16a34a22;
        color: #15803d;
        border: 1px solid #16a34a;
    }

    .badge-disabled {
        background: #b91c1c22;
        color: #b91c1c;
        border: 1px solid #b91c1c;
    }

    .service-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 4px;
    }

    .service-buttons button {
        padding: 6px 14px;
        border-radius: 999px;
        border: 1px solid transparent;
        font-size: 0.85rem;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-restart {
        border-color: #0f172a;
        background: #0f172a;
        color: #fff;
    }

    .btn-enable {
        border-color: #15803d;
        background: #15803d;
        color: #fff;
    }

    .btn-disable {
        border-color: #b91c1c;
        background: #b91c1c;
        color: #fff;
    }

    @media (max-width: 768px) {
        .card-right {
            align-items: flex-start;
            text-align: left;
        }
    }
</style>
<div class="containerindex">
    <div class="grid">
        <div class="card wide">
            <h3>Input Service</h3>
            <?php
            $status = shell_exec("sudo systemctl is-active encoder-main 2>&1");
            $status = trim($status);

            if ($status === "active")
                $serviceEnabled = true;
            else
                $serviceEnabled = false;
            ?>

            <div class="card-row">
                <div class="service-label">
                    <strong>Service</strong>

                    <?php if ($serviceEnabled): ?>
                        <span class="badge badge-enabled">Enabled</span>
                    <?php else: ?>
                        <span class="badge badge-disabled">Disabled</span>
                    <?php endif; ?>
                </div>

                <form method="post" class="service-buttons">
                    <button type="submit" name="action_rtmp" value="restart" class="btn-restart">
                        Restart
                    </button>

                    <?php if ($serviceEnabled): ?>
                        <button type="submit" name="action_rtmp" value="disable" class="btn-disable">
                            Disable
                        </button>
                    <?php else: ?>
                        <button type="submit" name="action_rtmp" value="enable" class="btn-enable">
                            Enable
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card wide">
            <h3>RTMP Server</h3>
            <?php
            $status = shell_exec("sudo systemctl is-active encoder-rtmp 2>&1");
            $status = trim($status);

            if ($status === "active")
                $serviceEnabled = true;
            else
                $serviceEnabled = false;

            $m3u8_url       = 'https://example.com/live/stream.m3u8';
            ?>

            <div class="card-row">
                <div class="card-left">
                    <div class="input-wrapper">
                        <input id="m3u8-link" type="text" readonly
                            value="<?php echo htmlspecialchars($m3u8_url, ENT_QUOTES); ?>">
                        <span class="copy-icon">📋</span>
                    </div>
                </div>
                <div class="card-right">
                    <div class="service-label">
                        <strong>Service</strong>

                        <?php if ($serviceEnabled): ?>
                            <span class="badge badge-enabled">Enabled</span>
                        <?php else: ?>
                            <span class="badge badge-disabled">Disabled</span>
                        <?php endif; ?>
                    </div>

                    <form method="post" class="service-buttons">
                        <button type="submit" name="action_rtmp" value="restart" class="btn-restart">
                            Restart
                        </button>

                        <?php if ($serviceEnabled): ?>
                            <button type="submit" name="action_rtmp" value="disable" class="btn-disable">
                                Disable
                            </button>
                        <?php else: ?>
                            <button type="submit" name="action_rtmp" value="enable" class="btn-enable">
                                Enable
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="card wide">
            <h3>SRT Server</h3>
            <?php
            $m3u8_url       = 'https://example.com/live/stream.m3u8';
            $status = shell_exec("sudo systemctl is-active encoder-srt 2>&1");
            $status = trim($status);

            if ($status === "active")
                $serviceEnabled = true;
            else
                $serviceEnabled = false;
            ?>

            <div class="card-row">
                <div class="card-left">
                    <div class="input-wrapper">
                        <input id="m3u8-link" type="text" readonly
                            value="<?php echo htmlspecialchars($m3u8_url, ENT_QUOTES); ?>">
                        <span class="copy-icon">📋</span>
                    </div>
                </div>
                <div class="card-right">
                    <div class="service-label">
                        <strong>Service</strong>

                        <?php if ($serviceEnabled): ?>
                            <span class="badge badge-enabled">Enabled</span>
                        <?php else: ?>
                            <span class="badge badge-disabled">Disabled</span>
                        <?php endif; ?>
                    </div>

                    <form method="post" class="service-buttons">
                        <button type="submit" name="action_rtmp" value="restart" class="btn-restart">
                            Restart
                        </button>

                        <?php if ($serviceEnabled): ?>
                            <button type="submit" name="action_rtmp" value="disable" class="btn-disable">
                                Disable
                            </button>
                        <?php else: ?>
                            <button type="submit" name="action_rtmp" value="enable" class="btn-enable">
                                Enable
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="card wide">
            <h3>Udp Service</h3>
            <?php
            $m3u8_url       = 'https://example.com/live/stream.m3u8';
            $status = shell_exec("sudo systemctl is-active encoder-udp   2>&1");
            $status = trim($status);

            if ($status === "active")
                $serviceEnabled = true;
            else
                $serviceEnabled = false;
            ?>

            <div class="card-row">
                <div class="card-left">
                    <div class="input-wrapper">
                        <input id="m3u8-link" type="text" readonly
                            value="<?php echo htmlspecialchars($m3u8_url, ENT_QUOTES); ?>">
                        <span class="copy-icon">📋</span>
                    </div>
                </div>
                <div class="card-right">
                    <div class="service-label">
                        <strong>Service</strong>

                        <?php if ($serviceEnabled): ?>
                            <span class="badge badge-enabled">Enabled</span>
                        <?php else: ?>
                            <span class="badge badge-disabled">Disabled</span>
                        <?php endif; ?>
                    </div>

                    <form method="post" class="service-buttons">
                        <button type="submit" name="action_rtmp" value="restart" class="btn-restart">
                            Restart
                        </button>

                        <?php if ($serviceEnabled): ?>
                            <button type="submit" name="action_rtmp" value="disable" class="btn-disable">
                                Disable
                            </button>
                        <?php else: ?>
                            <button type="submit" name="action_rtmp" value="enable" class="btn-enable">
                                Enable
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="card wide">
            <h3>Custom Output Service</h3>
            <?php
            $status = shell_exec("sudo systemctl is-active encoder-custom   2>&1");
            $status = trim($status);

            if ($status === "active")
                $serviceEnabled = true;
            else
                $serviceEnabled = false;
            ?>

            <div class="card-row">
                <div class="service-label">
                    <strong>Service</strong>

                    <?php if ($serviceEnabled): ?>
                        <span class="badge badge-enabled">Enabled</span>
                    <?php else: ?>
                        <span class="badge badge-disabled">Disabled</span>
                    <?php endif; ?>
                </div>

                <form method="post" class="service-buttons">
                    <button type="submit" name="action_rtmp" value="restart" class="btn-restart">
                        Restart
                    </button>

                    <?php if ($serviceEnabled): ?>
                        <button type="submit" name="action_rtmp" value="disable" class="btn-disable">
                            Disable
                        </button>
                    <?php else: ?>
                        <button type="submit" name="action_rtmp" value="enable" class="btn-enable">
                            Enable
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card wide">
            <div class="player-wrapper">
                <video
                    id="m3u8Player"
                    controls
                    playsinline>
                    Your browser does not support HTML5 video.
                </video>
            </div>
        </div>

    </div>
</div>

<br>
<br>
<?php include 'footer.php'; ?>