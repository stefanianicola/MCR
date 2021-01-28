$(document).ready(function() {
  // clonar items para el carrousel de marcas  
  var showItemsCarousel = function(widthItems, reset){
    $('.carousel .carousel-item-marcas').each(function(){
      var next = $(this).next();
      if (!next.length) {
        next = $(this).siblings(':first');
      }
      if(reset){
        $(this).children().not(':first-child').remove(); // remover items clonados  
      }
      next.children(':first-child').clone().appendTo($(this));
      var itemsToShow = widthItems >= 1200 ? 4 : 2; // de tablet para arriba mostrar 6 marcas
      for (var i=0;i<itemsToShow;i++) { 
        next=next.next();
        if (!next.length) {
          next = $(this).siblings(':first');
        }
        next.children(':first-child').clone().appendTo($(this));
      }
    });    
  };

    // obtener valor del ancho del sitio
    var widthWeb = $(window).width();
    showItemsCarousel(widthWeb, false);
  
    // ejecutar evento resize para calcular ancho del sitio
    $(window).resize(function() {
      widthWeb = $(window).width();
      showItemsCarousel(widthWeb, true);
    });

    

});