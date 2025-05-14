jQuery(function ($) {
  "use strict";

  /* ----------------------------------------------------------- */
  /*  Fixed header
	/* ----------------------------------------------------------- */
  $(window).on("scroll", function () {
    // fixedHeader on scroll
    function fixedHeader() {
      var headerTopBar = $(".top-bar").outerHeight();
      var headerOneTopSpace = $(".header-one .logo-area").outerHeight();

      var headerOneELement = $(".header-one .site-navigation");
      var headerTwoELement = $(".header-two .site-navigation");

      if ($(window).scrollTop() > headerTopBar + headerOneTopSpace) {
        $(headerOneELement).addClass("navbar-fixed");
        $(".header-one").css("margin-bottom", headerOneELement.outerHeight());
      } else {
        $(headerOneELement).removeClass("navbar-fixed");
        $(".header-one").css("margin-bottom", 0);
      }
      if ($(window).scrollTop() > headerTopBar) {
        $(headerTwoELement).addClass("navbar-fixed");
        $(".header-two").css("margin-bottom", headerTwoELement.outerHeight());
      } else {
        $(headerTwoELement).removeClass("navbar-fixed");
        $(".header-two").css("margin-bottom", 0);
      }
    }
    fixedHeader();

    // Count Up
    function counter() {
      var oTop;
      if ($(".counterUp").length !== 0) {
        oTop = $(".counterUp").offset().top - window.innerHeight;
      }
      if ($(window).scrollTop() > oTop) {
        $(".counterUp").each(function () {
          var $this = $(this),
            countTo = $this.attr("data-count");
          $({
            countNum: $this.text()
          }).animate(
            {
              countNum: countTo
            },
            {
              duration: 1000,
              easing: "swing",
              step: function () {
                $this.text(Math.floor(this.countNum));
              },
              complete: function () {
                $this.text(this.countNum);
              }
            }
          );
        });
      }
    }
    counter();

    // scroll to top btn show/hide
    function scrollTopBtn() {
      var scrollToTop = $("#back-to-top"),
        scroll = $(window).scrollTop();
      if (scroll >= 50) {
        scrollToTop.fadeIn();
      } else {
        scrollToTop.fadeOut();
      }
    }
    scrollTopBtn();
  });

  $(document).ready(function () {
    // navSearch show/hide
    function navSearch() {
      $(".nav-search").on("click", function () {
        $(".search-block").fadeIn(350);
      });
      $(".search-close").on("click", function () {
        $(".search-block").fadeOut(350);
      });
    }
    navSearch();

    // navbarDropdown
    function navbarDropdown() {
      if ($(window).width() < 992) {
        $(".site-navigation .dropdown-toggle").on("click", function () {
          $(this).siblings(".dropdown-menu").animate(
            {
              height: "toggle"
            },
            300
          );
        });

        var navbarHeight = $(".site-navigation").outerHeight();
        $(".site-navigation .navbar-collapse").css(
          "max-height",
          "calc(100vh - " + navbarHeight + "px)"
        );
      }
    }
    navbarDropdown();

    // back to top
    function backToTop() {
      $("#back-to-top").on("click", function () {
        $("#back-to-top").tooltip("hide");
        $("body,html").animate(
          {
            scrollTop: 0
          },
          800
        );
        return false;
      });
    }
    backToTop();

    // banner-carousel
    function bannerCarouselOne() {
      $(".banner-carousel.banner-carousel-1").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        dots: true,
        speed: 600,
        arrows: true,
        prevArrow:
          '<button type="button" class="carousel-control left" aria-label="carousel-control"><i class="fas fa-chevron-left"></i></button>',
        nextArrow:
          '<button type="button" class="carousel-control right" aria-label="carousel-control"><i class="fas fa-chevron-right"></i></button>'
      });
      $(".banner-carousel.banner-carousel-1").slickAnimation();
    }
    bannerCarouselOne();

    // banner Carousel Two
    function bannerCarouselTwo() {
      $(".banner-carousel.banner-carousel-2").slick({
        fade: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        dots: false,
        speed: 600,
        arrows: true,
        prevArrow:
          '<button type="button" class="carousel-control left" aria-label="carousel-control"><i class="fas fa-chevron-left"></i></button>',
        nextArrow:
          '<button type="button" class="carousel-control right" aria-label="carousel-control"><i class="fas fa-chevron-right"></i></button>'
      });
    }
    bannerCarouselTwo();

    // pageSlider
    function pageSlider() {
      $(".page-slider").slick({
        fade: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        dots: false,
        speed: 600,
        arrows: true,
        prevArrow:
          '<button type="button" class="carousel-control left" aria-label="carousel-control"><i class="fas fa-chevron-left"></i></button>',
        nextArrow:
          '<button type="button" class="carousel-control right" aria-label="carousel-control"><i class="fas fa-chevron-right"></i></button>'
      });
    }
    pageSlider();

    // Shuffle js filter and masonry
    function projectShuffle() {
      if ($(".shuffle-wrapper").length !== 0) {
        var Shuffle = window.Shuffle;
        var myShuffle = new Shuffle(
          document.querySelector(".shuffle-wrapper"),
          {
            itemSelector: ".shuffle-item",
            sizer: ".shuffle-sizer",
            buffer: 1
          }
        );
        $('input[name="shuffle-filter"]').on("change", function (evt) {
          var input = evt.currentTarget;
          if (input.checked) {
            myShuffle.filter(input.value);
          }
        });
        $(".shuffle-btn-group label").on("click", function () {
          $(".shuffle-btn-group label").removeClass("active");
          $(this).addClass("active");
        });
      }
    }
    projectShuffle();

    // testimonial carousel
    function testimonialCarousel() {
      $(".testimonial-slide").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        speed: 600,
        arrows: false
      });
    }
    testimonialCarousel();

    // Executive carousel
    $(document).ready(function () {
      $(".executive-carousel").owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        responsive: {
          0: {
            items: 1
          },
          576: {
            items: 2
          },
          992: {
            items: 3
          }
        }
      });
    });

    // team carousel
    function teamCarousel() {
      $(".team-slide").slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 2,
        arrows: true,
        prevArrow:
          '<button type="button" class="carousel-control left" aria-label="carousel-control"><i class="fas fa-chevron-left"></i></button>',
        nextArrow:
          '<button type="button" class="carousel-control right" aria-label="carousel-control"><i class="fas fa-chevron-right"></i></button>',
        responsive: [
          {
            breakpoint: 992,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 3
            }
          },
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2
            }
          },
          {
            breakpoint: 481,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });
    }
    teamCarousel();

    // media popup
    function mediaPopup() {
      $(".gallery-popup").colorbox({
        rel: "gallery-popup",
        transition: "slideshow",
        innerHeight: "500"
      });
      $(".popup").colorbox({
        iframe: true,
        innerWidth: 600,
        innerHeight: 400
      });
    }
    mediaPopup();
  });
});

