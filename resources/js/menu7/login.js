import $ from "jquery";
import { post } from "../modules/ajax";

$(function() {

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


});