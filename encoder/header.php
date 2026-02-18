<?php
require 'require_login.php';
include 'static.php';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ShreeBhattJi</title>
    <script src="chart.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

        :root {
            --bg: #020617;
            --panel: #0f172a;
            --panel2: #020617;
            --accent: #38bdf8;
            --accent2: #6366f1;
            --text: #e5e7eb;
            --muted: #94a3b8;
            --border: rgba(255, 255, 255, .08);
            --radius: 14px;
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: Inter, system-ui;
            background: var(--bg);
            color: var(--text);
        }

        /* HEADER */

        .top-header-1 {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #020617;
            border-bottom: 1px solid var(--border);
            z-index: 1002;
            font-size: 18px;
            font-weight: 600;
        }

        .top-header-1 a {
            text-decoration: none;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
            -webkit-background-clip: text;
            color: transparent;
        }

        .top-header-2 {
            position: fixed;
            top: 48px;
            left: 0;
            right: 0;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 26px;
            background: #020617;
            border-bottom: 1px solid var(--border);
            z-index: 1001;
            font-size: 14px;
        }

        .top-header-2 a {
            color: var(--muted);
            text-decoration: none;
        }

        .top-header-2 a:hover {
            color: var(--text)
        }

        .site-header {
            position: fixed;
            top: 90px;
            left: 0;
            right: 0;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(2, 6, 23, .85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            z-index: 1000;
        }

        .site-header nav {
            display: flex;
            gap: 26px;
            flex-wrap: wrap;
        }

        .site-header a {
            color: var(--muted);
            text-decoration: none;
        }

        .site-header a:hover {
            color: var(--text)
        }

        .page-wrap {
            padding-top: 150px
        }

        /* CONTAINER */

        .containerindex {
            max-width: 1280px;
            margin: 30px auto;
            padding: 24px;
            background: linear-gradient(180deg, #0b1220, #020617);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
        }

        /* GRID */

        .grid {
            display: grid;
            gap: 26px;
            grid-template-columns: repeat(2, 1fr);
        }

        /* last chart spans full row */
        .grid .card:last-child {
            grid-column: 1/-1;
        }

        /* mobile */
        @media(max-width:900px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        /* CARD */

        .card {
            padding: 20px;
            border-radius: var(--radius);
            background: rgba(255, 255, 255, .02);
            border: 1px solid var(--border);
            backdrop-filter: blur(4px);
        }

        .card h3 {
            margin: 0 0 16px;
            font-size: 18px;
            letter-spacing: .3px;
            font-weight: 600;
            color: #f1f5f9;
        }

        /* CHART AREA */

        .chart-wrap {
            position: relative;
            width: 100%;
            height: clamp(260px, 28vh, 420px);
        }

        .chart-wrap canvas {
            width: 100% !important;
            height: 100% !important;
        }

        /* GPU chart double height */
        .grid .card:last-child .chart-wrap {
            height: clamp(420px, 50vh, 720px);
        }

        /* TEXT */

        .muted {
            color: var(--muted)
        }

        #lastUpdate,
        #lastCpu,
        #lastRam,
        #lastGpu,
        #lastIn,
        #lastOut {
            font-weight: 600;
            color: #e2e8f0;
        }

        /* FOOTER */

        .site-footer {
            margin-top: 40px;
            padding: 20px 16px;
            display: flex;
            justify-content: center;
        }

        .footer-box {
            width: 100%;
            max-width: 1100px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 16px 22px;
            border-radius: var(--radius);
            background: linear-gradient(180deg, #0f172a, #020617);
            border: 1px solid var(--border);
            box-shadow: 0 10px 35px rgba(0, 0, 0, .45);
            font-size: 14px;
        }

        .phone {
            color: #38bdf8;
            text-decoration: none;
        }

        .phone:hover {
            text-decoration: underline
        }

        .heart {
            color: #ef4444;
            animation: pulse 1.4s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: .6
            }

            50% {
                opacity: 1
            }

            100% {
                opacity: .6
            }
        }
    </style>

</head>

<body>

    <!-- HEADER ROW 1 -->
    <header class="top-header-1">
        <a href="index.php">URMI Universal Digital Encoder / Decoder</a>
    </header>

    <!-- HEADER ROW 2 -->
    <header class="top-header-2">
        <a href="https://learn.urmic.org/" target="_blank">Tutorials</a>
        <a href="about_us.php">About Us</a>
        <a href="contact_us.php">Contact</a>
        <a href="premium_service.php">Premium</a>
        <a href="domain.php">Domain SSL</a>
    </header>

    <!-- HEADER ROW 3 -->
    <header class="site-header">
        <nav>
            <a href="status.php">Status</a>
            <a href="index.php">Monitor</a>
            <a href="input.php">Input</a>
            <a href="output.php">Output</a>
            <a href="network.php">Network</a>
            <a href="firewall.php">Firewall</a>
            <a href="firmware.php">Firmware</a>
            <a href="password.php">Password</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <!-- PAGE CONTENT WRAPPER -->
    <div class="page-wrap">