@extends('layouts.agent')

@section('content')

<script src="{{ config('app.agent_asset_url') }}/libs/smarteditor2/js/HuskyEZCreator.js" charset="utf-8"></script>

<form class="form-horizontal position-relative" id="service-create-form">
    <input type="hidden" name="id" value="{{ $service->id }}">
    <div class="tabbable">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link small fw-bold active" data-bs-toggle="tab" data-bs-target="#basic-info" type="button" role="tab">@lang('form.title.basic_info')*</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small fw-bold" data-bs-toggle="tab" data-bs-target="#api-info" type="button" role="tab">@lang('form.title.api_info')</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small fw-bold" data-bs-toggle="tab" data-bs-target="#version_tab" type="button" role="tab">@lang('form.title.version')</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small fw-bold" data-bs-toggle="tab" data-bs-target="#service-info" type="button" role="tab">@lang('form.title.service_info')*</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small fw-bold" data-bs-toggle="tab" data-bs-target="#search" type="button" role="tab">@lang('form.title.search')</button>
            </li>
        </ul>
    </div>
    <div class="tab-content border shadow-sm p-md-10 p-3 pt-5 pb-0 bg-white border-top-0" style="margin-bottom:60px;min-height:calc(100vh - 300px)">
        <div class="tab-pane fade show active" id="basic-info" role="tabpanel" aria-labelledby="basic-info-tab">
            @if($service->process == 2)
                <div class="row mb-4">
                    <div class="col-md-2">
                        <label class="form-label p-0 m-0 fs-6" style="line-height:37px;">@lang('form.reject_reason.label')</label>
                    </div>
                    <div class="col-md-10">
                        <label class="col-form-label pr-2 reject_reas">{{ $service->reject_reason }}</label>
                        <label class="col-form-label">{{ $service->reject_at }}</label>
                    </div>
                </div>
            @endif

            @include('partials.form.title', [
                'title' => __('form.title.basic_info'),
                'required' => true,
            ])

            @include('partials.form.radio', [
                'field' => 'lang',
                'field_text' => 'lang_select',
                'options' => langOption(),
                'labelCol' => 2,
                'value' => $service->lang ?? App::getLocale(),
            ])
            @include('partials.form.select', [
                'field' => 'default_lang',
                'options' => langOption(),
                'labelCol' => 2,
                'layout' => 0,
                'value' => $service->default_lang ?? App::getLocale(),
            ])
            @include('partials.form.input', [
                'field' => 'name',
                'field_text' => 'service_name',
                'value' => $service->name ?? '',
                'labelCol' => 2,
                'descriptionCol' => 12,
                'descriptionRight' => false,
                'mb' => 3,
            ])
            @include('partials.form.input', [
                'field' => 'url',
                'field_text' => 'service_url',
                'value' => $service->url ?? '',
                'labelCol' => 2,
            ])
            @include('partials.form.selectMultipleDepth', [
                'field' => 'category',
                'field_text' => 'service_category',
                'options' => App\Models\Agent\ServiceCategory::getCateForSelectBox(),
                'value' => explode('|', $service->category1.'|'.$service->category2),
                'default' => true,
                'labelCol' => 2,
            ])
            @include('partials.form.switch', [
                'field' => 'visible',
                'field_text' => 'store_visible',
                'disabled' => false,
                'checked' => $service->visible == 1 ? true : false,
                'labelCol' => 2,
            ])
            @include('partials.form.image', [
                'field' => 'icon',
                'value' => $service->icon ? Storage::url($service->icon) : asset('images/xbox.png'),
                'labelCol' => 2,
                'descriptionRight' => false,
                'extensions' => 'png jpeg jpg',
                'validate' => [
                    'width' => 200,
                    'height' => 200,
                ],
            ])
        </div>
        <div class="tab-pane fade" id="api-info" role="tabpanel" aria-labelledby="api-info-tab">
            @include('partials.form.title', [
                'title' => __('form.title.api_info')
            ])
            @include('partials.form.input', [
                'field' => 'redirect_url',
                'field_text' => 'redirect_uri',
                'textarea' => true,
                'labelCol' => 2,
                'value' => $service->redirect_url ?? '',
            ])

            <div class="offset-md-2 col-md-10 desc_pp">
                <label class="p-0 col-form-label small ">
                    - 유효성 검증을 위한 URL을 등록해 주세요.
                </label>
            </div>

            @include('partials.form.input', [
                'field' => 'api_id',
                'labelCol' => 2,
                'readonly' => true,
                'value' => $service->api_id,
                'append' => [
                    [
                        'type' => 'button',
                        'button-type' => 'btn-gray-dark',
                        'text' => __('button.copy'),
                        'on' => ['click' => "copyText($('#api_id').val())"],
                    ]
                ],
            ])
            @include('partials.form.input', [
                'field' => 'api_key',
                'labelCol' => 2,
                'readonly' => true,
                'value' => $service->api_key,
                'append' => [
                    [
                        'type' => 'button',
                        'button-type' => 'btn-primary',
                        'text' => __('button.refresh'),
                        'on' => ['click' => 'refreshKey()'],
                    ],
                    [
                        'type' => 'button',
                        'button-type' => 'btn-gray-dark',
                        'text' => __('button.copy'),
                        'on' => ['click' => "copyText($('#api_key').val())"],
                    ]
                ],
            ])
            @include('partials.form.input', [
                'field' => 'api_key_note',
                'value' => $service->api_key_note ?? '',
                'textarea' => true,
                'labelCol' => 2,
            ])

            <div class="offset-md-2 col-md-10 desc_pp">
                <label class="p-0 col-form-label small ">
                    - 자바스크립트 로딩 후 고객사의 실행 함수 정의. ex) barsQ('caid', '$caid'): 고객사의 barsQ 함수 실행
                </label>
            </div>

            <div class="row mb-3">
                <div class="col-md-2">
                    <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="api_scopes">@lang('form.api_scopes.label')</label>
                </div>
                <div class="col-md-10">
                    <mwc-formfield label="client">
                        <mwc-checkbox id="api_scopes-client" value="client" checked disabled></mwc-checkbox>
                    </mwc-formfield>
                    <mwc-formfield label="script">
                        <mwc-checkbox id="api_scopes-script" value="script" checked disabled></mwc-checkbox>
                    </mwc-formfield>
                    <mwc-formfield label="app">
                        @if(strpos($service->api_scope, 'app.') !== false)
                            <mwc-checkbox id="api_scopes-app" value="app" checked onchange="apiScopeCheck(event)"></mwc-checkbox>
                        @else
                            <mwc-checkbox id="api_scopes-app" value="app" onchange="apiScopeCheck(event)"></mwc-checkbox>
                        @endif
                    </mwc-formfield>
                </div>
            </div>
            <div class="form-group row">
                <div class="offset-md-2 col-md-10 p-0">
                    <div class="table-responsive col-md-12" id="api-scope-table">
                        <table class="table mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">@lang('table.api.scope')</th>
                                    <th scope="col">@lang('table.api.rw')</th>
                                    <th scope="col">@lang('table.api.description')</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="offset-md-2 col-md-10">
                    <label class="p-0 col-form-label small">
                        {!! nl2br(__(('form.api_scopes.description'))) !!}
                    </label>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="version_tab" role="tabpanel" aria-labelledby="version-tab">
            @include('partials.form.title', [
                'title' => __('form.title.version')
            ])
            <div class="form-group row h-100">
                <label class="col-md-2 col-form-label">@lang('form.current_version.label')</label>
                <div class="col-md-10">
                    <label class="col-form-label pr-2">{{ $service->version ?? '1.0' }}</label>
                    <label class="col-form-label">[{{ $service->last_update ?? '-' }}]</label>
                    <button onclick="addVersion()" class="btn btn-xs btn-outline-primary" type="button">
                        <span style="font-size: 1rem;" class="material-icons">add</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="service-info" role="tabpanel" aria-labelledby="service-info-tab">
            @include('partials.form.title', [
                'title' => __('form.title.service_info'),
                'required' => true,
            ])
            @if($service->banner_image)
                <div class="row mb-4">
                    <div class="col-md-2">
                        <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="banner_image">@lang('form.main_banner.label')</label>
                    </div>
                    <div class="col-md-10">
                        <img src="{{ Storage::url($service->banner_image) }}" onclick="$(this).next().click()" style="width:30%;">
                        <input type="file" name="banner_image" class="d-none" accept=".png, .jpeg, .jpg" onchange="thumbnailImg(this, $(this).prev(), [])">
                        <label class="p-0 col-form-label small">{!! nl2br(__(('form.main_banner.description'))) !!}</label>
                    </div>
                </div>
            @else
                @include('partials.form.input', [
                    'field' => 'banner_image',
                    'value' => $service->banner_image,
                    'field_text' => 'main_banner',
                    'extensions' => 'png jpeg jpg',
                    'type' => 'file',
                    'labelCol' => 2,
                    'descriptionCol' => 12,
                    'descriptionRight' => false,
                ])
            @endif
            @include('partials.form.input', [
                'field' => 'youtube_url',
                'value' => $service->youtube_url ?? '',
                'descriptionCol' => 12,
                'mb' => 3,
                'labelCol' => 2,
                'descriptionRight' => false,
            ])
            @include('partials.form.input', [
                'field' => 'service_info',
                'value' => $service->service_info ?? '',
                'descriptionCol' => 12,
                'mb' => 3,
                'labelCol' => 2,
                'descriptionRight' => false,
            ])
            <div class="row mb-3" id="short_description">
                <div class="col-md-2">
                    <label class="form-label p-0 m-0 fs-6" style="line-height:37px;">@lang('form.short_description.label')</label>
                </div>
                @foreach ($service->short_description ? explode(':::', $service->short_description) : [''] as $i => $row)
                    <div class="col-md-10 {{ $loop->first ?: 'offset-md-2' }} mb-1">
                        <div class="input-group">
                            <span class="input-group-text">{{ $i + 1 }}</span>
                            <input class="form-control" sequence="{{ $i }}" value="{{ $row }}" name="short_description[]" type="text">
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="row mb-4">
                <div class="col-md-2">
                    <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="full_description">@lang('form.full_description.label')</label>
                </div>
                <div class="col-md-10">
                    <div class="">
                        <textarea class="form-control" style="width:100%;" id="full_description" name="full_description" type="text" placeholder="@lang('form.full_description.placeholder')">{{$service->full_description}}</textarea>
                    </div>
                </div>
            </div>
            @if($service->image_description)
                <div class="row mb-4">
                    <div class="col-md-2">
                        <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="image_description">@lang('form.screenshot.label')</label>
                    </div>
                    <div class="col-md-10">
                        @foreach(explode(':::', $service->image_description) as $value)
                            @if ($loop->first)
                                <img src="{{ Storage::url($value) }}" style="width:30%; margin-right: 10px;">
                            @else
                                <img class="mt-2" src="{{ Storage::url($value) }}" style="width:30%; margin-right: 10px;">
                            @endif
                        @endforeach
                        <input class="form-control mt-2" value="" multiple="" name="image_description[]" type="file" accept=".png, .jpeg, .jpg">
                    </div>
                </div>
            @else
                @include('partials.form.input', [
                    'field' => 'image_description[]',
                    'field_text' => 'screenshot',
                    'extensions' => 'png jpeg jpg',
                    'type' => 'file',
                    'multiple' => true,
                    'labelCol' => 2,
                    'descriptionCol' => 12,
                    'descriptionRight' => false,
                ])
            @endif
            {{-- @include('partials.form.input', [
                'field' => 'ad_url',
                'value' => $service->ad_url ?? '',
                'labelCol' => 2,
            ])
            @include('partials.form.input', [
                'field' => 'sample_url',
                'value' => $service->sample_url ?? '',
                'labelCol' => 2,
            ]) --}}
            @include('partials.form.switch', [
                'field' => 'in_app_payment',
                'labelCol' => 2,
                'checked' => $service->in_app_payment,
                'on' => ['change' => "$('.payment-plan').toggleClass('d-none', this.checked)"],
            ])
            <div class="row mb-4">
                <div class="col-md-2 col-4">
                    <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="free_term">@lang('form.free_term.label')</label>
                </div>
                <div class="col-md-1" style="line-height:37px;">
                    <div class="form-check form-switch form-switch-md">
                        <input class="form-check-input" onchange="$('.free-period').toggleClass('d-none', !this.checked)" type="checkbox" {{ $service->free_term == 0 ? '' : 'checked' }} name="free_term">
                        <label class="form-check-label" for="free_term-switch"></label>
                    </div>
                </div>
                <div class="col-md-2 form-group row free-period {{ $service->free_term == 0 ? 'd-none' : ''}}">
                    <select name="free_period" class="form-select">
                        <option value="3" {{ $service->free_term == 3 ? 'selected' : '' }}><span>3</span>@lang('sub.agent-day')</option>
                        <option value="5" {{ $service->free_term == 5 ? 'selected' : '' }}><span>5</span>@lang('sub.agent-day')</option>
                        <option value="10" {{ $service->free_term == 10 ? 'selected' : '' }}><span>10</span>@lang('sub.agent-day')</option>
                        <option value="15" {{ $service->free_term == 15 ? 'selected' : '' }}><span>15</span>@lang('sub.agent-day')</option>
                        <option value="20" {{ $service->free_term == 20 ? 'selected' : '' }}><span>20</span>@lang('sub.agent-day')</option>
                        <option value="30" {{ $service->free_term == 30 ? 'selected' : '' }}><span>1</span>@lang('sub.agent-month')</option>
                        <option value="60" {{ $service->free_term == 60 ? 'selected' : '' }}><span>2</span>@lang('sub.agent-month')</option>
                        <option value="90" {{ $service->free_term == 90 ? 'selected' : '' }}><span>3</span>@lang('sub.agent-month')</option>
                        <option value="180" {{ $service->free_term == 180 ? 'selected' : '' }}><span>6</span>@lang('sub.agent-month')</option>
                        <option value="365" {{ $service->free_term == 365 ? 'selected' : '' }}><span>1</span>@lang('sub.agent-year')</option>
                        <option value="99999" {{ $service->free_term == 99999 ? 'selected' : '' }}>@lang('sub.agent-free')</option>
                    </select>
                </div>
            </div>
            <div class="form-group row payment-plan {{ $service->in_app_payment == 0 ? '' : 'd-none'}}">
                <div class="col-md-2">
                    <label for="pament_info" class="col-form-label">
                        @lang('form.payment_info.label')
                    </label>
                </div>
                <div class="col-md-9 pr-0">
                    <div class="table-responsive" id="payment-table">
                        <table class="table mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">@lang('form.product_name.label')</th>
                                    <th scope="col">@lang('form.service_term.label')</th>
                                    <th scope="col">@lang('form.payment_info.label')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($service->plan)
                                    @foreach ($service->plan as $plan)
                                        <tr id="{{ $plan->id }}">
                                            <input type="hidden" class="term" name="plan[{{ $plan->id }}][term]" value="{{ $plan->term }}">
                                            <input type="hidden" class="name" name="plan[{{ $plan->id }}][name]" value="{{ $plan->name }}">
                                            <input type="hidden" class="term_unit" name="plan[{{ $plan->id }}][term_unit]" value="{{ $plan->term_unit }}">
                                            <input type="hidden" class="amount" name="plan[{{ $plan->id }}][amount]" value="{{ $plan->amount }}">
                                            <input type="hidden" class="currency" name="plan[{{ $plan->id }}][currency]" value="{{ $plan->currency }}">
                                            <input type="hidden" class="description" name="plan[{{ $plan->id }}][description]" value="{{ $plan->description }}">
                                            <th scope="row">{{ $loop->index + 1 }}</th>
                                            <td>{{ $plan->name }}</td>
                                            <td>{{ $plan->term ?? '-' }} {{ [
                                                __('messages.month'),
                                                __('messages.day'),
                                            ][$plan->term_unit] ?? '' }}</td>
                                            <td>{{ $plan->amount }} {{ config('app.currency.'.$plan->currency) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-1 pl-0 text-right">
                    <button type="button" class="btn btn-sm btn-primary waves-effect waves-light float-right" onclick="createPaymentForm()">@lang('button.create.0')</button>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-2">
                    <label for="pament_info" class="col-form-label">
                        @lang('form.faq.label')
                    </label>
                </div>
                <div class="col-md-9 pr-0">
                    <div class="table-responsive" id="faq-table">
                        <table class="table mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">@lang('form.category.label')</th>
                                    <th scope="col">@lang('form.question.label')</th>
                                    <th scope="col">@lang('form.answer.label')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($service->faq)
                                    @foreach ($service->faq as $faq)
                                        <tr id="{{ $faq->id }}">
                                            <input type="hidden" class="faq_category" name="faq[{{ $faq->id }}][faq_category]" value="{{ $faq->faq_category }}">
                                            <input type="hidden" class="question" name="faq[{{ $faq->id }}][question]" value="{{ $faq->question }}">
                                            <input type="hidden" class="answer" name="faq[{{ $faq->id }}][answer]" value="{{ $faq->answer }}">
                                            <th>{{ $loop->index + 1 }}</th>
                                            <td>{{ $faq->faq_category }}</td>
                                            <td>{{ $faq->question }}</td>
                                            <td>{{ $faq->answer }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-1 pl-0 text-right">
                    <button type="button" class="btn btn-sm btn-primary waves-effect waves-light float-right" onclick="createFaqForm()">@lang('button.create.0')</button>
                </div>
            </div>
            @include('partials.form.radio', [
                'field' => 'contact_type',
                'field_text' => 'contact',
                'labelCol' => 2,
                'value' => $service->contact_type ?? 0,
                'options' => [
                    ['value' => 0, 'text' => __('form.contact.option.main')],
                    ['value' => 1, 'text' => __('form.contact.option.sub')],
                ],
                'on' => ['change' => 'contactChange($(this).val())']
            ])
            <div class="contact-area">
                <div data-value="0" class="{{ $service->contact_type == 0 ? '' : 'd-none'}}">
                    @include('partials.form.callno', [
                        'field' => 'contact_phone0',
                        'field_text' => 'call_no',
                        'value' => Auth::user()->manager_phone,
                        'labelCol' => 2,
                        'readonly' => true,
                    ])
                    @include('partials.form.email', [
                        'field' => 'contact_email0',
                        'field_text' => 'email',
                        'value' => Auth::user()->manager_email,
                        'readonly' => true,
                        'labelCol' => 2,
                    ])
                </div>
                <div data-value="1" class="{{ $service->contact_type == 1 ? '' : 'd-none'}}">
                    @include('partials.form.callno', [
                        'field' => 'contact_phone1',
                        'value' => $service->contact_phone ?? '',
                        'field_text' => 'call_no',
                        'labelCol' => 2,
                    ])
                    @include('partials.form.email', [
                        'field' => 'contact_email1',
                        'value' => $service->contact_email ?? '',
                        'field_text' => 'email',
                        'labelCol' => 2,
                    ])
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="search" role="tabpanel" aria-labelledby="search-tab">
            @include('partials.form.title', [
                'title' => __('form.title.search')
            ])
            @include('partials.form.tags', [
                'field' => 'search_keyword',
                'value' => ($service->search_keyword != '' && $service->search_keyword != null ? explode(',', $service->search_keyword) : []),
                'labelCol' => 2,
                'descriptionCol' => 12,
                'mb' => 1,
                'descriptionRight' => false,
            ])
            @include('partials.form.tags', [
                'field' => 'specification',
                'value' => ($service->specification != '' && $service->specification != null ? explode(',', $service->specification) : []),
                'labelCol' => 2,
                'descriptionCol' => 12,
                'mb' => 1,
                'descriptionRight' => false,
            ])
            <div class="form-group row">
                <label class="col-md-2 col-form-label">@lang('form.service_month_amount.label')</label>
                <div class="form-inline col-md-10 p-0">
                    <div class="col-md-3">
                        @include('partials.form.select', [
                            'field' => 'search_amount[currency]',
                            'options' => collect(config('app.currency'))->map(function($text, $value) {
                                return compact('text', 'value');
                            }),
                            'layout' => 3,
                            'value' => $service->currency ? array_search($service->currency, config('app.currency')) : 0,
                        ])
                    </div>
                    <div class="col-md-9">
                        <div class="input-group">
                            <div class="input-group mt-1 mt-md-0 w-100">
                                <span class="input-group-text">@lang('button.min')</span>
                                <input type="number" class="form-control" name="search_amount[min]" value="{!! $service->amount_min ?? 0 !!}">
                                <span class="input-group-text">~</span>
                                <span class="input-group-text">@lang('button.max')</span>
                                <input type="number" class="form-control" name="search_amount[max]" value="{!! $service->amount_max ?? 0 !!}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="version-create" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Version</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('partials.form.input', [
                        'field' => 'version',
                        'value' => $service->version ?? '1.0',
                        'readonly' => in_array($service->process, [0, 1]),
                        'layout' => 1,
                    ])
                    @include('partials.form.input', [
                        'field' => 'script_url',
                        'value' => $service->script_url,
                        'layout' => 1
                    ])
                    @include('partials.form.input', [
                        'field' => 'release_note',
                        'value' => $service->release_note,
                        'layout' => 1,
                        'textarea' => true,
                    ])
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-info waves-effect waves-light">@lang('button.save')</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 fixed-bottom border-top p-0 m-0 fixed-description active" style="height:50px;line-height:50px;">
        <div class="col-12 row p-0 m-0" style="background: #fff;">
            <div class="col-12 text-right">
                @if (in_array($service->process, [0, 2, 3, 5]))
                    <button type="button" onclick="review()" class="btn btn-primary waves-effect waves-light col-xs-6">
                        @lang('button.review_deploy')
                    </button>
                @endif
                <button type="button" onclick="save()" class="btn btn-secondary waves-effect waves-light">
                    @lang('button.save')
                </button>
            </div>
        </div>
    </div>
</form>

<div id="faq-create" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="javascript:void(0)" id="faq-create-form">
                <input type="hidden" name="update">
                <div class="modal-body">
                    <div class="row mb-4">
                        <?php
                            $faq_options = [];
                            $options = App\Models\Cms\QuestionOption::where('type', 4)->first();
                            $faq_options_arr = explode(',',$options->content);
                            $selectCol = count($faq_options_arr) + 1;
                            foreach($faq_options_arr as $option){
                                $faq_options[] = [
                                    'value' => $option,
                                    'text' => $option,
                                ];
                            }
                        ?>
                        @include('partials.form.input', [
                            'field' => 'question',
                            'layout' => 3,
                            'labelCol' => 12,
                            'selectCol' => $selectCol,
                            'select' => [
                                'options' => $faq_options,
                                'field' => 'faq_category',
                                'default' => true,
                                'value' => $faq_options[0]['value'],
                            ]
                        ])

                    </div>

                    @include('partials.form.input', [
                        'field' => 'question',
                        'validate_message' => __('validation.required'),
                        'layout' => 1,
                        'textarea' => false,
                        'labelCol' => 2,
                        'value' => '',
                    ])


                    @include('partials.form.input', [
                        'field' => 'answer',
                        'validate_message' => __('validation.required'),
                        'layout' => 1,
                        'textarea' => true,
                        'labelCol' => 2,
                    ])
                    <!-- @include('partials.form.input', [
                        'field' => 'file',
                        'type' => 'file',
                        'labelCol' => 0,
                        'layout' => 0,
                        'descriptionCol' => 12,
                        'descriptionRight' => false,
                    ]) -->
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="deleteFaq()" class="btn delete-button btn-danger waves-effect waves-light">@lang('button.delete')</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">@lang('button.create.0')</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="payment-create" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    {!! nl2br(__('messages.payment_info')) !!}
                </span>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="javascript:void(0)" id="payment-form">
                <input type="hidden" name="update" value="">
                <div class="modal-body">
                    @include('partials.form.input', [
                        'field' => 'product_name',
                        'layout' => 1,
                        'validate_message' => __('validation.required'),
                        'required' => true,
                    ])
                    @include('partials.form.selectMultipleDepth', [
                        'field' => 'service_term',
                        'field_text' => 'service_term',
                        'invalid_feedback' => __('validation.required'),
                        'labelCol' => 12,
                        'options' => [
                            [
                                'value' => 0,
                                'text' => __('form.service_term.option.0'),
                                'child' => (function() {
                                    $result = [];
                                    for ($i = 0; $i < 12; $i ++) {
                                        $result[] = [
                                            'value' => $i + 1,
                                            'text' => ($i + 1).__('messages.month'),
                                        ];
                                    }
                                    return $result;
                                })()
                            ],
                            [
                                'value' => 1,
                                'text' => __('form.service_term.option.1'),
                                'child' => (function() {
                                    $result = [];
                                    for ($i = 0; $i < 25; $i ++) {
                                        $result[] = [
                                            'value' => $i + 1,
                                            'text' => ($i + 1).__('messages.day')
                                        ];
                                    }
                                    return $result;
                                })()
                            ],
                            [
                                'value' => 2,
                                'text' => __('form.service_term.option.2'),
                            ],
                        ],
                    ])
                    @include('partials.form.input', [
                        'field' => 'amount',
                        'field_text' => 'payment_info',
                        'layout' => 0,
                        'type' => 'number',
                        'labelCol' => 12,
                        'select' => [
                            'options' => collect(config('app.currency'))->map(function($text, $val) {
                                return [
                                    'value' => $val,
                                    'text' => $text,
                                ];
                            }),
                            'field' => 'currency',
                            'default' => true,
                        ],
                        'value' => 0
                    ])
                    @include('partials.form.input', [
                        'field' => 'description',
                        'layout' => 1,
                        'textarea' => true,
                        'required' => true,
                        'validate_message' => __('validation.required'),
                    ])
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="deletePlan()" class="btn delete-button btn-danger waves-effect waves-light">@lang('button.delete')</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">@lang('button.create.0')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let faqFile = {};
    const term = @json([__('messages.month'), __('messages.day')]);
    const faqCategory = @json(App\Models\Agent\ServiceFaq::getCategorys());
    let apiScopes = "{{ $service->api_scope }}".split('#');
    let scope_arr = JSON.parse(@json(json_encode(__('scope'))));
    var oEditors = [];
    var clickTab4 = false;

    paymentTable();
    faqTable();

    $(".nav-tabs li:nth-child(4) button").click(function () {
        if(!clickTab4) {
            clickTab4 = true;
            nhn.husky.EZCreator.createInIFrame({
                oAppRef: oEditors,
                elPlaceHolder: "full_description",
                sSkinURI: "{{ config('app.agent_asset_url') }}/libs/smarteditor2/SmartEditor2Skin_photo.html",
                fCreator: "createSEditor2"
            });
        }
    });

    setTimeout(() => {
        nhn.husky.EZCreator.createInIFrame({
            oAppRef: oEditors,
            elPlaceHolder: "full_description",
            sSkinURI: "{{ config('app.agent_asset_url') }}/libs/smarteditor2/SmartEditor2Skin_photo.html",
            fCreator: "createSEditor2"
        });
    }, 500);

    function review(){
        let request = new FormData($('#service-create-form')[0]);

        let contact_type = parseInt(request.get('contact_type'));
        var phone_arr = [];
        $("input[name='contact_phone1']").each(function() {
            phone_arr.push($(this).val());
        });

        var email_arr = [];
        $("input[name='contact_email1']").each(function() {
            if(email_arr.length < 2)
                email_arr.push($(this).val());
        });

        request.set('id', "{{ $service->id }}");
        request.set('process', 1);
        request.set('version', $("#version").val());
        request.set('script_url', $("#script_url").val());
        request.set('release_note', $("#release_note").val());
        request.set('currency', currency[request.get('search_amount[currency]')]);
        request.set('amount_min', request.get('search_amount[min]'));
        request.set('amount_max', request.get('search_amount[max]'));
        request.set('contact_phone', phone_arr.join('-'));
        request.set('contact_email', email_arr[0] != '' ? email_arr.join('@') : '');
        request.set('api_scope', apiScopes.join('#'));

        oEditors.getById["full_description"].exec("UPDATE_CONTENTS_FIELD", []);
        request.set('full_description', $("#full_description").val());

        $.ajax({
            url: '/service/store',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                if(response.code == 200)
                    location.href = document.URL;
                else{
                    var messages = Object.values(response.error);

                    for (let i = 0; i < messages.length; i++){
                        toastr.error(messages[i][0]);
                        break;
                    }
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });
    }

    function save(){
        let request = new FormData($('#service-create-form')[0]);

        let contact_type = parseInt(request.get('contact_type'));
        var phone_arr = [];
        $("input[name='contact_phone1']").each(function() {
            phone_arr.push($(this).val());
        });

        var email_arr = [];
        $("input[name='contact_email1']").each(function() {
            if(email_arr.length < 2)
                email_arr.push($(this).val());
        });

        request.set('id', "{{ $service->id }}");
        request.set('process', "{{ $service->process }}");
        request.set('version', $("#version").val());
        request.set('script_url', $("#script_url").val());
        request.set('release_note', $("#release_note").val());
        request.set('currency', currency[request.get('search_amount[currency]')]);
        request.set('amount_min', request.get('search_amount[min]'));
        request.set('amount_max', request.get('search_amount[max]'));
        request.set('contact_phone', phone_arr.join('-'));
        request.set('contact_email', email_arr[0] != '' ? email_arr.join('@') : '');
        request.set('api_scope', apiScopes.join('#'));

        oEditors.getById["full_description"].exec("UPDATE_CONTENTS_FIELD", []);
        request.set('full_description', $("#full_description").val());

        $.ajax({
            url: '/service/store',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                if(response.code == 200) {
                    toastr.success(response.message);
                    location.href = document.URL;
                }
                else{
                    var messages = Object.values(response.error);

                    for (let i = 0; i < messages.length; i++){
                        toastr.error(messages[i][0]);
                        break;
                    }
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });
    }

    function thumbnailImg(object, obj, validate = {}){
        var file = object.files[0];
        let reader = new FileReader();

        reader.onload = function (event) {
            var img = new Image();
            img.src = event.currentTarget.result;
            img.onload = (event) => {
                if (
                    (!('height' in validate) || validate.height == event.currentTarget.height) &&
                    (!('width' in validate) || validate.width == event.currentTarget.width)
                ) {
                    obj.attr('src', event.currentTarget.src);
                } else {
                    toastr.error("{{ __('validation.image.size') }}");
                    object.value = '';
                }
            }
        }

        if (file)
            reader.readAsDataURL(file);
        else
            obj.attr('src', obj.attr('data-xbox'));
    }

    function refreshKey(){
        var url = "{{ route('agent.get_key', ':api_id') }}";
        url = url.replace(':api_id', '{{ $service->api_id }}');

        $.ajax({
            url: url,
            method: 'get',
            success: (response) => {
                if(response.code == 200)
                    $("#api_key").val(response.key);
            }
        });
    }

    function addVersion(){
        $('#version-create').modal('show');
    }

    let appendInput = (event) => {
        if (event.keyCode == 13) {
            let index = $('#short_description input').index(event.target) + 1;
            if (index == $('[name="short_description[]"]').length && $('[name="short_description[]"]').length < 5) {
                $('#short_description').append(`<div class="col-md-10 offset-md-2 mb-1">
                    <div class="input-group">
                        <span class="input-group-text">${$('[name="short_description[]"]').length + 1}</span>
                        <input class="form-control" sequence="${$('[name="short_description[]"]').length}" name="short_description[]" type="text">
                    </div>
                </div>`);
                $('#short_description input').unbind();
                $('#short_description input').keydown(appendInput);
            }
            $('#short_description input').eq(index).focus();
        } else if (event.keyCode == 8) {
            if (($(event.target).attr('sequence') * 1) + 1 == $('[name="short_description[]"]').length && event.target.value === '') {
                $(event.target).parents('.mb-1').remove();
                $('#short_description input:last').focus();
                $('#short_description input:last')[0].setSelectionRange(999999, 999999);
                event.preventDefault();
            }
        }
    }

    $('#short_description input').keydown(appendInput);

    function contactChange(val){
        $('.contact-area>div').addClass('d-none');
        $('.contact-area').find(`[data-value=${val}]`).removeClass('d-none');
    }

    function createPaymentForm(){
        $("#product_name").val('');
        $("[name=description]").val('');
        $('#payment-create').modal('show');
        $('#payment-create form')[0].reset();
        $('#payment-create').removeClass('update');
        $('#payment-form select').change();
        $('#payment-create .btn.delete-button').hide();
    }

    $('#payment-form').submit((event) => {
        let form = $(event.target)
        form.find('.is-invalid').removeClass('is-invalid')
        // 유효성 검사
        let payment = {
            name: form.find('[name=product_name]').val(),
            term: form.find('[name="service_term[]"]').eq(1).val(),
            term_unit: form.find('[name="service_term[]"]').eq(0).val(),
            amount: form.find('[name=amount]').eq(0).val() * 1,
            currency: form.find('[name=currency]').eq(0).val(),
            description: form.find('[name=description]').val(),
        }
        try {
            if (
                !payment.name ||
                !payment.term_unit ||
                !payment.amount || payment.amount == 0 ||
                !payment.currency ||
                !payment.description ||
                (payment.term_unit != 2 && !payment.term)
            ) {
                throw ''
            }
        } catch (e) {
            return
        } finally {
            if (!payment.name) {
                form.find('[name=product_name]').addClass('is-invalid')
            }
            if (!payment.term_unit) {
                form.find('[name="service_term[]"]').eq(0).addClass('is-invalid').selectpicker('refresh')
            }
            if (!payment.amount || payment.amount == 0) {
                form.find('[name=amount]').addClass('is-invalid')
            }
            if (!payment.currency) {
                form.find('[name=currency]').addClass('is-invalid').selectpicker('refresh')
            }
            if (payment.term_unit != 2 && !payment.term) {
                form.find('[name="service_term[]"]').eq(1).addClass('is-invalid').selectpicker('refresh')
            }
            if (!payment.description) {
                form.find('[name=description]').addClass('is-invalid')
            }
        }
        if (form.find('[name=update]').val()) {
            let id = form.find('[name=update]').val()
            let no = $(`#payment-table tbody #${id}`).index($('#payment-table tbody tr'))
            $(`#payment-table tbody #${id}`).html(`
                <input type="hidden" class="name" name="plan[${id}][name]" value="${payment.name}">
                <input type="hidden" class="term" name="plan[${id}][term]" value="${payment.term}">
                <input type="hidden" class="term_unit" name="plan[${id}][term_unit]" value="${payment.term_unit}">
                <input type="hidden" class="amount" name="plan[${id}][amount]" value="${payment.amount}">
                <input type="hidden" class="currency" name="plan[${id}][currency]" value="${payment.currency}">
                <input type="hidden" class="description" name="plan[${id}][description]" value="${payment.description}">
                <th scope="row">${no + 1}</th>
                <td>${payment.name}</td>
                <td>${payment.term || '-'} ${term[payment.term_unit] || ''}</td>
                <td>${payment.amount} ${currency[payment.currency]}</td>`)
        } else {
            let id = `new-${Math.round(Math.random() * 100000000)}`
            $('#payment-table tbody').append(`<tr id="${id}">
                <input type="hidden" class="name" name="plan[${id}][name]" value="${payment.name}">
                <input type="hidden" class="term" name="plan[${id}][term]" value="${payment.term}">
                <input type="hidden" class="term_unit" name="plan[${id}][term_unit]" value="${payment.term_unit}">
                <input type="hidden" class="amount" name="plan[${id}][amount]" value="${payment.amount}">
                <input type="hidden" class="currency" name="plan[${id}][currency]" value="${payment.currency}">
                <input type="hidden" class="description" name="plan[${id}][description]" value="${payment.description}">
                <th scope="row">${$('#payment-table').find('tbody tr').length + 1}</th>
                <td>${payment.name}</td>
                <td>${payment.term || ''} ${term[payment.term_unit]}</td>
                <td>${payment.amount} ${currency[payment.currency]}</td>
            </tr>`)
        }
        paymentTable();
        event.target.reset()
        $('#payment-create').removeClass('update')
        $('#payment-form select').change()
        $('#payment-create').modal('hide')
    })

    function deletePlan(){
        $(`#payment-table #${$('#payment-create input[name=update]').val()}`).remove()
        $('#payment-form')[0].reset()
        $('#payment-form').find('.selectpicker').change()
        $('#payment-create').removeClass('update')
        $('#payment-create').modal('hide')
        $.each($('#payment-table tbody tr'), (i, item) => {
            $(item).find('th').eq(0).html(i + 1)
        })
    }

    function paymentTable(){
        $('#payment-table tbody tr').unbind()
        $('#payment-table tbody tr').click((event) => {
            $('#payment-create').addClass('update');
            $('#payment-create').find('[name=update]').val(event.currentTarget.id)
            $('#payment-create').find('[name=product_name]').val($(event.currentTarget).find('.name').val())
            $('#payment-create').find('[name="service_term[]"]').eq(0).val($(event.currentTarget).find('.term_unit').val()).change()
            $('#payment-create').find('[name="service_term[]"]').eq(1).val($(event.currentTarget).find('.term').val()).change()
            $('#payment-create').find('[name=amount]').eq(0).val($(event.currentTarget).find('.amount').val())
            $('#payment-create').find('[name=currency]').eq(0).val($(event.currentTarget).find('.currency').val()).change()
            $('#payment-create').find('[name=description]').val($(event.currentTarget).find('.description').val())
            $('#payment-create .btn.delete-button').show();
            $('#payment-create').modal('show');
        })
    }

    function createFaqForm(){
        $("[name=update]").val('');
        $("[name=question]").val('');
        $("[name=answer]").val('');
        $('#faq-create').modal('show');
        $('#faq-create').removeClass('update');
        $('#faq-create .btn.delete-button').hide();
    }

    $('#faq-create-form').submit((event) => {
        let form = new FormData(event.target)
        form.set('faq_category', $('#faq_category').val())
        form.set('question', $('#question').val())
        form.set('answer', $('#answer').val())
        $(event.target).find('.is-invalid').removeClass('is-invalid')
        try {
            if (
                !form.get('faq_category') ||
                !form.get('question') ||
                !form.get('answer')
            ) {
                throw '';
            }
        } catch (e) {
            console.error(e)
            return
        } finally {
            if (!form.get('faq_category')) {
                $(event.target).find('[name=faq_category]').addClass('is-invalid')
            }
            if (!form.get('question')) {
                $(event.target).find('[name=question]').addClass('is-invalid')
            }
            if (!form.get('answer')) {
                $(event.target).find('[name=answer]').addClass('is-invalid')
            }
        }

        if (form.get('update')) {
            let id = form.get('update')
            if (form.get('file')) {
                faqFile[id] = form.get('file')
            }
            $(`#faq-table tbody #${id}`).html(`
                <input type="hidden" class="faq_category" name="faq[${id}][faq_category]" value="${form.get('faq_category')}">
                <input type="hidden" class="question" name="faq[${id}][question]" value="${form.get('question')}">
                <input type="hidden" class="answer" name="faq[${id}][answer]" value="${form.get('answer')}">
                <th>${$('#faq-table tbody tr').length + 1}</th>
                <td>${form.get('faq_category')}</td>
                <td>${form.get('question')}</td>
                <td>${form.get('answer')}</td>`)

                $('#faq-table tbody tr').each(function (e){
                    $(this).find('th').html(e+1);
                })
        } else {
            let id = `new-${Math.round(Math.random() * 100000000)}`
            if (form.get('file')) {
                faqFile[id] = form.get('file')
            }
            $('#faq-table tbody').append(`
                <tr id="${id}">
                    <input type="hidden" class="faq_category" name="faq[${id}][faq_category]" value="${form.get('faq_category')}">
                    <input type="hidden" class="question" name="faq[${id}][question]" value="${form.get('question')}">
                    <input type="hidden" class="answer" name="faq[${id}][answer]" value="${form.get('answer')}">
                    <th>${$('#faq-table tbody tr').length + 1}</th>
                    <td>${form.get('faq_category')}</td>
                    <td>${form.get('question')}</td>
                    <td>${form.get('answer')}</td>
                </tr>`)
        }
        event.target.reset()
        $(event.target).find('.selectpicker').change()
        $('#faq-create').removeClass('update')
        $('#faq-create').modal('hide')
        faqTable()
    })

    function deleteFaq(){
        $(`#faq-table #${$('#faq-create input[name=update]').val()}`).remove()
        // $('#faq-create-form')[0].reset()
        $("[name=update]").val('');
        $("[name=question]").val('');
        $("[name=answer]").val('');
        $('#faq-create-form').find('.selectpicker').change()
        $('#faq-create').removeClass('update')
        $('#faq-create').modal('hide')
        $.each($('#faq-table tbody tr'), (i, item) => {
            $(item).find('th').eq(0).html(i + 1)
        })
    }

    function faqTable(){
        $('#faq-table tbody tr').unbind()
        $('#faq-table tbody tr').click((event) => {
            $('#faq-create').addClass('update')
            $('#faq-create').find('[name=update]').val(event.currentTarget.id)
            $('#faq-create').find('[name=faq_category]').val($(event.currentTarget).find('.faq_category').val()).change()
            $('#faq-create').find('[name=question]').val($(event.currentTarget).find('.question').val())
            $('#faq-create').find('[name=answer]').val($(event.currentTarget).find('.answer').val())
            $('#faq-create .btn.delete-button').show();
            $('#faq-create').modal('show')
        })
    }

    function apiScopeChange(event) {
        let beforeScope = `${$(event.target).attr('data-scope')}.${$(event.target).val() == 'read' ? 'write' : 'read'}`;
        let scope = `${$(event.target).attr('data-scope')}.${$(event.target).val()}`;
        $(event.target).parents('tr').find('.description').html(scope_arr[scope.split('.')[0]][scope.split('.')[1]]);
        if ((idx = apiScopes.findIndex(value => value == beforeScope)) !== -1) {
            apiScopes.splice(idx, 1, scope);
        }
    }

    function apiScopeCheck(event) {
        let scope = $(event.target).attr('value');

        if (event.target.checked) {
            addScope(`${scope}.read`)
            apiScopes.push(`${scope}.read`)
        } else {
            if ((idx = apiScopes.findIndex(value => value.startsWith(scope))) !== -1) {
                apiScopes.splice(idx, 1)
                $(`#api-scope-table tbody tr[data-scope=${scope}]`).remove()
            }
        }
    }

    function setScope() {
        $('#api-scope-table tbody').html('');
        apiScopes.forEach((item, i) => {
            $(`#scope-${item.split('.')[0]}`).prop("checked", true);
            addScope(item);
        });
    }

    function addScope(item) {
        $('#api-scope-table tbody').append(`<tr data-scope="${item.split('.')[0]}">
            <td>${$('#api-scope-table tbody tr').length + 1}</td>
            <td>${item.split('.')[0]}</td>
            <td>
                <select onchange="apiScopeChange(event)" data-scope="${item.split('.')[0]}" class="form-select" name="category[]" id="category">
                    <option value="read" ${item.split('.')[1] == 'read' ? 'selected' : ''}>Read</option>
                    <option value="write" ${item.split('.')[1] == 'write' ? 'selected' : ''}>Write</option>
                </select>
            </td>
            <td class="description">`+scope_arr[item.split('.')[0]][item.split('.')[1]]+`</td>
        </tr>`)
    }

    setScope();



    // $('#service_term').change(function(){
    //     var servicet = $('#service_term option:selected').val();
    //     if(servicet == 2){

    //     }
    // })
</script>
@endpush
