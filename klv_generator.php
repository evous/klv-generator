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
        "13c93f386db9da3e00dda16d770b0c83",
        "6b1c01f9128a62a2c97b1a0da4612168",
        "3402d278d8519a522c94d122e98e2e49",
        "ba95613bc0fd94a9d89c5919670e7d5d"
        // credits zumor
    );

    $checksum_str = $game_version . $salts[0] . $protocol . $salts[1] . $hash . $salts[2] . $rid . $salts[3];
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

    return $hash;
}

$game_version = 4.25; // growtopia current version.
$protocol = 191; // if growtopia version change protocol will change too.
$rid = generate_rid();
$hash = proton_hash(generate_device_id() . "RT");
$klv = create_klv($game_version, $protocol, $hash, $rid);

echo "rid|" . $rid . PHP_EOL;
echo "hash|" . $hash . PHP_EOL;
echo "klv|" . $klv . PHP_EOL;

?>
