// Игра
function Game(server, switchPage) {
    var c, ui, graph;

    this.init = () => {
        ui = new UI(handlerUI);
        graph = new Graph(c);
    };

    this.start = (options) => {
        graph.init();
        graph.draw(options);
        ui.init();
        this.updateScene();
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

    // обновление сцены
    this.updateScene = async () => {
        const answer = await server.getScene();
        let isFinish = false; // игра закончена

        if(answer.result) {
            // положительный результат сервера
            if(answer.data.finish) {
                isFinish = true;
                c.text.user_score.text(answer.data.score);
                c.modal.finish.modal(); // выдаем окно с завершением игры
                c.modal.finish.on('hidden.bs.modal', function (e) {
                    // do something...
                    switchPage('ProfilePage');
                })
            } else {
                // Отрисовываем игру
                graph.draw(answer.data);
            }
        } else {
            error(answer.error);
        }
        if(!isFinish) {
            setTimeout(() => this.updateScene(), 200);
        }
    };


    // Константы
    c = {
        modal: {
            finish: $('#modalFinishGame'),
            closeFinish: $('#closeFinishGame'),
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
        size: {
            width: 28,
            height: 14,
            sizeSnake: 32,
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
                left: [192, 192],
                right: [256, 128],
            },
            // Еда
            eat: [0, 192],
        },
    };
}