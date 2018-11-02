function Server() {

	this.changeDirection = function (id = 0, direction = 'left') {
		return $.get('api', { method: 'changeDirection', id, direction });
	};
	this.login = function (options = {}) {
		return $.get('api', { method: 'login', ...options });
	};
	this.register = function (options) {
		return $.get('api', { method: 'register', ...options });
	};
	this.getCurrentUser = function() {
		return $.get('api', { method: 'getCurrentUser' });
	};

    this.getScene = function() {
        return $.get('api', { method: 'getScene' });
    };

}


function User(options = {}) {
	this.login = options.login;

}

function Struct() {
	this.foods = [];
	this.snakes = [];
	this.map = [];
	this.snakesBody = [];
	this.user = {};

	this.set = function(data = {}) {
		this.foods = data.foods;
        this.snakes = data.snakes;
        this.snakesBody = data.snakesBody;
        this.map = data.map;
	};

	this.setUser = function(user = {}) {
		this.user.login = user.login;
	};

	this.getUser = function() {
		return this.user;
	}
}
