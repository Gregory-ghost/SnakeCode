// Пользовательский интерфейс

function UI(callbacks) {

    isLoggedIn = callbacks.isLoggedIn;
    onStartGame = callbacks.onStartGame;
    onGamePage = callbacks.onGamePage;
    onGetMap = callbacks.onGetMap;

    let c = {
        loginPage: $('.loginPage'),
        gamePage: $('.gamePage'),
        registerPage: $('.registerPage'),
        profilePage: $('.profilePage'),
        mapsPage: $('.mapsPage'),

        gameWrapper: $('#game_wrapper'),
        loginForm: $('#loginForm'),
        registerForm: $('#registerForm'),

        startGameBtn: $('.startGameBtn'),
        stopGameBtn: $('.stopGameBtn'),
        logoutBtn: $('.logoutBtn'),

        mapsBlock: $('.mapsBlock'),
    };

    isLoggedIn();


    // Переключатель страниц
    this.switchPage = (page = '', callback) => {
        switch(page) {
            case 'LoginPage':
                c.gameWrapper.find('.page').addClass('hidden');
                c.loginPage.removeClass('hidden');

                break;
            case 'RegisterPage':
                c.gameWrapper.find('.page').addClass('hidden');
                c.registerPage.removeClass('hidden');

                break;
            case 'ProfilePage':
                c.gameWrapper.find('.page').addClass('hidden');
                c.profilePage.removeClass('hidden');

                break;
            case 'MapsPage':
                c.gameWrapper.find('.page').addClass('hidden');
                c.mapsPage.removeClass('hidden');

                break;
            case 'GamePage':
                c.gameWrapper.find('.page').addClass('hidden');
                c.gamePage.removeClass('hidden');

                break;
        }
        callback(page);
    };

    this.initProfile = () => {
        this.handleClickStartGameBtn(onStartGame);
    };
    this.initMaps = () => {
        c.mapsBlock.find('.map_item').click(() => {
            onGetMap();
        })
    };

    this.handleArrowKeys = function (callback) {
        $(window).bind({
            keydown: function (e) {
                if (!e) {
                    e = window.event;
                }
                let key = (e.which) ? e.which : e.keyCode;

                if (key == 37 || key == 65) {
                    callback("left");
                } else if (key == 39 || key == 68) {
                    callback("right");
                } else if (key == 38 || key == 87) {
                    callback("up");
                } else if (key == 40 || key == 83) {
                    callback("down");
                }
            },
        })
    };

    this.handleClickStartGameBtn = (callback) => {
        c.startGameBtn.bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };

    this.handleClickStopGameBtn = (callback) => {
        c.stopGameBtn.bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };

    this.showMessage = (msg) => {
        if(msg) {
            console.log(msg);
            //alert(msg)
        }
    };
}