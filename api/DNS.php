<?php
/**
 * Created by PhpStorm.
 * User: d3n
 * Date: 2019.02.14.
 * Time: 19:15
 */
include_once 'DNSResponseCode.php';

class DNS
{
    public static function GeoIP($ip)
    {
        $ipInfo = self::_callIpInfo($ip);
        return $ipInfo;
    }

    private function SOA($domain)
    {
        if ($domain[0] == "*") {
            //wildcard domain, remove the 2 first chars
            $domain = substr($domain, 2);
            $dnsResponse = self::_callDNS($domain, "SOA");
        } else {
            $dnsResponse = self::_callDNS($domain, "SOA");


        }
        //example response:{"Status": 0,"TC": false,"RD": true, "RA": true, "AD": false,"CD": false,"Question":[{"name": "bv.gov.hu.", "type": 6}],"Answer":[{"name": "bv.gov.hu.", "type": 6, "TTL": 7200, "data": "adns0.gov.hu. hostmaster.nisz.hu. 2018112803 43200 10800 2592000 86400"}]}
        $response = $dnsResponse;
        file_put_contents(__DIR__ . "/soa.json", json_encode($response));
        $SOARecord = self::parseSOA($response->Authority[0]->data);
        return (object)[
            "NS" => $SOARecord->AuthorativeDNSServer,
            "MX" => $SOARecord->AuthorativeMXServer
        ];
    }

    private function parseSOA($recordData)
    {
        //"data": "adns0.gov.hu. hostmaster.nisz.hu. 2018112803 43200 10800 2592000 86400"
        print("[!] $recordData\n");
        $recordArray = explode(' ', $recordData);
        return (object)[
            "AuthorativeDNSServer" => $recordArray[0],
            "AuthorativeMXServer" => $recordArray[1],
            "Serial" => $recordArray[2],
            "Refresh" => $recordArray[3],
            "Retry" => $recordArray[4],
            "Expire" => $recordArray[5],
            "Minimum" => $recordArray[6]
        ];
    }

    public static function Query($domain, $type = "A")
    {
        if ($domain[0] == "*") {
            //wildcard domain, remove the 2 first chars
            $domain = substr($domain, 2);
            $dnsResponse = self::_callDNS($domain, $type);
        } else {
            $dnsResponse = self::_callDNS($domain, $type);

        }
        $object = $dnsResponse;
        $code = DNSResponseCode::Get($object->Status);
        if ($code->RequestSuccessed) {
            $soa = self::SOA($domain);
            $serverIP = $object->Answer[0]->data;
            $ipInfo = self::GeoIP($serverIP);
            return (object)[
                'Country' => $ipInfo->country,
                'Org' => $ipInfo->org,
                'IP' => $serverIP,
                "DNS" => $soa->NS,
                "MX" => $soa->MX,
                "Success" => true,
            ];
        } else {
            return (object)[
                'Country' => $code->Description,
                'Org' => $code->Description,
                'IP' => $code->Description,
                "DNS" => $code->Description,
                "MX" => $code->Description,
                "Success" => false,
            ];
        }
    }

    private static function _callIpInfo($ip)
    {
        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL => "https://ipinfo.io/$ip/json?token=5650c6afa3d820",
            CURLOPT_HTTPHEADER => [
                'Accept: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER => false,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS => 10,     // stop after 10 redirects
            CURLOPT_ENCODING => "",     // handle compressed
            CURLOPT_USERAGENT => "CRTMonitor/GeoIPLookup", // name of client
            CURLOPT_AUTOREFERER => true,   // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
            CURLOPT_TIMEOUT => 120,    // time-out on response
        );
        curl_setopt_array($ch, $curlConfig);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

    private static function _callDNS($data, $type)
    {
        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL => "https://cloudflare-dns.com/dns-query?name=$data&type=$type",
            CURLOPT_HTTPHEADER => [
                'Accept: application/dns-json'
            ],
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER => false,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS => 10,     // stop after 10 redirects
            CURLOPT_ENCODING => "",     // handle compressed
            CURLOPT_USERAGENT => "CRTMonitor/dnsLookup", // name of client
            CURLOPT_AUTOREFERER => true,   // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
            CURLOPT_TIMEOUT => 120,    // time-out on response
        );
        curl_setopt_array($ch, $curlConfig);
        $result = curl_exec($ch);
        curl_close($ch);
        $obj = json_decode($result);

        return $obj;
    }
}