function User(server, switchPage) {

    let c = {
        startGameBtn: $('.startGameBtn'),
        stopGameBtn: $('.stopGameBtn'),

        mapsBlock: $('.mapsBlock'),
        loginPage: $('.loginPage'),
        gamePage: $('.gamePage'),
        registerPage: $('.registerPage'),
        profilePage: $('.profilePage'),
        mapsPage: $('.mapsPage'),

        loginForm: $('#loginForm'),
        gameWrapper: $('#game_wrapper'),
        registerForm: $('#registerForm'),

        logoutBtn: $('.logoutBtn'),
    };

    // Инициализация модуля
    this.init = () => {
        // Вызов хендлеров
    	this.handleLogin(this.onLogin);
        this.handleRegister(this.onRegister);
    	this.handleClickLoginBtn(() => {
            switchPage('RegisterPage');
		});
    	this.handleClickRegisterBtn(() => {
            switchPage('LoginPage');
		});
    	this.handleClickLogoutBtn(this.onLogout);
        this.handleClickStartGameBtn(this.onStartGame);

        c.mapsBlock.find('[data-map-id]').bind("click", (e) => {
            event.preventDefault();
            let map_id = $(e.target).data('map-id');
            this.onGetMap(map_id);
        })
	};

    // Нажатие на начать игру
    this.onStartGame = async () => {
        const answer = await server.getMaps();
        if(answer.result) {
            switchPage('MapsPage');
            // Отрисовываем выборку карт
            this.createMaps(answer.data);
        } else {
            error(answer.error);
        }
    };

    // Нажатие на карту
    this.onGetMap = async (map_id = 0) => {
        server.setMapId(map_id);
        const answer = await server.startGame(map_id);
        if(answer.result) {
            // Переключаемся в игру
            switchPage('GamePage', answer.data);
        } else {
            error(answer.error);
        }
    };
    // Отрисовываем карты
    this.createMaps = (maps) => {
        if(maps.length > 0) {
            c.maps.html('');
        }
        $.each(maps, (i, map) => {
            c.maps.append('<div class="map_item" data-map-id="'+map.id+'">Карта номер '+map.id+'</div>');
        });
    };

    // Авторизация на сервере
    this.onLogin = async (options = {}) => {
        const answer = await server.login(options);
        if(answer.result) {
            switchPage('ProfilePage');
        } else {
            error(answer.error);
        }
    };

    // Регистрация на сервере
    this.onRegister = async (options = {}) => {
        const answer = await server.register(options);
        if(answer.result) {
            switchPage('ProfilePage');
        } else {
            error(answer.error);
        }
    };



    /* Обработка нажатий */

    this.handleLogin = (callback) => {
        c.loginForm.on("submit", (event) => {
            event.preventDefault();
            callback({
                login: c.loginForm.find("input[name='login']").val(),
                password: c.loginForm.find("input[name='password']").val(),
            });
        });
    };

    this.handleClickRegisterBtn = (callback) => {
        c.loginPage.find('.registerLink').bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };

    this.handleRegister = (callback) => {
        c.registerForm.on("submit", (event) => {
            event.preventDefault();
            callback({
                name: c.registerForm.find("input[name='name']").val(),
                login: c.registerForm.find("input[name='login']").val(),
                password: c.registerForm.find("input[name='password']").val(),
            });
        });
    };

    this.handleClickLoginBtn = (callback) => {
        c.registerPage.find('.loginLink').bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };

    this.handleClickLogoutBtn = (callback) => {
        c.logoutBtn.bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

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

    error = (err = "") => {
        console.log("Ошибка :: %s", err)
    }

}