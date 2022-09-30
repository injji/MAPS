@extends('layouts.agent')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-7 col-xl-10">
                <div class="card card-raised shadow-10 mt-5 mt-xl-10 mb-5">
                    <div class="card-body p-5">
                        <form id="user-info-form" class="my_about">
                            <mwc-tab-bar class="nav nav-tabs" role="tablist" activeIndex="{{ $tab == 1 ? 0 : 1 }}">
                                <mwc-tab label="@lang('page.user_info')" data-bs-toggle="tab" role="tab" aria-selected="true" id="baseNavDemoTab" data-bs-target="#baseNavDemo" aria-controls="baseNavDemo"></mwc-tab>
                                <mwc-tab label="계좌정보" data-bs-toggle="tab" role="tab" aria-selected="false" id="baseNavHtmlTab" data-bs-target="#baseNavHtml" aria-controls="baseNavHtml"></mwc-tab>
                            </mwc-tab-bar>
                            <div class="tab-content mb-5">
                                <div class="tab-pane{{ $tab == 1 ? ' show active' : '' }}" role="tabpanel" id="baseNavDemo" aria-labelledby="baseNavDemoTab">
                                    @include('partials.form.title', [
                                        'title' => 'page.user_info',
                                    ])
                                    @include('partials.form.input', [
                                        'field' => '',
                                        'field_text' => 'account',
                                        'value' => $user->account,
                                        'readonly' => true,
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'password',
                                        'type' => 'password',
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'c_password',
                                        'type' => 'password',
                                    ])

                                    @include('partials.form.title', [
                                        'title' => 'page.company_info',
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'company_name',
                                        'value' => $user->company_name,
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'business_no',
                                        'value' => $user->business_no,
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'director_name',
                                        'value' => $user->director_name,
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'address',
                                        'value' => $user->address,
                                    ])
                                    @if($user->business_registration)
                                        <div class="row mb-4">
                                            <div class="col-md-3">
                                                <label class="form-label p-0 m-0 fs-6" style="line-height:37px;">@lang('form.business_registration.label')</label>
                                            </div>
                                            <div class="col-md-9">
                                                <div id="business_registration_name" style="line-height:34px;height:34px;">{{ explode('/', $user->business_registration)[2] }}</div>
                                                <input class="form-control" value="" name="business_registration" type="file" accept="." style="position:absolute;top:0;opacity:0;" onchange="changeBReg(this)">
                                            </div>
                                        </div>
                                    @else
                                        @include('partials.form.input', [
                                            'field' => 'business_registration',
                                            'value' => '',
                                            'type' => 'file',
                                        ])
                                    @endif
                                    @include('partials.form.input', [
                                        'field' => 'order_report_number',
                                        'value' => $user->order_report_number,
                                    ])
                                    {{-- 전문분야 --}}
                                    @include('partials.form.input', [
                                        'field' => 'homepage_url',
                                        'value' => $user->homepage_url,
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'director_phone',
                                        'value' => $user->director_phone,
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'director_email',
                                        'value' => $user->director_email,
                                    ])

                                    @include('partials.form.title', [
                                        'title' => 'page.manager_info',
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'manager_name',
                                        'value' => $user->manager_name,
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'manager_phone',
                                        'value' => $user->manager_phone,
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'manager_email',
                                        'value' => $user->manager_email,
                                    ])
                                </div>
                                <div class="tab-pane{{ $tab == 2 ? ' show active' : '' }}" role="tabpanel" id="baseNavHtml" aria-labelledby="baseNavHtmlTab">
                                    @include('partials.form.title', [
                                        'title' => 'page.settlement_info',
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'account_holder',
                                        'value' => $user->account_holder,
                                    ])
                                    @include('partials.form.select', [
                                        'field' => 'bank_name',
                                        'value' => $user->bank_name,
                                        'default' => true,
                                        'options' => collect($banks)->map(function($bank) {
                                            return [
                                                'value' => $bank,
                                                'text' => $bank,
                                            ];
                                        }),
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'account_number',
                                        'value' => $user->account_number,
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'tax_email',
                                        'value' => $user->tax_email,
                                    ])
                                    @include('partials.form.title', [
                                        'title' => 'page.tax_tail_info',
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'tax_company_name',
                                        'value' => $user->tax_company_name,
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'tax_business_no',
                                        'value' => $user->tax_business_no,
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'tax_director_name',
                                        'value' => $user->tax_director_name,
                                    ])
                                    @include('partials.form.input', [
                                        'field' => 'tax_address',
                                        'value' => $user->tax_address,
                                    ])
                                    @if($user->tax_business_registration)
                                        <div class="row mb-4">
                                            <div class="col-md-3">
                                                <label class="form-label p-0 m-0 fs-6" style="line-height:37px;">@lang('form.tax_business_registration.label')</label>
                                            </div>
                                            <div class="col-md-9">
                                                <div id="tax_business_registration_name" style="line-height:34px;height:34px;">{{ explode('/', $user->tax_business_registration)[2] }}</div>
                                                <input class="form-control" value="" name="tax_business_registration" type="file" accept="." style="position:absolute;top:0;opacity:0;" onchange="changeTBReg(this)">
                                            </div>
                                        </div>
                                    @else
                                        @include('partials.form.input', [
                                            'field' => 'tax_business_registration',
                                            'value' => '',
                                            'type' => 'file',
                                        ])
                                    @endif
                                </div>
                            </div>
                            <div class="text-right my_infoo">
                                <div>
                                    <button type="button" data-toggle="modal" data-target="#goodbye_user">탈퇴</button>
                                    <ol>
                                        <li>
                                            @if($drop)
                                                {{ $drop->created_at }} (요청)
                                            @endif
                                        </li>
                                        <li>
                                            @if($drop)
                                                @if($drop->status == 1)
                                                    {{ $drop->admin_reason }}
                                                @endif
                                            @endif
                                        </li>
                                    </ol>
                                </div>

                                <button class="btn btn-cancel" type="button" onclick="history.back()">@lang('button.cancel')</button>
                                <button class="btn btn-primary" type="button" onclick="save()">@lang('button.save')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@if(session('session-success'))
