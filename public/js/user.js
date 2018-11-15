function User(callbacks) {

	// Описание колбеков
    onSwitchPage = callbacks.onSwitchPage;
    onLogin = callbacks.onLogin;
    onRegister = callbacks.onRegister;
    onLogout = callbacks.onLogout;

    let c = {
        loginPage: $('.loginPage'),
        gamePage: $('.gamePage'),
        registerPage: $('.registerPage'),
        profilePage: $('.profilePage'),

        loginForm: $('#loginForm'),
        registerForm: $('#registerForm'),

        logoutBtn: $('.logoutBtn'),
    };

    // Инициализация модуля
    this.init = () => {
    	this.handleLogin(onLogin);
        this.handleRegister(onRegister);
    	this.handleClickLoginBtn(() => {
    		onSwitchPage('RegisterPage');
		});
    	this.handleClickRegisterBtn(() => {
    		onSwitchPage('LoginPage');
		});
    	this.handleClickLogoutBtn(onLogout);
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

    this.handleClickLogoutBtn = (callback) => {
        c.logoutBtn.bind("click", (event) => {
            event.preventDefault();
            callback();
            return false;

        })
    };


}