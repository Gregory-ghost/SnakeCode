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
            countItems = body.length; // Количество тел в змейке

        for(let i = 0; i < countItems; i++) {
            let snakePositionSprite = {}, // Позиция спрайта
                item = body[i];

            // Здесь проверка на позицию
            // и отталкиваясь от этого определяется какая это часть змейки

            if(lastPosition.x) {
                if(i > countItems-2) {
                    // Хвост
                    if(lastPosition.x == item.x && lastPosition.y > item.y) {
                        snakePositionSprite = sprites.footer['down'];
                    } else if(lastPosition.x == item.x && lastPosition.y < item.y) {
                        snakePositionSprite = sprites.footer['up'];
                    } else if(lastPosition.x > item.x && lastPosition.y == item.y) {
                        snakePositionSprite = sprites.footer['left'];
                    } else {
                        snakePositionSprite = sprites.footer['right'];
                    }
                } else {
                    // Тело
                    let predPosition = body[i+1];
                    if(predPosition.x < lastPosition.x) {
                        if(predPosition.y < lastPosition.y) {
                            snakePositionSprite = sprites.body['leftDown'];
                        } else if(predPosition.y > lastPosition.y) {
                            snakePositionSprite = sprites.body['leftUp'];
                        } else {
                            snakePositionSprite = sprites.body['lineHoriz'];
                        }
                    } else if(predPosition.x > lastPosition.x) {
                        if(predPosition.y < lastPosition.y) {
                            snakePositionSprite = sprites.body['leftUp'];
                        } else if(predPosition.y > lastPosition.y) {
                            snakePositionSprite = sprites.body['leftDown'];
                        } else {
                            snakePositionSprite = sprites.body['lineHoriz'];
                        }
                    } else {
                        snakePositionSprite = sprites.body['lineVert'];
                    }
                }
            } else {
                // Голова
                if(!direction) {
                    direction = 'left';
                }
                snakePositionSprite = sprites.head[direction];
            }
            let options = {
                xsprite: snakePositionSprite[0],
                ysprite: snakePositionSprite[1],
                x: item.x,
                y: item.y,
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
            x: food.x,
            y: food.y,
        };
        this.drawSprite(options);
    };


    // Рисование спрайта
    this.drawSprite = (options = {}) => {
        if(options) {
            let sprite = new Image();
            sprite.src = c.pathSprites + 'sprites.png';
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

