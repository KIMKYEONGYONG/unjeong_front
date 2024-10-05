import * as Csrf from "./csrf.js";
import Swal from "sweetalert2";

const loading =(view = true) => {
    try {
        if (view) {
            document.querySelector('.ys_pageloading_wrap').style.display = 'block';
        } else {
            document.querySelector('.ys_pageloading_wrap').style.display = 'none';
        }
    } catch (e) {

    }
}
const ajax = (
    url,
    method = 'get',
    data = {},
    dataType = 'json',
    loadingView = true
) => {
    method = method.toLowerCase();
    if (loadingView) {
        loading(true);
    }
    let options = {
        method,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }
    const csrfMethods = new Set(['post', 'put', 'delete', 'patch']);

    if (csrfMethods.has(method)) {
        if (dataType === 'json') {
            if (method !== 'post') {
                data = {...data, _METHOD: method.toUpperCase()}
            }
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify({...data,...Csrf.getCsrfFields()});
        } else {
            const formData = dataType === 'formData' ? data : new FormData(data);
            for (const [key, value] of Object.entries(Csrf.getCsrfFields())) {
                formData.append(key,value);
            }
            options.body = formData;
        }
    } else if (method === 'get') {
        url += '?' + (new URLSearchParams(data)).toString();
    }
    return fetch(url,options).then(response => {
        if (! response.ok) {
            if (response.status === 422 || response.status === 401) {
                response.json().then(error =>{
                    let message = error.message;
                    if (!message) message = `${response.status} ${response.statusText}`
                    handleErrors(message,response.status)
                }).catch(e => {
                    handleErrors('Json Parse Error!!',response.status)
                    console.log(e)
                });
            } else if (response.status === 403) {
                handleErrors('해당 페이지에 접근 권한이 없습니다',response.status);
            } else if (response.status === 404) {
                handleErrors("존재하지 않는 URL 입니다",response.status);
            } else {
                handleErrors(response.statusText,response.status);
            }
        }
        return response;
    }).catch(error => {
        Swal.fire({
            icon: 'warning',
            text: error.message,
            confirmButtonText: '확인'
        }).then()
    }).finally(()=>loading(false));
}

const get  = (url, data,  dataType = 'json',loadingView = true) => ajax(url, 'get', data,  dataType, loadingView);
const post = (url, data,  dataType = 'json', loadingView = true) => ajax(url, 'post', data,  dataType, loadingView);
const del = (url, data,  dataType = 'json', loadingView = true) => ajax(url, 'delete', data,  dataType, loadingView);
const put = (url, data, dataType = 'json', loadingView = true) => ajax(url, 'put', data,  dataType, loadingView);
function handleErrors(message,status) {
    Swal.fire({
        icon: 'warning',
        html: message,
        confirmButtonText: '확인'
    }).then(result => {
        if (result.isConfirmed) {
            if (status === 401) {
                location.href = '/auth/login';
            }
        }
    })
}

export {ajax, get, post, del, put }