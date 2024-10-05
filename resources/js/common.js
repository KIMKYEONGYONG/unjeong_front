import $ from "jquery"
import {isDefined} from "./modules/typecheck";
import Swal from "sweetalert2";
import { post } from "./modules/ajax";

$(function() {

    $(document).on("click",".ajaxRequest",function(){
        const ajaxUrl = $(this).attr('data-ajax-url');
        let data = $(this).attr('data-object');
        let dataType = 'json';
        let layerId = $(this).attr('data-layer-id');
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

});