// Скрипт клиента
// Питон / Змея
// ПИ 21

$(document).ready(async () => {
    const UPDATE_SCENE_INTERVAL = 1; // Интервал обращений к серверу в сек

	const server = new Server();
	const ui = new UI();
	const graph = new Graph();
	const struct = new Struct();


    function F1(callbacks) {
        isLoggedIn = callbacks.isLoggedIn;
        onLoginPage = callbacks.onLoginPage;
        onLogin = callbacks.onLogin;
        onSuccessLogin = callbacks.onSuccessLogin;
        handleClickRegisterBtn = callbacks.handleClickRegisterBtn;

        onRegisterPage = callbacks.onRegisterPage;
        onRegister = callbacks.onRegister;
        onSuccessRegister = callbacks.onSuccessRegister;
        handleClickLoginBtn = callbacks.handleClickLoginBtn;

        onProfilePage = callbacks.onProfilePage;
        onSuccessLogout = callbacks.onSuccessLogout;
        handleClickLogoutBtn = callbacks.handleClickLogoutBtn;
        handleClickStartGameBtn = callbacks.handleClickStartGameBtn;

        onGamePage = callbacks.onGamePage;
        onSnakeDestroy = callbacks.onSnakeDestroy;
        onGameEnd = callbacks.onGameEnd;
        onMove = callbacks.onMove;
        onUpdateScene = callbacks.onUpdateScene;
        onGetScene = callbacks.onGetScene;

        onError = callbacks.onError;


        isLoggedIn(); // Проверка на авторизацию

    }

    const f1 = new F1({
        isLoggedIn: async () => {
            const answer = await server.getCurrentUser();
            let isLoggedIn = answer.result;
            if(isLoggedIn) {
                struct.setUser(answer.data["myUser"]);
                ui.Router('ProfilePage', onProfilePage);
            } else {
                ui.Router('LoginPage', onLoginPage);
            }
        },
        // Страница входа
        onLoginPage: () => {
            ui.handleLogin(onLogin);
            ui.handleClickRegisterBtn(handleClickRegisterBtn);
        },
        onLogin: async (options = {}) => {
            const answer = await server.login(options);
            if(answer.result) {
                struct.setUser(options);
                onSuccessLogin(answer.data);
            } else {
                onError(answer.error);
            }
        },
        onSuccessLogin: (data = {}) => {
            ui.Router('GamePage', onGamePage);
            ui.showMessage(data);
        },
        handleClickRegisterBtn: (err) => {
            ui.Router('RegisterPage', onRegisterPage);
        },

        // Страница регистрации
        onRegisterPage: () => {
            ui.handleRegister(onRegister);
            ui.handleClickLoginBtn(handleClickLoginBtn);
        },
        onRegister: async (options = {}) => {
            const answer = await server.register(options);
            if(answer.result) {
                struct.setUser(options);
                onSuccessRegister(answer.data);
            } else {
                onError(answer.error);
            }
        },
        onSuccessRegister: (data = {}) => {
            ui.Router('GamePage', onGamePage);
            ui.showMessage(data);
        },
        handleClickLoginBtn: (err) => {
            ui.Router('LoginPage', onLoginPage);
        },

        // Страница профиля
        onProfilePage: () => {
            let user = struct.getUser();
            graph.output('myLoginText', user.login);
            ui.handleClickLogoutBtn(handleClickLogoutBtn);
            ui.handleClickStartGameBtn(handleClickStartGameBtn);
        },
        handleClickLogoutBtn: async () => {
            const answer = await server.logout();
            if(answer.result) {
                struct.destroyUser();
                onSuccessLogout();
            } else {
                onError(answer.error);
            }
        },
        handleClickStartGameBtn: async () => {
            let snake = {
                user_id: struct.getUser().id,
                map_id: struct.getMap().id,
                direction: 'right',
            };
            const answer = await server.createSnake(snake);
            if(answer.result) {
                struct.set(answer.data);
                let user = struct.getUser();
                getMySnake(user.id);
                ui.Router('GamePage', onGamePage);
            } else {
                onError(answer.error);
            }
        },
        onSuccessLogout: () => {
            ui.Router('LoginPage', onLoginPage);
            ui.showMessage('Вы успешно вышли из профиля');
        },

        // Страница игры
        onGamePage: () => {
            graph.init();
            ui.handleArrowKeys(onMove);

            // Обновление сцены по интервалу
            onUpdateScene();


        },
        onUpdateScene: () => {
            let intervalID = 0;
            let wait =
                ms => new Promise(
                    r => setTimeout(r, ms)
                );

            let repeat =
                (ms, func) => new Promise(
                    r => (
                        intervalID = setInterval(func, ms),
                            wait(ms).then(r)
                    )
                );

            let myfunction =
                () => new Promise(
                    r => r(getScene(onGetScene))
                );

            let handleStopGame =
                () => new Promise(
                    r => r(ui.handleClickStopGameBtn(() => {
                        clearInterval(intervalID);
                        onGameEnd('game end')
                    }))
                );
            let handleSnakeDestroy =
                () => new Promise(
                    r => r(onSnakeDestroy((res) => {
                        if(res) {
                            clearInterval(intervalID);
                            onGameEnd('game end')
                        }
                    }))
                );

            repeat(UPDATE_SCENE_INTERVAL * 1000, () => Promise.all([myfunction()]).then(handleSnakeDestroy())) // UPDATE_SCENE_INTERVAL * 1000
                .then(handleStopGame())
                .then(handleSnakeDestroy())
                .then(getScene(onGetScene));
        },
        onSnakeDestroy: (callback) => {
            let snakes = struct.getSnakes(),
                mySnake = struct.getSnake(),
                isFound = false;

            $.each(snakes, (i, snake) => {
                if(snake.id == mySnake.id) {
                    // Не нашел питона
                    isFound = true;
                }
            });
            callback(!isFound);

        },
        onMove: async (direction) => {
            // Нажатие на клавишу
            if(direction) {
                await server.changeDirection(struct.getSnake().id, direction);
            }
        },
        onGetScene: (result = false) => {
            // Получение сцены
            console.log('onGetScene');
            if (result) {
                console.log('onGetScene true');
                let s = struct.get();
                debugger;
                graph.draw(struct.get());
            }
        },
        onGameEnd: (result = false) => {
            // Закончилась игра
            //let score = 0;
            ui.showMessage(result);
        },


        onError: (err) => {
            ui.showMessage(err);
        },
    });

    getMySnake = (uid) => {
        let snakes = struct.getSnakes(),
            mySnake = {};

        $.each(snakes, (i, snake) => {
            if(snake.user_id == uid) {
                mySnake = snake;
            }
        });
        struct.setSnake(mySnake);
    };

	getScene = async (callback) => {
        const answer = await server.getScene(struct.getMap().id);
        if(answer.result) {
            struct.set(answer.data);
            callback(answer.result);
        } else {
            error(answer.error);
            callback(answer.result);
        }
	};

	error = (err = "") => {
		console.log("Ошибка :: %s", err)
	}


});
