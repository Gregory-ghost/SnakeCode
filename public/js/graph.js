// Отрисовка сцены
function Graph(c = {}) {
    const canvas = c.game.canvas, // холст
        ctx = canvas.getContext('2d'),
        SIZE = c.size, // размеры
        sprites = c.sprites; // Координаты в спрайте


    // Отрисовываем карту
    this.draw = (data = {}) => {
        c.game.block.attr({width: (SIZE.width * SIZE.sizeSnake) + 'px', height: (SIZE.height * SIZE.sizeSnake) + 'px'});

        ctx.fillStyle = "#577ddb";
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

        // Loop over every snake segment
        for (var i=0; i<snake.body.length; i++) {
            var segment = snake.body[i];
            var segx = segment.x;
            var segy = segment.y;
            var tilex = segx*SIZE.sizeSnake;
            var tiley = segy*SIZE.sizeSnake;

            // Sprite column and row that gets calculated
            var tx = 0;
            var ty = 0;

            if (i == 0) {
                // Head; Determine the correct image
                var nseg = snake.body[i+1]; // Next segment
                if (segy < nseg.y) {
                    // Up
                    tx = 3; ty = 0;
                } else if (segx > nseg.x) {
                    // Right
                    tx = 4; ty = 0;
                } else if (segy > nseg.y) {
                    // Down
                    tx = 4; ty = 1;
                } else if (segx < nseg.x) {
                    // Left
                    tx = 3; ty = 1;
                }
            } else if (i == snake.body.length-1) {
                // Tail; Determine the correct image
                var pseg = snake.body[i-1]; // Prev segment
                if (pseg.y < segy) {
                    // Up
                    tx = 3; ty = 2;
                } else if (pseg.x > segx) {
                    // Right
                    tx = 4; ty = 2;
                } else if (pseg.y > segy) {
                    // Down
                    tx = 4; ty = 3;
                } else if (pseg.x < segx) {
                    // Left
                    tx = 3; ty = 3;
                }
            } else {
                // Body; Determine the correct image
                var pseg = snake.body[i-1]; // Previous segment
                var nseg = snake.body[i+1]; // Next segment
                if (pseg.x < segx && nseg.x > segx || nseg.x < segx && pseg.x > segx) {
                    // Horizontal Left-Right
                    tx = 1; ty = 0;
                } else if (pseg.x < segx && nseg.y > segy || nseg.x < segx && pseg.y > segy) {
                    // Angle Left-Down
                    tx = 2; ty = 0;
                } else if (pseg.y < segy && nseg.y > segy || nseg.y < segy && pseg.y > segy) {
                    // Vertical Up-Down
                    tx = 2; ty = 1;
                } else if (pseg.y < segy && nseg.x < segx || nseg.y < segy && pseg.x < segx) {
                    // Angle Top-Left
                    tx = 2; ty = 2;
                } else if (pseg.x > segx && nseg.y < segy || nseg.x > segx && pseg.y < segy) {
                    // Angle Right-Up
                    tx = 0; ty = 1;
                } else if (pseg.y > segy && nseg.x > segx || nseg.y > segy && pseg.x > segx) {
                    // Angle Down-Right
                    tx = 0; ty = 0;
                }
            }

            // Draw the image of the snake part
            /*ctx.drawImage(tileimage, tx*64, ty*64, 64, 64, tilex, tiley,
                32, 32);*/
            let options = {
                xsprite: tx*64,
                ysprite: ty*64,
                x: tilex,
                y: tiley,
            };
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
                ctx.drawImage(sprite, options.xsprite, options.ysprite, 64 ,64, options.x, options.y, SIZE.sizeSnake, SIZE.sizeSnake);
            }, false);

        }
    };


    // Подготовка сцены
    this.init = () => {
        // путь до спрайтов
        let link = location.href;
        link = link.split('?')[0];
        link = link.split('/').slice(0, -1).join('/');
        c.path.sprites = link + c.path.sprites;
        c.path.images = link + c.path.images;
    };
    // Очистка сцены
    this.clear = () => {
        ctx.clearRect(0, 0, (SIZE.width * SIZE.sizeSnake), (SIZE.height * SIZE.sizeSnake));
    };

}

