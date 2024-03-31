<?php
    define('HOST', 'localhost');
    define('USER', 'root');
    define('PASS', '');
    define('DB', 'music_web');

    function get_connection()
    {
        $conn = new mysqli(HOST, USER, PASS, DB);
        if ($conn->connect_errno) {
            die('Connect failed with message: ' . $conn->connect_error);
        }
        $conn->set_charset("utf8");
        return $conn;
    }
?>