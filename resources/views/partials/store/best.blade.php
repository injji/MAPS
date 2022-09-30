<!-- CCC 20220526 -->
<div class="item_product">
    <a href="{{ route('store.service.detail', [
        'service' => $service->id
    ]) }}">
        <div class="item_img">
            <h4><img width="80" src="{{ Storage::url($service->icon) }}"></h4>
            <div class="star">
                @for ($i = 0; $i < 5; $i++)
                    <em><img src="{{ asset('images/store/star_'.($service->review->avg('rating') && $service->review->avg('rating') > $i ? 'on' : 'off').'.svg') }}"></em>
                @endfor
            </div>
        </div>
        <!-- <div class="service_content"> -->
        <div class="item_content">
            <h5>{{ $service->name }}</h5>
            <span>{{ $service->cat1->text }}</span>
            <p>
            @if($service->free_term > 0)
                @if($service->free_term == 99999)
                    @lang('sub.sub-filter_free')
                @else
                    {{ $service->free_term }}@lang('store.free_days')
                @endif
            @else
                {{ $service->plan->count() > 0?number_format($service->plan[0]->amount).$service->plan[0]->currency_text:'' }}
            @endif
            </p>
        </div>
    </a>
</div>
