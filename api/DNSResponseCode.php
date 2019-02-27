<?php
/**
 * Created by PhpStorm.
 * User: d3n
 * Date: 2019.02.15.
 * Time: 22:21
 */

class DNSResponseCode
{
    static public function Get($code)
    {
        $codes = [
            0 => "NoError",
            1 => "FormErr",
            2 => "ServFail",
            3 => "NXDomain",
            4 => "NotImpl",
            5 => "Refused",
            6 => "YXDomain(Name Exists when it should not)",
            7 => "YXRRSet(RR Set Exists when it should not)",
            8 => "NXRRSet(RR Set that should exist does not)",
            9 => "NotAuth(Server Not Authoritative for zone/Not Authorized)",
            10 => "NotZone(Name not contained in zone)",
            11 => "DSOTYPENI(DSO-TYPE Not Implemented)",
            16 => "BADVERS(Bad OPT Version)/BADSIG(TSIG Signature Failure)",
            17 => "BADKEY(Key not recognized)",
            18 => "BADTIME(Signature out of time window)",
            19 => "BADMODE(Bad TKEY Mode)",
            20 => "BADNAME(Duplicate key name)",
            21 => "BADALG(Algorithm not supported)",
            22 => "BADTRUNC(Bad Truncation)",
            23 => "BADCOOKIE(Bad/missing Server Cookie)"
        ];
        return (object)[
            "Code"=>$code,
            "Description"=> $codes[$code],
            "RequestSuccessed"=>($code==0)?true:false
        ];

    }
}