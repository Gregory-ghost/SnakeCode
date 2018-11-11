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
	this.struct = {
		foods: [],
		snakes: [],
		maps: [],
		snakesBody: [],
		user: {},

		snake: {},
		map: {
			id:1,
		},
	};

	this.set = function(data = {}) {
		let s = this.struct;
		s.foods = data.foods;
        s.snakes = data.snakes;
        s.snakesBody = data.snakesBody;
        for(let i = 0; i < s.snakes.length; i++) {
        	let snake = s.snakes[i];
        	let body = [];
        	for(let y = 0; y < s.snakesBody.length; y++) {
                let item = s.snakesBody[y];
                if(snake.id == item.snake_id) {
                    body.push({id: item.id, x: item.x, y: item.y});
				}
			}
			snake['body'] = body;
		}
        s.maps = data.maps;
	};

	this.get = function(data = {}) {
		return this.struct;
	};

	// Текущий пользователь
	this.setUser = function(user = {}) {
		this.struct.user = user;
	};
	this.destroyUser = function() {
		this.struct.user = {};
	};
	this.getUser = function() {
		return this.struct.user;
	};

	// Активная змейка
    this.setSnake = function(snake = {}) {
        this.struct.snake = snake;
    };
    this.destroySnake = function() {
        this.struct.snake = {};
    };
    this.getSnake = function() {
        return this.struct.snake;
    };


    this.getSnakes = function() {
        return this.struct.snakes;
    };
    this.getMap = function() {
        return this.struct.map;
    };

}
