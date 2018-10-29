// Отрисовка сцены

function Graph() {
    let canvas = document.getElementById('game'),
        ctx = canvas.getContext('2d');

    let SIZE = {
        sizeX: 14,
        sizeY: 7,
        sizeSnake: 64,
        px: 'px',
    };

    let c = {
        game: $('#game'),
        pathSprites: '/public/img/sprite/',
        pathImages: '/public/img/',
    };
    let sprites = {};

    this.init = () => {
        this.setSpritesPath();
        sprites = this.loadImages();
    };

    this.draw = (data = {}) => {
        SIZE.sizeX = data.map.sizeX;
        SIZE.sizeY = data.map.sizeY;
        SIZE.sizeSnake = data.map.sizeSnake;
        c.game.attr({width: (SIZE.sizeX * SIZE.sizeSnake) + SIZE.px, height: (SIZE.sizeY * SIZE.sizeSnake) + SIZE.px});

        this.clear();
        this.drawMap();

        this.drawSnakes(data.snakes);
    };

    this.clear = () => {
        // Очистка сцены
        ctx.clearRect(0, 0, (SIZE.sizeX * SIZE.sizeSnake), (SIZE.sizeY * SIZE.sizeSnake));
    };

    this.drawMap = () => {
        // Отрисовываем карту

        let sprite = new Image();
        sprite.src = c.pathImages + 'bg2.png';
        sprite.addEventListener("load", function(){ ctx.drawImage(sprite, 0, 0, (SIZE.sizeX * SIZE.sizeSnake), (SIZE.sizeY * SIZE.sizeSnake))}, false);
    };

    this.drawSnakes = (snakes = {}) => {
        // Отрисовываем всех змей
            for(let i = 0; i < snakes.length; i++) {
            this.drawSnake(snakes[i]);
        }
    };

    this.drawSnake = (snake = {}) => {
        // Рисование змеи
        let body = snake.body,
            direction = snake.direction,
            lastPosition = {},
            countItems = body.length;
        for(let i = 0; i < countItems; i++) {
            let snakePositionSprite = {},
                item = body[i];
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

    this.drawSprite = (options = {}) => {
        if(options) {
            let sprite = new Image();
            sprite.src = c.pathSprites + 'sprites.png';
            sprite.addEventListener("load", function(){
                ctx.drawImage(sprite, options.xsprite, options.ysprite, SIZE.sizeSnake ,SIZE.sizeSnake, options.x, options.y, SIZE.sizeSnake, SIZE.sizeSnake);
            }, false);

        }
    };


    this.loadImages = () => {
        // Подгрузка изображений
        let newSprites = {
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
            eat: [0, 192],
        };
        return newSprites;
    };

    this.newImage = (link = '') => {
        // Создание нового изображения
        let image = new Image();
        image.src = link;
        return image;
    };

    // Путь до спрайтов
    this.setSpritesPath = () => {
        let link = location.href;
        link = link.split('?')[0];
        link = link.split('/').slice(0, -1).join('/');
        c.pathSprites = link + c.pathSprites;
        c.pathImages = link + c.pathImages;
    };

}

