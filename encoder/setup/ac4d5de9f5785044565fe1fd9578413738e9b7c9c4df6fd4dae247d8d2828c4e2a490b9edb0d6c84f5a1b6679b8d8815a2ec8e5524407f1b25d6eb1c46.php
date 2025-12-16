<?php

$ethInterfaces = [];

foreach (scandir('/sys/class/net') as $iface) {
    if ($iface === '.' || $iface === '..' || $iface === 'lo') {
        continue;
    }

    $base = "/sys/class/net/$iface";

    // Must be physical hardware
    if (!is_dir("$base/device")) {
        continue;
    }

    // Exclude wireless
    if (is_dir("$base/wireless")) {
        continue;
    }

    // Must be Ethernet
    $type = @file_get_contents("$base/type");
    if (trim($type) !== '1') {
        continue;
    }

    $ethInterfaces[] = $iface;
}

$ethInterface = "";

$ethInterfaces
    ? $ethInterface = $ethInterfaces[0]
    : 'No physical wired Ethernet NIC found';

