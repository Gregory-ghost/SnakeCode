// Скрипт клиента
// Питон / Змея
// ПИ 21


$(document).ready(async () => {
    const UPDATE_SCENE_INTERVAL = 200; // Интервал обращений к серверу в мс

    var c, server, user, ui, graph;

    function init() {
         c = new Const(); // Элементы страницы
         server = new Server(); // Сервер
         user = new User(server, c, switchPage); // Пользователь

        // todo :: сделать одну структуру в Game.js
         ui = new UI(handlerUI);
         graph = new Graph(c);

        user.init();
    }


    // Переключатель страниц
    switchPage = (page = '', options = {}) => {
        switch(page) {
            case 'LoginPage':
                c.game.wrapper.find('.page').addClass('hidden');
                c.pages.login.removeClass('hidden');

                break;
            case 'RegisterPage':
                c.game.wrapper.find('.page').addClass('hidden');
                c.pages.register.removeClass('hidden');

                break;
            case 'ProfilePage':
                c.game.wrapper.find('.page').addClass('hidden');
                c.pages.profile.removeClass('hidden');

                break;
            case 'MapsPage':
                c.game.wrapper.find('.page').addClass('hidden');
                c.pages.maps.removeClass('hidden');

                break;
            case 'GamePage':
                c.game.wrapper.find('.page').addClass('hidden');
                c.pages.game.removeClass('hidden');
                if(options) {
                    graph.init();
                    graph.draw(options);
                    ui.init();
                    updateScene();
                }
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
    };
    updateScene = async () => {
        const answer = await server.getScene();

        if(answer.result) {
            // Отрисовываем игру
            graph.draw(answer.data);
        } else {
            error(answer.error);
        }
        setTimeout(() => updateScene(), 1000);
    };


    init();

	error = (err = "") => {
		console.log("Ошибка :: %s", err)
	}


});

// Элементы страницы
function Const() {
    return {
        pages: {
            login: $('.loginPage'),
            register: $('.registerPage'),
            game: $('.gamePage'),
            profile: $('.profilePage'),
            maps: $('.mapsPage'),
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
        size: {
            width: 14,
            height: 7,
            sizeSnake: 64,
            px: 'px',
        },
        path: {
            sprites: '/public/img/sprite/',
            images: '/public/img/',
        },
        sprites: {
            head: {
                up: [192, 0],
                down: [256, 64],
                left: [192, 64],
                right: [256, 0],
            },
            body: {
                lineHoriz: [64, 0],
                lineVert: [128, 64],
                leftDown: [0, 64],
                leftUp: [0, 0],
                rightUp: [128, 0],
                rightDown: [128, 128],
            },
            footer: {
                up: [192, 128],
                down: [256, 192],
                right: [192, 192],
                left: [256, 128],
            },
            // Еда
            eat: [0, 192],
        },
    };
}