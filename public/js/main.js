// Скрипт клиента
// Питон / Змея
// ПИ 21

$(document).ready(async () => {
    const UPDATE_SCENE_INTERVAL = 100; // Интервал обращений к серверу в сек

	const server = new Server();
	const ui = new UI();
	const graph = new Graph();
	const struct = new Struct();

	let user = struct.getUser();


    function F1(callbacks) {
        isLoggedIn = callbacks.isLoggedIn;
        onLoginPage = callbacks.onLoginPage;
        onLogin = callbacks.onLogin;
        onSuccessLogin = callbacks.onSuccessLogin;
        onErrorLogin = callbacks.onErrorLogin;
        handleClickRegisterBtn = callbacks.handleClickRegisterBtn;

        onRegisterPage = callbacks.onRegisterPage;
        onRegister = callbacks.onRegister;
        onSuccessRegister = callbacks.onSuccessRegister;
        onErrorRegister = callbacks.onErrorRegister;
        handleClickLoginBtn = callbacks.handleClickLoginBtn;

        onProfilePage = callbacks.onProfilePage;
        onSuccessLogout = callbacks.onSuccessLogout;
        onErrorLogout = callbacks.onErrorLogout;
        handleClickLogoutBtn = callbacks.handleClickLogoutBtn;
        handleClickStartGameBtn = callbacks.handleClickStartGameBtn;

        onGamePage = callbacks.onGamePage;
        onMove = callbacks.onMove;
        onGetScene = callbacks.onGetScene;


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
                onErrorLogin(answer.error);
            }
        },
        onSuccessLogin: (data = {}) => {
            ui.Router('GamePage', onGamePage);
            ui.showMessage(data);
        },
        onErrorLogin: (err) => {
            ui.showMessage(err);
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
                onErrorRegister(answer.error);
            }
        },
        onSuccessRegister: (data = {}) => {
            ui.Router('GamePage', onGamePage);
            ui.showMessage(data);
        },
        onErrorRegister: (err) => {
            ui.showMessage(err);
        },
        handleClickLoginBtn: (err) => {
            ui.Router('LoginPage', onLoginPage);
        },

        // Страница профиля
        onProfilePage: () => {
            graph.output('myLoginText', struct.user.login);
            ui.handleClickLoginBtn(handleClickLogoutBtn);
        },
        handleClickLogoutBtn: async () => {
            const answer = await server.logout();
            if(answer.result) {
                struct.destroyUser();
                onSuccessLogout();
            } else {
                onErrorLogout(answer.error);
            }
        },
        handleClickStartGameBtn: async () => {
            let snake = {
                user_id: struct.user.id,
                map_id: struct.map.id,
                direction: 'right',
            };
            const answer = await server.createSnake(snake);
            if(answer.result) {
                struct.setSnake(answer.data);
                onSuccessLogout();
            } else {
                onErrorLogout(answer.error);
            }
        },
        onSuccessLogout: () => {
            ui.Router('LoginPage', onLoginPage);
            ui.showMessage('Вы успешно вышли из профиля');
        },
        onErrorLogout: (err) => {
            ui.showMessage(err);
        },

        // Страница игры
        onGamePage: () => {
            graph.init();
            ui.handleArrowKeys(onMove);
            var updateScene = setInterval(getScene(onGetScene), UPDATE_SCENE_INTERVAL * 1000);
            getScene(onGetScene);
        },
        onMove: async (direction) => {
            // Нажатие на клавишу
            debugger;
            if(direction) {
                await server.changeDirection(struct.user.id, direction);
            }
        },
        onGetScene: (result = false) => {
            // Получение сцены
            if (result) {
                graph.draw(struct);
            }
        },
    });

	async function getScene(callback) {
        const answer = await server.getScene(struct.map.id);
        getAnswer(answer, callback);
	}

	function getAnswer(answer = {}, callback) {
        if(answer.result) {
            console.log(answer.data);
            struct.set(answer.data);
            callback(answer.result);
        } else {
            error(answer.error);
            callback(answer.result);
        }
	}

	function error(error = "") {
		console.log("Ошибка :: %s", error)
	}


});
