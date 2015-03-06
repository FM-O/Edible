/**
 * Created by flo on 06/03/15.
 */
(function(){

    var items = document.getElementsByClassName("item"),
        itemsLength = items.length;

    for (var i = 0 ; i < itemsLength ; i++) {
        items[i].addEventListener('click', function(e) {
            e.preventDefault();
            if (this.className == "item") {
                this.className = "item active";
            } else {
                this.className = "item";
            }

        }, false);
    }
})();

(function(){
    var cross = document.getElementsByClassName('cross'),
        crossLength = cross.length;
        main_scanko = document.getElementById('main_scanko'),
        main_scanok = document.getElementById('main_scanok'),
        main = document.getElementById('main');

    for (var i = 0 ; i < crossLength ; i++){
        cross[i].addEventListener('click', function(e) {
            e.preventDefault();
            if (main_scanko.style.display == "block") {
                main_scanko.style.display = "none";
                main.style.display = "block";
            } else if (main_scanok.style.display == "block") {
                main_scanok.style.display = "none";
                main.style.display = "block";
            }
        }, false);
    }
})();