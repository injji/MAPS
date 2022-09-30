<div class="form-group site-select">
    <div class="input-group">
        <div class="input-group">
            <label class="col-md-12 col-form-label text-center">@lang('client.client_sel_site')</label>
            <button id="site-select" class="btn btn-outline-dark dropdown-toggle waves-effect waves-light col-8 offset-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ Auth::user()->current_site->name ?? __('messages.site.empty') }} <i class="mdi mdi-chevron-down"></i>
            </button>
            <ul class="dropdown-menu" style="width:66,66666%;margin:0 16.6666%">
                @if (Auth::user()->site->count() > 0)
                    @foreach (Auth::user()->site as $site)
                        @if (Auth::user()->current_site == $site)
                            <li><a href="javascript:void(0)" class="dropdown-item">{{ $site->name }}</a></li>
                        @else
                            <li><a href="javascript:changeSite(@json($site->id))" class="dropdown-item">{{ $site->name }}</a></li>
                        @endif
                    @endforeach
                @else
                    <li><a href="javascript:void(0)" class="dropdown-item">@lang('messages.site.empty')</a></li>
                @endif
                <li class="dropdown-divider"></li>
                <li><a href="javascript:createSite()" class="dropdown-item">@lang('button.create.0')</a></li>
            </ul>
        </div>
    </div>
</div>
