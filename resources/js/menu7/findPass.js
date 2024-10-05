import $ from "jquery"
import Swal from "sweetalert2";
import {isDefined} from "../modules/typecheck";
import {AuthMode, confirmAuthNo, requestAuthNo} from "../modules/phone_auth";
import {post} from "../modules/ajax";

$(function() {

    const registerBtn = document.getElementById('registerBtn');
    registerBtn.addEventListener('click',async function(){
        const formData = new FormData(document.querySelector('form'));


        post('/action/account/findExist',formData,'formData').then(response => {
            if (response.ok) {
                location.href =  "/menu7/passFind2"
            }
        });
    });




});