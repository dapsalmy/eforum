
$(document).ready(function () {

    "use strict";

    /* ==========================================================================
    ApexChart Line Graph
    ========================================================================== */
	$('.carousel-categories').owlCarousel({
		mouseDrag: true,
		touchDrag: true,
		dots: true,
		loop: true,
		autoplay: false,
		smartSpeed: 600,
		margin: 30,
		autoHeight: true,
		responsive: {
			0 : {
				items: 1,
			},
			576 : {
				items: 2,
			},
			768 : {
				items: 2,
				margin: 30,
			},
			992 : {
				items: 3,
				margin: 30,
			},
			1200 : {
				items: 3,
				margin: 40,
				mouseDrag: false,
			},
		}
	});	

	$('.carousel-users').owlCarousel({
		mouseDrag: true,
		touchDrag: true,
		dots: true,
		loop: true,
		autoplay: false,
		smartSpeed: 600,
		margin: 30,
		autoHeight: true,
		responsive: {
			0 : {
				items: 1,
			},
			576 : {
				items: 2,
			},
			768 : {
				items: 2,
				margin: 30,
			},
			992 : {
				items: 3,
				margin: 30,
			},
			1200 : {
				items: 3,
				margin: 40,
				mouseDrag: false,
			},
			2000 : {
				items: 4,
				margin: 40,
				mouseDrag: false,
			},
		}
	});	

	/*==============================
	Navigation
	==============================*/
	$('.carousel-nav-prev').on('click', function() {
		var carouselId = $(this).attr('data-nav');
		$(carouselId).trigger('prev.owl.carousel');
	});
	$('.carousel-nav-next').on('click', function() {
		var carouselId = $(this).attr('data-nav');
		$(carouselId).trigger('next.owl.carousel');
	});

  
});