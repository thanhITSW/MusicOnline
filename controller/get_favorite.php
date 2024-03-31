<?php
    session_start();

    header('Content-Type: application/json');

    require_once('../config_model/music_db.php');

    if(!isset($_SESSION['user'])) {
        $res = array(
            'favorite' => []
        );
    
        echo json_encode($res);
    }
    else {
        $favorite = get_favorite($_SESSION['user']);

        $res = array(
            'favorite' => $favorite
        );
    
        echo json_encode($res);
    }
?>