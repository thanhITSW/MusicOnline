<?php
    require_once('db.php');

    function get_songs() {
        $conn = get_connection();
        $sql = "select * from music";

        $pointer = $conn->query($sql);

        $items = [];

        for($i = 1; $i <= $pointer->num_rows; $i ++) {
            $items[] = $pointer->fetch_assoc();
        }

        return $items;
    }

    function get_trending_songs() {
        $conn = get_connection();
        $sql = "select T.position, M.name, M.image from trendingmusic as T join music as M on T.position = M.position";

        $pointer = $conn->query($sql);

        $items = [];

        for($i = 1; $i <= $pointer->num_rows; $i ++) {
            $items[] = $pointer->fetch_assoc();
        }

        return $items;
    }
    
    function get_singers() {
        $conn = get_connection();
        $sql = "select * from singer";

        $pointer = $conn->query($sql);

        $items = [];

        for($i = 1; $i <= $pointer->num_rows; $i ++) {
            $items[] = $pointer->fetch_assoc();
        }

        return $items;
    }

    function get_songs_by_singer($id_singer) {
        $conn = get_connection();
        $sql = "select S.name as singer, A.* from (select SS.id_singer, M.* from `singer_songs`as SS JOIN `music` as M ON SS.position = M.position) 
        as A JOIN `singer` as S ON A.id_singer = S.id_singer WHERE S.id_singer = ?";

        $stm = $conn->prepare($sql);
        $stm->bind_param('i', $id_singer);

        $stm->execute();
        $pointer = $stm->get_result();

        $items = [];

        for($i = 1; $i <= $pointer->num_rows; $i ++) {
            $items[] = $pointer->fetch_assoc();
        }

        return $items;
    }

    function get_song_result($name) {
        if($name == '') {
            return [];
        }

        $conn = get_connection();
        $sql = "select * from music where name like ?";

        $name = '%' . $name . '%';

        $stm = $conn->prepare($sql);
        $stm->bind_param('s', $name);

        $stm->execute();
        $pointer = $stm->get_result();

        $items = [];

        for($i = 1; $i <= $pointer->num_rows; $i ++) {
            $items[] = $pointer->fetch_assoc();
        }

        if(count($items) != 0) {
            return $items;
        }
        
        $sql = "select * from music where singer like ?";

        $stm = $conn->prepare($sql);
        $stm->bind_param('s', $name);

        $stm->execute();
        $pointer = $stm->get_result();

        for($i = 1; $i <= $pointer->num_rows; $i ++) {
            $items[] = $pointer->fetch_assoc();
        }

        return $items;
    }

    function get_listened() {
        $conn = get_connection();
        $sql = "select distinct M.* from listened as L join music as M on L.position = M.position 
        order by L.order_number desc";

        $pointer = $conn->query($sql);

        $items = [];

        for($i = 1; $i <= $pointer->num_rows; $i ++) {
            $items[] = $pointer->fetch_assoc();
        }

        return $items;
    }

    function add_listened($position) {
        $conn = get_connection();
        
        $sql = "select count(*) from listened";

        $pointer = $conn->query($sql);

        $order_number = $pointer->fetch_assoc()['count(*)'] + 1;

        $sql = "insert into `listened`(order_number, position) values (?,?)";

        $stm = $conn->prepare($sql);
        $stm->bind_param('ii', $order_number, $position);

        $stm->execute();
    }

    function reset_listened() {
        $conn = get_connection();
        $sql = "delete from listened";

        $conn->query($sql);
    }

    function get_favorite($username) {
        $conn = get_connection();
        $sql = "select M.* from `favorite` as F join `music` as M on F.position = M.position where F.username = ?";

        $stm = $conn->prepare($sql);
        $stm->bind_param('s', $username);

        $stm->execute();
        $pointer = $stm->get_result();

        $items = [];

        for($i = 1; $i <= $pointer->num_rows; $i ++) {
            $items[] = $pointer->fetch_assoc();
        }

        return $items;
    }

    function add_favorite($username, $position) {
        $conn = get_connection();

        $sql = "select count(*) from favorite where username = ? and position = ?";

        $stm = $conn->prepare($sql);
        $stm->bind_param('si', $username, $position);

        $stm->execute();
        $pointer = $stm->get_result();

        $is_exist = $pointer->fetch_assoc()['count(*)'];

        $sql = "insert into favorite (username, position) values (?,?)";
        $update = 'add';

        if($is_exist) {
            $sql = "delete from favorite where username = ? and position = ?";
            $update = 'delete';
        }

        $stm = $conn->prepare($sql);
        $stm->bind_param('si', $username, $position);

        $stm->execute();
    
        return $update;
    }
?>