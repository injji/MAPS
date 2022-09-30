<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="{{ asset('images/logo_b.png') }}" alt="" height="50">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto me-lg-5">
                <li class="nav-item">
                    <a class="nav-link text-black" href="#service">진행중인 서비스</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-black" href="#search">예산 검색</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-black" href="#agent">API 예제</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-black" href="#qna">고객 문의</a>
                </li>
            </ul>
            <span class="navbar-text ms-auto text-muted">
                {{-- <a href="{{ route('login') }}" class="text-muted">LOG IN</a> | <a href="{{ route('register.agree', ['type' => 1]) }}" class="text-muted">SIGN UP</a> --}}
                <a href="{{ route('login') }}" class="text-muted">LOG IN</a> | <a href="javascript:;" onclick="signModal()" class="text-muted">SIGN UP</a>
                <button type="button" class="btn btn-primary joinselect" data-toggle="modal" data-target="#joinselect" style="display:none">@lang('page.register')</button>
            </span>
        </div>
    </div>
</nav>
<!-- Modal -->
<div class="modal-open">
    <div class="modal fade join_btns" id="joinselect" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-body">
                    <ul>
                        <li>
                            <h3>@lang('messages.register.option.client')</h3>
                            <p>@lang('messages.register.description.client')</p>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#consumer_btn1"  data-dismiss="modal">@lang('button.register')</button>
                        </li>

                        <li>
                            <h3>@lang('messages.register.option.agent')</h3>
                            <p>@lang('messages.register.description.agent')</p>
                            <a href="{{ route('register.agree', ['type' => 2]) }}">@lang('button.register')</a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade join_btns" id="consumer_btn1" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-body ">
                    <ul>
                        <li>
                            <h3>@lang('messages.register.option.maps')</h3>
                            <p>@lang('messages.register.description.maps')</p>
                            <a href="{{ route('register.agree', ['type' => 1]) }}">@lang('button.register')</a>
                        </li>

                        <li>
                            <h3>@lang('messages.register.option.byapps')</h3>
                            <p>@lang('messages.register.description.byapps')</p>
                            <a href="{{ route('register.register3') }}">@lang('button.register')</a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    function signModal(){
        $('.joinselect').click();
    }
</script>
