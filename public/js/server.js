function Server() {

	this.changeDirection = function (id, direction) {
		return $.get('api', { method: 'changeDirection', id, direction });
	};
	
}

function Struct() {
	this.foods = [];
	this.snakes = [];
	this.maps = {};
	this.user = [];


}

function Food(food) {
	this.
}