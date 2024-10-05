import $ from "jquery"
import Swal from "sweetalert2";
import {isDefined} from "../modules/typecheck";
import {AuthMode, confirmAuthNo, requestAuthNo} from "../modules/phone_auth";
import {post} from "../modules/ajax";

$(function() {


    const phoneCheck = document.getElementById('phoneCheck');
    const authenticationNum = document.querySelector('input[name="authenticationNum"]');
    const phone = document.querySelector('input[name="phone"]');
    const phoneCheckNum = document.getElementById('phoneCheckNum');
    const resend = document.getElementById('resend');
    const registerBtn = document.getElementById('registerBtn');

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


    registerBtn.addEventListener('click',async function(){
        const formData = new FormData(document.querySelector('form'));

        formData.append('phone', $('input[name="phone"]').val())
        formData.append('authenticationNum', $('input[name="authenticationNum"]').val())

        post('/action/account/create',formData,'formData').then(response => {
            if (response.ok) {
                location.replace('/menu8/signup_complete');
            }
        });
    });


});