@foreach ($reviews as $review)
    <li class="js-load">
        <div class="user_type">
            <div id="star">
                @for ($i = 0; $i < 5; $i++)
                    <img src="{{ asset('images/store/star_'.($review->rating && $review->rating > $i ? 'on' : 'off').'.svg') }}">
                @endfor
            </div>
            <h3>{{ Str::limit($review->author->account, 2, '*****') }}</h3>
            <h5>{{ $review->created_at->format('Y.m.d H:i:s') }}</h5>
        </div>
        <div class="review_txt">
            <div class="more_wrap">
                <p id="user_review">
                    {!! nl2br($review->content) !!}
                </p>
            </div>
            @if ($review->answered_at != null)
                <div class="answer">
                    <div class="more_wrap2">
                        <p id="ad_review">
                            {!! nl2br($review->answer) !!}
                        </p>
                    </div>
                    <div class="master">
                        <h3>{{ $user->company }}</h3>
                        <h6>{{ $review->answered_at->format('Y.m.d H:i:s') }}</h6>
                    </div>
                </div>
            @endif
        </div>
    </li>
@endforeach
