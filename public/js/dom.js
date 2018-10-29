// Обработка нажатий с клиента

function DOM() {


    this.handleArrowKeys = function(callback) {
        $(window).bind({
            keydown: function(e) {
                if (!e) {
                    e = window.event;
                }
                let key = (e.which) ? e.which : e.keyCode;

                if(key == 37 || key == 65) {
                    callback("left");
                } else if(key == 39 || key == 68) {
                    callback("right");
                } else if(key == 38 || key == 87) {
                    callback("up");
                } else if(key == 40 || key == 83) {
                    callback("down");
                }
            },
        })
    }

}