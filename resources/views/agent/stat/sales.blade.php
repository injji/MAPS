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
            'type'    => 'sales',
        ]) }}" ><button>@lang('sub.agent-download')</button></a>
</div>

<div class="graph_chart_wrap2">
    <div class="chart_service2">
        <canvas id="chart_service" height="100"></canvas>
    </div>

    <div class=" graph_chart_table3">
        <table>
            <tr>
                <th rowspan="2">@lang('sub.agent-date')</th>
                <th colspan="2">@lang('sub.agent-new')</th>
                <th colspan="2">@lang('sub.agent-more')</th>
                <th colspan="2">@lang('sub.agent-hab')</th>
            </tr>

            <tr>
                <th><em></em>@lang('sub.agent-su')</th>
				<th><em></em>@lang('sub.agent-pay')</th>
				<th><em></em>@lang('sub.agent-su')</th>
				<th><em></em>@lang('sub.agent-pay')</th>
				<th><em></em>@lang('sub.agent-su')</th>
				<th><em></em>@lang('sub.agent-pay')</th>
            </tr>

        @php $results = collect($results)->sortBy('day')->reverse()->toArray(); @endphp
        @foreach($results as $key => $item)
            <?php 
                $extend_cnt = $item->new_cnt + $item->extend_cnt; 
                $extend_sum = $item->new_sum + $item->extend_sum; 
            ?>
            <tr>
                <td>{{ $item->day }}</td>
                <td>{{ number_format($item->new_cnt) }}</td>
                <td>{{ number_format($item->new_sum) }}</td>
                <td>{{ number_format($item->extend_cnt) }}</td>
                <td>{{ number_format($item->extend_sum) }}</td>
                <td>{{ number_format($extend_cnt) }}</td>
                <td>{{ number_format($extend_sum) }}</td>
            </tr>
        @endforeach
	
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
        })
    });
</script>

<script type="text/javascript">
let chart = null;
$(function(){
    let ctx = document.getElementById('chart_service').getContext('2d');
    chart = new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: chartOptions
    });
    // 
    getChartData('{{$st_date}}', '{{$ed_date}}', '{{$lang}}');
});

// chart data and options
let chartData = {
    labels: [],
    datasets: [
        {
            label: ' 매출건수',
			type: "line",
            yAxisID: 'y-left',
            data: [],
			borderColor: 'rgba(0, 124, 79, 1)',
			pointBackgroundColor: "rgba(0, 124, 79, 1)",
			pointBorderWidth: 1,
			pointHoverRadius: 3,
			pointHoverBackgroundColor: "rgba(75,192,192,1)",
			pointHoverBorderColor: "rgba(220,220,220,1)",
			pointHoverBorderWidth: 2,
            borderWidth: 2,
				tension: 0.4,
        },
        {
            label: ' 매출액',
            yAxisID: 'y-right',
            data: [],
            backgroundColor:'rgba(0, 124, 79, 0.3)',
			barPercentage: 0.2,
        }
    ]
}

let chartOptions = {
    responsive:true,
	plugins: {
		legend: {
			display: false
		},
		tooltip: {
		 mode: 'index',
		 intersect: false,
			 padding	: 10,
			backgroundColor: '#fff',
			 bodyColor: '#000000',
             borderColor : '#00000012',
			 borderWidth: 1,
			 bodyFontColor: '#000',
			 bodyFontSize: 14,
			 titleColor: '#000',
			 callbacks:{
				title: ()=>'합계'
			 }
		}
	},
    scales: {
        x: {
            title: {
                display: true,
            },
			grid: {
				display:false
			}
        },
        'y-left': {
            type: 'linear',
            position: 'left',
            title: {
                display: true,
            },
            grid: {
				display:true
			}
        },
        'y-right': {
            type: 'linear',
            position: 'right',
            title: {
                display: true,
            },
            grid: {
				display:false
			}
        },
		
    },
	
}

function getChartData(st_date, ed_date, lang){
    
    $.ajax({
        url: '/stat/stat_chart',
        method: 'post',
        data: {st_date, ed_date, lang, type: 'sales'},
        success: (response) => {
            if(response.code == 200){
                let data = response.data;
                let tmpLabel = [];
                let tmpData1 = [];
                let tmpData2 = [];
                for(let i in data) {
                    tmpLabel.push(data[i].day);
                    let cnt = parseInt(data[i].new_cnt ?? 0) + parseInt(data[i].extend_cnt ?? 0);
                    let sum = parseInt(data[i].new_sum ?? 0) + parseInt(data[i].extend_sum ?? 0);
                    tmpData1.push(cnt);
                    tmpData2.push(sum);
                }
                // 
                chart.data.labels = tmpLabel; 
                chart.data.datasets[0].data = tmpData1;
                chart.data.datasets[1].data = tmpData2;
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