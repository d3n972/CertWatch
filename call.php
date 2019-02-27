<?php
include 'responseParser.php';
$watchList = [
    "%25.gov.hu",
    "%25.telekom.hu",
    "%25.vodafone.hu",
    "%25.nisz.hu",
    "%25.otp.hu",
    "%25.telenor.hu",
    "%25.live.com",
    "%25.daum.net",
    "%25.kbs.co.kr",
    "%25.bbc.co.uk",
    "%25.xip.io",
    "%25.nip.io",
    "%25.gov.bt"
];
$newdomains = [];
foreach ($watchList as $dom) {
    $p = call($dom);
    print("[ + ] Checking: " . $dom . "\n");
    $ret = parse($p["content"]);
    if ($ret)
        $newdomains[] = $ret;
    sleep(2);
}
