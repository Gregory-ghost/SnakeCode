// Отрисовка сцены

function UI() {
    let $canvas = document.getElementById('game'),
        ctx = $canvas.getContext('2d'),
        w = 350,
        h = 350,
        snakeSize = 10;


    this.draw = (data = {}) => {
        w = data.map.sizeX;
        h = data.map.sizeY;

        this.drawMap();

        this.drawSnakes(data.snakes);


    };

    this.drawMap = () => {
        // Отрисовываем карту
        ctx.fillStyle = 'lightgrey';
        ctx.fillRect(0, 0, w, h);

        ctx.strokeStyle = 'black';
        ctx.strokeRect(0, 0, w, h);
    };

    this.drawSnakes = (snakes = {}) => {
        // Отрисовываем всех змей
            for(let i = 0; i < snakes.length; i++) {
            this.drawSnake(snakes[i]);
        }
    };

    this.drawSnake = (snake = {}) => {
        // Рисование змеи
        let body = snake.body;
        for(let i = 0; i < body.length; i++) {
            let x = body[i].x,
                y = body[i].y;
            ctx.fillStyle = 'green';
            ctx.fillRect(x*snakeSize, y*snakeSize, snakeSize, snakeSize);
            // This is the border of the square
            ctx.strokeStyle = 'darkgreen';
            ctx.strokeRect(x*snakeSize, y*snakeSize, snakeSize, snakeSize);
        }
    }
}

