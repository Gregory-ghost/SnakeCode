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

        onGamePage = callbacks.onGamePage;
        onMove = callbacks.onMove;
        onGetScene = callbacks.onGetScene;


        if(!user.login) {
            ui.Router('LoginPage', onLoginPage);
        } else {
            ui.Router('GamePage', onGamePage);
        }

    }

    const f1 = new F1({
        onLoginPage: () => {
            ui.handleLogin(onLogin);
            ui.handleClickRegisterBtn(handleClickRegisterBtn);
        },
        onLogin: async (options = {}) => {
            const answer = await server.login(options);
            if(answer.result) {
                onSuccessLogin(answer.data);
            } else {
                onErrorLogin(answer.error);
            }
        },
        onSuccessLogin: (data = {}) => {
            ui.showMessage(data);
        },
        onErrorLogin: (err) => {
            ui.showMessage(err);
        },
        handleClickRegisterBtn: (err) => {
            ui.Router('RegisterPage', onRegisterPage);
        },

        onRegisterPage: () => {
            ui.handleRegister(onRegister);
            ui.handleClickLoginBtn(handleClickLoginBtn);
        },
        onRegister: async (options = {}) => {
            const answer = await server.register(options);
            if(answer.result) {
                onSuccessRegister(answer.data);
            } else {
                onErrorRegister(answer.error);
            }
        },
        onSuccessRegister: (data = {}) => {
            ui.showMessage(data);
        },
        onErrorRegister: (err) => {
            ui.showMessage(err);
        },
        handleClickLoginBtn: (err) => {
            ui.Router('LoginPage', onLoginPage);
        },


        onGamePage: () => {
            graph.init();
            ui.handleArrowKeys(onMove);
            var updateScene = setInterval(getScene(onGetScene), UPDATE_SCENE_INTERVAL * 1000);
            getScene(onGetScene);
        },
        onChangePage: () => {
            //graph.init();
        },
        onMove: async (direction) => {
            // Нажатие на клавишу
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
        const answer = await server.getScene();
        getAnswer(answer, callback);
	}

	function getAnswer(answer = {}, callback) {
        if(answer.result) {
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
