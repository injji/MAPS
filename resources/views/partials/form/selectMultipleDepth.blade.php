<?php
    $field_text = $field_text ?? $field;
    $type = $type ?? 'text';
    $labelText = Lang::has('form.'.$field_text.'.label2') ? __('form.'.$field_text.'.label2') : '';
    $readonly = ($readonly ?? false) ? 'readonly' : '';
    $validateMessage = $validate_message ?? '';
    $required = ($required ?? false) ? 'required' : '';
    $mb = $mb ?? 4;
    $value = $value ?? [null, null];
    $options = $options ?? [];
    if ($selected = collect($options)->where('value', $value[0])->first()) {
        $childs = $selected['child'] ?? [];
    } else {
        $childs = [];
    }
    $layout = $layout ?? 0;
    $default = $default ?? false;
    $display = $display ?? true;
    $on['change'] = $on['change'] ?? '';
    $id = $id ?? '';
?>

@switch ($layout)
    @case (0)
        <?php
            $labelCol = $labelCol ?? 3;
            $descriptionCol = $descriptionCol ?? 0;
            $inputCol = 12 - $labelCol - $descriptionCol;
            if ($inputCol < 1) {
                $inputCol = 12;
            }
        ?>
        <div class="row mb-{{ $mb }} {{ $display ? '' : 'd-none' }}" id="{{ $id }}">
            <div class="col-md-{{ $labelCol }}">
                <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="@name($field)">{{ $labelText }}</label>
            </div>
            <div class="col-md-{{ $inputCol / 2 }} mb-1 mb-md-0">
                <select class="form-select" depth="1" name="@name($field)[]" onchange="{!! $on['change'] !!}" id="@name($field)">
                    @if ($default)
                        <option value="">@lang('messages.selected')</option>
                    @endif
                    @foreach ($options as $option)
                        <option onclick="{{ $option['on']['click'] ?? '' }}" value="{{ $option['value'] }}" {{ $value[0] == $option['value'] ? 'selected' : '' }}>{{ $option['text'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-{{ $inputCol / 2 }} mb-1 mb-md-0">
                <select class="form-select"  depth="2" name="@name($field)[]" onchange="{!! $on['change'] !!}">
                    @if ($default)
                        <option value="">@lang('messages.selected')</option>
                    @endif
                    @foreach ($childs as $option)
                        <option onclick="{{ $option['on']['click'] ?? '' }}" value="{{ $option['value'] }}" {{ $value[1] == $option['value'] ? 'selected' : '' }}>{{ $option['text'] }}</option>
                    @endforeach
                </select>
            </div>
            @if ($descriptionCol)
                <div class="col-md-{{ $descriptionCol }}">
                    <label class="col-form-label text-right float-right small">
                        {!! nl2br(__(('form.'.($field_text).'.description'))) !!}
                    </label>
                </div>
            @endif
        </div>
        <script>
            (() => {
                let field = '@name($field)';
                let options = @json($options);
                let defaultOption = '@lang('messages.selected')';
                window.addEventListener('load', () => {
                    $(`[name="${field}[]"][depth=1]`).change((event) => {
                        console.log(event);
                        console.log(event.target.value);
                        childs = [];
                        options.some((item, i) => {
                            if (item.value == event.target.value) {
                                childs = item.child
                                return true
                            }
                        })

                        const oneTerm = event.target.parentNode.parentNode;
                        const oneTermDiv = oneTerm.childNodes[5];

                        if(`${field}` == 'service_term' && event.target.value == 2){
                            oneTermDiv.innerHTML = `<input type="text" class="one_term_input" name="${field}[]" placeholder="옵션명을 입력하세요">`
                        }else{
                            if(`${field}` == 'service_term'){
                                let oneHtml = `<select class="form-select"  depth="2" name="${field}[]" onchange="{!! $on['change'] !!}">`
                                oneTermDiv.innerHTML = oneHtml

                                optionHtml = `<option>${defaultOption}</option>`
                                childs.forEach((option, i) => {
                                    optionHtml += `<option value="${option.value}">${option.text}</option>`
                                })
                                $(`[name="${field}[]"][depth=2]`).html(optionHtml)
                            }else{
                                optionHtml = `<option>${defaultOption}</option>`
                                childs.forEach((option, i) => {
                                    optionHtml += `<option value="${option.value}">${option.text}</option>`
                                })
                                $(`[name="${field}[]"][depth=2]`).html(optionHtml)
                            }
                        }
                    })
                })
            })()
        </script>
        @break
    @case (2)
        <mwc-select label="{{ $labelText }}" onchange="{{ $on['change'] }}" name="@name($field)">
            @if ($default)
                <mwc-list-item></mwc-list-item>
            @endif
            @foreach ($options as $option)
                <mwc-list-item onclick="{{ $option['on']['click'] ?? '' }}" value="{{ $option['value'] }}" {{ $value === $option['value'] ? 'selected' : '' }}>{{ $option['text'] }}</mwc-list-item>
            @endforeach
        </mwc-select>
        @break
@endswitch
