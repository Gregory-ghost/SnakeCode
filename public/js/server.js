function Server() {

	this.changeDirection = function (id = 0, direction = 'left') {
		return $.get('api', { method: 'changeDirection', id, direction });
	};
    this.createSnake = function (options) {
        return $.get('api', { method: 'createSnake', ...options });
    };

	this.login = function (options = {}) {
		return $.get('api', { method: 'login', ...options });
	};
	this.register = function (options) {
		return $.get('api', { method: 'register', ...options });
	};
	this.logout = function () {
		return $.get('api', { method: 'logout', ...{}});
	};
	this.getCurrentUser = function() {
		return $.get('api', { method: 'getCurrentUser' });
	};

    this.getScene = function(id = 1) {
        return $.get('api', { method: 'getScene', id });
    };

}


function Struct() {
	this.foods = [];
	this.snakes = [];
	this.maps = [];
	this.snakesBody = [];
	this.user = {};

	this.snake = {};
	this.map = {
		id: 1,
	};

	this.set = function(data = {}) {
		this.foods = data.foods;
        this.snakes = data.snakes;
        this.snakesBody = data.snakesBody;
        for(let i = 0; i < data.snakes.length; i++) {
        	let snake = data.snakes[i];
        	let body = [];
        	for(let y = 0; y < this.snakesBody.length; y++) {
        		let item = this.snakesBody[y];
        		if(snake.id == item.snake_id) {
					body.push({id: item.id, x: item.x, y: item.y});
				}
			}
			snake['body'] = body;
		}
        this.maps = data.maps;
	};

	// Текущий пользователь
	this.setUser = function(user = {}) {
		this.user = user;
	};
	this.destroyUser = function() {
		this.user = {};
	};
	this.getUser = function() {
		return this.user;
	};

	// Активная змейка
    this.setSnake = function(snake = {}) {
        this.snake = snake;
    };
    this.destroySnake = function() {
        this.snake = {};
    };
    this.getSnake = function() {
        return this.snake;
    };

}
