<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home music</title>
  <!-- it is connected to stylesheets -->
  <link rel="stylesheet" type="text/css" href="style.css"><!-- this is the main stylesheet -->
  <!-- this script is just for font awesome fonts -->
  <script src="https://kit.fontawesome.com/2d9b67a497.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
  <!-- top navigation bar -->
  <nav class="navigation-bar">
    <!--This div contains the logo and title of the page-->
    <div class="title-combo">
      <div class="website-logo">
        <img src="assets/logo.jpg">
      </div>

      <div class="website-name">
        <h2>
          TTP Music Hub
        </h2>
        <h6>
          Music Player
        </h6>
      </div>
    </div>

    <!-- this is the animated favourites text -->
    <div id="play-list" style="color:aqua" class="favs">
      <h4>
        Danh sách nhạc
      </h4>
    </div>

    <div id="favorite-list" style="color:palevioletred" class="favs">
      <h4>
        Mục yêu thích
      </h4>
    </div>

    <div id="listened-list" style="color:#fcb045" class="favs">
      <h4>
        Bài hát nghe gần đây
      </h4>
    </div>

    <!-- this is for search bar -->
    <div class="search-bar">

      <div>
        <!-- magnifying glass- search icon -->
        <!-- div:nth-child(1) -->
        <i class="fas fa-search search-ico"></i>
      </div>

      <!-- Input -->
      <input id="search" type="text" name="search" placeholder="Tìm kiếm">
    </div>

    <!-- Login status -->
    <div class="login-form">
      <?php
      if (isset($_SESSION['name'])) {
        echo "<span>$_SESSION[name]</span></br>";
        echo '<button><a href="logout.php">Đăng xuất</a></button>';
        echo "<input style='display: none' type='text' value=$_SESSION[user] id='username'>";
      } else {
        echo '<button><a href="login.php">Đăng nhập</a></button>';
        echo "<input style='display: none' type='text' value='' id='username'>";
      }
      ?>

    </div>
  </nav>

  <main>

    <!-- here starts the section 1 of our page -->
    <aside class="aside section-1">

      <!-- this is the section heading part. the heading will be static -->
      <div class="heading">
        <h1 id="title-list" style="margin-left: 10px;">Danh sách nhạc</h1>
      </div>

      <!-- this is the content of the playlist. it will be dynamic. -->
      <div class="playlist-content">
        <!-- first playlist item -->

      </div>
    </aside>

    <!-- aside section 2 -->
    <aside class="aside section-2">
      <div class="outer-carousel">
        <div class="animate__animated animate__flipInX carousel" style="max-width:2048px">
          <!-- these are the 3 images in the carousel -->
          <img class="mySlides" src="assets/img/song4.jpg" width="2048px" height="1268px">
          <img class="mySlides" src="assets/img/song8.jpg" width="2048px" height="1268px">
          <img class="mySlides" src="assets/img/song10.jpg" width="2048px" height="1268px">
          <img class="mySlides" src="assets/img/song12.jpg" width="2048px" height="1268px">
          <img class="mySlides" src="assets/img/song22.jpg" width="2048px" height="1268px">
        </div>
      </div>

      <div class="language english">
        <h1 class="language-heading" style="margin-bottom: -11px;">
        <div style="display:none" id="lyricsForm">
          <h2 id="name-song">Tên bài hát</h2>
          <p id="lyrics-song"></p>
          <button id="closeLyrics" class="btn btn-success px-5">Ẩn</button>
      </div>
    </div>


      <!-- Kết Quả Tìm Kiếm -->
      <div id="result-show" style="display: none;" class="language english">
        <h1 class="language-heading" style="margin-bottom: -11px;">
        Kết Quả Tìm Kiếm
        </h1>
          <!-- contents of latest english -->
        <div id="result" class="language-content">
            
        </div>
      </div>

      <!-- Bài hát thịnh hành -->
      <div class="language english">
        <!-- latest english section -->
        <h1 class="language-heading" style="margin-bottom: -11px;">
          Bài Hát Thịnh Hành
        </h1>
        <!-- contents of latest english -->
        <div id="trending" class="language-content">
          <!-- <div>
            <img src="media/nangluongtichcuc/thichemhoinhieu.jpg">
            <p>Thích em hơi nhiều </p>
          </div> -->
        </div>
      </div>

      <!-- Nghệ Sĩ Thịnh Hành -->
      <div class="language english">
        <!-- latest english section -->
        <h1 class="language-heading" style="margin-bottom: -11px;">
          Nghệ Sĩ Thịnh Hành
        </h1>
        <!-- contents of latest english -->
        <div id="singer-trending" class="language-content">
          <!-- <div>
            <img src="media/nghesithinhhanh/vu.jpg" width="100%">
            <p>Vũ</p>
          </div> -->
        </div>
      </div>

      <!-- Album -->
      <div id="album-show" style="display: none;" class="language english">
        <!-- latest english section -->
        <h1 class="language-heading" style="margin-bottom: -11px;">
          Album
        </h1>
        <!-- contents of latest english -->
        <div id="album" class="language-content">
          
        </div>
      </div>
    </aside>

  </main>

  <!-- Footer part -->
  <footer style="display: none">
    <div class="active-song-description">

      <!-- song image -->
      <div>
        <img id="song-image" src="">
      </div>

      <!-- song name and singer -->
      <div class="song-desc">
        <div id="name">

        </div>
        <div id="singer">

        </div>
      </div>
    </div>

    <!-- these are the main player controls -->
    <div class="player">
      <div class="controls">
        <button class="random"><i class="fas fa-random"></i></button>
        <button class="back"><i class="fas fa-step-backward"></i></button>
        <button class="play-song"><i class="fas fa-play"></i></button>
        <button class="forward"><i class="fas fa-step-forward"></i></button>
        <button class="redo"><i class="fas fa-redo"></i></button>
      </div>

      <!-- this is the slider -->
      <div id="slider">
        <!-- current time -->
        <div id="current" class="time">
          1:39
        </div>
        <div class="slidecontainer">
          <input type="range" min="0" max="100" value="0" class="slider" id="progress">
        </div>
        <!-- total time -->
        <div id="total" class="time">
          4:44
        </div>
      </div>
    </div>

    <!-- other icons including the volume slider and all -->
    <div class="extras">
      <div id="favorite">
        <input style="display: none" type="number">
        <i class="fa fa-heart"></i>
      </div>
      <div>
        <button id="lyrics">Lời bài hát</button>
      </div>
      <div>
        <a id="download" href="assets/music/song1.mp3" download="song1.mp3">Tải</a>
      </div>

      <div>
        <i class="fa fa-volume-up"></i>
      </div>
      <div class="slidecontainer" style="width:30%;">
        <input type="range" min="0" max="1" step="0.1" value="1" class="slider" id="volume" style="margin-top:0px;">
      </div>
      <div>
        <i id="close" class="fa fa-times"></i>
      </div>

      <audio id="audio" src=""></audio>
    </div>
  </footer>

  <script>
    var myIndex = 0;
    carousel();

    function carousel() {
      var i;
      var x = document.getElementsByClassName("mySlides");
      for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
      }
      myIndex++;
      if (myIndex > x.length) { myIndex = 1 }
      x[myIndex - 1].style.display = "block";
      setTimeout(carousel, 2000);
    }
  </script>

  <script src="script.js"></script>

</body>

</html>