@extends('layouts.agent')

@section('content')
<style>
    .nav a.nav-link:nth-child(1) {
        color: #007C4F;
    }

    .nav a:nth-child(1) .nav-link-icon i {
        color: #007C4F;
    }
</style>

<script>
    var popup_list = @json($popup_list);
    var popup_idx = 0;

    console.log(popup_list);


    //쿠키설정
    function setCookie(name, value, expiredays) {
        var todayDate = new Date();
        todayDate.setDate(todayDate.getDate() + expiredays);
        document.cookie = name + '=' + escape(value) + '; path=/; expires=' + todayDate.toGMTString() + ';'
    }

    //쿠키 불러오기
    function getCookie(name) {
        var obj = name + "=";
        var x = 0;
        while (x <= document.cookie.length) {
            var y = (x + obj.length);
            if (document.cookie.substring(x, y) == obj) {
                if ((endOfCookie = document.cookie.indexOf(";", y)) == -1)
                    endOfCookie = document.cookie.length;
                return unescape(document.cookie.substring(y, endOfCookie));
            }
            x = document.cookie.indexOf(" ", x) + 1;

            if (x == 0) break;
        }
        return "";
    }

    //닫기 버튼 클릭시
    function closeWin(id) {
        if ($("#todaycloseyn"+id).prop("checked")) {
            setCookie('divpop' + id, 'Y', 1);
        }

        $("#divpop"+id).hide();
    }

    function popupZindex(element){
        const divpop = document.querySelectorAll('.divpop');
        for ( var i = 0; i < divpop.length; i++ ) {
            divpop[i].style.zIndex = 999;
        }
        element.style.zIndex = 1000;

        return false;
    }

    function showPopup(idx) {

        var str = JSON.stringify(popup_list[idx]).replaceAll("\"","'");

        var html = '<div id="divpop'+popup_list[idx].id+'" class="divpop" onclick="popupZindex(this)" ondrag="popupZindex(this)">';
        html += '<div>';
        html += '<a href="javascript:detail(' + str + ')">';

        if (popup_list[idx].img != null && popup_list[idx].img != '') {
            var img = "{!! Storage::url('"+popup_list[idx].img+"') !!}";
            html += '<img src="'+img+'">';
        }

        html += '</a>';
        html += '</div>';
        html += '<div class="button_area">';
        html += '<input type="checkbox" name="chkbox" id="todaycloseyn'+popup_list[idx].id+'" value="Y">오늘 하루 이 창을 열지 않음';
        html += '<a href="javascript:closeWin('+popup_list[idx].id+')">닫기</a>';
        html += '</div>';
        html += '</div>';

        $("#popup_list").append(html);
        $('#divpop'+popup_list[idx].id).show();
        $('#divpop'+popup_list[idx].id).draggable();
        $('#divpop'+popup_list[idx].id).css('top', (50 + popup_idx * 20)+'px');
        $('#divpop'+popup_list[idx].id).css('left', (50 + popup_idx * 20)+'px');
        popup_idx++;
    }

    $(function () {
        $("#popup_list").empty();

        for (var i = 0; i < popup_list.length; i++) {
            if (getCookie("divpop"+popup_list[i].id) != "Y") {
                showPopup(i);
            }
        }
    });



</script>
{{--
<script>
    $('.divpop').click(function(){
        console.log('a');
    })
</> --}}

<!-- 팝업 -->
<div id="popup_list"></div>




