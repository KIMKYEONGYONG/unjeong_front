import $ from "jquery";
import { post } from "./modules/ajax";
import Swal from "sweetalert2";
import {isDefined} from "./modules/typecheck";

$(function(){
    try {

        const editorId = 'editorTxt';
        let oEditors = [];
        if($('#editorTxt').length > 0){
            if (typeof nhn !== 'undefined' && nhn !== null) {
                nhn.husky.EZCreator.createInIFrame({
                    oAppRef: oEditors,
                    elPlaceHolder: editorId,
                    sSkinURI: "/naver_se2/SmartEditor2Skin.html",
                    fCreator: "createSEditor2"
                });
            }
        }


        // 등록
        $(document).on("click", "div.confirm.register", function () {
            const $code = $(this).attr('data-code')
            const $dataAjaxUrl = $(this).attr('data-ajax-url')
            if($code !== '' && $code !== undefined){
                const formData = commonForm()

                post($dataAjaxUrl,  formData, 'formData').then(response => {
                    if (response.ok) {
                        Swal.fire({
                            icon : "success",
                            text : '정상적으로 처리 되었습니다',
                            showConfirmButton: false,
                            timer: 1000
                        }).then( () => {
                            history.back()
                        })
                    }
                });
            }
        });
        // 수정
        $(document).on("click", "div.confirm.update", function () {
            const $dataId = $(this).attr('data-id')
            if($dataId !== '' && $dataId !== undefined){
                const $dataId = $(this).attr('data-id')
                const $dataAjaxUrl = $(this).attr('data-ajax-url')

                const formData = commonForm()

                post(`${$dataAjaxUrl}${$dataId}`,  formData, 'formData').then(response => {
                    if (response.ok) {
                        Swal.fire({
                            icon : "success",
                            text : '정상적으로 처리 되었습니다',
                            showConfirmButton: false,
                            timer: 1000
                        }).then( () => {
                            history.back()
                        })
                    }
                });

            }
        });


        function commonForm(){
            const form = document.querySelector('form');
            const formData = new FormData(form);

            if (typeof nhn !== 'undefined' && nhn !== null && $('#'+editorId).length > 0) {
                oEditors.getById[editorId].exec("UPDATE_CONTENTS_FIELD", []);
                // 가져온 내용을 content textarea에 넣기
                let editorContent = document.getElementById(editorId).value;
                formData.append('content',editorContent);
            }
            return formData
        }


    } catch (e) {
        console.log(e);
    }
});