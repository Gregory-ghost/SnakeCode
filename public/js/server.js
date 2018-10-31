function Server() {

	this.changeDirection = function (id = 0, direction = 'left') {
		return $.get('api', { method: 'changeDirection', id, direction });
	};
	this.login = function (options) {
		return $.get('api', { method: 'login', options });
	};
	this.register = function (options) {
		return $.get('api', { method: 'register', options });
	};

	this.getScene = function() {
		return $.get('api', { method: 'getScene' });
	};

}


function User(options = {}) {
	this.name = options.name;
	this.id = createId();

	function createId() {
		return 1;
	}
}

function Struct() {
	this.foods = [];
	this.snakes = [];
	this.map = {};
	this.user = {};

	this.set = function(data = {}) {
        let $this = this;
		this.foods = data.foods;
        this.snakes = data.snakes;
        this.map = data.map;
	};

	this.setUser = function(user = {}) {
		this.user.name = user.name;
		this.user.login = user.login;
		this.user.id = user.id;
	};

	this.getUser = function() {
		return this.user;
	}
}
