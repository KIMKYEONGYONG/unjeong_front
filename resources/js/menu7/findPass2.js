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
    const passwordReset = document.getElementById('passwordReset');

    if (isDefined(phoneCheck)) {
        requestAuthNo(
            phoneCheck,
            phoneCheckNum,
            phone,
            authenticationNum,
            AuthMode.CERT_AUTHNO_FIND_PWD
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
            AuthMode.CERT_AUTHNO_FIND_PWD
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

        post('/action/account/findId',formData,'formData').then(response => {
            if (response.ok) {
                response.json().then(json =>{
                    $('#findPass1').hide();

                    const userId = json.userId;

                    $('#findPass2 .green').html(userId); // Set the value of the radio input
                    $('#findPass2 input[name="userId"]').val(userId); // Set the value of the radio input

                    $('#findPass2').show();
                })


            }
        });
    });

    passwordReset.addEventListener('click',async function(){
        const formData = new FormData(document.querySelector('form'));
        post('/action/account/passwordReset',formData,'formData').then(response => {
            if (response.ok) {
                location.href = "/menu7/login"
            }
        });
    })




});