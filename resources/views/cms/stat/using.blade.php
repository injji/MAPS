@extends('layouts.cms')

@section('content')

<div class="option_btn">

    <form class="date_order" name="fsearch">
        <p>@lang('sub.agent-period')</p>

        <input type="hidden" value="{{$tab}}" name="tab" />
        <input type="text" value="{{$st_date}} ~ {{$ed_date}}" id="data_range" name="data_range" />
        <button>
            <img src="/assets/images/store/search_icon_black.svg">
        </button>
    </form>

</div>

<div class="graph_chart_wrap2">
    <div class="chart_service2 pt-4">
		<div class="text-center my-4">
			<button type="button" onclick="location.href='/stat/using?tab=1'" class="{{$tab == 1 ? 'set1' : 'set3' }}">일간</button>
			<button type="button" onclick="location.href='/stat/using?tab=2'" class="{{$tab == 2 ? 'set1' : 'set3' }}">주간</button>
			<button type="button" onclick="location.href='/stat/using?tab=3'" class="{{$tab == 3 ? 'set1' : 'set3' }}">월간</button>
		</div>
        <canvas id="chart_service" height="100"></canvas>
    </div>

	<div class="option_btn mb-4">

	    <strong>일일통계</strong>

	    <a href="{{ route('stat_export', [
	            'st_date' => $st_date,
	            'ed_date' => $ed_date,
                'tab'     => $tab,
	            'type'    => 'using',
	        ]) }}" ><button>@lang('sub.agent-download')</button></a>
	</div>
    <div class=" graph_chart_table3">
        <table>
            <tr>
                <th rowspan="2">DATE</th>
                <th rowspan="2">DAU</th>
                <th colspan="2">신규가입</th>
                <th rowspan="2">신청수</th>
                <th rowspan="2">매출액</th>
            </tr>
            <tr>
                <th>제휴사</th>
				<th>고객사</th>
            </tr>

            @foreach($results as $key => $item)
                <tr>
                    <td>{{ $item->day }}</td>
                    <td>{{ number_format($item->dau_cnt) }}</td>
                    <td>{{ number_format($item->new_cnt1) }}</td>
                    <td>{{ number_format($item->new_cnt2) }}</td>
                    <td>{{ number_format($item->req_cnt) }}</td>
                    <td>{{ number_format($item->pay_sum) }}</td>
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
</script>

<script>
    $(function () {

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
    getChartData('{{$st_date}}', '{{$ed_date}}', '{{$tab}}');
});

// chart data and options
let chartData = {
    labels: [],
    datasets: [
        {
            label: ' 매출액',
            yAxisID: 'y-right',
            data: [],
            backgroundColor:'rgba(0, 124, 79, 0.3)',
            barPercentage: 0.2,
        },
        {
            label: ' DAU',
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
            label: ' 신규가입',
			type: "line",
            yAxisID: 'y-left',
            data: [],
			borderColor: '#5B9BD5',
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
            label: ' 신청수',
			type: "line",
            yAxisID: 'y-left',
            data: [],
			borderColor: '#ED7D31',
			pointBackgroundColor: "rgba(0, 124, 79, 1)",
			pointBorderWidth: 1,
			pointHoverRadius: 3,
			pointHoverBackgroundColor: "rgba(75,192,192,1)",
			pointHoverBorderColor: "rgba(220,220,220,1)",
			pointHoverBorderWidth: 2,
            borderWidth: 2,
				tension: 0.4,
        },
    ]
}

let chartOptions = {
    responsive:true,
	plugins: {
		legend: {
			display: true,
			position: 'bottom',
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

function getChartData(st_date, ed_date, tab){
    
    $.ajax({
        url: '/stat/stat_chart',
        method: 'post',
        data: {st_date, ed_date, tab},
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
                    tmpData1.push(data[i].pay_sum ?? 0);
                    tmpData2.push(data[i].dau_cnt ?? 0);
                    tmpData3.push(data[i].new_cnt1 ?? 0 + data[i].new_cnt2 ?? 0);
                    tmpData4.push(data[i].req_cnt ?? 0);
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