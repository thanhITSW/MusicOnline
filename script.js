// load and start web music
loadMusic();

function loadMusic() {
    fetch('http://localhost/controller/get_music.php')
    .then(res => res.json())
    .then(data => {
        startWebMusic(data)
    })
    .catch(error => console.log(error));
}

function startWebMusic(music) {
    //head
    const search = document.getElementById('search');
    const playlist = document.getElementById('play-list');
    const favoritelist = document.getElementById('favorite-list');
    const listenedlist = document.getElementById('listened-list');
    const username = document.getElementById('username');

    //favorite
    const heartFooter = document.getElementById('favorite');

    //main
    const aside2 = document.getElementsByTagName('aside')[1];
    const name_song = document.getElementById('name-song');
    const lyrics_song = document.getElementById('lyrics-song');

    //list songs
    const titleList = document.getElementById('title-list');
    const playlistContent = document.getElementsByClassName('playlist-content')[0];
    const resultShow = document.getElementById('result-show');
    const result = document.getElementById('result');
    const trending = document.getElementById('trending');
    const singerTrending = document.getElementById('singer-trending');
    const albumShow = document.getElementById('album-show');
    const album = document.getElementById('album');

    //footer
    const imageSong = document.getElementById('song-image');
    const nameSong = document.getElementById('name');
    const singerSong = document.getElementById('singer');
    const audioSong = document.getElementById('audio');

    //player
    const footer = document.getElementsByTagName('footer')[0];
    const randomBtn = document.getElementsByClassName('random')[0];
    const backBtn = document.getElementsByClassName('back')[0];
    const playSongBtn = document.getElementsByClassName('play-song')[0];
    const forwardBtn = document.getElementsByClassName('forward')[0];
    const redoBtn = document.getElementsByClassName('redo')[0];
    const progress = document.getElementById('progress')
    const current = document.getElementById('current');
    const total = document.getElementById('total');
    const volume = document.getElementById('volume');
    const close = document.getElementById('close');
    const showLyricsBtn = document.getElementById('lyrics');
    const lyrics = document.getElementById('lyricsForm');
    const closeLyricsBtn = document.getElementById('closeLyrics')
    const download = document.getElementById('download');

    const app = {

        currentIndex: -1,
        isPlaying: false,
        isRandom: false,
        isRedo: false,

        songs: music.songs,
        trendingSongs: music.trendingSongs,
        singers: music.singers,

        defineProperties: function() {
            Object.defineProperty(this, 'currentSong', {
                get: function() {
                    return this.songs[this.currentIndex];
                }
            })
        },

        render: function() {
            //show playlist
            playlist.click();

            //songs trending
            const listTrending = this.trendingSongs.map(song => {
                return `
                    <div>
                        <div class="play">
                            <input style="display: none" type="number" value="${song.position}">
                            <img src="${song.image}">
                        </div>
                        <p>${song.name}</p>
                    </div>
                `
            })
            trending.innerHTML = listTrending.join('\n');

            //singer trending
            const listSingers = this.singers.map(singer => {
                return `
                    <div class="display-album">
                        <input style="display: none" type="number" value="${singer.id_singer}">
                        <img src="${singer.image}" width="100%">
                        <span>${singer.name}</span>
                    </div>
                `
            })
            singerTrending.innerHTML = listSingers.join('\n');

            //play song when click
            const play = document.getElementsByClassName('play');
            app.playWhenClick(play);

            //display album when click singer
            const displayAlbum = document.getElementsByClassName('display-album');
            this.displayAlbumWhenClick(displayAlbum);
        },

        //listen and handle events
        handleEvents: function() {
            //click play list
            playlist.onclick = function() {
                titleList.innerHTML = 'Danh sách nhạc';
                app.showList(app.songs);
            }

            //click favorite list
            favoritelist.onclick = function() {
                if(username.value == '') {
                    alert('Vui lòng đăng nhập để xem danh muc yêu thích');
                }
                else {
                    titleList.innerHTML = 'Mục yêu thích';

                    fetch('http://localhost/controller/get_favorite.php')
                    .then(res => res.json())
                    .then(data => {
                        app.showList(data.favorite);
                    })
                    .catch(error => console.log(error));
                }
            }

            //click listened list
            listenedlist.onclick = function() {
                titleList.innerHTML = 'Danh sách nghe gần đây';
                
                fetch('http://localhost/controller/get_listened.php')
                .then(res => res.json())
                .then(data => {
                    app.showList(data.listened);
                })
                .catch(error => console.log(error));
            }

            //click play/pause
            playSongBtn.onclick = function() {
                if(app.isPlaying) {
                    audioSong.pause();
                }
                else {
                    audioSong.play();
                }
            }

            //play song
            audioSong.onplay = function() {
                app.isPlaying = true;
                playSongBtn.innerHTML =  `<i class="fas fa-pause"></i>`;
            }

            //pause song
            audioSong.onpause = function() {
                app.isPlaying = false;
                playSongBtn.innerHTML =  `<i class="fas fa-play"></i>`;
            }

            //update time
            audioSong.ontimeupdate = function() {
                if(audioSong.duration > 0) {
                    percentProgress = audioSong.currentTime / audioSong.duration * 100;
                    progress.value = percentProgress;
                    current.innerHTML = app.displayTime(audioSong.currentTime);
                    total.innerHTML = app.displayTime(audioSong.duration);
                }
            }

            //change time
            progress.onchange = function(e) {
                const changeProgress = audioSong.duration / 100 * e.target.value;
                audioSong.currentTime = changeProgress;
                current.innerHTML = app.displayTime(audioSong.currentTime);
                total.innerHTML = app.displayTime(audioSong.duration);
            }

            //next song
            forwardBtn.onclick = function() {
                if(app.isRandom) {
                    app.randomSong();
                }
                else {
                    app.nextSong();
                }
                audioSong.play();
            }

            //prev song
            backBtn.onclick = function() {
                if(app.isRandom) {
                    app.randomSong();
                }
                else {
                    app.prevSong();
                }
                audioSong.play();
            }

            //random song
            randomBtn.onclick = function() {
                app.isRandom = !app.isRandom;
                if(app.isRandom) {
                    randomBtn.style.color = "red";
                }
                else {
                    randomBtn.style.color = "white";
                }
            }

            //redo song
            redoBtn.onclick = function() {
                app.isRedo = !app.isRedo;
                if(app.isRedo) {
                    redoBtn.style.color = "red";
                }
                else {
                    redoBtn.style.color = "white";
                }
            }

            //end song
            audioSong.onended = function() {
                if(app.isRedo) {
                    audioSong.play();
                }
                else {
                    forwardBtn.click();
                }
            }

            //change volume
            volume.onchange = function(e) {
                audioSong.volume = e.target.value;
            }

            //close song
            close.onclick = function() {
                app.closeSong();
            }

            //search song
            search.oninput = function() {
                app.suggest(this.value);
            }

            //add song to favorite
            heartFooter.onclick = function() {
                if(username.value == '') {
                    alert('Vui lòng đăng nhập để thêm bài hát yêu thích');
                }
                else {
                    const position = this.children[0].value;
                    app.addFavorite(position);
    
                    const iconHeart = this.children[1];
                    currentColor = iconHeart.style.color;
    
                    if(currentColor !== 'red') {
                        iconHeart.style.color = 'red';
                    }
                    else {
                        iconHeart.style.color = 'white';
                    }
                }
            }

            showLyricsBtn.onclick = function() {
                lyrics.style.display = "";
                aside2.scrollTo(0, 400);
            }

            closeLyricsBtn.onclick = function() {
                lyrics.style.display = "none";
            }
        },

        //load song
        loadCurrentSong: function() {
            footer.style.display = "";
            imageSong.src = this.currentSong.image;
            nameSong.innerHTML = this.currentSong.name;
            singerSong.innerHTML = this.currentSong.singer;
            audioSong.src = this.currentSong.path;
            total.innerHTML = '';
            current.innerHTML = '0:00';

            name_song.innerHTML = this.currentSong.name;
            const array_lyrics = this.currentSong.lyric.split('\r\n');
            // console.log(array_lyrics);
            lyrics_song.innerHTML = array_lyrics.join('</br>')

            const position = this.currentSong.position;
            this.addListened(position);
            heartFooter.children[0].value = position;

            // download music
            download.href = this.currentSong.path;
            download.download = this.currentSong.name + '_' + this.currentSong.singer;

            //heart color in footer
            fetch('http://localhost/controller/get_favorite.php')
            .then(res => res.json())
            .then(data => {
                const favorite = data.favorite;

                let check = false;
                for (var i = 0; i < favorite.length; i++) {
                    if(favorite[i].position == position) {
                        heartFooter.children[1].style.color = 'red';
                        check = true;
                        break;
                    }
                }
                if(!check) {
                    heartFooter.children[1].style.color = 'white';
                }
            })
            .catch(error => console.log(error));
        },

        //show list music
        showList: function(list) {

            fetch('http://localhost/controller/get_favorite.php')
            .then(res => res.json())
            .then(data => {
                const favorite = data.favorite;

                const listItem = list.map(song => {
                    let color = 'white';


                    for (var i = 0; i < favorite.length; i++) {
                        
                        if(favorite[i].position == song.position) {
                            //heart color in list
                            color = 'red';

                            break;
                        }
                    }

                    return `
                        <div class=playlist-item>
                            <div class="left-content">
                                <div class="coverer">
                                    <img src=${song.image}>
                                    <div class="play_ play-button1">
                                        <input style="display: none" type="number" value="${song.position}">
                                        <button class="fas fa-play" aria-hidden="true"></button>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        ${song.name}
                                    </div>
                                    <p>
                                        ${song.singer}
                                    </p>
                                </div>
                            </div>
                            <div class="right-content favorite">
                                <input style="display: none" type="number" value="${song.position}">
                                <i class="fa fa-heart" style="color: ${color}"></i>
                            </div>
                        </div>
                    `
                })
                playlistContent.innerHTML = listItem.join('\n');
    
                //play song when click
                const play = document.getElementsByClassName('play_');
                app.playWhenClick(play);
    
                //add favorite when click
                const heart = document.getElementsByClassName('favorite');
                this.addFavoriteWhenClick(heart);
            })
            .catch(error => console.log(error));
        },

        //next song
        nextSong: function() {
            this.currentIndex++;

            if(this.currentIndex >= this.songs.length) {
                this.currentIndex = 0;
            }

            this.loadCurrentSong();
        },

        //prev song
        prevSong: function() {
            this.currentIndex--;
            
            if(this.currentIndex < 0) {
                this.currentIndex = this.songs.length - 1;
            }

            this.loadCurrentSong();
        },

        //random song
        randomSong: function() {
            prevIndex = this.currentIndex;

            do {
                this.currentIndex = Math.floor(Math.random() * this.songs.length);
            } while(this.currentIndex === prevIndex);

            this.loadCurrentSong();
        },

        //format of display time
        displayTime: function(seconds) {
            seconds = seconds.toFixed(0);
            minutes = 0;

            while(seconds >= 60) {
                minutes++;
                seconds = seconds - 60;
            }

            string = ':'
            if(seconds < 10) {
                string = ':0'
            }

            return minutes.toString() + string + seconds.toString();

        },

        // select song when click
        playWhenClick: function(classlist) {
            for (var i = 0; i < classlist.length; i++) {
                const item = classlist[i];
                
                item.addEventListener("click", function(){
                    app.select(this);
                });
            }
        },

        // select song
        select: function(e) {
            this.currentIndex = e.children[0].value-1;
            this.loadCurrentSong();
            audioSong.play();
        },

        //close song
        closeSong: function() {
            footer.style.display = "none";
            imageSong.src = "";
            nameSong.innerHTML = "";;
            singerSong.innerHTML = "";;
            audioSong.src = "";
            total.innerHTML = "";
            current.innerHTML = "";
        },

        //display album when click singer
        displayAlbumWhenClick: function(classlist) {
            for (var i = 0; i < classlist.length; i++) {
                const item = classlist[i];
                
                item.addEventListener("click", function(){
                    app.displayAlbum(this);
                });
            }
        },

        //display album of singer
        displayAlbum: function(e) {
            albumShow.style.display = "";

            id_singer = e.children[0].value;

            fetch('http://localhost/controller/get_music.php?id=' + id_singer)
            .then(res => res.json())
            .then(data => {
                const songs = data.songsBySinger;

                const nameSinger = songs[0].singer;
                albumShow.children[0].innerHTML = "Các bài hát của " + nameSinger;

                const listAlbum = songs.map(song => {
                    return `
                        <div>
                            <div class="play">
                                <input style="display: none" type="number" value="${song.position}">
                                <img src="${song.image}">
                            </div>
                            <p>${song.name}</p>
                        </div>
                    `
                })
                album.innerHTML = listAlbum.join('\n');

                const play = document.getElementsByClassName('play')
                app.playWhenClick(play);

                aside2.scrollTo(0, 1000);
            })
            .catch(error => console.log(error));
        },

        //request song
        id :null,
        suggest: function(text) {
            clearInterval(this.id);

            this.id = setTimeout(() => {
                this.searchSong(text);
            }, 300);
        },

        //search song
        searchSong: function(text) {
            resultShow.style.display = "";

            fetch('http://localhost/controller/get_music.php?name=' + text)
            .then(res => res.json())
            .then(data => {
                songs = data.songResult;

                if(songs.length > 0) {

                    const listResult = songs.map(song => {
                        return `
                            <div class="language-content">
                                <div class="song">
                                    <input style="display: none" type="number" value="${song.position}">
                                    <img src="${song.image}">
                                    <span>${song.name}</span>
                                    <br>
                                    <span>${song.singer}</span>
                                </div>
                            </div>
                        `;
                    })
                    result.innerHTML = listResult.join('\n');

                    const items = document.getElementsByClassName('song');
                    app.playWhenClick(items);

                    aside2.scrollTo(0, 400);
                }
                else {
                    resultShow.style.display = "none";
                    result.innerHTML = "";
                }
            })
            .catch(error => console.log(error));
        },

        //add song listened
        addListened: function(position) {
            fetch('http://localhost/controller/add_listened.php?position=' + position);
        },

        //add favorite when click
        addFavoriteWhenClick: function(classlist) {
            for (var i = 0; i < classlist.length; i++) {
                const item = classlist[i];
                
                item.addEventListener("click", function(){
                    if(username.value == '') {
                        alert('Vui lòng đăng nhập để thêm bài hát yêu thích');
                    }
                    else {
                        const position = this.children[0].value;
                        app.addFavorite(position);
    
                        const iconHeart = this.children[1];
                        currentColor = iconHeart.style.color;
    
                        if(currentColor !== 'red') {
                            iconHeart.style.color = 'red';
                        }
                        else {
                            iconHeart.style.color = 'white';
                        }
                    }
                });
            }
        },

        //add favorite song
        addFavorite: function(position) {
            fetch('http://localhost/controller/add_favorite.php?position=' + position)
            .then(res => res.json())
            .then(data => {
                const update = data.update;

                if(update == 'add') {
                    alert('Đã thêm vào mục yêu thích');
                }
                else if(update == 'delete') {
                    alert('Đã xóa khỏi mục yêu thích');
                }
                else if(update == 'no_chagne') {
                    alert('Vui lòng đăng nhập');
                }
                
            })
            .catch(error => console.log(error));
        },

        //reset listened
        resetListened: function() {
            fetch('http://localhost/controller/reset_listened.php');
        },

        //srat application
        start: function() {
            //define properties
            this.defineProperties();

            //handles events
            this.handleEvents();

            // show music
            this.render();

            //reset listened
            this.resetListened();
        }
    }

    app.start();
}