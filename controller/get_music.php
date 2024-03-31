<?php
    header('Content-Type: application/json');

    require_once('../config_model/music_db.php');

    $songs = get_songs();

    $trendingSongs = get_trending_songs();

    $singers = get_singers();

    $songsBySinger = [];

    $songResult = null;

    if(isset($_GET['id'])) {
        $id_singer = $_GET['id'];
        $songsBySinger = get_songs_by_singer($id_singer);
    }

    if(isset($_GET['name'])) {
        $name = $_GET['name'];
        $songResult = get_song_result($name);
    }

    $res = array(
        'songs' => $songs,
        'trendingSongs' => $trendingSongs,
        'singers' => $singers,
        'songsBySinger' => $songsBySinger,
        'songResult' => $songResult
    );

    echo json_encode($res);
?>