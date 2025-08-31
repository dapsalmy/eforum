
$(document).ready(function () {

    "use strict";

      /* --------------------------------------------------
       * back to top
       * --------------------------------------------------*/
      var scrollTrigger = 500; // px
      var t = 0;
      function backToTop() {
          var scrollTop = $(window).scrollTop();
          if (scrollTop > scrollTrigger) {
              $('#back-to-top').addClass('show');
              $('#back-to-top').removeClass('hide');
              t = 1;
          }

          if (scrollTop < scrollTrigger && t==1) {
              $('#back-to-top').addClass('hide');
          }

      };

	  $(document).on('click', '#back-to-top', function() {
          $("html, body").animate({ scrollTop: 0 }, 600);
          return false;
      });



      /* ==========================================================================
      When document is Scrolling, do
      ========================================================================== */

      $(window).on('scroll', function() {
          backToTop();
      });


      /* ==========================================================================
      Dark/Light Switcher
      ========================================================================== */

		//Switch Function
		const switchTheme = () => {
			//Get root element and data-theme value
			const rootElem = document.documentElement
			let dataTheme = rootElem.getAttribute('data-theme'),
			newTheme

			newTheme = (dataTheme === 'light') ? 'dark' : 'light'

			//Set the new HTML attribute
			rootElem.setAttribute('data-theme', newTheme)

			//Set the new local storage item
			localStorage.setItem('theme', newTheme)

			if(dataTheme === 'light'){
				$('#switcher-icon').removeClass('bi bi-moon');
				$('#switcher-icon').addClass('bi bi-sun');
				$('#trumbowyg-change').removeClass('trumbowyg-light');
				$('#trumbowyg-change').addClass('trumbowyg-dark');

			} else if(dataTheme === 'dark'){
				$('#switcher-icon').removeClass('bi bi-sun');
				$('#switcher-icon').addClass('bi bi-moon');
				$('#trumbowyg-change').removeClass('trumbowyg-dark');
				$('#trumbowyg-change').addClass('trumbowyg-light');
			}
		}

		//Add event listener for the theme switcher
		document.querySelector('#theme-switcher').addEventListener('click', switchTheme);

		/* ==========================================================================
		AOS Animations
		========================================================================== */
		AOS.init({
			duration: 1000,
			once: true
		});


		/* ==========================================================================
		Tooltip
		========================================================================== */
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        /*============================================
        Alert Fade
        ==============================================*/

        $(".danger-alert").fadeIn(1000).delay(5000).fadeOut(700);
        $(".alert-warning").fadeIn(1000).delay(5000).fadeOut(700);
        $(".alert-danger").fadeIn(1000).delay(5000).fadeOut(700);
        $(".alert-success").fadeIn(1000).delay(5000).fadeOut(700);

        /*============================================
        Check length of Description
        ==============================================*/
        $("#description").on('keyup',function(){
            var characters = $(this).val().length;
            var maxlen = 400;
            var countlast = Math.ceil(maxlen - characters) ;

            if (characters>maxlen){

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'This field text is too long!',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    },
                    onOpen: function() {
                        var maanlms = document.getElementById("myAudio");
                        maanlms.play();
                    }
                })

                $('#addpost_btn, #editpost_btn').prop("disabled",true);

                $("#count").css("color","red").css("background","yellow");

            }else{
                $('#addpost_btn, #editpost_btn').prop("disabled",false);
                $("#count").css("color","black");
                $("#count").css("background","#D4FCF6");
            }
            $("#count").text("Characters: " + countlast );

        });

		/* ==========================================================================
		Search Toggle
		========================================================================== */
        let searchblock = document.querySelector(".search-block"),
            searchoverlay = document.querySelector(".search-overlay"),
            search = document.querySelectorAll('[data-toggle="search"]'),
            searchclose = document.querySelectorAll('[data-toggle="search-close"]');
        search.forEach(search => {
            search.addEventListener("click", function() {
                searchblock.classList.add("is-visible"), searchoverlay.classList.add("is-visible"), setTimeout(() => {
                    document.querySelector('[aria-label="search-query"]').focus()
                }, 250)
            })
        }), searchclose.forEach(search => {
            search.addEventListener("click", function() {
                searchblock.classList.remove("is-visible"), searchoverlay.classList.remove("is-visible"), document.querySelector('[aria-label="search-query"]').value = ""
            })
        })



  });