<div class="dashboard_wrap">
    <div class="shot_information">
        <div>
            <h4>
                <img src="{{ asset('images/store/dash_icon1.svg') }}">
            </h4>


            <div>
                <h3>@lang('sub.agent-dashboard_service')</h3>

                <h1>{{ number_format($cur_month_cnt1) }}</h1>

                <ul>
                    <li><em>today</em>{{ number_format($today_cnt1) }}</li>
                    <li><em>total</em>{{ number_format($total_cnt1) }}</li>
                </ul>
            </div>
        </div>

        <div>
            <h4>
                <img src="{{ asset('images/store/dash_icon2.svg') }}">
            </h4>


            <div>
                <h3>@lang('sub.agent-dashboard_service_go')</h3>

                <h1>{{ number_format($cur_month_cnt2) }}</h1>

                <ul>
                    <li><em>today</em>{{ number_format($today_cnt2) }}</li>
                    <li><em>total</em>{{ number_format($total_cnt2) }}</li>
                </ul>
            </div>
        </div>

        <div>
            <h4>
                <img src="{{ asset('images/store/dash_icon3.svg') }}">
            </h4>


            <div>
                <h3>@lang('sub.agent-dashboard_sale')</h3>

                <h1>{{ number_format($cur_month_cnt3) }}</h1>

                <ul>
                    <li><em>today</em>{{ number_format($today_cnt3) }}</li>
                    <li><em>total</em>{{ number_format($total_cnt3) }}</li>
                </ul>
            </div>
        </div>

    </div>

    <div class="agent_graph">
        <div>
            <canvas id="chart1" height="308"></canvas>
        </div>
        <div>
            <canvas id="chart2" height="308"></canvas>
        </div>
        <div>
            <canvas id="chart3" height="308"></canvas>
        </div>
    </div>

    <div class="bell">
        <div class="notice">
            <h2>
                @lang('sub.agent-dashboard_notice')
                <span><a href="{{ route('agent.notice') }}">@lang('sub.sub-more')</a></span>
            </h2>

            <div>
                <div class="beno">
                    <ul>
                        @foreach($notice_list as $key => $item)
                        <li>
                            <a href="javascript:detail({{ $item }})">
                                <span>{!! str_replace('-', '.', substr($item->created_at, 0, 10)) !!}</span>
                                {{ $item->title }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="notice">
            <h2>
                @lang('sub.agent-dashboard_bell')
            </h2>

            <div>
                <div class="beno">
                    <ul class="alarmcenter">
                        @foreach($alim_list as $key => $item)
                        <li>
                            <?php $url = 'javascript:void(0)' ?>
                            @if($item->type == 1)
                            <?php $url = route('agent.service_list'); ?>
                            @elseif($item->type == 2)
                            <?php $url = route('agent.payment.refund'); ?>
                            @elseif($item->type == 3)
                            <?php $url = route('agent.payment.settlement'); ?>
                            @elseif($item->type == 4)
                            <?php $url = route('agent.inquiry_client'); ?>
                            @elseif($item->type == 5)
                            <?php $url = route('agent.inquiry_agent'); ?>
                            @elseif($item->type == 6)
                            <?php $url = route('agent.store.review'); ?>
                            @endif
                            <a href="{{ $url }}">
                                <span>{!! str_replace('-', '.', substr($item->created_at, 0, 10))
                                    !!}</span>{{ $item->content }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
    </div>

</div>

<div class="modal fade client_notice_detail" id="detail" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="m_title"></h5>
                <span id="m_time"></span>
            </div>
            <div class="modal-body">
                <div id="m_content"></div>
                <button data-bs-dismiss="modal" class="notice_close">@lang('button.close')</button>
            </div>

        </div>
    </div>
</div>

@if(session('login-success'))
<div id="welcome_popup">
    <button id="wpc"><img src="/assets/images/store/popupx_img.png"></button>
    <div>
        <img src="/assets/images/store/popup_img.png">
        <h1>
            <span>Welcome!</span>
            {{ Auth::user()->company_name }}@lang('messages.login.success')
        </h1>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script> 

// 상태표시 색 변경 퍼블리싱용으로 일단 작성해놓음.
$('.alarmcenter li').each(function() {
    $(this).html($(this).html().replace(/접수/g, '<em style="color:#FF0000 ">접수</em>'));
    $(this).html($(this).html().replace(/등록/g, '<em style="color:#FF0000 ">등록</em>'));
    $(this).html($(this).html().replace(/수정/g, '<em style="color:#FF0000 ">수정</em>'));
    $(this).html($(this).html().replace(/판매중/g, '<em style="color:#FF0000 ">판매중</em>'));
    $(this).html($(this).html().replace(/심사거절/g, '<em style="color:#FF0000 ">심사거절</em>'));
    $(this).html($(this).html().replace(/판매중지/g, '<em style="color:#FF0000 ">판매중지</em>'));
    $(this).html($(this).html().replace(/중지/g, '<em style="color:#FF0000 ">중지</em>'));
    $(this).html($(this).html().replace(/ 환불/, '<em style="color:#FF0000 "> 환불</em>'));

});
</script>

<script>
    function detail(obj) {
        console.log(obj);
        closeWin(obj.id);
        $("#m_title").html(obj.title);
        $("#m_time").html(getCorrectDateTime(obj.created_at).replace(/-/g, '.'));
        $("#m_content").html(obj.content);
        $("#detail").modal('show');

        $.ajax({
			url: '/notice/hits',
			method: 'post',
			data: {id: obj.id},
			success: (response) => {}
		});
    }

    var ctx = document.getElementById("chart1").getContext("2d");
    var gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(0,124,79,0.2)');
    gradient.addColorStop(1, 'rgba(255,255,255,0)');

    var data = {
        labels: @json($date_arr),
        datasets: [{
            label: " " + "{{ __('sub.agent-apply-cnt')}}",
            backgroundColor: gradient,
            fill: true,
            borderColor: 'rgba(0, 124, 79, 1)',
            pointBackgroundColor: "rgba(0, 124, 79, 1)",
            pointBorderWidth: 1,
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(75,192,192,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            tension: 0.4,
            data: @json($chart1_arr)
        }]
    };

    var options = {
        plugins: {
            legend: {
                display: false
            },

            tooltip: {
                mode: 'index',
                intersect: false,
                padding: 10,
                backgroundColor: '#fff',
                bodyColor: '#000000',
                borderColor: '#00000012',
                borderWidth: 1,
                bodyFontColor: '#000',
                bodyFontSize: 14,
                callbacks: {
                    title: () => {}
                }
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                display: true,

                ticks: {
                    beginAtZero: true,
                    steps: 10,
                    stepValue: 5,
                }
            }
        },
    };
    var myLineChart = new Chart(ctx, {
        type: "line",
        data: data,
        options: options
    });

    var ctx = document.getElementById("chart2").getContext("2d");
    var gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(0,124,79,0.2)');
    gradient.addColorStop(1, 'rgba(255,255,255,0)');

    var data = {
        labels: @json($date_arr),
        datasets: [{
            label: " " + "{{ __('sub.agent-progress-cnt')}}",
            backgroundColor: gradient,
            fill: true,
            borderColor: 'rgba(0, 124, 79, 1)',
            pointBackgroundColor: "rgba(0, 124, 79, 1)",
            pointBorderWidth: 1,
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(75,192,192,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            tension: 0.4,
            data: @json($chart2_arr)
        }]
    };

    var options = {
        plugins: {
            legend: {
                display: false
            },

            tooltip: {
                mode: 'index',
                intersect: false,
                padding: 10,
                backgroundColor: '#fff',
                bodyColor: '#000000',
                borderColor: '#00000012',
                borderWidth: 1,
                bodyFontColor: '#000',
                bodyFontSize: 14,
                callbacks: {
                    title: () => {}
                }
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                display: true,
                ticks: {
                    beginAtZero: true,
                    steps: 10,
                    stepValue: 5,
                }
            }
        },
    };
    var myLineChart = new Chart(ctx, {
        type: "line",
        data: data,
        options: options
    });

    var ctx = document.getElementById("chart3").getContext("2d");
    var gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(0,124,79,0.2)');
    gradient.addColorStop(1, 'rgba(255,255,255,0)');

    var data = {
        labels: @json($date_arr),
        datasets: [{
            label: " " + "{{ __('sub.agent-sales')}}",
            backgroundColor: gradient,
            fill: true,
            borderColor: 'rgba(0, 124, 79, 1)',
            pointBackgroundColor: "rgba(0, 124, 79, 1)",
            pointBorderWidth: 1,
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(75,192,192,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            tension: 0.4,
            data: @json($chart3_arr)
        }]
    };

    var options = {
        plugins: {
            legend: {
                display: false
            },

            tooltip: {
                mode: 'index',
                intersect: false,
                padding: 10,
                backgroundColor: '#fff',
                bodyColor: '#000000',
                borderColor: '#00000012',
                borderWidth: 1,
                bodyFontColor: '#000',
                bodyFontSize: 14,
                callbacks: {
                    title: () => {}
                }
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                display: true,
                ticks: {
                    beginAtZero: true,
                    steps: 10,
                    stepValue: 5,
                }
            }
        },

    };
    var myLineChart = new Chart(ctx, {
        type: "line",
        data: data,
        options: options
    });
</script>
@endpush

<style>
    #agent_nav {
        display: none !important;
    }
    .beno {
        overflow-y: auto !important;
    }
    #m_content {
        text-align: left;
        width: 100%;
        white-space: pre-line;
    }
    #m_content img {
        max-width: 100% !important;
    }
</style>
