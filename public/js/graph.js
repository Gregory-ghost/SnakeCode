// Отрисовка сцены
function Graph(c = {}) {
    const canvas = c.game.canvas, // холст
        ctx = canvas.getContext('2d'),
        SIZE = c.size, // размеры
        sprites = c.sprites; // Координаты в спрайте

    var spriteImage = null, // изображения
        initialized = false, // подготовлено
        gameover = false, // конец игры
        preloaded = false; // предзагрузка

    // Структура
    var snakes = {},
        foods = {};

    // #d5cbdf - food
    // #a1887f - snake

    // Отрисовываем карту
    this.draw = () => {
        window.requestAnimationFrame(this.draw);

        if(!initialized) {
            // Preloader

            c.game.block.attr({width: (SIZE.width * SIZE.sizeSnake) + 'px', height: (SIZE.height * SIZE.sizeSnake) + 'px'});
            // Clear the canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            if(preloaded) {
                initialized = true;
            }
        } else {
            this.render();
        }


    };

    // обновление данных
    this.updateData = (data = {}) => {
      snakes = data.snakes;
      foods = data.foods;
    };

    this.render = () => {
        // Рисование фона
        ctx.fillStyle = "#577ddb";
        this.clear();

        this.drawMap();
        this.drawFoods();
        this.drawSnakes();

        // Game over
        if (gameover) {
            ctx.fillStyle = "rgba(0, 0, 0, 0.5)";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }
    };


    // Отрисовываем карту
    this.drawMap = () => {
        ctx.drawImage(spriteImage[1], 0, 0, (SIZE.width * SIZE.sizeSnake), (SIZE.height * SIZE.sizeSnake))
    };

    this.drawSnakes = () => {
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

            let options = {
                x: tilex,
                y: tiley,
            };

            if (i == 0) {
                // Head; Determine the correct image
                var nseg = snake.body[i+1]; // Next segment
                if (segy < nseg.y) {
                    // Up
                    options.xsprite = sprites.head.up[0];
                    options.ysprite = sprites.head.up[1];
                } else if (segx > nseg.x) {
                    // Right
                    options.xsprite = sprites.head.right[0];
                    options.ysprite = sprites.head.right[1];
                } else if (segy > nseg.y) {
                    // Down
                    options.xsprite = sprites.head.down[0];
                    options.ysprite = sprites.head.down[1];
                } else if (segx < nseg.x) {
                    // Left
                    options.xsprite = sprites.head.left[0];
                    options.ysprite = sprites.head.left[1];
                }
            } else {
                options.xsprite = sprites.body.lineHoriz[0];
                options.ysprite = sprites.body.lineHoriz[1];
            }

            // Draw the image of the snake part
            /*ctx.drawImage(tileimage, tx*64, ty*64, 64, 64, tilex, tiley,
                32, 32);*/

            this.drawSprite(options);
        }
    };

    // Рисование еды
    this.drawFoods = () => {
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
            ctx.drawImage(spriteImage[0], options.xsprite, options.ysprite, 205 , 205, options.x, options.y, SIZE.sizeSnake, SIZE.sizeSnake);
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

        spriteImage = this.loadImages([c.path.sprites + 'sprites2.png', c.path.images + 'bg2.png']);

        this.draw();

    };
    // Очистка сцены
    this.clear = () => {
        ctx.clearRect(0, 0, (SIZE.width * SIZE.sizeSnake), (SIZE.height * SIZE.sizeSnake));
    };


    // Прогрузка изображений
    this.loadImages = (imagefiles) => {
        // Load the images
        var loadedimages = [];
        for (var i=0; i<imagefiles.length; i++) {
            // Create the image object
            var image = new Image();

            // Add onload event handler
            image.onload = function () {
                preloaded = true;
            };

            // Set the source url of the image
            image.src = imagefiles[i];

            // Save to the image array
            loadedimages[i] = image;
        }

        // Return an array of images
        return loadedimages;
    }

}

