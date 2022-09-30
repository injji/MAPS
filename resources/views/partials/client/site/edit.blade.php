<div class="modal fade" id="{{ request()->input('id', 'site-edit-popup') }}" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" id="register-1">
        <div class="modal-content">
            <div class="modal-header">
                <span>
                    <h4 class="modal-title mt-0">@lang('messages.site.editpopup.title')</h4>
                    @lang('messages.site.editpopup.description')
                </span>
            </div>
            <div class="modal-body">
                <form action="javascript:updateSiteSend()" id="site-edit-form">
                    @include('partials.form.input', [
                        'field' => 'site_name',
                        'required' => true
                    ])
                    @include('partials.form.input', [
                        'field' => 'site_url',
                        'id' => 'site-url',
                        'required' => true,
                    ])
                    @include('partials.form.select', [
                        'field' => 'site_type',
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
                        'field' => 'site_hostname',
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
						@lang('client.client_app_btn_no_update')
						</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light"><!-- @lang('button.create.0') -->
						@lang('client.client_app_btn_update')</button>
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

<style> 
    #site-edit-popup {
        border-radius: 10px;
    }

    #site-edit-popup .modal-header {
        text-align: center;
        display: block;
        padding: 80px 0 40px 0;
    }

    #site-edit-popup .modal-header h4 {
        margin-bottom: 8px;
        font-size: 24px;
        font-weight: bold;
    }

    #site-edit-popup .modal-header span {
        color: #666;
        font-size: 0.875rem;
    }

    #site-edit-popup .modal-body label {
        font-size: 14px;
        color: #888;
        line-height: 20px !important;
        margin-bottom: 8px !important;
    }
</style>