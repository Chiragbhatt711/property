jQuery(document).ready(function($) {
  $('.app_slider').slick({
    dots: false,
    arrows:true,
    infinite: true,
    autoplay: true,
    centerMode: true,
    centerPadding: '0',
    slidesToShow: 3,
    slidesToScroll: 1,    
    responsive: [
      {
        breakpoint:991,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1
        }
      },
      {
      breakpoint:767,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1,        
      }
    },
    {
       breakpoint: 479,
       settings: {          
          arrows: true,
          slidesToShow: 1,
          centerPadding: '60px',
          slidesToScroll: 1
       }
    }]
});
});
