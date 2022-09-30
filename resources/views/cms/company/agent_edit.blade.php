@extends('layouts.cms')

@section('content')

<form id="user-info-form">
	<div class="board_table_info mt-0">
		<div class="fs-5 fw-bold">
			<span>제휴사정보</span>
		</div>

		<div>
			<div class="my-3"><label class="form-label fs-6">설정관리</label></div>
			<table>
				<tbody>
				<tr>
					<th style="width:20%; height:60px">결제수동등록</th>
					<td  style="text-align: left">
						<input type="checkbox" id="self_payment" name="self_payment" {{ ($user->self_payment == 1) ? 'checked' : '' }} value="{{ ($user->self_payment) }}" />
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<div>
			<div class="my-3"><label class="form-label fs-6">회원정보</label></div>
			<table>
				<tbody>
					<tr>
						<th style="height: 60px">아이디</th>
						<td colspan="3" class="text-left">{{ $user->account }}</td>
					</tr>
					<tr>
						<th style="width: 20%;">비밀번호</th>
						<td style="width: 30%;">
							<input type="password" name="password" placeholder="******" />
						</td>
						<th style="width: 20%;">비밀번호 확인</th>
						<td style="width: 30%;">
							<input type="password" name="c_password" placeholder="******" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div>
			<div class="my-3"><label class="form-label fs-6">회사정보</label></div>
			<table>
				<tbody>
					<tr>
						<th style="width: 20%;">회사명</th>
						<td style="width: 30%;">
							<input type="text" name="company_name" value="{{ $user->company_name }}" />
						</td>
						<th style="width: 20%;">사업자등록번호</th>
						<td style="width: 30%;">
							<input type="text" name="business_no" value="{{ $user->business_no }}" />
						</td>
					</tr>
					<tr>
						<th>주소</th>
						<td colspan="3">
							<input type="text" name="address" value="{{ $user->address }}" />
						</td>
					</tr>
					<tr>
						<th>대표자명</th>
						<td>
							<input type="text" name="director_name" value="{{ $user->director_name }}" />
						</td>
						<th>통신판매신고번호</th>
						<td>
							<input type="text" name="order_report_number" value="{{ $user->order_report_number }}" />
						</td>
					</tr>
					<tr>
						<th>대표자 연락처</th>
						<td>
							<input type="text" name="director_phone" value="{{ $user->director_phone }}" />
						</td>
						<th>홈페이지</th>
						<td>
							<input type="text" name="homepage_url" value="{{ $user->homepage_url }}" />
						</td>
					</tr>
					<tr>
						<th>대표자 이메일</th>
						<td>
							<input type="text" name="director_email" value="{{ $user->director_email }}" />
						</td>
						<th>사업자등록증</th>
						<td class="left">{{ $user->business_registration ? explode('/', $user->business_registration)[2] : '' }}</td>
					</tr>
					<tr>
						<th rowspan="2">계좌정보</th>
						<td rowspan="2">
							<div class="d-flex">
								<span class="label-text">예금주</span>
								<input type="text" name="account_holder" value="{{ $user->account_holder }}" />
								<span class="label-text">은행</span>
								<select name="bank_name" style="width: 100%">
									<option value="">은행선택</option>
									@foreach($bank_list as $key => $item)
										<option value="{{ $item }}" {{ $user->bank_name == $item ? 'selected' : ''}}>{{ $item }}</option>
									@endforeach
								</select>
							</div>
							<div class="d-flex mt-2">
								<span class="label-text" style="width: 86px;padding-right: 0;">계좌번호</span>
								<input type="text" name="account_number" value="{{ $user->account_number }}" />
							</div>
						</td>
						<th>세금계산서 이메일</th>
						<td>
							<input type="text" name="tax_email" value="{{ $user->tax_email }}" />
						</td>
					</tr>
					<tr>
						<th>수수료율</th>
						<td>
							<div class="d-flex" style="align-items:center;">
								<input type="text" name="fees" value="{{ $user->fees }}" style="width:100px;" />
								<span class="label-text">%</span>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div>
			<div class="my-3"><label class="form-label fs-6">담당자 정보</label></div>
			<table>
				<tbody>
					<tr>
						<th style="width: 20%;">담당자명</th>
						<td style="width: 30%;">
							<input type="text" name="manager_name" value="{{ $user->manager_name }}" />
						</td>
						<th style="width: 20%;">이메일</th>
						<td style="width: 30%;">
							<input type="text" name="manager_email" value="{{ $user->manager_email }}" />
						</td>
					</tr>
					<tr>
						<th>전화번호</th>
						<td colspan="3">
							<input type="text" name="manager_phone" value="{{ $user->manager_phone }}" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div>
			<div class="my-3"><label class="form-label fs-6">서비스 정보</label></div>
			<table>
				<tbody>
					<tr>
						<th>서비스명</th>
						<th>대표설명</th>
						<th>심사완료일</th>
						<th>누적 이용수</th>
					</tr>
					@foreach($user->service as $key => $item)
						<tr>
							<td>{{ $item->name }}</td>
							<td>{{ $item->service_info }}</td>
							<td>{{ $item->complete_at }}</td>
							<td>{{ $item->service->count() }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>

		<div class="mt-4">
			<div class="col-12 row p-0 m-0" >
				<div class="col-12 text-right">
					<button type="button" onclick="javascript:history.go(-1)" class="btn btn-light waves-effect waves-light">취소</button>
					<button type="button" onclick="save()" class="btn btn-secondary waves-effect waves-light">저장</button>
				</div>
			</div>
		</div>
	</div>
</form>

@endsection

@push('scripts')
<script type="text/javascript">

    $('#self_payment').on('click', function(){
        console.log($(this).is(':checked'));
        if($(this).is(':checked')){
            $(this).val(1);
        }else{
            $(this).val(0);
        }
    })

	function save(){
        let request = new FormData($('#user-info-form')[0]);
		request.set('id', "{{ $user->id }}");

        $.ajax({
            url: '/user/info/store',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: (response) => {
                if(response.code == 200)
                    toastr.success(response.message);
                else
                    toastr.error('입력을 학인하세요.')
            }
        })
    }
</script>
@endpush

<style type="text/css">
	.board_table_info table td.left {text-align: left;}
</style>
