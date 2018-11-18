// Скрипт клиента
// Питон / Змея
// ПИ 21


$(document).ready(async () => {
    const UPDATE_SCENE_INTERVAL = 1; // Интервал обращений к серверу в сек

    var server, ui, graph, user;

    function init() {
        server = new Server();
        ui = new UI(handlerUI);
        graph = new Graph();
        user = new User(handlerUser);

        ui.init();
    }

    // Отлавливаем колбеки UI
    const handlerUI = {
        isLoggedIn: () => {
            let token = server.token;
            if(!token) {
                ui.switchPage('LoginPage');
                user.init();
            } else {
                ui.switchPage('ProfilePage');
                ui.initProfile();
            }
        },
        onStartGame: async () => {
            const answer = await server.getMaps();
            if(answer.result) {
                ui.switchPage('MapsPage');
                // Отрисовываем выборку карт
                graph.initMaps(answer.data);
                // Ловим нажатия на карту
                ui.initMaps();
            } else {
                error(answer.error);
            }
        },
        onGetMap: async (map_id = 0) => {
            server.setMapId(map_id);
            const answer = await server.startGame(map_id);
            if(answer.result) {
                ui.switchPage('GamePage');
                ui.initGame();
                // Отрисовываем игру
                graph.init(answer.data);
                graph.draw(answer.data);
            } else {
                error(answer.error);
            }
        },
        onChangeDirection: async (direction = 'right') => {
            const answer = await server.changeDirection(direction);
            if(answer.result) {
                // Отрисовываем игру
            } else {
                error(answer.error);
            }
        },
        onUpdateScene: async (map_id = 0) => {
            const answer = await server.getScene();
            if(answer.result) {
                // Отрисовываем игру
                graph.draw(answer.data);
            } else {
                error(answer.error);
            }
        },
    };
    // Отлавливаем колбеки User
    const handlerUser = {
        onSwitchPage: (page = '') => {
            // Переключатель страниц
            ui.switchPage(page);
        },
        onLogin: async (options = {}) => {
            // Авторизация на сервере
            const answer = await server.login(options);
            if(answer.result) {
                server.setToken(answer.data.token);
                ui.switchPage('ProfilePage');
                ui.initProfile();
            } else {
                error(answer.error);
            }
        },
        onRegister: async (options = {}) => {
            // Регистрация на сервере
            const answer = await server.register(options);
            if(answer.result) {
                server.setToken(answer.data.token);
                ui.switchPage('ProfilePage');
                ui.initProfile();
            } else {
                error(answer.error);
            }
        },
        onLogout: async (options = {}) => {
            // Выход из профиля
            const answer = await server.logout();
            if(answer.result) {
                server.setToken('');
                ui.switchPage('LoginPage');
                user.init();
            } else {
                error(answer.error);
            }
        },
    };

	init();



	error = (err = "") => {
		console.log("Ошибка :: %s", err)
	}


});
