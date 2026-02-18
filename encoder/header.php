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
            text-decoration: none
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
            flex-wrap: wrap
        }

        .site-header a {
            color: var(--muted);
            text-decoration: none
        }

        .site-header a:hover {
            color: var(--text)
        }

        .page-wrap {
            padding-top: 150px
        }

        /* CONTAINER */

        .containerindex {
            max-width: 1100px;
            margin: 30px auto;
            padding: 24px;
            background: linear-gradient(180deg, var(--panel), var(--panel2));
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
        }

        /* GRID */

        .grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        }

        /* CARD */

        .card {
            padding: 20px;
            border-radius: var(--radius);
            background: rgba(255, 255, 255, .02);
            border: 1px solid var(--border);
        }

        .card.wide {
            grid-column: 1/-1
        }

        .card h3 {
            margin: 0 0 16px;
            font-size: 17px;
            font-weight: 600;
            color: #f1f5f9;
        }

        /* DROPDOWN ROW */

        .dropdown-container {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .dropdown-label {
            min-width: 170px;
            color: var(--muted);
            font-size: 14px;
        }

        .dropdown select {
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #020617;
            color: var(--text);
            min-width: 180px;
        }

        /* INPUT ROW BLOCK (URL rows etc) */

        .input-container {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            align-items: flex-end;
        }

        .input-container .input-group {
            flex: 1 1 260px;
            margin: 0;
        }

        /* INPUT */

        .input-group {
            position: relative;
            margin-bottom: 18px;
        }

        .input-group input {
            width: 100%;
            padding: 14px 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #020617;
            color: var(--text);
            font-size: 14px;
        }

        .input-group input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(56, 189, 248, .15);
            outline: none;
        }

        .input-group label {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            color: var(--muted);
            background: #020617;
            padding: 0 6px;
            transition: .2s;
        }

        .input-group input:focus+label,
        .input-group input:not(:placeholder-shown)+label {
            top: -7px;
            font-size: 11px;
            color: var(--accent);
        }

        /* CHECKBOX */

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .checkbox-group input {
            width: 18px;
            height: 18px;
            accent-color: #38bdf8;
        }

        .checkbox-group label {
            font-size: 14px;
            color: var(--muted);
        }

        /* BUTTON */

        button[type="submit"] {
            background: linear-gradient(90deg, #ef4444, #dc2626);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            opacity: .9
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

        .footer-box strong {
            color: #e2e8f0
        }

        .phone {
            color: #38bdf8;
            text-decoration: none
        }

        .phone:hover {
            text-decoration: underline
        }

        .muted {
            color: #94a3b8
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

        /* MOBILE */

        @media(max-width:700px) {
            .site-header nav {
                gap: 14px
            }

            .page-wrap {
                padding-top: 180px
            }

            .footer-box {
                flex-direction: column;
                text-align: center
            }
        }

        /* CONTACT PAGE */

        .card ul {
            margin: 10px 0 0 18px;
            padding: 0;
            line-height: 1.7;
            color: #cbd5e1;
            font-size: 15px;
        }

        .card ul li {
            margin-bottom: 8px;
        }

        .card p {
            margin: 6px 0;
            line-height: 1.6;
            color: #cbd5e1;
            font-size: 15px;
        }

        /* SOCIAL ICON ROW */

        .social-row {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 10px;
            justify-content: center;
        }

        .social-btn {
            width: 58px;
            height: 58px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: rgba(255, 255, 255, .03);
            border: 1px solid rgba(255, 255, 255, .08);
            color: #cbd5e1;
            transition: .25s;
            text-decoration: none;
        }

        .social-btn:hover {
            transform: translateY(-4px) scale(1.05);
            background: linear-gradient(135deg, #0ea5e9, #6366f1);
            color: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .45);
        }

        .social-btn svg {
            width: 24px;
            height: 24px;
            display: block;
        }

        /* CONTACT CARD HEADINGS */

        .card.wide h3 {
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            padding-bottom: 8px;
            margin-bottom: 14px;
        }

        /* ADDRESS BLOCK */

        .card p br {
            margin-bottom: 6px;
        }

        /* MOBILE TUNING */

        @media(max-width:600px) {
            .social-row {
                gap: 10px;
            }

            .social-btn {
                width: 50px;
                height: 50px;
            }
        }

        /* ===== FIREWALL PAGE ADDON ===== */

        /* section title */
        .card h2 {
            margin: 0 0 18px;
            font-size: 18px;
            font-weight: 600;
            border-bottom: 1px solid var(--border);
            padding-bottom: 8px;
            color: #f1f5f9;
        }

        /* form rows */
        .row {
            margin-bottom: 18px;
        }

        /* label */
        .row label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--muted);
        }

        /* textarea fields */
        textarea {
            width: 100%;
            padding: 14px 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #020617;
            color: var(--text);
            font-size: 14px;
            resize: vertical;
            transition: .2s;
        }

        textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(56, 189, 248, .15);
        }

        /* helper hint */
        .row small {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: #64748b;
        }

        /* invalid highlight */
        textarea:invalid {
            border-color: #ef4444;
        }

        /* submit button spacing */
        .card form button {
            margin-top: 18px;
        }

        /* smooth card spacing for stacked ports */
        .row:not(:last-child) {
            padding-bottom: 12px;
            border-bottom: 1px dashed rgba(255, 255, 255, .05);
        }

        /* mobile optimization */
        @media(max-width:600px) {
            .card h2 {
                font-size: 16px
            }

            textarea {
                font-size: 13px
            }
        }

        /* ===== PASSWORD PAGE GLOBAL STYLES ===== */

        .password-form {
            max-width: 520px;
            margin-top: 10px;
        }

        .password-form .field {
            margin-bottom: 18px;
        }

        .password-form label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--muted);
        }

        .password-form input {
            width: 100%;
            padding: 14px 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #020617;
            color: var(--text);
            font-size: 14px;
            transition: .2s;
        }

        .password-form input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(56, 189, 248, .15);
        }

        .password-form input:invalid {
            border-color: #ef4444;
        }

        /* divider between fields */
        .password-form .field:not(:last-of-type) {
            padding-bottom: 14px;
            border-bottom: 1px dashed rgba(255, 255, 255, .05);
        }

        /* strength bar container */
        .strength {
            margin-top: 8px;
            height: 8px;
            border-radius: 6px;
            background: #111827;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        /* strength fill */
        .strength-bar {
            height: 100%;
            width: 0%;
            background: #ef4444;
            transition: .3s;
        }

        /* strength text */
        .strength-text {
            font-size: 12px;
            margin-top: 6px;
            color: #94a3b8;
        }

        /* colors by strength */
        .strength-weak {
            background: #ef4444
        }

        .strength-medium {
            background: #f59e0b
        }

        .strength-good {
            background: #22c55e
        }

        .strength-strong {
            background: linear-gradient(90deg, #22c55e, #38bdf8);
        }

        /* show password toggle */
        .pass-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 13px;
            color: #94a3b8;
            user-select: none;
        }

        .pass-wrap {
            position: relative;
        }

        /* mobile */
        @media(max-width:600px) {
            .password-form {
                max-width: 100%
            }
        }

        /* ===== CERT REQUEST PAGE ADDON ===== */

        /* wrapper */
        .wrap {
            width: 100%;
        }

        /* labels */
        .wrap label {
            display: block;
            margin-top: 14px;
            font-size: 14px;
            font-weight: 600;
            color: var(--muted);
        }

        /* inputs */
        .wrap input[type=text],
        .wrap input[type=email],
        .wrap select {
            width: 100%;
            padding: 14px 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #020617;
            color: var(--text);
            font-size: 14px;
            transition: .2s;
        }

        .wrap input:focus,
        .wrap select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(56, 189, 248, .15);
        }

        /* grid row */
        .wrap .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 12px;
        }

        /* checkbox block */
        .wrap .checkbox {
            display: flex;
            gap: 10px;
            margin-top: 18px;
            align-items: flex-start;
        }

        .wrap .checkbox input {
            margin-top: 4px;
            accent-color: #38bdf8;
        }

        /* links */
        .wrap .links {
            margin-top: 14px;
            font-size: 14px;
        }

        .wrap .links a {
            color: #38bdf8;
            text-decoration: none;
        }

        .wrap .links a:hover {
            text-decoration: underline;
        }

        /* buttons row */
        .wrap .actions {
            display: flex;
            gap: 12px;
            margin-top: 18px;
        }

        .wrap .ghost {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        /* info note box */
        .wrap .note {
            margin-top: 20px;
            padding: 16px;
            border-radius: var(--radius);
            background: rgba(255, 255, 255, .03);
            border: 1px solid var(--border);
            font-size: 13px;
            line-height: 1.6;
        }

        /* code block */
        .wrap pre {
            margin-top: 10px;
            padding: 12px;
            border-radius: 10px;
            background: #020617;
            border: 1px dashed var(--border);
            color: #cbd5e1;
            font-size: 13px;
        }

        /* responsive */
        @media(max-width:700px) {
            .wrap .row {
                grid-template-columns: 1fr;
            }
        }

        /* ===== NOTE BLOCK FIX ===== */

        .card .note {
            margin-top: 22px;
            padding: 18px;
            border-radius: 14px;
            background: linear-gradient(180deg, #020617, #020617);
            border: 1px solid var(--border);
            color: #cbd5e1;
            font-size: 14px;
            line-height: 1.65;
        }

        /* title */
        .card .note strong {
            display: block;
            margin-bottom: 10px;
            font-size: 15px;
            color: #e2e8f0;
        }

        /* code block inside note */
        .card .note pre {
            margin-top: 12px;
            padding: 14px;
            border-radius: 12px;
            background: #010409;
            border: 1px dashed rgba(255, 255, 255, .08);
            color: #94a3b8;
            font-size: 13px;
            line-height: 1.6;
            overflow: auto;
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