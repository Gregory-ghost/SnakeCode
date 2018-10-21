// Скрипт клиента
// Питон / Змея
// ПИ 21

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
};

// Структура
$Game.struct = {
    map: [],
    snakes: [],
    foods: [],
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
        //$Game.scene.info();

        // Изменяем направление змеи
    },
    stop: function() {
        // Закончить игру

    },
    records: function() {
        // Рекордная таблица

    },
};

// Сцена
$Game.scene = {
    info: function() {
        $(document).ready(async function () {
            let result = await $Game.scene.get();
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
    get: function() {
        // Получение информации
        return $Game.cmd.execute('GET_SCENE', {});
    },
    update: function() {
        // Обновление сцены
        return $Game.cmd.execute('UDPATE_SCENE', {});
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
    create: function() {
        // Создать змею

    },
};

// Еда
$Game.food = {
    create: function() {

    },
    eat: function() {

    },
    get: function() {

    },
};

$Game.cmd = {
    execute: function(method = '', options) {
        // Выполнение команды на сервере
        method = {method: $Game.methods.method};
        options = {...method, ...options};
        return $.get('api', options);
    },
}
