import $ from "jquery"
import Swal from "sweetalert2";

$(function() {

    $(document).on("click",".btn-primary",function(){
        const term2 = $('input[name="term2"]:checked').val()
        const term3 = $('input[name="term3"]:checked').val()

        if(term2 === undefined || term2 === ""){
            Swal.fire({
                icon : "warning",
                html : "이용약관에 동의해 주세요."
            }).then();
            return;
        }
        if(term3 === undefined || term3 === ""){
            Swal.fire({
                icon : "warning",
                html : "개인정보 수집 및 이용동의에 동의해주세요"
            }).then();
            return;
        }

        location.href = "/menu8/signup"
    });


});