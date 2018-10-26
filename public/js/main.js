// Скрипт клиента
// Питон / Змея
// ПИ 21

$(document).ready(async () => {

	const server = new Server();
	const dom = new DOM();
	const ui = new UI();
	const struct = new Struct();

	// Создание пользователя
	struct.createUser(new User("Вася"));


	dom.handleArrowKeys(async (direction = 'left') => {
        // Отлавливание нажатий
        if(direction) {
            const answer = await server.changeDirection(struct.user.id, direction);
            getAnswer(answer, (result) => {
                if(result) {
                    //ui.draw(struct);
                }
			});
    	}
	});

	// Обновление сцены
	updateScene((result = false) => {
		if(result) {
			ui.draw(struct);
		}

	async function updateScene(callback) {
        const answer = await server.getScene();
        getAnswer(answer, callback);
	}

	function getAnswer(answer = {}, callback) {
        if(answer.result) {
            struct.set(answer.data);
            callback(answer.result);
        } else {
            error(answer.error);
            callback(answer.result);
        }
	}

	function error(error = "") {
		console.log("Ошибка :: %s", error)
	}


	/*const answer = await server.changeDirection(12, 'left');

	if(answer.result) {
		console.log(answer.data);
	}*/


});
