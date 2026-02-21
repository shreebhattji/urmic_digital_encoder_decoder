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
            background: radial-gradient(circle at 50% -20%, #0b1225 0%, #020617 60%);
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

            /* standard + vendor for compatibility */
            background-clip: text;
            -webkit-background-clip: text;

            color: transparent;
            -webkit-text-fill-color: transparent;
            /* required for Safari/WebKit */
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
        /* slightly brighter panel for readability */
        /* ===== MERGED + IMPROVED VISIBILITY (REPLACE EXISTING BLOCKS) ===== */

        /* container */
        .containerindex {
            max-width: 1280px;
            margin: 30px auto;
            padding: 24px;
            background: linear-gradient(180deg, #0f1b33, #020617);
            border: 1px solid rgba(56, 189, 248, .25);
            border-radius: var(--radius);
            box-shadow:
                0 0 0 1px rgba(56, 189, 248, .08),
                0 25px 70px rgba(0, 0, 0, .75),
                0 0 40px rgba(56, 189, 248, .06);
            position: relative;
        }

        /* inner glow separation */
        .containerindex::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: var(--radius);
            pointer-events: none;
            box-shadow: inset 0 0 35px rgba(99, 102, 241, .08);
        }

        /* cards */
        .card {
            padding: 20px;
            border-radius: var(--radius);
            background: linear-gradient(180deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .015));
            border: 1px solid rgba(255, 255, 255, .12);
            backdrop-filter: blur(4px);
            box-shadow: 0 8px 28px rgba(0, 0, 0, .55);
            margin-bottom: 24px;
        }

        /* GRID */

        .grid {
            display: grid;
            gap: 26px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        /* CARD */

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

        .card:has(#gpuChart) {
            grid-column: 1 / -1;
        }

        /* slightly taller only for GPU chart */
        .card:has(#gpuChart) .chart-wrap {
            height: 260px;
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

            .grid {
                grid-template-columns: 1fr;
            }

            .card:has(#gpuChart) {
                grid-column: auto;
            }

            .wrap .row {
                grid-template-columns: 1fr;
            }

            .card.wide {
                max-width: 100%;
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
            margin-bottom: 22px;
            display: flex;
            flex-direction: column;
        }


        /* label */
        .row label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #cbd5e1;
            letter-spacing: .2px;
        }

        .row textarea {
            width: 100%;
            min-height: 70px;
            padding: 14px 14px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: linear-gradient(180deg, #020617, #020617);
            color: #f1f5f9;
            font-size: 14px;
            line-height: 1.5;
            resize: vertical;
            transition: .18s ease;
        }

        .row textarea::placeholder {
            color: #64748b;
        }

        .row textarea:focus {
            border-color: #38bdf8;
            box-shadow:
                0 0 0 2px rgba(56, 189, 248, .18),
                0 6px 18px rgba(0, 0, 0, .45);
            outline: none;
        }

        .row small {
            margin-top: 7px;
            font-size: 12px;
            color: #64748b;
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

        @media (max-width:600px) {

            /* headings */
            .card h2 {
                font-size: 16px;
            }

            /* textarea */
            textarea {
                font-size: 13px;
            }

            /* social buttons */
            .social-row {
                gap: 10px;
            }

            .social-btn {
                width: 50px;
                height: 50px;
            }

            .price {
                font-size: 20px;
            }

            th,
            td {
                font-size: 13px;
                padding: 8px 6px;
            }

            .cta {
                flex: 1;
                text-align: center;
            }

            .panel {
                grid-template-columns: 1fr;
            }
        }

        /* ===== PASSWORD PAGE REFINEMENT PATCH ===== */

        .password-form {
            width: 100%;
            max-width: 560px;
            margin-top: 6px;
        }

        .password-form .field {
            margin-bottom: 22px;
            display: flex;
            flex-direction: column;
        }

        .password-form label {
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #cbd5e1;
        }

        /* consistent input sizing */
        .password-form input {
            width: 100%;
            height: 46px;
            padding: 0 16px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: #020617;
            color: #f8fafc;
            font-size: 14px;
            transition: .18s;
        }

        /* focus */
        .password-form input:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow:
                0 0 0 2px rgba(56, 189, 248, .18),
                0 6px 18px rgba(0, 0, 0, .45);
        }

        /* wrapper */
        .pass-wrap {
            position: relative;
            width: 100%;
        }


        /* space for toggle text */
        .pass-wrap input {
            padding-right: 70px;
        }

        .pass-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            font-weight: 500;
            letter-spacing: .2px;
            cursor: pointer;
        }

        .pass-toggle:hover {
            color: #38bdf8;
        }

        .password-form .strength {
            width: 100%;
            overflow: hidden;
        }

        /* strength bar container */
        .strength {
            height: 10px;
            border-radius: 8px;
            margin-top: 12px;
            background: #020617;
        }

        /* strength fill animation */
        .strength-bar {
            border-radius: 8px;
        }

        .strength-bar {
            height: 100%;
            width: 0;
            transition: width .25s ease;
        }

        /* strength text */
        .strength-text {
            margin-top: 8px;
            font-size: 13px;
            letter-spacing: .2px;
        }

        /* submit button spacing */
        .password-form button {
            margin-top: 10px;
            width: 100%;
            max-width: 260px;
            align-self: center;
        }

        /* better divider line */
        .password-form .field:not(:last-of-type) {
            padding-bottom: 18px;
            border-bottom: 1px dashed rgba(255, 255, 255, .06);
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
            display: grid;
            grid-template-columns: 20px 1fr;
            column-gap: 12px;
            margin-top: 20px;
            align-items: start;
        }

        /* checkbox */
        .wrap .checkbox input {
            margin-top: 3px;
            width: 18px;
            height: 18px;
            accent-color: #38bdf8;
        }

        /* text block */
        .wrap .checkbox label {
            margin: 0;
            font-weight: 700;
            line-height: 1.45;
            cursor: pointer;
        }

        /* description under label */
        .wrap .checkbox .muted {
            margin-top: 4px;
            font-size: 13px;
            line-height: 1.45;
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
            tab-size: 4;
            letter-spacing: .2px;
            border-radius: 12px;
            background: #010409;
            border: 1px dashed rgba(255, 255, 255, .08);
            color: #94a3b8;
            font-size: 13px;
            line-height: 1.6;

            white-space: pre-wrap;
            /* allow wrapping */
            word-break: break-word;
            /* break long strings */
            overflow-wrap: anywhere;
            /* modern wrap support */
        }

        /* ===== CHART SIZE + READABILITY ===== */

        /* chart container */
        .chart-wrap {
            position: relative;
            height: 220px;
            margin-top: 6px;
        }

        .chart-wrap canvas {
            width: 100% !important;
            height: 100% !important;
        }


        /* headings easier to read */
        .card h3 {
            color: #f8fafc;
            text-shadow: 0 0 8px rgba(56, 189, 248, .15);
        }

        /* metrics row visibility */
        #lastUpdate,
        #lastCpu,
        #lastRam,
        #lastGpu,
        #lastIn,
        #lastOut {
            color: #ffffff;
            font-weight: 700;
        }

        form[enctype="multipart/form-data"] {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        /* restore label */
        form[enctype="multipart/form-data"]>label {
            font-weight: 600;
            color: #e2e8f0;
            text-align: center;
        }

        /* file input */
        form[enctype="multipart/form-data"]>input[type="file"] {
            width: 100%;
            max-width: 420px;
            padding: 12px;
            border-radius: 10px;
            border: 1px dashed var(--border);
            background: #020617;
            color: var(--muted);
            cursor: pointer;
            transition: .25s;
        }

        /* hover + focus */
        form[enctype="multipart/form-data"]>input[type="file"]:hover {
            border-color: var(--accent);
            background: rgba(56, 189, 248, .05);
        }

        form[enctype="multipart/form-data"]>input[type="file"]:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(56, 189, 248, .15);
        }

        /* restore button spacing only */
        form[enctype="multipart/form-data"]>.red-btn {
            margin-top: 6px;
            min-width: 200px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 26px;
        }

        /* price text */
        .price {
            font-size: 24px;
            font-weight: 700;
            color: #f8fafc;
            margin-top: 2px;
        }

        /* badge */
        .pill {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 999px;
            background: linear-gradient(90deg, #0ea5e9, #6366f1);
            color: #fff;
            white-space: nowrap;
        }

        /* tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        th {
            text-align: left;
            padding: 10px 8px;
            background: rgba(255, 255, 255, .04);
            border-bottom: 1px solid var(--border);
            color: #e2e8f0;
            font-weight: 600;
        }

        td {
            padding: 10px 8px;
            border-bottom: 1px dashed rgba(255, 255, 255, .06);
            color: #cbd5e1;
        }

        /* CTA buttons */
        .cta {
            display: inline-block;
            padding: 11px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: .25s;
        }

        .cta-primary {
            background: linear-gradient(90deg, #22c55e, #16a34a);
            color: #fff;
            box-shadow: 0 6px 20px rgba(34, 197, 94, .25);
        }

        .cta-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(34, 197, 94, .35);
        }

        .cta-ghost {
            border: 1px solid var(--border);
            color: var(--text);
        }

        .cta-ghost:hover {
            background: rgba(255, 255, 255, .05);
            border-color: rgba(255, 255, 255, .2);
        }

        /* benefits section spacing */
        .benefits {
            margin-bottom: 28px;
        }

        /* footer note */
        footer .muted {
            text-align: center;
            font-size: 14px;
        }

        /* ===== FULL WIDTH MODE FOR SINGLE CARD PAGES ===== */

        /* make wrapper span full grid */
        .grid>.wrap:has(.card.wide) {
            grid-column: 1 / -1;
        }

        /* full width card */
        .card.wide {
            width: 100%;
            max-width: 1200px;
            /* adjust max page width */
            margin: 0 auto;
            /* center */
            grid-column: 1/-1
        }

        /* center form layout nicely */
        .card.wide form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .card.wide .row {
            width: 100%;
            max-width: 100%;
            margin-left: 0;
            margin-right: 0;
        }

        /* labels flush left */
        .card.wide .row label {
            text-align: left;
            width: 100%;
        }

        /* textarea full width + proper inner spacing */
        .card.wide .row textarea {
            width: 100%;
            max-width: 100%;
            display: block;
        }

        /* helper text aligned left */
        .card.wide .row small {
            text-align: left;
        }

        /* keep inputs readable */
        .card.wide input,
        .card.wide select,
        .card.wide textarea {
            width: 100%;
        }

        /* center checkbox + links + buttons */
        .card.wide .checkbox,
        .card.wide .links,
        .card.wide .actions {
            justify-content: center;
            text-align: center;
        }

        /* center note box but keep text readable */
        .card.wide .note {
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            text-align: left;
        }

        /* ===== COLOR PANEL ADDON — COMPACT GRID VERSION ===== */

        .panel {
            margin-top: 20px;
            padding: 20px 22px 22px;
            border-radius: var(--radius);
            background: linear-gradient(180deg, #020617, #020617);
            border: 1px solid var(--border);
            box-shadow:
                inset 0 0 22px rgba(99, 102, 241, .05),
                0 10px 28px rgba(0, 0, 0, .55);
            position: relative;

            /* 2 controls per row */
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 22px;
        }

        /* heading spans full width */
        .panel h2 {
            grid-column: 1/-1;
            text-align: left;
            font-size: 16px;
            margin-bottom: 4px;
            color: #f8fafc;
            letter-spacing: .3px;
        }

        /* each slider block */
        .control {
            margin: 0;
            padding: 0;
        }

        /* label + value row */
        .control .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            font-size: 13px;
            color: var(--muted);
        }

        .control .row span:last-child {
            color: #fff;
            font-weight: 600;
            min-width: 42px;
            text-align: right;
        }

        /* slider track */
        .control input[type=range] {
            width: 100%;
            height: 5px;
            border-radius: 6px;
            background: linear-gradient(90deg, #0f172a, #1e293b);
            outline: none;
            appearance: none;
            border: 1px solid rgba(255, 255, 255, .06);
            transition: .2s;
        }

        /* hover glow */
        .control input[type=range]:hover {
            border-color: rgba(56, 189, 248, .4);
        }

        /* slider thumb */
        .control input[type=range]::-webkit-slider-thumb {
            appearance: none;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            cursor: pointer;
            border: none;
            box-shadow:
                0 0 0 3px rgba(56, 189, 248, .15),
                0 2px 8px rgba(0, 0, 0, .6);
            transition: .15s;
        }

        .control input[type=range]::-moz-range-thumb {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border: none;
            cursor: pointer;
        }

        /* active drag effect */
        .control input[type=range]:active::-webkit-slider-thumb {
            transform: scale(1.15);
        }

        /* remove divider lines */
        .control:not(:last-child) {
            border: none;
        }

        /* reset button layout */
        .panel-actions {
            grid-column: 1/-1;
            display: flex;
            justify-content: flex-end;
            margin-top: 6px;
        }

        .panel-actions button {
            padding: 7px 16px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: linear-gradient(180deg, #0f172a, #020617);
            color: var(--text);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
        }

        .panel-actions button:hover {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(56, 189, 248, .15);
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
        <a href="contact_us.php">Contact Us</a>
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