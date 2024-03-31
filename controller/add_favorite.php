<?php
    session_start();

    require_once('../config_model/music_db.php');

    $update = 'no_change';

    if(isset($_SESSION['user'])) {
        if(isset($_GET['position'])) {
            $position = $_GET['position'];
            $update = add_favorite($_SESSION['user'], $position);
        }
    }
    
    $res = array(
        'update' => $update
    );

    echo json_encode($res);
?>