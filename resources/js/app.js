/*!
 * fullPage 2.9.6
 * https://github.com/alvarotrigo/fullPage.js
 * @license MIT licensed
 *
 * Copyright (C) 2015 alvarotrigo.com - A project by Alvaro Trigo
 */
import "../css/styles.css";

window.$(function(){
    try {
        window.AOS.init();

        // header
        $('.gnb-menu').mouseenter(function () {
            $('header').addClass('active');
        });
        $('.gnb-menu').mouseleave(function () {
            $('header').removeClass('active');
        });

        $('#hamburgerBtn').click(function () {
            $('.mobile-menu-container').addClass('open');
            $('body').css('overflow', 'hidden');
            window.swiper.disable();
        });

        $('#mobile-menu-close-btn').click(function () {
            $('.mobile-menu-container').removeClass('open');
            $('body').css('overflow', 'visible');
            window.swiper.enable();
        });

        $('.mobile-menu-bg').click(function () {
            $('.mobile-menu-container').removeClass('open');
        });

        $('.menu-inner').click(function () {
            var submenuContainer = $(this).closest('.menu').find('.submenu-container');

            // Toggle the clicked submenu
            if (submenuContainer.hasClass('open')) {
                submenuContainer.removeClass('open');
            } else {
                // Close other submenus
                $('.submenu-container').removeClass('open');
                submenuContainer.addClass('open');
            }
        });



// mobile submenu
        const mainMenuBtn = $('#mainMenuBtn');
        const mainMenuList = $('#mainMenuList');
        const subMenuBtn = $('#subMenuBtn');
        const subMenuList = $('#subMenuList');

        mainMenuBtn.click(function () {
            mainMenuList.toggle();
        });
        subMenuBtn.click(function () {
            subMenuList.toggle();
        });

        $(document).click(function (event) {
            if (!mainMenuBtn.is(event.target) && !mainMenuList.is(event.target) && mainMenuList.has(event.target).length === 0) {
                mainMenuList.hide();
            }
            if (!subMenuBtn.is(event.target) && !subMenuList.is(event.target) && subMenuList.has(event.target).length === 0) {
                subMenuList.hide();
            }
        });
    } catch (e) {
        console.log(e);
    }
});


