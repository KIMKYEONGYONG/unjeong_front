import "../css/app.scss";
import $ from "jquery"
import {isDefined} from "./modules/typecheck";
import Swal from "sweetalert2";
import { post, del } from "./modules/ajax";

$(function(){
    try {

        //링크이동
        $(document).on("click",".moveUrl",function(){
            location.href=$(this).attr("data-url");
        });

        //링크새창열기
        $(document).on("click",".openUrl",function(){
            window.open($(this).attr("data-url"));
        });

        //링크리플레이스
        $(document).on("click",".replaceUrl",function(){
            location.replace($(this).attr("data-url"));
        });

        // 뒤로가기
        $(document).on("click",".historyBack",function(){
            history.back();
        });

        //메뉴토글
        $(document).on("click",".menu_wrap .menu ul li:nth-child(1)",function(){
            if(!$(this).parent().hasClass("on")){
                $(this).parent().toggleClass("open");
            }
        });

        //메뉴접기 펼치기
        $(document).on("click",".menuToggle",function(){
            $(".menu_wrap .inbox").css("left","0");
            $(".menu_wrap .back").show();
        });
        $(document).on("click",".menu_wrap .back",function(){
            $(".menu_wrap .inbox").css("left","-250px");
            $(".menu_wrap .back").hide();
        })

        //소트토글
        $(document).on("click",".sort_box .toggle",function(){
            if($(this).parent().find(".sort").css("height") === "50px"){
                $(".sort").css("height","auto");
                $(this).text("접기");
            }else{
                $(".sort").css("height","50px");
                $(this).text("펼치기");
            }
        })

        //레이어
        $(document).on("click",".layerOpen",function(){
            let layer = $(this).attr("data-layer")
            $(layer).show();
        });
        $(document).on("click",".layerClose",function(){
            let layer = $(this).attr("data-layer")
            $(layer).hide();
        });

        $(document).ready(function() {
            // Get the current pathname
            const currentPath = window.location.pathname;

            // Select all elements with the data-url attribute
            $('.menu_wrap .menu ul li dl').each(function() {
                const dataUrlForm = $(this).attr("data-url-form");

                if (currentPath.startsWith(dataUrlForm)) {
                    // Add a class to the matching element
                    $(this).addClass("on");
                    if (!$(this).parent().parent().hasClass("on")) {
                        $(this).parent().parent().toggleClass("open");
                    }
                }
            });
        });

        $(document).on("click",".ajaxRequest",function(){
            const ajaxUrl = $(this).attr('data-ajax-url');
            let data = $(this).attr('data-object');
            let dataType = 'json';
            let layerId = $(this).attr('data-layer-id');
            if(!isDefined(data)){
                data = document.querySelector('form#'+layerId);
                dataType = 'form';
            }
            const action = $(this).attr('data-action');

            post(ajaxUrl, data, dataType).then(response => {
                if (response.ok) {
                    Swal.fire({
                        icon : "success",
                        text : '정상적으로 처리 되었습니다',
                        showConfirmButton: false,
                        timer: 1000
                    }).then( () => {
                        if (action === 'move') {
                            let moveUrl = $(this).attr('data-move-url');
                            if(!isDefined(moveUrl)){
                                moveUrl = '/';
                            }
                            location.href = moveUrl
                        }else if (action === 'reload'){
                            location.reload()
                        }else if (action === 'back'){
                            history.back()
                        }
                    })
                }
            });
        });

        $(document).on("click",".ajaxDelete",function(){
            const ajaxUrl = $(this).attr('data-ajax-url');
            let content = $(this).attr('data-content')
            if(!isDefined(content)){
                content = '목록'
            }
            Swal.fire({
                html: `해당 ${content}을(를) 삭제하시겠습니까?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '삭제',
                cancelButtonText: '취소',
                cancelButtonColor: '#555',
            }).then(result => {
                if (result.isConfirmed) {
                    del(ajaxUrl, {} ).then(response => {
                        if (response.ok) {
                            Swal.fire({
                                icon : "success",
                                text : '정상적으로 처리 되었습니다',
                                showConfirmButton: false,
                                timer: 1000
                            }).then( () => {
                                location.reload()
                            })
                        }
                    });
                }
            });
        });

        $(document).ready(function() {
            const $selectPageSize = $('select[name="pageSize"]');

            $selectPageSize.on('change', function() {
                const pageSize = $(this).val() || 10;
                const $searchForm = $('#searchForm');

                if ($searchForm.length) {
                    const data = $searchForm.serializeArray();
                    data.push({ name: 'pageSize', value: pageSize });
                    const queryString = $.param(data);
                    window.location.href = $searchForm.attr('action') + '?' + queryString;
                } else {
                    window.location.href = window.location.href.split('?')[0] + '?pageSize=' + pageSize;
                }
            });
        });

        $(document).on("click",".sort_btn",function(){

            const currentPath = window.location.pathname;

            console.log(currentPath)

            if(currentPath.indexOf('report') > 0){

                const startDate =  $("input[name='startDate']").val()
                const endDate =  $("input[name='endDate']").val()

                const today = new Date();
                const todayYmd = formatDate(today);

                if(startDate !== ''){
                    if(startDate > todayYmd){
                        Swal.fire({
                            icon : "warning",
                            text : '시작일을 금일보다 크게 설정할 수 없습니다. ',
                            showConfirmButton: false,
                            timer: 1000
                        }).then();
                        return;
                    }
                }

                if(endDate !== ''){
                    if(endDate > todayYmd){
                        Swal.fire({
                            icon : "warning",
                            text : '종료일을 금일보다 크게 설정할 수 없습니다. ',
                            showConfirmButton: false,
                            timer: 1000
                        }).then();
                        return;
                    }
                }
            }


            const form = document.getElementById("searchForm");

            let queryString = "?";
            for (let i = 0; i < form.elements.length; i++) {
                let element = form.elements[i];
                if (element.name) {
                    if (queryString !== "?") {
                        queryString += "&";
                    }
                    queryString += encodeURIComponent(element.name) + "=" + encodeURIComponent(element.value);
                }
            }
            const selectTerm = $('.on.selectTerm');
            const dataTerm = selectTerm.attr('data-term');
            if(dataTerm !== "" && dataTerm !== undefined){
                queryString += '&dateTerm=' + dataTerm
            }
            location.href = window.location.pathname + queryString;

        });

        $(document).on("click", ".selectTerm", function(){
            const dateTerm = $(this).attr("data-term");
            const dateTermType = $(this).attr("data-term-type");
            $(".selectTerm").each(function(index, item){
                $(item).removeClass('on');
            });
            $(this).addClass('on');
            inputDate('startDate', 'endDate', dateTermType, dateTerm);

        });

        function inputDate(startDate, endDate, dateTermType, dateTerm)
        {
            if(dateTermType === 'D'){
                const _startDate = new Date()
                const _endDate = new Date();
                _startDate.setDate(_startDate.getDate() - Number(dateTerm));
                _endDate.setDate(_endDate.getDate() - Number('1'));

                const $startDate = formatDate(_startDate)
                const $endDate = formatDate(_endDate)

                if(dateTerm !== ''){
                    $("input[name='"+startDate+"'").val($startDate)
                    $("input[name='"+endDate+"'").val($endDate)
                }else{
                    $("input[name='"+startDate+"'").val('')
                    $("input[name='"+endDate+"'").val('')
                }
            }
        }

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }


    } catch (e) {
        console.log(e);
    }
});


