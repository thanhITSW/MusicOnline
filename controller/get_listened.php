<?php
    header('Content-Type: application/json');

    require_once('../config_model/music_db.php');

    $listened = get_listened();

    $res = array(
        'listened' => $listened
    );

    echo json_encode($res);
?>