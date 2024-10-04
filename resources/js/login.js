import $ from "jquery";
import { post } from "./modules/ajax";

$(function(){
    try {

        $(document).on("click", "div.btn", function () {
            post('/action/login', {
                userId: document.querySelector('input[name="userId"]').value,
                password: document.querySelector('input[name="password"]').value
            }).then(response => {
                if (response.ok) {
                    location.href = '/';
                }
            });
        });

        $(document).on("keyup", "#loginFrm input[type=password]", function (e) {
            if (e.keyCode === 13) {
                $("#loginBtn").click();
            }
        });


        let fn_video_set = function () {
            let introVideo = $('.bg_intro_mv video');
            if(	$(window).width() > $(window).height()*1.7777777){
                introVideo.css('width','100%')
                introVideo.css('height','auto')
            }else{
                introVideo.css('width','auto')
                introVideo.css('height','100%')
            }
            let playbox = $('.playbox');
            let pw = playbox.width()
            let ph = (315*pw)/560
            playbox.css('height',ph)

        };
        $(window).bind({
            resize: function () { fn_video_set(); }
        });
        $(function(){
            fn_video_set();
        });

        $(document).on("click",".login_wrap.login .reset",function(){
            $(".login_wrap.login").hide();
            $(".login_wrap.password").show();
        })

        $(document).on("click",".login_wrap.password .passwordClose",function(){
            $(".login_wrap.password").hide();
            $(".login_wrap.login").show();
        })

        $(document).on("click",".login_wrap.password .passwordChange",function(){
            $(".login_wrap.password").hide();
            $(".login_wrap.login").show();
        })


    } catch (e) {
        console.log(e);
    }
});