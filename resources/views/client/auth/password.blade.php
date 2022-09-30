@extends('layouts.client')

@section('content')
<div class="my-5 pt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <form action="javascript:void(0)" id="agent-auth-form">
                    @include('partials.form.title', [
                        'title' => 'messages.password.mypage'
                    ])
                    @include('partials.form.input', [
                        'field' => 'account',
                        'value' => Auth::user()->account,
                        'readonly' => true
                    ])
                    @include('partials.form.input', [
                        'field' => 'password',
                        'type' => 'password'
                    ])
                    <div class="form-group text-center mb-3">
                        <button id="done-btn" class="btn btn-primary btn-lg width-lg btn-rounded" type="submit">@lang('button.auth')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#agent-auth-form').submit((event) => {
        $.ajax({
            url: `//${domain.client}/password/check`,
            method: 'POST',
            data: {password: $(event.target).find('[name=password]').val()},
            success: (response) => {
                if(response.code == 200){                    
                    toastr.keepMessage('success', response.message);
                    location.reload();
                }
                else
                    showErr(response.error);
            }
        })
    });
</script>
@endpush
