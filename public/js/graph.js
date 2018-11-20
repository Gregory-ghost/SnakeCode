// Отрисовка сцены
function Graph(c = {}) {
    const canvas = c.game.canvas, // холст
        ctx = canvas.getContext('2d'),
        SIZE = c.size, // размеры
        sprites = c.sprites; // Координаты в спрайте



    // Отрисовываем карту
    this.draw = (data = {}) => {
        c.game.block.attr({width: (SIZE.width * SIZE.sizeSnake) + 'px', height: (SIZE.height * SIZE.sizeSnake) + 'px'});

        this.clear();

        this.drawMap();
        this.drawSnakes(data.snakes);
        this.drawFoods(data.foods);
    };


    // Отрисовываем карту
    this.drawMap = () => {
        let sprite = new Image();
        sprite.src = c.path.images + 'bg2.png';

        sprite.addEventListener('load', () => {
            ctx.drawImage(sprite, 0, 0, (SIZE.width * SIZE.sizeSnake), (SIZE.height * SIZE.sizeSnake))
        }, false);
    };

    this.drawSnakes = (snakes = {}) => {
        // Отрисовываем всех змей
        for(let i = 0; i < snakes.length; i++) {
            this.drawSnake(snakes[i]);
        }
    };

    // Рисование змеи
    this.drawSnake = (snake = {}) => {
        let body = snake.body, // Тело
            direction = snake.direction, // Направление
            lastPosition = {}, // Последняя позиция
            countItems = 0; // Количество тел в змейке

        if(!body) return;
        countItems = body.length;

        for(let i = 0; i < countItems; i++) {
            let snakePositionSprite = {}, // Позиция спрайта
                item = body[i];


            // Здесь проверка на позицию
            // и отталкиваясь от этого определяется какая это часть змейки

            if(lastPosition.x) {
                if(i > countItems-2) {
                    // Хвост
                    if(lastPosition.x == item.x && lastPosition.y > item.y) {
                        // ползет вниз
                        snakePositionSprite = sprites.footer['down'];
                    } else if(lastPosition.x == item.x && lastPosition.y < item.y) {
                        // ползет вверх
                        snakePositionSprite = sprites.footer['up'];
                    } else if(lastPosition.x > item.x && lastPosition.y == item.y) {
                        // ползет вправо
                        snakePositionSprite = sprites.footer['right'];
                    } else {
                        // ползет влево
                        snakePositionSprite = sprites.footer['left'];
                    }
                } else {
                    // Тело
                    let predPosition = body[i+1];
                    if(predPosition.x < lastPosition.x) {
                        // движется вправо
                        if(predPosition.y < lastPosition.y) {
                            // заворачивает вправо сверху
                            snakePositionSprite = sprites.body['leftDown'];
                        } else if(predPosition.y > lastPosition.y) {
                            // заворачивает вправо снизу
                            snakePositionSprite = sprites.body['rightDown'];
                        } else {
                            // движется горизонтально
                            snakePositionSprite = sprites.body['lineHoriz'];
                        }
                    } else if(predPosition.x > lastPosition.x) {
                        // движется влево
                        if(predPosition.y < lastPosition.y) {
                            // заворачивает влево сверху
                            snakePositionSprite = sprites.body['leftUp'];
                        } else if(predPosition.y > lastPosition.y) {
                            // заворачивает влево снизу
                            snakePositionSprite = sprites.body['rightUp'];
                        } else {
                            // движется горизонтально
                            snakePositionSprite = sprites.body['lineHoriz'];
                        }
                    } else {
                        // движется вертикально
                        snakePositionSprite = sprites.body['lineVert'];
                    }
                }
            } else {
                // Голова
                if(!direction) {
                    direction = 'left';
                }
                let predPosition = body[i+1];
                if(predPosition) {
                    if(predPosition.x < item.x) {
                        // движется вправо
                        snakePositionSprite = sprites.head['right'];
                    } else if(predPosition.x > item.x) {
                        // движется влево
                        snakePositionSprite = sprites.head['left'];
                    } else {
                        if(predPosition.y < item.y) {
                            // движется вниз
                            snakePositionSprite = sprites.head['down'];
                        } else {
                            // движется вверх
                            snakePositionSprite = sprites.head['up'];
                        }
                    }
                } else {
                    // нету тела, только голова
                    snakePositionSprite = sprites.head[direction];
                }
            }
            let options = {
                xsprite: snakePositionSprite[0],
                ysprite: snakePositionSprite[1],
                x: item.x*SIZE.sizeSnake,
                y: item.y*SIZE.sizeSnake,
            };
            lastPosition = body[i];
            this.drawSprite(options);
        }
    };

    // Рисование еды
    this.drawFoods = (foods = {}) => {
        // Отрисовываем всех змей
        for(let i = 0; i < foods.length; i++) {
            this.drawFood(foods[i]);
        }
    };

    this.drawFood = (food = {}) => {
        let options = {
            xsprite: sprites.eat[0],
            ysprite: sprites.eat[1],
            x: food.x*SIZE.sizeSnake,
            y: food.y*SIZE.sizeSnake,
        };
        this.drawSprite(options);
    };


    // Рисование спрайта
    this.drawSprite = (options = {}) => {
        if(options) {
            let sprite = new Image();
            sprite.src = c.path.sprites + 'sprites.png';
            sprite.addEventListener("load", function(){
                ctx.drawImage(sprite, options.xsprite, options.ysprite, SIZE.sizeSnake ,SIZE.sizeSnake, options.x, options.y, SIZE.sizeSnake, SIZE.sizeSnake);
            }, false);

        }
    };

    // Подготовка сцены
    this.init = () => {
        // путь до спрайтов
        let link = location.href;
        link = link.split('?')[0];
        link = link.split('/').slice(0, -1).join('/');
        c.pathSprites = link + c.pathSprites;
        c.pathImages = link + c.pathImages;
    };
    // Очистка сцены
    this.clear = () => {
        ctx.clearRect(0, 0, (SIZE.width * SIZE.sizeSnake), (SIZE.height * SIZE.sizeSnake));
    };

}

