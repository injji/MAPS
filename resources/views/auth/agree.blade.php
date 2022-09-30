@extends('layouts.auth')

@section('title', __('page.agreement'))

@section('body')
<div class="login_wrap  join_wrap">

    <div class="login join">
        <progress value="1" max="2"></progress>
        <h1>
            <img src="{{ asset('images/logo_b.png') }}">
        </h1>
        <div class="agree">
            <label>
                <em>
                    <input type="checkbox" id="all_chk">
                    <span></span>
                    @lang('agreement.agree')
                </em>
            </label>
            <label>
                <em>
                    <input type="checkbox" id="chk1">
                    <span></span>
                    @lang('agreement.service_shot')
                </em>
                <button type="button" class="btn btn-primary" onclick="termModal(1)">@lang('button.view_full_text')</button>
            </label>
            <label>
                <em>
                    <input type="checkbox" id="chk2">
                    <span></span>
                    @lang('agreement.privacy_shot')
                </em>
                <button type="button" class="btn btn-primary" onclick="termModal(2)">@lang('button.view_full_text')</button>
            </label>
            <label>
                <em>
                    <input type="checkbox" id="chk3">
                    <span></span>
                    @lang('agreement.marketing_shot')
                </em>
                <button type="button" class="btn btn-primary" onclick="termModal(3)">@lang('button.view_full_text')</button>
            </label>            
        </div>

        <div class="next">
            <a href="{{ route('login') }}">@lang('button.login')</a>
            <a href="javascript:next({{ $type }})">@lang('button.next')</a>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade agree_content" id="term_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <input type="hidden" id="type" value="0">
        <div class="modal-content">
            <div class="modal-body">
                <h1></h1>
                <div id="agree_content" style="margin-top: 20px;font-size: 14px;"></div>
                <button onclick="closeModal()" type="button" class="btn btn-secondary"
                    data-dismiss="modal">@lang('button.agree')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">    
    $("#all_chk").change(function(){
        if(this.checked){
            $("#chk1").prop('checked', true);
            $("#chk2").prop('checked', true);
            $("#chk3").prop('checked', true);
        }
        else{
            $("#chk1").prop('checked', false);
            $("#chk2").prop('checked', false);
            $("#chk3").prop('checked', false);
        }
    });

    $("#chk1").change(function(){               
        if(this.checked && $("#chk2").prop('checked') && $("#chk3").prop('checked'))
            $("#all_chk").prop('checked', true);
        else
            $("#all_chk").prop('checked', false);
    });

    $("#chk2").change(function(){
        if(this.checked && $("#chk1").prop('checked') && $("#chk3").prop('checked'))
            $("#all_chk").prop('checked', true);
        else
            $("#all_chk").prop('checked', false);
    });

    $("#chk3").change(function(){
        if(this.checked && $("#chk1").prop('checked') && $("#chk2").prop('checked'))
            $("#all_chk").prop('checked', true);
        else
            $("#all_chk").prop('checked', false);
    });

    function termModal(type){
        $("#type").val(type);

        $.ajax({
            url : "{{ route('register.get.term') }}",
            type : 'post',
            data : {type: parseInt(type) - 1},
            success : (response) => {
                if(response.code == 200){
                    $("#agree_content").html(response.content.content);

                    if(type == 1)
                        $("#term_modal .modal-body h1").html("{{ __('page.agreement1') }}");
                    else if(type == 2)
                        $("#term_modal .modal-body h1").html("{{ __('page.agreement2') }}");
                    else if(type == 3)
                        $("#term_modal .modal-body h1").html("{{ __('page.agreement3') }}");

                    $("#term_modal").modal();
                }
            }
        });
    }

    function closeModal(){
        $("#term_modal").modal('hide');
        $("#chk"+$("#type").val()).prop('checked', true);
        if($("#chk1").prop('checked') && $("#chk2").prop('checked') && $("#chk3").prop('checked'))
            $("#all_chk").prop('checked', true);
        else
            $("#all_chk").prop('checked', false);
    }

    function next(type){
        var chk1 = $("#chk1").is(":checked");
        var chk2 = $("#chk2").is(":checked");

        if(!chk1 || !chk2){
            alert("{{ __('messages.required_agreement') }}");
            return;
        }
        
        if (type == 1)
            location.href = "{{ route('register.register1') }}";
        else
            location.href = "{{ route('register.register2') }}";
    }
</script>
@endpush
