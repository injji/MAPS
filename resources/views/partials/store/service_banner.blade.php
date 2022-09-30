<!-- CCC 20220526 -->
<div class="service_list">
    <div>
        <a href="{{ route('store.service.detail', [
            'service' => $service->id
        ]) }}">
            <h4><img src="{{ Storage::url($service->banner_image) }}"></h4>
            <div class="service_content">
                <h5>{{ $service->name }}</h5>
                <span>{{ $service->cat1->text }}</span>
                {{-- 인앱상품일 경우 --}}
                @if($service->in_app_payment == 1)
                    <h6>@lang('store.inapp')</h6>
                @else
                {{-- 인앱상품이 아닌경우 --}}
                    @if($service->free_term > 0)
                        @if($service->free_term == 99999)
                            <h6>@lang('sub.sub-filter_free')</h6>
                        @else
                            <h6>{{ $service->free_term }}@lang('store.free_days')</h6>
                        @endif
                    @else
                        <h6>{{ $service->plan->count() > 0?number_format($service->plan[0]->amount).$service->plan[0]->currency_text:'' }}</h6>
                    @endif
                @endif
                <p>{{ $service->service_info }}</p>
            </div>
        </a>
    </div>
</div>
