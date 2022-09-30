@extends('layouts.agent')

@section('content')

<div class="option_btn">
    <div class="kokka">
        <p>@lang('sub.agent-contry')</p>

        <div class="select_box search_select">
            <div class="box">
                <div class="select2">{{ config('app.lang_text.'.$lang) }}</div>
                <ul class="list">
                    <li data-lang="ko" class="{{ $lang == 'ko' ? 'selected' : '' }}">@lang('lang.ko')</li>
                    <li data-lang="en">@lang('lang.en')</li>
                    <li data-lang="jp">@lang('lang.jp')</li>
                    <li data-lang="cn">@lang('lang.cn')</li>
                    <li data-lang="tw">@lang('lang.tw')</li>
                    <li data-lang="vn">@lang('lang.vn')</li>
                </ul>
            </div>
        </div>
    </div>

    <form class="date_order" name="fsearch">
        <p>@lang('sub.agent-period')</p>
        <input type="hidden" value="{{$lang}}" name="lang" />

        <input type="text" value="{{$st_date}} ~ {{$ed_date}}" id="data_range" name="data_range" />
        <button>
            <img src="/assets/images/store/search_icon_black.svg">
        </button>
    </form>

    <a href="{{ route('agent.stat_export', [
            'st_date' => $st_date,
            'ed_date' => $ed_date,
            'lang'    => $lang,
            'type'    => 'service',
        ]) }}" ><button>@lang('sub.agent-download')</button></a>
</div>

<div class="graph_chart_wrap">
    <div class="graph_chart2">
        <canvas id="chart_service" height="458"></canvas>
    </div>

    <div class="graph_chart_table graph_chart_table2">
        <table>
            <tr>
                <th>@lang('sub.agent-date')</th>
                <th>@lang('sub.agent-nu_service')</th>
                <th>@lang('sub.agent-nu_service_go')</th>
                <th><em></em>@lang('sub.agent-nu_service_ug')</th>
            </tr>

        <?php 
            $results = collect($results)->sortBy('day')->reverse()->toArray(); 
            $total=0; ?>
        @foreach($results as $key => $item)
            <?php 
                $using_cnt = $item->prev_using_cnt + ($item->using_cnt - $item->expire_cnt); 
                $wait_cnt  = $item->prev_wait_cnt + $item->wait_cnt; 
                $percent   = $wait_cnt > 0 ? ($using_cnt / $wait_cnt * 100) : 0;
                $total     += $percent;
            ?>
            <tr>
                <td>{{ $item->day }}</td>
                <td>{{ number_format($using_cnt) }}</td>
                <td>{{ number_format($wait_cnt) }}</td>
                <td>{{ number_format($percent) }}%</td>
            </tr>
        @endforeach

            <tr>
                <td colspan="3">@lang('sub.agent-average_service_ug')</td>
                <td>{{ count($results)>0 ? number_format($total / count($results)) : 0 }}%</td>
            </tr>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $("#data_range").data('daterangepicker').setStartDate('{{$st_date}}');
        $("#data_range").data('daterangepicker').setEndDate('{{$ed_date}}');
    });
    function CustomSelectBox(selector) {
        this.$selectBox = null,
            this.$select = null,
            this.$list = null,
            this.$listLi = null;
        CustomSelectBox.prototype.init = function (selector) {
            this.$selectBox = $(selector);
            this.$select = this.$selectBox.find('.box .select2');
            this.$list = this.$selectBox.find('.box .list');
            this.$listLi = this.$list.children('li');
        }
        CustomSelectBox.prototype.initEvent = function (e) {
            var that = this;
            this.$select.on('click', function (e) {
                that.listOn();
            });
            this.$listLi.on('click', function (e) {
                that.listSelect($(this));
            });
            $(document).on('click', function (e) {
                that.listOff($(e.target));
            });
        }
        CustomSelectBox.prototype.listOn = function () {
            this.$selectBox.toggleClass('on');
            if (this.$selectBox.hasClass('on')) {
                this.$list.css('display', 'block');
            } else {
                this.$list.css('display', 'none');
            };
        }
        CustomSelectBox.prototype.listSelect = function ($target) {
            $target.addClass('selected').siblings('li').removeClass('selected');
            this.$selectBox.removeClass('on');
            this.$select.text($target.text());
            this.$list.css('display', 'none');
        }
        CustomSelectBox.prototype.listOff = function ($target) {
            if (!$target.is(this.$select) && this.$selectBox.hasClass('on')) {
                this.$selectBox.removeClass('on');
                this.$list.css('display', 'none');
            };
        }
        this.init(selector);
        this.initEvent();
    }
</script>

<script>
    $(function () {
        var select = new CustomSelectBox('.select_box');
        // 
        $('.select_box li').click(function() {
            let lang = $(this).data('lang');
            $('input[name="lang"]').val(lang);
            $('form[name="fsearch"]').submit();
        });
        // 
        getChartData('{{$st_date}}', '{{$ed_date}}', '{{$lang}}');
    });
</script>

<script type="text/javascript">
    var ctx = document.getElementById("chart_service").getContext("2d");
    var gradient = ctx.createLinearGradient(0, 0, 0, 464);
    gradient.addColorStop(0, 'rgba(0,124,79,0.2)');
    gradient.addColorStop(1, 'rgba(255,255,255,0)');

    var data = {
        labels: [],
        datasets: [{
                label: " 서비스 유지율",
                backgroundColor: gradient,
				fill:true,
                borderColor: 'rgba(0, 124, 79, 1)',
                pointBackgroundColor: "rgba(0, 124, 79, 1)",
                pointBorderWidth: 1,
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(75,192,192,1)",
                pointHoverBorderColor: "rgba(220,220,220,1)",
                pointHoverBorderWidth: 2,
                data: [],
                tension: 0.4,
            }

        ]
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
                    callback: function (value) {
                        return value + "%";
                    }
                }
            }
        },
    };
    var chart = new Chart(ctx, {
        type: "line",
        data: data,
        options: options
    });

    function getChartData(st_date, ed_date, lang){
        
        $.ajax({
            url: '/stat/stat_chart',
            method: 'post',
            data: {st_date, ed_date, lang, type: 'service'},
            success: (response) => {
                if(response.code == 200){
                    let data = response.data;
                    let tmpLabel = [];
                    let tmpData1 = [];
                    for(let i in data) {
                        tmpLabel.push(data[i].day);
                        let using_cnt = data[i].prev_using_cnt + (data[i].using_cnt - data[i].expire_cnt); 
                        let wait_cnt  = data[i].prev_wait_cnt + data[i].wait_cnt; 
                        let percent   = wait_cnt > 0 ? (using_cnt / wait_cnt * 100) : 0;
                        tmpData1.push(parseInt(percent));
                    }
                    // 
                    chart.data.labels = tmpLabel; 
                    chart.data.datasets[0].data = tmpData1;
                    chart.update(); 
                }                    
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });  
    }
</script>

@endpush