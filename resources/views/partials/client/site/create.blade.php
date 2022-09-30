<div class="modal fade" id="{{ request()->input('id', 'site-create-popup') }}" role="dialog" tabindex="-1" aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" id="register-1">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    <h4 class="modal-title mt-0">@lang('messages.site.createpopup.title')</h4>
                    @lang('messages.site.createpopup.description')
                </span>
            </div>
            <div class="modal-body">
                <form action="javascript:createSiteSend()" id="site-create-form">
                    @include('partials.form.input', [
                        'field' => 'site.name',
                        'required' => true
                    ])
                    @include('partials.form.input', [
                        'field' => 'site.url',
                        'id' => 'site-url',
                        'required' => true,
                    ])
                    @include('partials.form.select', [
                        'field' => 'site.type',
                        'default' => true,
                        'options' => [
                            ['value' => 0, 'text' => __('form.site.type.option.shop')],
                            ['value' => 1, 'text' => __('form.site.type.option.homepage')],
                            ['value' => 2, 'text' => __('form.site.type.option.news')],
                            ['value' => 3, 'text' => __('form.site.type.option.etc')],
                        ],
                        'on' => ['change' => "$('#hostname').toggleClass('d-none', $(this).val() != 0)"],
                        'required' => true,
                    ])
                    @include('partials.form.select', [
                        'id' => 'hostname',
                        'default' => true,
                        'field' => 'site.hostname',
                        'options' => [
                            ['value' => 0, 'text' => __('form.site.hostname.option.etc')],
                            ['value' => 1, 'text' => __('form.site.hostname.option.cafe24')],
                            ['value' => 2, 'text' => __('form.site.hostname.option.make')],
                            ['value' => 3, 'text' => __('form.site.hostname.option.godo')],
                        ],
                        'required' => true,
                    ])
                    <div class="foot_btn">
                        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal"><!-- @lang(request()->has('first') ? 'button.later_later' : 'button.cancel') -->
						@lang('client.client_app_btn_no')
						</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light"><!-- @lang('button.create.0') -->
						@lang('client.client_app_btn')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
	$('.foot_btn .btn-secondary').click(function(){
		$('.modal').removeClass('show');
		$('.modal-backdrop').removeClass('show');
	})
</script>
