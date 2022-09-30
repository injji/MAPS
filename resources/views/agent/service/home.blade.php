@extends('layouts.agent')

@section('content')

<div class="row">    
    @if(count($services) == 0)
    <div id="no_service">
        <div class="no_service">
        <div>
        <h2>@lang('messages.noservice')</h2>
        <p>@lang('messages.goservice')</p>
        <button type="button" onclick="addService()">@lang('button.service_upload')</button>
        </div>
        </div>
    </div>
    @endif
    @foreach ($services as $service)
        <div class="col-lg-2 col-md-3 col-6">
            <a href="{{ route('agent.service_edit', $service->getKey()) }}" class="text-decoration-none">
                <div class="card">
                    <div class="card-header">
                        <img
                            src="{{ $service->icon ? Storage::url($service->icon) : asset('images/xbox.png') }}" alt="image" class="img-fluid rounded" width="2000">
                    </div>
                    <div class="card-body text-dark">
                        <p class="mb-0">{{ $service->name ?? '' }}</p>

                        <div class="dapro">
                        <p class="mb-0">{{ ($service->created_at ?? false) ? $service->created_at->format('Y-m-d') : '-' }}</p>
                        <p class="mb-0 process_txt" >{{ $service->process_text ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
    function addService(){
        location.href = "{{ route('agent.service_append') }}";
    }

    var tmp = document.querySelectorAll('.process_txt');
    
    for (var i = 0;i < tmp.length;i++){        
        if (tmp[i].innerHTML == "등록대기") {
            tmp[i].classList.add('yellow');
        } else if (tmp[i].innerHTML == "판매중") {
            tmp[i].classList.add('green');
        } else if (tmp[i].innerHTML == "심사중") {
            tmp[i].classList.add('purple');
        } else if (tmp[i].innerHTML == "심사거절") {
            tmp[i].classList.add('red');
        } else {
            tmp[i].classList.add('black');
        }
    }
</script>
@endpush