function Server() {

	this.changeDirection = function (id = 0, direction = 'left') {
		return $.get('api', { method: 'changeDirection', id, direction });
	};

	this.getScene = function() {
		return $.get('api', { method: 'getScene' });
	};

}


function User(options = {}) {
	this.name = options.name;
	this.id = createId();

	function createId() {
		return 12;
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

	this.createUser = function(user = {}) {
		this.user.name = user.name;
		this.user.id = user.id;
	};

	this.getUser = function() {
		return this.user;
	}
}
