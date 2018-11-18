// Пользовательский интерфейс

function UI(callbacks) {

    // колбеки
    isLoggedIn = callbacks.isLoggedIn;
    onStartGame = callbacks.onStartGame;
    onGamePage = callbacks.onGamePage;
    onGetMap = callbacks.onGetMap;
    onChangeDirection = callbacks.onChangeDirection;
    onUpdateScene = callbacks.onUpdateScene;

    // элементы страницы
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
    const UPDATE_TIMEOUT = 1000; // время обновления сцены

    // Подготавливаем модуль
    this.init = () => {
        isLoggedIn();
    };


    // Переключатель страниц
    this.switchPage = (page = '') => {
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
    };

    this.initProfile = () => {
        this.handleClickStartGameBtn(onStartGame);
    };
    this.initMaps = () => {
        c.mapsBlock.find('[data-map-id]').bind("click", (e) => {
            event.preventDefault();
            let map_id = $(e.target).data('map-id');
            onGetMap(map_id);
        })
    };
    this.initGame = () => {
        this.handleArrowKeys(onChangeDirection);
        setTimeout(onUpdateScene(), UPDATE_TIMEOUT);
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