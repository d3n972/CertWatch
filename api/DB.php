<?php
/**
 * Created by PhpStorm.
 * User: d3n
 * Date: 2019.02.15.
 * Time: 21:31
 */
Class DB
{
    private $mysqli;
    public function __construct($host, $user, $pass, $db, $charset=NULL)
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->mysqli = new mysqli($host, $user, $pass, $db);
     //   $this->mysqli->set_charset($charset);
        return $this;
    }

    public function Query($host, $user, $pass, $db,$sql, $params, $types = "")
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $con = new mysqli($host, $user, $pass, $db);
        $types = $types ?: str_repeat("s", count($params));
        $stmt = $con->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt;
    }
}