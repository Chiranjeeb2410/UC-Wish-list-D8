/*
 * js for image slider
 */

var elements = document.getElementsByClassName('wishlist_slider');

for (var i = 0; i < elements.length; i++) {
   var slider = new IdealImageSlider.Slider({
       selector: '#wishlist_slider_' + (i+1),
       height: 150,
       interval: 4000
   });
   slider.start();
}



