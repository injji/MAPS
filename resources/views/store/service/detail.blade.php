@extends('layouts.auth')

@section('title', __('store.detail_page'))

@section('body')

@include('layouts.sub_header')
<!-- CCC 20220615 -->
<div class="base_wrap detail_wrap">
    <input type="hidden" id="svc_ok" value="{{ Session::has('svc_ok') ?? '' }}">
    <div class="detail_head">
        @if($service->youtube_url != '')
        <?php
            $video_id = substr($service->youtube_url, strpos($service->youtube_url, "v=")+2);

        ?>
        <iframe src="https://www.youtube.com/embed/{{$video_id}}" title="YouTube video player" framebordesr="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen>
        </iframe>
        @else
        <div class="detail_imgurl">
            <img src="{{ Storage::url($service->banner_image) }} ">
        </div>
        @endif

        <div class="detail_tit">
            <ul class="country">
                @if($service->lang == 'ko')
                <li><a href=""><img src="/assets/images/store/country_k.png"></a></li>
                @elseif($service->lang == 'en')
                <li><a href=""><img src="/assets/images/store/country_a.png"></a></li>
                @elseif($service->lang == 'cn')
                <li><a href=""><img src="/assets/images/store/country_c.png"></a></li>
                @elseif($service->lang == 'jp')
                <li><a href=""><img src="/assets/images/store/country_j.png"></a></li>
                @endif
            </ul>
            <h1>
                {!! nl2br($service->name) !!}
            </h1>

            <h3>{{ $service->user->company_name }}</h3>
            {{-- 인앱상품 --}}
            @if($service->in_app_payment == 1)
                <h2>@lang('store.inapp')</h2>
            {{-- 자체상품 --}}
            @else
                @if($service->free_term > 0)
                    @if($service->free_term == 99999)
                        <h2>@lang('sub.sub-filter_free')</h2>
                    @else
                        <h2>{{ $service->plan->count() > 0?number_format($service->plan[0]->amount).$service->plan[0]->currency_text:'' }}</h2>
                    @endif
                @else
                    <h2>{{ $service->plan->count() > 0?number_format($service->plan[0]->amount).$service->plan[0]->currency_text:'' }}</h2>
                @endif
            @endif

            <div class="pick">
                <ul class="pick_icon">
                    <li>
                        <img src="/assets/images/store/star_gray.svg">
                        {{ number_format($rating, 1) }}
                    </li>
                    <li>
                        <img src="/assets/images/store/comment.svg">
                        {{ $reviewCount }}
                    </li>
                </ul>
                <span class="{{ $pickid >0 ?'mp':'' }}" onclick="myPick()">@lang('sub.sub-pick')</span>
            </div>

            <div class="cata">
                <p><em>@lang('form.category.label')</em> {{ $category1->text.' > '.$category2->text }}</p>
                @if ($service->free_term)
                    <p><em>@lang('form.free_term.label')</em> {{ $service->free_term == 99999?__('messages.free'):$service->free_term.__('messages.freeday') }}</p>
                @endif
            </div>

            <div class="btns" >
                <!-- <button>미리보기</button> -->
                @if (Auth::guard('user')->check())
                    <button type="button" data-toggle="modal" data-target="#detail_btn2">@lang('sub.sub-contact')</button>
                @else
                    @if ($byapps_id)
                        <button type="button" onclick="location.href=`{{ route('store.register.register3.appid' , ['app_id' => $app_id]) }}`">
                    @else
                        <button type="button" onclick="location.href=`{{ route('store.login') }}`">
                    @endif
                            <a style="color:white" >@lang('sub.sub-contact')</a>
                        </button>
                @endif

                @if(!Auth::guard('user')->check())
                    @if ($byapps_id)
                        <button type="button" onclick="location.href=`{{ route('store.register.register3.appid' , ['app_id' => $app_id]) }}`">
                    @else
                        <button type="button" onclick="location.href=`{{ route('store.login') }}`">
                    @endif
                            <a style="color:white" >@lang('store.svcrequest')</a>
                        </button>
                @else
                    @if (count($reqablesvcs) > 0)
                        <!-- <button type="button" onclick="openReqModel()" >@lang('store.svcrequest')</button> -->
                        <button type="button" data-toggle="modal" data-target="#yes_site" >@lang('store.svcrequest')</button>
                        <button type="button" data-toggle="modal" data-target="#service_sincheng_end" class="service_sincheng_end" style="display: none;">서비스신청완료(퍼블용)</button>

                    @else
                        @if (Auth::guard('user')->user()->client_service->where('service_id', $service->id)->count() > 0)
                        <button type="button" data-toggle="modal" data-target="#has_service">@lang('store.svcrequest')</button>
                        @else
                        <button type="button" data-toggle="modal" data-target="#no_site">@lang('store.svcrequest')</button>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<div class="detail_mid">
    <h1>{{ $service->service_info }}</h1>
    <ul>
        @foreach (explode(':::', $service->short_description) as $row)
            <li><span>{{ $loop->iteration }}</span>{{ $row }}</li>
        @endforeach
    </ul>
</div>

<div class="detail_img base_wrap" style="font-size: 16px">
    {!! nl2br($service->full_description) !!}
</div>


<div class="detail_screen">
    <div class="base_wrap">
        <h1>@lang('sub.sub-screenshot')</h1>
        <ul class="arrow">
            <li class="prev_b"><img src="/assets/images/store/prev_box.svg"></li>
            <li class="next_b"><img src="/assets/images/store/next_box.svg"></li>
        </ul>
        @if ($service->image_description)
        <div class="screenshot">
            @foreach (explode(':::', $service->image_description) as $row)
                <div data-toggle="modal" data-target="#screenshot_modal">
                    <img class="screenshot_img" src="{{ Storage::url($row) }}" onclick="screenshot(this)">
                </div>
            @endforeach

        </div>

        <!-- modal -->
        <div class="modal fade" id="screenshot_modal" tabindex="-1" role="dialog" aria-labelledby="basicModal"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <img id="screenshot_modal_img" src="{{ Storage::url($row) }}">
                </div>
            </div>
        </div>
        </div>
        <!-- modal -->

        @endif



    </div>
</div>