// Animation Initialization for all page elements
function initPageAnimations() {
  // Hero section animation
  const heroContainer = document.querySelector(".hero-container");
  const heroTitle = document.querySelector(".hero-hd-title");
  const heroSubtitle = document.querySelector(".hero-shd-title");
  
  if (heroContainer) {
    // Immediately show the hero section without waiting for intersection
    setTimeout(() => {
      heroContainer.classList.add("show");
      if (heroTitle) heroTitle.classList.add("show");
      if (heroSubtitle) heroSubtitle.classList.add("show");
    }, 300);
  }

  // Service boxes animation
  const serviceBoxes = document.querySelectorAll(".ts-service-box");
  if (serviceBoxes.length > 0) {
    const serviceObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("show");
          }
        });
      },
      { threshold: 0.3 }
    );
    serviceBoxes.forEach((box) => serviceObserver.observe(box));
  }

  // Gallery items animation
  const galleryItems = document.querySelectorAll(".gallery-block .item");
  const galleryHeading = document.querySelector(".gallery-block .heading");
  if (galleryItems.length > 0) {
    const galleryObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("show");
          }
        });
      },
      { threshold: 0.2 }
    );
    galleryItems.forEach((item) => galleryObserver.observe(item));
    if (galleryHeading) galleryObserver.observe(galleryHeading);
  }

  // Executives animation
  const excosSection = document.querySelector(".container-excos");
  if (excosSection) {
    const excosObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const teams = entry.target.querySelectorAll(".our-team");
            teams.forEach((team, index) => {
              setTimeout(() => {
                team.classList.add("show");
              }, index * 200);
            });
          }
        });
      },
      { threshold: 0.1 }
    );
    excosObserver.observe(excosSection);
  }

  // Events animation
  const events = document.querySelectorAll(".event");
  const eventSection = document.querySelector(".events-section");
  if (events.length > 0 && eventSection) {
    const eventObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            events.forEach((event, index) => {
              setTimeout(() => {
                event.classList.add("show");
              }, index * 150);
            });
          }
        });
      },
      { threshold: 0.1 }
    );
    eventObserver.observe(eventSection);
  }

  // Footer animation
  const footer = document.querySelector(".footer");
  if (footer) {
    const footerObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("show");
          }
        });
      },
      { threshold: 0.2 }
    );
    footerObserver.observe(footer);
  }
}

