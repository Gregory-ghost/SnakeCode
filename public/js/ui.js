// Пользовательский интерфейс

function UI() {

    let c = {
        loginPage: $('.loginPage'),
        gamePage: $('.gamePage'),
        registerPage: $('.registerPage'),
        profilePage: $('.profilePage'),

        gameWrapper: $('#game_wrapper'),
        loginForm: $('#loginForm'),
        registerForm: $('#registerForm'),

        startGameBtn: $('.startGameBtn'),
        stopGameBtn: $('.stopGameBtn'),
        logoutBtn: $('.logoutBtn'),
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
            case 'ProfilePage':
                c.gameWrapper.find('.page').addClass('hidden');
                c.profilePage.removeClass('hidden');

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
        c.loginForm.on("submit", (event) => {
            event.preventDefault();
            callback({
                login: c.loginForm.find("input[name='login']").val(),
                password: c.loginForm.find("input[name='password']").val(),
            });
        });
    };

    this.handleClickRegisterBtn = (callback) => {
        c.loginPage.find('.registerLink').bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };

    this.handleRegister = (callback) => {
        c.registerForm.on("submit", (event) => {
            event.preventDefault();
            callback({
                name: c.registerForm.find("input[name='name']").val(),
                login: c.registerForm.find("input[name='login']").val(),
                password: c.registerForm.find("input[name='password']").val(),
            });
        });
    };

    this.handleClickLoginBtn = (callback) => {
        c.registerPage.find('.loginLink').bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };
    this.handleClickStartGameBtn = (callback) => {
        c.startGameBtn.bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };
    this.handleClickStopGameBtn = (callback) => {
        c.stopGameBtn.bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };

    this.handleClickLogoutBtn = (callback) => {
        c.logoutBtn.bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };

    this.showMessage = (msg) => {
        if(msg) {
            console.log(msg);
            //alert(msg)
        }
    };

}