<!DOCTYPE html>
<html lang="{{ Lang::locale() }}" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }}{{ Lang::has('menu.'.($activeMenu ?? '')) ? (' : '.__('menu.'.$activeMenu)) : '' }}</title>
        <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">

		<link rel="stylesheet" href="{{ asset('/css/fontawesome_all.css') }}" />
		<link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datatable.css') }}">
        <link rel="stylesheet" href="{{ asset('libs/bootstrap-timepicker/bootstrap-timepicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('libs/bootstrap-daterangepicker/daterangepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('libs/clockpicker/bootstrap-clockpicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('libs/swiper/swiper-bundle.min.css') }}">
        <link rel="stylesheet" href="{{ asset('libs/selectize/css/selectize.bootstrap3.css') }}">
        <link rel="stylesheet" href="{{ asset('css/summernote.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/dropify.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/jquery.nestable.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/switchery.min.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/style.css">

        @section('theme')
			<link rel="stylesheet" href="{{ asset('/css/store/reset.css') }}">
            <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
			<link rel="stylesheet" href="{{ asset('css/agent.css') }}">
			<link rel="stylesheet" href="{{ asset('css/client.css') }}">
        @show
        <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        @stack('styles')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    </head>
    <body class="nav-fixed">
        @yield('body')
        <script type="text/javascript">
            let domain = @json(config('app.domain'));
            let currency = @json(config('app.currency'));
        </script>
        <script src="{{ asset('js/cookie.min.js') }}" charset="utf-8"></script>
        <script src="{{ asset('js/toastr.min.js') }}" charset="utf-8"></script>
        <script src="{{ asset('js/common.js') }}" charset="utf-8"></script>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}" charset="utf-8"></script>
        <script src="{{ asset('libs/selectize/js/standalone/selectize.min.js') }}" charset="utf-8"></script>

        <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ asset('libs/moment/moment.min.js') }}"></script>
        <script src="{{ asset('libs/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{ asset('js/scripts.js') }}" charset="utf-8"></script>
        {{-- <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.ui/1.12.1/jquery-ui.min.js"></script> --}}
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
        @stack('locale')
        <script src="{{ asset('libs/material.js') }}" async charset="utf-8"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

        <script type="text/javascript">

            Date.prototype.addDays = function(days) {
                var date = new Date(this.valueOf());
                date.setDate(date.getDate() + days);
                return date;
            }

            var date = new Date();
            date = date.addDays(-30);

            $('#data_range').daterangepicker({
                "locale": {
                    "format": "YYYY-MM-DD",
                    "separator": " ~ ",
                    "applyLabel": "확인",
                    "cancelLabel": "취소",
                    "fromLabel": "From",
                    "toLabel": "To",
                    "customRangeLabel": "Custom",
                    "weekLabel": "W",
                    "daysOfWeek": ["일", "월", "화", "수", "목", "금", "토"],
                    "monthNames": ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
                },
                "startDate": date,
                "endDate": new Date(),
                "drops": "auto"
            }, function (start, end, label) {
                console.log('New date range selected: ' + start.format('YYYY.MM.DD') + ' to ' + end.format(
                    'YYYY.MM.DD') + ' (predefined range: ' + label + ')');
            });

            $('#single_date, .single_date').daterangepicker({
                "locale": {
                    "format": "YYYY-MM-DD",
                    "separator": " ~ ",
                    "applyLabel": "확인",
                    "cancelLabel": "취소",
                    "fromLabel": "From",
                    "toLabel": "To",
                    "customRangeLabel": "Custom",
                    "weekLabel": "W",
                    "daysOfWeek": ["일", "월", "화", "수", "목", "금", "토"],
                    "monthNames": ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
                },
                singleDatePicker: true,
                "drops": "auto"
            });
        </script>
        @stack('scripts')
    </body>
</html>
