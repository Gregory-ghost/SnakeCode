// Скрипт клиента
// Питон / Змея
// ПИ 21



/*
var $Game = {};


// Контейнеры
$Game.c = {
    game:           '#game',
    result:         '#result',
};

// Языковая поддержка
$Game.message = {
    startGame:      'Начать игру',
    stopGame:       'Остановить игру',
    winGame:        'Победа',
    loseGame:       'Поражение',
    resultGame:     'Набрано очков:',
    createSnake:    'Создать змею'
};

// Методы для вызова команды на сервере
$Game.methods = {
    'CHANGE_DIRECTION':  'changeDirection',
    'GET_SCENE':         'getScene',
    'EAT_FOOD':          'eatFood',
    'CREATE_FOOD':       'createFood',
    'GET_FOOD':          'getFood',
    'GET_SNAKE':         'getSnake',
    'DESTROY_SNAKE':     'destroySnake',
    'MOVE_SNAKE':        'moveSnake',
};

// Структура
$Game.struct = {
    map: [], // Карта
    snakes: [], // Питоны
    foods: [], // Еда
    setMap: function(map = []) {
        // Задаем информацию о карте
        if ( map ) {
            $Game.struct.map = map;
        }
    },
    setSnakes: function(snakes = []) {
        // Задаем информацию о питонах
        if ( snakes ) {
            $Game.struct.snakes = snakes;
        }
    },
    setFoods: function(foods = []) {
        // Задаем информацию о еде
        if ( foods ) {
            $Game.struct.foods = foods;
        }
    },
    user: {
        // Текущий пользователь
        snake: {},
        username: {},
        points: 0,
        level: 0,
    },
};

// Настроки игры
$Game.init = {
    start: function() {
        // Начать игру
        $($Game.c.game).html('');

        // Получаем информацию о сцене
        // $Game.scene.info();

        // Изменяем направление змеи
        let options = {id: 12, direction: 'right'};
        $(document).ready(async function () {
            let result = await $Game.snake.changeDirection(options);
            console.log(result);
            if ( result ) {
                $($Game.c.game).html('Направление изменено');
            } else {
                $($Game.c.game).html('Направление не изменено');
            }
        });
    },
    stop: function() {
        // Закончить игру

    },
    records: function() {
        // Рекордная таблица

    },
    scene: function() {
        // Информация о сцене
        $(document).ready(async function () {
            let result = await $Game.scene.get();
            // Запись в структуру
            if ( result.map ) {
                $Game.struct.setMap(result.map);
            }
            if ( result.snakes ) {
                $Game.struct.setSnakes(result.snakes);
            }
            if ( result.foods ) {
                $Game.struct.setFoods(result.foods);
            }
            console.log($Game.struct);

            console.log(result);
        });
    },
};

// Сцена
$Game.scene = {
    get: function() {
        // Получение информации
        return $Game.cmd.execute('GET_SCENE', {});
    },
    update: function() {
        // Обновление сцены
        return $Game.cmd.execute('UDPATE_SCENE', {});
    },
};

// Пользовательские функции
$Game.user = {
    login: function() {
        // Вход
    },
    logout: function() {
        // Выход

    },
    register: function() {
        // Регистрация

    },
    get: function() {
        // Получение данных о пользователе

    },
    save: function() {
        // Сохранение информации

    },
};

// Змейка
$Game.snake = {
    changeDirection: function( options = {id: 0, direction: 'left'} ) {
        // Изменить направление
        return $Game.cmd.execute('CHANGE_DIRECTION', options);
    },
    move: function( id = 0 ) {
        // Подвинуть змею
        return $Game.cmd.execute('MOVE_SNAKE', {id: id });
    },
    create: function( options = {} ) {
        // Создать змею
        return $Game.cmd.execute('CREATE_SNAKE', { options });

    },
    destroy: function( id = 0 ) {
        // Удалить змею
        return $Game.cmd.execute('CREATE_SNAKE', { id: id });
    },
};

// Еда
$Game.food = {
    create: function( options = {} ) {
        return $Game.cmd.execute('CREATE_FOOD', options);
    },
    eat: function( id = 0 ) {
        return $Game.cmd.execute('EAT_FOOD', {id: id});

    },
    get: function( options = {} ) {
        return $Game.cmd.execute('CHANGE_DIRECTION', options);
    },
    destroy: function() {

    },
};


// Карта
// Функции для работы с клиентом
$Game.map = {
    create: function() {

    },
    getSize: function() {

    },
    setSize: function() {

    },
    delete: function() {

    },
};

// Рекорды. Турнирная таблица
$Game.records = {
    get: function() {

    },
    add: function() {

    },
    delete: function() {

    },
};

// Команды
$Game.cmd = {
    execute: async function(method = '', options) {
        // Выполнение команды на сервере
        method = {method: $Game.methods[method]};
        options = {...method, ...options};
        return await $.get('api', options);
    },
};

*/


$(document).ready(async function(){
	
	const server = new Server();
	
	const answer = await server.changeDirection(12, 'left');
	
	if(answer.result) {
		
		console.log(answer.data);
	}
	
	
});
