<?php

// credits teosleep [https://github.com/teosleep]

function get_md5_checksum($str) {
    $md_value = md5($str, true);

    $hex_value = "";
    for ($i = 0; $i < strlen($md_value); ++$i) {
        $hex_value .= sprintf("%02X", ord($md_value[$i]));
    }

    return $hex_value;
}

function create_klv($game_version, $protocol, $hash, $rid) {
    $salts = array(
        "0b02ea1d8610bab98fbc1d574e5156f3",
        "b414b94c3279a2099bd817ba3a025cfc",
        "bf102589b28a8cc3017cba9aec1306f5",
        "dded9b27d5ce7f8c8ceb1c9ba25f378d"
    );

    $checksum_str = $salts[0] . $game_version . $salts[1] . $hash . $salts[2] . $rid . $salts[3] . $protocol;
    return get_md5_checksum($checksum_str);
}

function generate_rid() {
    $rid_str = "";

    for ($i = 0; $i < 16; $i++) {
        $rid_str .= sprintf("%02X", mt_rand(0, 255));
    }

    return strtoupper($rid_str);
}

function generate_device_id($length = 16) {
    $bytes = random_bytes($length);

    $hexString = bin2hex($bytes);

    return strtoupper($hexString);
}

function proton_hash($data, $length = 0) {
    $hash = 0x55555555;

    if (!empty($data)) {
        if ($length > 0) {
            for ($i = 0; $i < $length; $i++) {
                $hash = (($hash >> 27) + ($hash << 5) + ord($data[$i])) & 0xFFFFFFFF;
            }
        } else {
            $length = strlen($data);
            for ($i = 0; $i < $length; $i++) {
                $hash = (($hash >> 27) + ($hash << 5) + ord($data[$i])) & 0xFFFFFFFF;
            }
        }
    }

    if ($hash >= 0x80000000) {
        $hash -= 0x100000000;
    }

    return $hash;
}

$game_version = 4.23; // growtopia current version.
$protocol = 189; // if growtopia version change protocol will change too.
$rid = generate_rid();
$hash = proton_hash(generate_device_id() . "RT");
$klv = create_klv($game_version, $protocol, $hash, $rid);

echo "rid|" . $rid . PHP_EOL;
echo "hash|" . $hash . PHP_EOL;
echo "klv|" . $klv . PHP_EOL;

?>
