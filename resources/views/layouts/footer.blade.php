<footer>
    <div class="base_wrap">
        {{-- <h3><a href="/terms/0">@lang('foot.foot-term0')</a>  |  <a href="/terms/1">@lang('foot.foot-term1')</a></h3> --}}
        <h3><a href="{{ route('store.faq') }}">@lang('sub.sub-faq')</a>  |  <a href="{{ route('store.term',0) }}">@lang('foot.foot-term1')</a>  |  <a href="{{ route('store.term',1) }}">@lang('foot.foot-term1')</a></h3>
        <h1>
            <img src="/assets/images/store/logo_w.png">
        </h1>

        <div class="info_company">
            <p>@lang('foot.foot-company')  |  @lang('foot.foot-boss')  |  @lang('foot.foot-adress') </p>
            <p>@lang('foot.foot-b_number')  |  @lang('foot.foot-tongsin') |  @lang('foot.foot-tel')  |  @lang('foot.foot-email')</p>
            <span>@lang('foot.foot-copy')</span>
        </div>
    </div>

</footer>
