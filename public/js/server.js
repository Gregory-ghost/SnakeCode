function Server() {

	// Ключ пользователя
	let token,
		map_id;

	// Обращения к серверу
	this.changeDirection = function (direction = 'left') {
		return $.get('api', { method: 'changeDirection', direction, token });
	};
    this.getMaps = function () {
        return $.get('api', { method: 'getMaps', token });
    };
    this.startGame = function (options) {
        return $.get('api', { method: 'startGame', ...options, ...token });
    };

	this.login = function (options = {}) {
		return $.get('api', { method: 'login', ...options });
	};
	this.register = function (options) {
		return $.get('api', { method: 'register', ...options });
	};
	this.logout = function () {
		return $.get('api', { method: 'logout'});
	};
    this.getScene = function(id = 1) {
        return $.get('api', { method: 'getScene', id });
    };

    // Токены
    this.setToken = (uToken = '') => {
    	token = uToken;
	};
	this.getToken = () => {
    	return token;
	};

}