<div id="welcome_popup">
	<button id="wpc"><img src="/assets/images/store/popupx_img.png"></button>
	<div>
		<img src="/assets/images/store/popup_img.png">
		<h1>
			회원 인증이 완료되었습니다.
		</h1>
	</div>
</div>

@endif


<!-- modal -->
<div class="modal fade goodbye_user" id="goodbye_user" tabindex="-1" role="dialog" aria-labelledby="basicModal"
aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
	<div class="modal-content">
		<div class="modal-body">
            <h5>정말로 회원탈퇴 요청 하시겠습니까?</h5>
            <p>탈퇴 요청 시 진행 중인 서비스 체크 후 탈퇴 처리 진행되기 때문에 수일 경과될 수 있습니다.</p>
			<select class="form-select goodbye_select" name="reason" onchange="" id="drop_reason">
                <option value="">사유선택</option>
                @foreach($options as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
			</select>

			{{-- <input class="form-control" value="" id="question" name="question" type="text" placeholder="사유를 입력하세요." accept="."> --}}

			<ul>
				<li><button  type="button" data-dismiss="modal">취소</button></li>
				<li><button type="button" data-dismiss="modal" onclick="goodbye_user()">탈퇴</button></li>
			</ul>


		</div>
	</div>
</div>
</div> <!-- modal -->

<button type="button" id="already" data-toggle="modal" data-target="#goodbye_user1" style="display:none">이미탈퇴</button>
<!-- modal -->
<div class="modal fade goodbye_user" id="goodbye_user1" tabindex="-1" role="dialog" aria-labelledby="basicModal"
aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
	<div class="modal-content">
		<div class="modal-body">
            <h5>이미 탈퇴요청 접수되었습니다.</h5>
            <p>탈퇴 요청 시 진행 중인 서비스 체크 후 탈퇴 처리 진행되기 때문에 수일 경과될 수 있습니다.</p>

			<ul>
				<li><button  type="button"  data-dismiss="modal">확인</button></li>
			</ul>


		</div>
	</div>
</div>
</div> <!-- modal -->

@endsection

@push('scripts')
<script>
    // $(document).ready(function() {
    //     $('.goodbye_select').change(function() {
    //         var result = $('.goodbye_select option:selected').val();
    //         if (result == '2') {
    //             $('#question').show();
    //         } else {
    //             $('#question').hide();
    //         }
    //     });
    // });

    var tab = "{{ $tab }}";

    $("#baseNavDemoTab").click(function() {
        tab = 1;
    });

    $("#baseNavHtmlTab").click(function() {
        tab = 2;
    });

	var WP = document.getElementById('welcome_popup');

    $('#wpc').click(function(){
		WP.style.display = 'none';
	});

    var WP = document.getElementById('welcome_popup');

    setTimeout(function() {
        $(WP).addClass('right_time');
    }, 0);

    setTimeout(function() {
        $(WP).addClass('none_time');
    }, 8000);

    function save() {
        let request = new FormData($('#user-info-form')[0]);
        request.set('tab', tab);

        $.ajax({
            url: '/my/store',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                if(response.code == 200) {
                    toastr.success(response.message);
                    location.href = "{{ route('client.my') }}"+'?tab='+tab;
                }
                else {
                    toastr.error('입력을 학인하세요.');
                }
            }
        });
    }

    function changeBReg(obj) {
		$("#business_registration_name").html(obj.files[0].name);
    }

    function changeTBReg(obj) {
		$("#tax_business_registration_name").html(obj.files[0].name);
    }

    function goodbye_user(){
        let request = new FormData();
        request.set('reason', $('#drop_reason').val());

        if(!$('#drop_reason').val()){
            alert('사유를 선택하세요.');
            return;
        }

        $.ajax({
            url: '/my/goodbye',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: ( response ) => {
                console.log(response);
                if(response.code == 200){
                    alert('요청되었습니다.');
                    location.reload();
                }else{
                    $('#already').click();
                }
            }
        })
    }
</script>
@endpush
