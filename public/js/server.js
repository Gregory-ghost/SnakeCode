function Server() {
	
	this.changeDirection = function (id, direction) {
		return $.get('api', { method: 'changeDirection', id, direction });
	}
	
}