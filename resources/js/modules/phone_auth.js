import {isDefined} from "./typecheck";
import {post} from "./ajax";
import Swal from "sweetalert2";

export const AuthMode = {
    CERT_AUTHNO_REGISTER: 1,
    CERT_AUTHNO_FIND_ID: 2,
    CERT_AUTHNO_FIND_PWD: 3,
    CERT_AUTHNO_CHANGE_HP: 4,
};
Object.freeze(AuthMode);

const requestAuthNo = (
    reqAuthBtnEm,
    confirmBtnEm,
    phoneFiled,
    authNoFiled,
    mode = AuthMode.CERT_AUTHNO_REGISTER
) => {
    try {
        if (isDefined(reqAuthBtnEm)) {
            reqAuthBtnEm.addEventListener('click',async function(){
                let data;
                if (mode === AuthMode.CERT_AUTHNO_FIND_PWD) {
                    data = {
                        userId : document.querySelector('input[name="userId"]').value,
                        phone : document.querySelector('input[name="phone"]').value,
                    }
                } else {
                    data = {
                        phone : document.querySelector('input[name="phone"]').value,
                    }
                }
                post('/action/phone/authNo/request/' + mode,data).then(r => {
                    if (r.ok) {
                        Swal.fire({
                            icon: 'success',
                            html: '인증번호가 발송 되었습니다',
                            showConfirmButton: false,
                            timer: 1200
                        });
                    }
                });
            });
        }
    } catch (e) {

    }
}
const confirmAuthNo = (
    confirmBtnEm,
    phoneFiled,
    authNoFiled,
    callback = null,
    mode = AuthMode.CERT_AUTHNO_REGISTER
) => {
    if (isDefined(confirmBtnEm)) {
        confirmBtnEm.addEventListener('click',async function(){
            post('/action/phone/authNo/check/' + mode,{
                'phone' : phoneFiled.value,
                'authNo' : authNoFiled.value
            }).then(r => {
                if (r.ok) {
                    r.json().then(json => {
                        if (typeof callback === 'function') {
                            callback(json);
                        }
                    }).catch(e => {
                        if (typeof callback === 'function') {
                            callback();
                        }
                    });
                }
            });
        });
    }
}
export {
    requestAuthNo,
    confirmAuthNo
}