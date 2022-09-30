@extends('layouts.cms')

@section('content')

<div class="board_table_info mt-0">
	<form id="form">
		<input type="hidden" name="active" />
		<input type="hidden" name="site_card" />
    <div>
    	<div class="my-3"><label class="form-label fs-6">어휘설정</label> <span class="ml-2 small">어휘분류에 대하여 항목을 설정 할 수 있습니다.</span></div>
		<table class="settion_site_cms">
			<tbody>
				<tr class="tabletr">
					<th style="width: 20%;">어휘분류</th>
					<td class="text-left">
						<select id="option_id" name="option_id">
							<option value="1">심사거절</option>
							<option value="2">고객사문의</option>
							<option value="3">제휴사문의</option>
                            <option value="4">탈퇴사유</option>
                            <option value="5">FAQ</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>항목설정 <br />(,(콤마)로 구분)</th>
					<td>
						<textarea name="content" class="form-control">{{ $q_options[0] ? $q_options[0]->content : '' }}</textarea>
					</td>
				</tr>
			</tbody>
		</table>
    </div>

    <div>
    	<div class="my-3"><label class="form-label fs-6">기업정보</label></div>
		<table>
			<tbody>
				<tr>
					<th style="width: 20%;">회사명</th>
	                <td style="width: 30%;"><input type="text" name="company_name" value="{{ $site_info->company_name }}" /></td>
					<th style="width: 20%;">사업자등록번호</th>
	                <td style="width: 30%;"><input type="text" name="buss_num" value="{{ $site_info->buss_num }}" /></td>
				</tr>
				<tr>
					<th>주소</th>
	                <td colspan="2"><input type="text" name="address" value="{{ $site_info->address }}" /></td>
				</tr>
				<tr>
					<th>대표자명</th>
	                <td><input type="text" name="officer_name" value="{{ $site_info->officer_name }}" /></td>
					<th>개인정보 관리 책임자</th>
	                <td><input type="text" name="personal_manager" value="{{ $site_info->personal_manager }}" /></td>
				</tr>
				<tr>
					<th>연락처</th>
	                <td><input type="text" name="phone" value="{{ $site_info->phone }}" /></td>
					<th>FAX</th>
	                <td><input type="text" name="fax" value="{{ $site_info->fax }}" /></td>
				</tr>
				<tr>
					<th>이메일</th>
	                <td><input type="text" name="email" value="{{ $site_info->email }}" /></td>
					<th>세금계산서 이메일</th>
	                <td><input type="text" name="tax_mail" value="{{ $site_info->tax_mail }}" /></td>
				</tr>
			</tbody>
		</table>
    </div>
	</form>

    <div>
    	<div class="my-3"><label class="form-label fs-6">계좌정보</label> <button type="button" class="set1 ml-3" onclick="addPayment()">추가</button></div>
		<table>
			<thead>
				<tr>
					<th>주계좌</th>
					<th>은행명</th>
					<th>계좌번호</th>
					<th>예금주</th>
					<th>삭제</th>
				</tr>
			</thead>
			<tbody class="payment_table">
				@foreach($site_card as $key => $item)
				<tr>
	                <td>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" id="active{{ $key }}" name="active" type="radio" value="{{ $item->id }}" {{ $item->active ? 'checked' : '' }} />
                            <label class="form-check-label" for="active{{ $key }}"></label>
                        </div>
	                </td>
	                <td>{{ config('app.banks')[$item->bank] }}</td>
	                <td>{{ $item->account }}</td>
	                <td>{{ $item->owner }}</td>
	                <td><button type="button" class="set2 del_payment has" data-idx="{{ $key }}" data-id="{{ $item->id }}">삭제</button></td>
				</tr>
				@endforeach
			</tbody>
		</table>
    </div>

    <div class="mt-4">
        <div class="col-12 row p-0 m-0" >
            <div class="col-12 text-right">
				@if ($write_permission)
                	<button type="button" onclick="update()" class="btn btn-secondary waves-effect waves-light">저장</button>
				@else
					<button type="button" onclick="javascript:alert('관리자에게 권한 요청해 주세요.')" class="btn btn-secondary waves-effect waves-light">저장</button>
				@endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">

    $(document).ready(function () {
    	$('#option_id').change(function() {
    		let type = $(this).val();
    		let option_val = @json($q_options);

    		$('table textarea').val(option_val[type - 1] ? option_val[type - 1].content : '');
    	});
    	//
    	$(document).on('click', '.del_payment', function() {
    		if($(this).hasClass('has')) {	// delete
    			if(!confirm('정말로 삭제하시겠습니까?')) {
    				return;
    			}
    			let data = {
    				type: 'delete', id: $(this).data('id')
    			}
		        $.ajax({
		            url: '/setting/site_update',
		            method: 'post',
		            data,
		            success: (response) => {
		                if(response.code == 200){
		                    location.reload();
		                }
		            },
		            error: (e) => {
		                console.log(e.responseJSON)
		            }
		        });
    		}
    		else {		// new added
	    		let idx = $(this).data('idx');
	    		$(this).parent().parent().remove();
    		}
    	});
    });

    function update() {

    	let site_card = [];
    	$('select[name="bank"]').each(function() {
    		let idx = $(this).data('idx');
	    	let data = {
	    		bank: $(this).val(),
	    		account: $(`input.account[data-idx="${idx}"]`).val(),
	    		owner: $(`input.owner[data-idx="${idx}"]`).val(),
	    	};
	    	site_card.push(data);
    	});
    	$('input[name="site_card"]').val(JSON.stringify(site_card));
    	//
    	let active = $('input[name="active"]:checked').val();
    	$('input[name="active"]').val(active);

        let request = new FormData($('#form')[0]);
        let data  = {};
        let valid = [];
        request.forEach((value, key) => {
        	(data[key] = value);
        	if(!value) {
        		valid.push(true);
        	}
        });
        // if(valid.length) {
        // 	return alert('필수항목들을 모두 입력해주세요.');
        // }

        $.ajax({
            url: '/setting/site_update',
            method: 'post',
            data,
            success: (response) => {
                if(response.code == 200){
                    // toastr.success('저장되었습니다.');
                    location.reload();
                }
            },
            error: (e) => {
                console.log(e.responseJSON)
            }
        });
    }

    function addPayment() {

    	let idx = $('.payment_table').children().length;
     	$('.payment_table').append(renderPayment(idx));
    }

    function renderPayment(idx) {

		let html = `<tr class="tabletr">
		                <td>
	                        <div class="form-check form-check-inline">
	                            <input class="form-check-input" id="active${idx}" name="active" type="radio" value="" data-idx="${idx}" />
	                            <label class="form-check-label" for="active${idx}"></label>
	                        </div>
		                </td>
		                <td>
		                	<select name="bank" data-idx="${idx}">
		                		@foreach(config('app.banks') as $key => $item)
									<option value="{{$key}}">{{$item}}</option>
								@endforeach
							</select>
		                </td>
		                <td><input type="text" class="account" value="" data-idx="${idx}" /></td>
		                <td><input type="text" class="owner" value="" data-idx="${idx}" /></td>
		                <td><button type="button" class="set2 del_payment" data-idx="${idx}">삭제</button></td>
					</tr>`;

		return html;
	}
</script>
@endpush
