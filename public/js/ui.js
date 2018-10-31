// Пользовательский интерфейс

function UI() {

    let c = {
        gameWrapper: $('#game_wrapper'),
        loginPage: $('.loginPage'),
        registerPage: $('.registerPage'),
        gamePage: $('.gamePage'),
        loginForm: $('#loginForm'),
    };


    this.Router = (page = '', callback) => {
        switch(page) {
            case 'LoginPage':
                c.gameWrapper.find('.page').addClass('hidden');
                c.loginPage.removeClass('hidden');

                break;
            case 'RegisterPage':
                c.gameWrapper.find('.page').addClass('hidden');
                c.registerPage.removeClass('hidden');

                break;
            case 'GamePage':
                c.gameWrapper.find('.page').addClass('hidden');
                c.gamePage.removeClass('hidden');

                break;
        }
        callback(page);
    };

    this.handleArrowKeys = function (callback) {
        $(window).bind({
            keydown: function (e) {
                if (!e) {
                    e = window.event;
                }
                let key = (e.which) ? e.which : e.keyCode;

                if (key == 37 || key == 65) {
                    callback("left");
                } else if (key == 39 || key == 68) {
                    callback("right");
                } else if (key == 38 || key == 87) {
                    callback("up");
                } else if (key == 40 || key == 83) {
                    callback("down");
                }
            },
        })
    };

    this.handleLogin = (callback) => {
        c.loginForm.on("submit", function (event) {
            event.preventDefault();
            callback({
                login: $("#inputLogin").val(),
                password: $("#inputPassword").val(),
            });

            $.ajax({
                type: "POST",
                url: "/api/index.php",
                data: {
                    login: $("#inputLogin").val(),
                    password: $("#inputPassword").val(),
                    method: 'auth',
                },
                dataType: "json",
                success: (data) => {
                    callback(data)
                },
                error: () => {
                    callback("error")
                }
            });
        });
    }

}