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
    ui.init();

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

    // Движение змеи
    moveSnake(struct.user.id, (result = false) => {
        if (result) {
            debugger;
            ui.draw(struct);
        }
    });

	// Обновление сцены
	updateScene((result = false) => {
        if (result) {
            ui.draw(struct);
        }
    });


	async function updateScene(callback) {
        const answer = await server.getScene();
        getAnswer(answer, callback);
	}

	async function moveSnake(id, callback) {
        const answer = await server.moveSnake(id);
        getAnswer(answer, callback);
	}

	function getAnswer(answer = {}, callback) {
        if(answer.result) {
            struct.set(answer.data);
            callback(answer.result);
            debugger;
        } else {
            error(answer.error);
            callback(answer.result);
        }
	}

	function error(error = "") {
		console.log("Ошибка :: %s", error)
	}


});
