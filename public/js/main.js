// Скрипт клиента
// Питон / Змея
// ПИ 21


$(document).ready(async () => {
    const UPDATE_SCENE_INTERVAL = 200; // Интервал обращений к серверу в мс

    var c, server, user, game;

    function init() {
         c = new Const(); // Элементы страницы
         server = new Server(); // Сервер
         user = new User(server, switchPage); // Пользователь
         game = new Game(server, switchPage); // Игра


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
                    game.init();
                    game.start(options);
                }
                break;
        }
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
        modal: {
            finish: $('#modalFinishGame'),
        },
        text: {
            user_score: $('.user_score'),
        },
        game: {
            canvas: document.getElementById('game'),
            block: $('#game'),
            wrapper: $('#game_wrapper'),
        },
    };
}