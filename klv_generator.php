<?php

// credits teosleep [https://github.com/teosleep]
// credits zumor

function gen_hex($length = 16) {
    $bytes = random_bytes($length);
    return strtoupper(bin2hex($bytes));
}

function proton_hash($data, $length = 0) {
    $hash = 0x55555555;
    $length = $length > 0 ? $length : strlen($data);

    for ($i = 0; $i < $length; $i++) {
        $hash = (($hash >> 27) + ($hash << 5) + ord($data[$i])) & 0xFFFFFFFF;
    }

    return $hash;
}

function sha256($str) {
    return hash('sha256', $str);
}

function create_klv($game_version, $protocol, $hash, $rid) {
    $salts = [
        '198c4213effdbeb93ca64ea73c1f505f',
        '82a2e2940dd1b100f0d41d23b0bb6e4d',
        'c64f7f09cdd0c682e730d2f936f36ac2',
        '27d8da6190880ce95591215f2c9976a6'
    ];

    return sha256(
        sha256((string)$game_version) . 
        $salts[0] . 
        sha256((string)$hash) . 
        $salts[1] . 
        sha256((string)$protocol) . 
        $salts[2] . 
        sha256($rid) . 
        $salts[3]
    );
}

$game_version = $_GET['gt_version'] ?? 4.26;
$protocol = $_GET['protocol'] ?? 191;
$rid = $_GET['rid'] ?? gen_hex();

if (!empty($_GET['device_id'])) {
    $hash = proton_hash($_GET['device_id'] . "RT");
} elseif (!empty($_GET['hash'])) {
    $hash = $_GET['hash'];
} else {
    $hash = proton_hash(gen_hex() . "RT");
}

$klv = create_klv($game_version, $protocol, $hash, $rid);

echo "rid|" . $rid . PHP_EOL;
echo "hash|" . $hash . PHP_EOL;
echo "klv|" . $klv . PHP_EOL;

?>