<div class="detail_faq base_wrap">
    <h1>@lang('sub.sub-faq')</h1>
    @foreach ($faqs as $faq)
        <div class="d_faq">
            <div class="Qq">
                <em>Q.</em>
                <p>
                    {{ $faq->question }}
                </p>
                <span><img src="/assets/images/store/plus.svg"></span>
            </div>
            <div class="Aa">
                <div>
                    <em>A.</em>
                    <p>
                        {!! nl2br($faq->answer) !!}
                    </p>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="detail_review base_wrap">
    <div class="review_tit">
        <h1>@lang('sub.sub-review')</h1>
        @if (Auth::guard('user')->check())
            @if (Auth::guard('user')->user()->client_service->where('service_id', $service->id)->count() > 0)
                @if(Auth::guard('user')->user()->client_service->where('service_id', $service->id)->first()->review_flag == 1)
                    <button onclick="openReview()" class="only_pctab">@lang('sub.sub-write')</button>
                @endif
            @else
                <button data-toggle="modal" data-target="#req_service" type="button" class="only_pctab">@lang('sub.sub-write')</button>
            @endif
        @else
            <a href="{{ route('store.login') }}">
                <button type="button" class="only_pctab">@lang('sub.sub-write')</button>
            </a>
        @endif
    </div>

    <div class="review_btn">
        <p><img src="/assets/images/store/comment.svg"><span>{{ $reviewCount }}@lang('sub.sub-su_review')</span></p>
        {{-- <ul>
            <li class="active"><a href="">@lang('sub.sub-new')</a></li>
            <li><a href="">@lang('sub.sub-star')</a></li>
        </ul> --}}
    </div>

    <div class="review_content">
        <h2>
            <em id="star_avarage">{{ number_format($rating, 1) }}</em> / 5.0
        </h2>
        <div id="js-load">
            <ul id="review-content">
                @include('partials.store.detail_review', [
                    'reviews' => $reviews,
                    'user' => $service->user,
                ])
            </ul>
        </div>
    </div>

    <div class="more1 only_m" onclick="load()">+ @lang('store.reviewmore')</div>
    <script>
        function load(){
            $('.js-load').show();
            $('.more1').hide();
        }
    </script>

    <div class="review_page only_pctab">
        @if($reviews->currentPage() > 1)
        <button data-page="{{ $reviews->currentPage()-1 }}" class="review-fbtnpage"><img src="/assets/images/store/prev_g.svg"></button>
        @else
        <button data-page="0" class="review-fbtnpage"><img src="/assets/images/store/prev_g.svg"></button>
        @endif
        @for ($i=1; $i <= $reviews->lastPage(); $i++)
            <a data-page="{{ $i }}" class="curpoint review-page {{ $reviews->currentPage() == $i ? 'act' : '' }}">{{ $i }}</a>
        @endfor
        @if($reviews->currentPage() < $reviews->lastPage())
        <button data-page="{{ $reviews->currentPage()+1 }}" class="review-lbtnpage"><img src="/assets/images/store/next_g.svg"></button>
        @else
        <button data-page="0" class="review-lbtnpage"><img src="/assets/images/store/next_g.svg"></button>
        @endif
    </div>
</div>
<?php
// 문의 하기
?>
<div class="detail_faq base_wrap">
    <div class="review_tit">
        <h1>@lang('sub.sub-question')</h1>
        <div>
            @if (Auth::guard('user')->check())
                <button type="button" class="contact_btn2" onclick="location.href=`{{ route('client.inquiry') }}`">@lang('store.contact_his')</button>
                <button type="button" data-toggle="modal" data-target="#detail_btn2">@lang('sub.sub-contact')</button>
            @else
                <button type="button" class="contact_btn2" onclick="location.href=`{{ route('store.login') }}`">@lang('store.contact_his')</button>
                <button type="button" data-toggle="modal" onclick="location.href=`{{ route('store.login') }}`">@lang('sub.sub-contact')</button>
            @endif
        </div>

    </div>
    <div id="inquiry-content">
        @include('partials.store.detail_inquiry', [
            'inquiries' => $inquiries,
        ])
    </div>
    <div class="more2 only_m" onclick="load2()">+ @lang('store.inqumore')</div>
    <script>
        function load2(){
            $('.d_faq').show();
            $('.more2').hide();
        }
    </script>
    <div class="review_page only_pctab">
        @if($inquiries->currentPage() > 1)
        <button data-page="{{ $inquiries->currentPage()-1 }}" class="inquiry-fbtnpage"><img src="/assets/images/store/prev_g.svg"></button>
        @else
        <button data-page="0" class="inquiry-fbtnpage"><img src="/assets/images/store/prev_g.svg"></button>
        @endif
        @for ($i=1; $i <= $inquiries->lastPage(); $i++)
            <a data-page="{{ $i }}" class="curpoint  inquiry-page {{ $inquiries->currentPage() == $i ? 'act' : '' }}">{{ $i }}</a>
        @endfor
        @if($inquiries->currentPage() < $inquiries->lastPage())
        <button data-page="{{ $inquiries->currentPage()+1 }}" class="inquiry-lbtnpage"><img src="/assets/images/store/next_g.svg"></button>
        @else
        <button data-page="0" class="inquiry-lbtnpage"><img src="/assets/images/store/next_g.svg"></button>
        @endif
    </div>
</div>

