<?php
require 'apiCall.php';
require_once 'api/DB.php';
require_once 'api/DNS.php';
require_once 'config.php';
//$DB= new DB('10.223.29.27', 'certwatch', 'certwatch', 'certwatch');
$f = "";
function call($filter)
{
    $f = $filter;
    return API_CALL("https://crt.sh/json?q=$filter&dir=^&sort=1&exclude=expired&n=30");
}

function postToDiscord($message)
{
    $data = array("content" => $message, "username" => "CertBot");
    $curl = curl_init(DISCORD_HOOK);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($curl);
}

function parseIssuer($certificateIssuer)
{
    $issuer = explode(", ", $certificateIssuer);
    foreach ($issuer as $e) {
        $st = substr($e, 0, 2);
        if ($st == "CN")
            return $e;
    }
    $example = <<<EXA
    C=HU,
    L=Budapest,
    O=Microsec Ltd., 
    CN=e-Szigno SSL CA 2014, 
    emailAddress=info@e-szigno.hu
EXA;
}

function parse($json)
{
    $doms = [];
    $object = json_decode($json);
    print('[' . date("Y-m-d H:s") . "] Checking started for " . sizeof($object) . " certificate.\n");
    foreach ($object as $cert) {
        $cacheFile = __DIR__ . "/cache/$cert->min_cert_id.json";
        if (!file_exists($cacheFile)) {
            file_put_contents($cacheFile, json_encode($cert));
            //file_get_contents(PEM_STORE."/$cert->name_value.pem",);
            print('[' . date("Y-m-d H:s") . "]$cert->name_value\n");
            // $dns = DNS::Query($cert->name_value);
            $cn = parseIssuer($cert->issuer_name);
            $doms[] = "**[$cert->name_value](https://crt.sh/?id=$cert->min_cert_id)**($cn)\n";
            //  $DB->query("INSERT INTO certificates VALUES(NULL,$cert->min_cert_id,$cert->issuer_ca_id,$cert->issuer_name,$cert->min_entry_timestamp,$cert->not_before,$cert->not_after);");


        }

    }
    if (sizeof($doms) > 0) {
        $nd = implode(PHP_EOL, $doms);
        $msg = <<<MSG
==CERTWATCH==
New domains:
$nd
MSG;

        postToDiscord($msg);
    }
}