// Initialize animations on DOMContentLoaded
document.addEventListener("DOMContentLoaded", function() {
  // Initialize animations
  initPageAnimations();
  
  // Fallback initialization with timeout in case DOMContentLoaded fires too early
  setTimeout(initPageAnimations, 1000);
});

// Also initialize on window load as a final fallback
window.addEventListener("load", initPageAnimations);

// Upcoming Event | Countdown Timer | Slider
document.addEventListener("DOMContentLoaded", function () {
  updateCountdown();

  // Event Slider Functionality
  const eventContainer = document.querySelector(".events-calendar");
  const events = document.querySelectorAll(".event");
  const totalEvents = events.length;
  let currentIndex = 0;
  let eventsPerSlide = window.innerWidth >= 768 ? 3 : 1; // 3 for larger screens, 1 for mobile

  function showEvents() {
    events.forEach((event, i) => {
      event.style.display =
        i >= currentIndex && i < currentIndex + eventsPerSlide
          ? "block"
          : "none";
    });
  }

  function nextEvent() {
    currentIndex += eventsPerSlide;
    if (currentIndex >= totalEvents) {
      currentIndex = 0; // Loop back to start
    }
    showEvents();
  }

  function prevEvent() {
    currentIndex -= eventsPerSlide;
    if (currentIndex < 0) {
      currentIndex = totalEvents - eventsPerSlide;
    }
    showEvents();
  }

  // Create Navigation Buttons
  const prevButton = document.createElement("button");
  prevButton.textContent = "❮";
  prevButton.className = "slider-btn prev-btn";
  prevButton.onclick = prevEvent;

  const nextButton = document.createElement("button");
  nextButton.textContent = "❯";
  nextButton.className = "slider-btn next-btn";
  nextButton.onclick = nextEvent;

  eventContainer.parentElement.appendChild(prevButton);
  eventContainer.parentElement.appendChild(nextButton);

  // Initialize Slider
  showEvents();

  // Auto-slide every 5 seconds
  setInterval(nextEvent, 8000);

  // Update slides on window resize
  window.addEventListener("resize", () => {
    eventsPerSlide = window.innerWidth >= 768 ? 3 : 1;
    showEvents();
  });
});

// Countdown Timer Script
function updateCountdown() {
  const countdownElements = document.querySelectorAll(".countdown");

  countdownElements.forEach((element) => {
    const eventDate = new Date(element.getAttribute("data-date")).getTime();
    const now = new Date().getTime();
    const timeLeft = eventDate - now;

    if (timeLeft > 0) {
      const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
      const hours = Math.floor(
        (timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
      );
      const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));

      element.innerHTML = `⏳ ${days}d ${hours}h ${minutes}m left`;
    } else {
      element.innerHTML = "🎉 Event Started!";
    }
  });
}

setInterval(updateCountdown, 60000);

// Registration Form Script
function openForm(eventName) {
  document.getElementById("registrationModal").style.display = "flex";
  document.getElementById("eventName").textContent = eventName;
}

function closeForm() {
  document.getElementById("registrationModal").style.display = "none";
}

document
  .getElementById("registrationForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    alert("✅ Registration Successful!");
    closeForm();
  });

//   Owl Carousel JS --
$(document).ready(function () {
  $(".project-carousel").owlCarousel({
    loop: true,
    margin: 10,
    autoplay: true,
    autoplayTimeout: 3000,
    smartSpeed: 800,
    nav: true,
    dots: false,
    items: 3,
    responsive: {
      0: { items: 1 },
      600: { items: 2 },
      1000: { items: 3 }
    }
  });
});
