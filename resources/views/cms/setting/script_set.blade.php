@extends('layouts.cms')

@section('content')

<div class="board_table_ex">
    <table class="table_no8">
        <colgroup>
            <col width="56px" />
            <col />
            <col />
            <col/>
            <col />
            <col />
            <col/>
            <col width="105px" />
        </colgroup>
        <thead>
            <tr>
                <th>No</th>
                <th>고객사</th>
                <th>사이트</th>
                <th>솔루션</th>
                <th>아이디</th>
                <th>비밀번호</th>
                <th>요청일자</th>
                <th>관리</th>
            </tr>
        </thead>

        <tbody>
            @if($script)
                @foreach($script as $key => $item)
                    <?php
                        switch($item->hostname){
                            case 1: $hostname = '카페24'; break;
                            case 2: $hostname = '메이크샵'; break;
                            case 3: $hostname = '고도몰'; break;
                            default: $hostname = '독립몰'; break;
                        }
                    ?>
                    <tr>
                        <td>{{ $script->total() - ($script->currentPage() - 1) * $script->perPage() - $key }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->admin_url }}</td>
                        <td>{{ $hostname }}</td>
                        <td>{{ $item->account }}</td>
                        <td><button class="copy_btn" onclick="copyPW('{{ $item->password }}')">복사</button></td>
                        <td>{{ $item->created_at }}</td>
                        <td><button class="btn_status{{ ($item->flag == 0) ? 1 : 2 }}" {{ ($item->flag == 0) ? "data-toggle=modal data-target=#btn_status1" : "" }} data-id={{ $item->id }}>{{ ($item->flag == 0) ? "요청" : "완료" }}</button></td>

                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="list_btn_ex">
        {!! $script->appends(Request::except('page'))->render() !!}
    </div>

</div>


<!-- Modal 문의 하기 -->
<div class="modal fade contact_modal" id="btn_status1" tabindex="-1" role="dialog"
aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <h2>완료 처리하시겠습니까?</h2>
            <p>
                처리 시,<br> 회원정보에 입력된 메일, SMS를 통해 안내됩니다.
            </p>
            <div class="two_btn">
                <input type="hidden" id="item_id" value="">
                <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
                <button  data-toggle="modal" data-dismiss="modal" onclick="request_finish()">완료</button>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>

    $('.btn_status1').on('click', function(){
        let id = $(this).data('id');
        $('#item_id').val(id);
    })

    function request_finish(){
        let id = $('#item_id').val();
        let request = new FormData();
        request.set('id', id);

        $.ajax({
            url: '/setting/script_request',
            method: 'post',
            data: request,
            contentType: false,
            processData: false,
            success: ( response ) => {
                console.log(response);
                if(response.code == 200){
                    location.reload();
                }
            }
        })
    }

    function copyPW(pw){
        let $temp = $("<input>");
        $("body").append($temp);
        $temp.val(pw).select();
        document.execCommand("copy");
        $temp.remove();
        alert('복사되었습니다.');
    }
</script>
@endpush
