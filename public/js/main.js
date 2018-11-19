// Скрипт клиента
// Питон / Змея
// ПИ 21


$(document).ready(async () => {
    const UPDATE_SCENE_INTERVAL = 200; // Интервал обращений к серверу в мс

    var server, ui, graph, user;

    function init() {
        server = new Server();
        ui = new UI(handlerUI);
        graph = new Graph();
        user = new User(server, switchPage);

        user.init();

    }

    // Переключатель страниц
    switchPage = (page = '', options = {}) => {
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

    // Отлавливаем колбеки UI
    const handlerUI = {
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

	init();



	error = (err = "") => {
		console.log("Ошибка :: %s", err)
	}


});
