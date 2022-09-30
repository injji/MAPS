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
            'type'    => 'order',
        ]) }}" ><button>@lang('sub.agent-download')</button></a>
</div>

<div class="graph_chart_wrap">
	<div class="graph_chart2">
        <canvas id="jsChart" height="210"></canvas>
    </div>
	
	<div class="graph_chart_table">
	<table>
		<tr>
			<th>@lang('sub.agent-date')</th>
			<th><em></em>@lang('sub.agent-state1')</th>
			<th><em></em>@lang('sub.agent-state2')</th>
			<th><em></em>@lang('sub.agent-state3')</th>
			<th><em></em>@lang('sub.agent-state4')</th>
		</tr>

		<?php $results = collect($results)->sortBy('day')->reverse()->toArray();
			$total=0; $wait_cnt=0; $complete_cnt=0; $expire_cnt=0; ?>
		@foreach($results as $key => $item)
			<?php 
				$total 			+= $item->wait_cnt + $item->complete_cnt + $item->expire_cnt; 
				$wait_cnt 		+= $item->wait_cnt; 
				$complete_cnt 	+= $item->complete_cnt; 
				$expire_cnt 	+= $item->expire_cnt; 
			?>
		<tr>
			<td>{{ $item->day }}</td>
			<td>{{ number_format($item->wait_cnt + $item->complete_cnt + $item->expire_cnt) }}</td>
			<td>{{ number_format($item->wait_cnt) }}</td>
			<td>{{ number_format($item->complete_cnt) }}</td>
			<td>{{ number_format($item->expire_cnt) }}</td>
		</tr>
		@endforeach

		<tr>
			<td>@lang('sub.agent-total')</td>
			<td>{{ number_format($total) }}</td>
			<td>{{ number_format($wait_cnt) }}</td>
			<td>{{ number_format($complete_cnt) }}</td>
			<td>{{ number_format($expire_cnt) }}</td>
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
        })
    });
</script>

<script type="text/javascript">

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
			 callbacks:{
				title: ()=>{}
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
			},
			stacked: true,
        },
        y: {
            type: 'linear',
            position: 'left',
            title: {
                display: true,
            },
            grid: {
				display:true
			},
			stacked: true,
        },
        
		
    },
	
}
let chart = null;
$(function(){
    let ctx = document.getElementById('jsChart').getContext('2d');
    chart = new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: chartOptions
    });
    // 
    getChartData('{{$st_date}}', '{{$ed_date}}', '{{$lang}}');
})

// chart data and options
let chartData = {
    labels: [],
    datasets: [
        {
            label: ' 매출건수',
			type: "line",
			borderColor: 'rgba(0, 124, 79, 1)',
			pointBackgroundColor: "rgba(0, 124, 79, 1)",
			pointBorderWidth: 1,
			pointHoverRadius: 3,
			pointHoverBackgroundColor: "rgba(75,192,192,1)",
			pointHoverBorderColor: "rgba(220,220,220,1)",
			pointHoverBorderWidth: 2,
			fill: false,
				tension: 0.4,
			borderWidth: 2,
			data: []
        },
        {
            label: ' 대기',
            backgroundColor: 'rgba(1, 40, 26, 0.5)',
			pointBorderWidth: 1,
			pointHoverRadius: 5,
			pointHoverBackgroundColor: "rgba(75,192,192,1)",
			pointHoverBorderColor: "rgba(220,220,220,1)",
			pointHoverBorderWidth: 2,
			data: [],
			barPercentage: 0.3,
				borderRadius: 30,
        },
		{
            label: ' 진행',
            backgroundColor: 'rgba(255, 179, 0, 0.5)',
                pointBackgroundColor: "#fff",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(75,192,192,1)",
                pointHoverBorderColor: "rgba(220,220,220,1)",
                pointHoverBorderWidth: 2,
                data: [],
			barPercentage: 0.3,
				borderRadius: 30,
        },
		{
            label: ' 만료',
            backgroundColor: 'rgba(139, 139, 139, 0.5)',
                pointBackgroundColor: "#fff",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(75,192,192,1)",
                pointHoverBorderColor: "rgba(220,220,220,1)",
                pointHoverBorderWidth: 2,
                endingShape: 'rounded',
                data: [],
			barPercentage: 0.3,
				borderRadius: 30,
        },

			
    ]
}

function getChartData(st_date, ed_date, lang){
    
	$.ajax({
		url: '/stat/stat_chart',
		method: 'post',
		data: {st_date, ed_date, lang, type: 'order'},
		success: (response) => {
			if(response.code == 200){
				let data = response.data;
                let tmpLabel = [];
                let tmpData1 = [];
                let tmpData2 = [];
                let tmpData3 = [];
                let tmpData4 = [];
                for(let i in data) {
                    tmpLabel.push(data[i].day);
                    let wait_cnt   = data[i].wait_cnt ?? 0;
                    let comp_cnt   = data[i].complete_cnt ?? 0;
                    let expire_cnt = data[i].expire_cnt ?? 0;
                    tmpData1.push(wait_cnt + comp_cnt + expire_cnt);
                    tmpData2.push(wait_cnt);
                    tmpData3.push(comp_cnt);
                    tmpData4.push(expire_cnt);
                }
                // 
                chart.data.labels = tmpLabel; 
                chart.data.datasets[0].data = tmpData1;
                chart.data.datasets[1].data = tmpData2;
                chart.data.datasets[2].data = tmpData3;
                chart.data.datasets[3].data = tmpData4;
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