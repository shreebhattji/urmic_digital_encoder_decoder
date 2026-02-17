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
            --accent: #38bdf8;
            --accent2: #6366f1;
            --text: #e5e7eb;
            --muted: #94a3b8;
            --border: rgba(255, 255, 255, .08);
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

        /* ---------- HEADER STACK ---------- */

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
            transition: .25s;
        }

        .top-header-2 a:hover {
            color: var(--text)
        }


        /* ---------- MAIN NAV ---------- */

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
            font-size: 14px;
            transition: .25s;
        }

        .site-header a:hover {
            color: var(--text);
        }

        /* ---------- PAGE OFFSET ---------- */

        .page-wrap {
            padding-top: 150px;
        }

        /* ---------- CONTAINER ---------- */

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 24px;
            background: linear-gradient(180deg, #0f172a, #020617);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
        }

        /* ---------- MOBILE ---------- */

        @media(max-width:700px) {
            .site-header nav {
                gap: 16px
            }

            .top-header-2 {
                flex-wrap: wrap;
                height: auto;
                padding: 8px
            }

            .page-wrap {
                padding-top: 180px
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