<div class="about base_wrap">
    <div class="mmmm">
        <h1>@lang('sub.sub-about')</h1>
        <div class="ab_wrap">
            <div class="company">
                {{-- <img src="/assets/images/store/company.jpg"> --}}
                <div class="c_ab">
                    <h3>{{ $service->user->company }}</h3>
                    <span><a href="{{ $service->user->homepage_url }}" target="_blank">{{ $service->user->homepage_url }}</a></span>
                    <div class="company_info_wrap">
                        <button id="company_info_btn">@lang('store.defail_info')</button>

                        <div class="compay_info_content">
                            <ul>
                                <li><span>@lang('store.business_name')</span>{{ $service->user->company_name }}</li>
                                <li><span>@lang('sub.sub-boss')</span>{{ $service->user->director_name }}</li>
                                <li><span>@lang('form.business_no.label')</span>{{ $service->user->business_no }}</li>
                                <li><span>@lang('form.order_report_number.label')</span>{{ $service->user->order_report_number }}</li>
                                <li><span>@lang('form.address.label')</span>{{ $service->user->address }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $('#company_info_btn').click(function () {
                    $('.compay_info_content').fadeToggle();
                });
            </script>
            <div class="company2">
                <div>
                    <?php
                        $category = \App\Models\Agent\ServiceCategory::find($service->user->specialized_field);
                        $lang = \Lang::getLocale();
                    ?>
                    <p><span>@lang('sub.sub-field')</span>{{ ($category->$lang) ?? '' }}</p>
                    {{-- <p><span></span></p>
                    <p><span>@lang('sub.sub-company')</span>
                        <em>@lang('store.corporation')</em> {{ $service->user->business_no }}
                    </p> --}}
                    <p><span>@lang('sub.sub-clock')</span>{{ $service->user->inquiry_time }}</p>
                </div>
                <div>
                    <p><span>@lang('sub.sub-mail')</span>{{ $service->user->manager_email }}</p>
                    <p><span>@lang('sub.sub-tel')</span>{{ $service->user->manager_phone }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="key">
    <div class="base_wrap">
        <h1>@lang('sub.sub-keyword')</h1>
        <ul>
            @foreach (explode(',', $service->search_keyword) as $keyword)
            <li><a href="/search?sort=1&limit=8&category=0&cateindex=0&freecost=0&filtercatory=&keyword={{ $keyword }}">{{ $keyword }}</a></li>
            @endforeach
        </ul>

    </div>
</div>

<!-- Modal 문의 하기 -->
<div class="modal fade contact_modal" id="detail_btn2" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h1>@lang('sub.sub-contact')</h1>
                <div class="modal_company">
                    <img class="svclog" src="{{ Storage::url($service->icon) }}">
                    <div>
                        <h3>{{ $service->name }}</h3>
                        <ul>
                            <li>{{ $service->user->company_name }}<br>{{ $category2->text }}</li>
                            <li>{{ $service->user->manager_phone }}<br>{{ $service->user->manager_email }}</li>
                        </ul>
                    </div>
                </div>
                <form action="javascript:void(0)" onsubmit="createInquiry(this)">
                    <div class="contact_list">
                        <h2>@lang('store.inqutype')</h2>
                        <div class="select_box" data-name="type">
                            <div class="box">
                                <input id="inquiry_type" type="hidden" class="select_val" value="0"/>
                                <div class="select2">{{ explode(',', App\Models\Cms\QuestionOption::where('type', 1)->first()->content)[0] }}</div>
                                <ul class="list">
                                    @foreach(explode(',', App\Models\Cms\QuestionOption::where('type', 1)->first()->content) as $key => $item)
                                        <li value="{{ $key }}">{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="contact_list">
                            <h2>@lang('store.inqurtitle')</h2>
                            <input type="text" id="inquiry_title" placeholder="@lang('service.placeholder2')">
                        </div>
                        <div class="contact_list">
                            <h2>@lang('store.inqurcontxt')</h2>
                            <textarea id="inquiry_content" placeholder="@lang('service.placeholder3')" ></textarea>
                        </div>
                        <div class="contact_list contact_list_secret">
                            <h2>@lang('store.public')</h2>
                            <div class="secret_label">
                                <label>
                                    <input type="radio" id="inquiry_visible" name="visible" value="1" checked>
                                    <span>@lang('service.txt19')</span>
                                </label>
                                <label>
                                    <input type="radio" id="inquiry_visible" name="visible" value="0">
                                    <span>@lang('service.txt20')</span>
                                </label>
                            </div>
                        </div>
                        <div class="contact_list contact_file">
                            <h2>@lang('store.attachfile')</h2>
                            <div class="file_btn">
                                <div class="image-upload">
                                    <label for="file-input">
                                        <div class="upload-icon">
                                            <img class="prev" src="/assets/images/store/file.svg">
                                        </div>
                                    </label>
                                    <input id="file-input" type="file" />
                                </div>
                                <p>
                                    - 10mb @lang('store.limitsize')<br>
                                    <!-- - jpg, jpeg, png, pdf, zip -->
                                </p>
                            </div>
                            <script>
                                var question_file = null;
                                function readURL(input) {
                                    var id = $(input).attr("id");

                                    if (input.files && input.files[0]) {
                                        question_file = input.files[0];
                                        var reader = new FileReader();

                                        reader.onload = function (e) {
                                            $('label[for="' + id + '"] .upload-icon').css("border", "none");
                                            $('label[for="' + id + '"] .icon').hide();
                                            $('label[for="' + id + '"] .prev').attr('src', e.target.result).show();
                                        }

                                        reader.readAsDataURL(input.files[0]);
                                    }
                                }

                                $("input[id^='file-input']").change(function () {
                                    readURL(this);
                                });
                            </script>
                        </div>
                    </div>
                    <ul class="contact_buttons">
                        <li>
                            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('sub.sub-close')</button>
                        </li>

                        <li>
                            <button onclick="addInquiry()">@lang('sub.sub-enrollment')</button>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade no_site" id="no_site" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body ">
                <p>@lang('client.client_sel_site_no')<br>@lang('store.reqsitedes')</p>
                <ul class="contact_buttons">
                    <li>
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('sub.sub-close')</button>
                    </li>
                    <li>
                        <button type="button">
                            <a style="color:white" target="_blank" href="{{ route('client.dashboard') }}">
                                @lang('sub.sub-enrollment')
                            </a>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade no_site" id="has_service" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body ">
                <p>@lang('store.has_service')</p>
                <ul class="contact_buttons">
                    <li>
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('sub.sub-close')</button>
                    </li>

                    <li>
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="goDashboard()">@lang('store.admin_go')</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="modal fade no_site" id="req_service" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body ">
                <p>@lang('store.req_service')</p>
                <ul class="contact_buttons">
                    <li>
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('sub.sub-close')</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
@if (Auth::guard('user')->check() && count($reqablesvcs) > 0)
    <div class="modal fade yes_site" id="yes_site" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body ">
                    <p>@lang('client.client_sel_site')</p>
                    <div class="select_box">
                        <div class="box">
                            <input id="reqsite_id" type="hidden" class="select_val" value="{{ $reqablesvcs->first()->getKey() }}"/>
                            <input id="reqsite_header" type="hidden" class="select_header" value="{{ $reqablesvcs->first()->header }}"/>
                            <input id="reqsite_name" type="hidden" class="select_name" value="{{ $reqablesvcs->first()->name }}"/>
                            <div class="select2">{{ $reqablesvcs->first()->name }}</div>
                            <ul class="list">
                                @foreach ($reqablesvcs as $site)
                                    <li value="{{ $site->getKey() }}" header="{{ $site->header }}">{{ $site->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <ul class="contact_buttons2">
                        <li>
                            <button type="button" onclick="location.href=`{{ route('client.dashboard') }}`">@lang('store.addregister')</button>
                        </li>
                        <li>
                            <button onclick="doOpenservicePay()">@lang('store.dorequest')</button>
                            <button type="button" id="btn_yes_site2" data-toggle="modal" data-target="#yes_site2" data-dismiss="modal" style="display: none;">@lang('store.dorequest')</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade contact_modal" id="servicePay" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h1>@lang('store.svcpay')</h1>
                    <div class="modal_company">
                        <img class="svclog" src="{{ Storage::url($service->icon) }}">
                        <div>
                            <h3>{{ $service->name }}</h3>
                            <ul>
                                <li>{{ $service->user->company_name }}<br>{{ $category2->text }}</li>
                                <li>{{ $service->user->manager_phone }}<br>{{ $service->user->manager_email }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="contact_list">
                        <table>
                            <tr>
                                <th>@lang('store.payoption')</th>
                                <th>@lang('store.payserviceoption')</th>
                                <th>@lang('store.payamount')</th>
                            </tr>

                            <tr>
                                <td>
                                    <select id="pay_option" onchange="changePayOption()">
                                        @foreach ($service->plan as $plan)
                                            <option value="{{ $plan->id.'_'.$plan->term.'_'.$plan->term_unit.'_'.$plan->amount.'_'.$plan->currency.'_'.$plan->name }}">{{ $plan->name }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <div id="pay_term">{{ $service->plan->count() >0 ? $service->plan->first()->term :"" }}
                                    {{ $service->plan->count() > 0 ? ($service->plan->first()->term_unit == 0?__('store.unitmonth'):($service->plan->first()->term_unit == 1?__('store.unitday'):"")) : "" }}
                                    </div>
                                    <!-- <select>
                                        <option>3일</option>
                                        <option>1주일</option>
                                        <option>2주일</option>
                                    </select> -->
                                </td>

                                <td>
                                    <div id="pay_cost">
                                    {{ $service->plan->count() > 0 ? number_format($service->plan->first()->amount) : "" }}
                                    {{ $service->plan->count() > 0 ? config('app.currency')[$service->plan->first()->currency] : "" }}
                                    </div>
                                    <!-- 0원 -->
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="total_pay">
                        <h2>@lang('store.totalcost')</h2>
                        <p id="pay_totalcost">
                            {{ $service->plan->count() > 0 ? number_format($service->plan->first()->amount) : "" }}
                            {{ $service->plan->count() > 0 ? config('app.currency')[$service->plan->first()->currency] : "" }}
                        </p>
                    </div>

                    <ul class="contact_buttons">
                        <li>
                            <button onclick="requestSvc()">@lang('store.dopayment')</button>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
@endif


<div class="modal fade yes_site" id="yes_site3" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body ">
                    <p>@lang('client.client_sel_site')</p>
                    <div class="select_box">
                        <div class="box">
                            {{-- <input id="reqsite_id" type="hidden" class="select_val" value="{{ $reqablesvcs->first()->getKey() }}"/> --}}
                            {{-- <div class="select2">{{ $reqablesvcs->first()->name }}</div>
                            <ul class="list">
                                @foreach ($reqablesvcs as $site)
                                    <li value="{{ $site->getKey() }}">{{ $site->name }}</li>
                                @endforeach
                            </ul> --}}
                        </div>
                    </div>
                    <ul class="contact_buttons2">
                        <li>
                            <button type="button" onclick="location.href=`{{ route('client.dashboard') }}`">@lang('store.addregister')</button>
                        </li>
                        <li>
                            <button data-toggle="modal" data-target="#yes_site3" data-dismiss="modal">@lang('store.dorequest')</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade yes_site" id="yes_site2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body ">
                    <p>'<span class="current_site"></span>'@lang('store.noscript')
                        <span>@lang('store.set_help')</span>
                    </p>

                    <ul class="contact_buttons2">
                        <li>
                            <button type="button" onclick="location.href=`{{ route('client.dashboard') }}`">@lang('client.client_script_btn2')</button>
                        </li>
                        <li>
                            <button data-dismiss="modal">@lang('button.confirm')</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade yes_site" id="service_sincheng_end" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body ">
                    <p>
                        <b>@lang('store.service_end')</b><br>
                        @lang('store.service_end_confirm')
                    </p>

                    <ul class="contact_buttons2">
                        <li>
                            <button type="button" onclick="location.href=`{{ route('client.dashboard') }}`">@lang('button.go_adminpage')</button>
                        </li>
                        <li>
                            <button data-dismiss="modal">@lang('button.confirm')</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<!-- Modal -->
<div class="modal fade review_modal" id="review_modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="javascript:void(0)" onsubmit="createReview(this)">
                <div class="modal-body ">
                    <h1>@lang('sub.sub-review_write')</h1>
                    <div class="modal_company review_company">
                        <img class="svclog" src="{{ Storage::url($service->icon) }}">
                        <div>
                            <h3>{{ $service->name }}</h3>
                            <div class="star_wrap">
                                <h4>@lang('sub.sub-star_review')</h4>
                                <span class="star">
                                    <img src="/assets/images/store/star_no.svg">
                                    <img src="/assets/images/store/star_no.svg">
                                    <img src="/assets/images/store/star_no.svg">
                                    <img src="/assets/images/store/star_no.svg">
                                    <img src="/assets/images/store/star_no.svg">
                                    <span>
                                        <img src="/assets/images/store/star_yes.svg">
                                        <img src="/assets/images/store/star_yes.svg">
                                        <img src="/assets/images/store/star_yes.svg">
                                        <img src="/assets/images/store/star_yes.svg">
                                        <img src="/assets/images/store/star_yes.svg">
                                    </span>
                                    <input type="range" id="rating" name="rating" oninput="drawStar(this)" value="5" step="0.5" min="0" max="5">
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="contact_list">
                        <h2>@lang('sub.sub-re_wr')</h2>
                        <textarea id="reviewcontent" placeholder="@lang('sub.sub-opinion')"></textarea>

                        <ul class="contact_buttons">
                            <li>
                                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('sub.sub-close')</button>
                            </li>

                            <li>
                                <button onclick="writeReview()">@lang('sub.sub-enrollment')</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    /****************************************************************/
    /* m_Completepayment  설명                                      */
    /****************************************************************/
    /* 인증완료시 재귀 함수                                         */
    /* 해당 함수명은 절대 변경하면 안됩니다.                        */
    /* 해당 함수의 위치는 payplus.js 보다먼저 선언되어여 합니다.    */
    /* Web 방식의 경우 리턴 값이 form 으로 넘어옴                   */
    /****************************************************************/
    function m_Completepayment( FormOrJson, closeEvent )
    {
        var frm = document.order_info;

        /********************************************************************/
        /* FormOrJson은 가맹점 임의 활용 금지                               */
        /* frm 값에 FormOrJson 값이 설정 됨 frm 값으로 활용 하셔야 됩니다.  */
        /* FormOrJson 값을 활용 하시려면 기술지원팀으로 문의바랍니다.       */
        /********************************************************************/
        GetField( frm, FormOrJson );


        if( frm.res_cd.value == "0000" )
        {
            frm.submit();
        }
        else
        {
            alert( "[" + frm.res_cd.value + "] " + frm.res_msg.value );

            closeEvent();
        }
    }
</script>
<form name="order_info" id="order_info" method="post" action="">
    <input type="hidden" name="site_cd" value="{{ config('services.kcp.site_code') }}" />
    <input type="hidden" name="ordr_idxx" id="ordr_idxx" maxlength="40" />
    <!-- 신용카드 -->
    <input type="hidden" name="pay_method" value="100000000000" />
    <!-- 가상계좌 -->
    <!-- <input type="hidden" name="pay_method" value="001000000000" /> -->

    <input type="hidden" name="good_name" id="good_name" value="" />
    <input type="hidden" name="good_mny" id="good_mny" value="" maxlength="9" />
    <input type="hidden" name="currency" id="currency" value="WON" />
    <input type="hidden" name="shop_user_id" id="shop_user_id" value="" />
    <input type="hidden" name="buyr_name" value="<?php echo Auth::guard('user')->check()?Auth::guard('user')->user()->manager_name:""; ?>" />
    <input type="hidden" name="buyr_mail" value="<?php echo Auth::guard('user')->check()?Auth::guard('user')->user()->manager_email:""; ?>" />

    <input type="hidden" name="res_cd" value=""/>
    <input type="hidden" name="res_msg" value=""/>
    <input type="hidden" name="enc_info" value=""/>
    <input type="hidden" name="enc_data" value=""/>
    <input type="hidden" name="ret_pay_method" value=""/>
    <input type="hidden" name="tran_cd" value=""/>
    <input type="hidden" name="use_pay_method" value=""/>

    <input type="hidden" name="pay_option" id="payoption" value="" />
    <input type="hidden" name="service_id" id="service_id" value="" />
    <input type="hidden" name="site_id" id="site_id" value="" />
</form>
<form name="Morder_info" id="Morder_info" method="post" action="">
    <input type="hidden" name="ordr_idxx" id="ordr_idxx" maxlength="40" />
    <input type="hidden" name="good_name" id="good_name" value="" />
    <input type="hidden" name="good_mny" id="good_mny" value="" maxlength="9" />
    <input type="hidden" name="ActionResult" value="card">
    <!-- <input type="hidden" name="ActionResult" value="vcnt"> -->

    <input type="hidden" name="site_cd" value="{{ config('services.kcp.site_code') }}" />
    <!-- 신용카드 -->
    <input type="hidden" name="pay_method" value="CARD" />
    <!-- 가상계좌 -->
    <!-- <input type="hidden" name="pay_method" value="VCNT" /> -->
    <!-- 휴대폰 -->
    <input type="hidden" name="user_agent" value="" /> <!--사용 OS-->
    <input type="hidden" name="Ret_URL" value="<?php echo config('app.pre_url').'://'.config('app.domain.store').'/mreqsvcpay'; ?>" />
    <input type="hidden" name="van_code" value="">

    <input type="hidden" name="pay_option" id="payoption" value="" />
    <input type="hidden" name="service_id" id="service_id" value="" />
    <input type="hidden" name="site_id" id="site_id" value="" />
</form>
@endsection

@push('scripts')
<script type="text/javascript" src="{{ config('services.kcp.payplus_url') }}"></script>
<script type="text/javascript">
    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i) || navigator.userAgent.match(/WPDesktop/i);
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };

    /* 표준웹 실행 */
    function jsf__pay( form )
    {
        try
        {
            KCP_Pay_Execute( form );
        }
        catch (e)
        {
            /* IE 에서 결제 정상종료시 throw로 스크립트 종료 */
        }
    }
</script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script src="{{ asset('js/page/store-service-detail.js') }}" charset="utf-8"></script>
<script>
const SERVICE = @json($service);
var sel_siteid = null;
let inApp = {{ $service->in_app_payment }};
let inAppUrl = `{{ $service->url }}`;
console.log(inApp);
console.log(inAppUrl);
$(document).ready(function () {
    if($('#reqsite_name').val()){
        $('.current_site').text($('#reqsite_name').val());
    }
    if($('#svc_ok').val() == 1){
        $('.service_sincheng_end').click();
        $.ajax({
            url: '/service/modal/status',
            method: 'post',
            success: (response) => {
                console.log('session destroy');
            },
            error: (e) => {
                console.log(e.responseJSON);
            }

        })
    }
    $('.screenshot').slick({
        autoplay: true,
        infinite: true,
        slidesToShow: 3,
        dots: false,
        arrows: true,
        prevArrow: $('.prev_b'),
        nextArrow: $('.next_b'),
        responsive: [ // 반응형 웹 구현 옵션
            {
                breakpoint: 768, //화면 사이즈 768px
                settings: {
                    //위에 옵션이 디폴트 , 여기에 추가하면 그걸로 변경
                    slidesToShow: 1,
                }
            }
        ]

    });

});

$('.review-page').click(loadReview = (event = null) => {
    var reviewPage = 0;
    if (event) {
        reviewPage = $(event.target).data().page
    }
    if(reviewPage > 0)
    {
        $.ajax({
            url: `/service/detail/${SERVICE.id}/reviews`,
            data: {
                page: reviewPage,
            },
            success: (response) => {
                $('#review-content').html(response)
                $('.act.review-page').removeClass('act')
                $(event.target).addClass('act')
                if(reviewPage > 1)
                {
                    $('.review-fbtnpage').attr('data-page', reviewPage - 1);
                }else{
                    $('.review-fbtnpage').attr('data-page', 0);
                }
                if(reviewPage < {{$reviews->lastPage()}})
                {
                    $('.review-lbtnpage').attr('data-page', reviewPage + 1);
                }else{
                    $('.review-lbtnpage').attr('data-page', 0);
                }
            }
        })
    }
})

$('.review-fbtnpage').click(function(){
    let reviewPage = parseInt($('.review-fbtnpage').attr('data-page'));
    if(reviewPage != 0)
    {
        var sel_pageid = reviewPage + 1;
        $.ajax({
            url: `/service/detail/${SERVICE.id}/reviews`,
            data: {
                page: reviewPage,
            },
            success: (response) => {
                $('#review-content').html(response);
                if(reviewPage > 1)
                {
                    $('.review-fbtnpage').attr('data-page', reviewPage - 1);
                }else{
                    $('.review-fbtnpage').attr('data-page', 0);
                }
                $('.review-lbtnpage').attr('data-page', reviewPage + 1);
                $('.review-page').removeClass('act');
                $('.review-page:nth-child('+sel_pageid+')').addClass('act');
            }
        })
    }
})

$('.review-lbtnpage').click(function(){
    let reviewPage = parseInt($('.review-lbtnpage').attr('data-page'));
    if(reviewPage != 0)
    {
        let sel_pageid = reviewPage + 1;
        $.ajax({
            url: `/service/detail/${SERVICE.id}/reviews`,
            data: {
                page: reviewPage,
            },
            success: (response) => {
                $('#review-content').html(response);
                $('.review-page').removeClass('act');
                $('.review-page:nth-child('+sel_pageid+')').addClass('act');
                $('.review-fbtnpage').attr('data-page', reviewPage - 1);
                if(reviewPage < {{$reviews->lastPage()}})
                {
                    $('.review-lbtnpage').attr('data-page', reviewPage + 1);
                }else{
                    $('.review-lbtnpage').attr('data-page', 0);
                }
            }
        })
    }
})

function ready_QQ(){
    $('.Qq').on('click', function () {
        function slideDown(target) {
            if(target.hasClass('hasno')) {
                return alert('비공개 문의사항 입니다.');
            }
            slideUp();
            $(target).addClass('on').next().slideDown();
        }

        function slideUp() {
            $('.Qq').removeClass('on').next().slideUp();
        };
        $(this).hasClass('on') ? slideUp() : slideDown($(this));
    })
}

$('.inquiry-page').click(loadInquiry = (event = null) => {
    // alert('22222');
    var inquiryPage = 0;
    if (event) {
        inquiryPage = $(event.target).data().page
    }
    if(inquiryPage > 0)
    {
        $.ajax({
            url: `/service/detail/${SERVICE.id}/inquiries`,
            data: {
                page: inquiryPage,
            },
            success: (response) => {
                // console.log(response);
                // return false;
                $('#inquiry-content').html(response)
                $('.act.inquiry-page').removeClass('act')
                $(event.target).addClass('act')
                if(inquiryPage > 1)
                {
                    $('.inquiry-fbtnpage').attr('data-page', inquiryPage - 1);
                }else{
                    $('.inquiry-fbtnpage').attr('data-page', 0);
                }
                if(inquiryPage < {{$inquiries->lastPage()}})
                {
                    $('.inquiry-lbtnpage').attr('data-page', inquiryPage + 1);
                }else{
                    $('.inquiry-lbtnpage').attr('data-page', 0);
                }
                ready_QQ();
            }
        })
    }
})

$('.inquiry-fbtnpage').click(function(){
    let inquiryPage = parseInt($('.inquiry-fbtnpage').attr('data-page'));
    if(inquiryPage != 0)
    {
        var sel_pageid = inquiryPage + 1;
        $.ajax({
            url: `/service/detail/${SERVICE.id}/inquiries`,
            data: {
                page: inquiryPage,
            },
            success: (response) => {
                $('#inquiry-content').html(response);
                if(inquiryPage > 1)
                {
                    $('.inquiry-fbtnpage').attr('data-page', inquiryPage - 1);
                }else{
                    $('.inquiry-fbtnpage').attr('data-page', 0);
                }
                $('.inquiry-lbtnpage').attr('data-page', inquiryPage + 1);
                $('.inquiry-page').removeClass('act');
                $('.inquiry-page:nth-child('+sel_pageid+')').addClass('act');
                ready_QQ();
            }
        })
    }
})

$('.inquiry-lbtnpage').click(function(){
    let inquiryPage = parseInt($('.inquiry-lbtnpage').attr('data-page'));
    if(inquiryPage != 0)
    {
        var sel_pageid = inquiryPage + 1;
        $.ajax({
            url: `/service/detail/${SERVICE.id}/inquiries`,
            data: {
                page: inquiryPage,
            },
            success: (response) => {
                $('#inquiry-content').html(response);
                $('.inquiry-page').removeClass('act');
                $('.inquiry-page:nth-child('+sel_pageid+')').addClass('act');
                $('.inquiry-fbtnpage').attr('data-page', inquiryPage - 1);
                if(inquiryPage < {{$inquiries->lastPage()}})
                {
                    $('.inquiry-lbtnpage').attr('data-page', inquiryPage + 1);
                }else{
                    $('.inquiry-lbtnpage').attr('data-page', 0);
                }
                ready_QQ();
            }
        })
    }
})

function changePayOption(){
    option_inf = $("#pay_option").val();
    const words = option_inf.split('_');
    let currency_unit = @json(config('app.currency'));
    var id = words[0];
    var term = words[1];
    var term_unit = words[2];
    var amount = words[3].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    var currency = words[4];

    if(term_unit == 0)
    {
        $("#pay_term").html(term+"@lang('store.unitmonth')");
    }else if(term_unit == 1)
    {
        $("#pay_term").html(term+"@lang('store.unitday')");
    }else if(term_unit == 2)
    {
        $("#pay_term").html(term+"@lang('store.unittime')");
    }

    $("#pay_cost").html(amount+currency_unit[currency]);
    $("#pay_totalcost").html(amount+currency_unit[currency]);
}

function openReview(){
    $("#review_modal").modal();
    drawStar({value: {{ $myreview != null?$myreview->rating:5}}});
}

function addInquiry() {
    let request = new FormData();
    request.set('service_id', {{ request()->service }});
    request.set('type', $("#inquiry_type").val());
    request.set('title', $("#inquiry_title").val());
    request.set('content', $("#inquiry_content").val());
    request.set('question_file', question_file);
    request.set('visible', $("#inquiry_visible:checked").val());
    $.ajax({
        url: '/add/inquiry',
        method: 'post',
        data: request,
        contentType: false,
        processData: false,
        success: (response) => {
            $("#detail_btn2").modal('hide');
            if(response.code == 200) {
                // toastr.success(response.message);
                alert(response.message);
                location.replace(document.URL);
            }
        },
        error: (e) => {
            $("#detail_btn2").modal('hide');
            console.log(e.responseJSON);
        }
    });
}

function requestSvc() {
    @if ($service->free_term > 0 || $service->in_app_payment == 1)
        let request = new FormData();
        request.set('service_id', {{ request()->service }});
        request.set('site_id', sel_siteid);
        request.set('pay_option', "{{ '0_'.$service->free_term.'_1'}}");
        $.ajax({
            url: '/getorderno',
            method: 'post',
            success: (response) => {
                if(response.code == 200) {
                    request.set('order_no', response.order_no);
                    $.ajax({
                        url: '/add/svcreq',
                        method: 'post',
                        data: request,
                        contentType: false,
                        processData: false,
                        success: (response) => {
                            $("#servicePay").modal('hide');
                            /*
                            if(response.code == 200) {
                                toastr.success(response.message);
                            }
                            */
                            if(inApp != 1) {
                                $('.service_sincheng_end').click();
                            }else{
                                location.replace(inAppUrl + '?' + response.hmac_query + '?hmac=' + response.hmac);
                            }
                            // location.replace(document.URL);
                        },
                        error: (e) => {
                            $("#servicePay").modal('hide');
                            console.log(e.responseJSON);
                        }
                    });
                }
            },
            error: (e) => {
                console.log(e.responseJSON);
            }
        });
    @elseif(Auth::guard('user')->check())
        $.ajax({
            url: '/getorderno',
            method: 'post',
            success: (response) => {
                if(response.code == 200) {
                    if( isMobile.any() ){
                        var pay_option = $("#pay_option").val();
                        var pay_infs = pay_option.split("_");
                        document.Morder_info.ordr_idxx.value = response.order_no;
                        document.Morder_info.good_name.value = pay_infs[5];
                        document.Morder_info.good_mny.value= pay_infs[3];
                        document.Morder_info.payoption.value = pay_option;

                        document.Morder_info.service_id.value = "<?php echo $service->id; ?>";
                        document.Morder_info.site_id.value = sel_siteid;
                        document.Morder_info.action = "/kcp_api_trade_reg";
                        document.Morder_info.submit();
                    } else{
                        document.order_info.ordr_idxx.value= response.order_no;
                        var pay_option = $("#pay_option").val();
                        var pay_infs = pay_option.split("_");
                        document.order_info.good_name.value= pay_infs[5];
                        document.order_info.shop_user_id.value= "<?php echo Auth::guard('user')->user()->id ?>";
                        if(pay_infs[4] == 0)
                        {
                            document.order_info.currency.value= "WON";
                            document.order_info.good_mny.value= pay_infs[3];

                        }else if(pay_infs[4] == 1){
                            document.order_info.currency.value= "USD";
                            $("#currency").val("USD");
                            document.order_info.good_mny.value = pay_infs[3]+"00";
                        }

                        document.order_info.payoption.value = pay_option;
                        document.order_info.service_id.value = "<?php echo $service->id; ?>";
                        document.order_info.site_id.value = sel_siteid;
                        document.order_info.action = "/reqsvcpay";
                        $("#servicePay").modal('hide');
                        jsf__pay(document.order_info);

                    }
                    // $('.service_sincheng_end').click();
                }
            },
            error: (e) => {
                console.log(e.responseJSON);
            }
        });
    @endif
}

function openReqModel() {
    @if(Auth::guard('user')->check())
        @if (count($reqablesvcs) > 0)
            $("#yes_site").modal();
        @else
            $("#no_site").modal();
        @endif
    @endif
}

function doOpenservicePay() {
    // let header = $('#reqsite_header').val();
    // if(header == 0){
    //     $('#btn_yes_site2').click();
    // }else{
    //     sel_siteid = $("#reqsite_id").val();
    //     $("#yes_site").modal('hide');
    //     if( inApp == 1 ){
    //         requestSvc()
    //     }else{
    //         @if ($service->free_term > 0)
    //             requestSvc()
    //         @else
    //             $("#servicePay").modal();
    //         @endif
    //     }
    // }
    sel_siteid = $("#reqsite_id").val();
    $("#yes_site").modal('hide');
    if( inApp == 1 ){
        requestSvc()
    }else{
        @if ($service->free_term > 0)
            requestSvc()
        @else
            $("#servicePay").modal();
        @endif
    }
}

function writeReview() {
    let request = new FormData();
    request.set('service_id', {{ request()->service }});
    request.set('rating', $("#rating").val());
    request.set('content', $("#reviewcontent").val());
    $.ajax({
        url: '/add/review',
        method: 'post',
        data: request,
        contentType: false,
        processData: false,
        success: (response) => {
            $("#review_modal").modal('hide');
            if(response.code == 200) {
                location.href = document.URL;
                // toastr.success(response.message);
            }
        },
        error: (e) => {
            // $("#review_modal").modal('hide');
            console.log(e.responseJSON);
        }
    });
}
const drawStar = (target) => {
    document.querySelector(`.star span`).style.width = `${target.value * 20}%`;
}
function myPick() {

    $.ajax({
            url: `/pick`,
            method: 'GET',
            data: {svcid:{{ request()->service }}},
            success: (response) => {
                if(response.code == 200){
                    if(response.message == 1)
                    {
                        $('.pick span').addClass('mp');
                    }else{
                        $('.pick span').removeClass('mp');
                    }
                }
                else
                    $('.pick span').removeClass('mp');
            }
        })
}

function goDashboard(){
    location.href="{{ route('client.dashboard') }}";
}

$(document).ready(function () {
    ready_QQ();
    $('.more_wrap2').each(function () {
        //var content = $(this).children('.content');
        var content = $(this).find('#ad_review');

        var content_txt = content.text();
        var content_html = content.html();
        var content_txt_short = content_txt.substring(0, 200) + "...";
        var btn_more = $('<a href="javascript:void(0)" class="more">@lang("sub.sub-more")</a>');


        $(this).append(btn_more);

        if (content_txt.length >= 200) {
            content.html(content_txt_short)

        } else {
            btn_more.hide()
        }

        btn_more.click(toggle_content);

        function toggle_content() {
            if ($(this).hasClass('short')) {
                // 접기 상태
                $(this).html('@lang("sub.sub-more")');
                content.html(content_txt_short)
                $(this).removeClass('short');
            } else {
                // 더보기 상태
                $(this).html('@lang("sub.sub-nomore")');
                content.html(content_html);
                $(this).addClass('short');

            }
        }
    });
});

$(document).ready(function () {

    $('.more_wrap').each(function () {
        //var content = $(this).children('.content');
        var content = $(this).find('#user_review');

        var content_txt = content.text();
        var content_html = content.html();
        var content_txt_short = content_txt.substring(0, 100) + "...";
        var btn_more = $('<a href="javascript:void(0)" class="more">@lang("sub.sub-more")</a>');


        $(this).append(btn_more);

        if (content_txt.length >= 100) {
            content.html(content_txt_short)

        } else {
            btn_more.hide()
        }

        btn_more.click(toggle_content);

        function toggle_content() {
            if ($(this).hasClass('short')) {
                // 접기 상태
                $(this).html('@lang("sub.sub-more")');
                content.html(content_txt_short)
                $(this).removeClass('short');
            } else {
                // 더보기 상태
                $(this).html('@lang("sub.sub-nomore")');
                content.html(content_html);
                $(this).addClass('short');

            }
        }
    });
});
function CustomSelectBox(selector) {
    this.$selectBox = null,
        this.$select = null,
        this.$list = null,
        this.$listLi = null;
        this.$valele = null;
        this.$header = null;
        this.$name = null;
    CustomSelectBox.prototype.init = function (selector) {
        this.$selectBox = $(selector);
        this.$select = this.$selectBox.find('.box .select2');
        this.$list = this.$selectBox.find('.box .list');
        this.$listLi = this.$list.children('li');
        this.$valele = this.$selectBox.find('.box .select_val');
        this.$header = this.$selectBox.find('.box .select_header');
        this.$name = this.$selectBox.find('.box .select_name');
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
        console.log('selected');
        this.$valele.val($target.val());
        this.$header.val($target.attr('header'));
        this.$name.val($target.text());
        $('.current_site').text($target.text());
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
$(function () {
    var select = new CustomSelectBox('.select_box');
});

function screenshot(e){
    $('#screenshot_modal_img').attr('src',$(e).attr('src'));
}
</script>
<style>
    #NAX_BLOCK {
        z-index: 999999 !important;
        top: calc((100vh - 570px) / 2) !important;
    }
    .curpoint{
        cursor: pointer;
    }
    .svclog{
        width:67px;
        height:67px;
        background-size: contain;
    }

    .detail_img.base_wrap img{
        width:100%;
        background-size: contain;
    }

    .screenshot img{
        width:100%;
        height: auto;
        background-size: cover;
    }

    .screenshot .slick-slide{
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .Qq2.on p {
        white-space: unset;
    }

    .detail_faq .d_faq .Aa>div {
        border-top: none;
    }

    .detail_faq .d_faq .Aa>div.QusA {
        border-bottom: 1px solid #ccc;
        padding-top: 0;
    }

    .about .company .c_ab span a {
        color: #888
    }

    @media (min-width: 1400px) {
        .detail_faq .d_faq .Aa>div.QusA p {
            margin-left: 40px;
            width: 1140px;
        }

        .detail_head .detail_imgurl {
            width: 820px;
            height: 461px;
            border: 1px solid #EEEEEE;

        }

        .detail_head .detail_imgurl em {
            width: 100%;
            height: 100%;
        }

        .detail_head .detail_imgurl img {
            width: 100%;
            height: 100%;
        }

        .company_info_wrap {
            position: relative;
        }

        .about .company .c_ab .compay_info_content {
            font-size: 1.4rem;
            position: absolute;
            border: 1px solid #ccc;
            padding: 10px 20px;
            border-radius: 5px;
            background: #fff;
            top: 0;
            left: 60px;
            display: none;
            width: 400px
        }

        .about .company .c_ab .compay_info_content li {
            display: flex;
            justify-content: start;
            align-items: center;
            margin: 5px 0;
        }

        .about .company .c_ab .compay_info_content span {
            width: 120px;
            display: inline-block;
            margin-top: 0;
            font-size: 1.4rem;
        }


    }

    @media (max-width: 1400px) and (min-width: 768px) {
        .detail_faq .d_faq .Aa>div.QusA p {
            margin-left: 55px;
        }

        .detail_head .detail_imgurl {
            position: relative;
            padding-top: 56.25%;
            overflow: hidden;

        }

        .detail_head .detail_imgurl em {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            -webkit-transform: translate(0%, 0%);
            -ms-transform: translate(0%, 0%);
            transform: translate(0%, 0%);
        }

        .detail_head .detail_imgurl img {
            position: absolute;
            top: 49.5%;
            left: 0;
            max-width: 100%;
            height: auto;
            -webkit-transform: translate(-0%, -50%);
            -ms-transform: translate(-0%, -50%);
            transform: translate(-0%, -50%);

        }

        .company_info_wrap {
            position: relative;
        }

        .about .company .c_ab button {
            display: block;
            font-size: 1.4rem;
            color: #3079ff;
            margin-top: 23px;
            background: none;
        }

        .about .company .c_ab .compay_info_content {
            font-size: 1.4rem;
            position: absolute;
            border: 1px solid #ccc;
            padding: 10px 20px;
            border-radius: 5px;
            background: #fff;
            bottom: 0;
            left: 60px;
            width: 373px;
            display: none;
        }

        .about .company .c_ab .compay_info_content li {
            display: flex;
            justify-content: start;
            align-items: center;
            margin: 5px 0;
        }

        .about .company .c_ab .compay_info_content span {
            width: 120px;
            display: inline-block;
            margin-top: 0;
            font-size: 1.4rem;
        }


    }

    @media (max-width: 768px) {

        .detail_head .detail_imgurl {
            position: relative;
            padding-top: 56.25%;
            overflow: hidden;

        }

        .detail_head .detail_imgurl em {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            -webkit-transform: translate(0%, 0%);
            -ms-transform: translate(0%, 0%);
            transform: translate(0%, 0%);
        }

        .detail_head .detail_imgurl img {
            position: absolute;
            top: 49.5%;
            left: 0;
            max-width: 100%;
            height: auto;
            -webkit-transform: translate(-0%, -50%);
            -ms-transform: translate(-0%, -50%);
            transform: translate(-0%, -50%);

        }

        .company_info_wrap {
            position: relative;
        }

        .about .company .c_ab button {
            display: block;
            font-size: 1.4rem;
            color: #3079ff;
            margin-top: 23px;
            background: none;
        }

        .about .company .c_ab .compay_info_content {
            font-size: 1.4rem;
            position: absolute;
            border: 1px solid #ccc;
            padding: 5px 10px;
            border-radius: 5px;
            background: #fff;
            bottom: 0;
            left: 45px;
            width: 310px;
            display: none;
        }

        .about .company .c_ab .compay_info_content li {
            display: flex;
            justify-content: start;
            align-items: center;
            margin: 5px 0;
            font-size: 1.4rem;
        }

        .about .company .c_ab .compay_info_content span {
            width: 100px;
            display: inline-block;
            margin-top: 0;
            font-size: 1.4rem;
        }

    }
</style>
@endpush
