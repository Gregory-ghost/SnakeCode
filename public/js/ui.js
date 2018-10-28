// Отрисовка сцены

function UI() {
    let canvas = document.getElementById('game'),
        ctx = canvas.getContext('2d');

    let SIZE = {
        w: 900,
        h: 420,
        padding: 10,
        spriteW: 64,
        spriteH: 64,
        px: 'px',
    };

    let c = {
        game: $('#game'),
        pathSprites: '/public/img/sprite/',
        pathImages: '/public/img/',
    };
    let sprites = {};


    this.draw = (data = {}) => {
        w = data.map.sizeX;
        h = data.map.sizeY;
        c.game.attr({width: SIZE.w + SIZE.px, height: SIZE.h + SIZE.px});

        this.setSpritesPath();
        sprites = this.loadImages();
        this.drawMap();

        this.drawSnakes(data.snakes);
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

    this.drawMap = () => {
        // Отрисовываем карту

        let sprite = new Image();
        sprite.src = c.pathImages + 'bg2.png';
        sprite.addEventListener("load", function(){ ctx.drawImage(sprite, 0, 0, SIZE.w, SIZE.h)}, false);
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
            debugger;
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
                ctx.drawImage(sprite, options.xsprite, options.ysprite, SIZE.spriteW ,SIZE.spriteH, options.x+SIZE.padding, options.y+SIZE.padding, SIZE.spriteW, SIZE.spriteH);
            }, false);

        }
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

