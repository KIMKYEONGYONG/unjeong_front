import $ from "jquery"
import {isDefined} from "./modules/typecheck";
import Swal from "sweetalert2";
import { post } from "./modules/ajax";

$(function() {
    checkPopBg();

    $(document).on("click",".ajaxRequest",function(){
        const ajaxUrl = $(this).attr('data-ajax-url');
        let data = $(this).attr('data-object');
        let dataType = 'json';
        let layerId = $(this).attr('data-layer-id');
        if(layerId === undefined){
            layerId = "regFrm"
        }
        if(!isDefined(data)){
            data = document.querySelector('form#'+layerId);
            dataType = 'form';
        }
        const action = $(this).attr('data-action');

        post(ajaxUrl, data, dataType).then(response => {
            if (response.ok) {
                Swal.fire({
                    icon : "success",
                    text : '정상적으로 처리 되었습니다',
                    showConfirmButton: false,
                    timer: 1000
                }).then( () => {
                    if (action === 'move') {
                        let moveUrl = $(this).attr('data-move-url');
                        if(!isDefined(moveUrl)){
                            moveUrl = '/';
                        }
                        location.href = moveUrl
                    }else if (action === 'reload'){
                        location.reload()
                    }else if (action === 'back'){
                        history.back()
                    }
                })
            }
        });
    });

    $(document).on("click", ".popup .popup-ui .check", function(){
        $(this).parent().parent().parent().remove();
        checkPopBg();
        if( getCookie('Cookie_UN') === null){
            let tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            setCookie('Cookie_UN', 'Y', tomorrow, '/', document.domain );
        }
    });

    function checkPopBg(){
        const popBg = $('.popup');
        getCookie('Cookie_UN') !== null ? popBg.hide() : popBg.show();
    }

    function setCookie(cookieName, cookieValue, cookieExpire, cookiePath, cookieDomain, cookieSecure){
        let cookieText = cookieName + '=' + cookieValue;
        cookieText+=(cookieExpire ? '; EXPIRES='+cookieExpire.toGMTString() : '');
        cookieText+=(cookiePath ? '; PATH='+cookiePath : '');
        cookieText+=(cookieDomain ? '; DOMAIN='+cookieDomain : '');
        cookieText+=(cookieSecure ? '; SECURE' : '');
        document.cookie=cookieText;
    }

    function getCookie(cookieName){
        let cookieValue = null;
        if(document.cookie){
            let array=document.cookie.split((cookieName+'='));
            if(array.length >= 2){
                let arraySub=array[1].split(';');
                cookieValue=arraySub[0];
            }
        }
        return cookieValue;
    }



});