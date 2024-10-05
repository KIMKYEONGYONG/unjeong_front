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
            AuthMode.CERT_AUTHNO_FIND_ID
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
            AuthMode.CERT_AUTHNO_FIND_ID
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
                    $('#findId1').hide();

                    console.log(json)
                    const userId = json.userId;
                    const createdAt = json.createdAt;

                    $('#findId2 #id1').val(userId); // Set the value of the radio input
                    $('#findId2 label[for="id1"]').html(userId); // Set the userId inside the label
                    $('#findId2 .reg-info').html(createdAt); //

                    $('#findId2').show();
                })


            }
        });
    });


});