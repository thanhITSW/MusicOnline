<?php
    require_once('../config_model/music_db.php');

    if(isset($_GET['position'])) {
        $position = $_GET['position'];
        add_listened($position);
    }
?>