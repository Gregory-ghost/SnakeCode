// Скрипт клиента
// Питон / Змея
// ПИ 21

$(document).ready(async () => {
    const UPDATE_SCENE_INTERVAL = 100; // Интервал обращений к серверу в сек

	const server = new Server();
	const ui = new UI();
	const graph = new Graph();
	const struct = new Struct();


    function F1(callbacks) {
        onInit = callbacks.onInit;
        onMove = callbacks.onMove;
        onGetScene = callbacks.onGetScene;


        onInit();
        ui.handleArrowKeys(onMove);
        var updateScene = setInterval(getScene(onGetScene), UPDATE_SCENE_INTERVAL * 1000);
        getScene(onGetScene);
    }

    const f1 = new F1({

        onInit: () => {
            // Создание пользователя
            struct.createUser(new User("Вася"));
            graph.init();
        },
        onMove: async (direction) => {
            // Нажатие на клавишу
            if(direction) {
                await server.changeDirection(struct.user.id, direction);
            }
        },
        onGetScene: (result = false) => {
            // Получение сцены
            if (result) {
                graph.draw(struct);
            }
        },
    });



	async function getScene(callback) {
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


});
