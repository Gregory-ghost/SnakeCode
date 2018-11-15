// Отрисовка сцены



function Graph() {
    let canvas = document.getElementById('game'),
        ctx = canvas.getContext('2d');

    let SIZE = {
        width: 14,
        height: 7,
        sizeSnake: 64,
        px: 'px',
    };

    let c = {
        game: $('#game'),
        gameWrapper: $('#game_wrapper'),
        pathSprites: '/public/img/sprite/',
        pathImages: '/public/img/',
        maps: $('.mapsBlock')
    };
    let sprites = {};

    this.init = () => {
        this.setSpritesPath();
        sprites = this.loadImages();
    };

    // Отрисовываем карты
    this.initMaps = (maps) => {
        $.each(maps, (i, map) => {
            c.maps.append(getMapTemplate(map));
        });
    };
    this.getMapTemplate = (map) => {
        return '<div class="map_item">Карта номер '+map.id+'</div>';
    };

    this.output = (c, txt) => {
        $('.' + c).html(txt);
    };

    // Отрисовываем карту
    this.draw = (data = {}) => {
        // SIZE.width = data.maps[0].width;
        // SIZE.height = data.maps[0].height;
        // SIZE.sizeSnake = data.maps[0].sizeSnake;
        c.game.attr({width: (SIZE.width * SIZE.sizeSnake) + SIZE.px, height: (SIZE.height * SIZE.sizeSnake) + SIZE.px});

        this.clear();
        this.drawMap();

        this.drawSnakes(data.snakes);
    };

    this.clear = () => {
        // Очистка сцены
        ctx.clearRect(0, 0, (SIZE.width * SIZE.sizeSnake), (SIZE.height * SIZE.sizeSnake));
    };

    this.drawMap = () => {
        // Отрисовываем карту

        let sprite = new Image();
        sprite.src = c.pathImages + 'bg2.png';
        sprite.addEventListener("load", function(){ ctx.drawImage(sprite, 0, 0, (SIZE.width * SIZE.sizeSnake), (SIZE.height * SIZE.sizeSnake))}, false);
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

        debugger;
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

    this.drawFoods = (foods = {}) => {
        // Отрисовываем всех змей
        for(let i = 0; i < foods.length; i++) {
            this.drawSnake(foods[i]);
        }
    };

    this.drawFood = (food = {}) => {
        let x = food.x,
            y = food.y,
            value = food.value,
            type = food.type,
            map_id = food.map_id;

        // Cubic curves example
        ctx.beginPath();
        ctx.moveTo(75,40);
        ctx.bezierCurveTo(75,37,70,25,50,25);
        ctx.bezierCurveTo(20,25,20,62.5,20,62.5);
        ctx.bezierCurveTo(20,80,40,102,75,120);
        ctx.bezierCurveTo(110,102,130,80,130,62.5);
        ctx.bezierCurveTo(130,62.5,130,25,100,25);
        ctx.bezierCurveTo(85,25,75,37,75,40);
        ctx.fill();
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

