<?php
// CCC 20220514
?>
<?php
    $myid = 0;
    if (Auth::guard('user')->check())
    {
        $myid = Auth::guard('user')->user()->id;
    }
?>
@foreach ($inquiries as $inquiry)
    <div class="d_faq">
        <div class="Qq Qq2 Qq_m {{ $inquiry->visible == 0 && $inquiry->client_id != $myid ? 'hasno' : '' }}">
            @if ($inquiry->answered_at == null)
                <h2>@lang('sub.sub-mianswer')</h2>sub-
            @else
                <h2 class="end">@lang('sub.sub-answer')</h2>
            @endif
            <em>Q.</em>
            <p>
                {{ $inquiry->title }}
            </p>
            <span><img src="/assets/images/store/plus.svg"></span>
        </div>

        @if ($inquiry->visible == 1 || $inquiry->client_id == $myid)
            <div class="Aa Aa2">
                <div class="QusA">
                    <p>{!! nl2br($inquiry->content) !!}</p>
                </div>
                @if ($inquiry->answered_at != null)
                <div>
                    <em>A.</em>
                    <div class="contact_p">
                        <p>{!! nl2br($inquiry->answer) !!}</p>
                        <div class="master">
                            <h3>{!! $inquiry->service->user->company_name !!}</h3>
                            <h6>{!! $inquiry->answered_at !!}</h6>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        @endif
    </div>
@endforeach
