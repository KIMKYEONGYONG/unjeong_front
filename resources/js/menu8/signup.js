import $ from "jquery"
import Swal from "sweetalert2";
import {isDefined} from "../modules/typecheck";
import {AuthMode, confirmAuthNo, requestAuthNo} from "../modules/phone_auth";

$(function() {


    const phoneCheck = document.getElementById('phoneCheck');
    const authenticationNum = document.querySelector('input[name="authenticationNum"]');
    const phone = document.querySelector('input[name="phone"]');
    const phoneCheckNum = document.getElementById('phoneCheckNum');
    const resend = document.getElementById('resend');

    if (isDefined(phoneCheck)) {
        requestAuthNo(
            phoneCheck,
            phoneCheckNum,
            phone,
            authenticationNum,
            AuthMode.CERT_AUTHNO_REGISTER
        );

        phoneCheck.addEventListener('click',async function(){
            const $phone = $('input[name="phone"]');
            $phone.prop('disabled', true);
            $phone.prop('readonly', true);
        });
    }

    if (isDefined(resend)) {
        requestAuthNo(
            resend,
            phoneCheckNum,
            phone,
            authenticationNum,
            AuthMode.CERT_AUTHNO_REGISTER
        );

        resend.addEventListener('click',async function(){
            const $authenticationNum = $('input[name="authenticationNum"]');
            $authenticationNum.prop('disabled', false);
            $authenticationNum.prop('readonly', false);
            $authenticationNum.val('')
        });
    }

    if (isDefined(phoneCheckNum)) {
        confirmAuthNo(
            phoneCheckNum,
            phone,
            authenticationNum,
            function () {
                Swal.fire({
                    icon: 'success',
                    html: '전화번호가 인증 되었습니다',
                    showConfirmButton: false,
                    timer: 1200
                }).then();
            }
        );

        phoneCheckNum.addEventListener('click',async function(){
            const $authenticationNum = $('input[name="authenticationNum"]');
            $authenticationNum.prop('disabled', true);
            $authenticationNum.prop('readonly', true);
        });
    }


});