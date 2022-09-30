@extends('layouts.agent')

@section('content')
<div class="p-3 m-0 mb-3 pt-3 bg-white row stat_tit">
    <div class="col-md-6 m-0 p-0 ">
        <div class="col-12 m-title ml-4 pl-md-0 pl-3">
            <div class="page-title-box">
                <div class="fs-5 fw-bold">
                    <span>@lang('menu.stat.home')</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 m-0 d-none d-none d-md-block d-lg-block text-right" style="line-height: 30px;">
        <a href="https://agent-dev.mapstrend.com/service" class="subheading-2 text-black" display-6=""
            style="text-decoration: none; vertical-align: middle;">
            @lang('menu.stat.home') </a>
        <span class="material-icons text-muted" style="vertical-align: middle;opacity: .5;">chevron_right</span>
        <a href="javascript:void(0)" class="subheading-2 text-black" display-6=""
            style="text-decoration: none; vertical-align: middle;">
            @lang('menu.agent.stat_order') </a>
    </div>
</div>


<div class="option_btn">
	<div class="kokka">
		<p>@lang('sub.agent-contry')</p>

		<div class="select_box search_select">
			<div class="box">
				<div class="select2">@lang('lang.ko')</div>
				<ul class="list">
					<li class="selected">@lang('lang.ko')</li>
					<li>@lang('lang.en')</li>
					<li>@lang('lang.cn')</li>
					<li>@lang('lang.jp')</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="date_order">
		<p>@lang('sub.agent-period')</p>
		<input type="text" value="2021-12-16 - 2022-1-12" id="myInputTextField"> 
		
		<button>
			<img src="/assets/images/store/search_icon_black.svg">
		</button>
	</div>

	<button id='excelExport'>@lang('button.download')</button>
</div>

<div class="graph_chart_wrap">
	<div class="graph_chart">
        <canvas id="jsChart" height="210"></canvas>
    </div>
	
	<div class="graph_chart_table">
	<table id="datatable"  class="table table-bordered" name="datatables">
		<thead>
			<tr>
				<th>@lang('sub.agent-date')</th>
				<th><em></em>@lang('sub.agent-state1')</th>
				<th><em></em>@lang('sub.agent-state2')</th>
				<th><em></em>@lang('sub.agent-state3')</th>
				<th><em></em>@lang('sub.agent-state4')</th>
			</tr>
		</thead>
		
		<tfoot>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
		</tfoot>
	</table>

	
	</div>
</div>

<div id="listData"></div>

@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/page/agent-stat-order.js') }}" charset="utf-8"></script>

<script>
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
    });
</script>

<script type="text/javascript">


$(function(){
    let ctx = document.getElementById('jsChart').getContext('2d');
    let chart = new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: chartOptions
    })
})

// chart data and options
let chartData = {
    labels: [8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 8.7 ],
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
			data: [26, 27, 64, 28, 49, 36, 50]
        },
        {
            label: ' 대기',
            backgroundColor: 'rgba(1, 40, 26, 0.5)',
			pointBorderWidth: 1,
			pointHoverRadius: 5,
			pointHoverBackgroundColor: "rgba(75,192,192,1)",
			pointHoverBorderColor: "rgba(220,220,220,1)",
			pointHoverBorderWidth: 2,
			data: [13, 21, 22, 19, 26, 17, 19],
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
                data: [10, 4, 13, 9, 20, 11, 20],
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
                data: [3, 2, 29, 0, 3, 8, 11],
			barPercentage: 0.3,
				borderRadius: 30,
        },

			
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


</script>
	



@endpush


</body>
</html>