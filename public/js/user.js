function User(server, switchPage) {

    var c;

    // Инициализация модуля
    this.init = () => {
        switchPage('LoginPage');
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
            c.blocks.maps.html('');
        }
        $.each(maps, (i, map) => {
            c.blocks.maps.append('<div class="map_item" data-map-id="'+map.id+'">Карта номер '+map.id+'</div>');
        });
        c.blocks.maps.find('[data-map-id]').bind("click", (e) => {
            event.preventDefault();
            let map_id = $(e.target).data('map-id');
            this.onGetMap(map_id);
        })
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
        c.form.login.on("submit", (event) => {
            event.preventDefault();
            callback({
                login: c.form.login.find("input[name='login']").val(),
                password: c.form.login.find("input[name='password']").val(),
            });
        });
    };

    this.handleClickRegisterBtn = (callback) => {
        c.pages.login.find('.registerLink').bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };

    this.handleRegister = (callback) => {
        c.form.register.on("submit", (event) => {
            event.preventDefault();
            callback({
                name: c.form.register.find("input[name='name']").val(),
                login: c.form.register.find("input[name='login']").val(),
                password: c.form.register.find("input[name='password']").val(),
            });
        });
    };

    this.handleClickLoginBtn = (callback) => {
        c.pages.register.find('.loginLink').bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };

    this.handleClickLogoutBtn = (callback) => {
        c.btn.logout.bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };

    this.handleClickStartGameBtn = (callback) => {
        c.btn.startGame.bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };

    this.handleClickStopGameBtn = (callback) => {
        c.btn.stopGame.bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };

    error = (err = "") => {
        console.log("Ошибка :: %s", err)
    };

    c = {
        pages: {
            login: $('.loginPage'),
            register: $('.registerPage'),
            game: $('.gamePage'),
            profile: $('.profilePage'),
            maps: $('.mapsPage'),
        },
        modal: {
            finish: $('#modalFinishGame'),
        },
        text: {
            user_score: $('.user_score'),
        },
        btn: {
            startGame: $('.startGameBtn'),
            stopGame: $('.stopGameBtn'),
            logout: $('.logoutBtn'),
        },
        form: {
            login: $('#loginForm'),
            register: $('#registerForm'),
        },
        game: {
            canvas: document.getElementById('game'),
            block: $('#game'),
            wrapper: $('#game_wrapper'),
        },
        blocks: {
            maps: $('.mapsBlock'),
        },
    };

}