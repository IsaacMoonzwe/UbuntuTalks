$(document).ready(function() {
    var dateString = "2023/03/30"
    var deadline = new Date(dateString);

    function updateClock() {
       var today = Date();
       var diff = Date.parse(deadline) - Date.parse(today);
       if (diff <= 0) {
          clearInterval(interval);
       } else {
          var seconds = Math.floor((diff / 1000) % 60);
          var minutes = Math.floor((diff / 1000 / 60) % 60);
          var hours = Math.floor((diff / 1000 / 60 / 60) % 24);
          var days = Math.floor(diff / (1000 * 60 * 60 * 24) % 30.5);
          var months = Math.floor(diff / (1000 * 60 * 60 * 24 * 30.5) % 12);

          $("#months").text(('0' + months).slice(-2));
          $("#days").text(('0' + days).slice(-2));
          $("#hours").text(('0' + hours).slice(-2));
          $("#minutes").text(('0' + minutes).slice(-2));
          $("#seconds").text(('0' + seconds).slice(-2));

       } //EOF ELSE

    } //EOF FUNCTION

    var interval = setInterval(updateClock, 1000);

 }); //EOF DOCUMENT.READY
 $(document).ready(function() {
    // (function() {
    //    const second = 1000,
    //       minute = second * 60,
    //       hour = minute * 60,
    //       day = hour * 24;

    //    //I'm adding this section so I don't have to keep updating this pen every year :-)
    //    //remove this if you don't need it
    //    let today = new Date(),
    //       dd = String(today.getDate()).padStart(2, "0"),
    //       mm = String(today.getMonth() + 1).padStart(2, "0"),
    //       yyyy = today.getFullYear(),
    //       nextYear = yyyy + 1,
    //       dayMonth = "09/30/",
    //       birthday = dayMonth + yyyy;

    //    today = mm + "/" + dd + "/" + yyyy;
    //    if (today > birthday) {
    //       birthday = dayMonth + nextYear;
    //    }
    //    //end

    //    const countDown = new Date(birthday).getTime(),
    //       x = setInterval(function() {

    //          const now = new Date().getTime(),
    //             distance = countDown - now;

    //          document.getElementById("days").innerText = Math.floor(distance / (day)),
    //             document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour)),
    //             document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute)),
    //             document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);

    //          //do something later when date is reached
    //          if (distance < 0) {
    //             document.getElementById("headline").innerText = "It's my birthday!";
    //             document.getElementById("countdown").style.display = "none";
    //             document.getElementById("content").style.display = "block";
    //             clearInterval(x);
    //          }
    //          //seconds
    //       }, 0)
    // }());
    var btn = $('#button');

    $(window).scroll(function() {
       if ($(window).scrollTop() > 300) {
          btn.addClass('show');
       } else {
          btn.removeClass('show');
       }
    });

    btn.on('click', function(e) {
       e.preventDefault();
       $('html, body').animate({
          scrollTop: 0
       }, '300');
    });


    $(window).on("resize", function(e) {
       checkScreenSize();
    });

    checkScreenSize();

    function checkScreenSize() {
       var newWindowWidth = $(window).width();
       if (newWindowWidth < 992) {
          $(".events-tabs #myTab a").click(function() {
             $('html, body').animate({
                scrollTop: $(".sidebar-social").offset().top
             }, 500);
          });
          $(".main-menu a[href^='#']").click(function(e) {
             e.preventDefault();
             var position = $($(this).attr("href")).offset().top;
             $("body, html").animate({
                scrollTop: 750
             } /* speed */ );
          });

          $(".symposium-detais a[href^='#'], .ent-btn a[href^='#']").click(function(e) {
             e.preventDefault();
             var position = $($(this).attr("href")).offset().top;
             $("body, html").animate({
                scrollTop: 1500
             } /* speed */ );
          });
          $('#bottom_speaker').click(function(evt) {
             setTimeout(function() {
                $('#speakers-tab').trigger('click');
             }, 300);
          });
          $('#entertainment_speaker').click(function(evt) {
             setTimeout(function() {
                $('#entertainments-tab').trigger('click');
             }, 600);
          });

          $(".symposium-detais a[href^='#'], .ent-btn a[href^='#'], .main-menu a[href^='#']").click(function(e) {
             e.preventDefault();
             var position = $($(this).attr("href")).offset().top;
             $("body, html").animate({
                scrollTop: 1500
             } /* speed */ );
          });

          // Sponsor button for Mobile
          $(".sidebar-btns a.sponsor[href^='#']").click(function(e) {
             e.preventDefault();
             var position = $($(this).attr("href")).offset().top;
             $("body, html").animate({
                scrollTop: 1500
             } /* speed */ );
             setTimeout(function() {
                $('#sponsorship-tab').trigger('click');
             }, 300);
          });

          // Donation button for Mobile
          $(".sidebar-btns a.donation[href^='#']").click(function(e) {
             e.preventDefault();
             var position = $($(this).attr("href")).offset().top;
             $("body, html").animate({
                scrollTop: 1500
             } /* speed */ );
             setTimeout(function() {
                $('#donation-tab').trigger('click');
             }, 300);
          });
       } else {
          $(".ent-btn a[href^='#']").click(function(e) {
             e.preventDefault();
             var position = $($(this).attr("href")).offset().top;
             $("body, html").animate({
                scrollTop: 600
             } /* speed */ );
          });

          $(".symposium-detais a[href^='#']").click(function(e) {
             e.preventDefault();
             var position = $($(this).attr("href")).offset().top;
             $("body, html").animate({
                scrollTop: 600
             } /* speed */ );
          });
          $('#bottom_speaker').click(function(evt) {
             setTimeout(function() {
                $('#speakers-tab').trigger('click');
             }, 300);
          });
          $('#entertainment_speaker').click(function(evt) {
             setTimeout(function() {
                $('#entertainments-tab').trigger('click');
             }, 600);
          });

          $(".main-menu a[href^='#']").click(function(e) {
             e.preventDefault();
             var position = $($(this).attr("href")).offset().top;
             $("body, html").animate({
                scrollTop: 550
             } /* speed */ );
          });

          // Sponsor Button For Desktop
          $(".sidebar-btns a.sponsor[href^='#']").click(function(e) {
             e.preventDefault();
             var position = $($(this).attr("href")).offset().top;
             $("body, html").animate({
                scrollTop: 500
             } /* speed */ );
             setTimeout(function() {
                $('#sponsorship-tab').trigger('click');
             }, 300);
          });

          // Donation button for Desktop
          $(".sidebar-btns a.donation[href^='#']").click(function(e) {
             e.preventDefault();
             var position = $($(this).attr("href")).offset().top;
             $("body, html").animate({
                scrollTop: 500
             } /* speed */ );
             setTimeout(function() {
                $('#donation-tab').trigger('click');
             }, 300);
          });

       }
    }
 });


 function initMap() {
    // The location of Uluru
    const uluru = {
       lat: -15.3893,
       lng: 28.3133
    };

    // The map, centered at Uluru
    const map = new google.maps.Map(document.getElementById("map"), {
       zoom: 17,
       center: uluru,
    });
    // The marker, positioned at Uluru
    const marker = new google.maps.Marker({
       position: uluru,
       map: map,
    });
 }
 var initial_list_id = document.getElementsByClassName("tab_agenda")[0].id;
 tabClick(initial_list_id);

 function tabClick(id) {
    console.log("id", id);
    var list = document.getElementsByClassName("tab_agenda");
    for (var i = 0; i < list.length; i++) {
       var tab_id = list[i].getAttribute("id");
       console.log("ii", tab_id);
       if (tab_id == id) {
          list[i].classList.add("active");
       } else {
          list[i].classList.remove("active");
       }
    }
 }
 window.initMap = initMap;
 $(".cards").click(function() {
    $(".authore_information").show();
 });

 $(".x-mark").click(function() {
    $(".authore_information").hide();
 });

 //this is the button
 var acc = document.getElementsByClassName("course-accordion");
 var i;

 for (i = 0; i < acc.length; i++) {
    //when one of the buttons are clicked run this function
    acc[i].onclick = function() {
       //variables
       var panel = this.nextElementSibling;
       var coursePanel = document.getElementsByClassName("course-panel");
       var courseAccordion = document.getElementsByClassName("course-accordion");
       var courseAccordionActive = document.getElementsByClassName("course-accordion active");

       /*if pannel is already open - minimize*/
       if (panel.style.maxHeight) {
          //minifies current pannel if already open
          panel.style.maxHeight = null;
          //removes the 'active' class as toggle didnt work on browsers minus chrome
          this.classList.remove("active");
       } else { //pannel isnt open...
          //goes through the buttons and removes the 'active' css (+ and -)
          for (var ii = 0; ii < courseAccordionActive.length; ii++) {
             courseAccordionActive[ii].classList.remove("active");
          }
          //Goes through and removes 'activ' from the css, also minifies any 'panels' that might be open
          for (var iii = 0; iii < coursePanel.length; iii++) {
             this.classList.remove("active");
             coursePanel[iii].style.maxHeight = null;
          }
          //opens the specified pannel
          panel.style.maxHeight = panel.scrollHeight + "px";
          //adds the 'active' addition to the css.
          this.classList.add("active");
       }
    } //closing to the acc onclick function
 } //closing to the for loop.


 var coll = document.getElementsByClassName("collapsible");
 var i;
 for (i = 0; i < coll.length; i++) {
    coll[i].addEventListener("click", function() {
       this.classList.toggle("active");
       var content = this.nextElementSibling;
       if (content.style.display === "block") {
          content.style.display = "none";
       } else {
          content.style.display = "block";
       }
    });
 }

 $('.owl-carousel').owlCarousel({
    margin: 10,
    loop: true,
    autoplay: true,
    dots: false,
    autoplayTimeout: 5000,
    animateIn: 'fadeIn',
    nav: false,
    responsive: {
       0: {
          items: 1
       },
       600: {
          items: 1
       },
       1000: {
          items: 1
       }
    }
 })