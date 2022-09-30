<?php
    $field_text = $field_text ?? $field;
    $type = $type ?? 'text';
    $labelText = Lang::has('form.'.$field_text.'.label') ? __('form.'.$field_text.'.label') : '';
    $readonly = ($readonly ?? false) ? 'disabled' : '';
    $validateMessage = $validate_message ?? '';
    $mb = $mb ?? 4;
    $required = ($required ?? false) ? 'required' : '';
    $value = $value ?? null;
    $layout = $layout ?? 0;
    $default = $default ?? false;
    $options = $options ?? [];
    $display = $display ?? true;
    $on['change'] = $on['change'] ?? '';
    $width = $width ?? 'auto';
    $id = $id ?? '';
?>



@switch ($layout)
    @case (0)
        <?php
            $labelCol = $labelCol ?? 3;
            $descriptionCol = $descriptionCol ?? 0;
            $inputCol = 12 - $labelCol - $descriptionCol;
        ?>
        <div class="row mb-{{ $mb }} {{ $display ? '' : 'd-none' }}" id="{{ $id }}">
            <div class="col-md-{{ $labelCol }}">
                <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="@name($field)">{{ $labelText }}</label>
            </div>
            <div class="col-md-{{ $inputCol }}">
                <select class="form-select" name="@name($field)" onchange="{!! $on['change'] !!}" id="@name($field)" >
                    @if ($default)
                        <option value="">@lang('messages.selected')</option>
                    @endif
                    @foreach ($options as $option)
                        <option onclick="{{ $option['on']['click'] ?? '' }}" value="{{ $option['value'] }}" {{ $value == $option['value'] ? 'selected' : '' }}>{{ $option['text'] }}</option>
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
        @break
    @case (2)
        <mwc-select style="width:{{ $width }}px;" label="{{ $labelText }}" onchange="{{ $on['change'] }}" name="@name($field)" >
            @if ($default)
                <mwc-list-item></mwc-list-item>
            @endif
            @foreach ($options as $option)
                <mwc-list-item  onclick="{{ $option['on']['click'] ?? '' }}" value="{{ $option['value'] }}" {{ $value === $option['value'] ? 'selected' : '' }} >{{ $option['text'] }}</mwc-list-item>
            @endforeach
        </mwc-select>

        @break
    @case (3)
        <select class="form-select" name="@name($field)" {{ $readonly }} onchange="{!! $on['change'] !!}" id="@name($field)" >
            @if ($default)
                <option value="">@lang('messages.selected')</option>
            @endif
            @foreach ($options as $option)
                <option onclick="{{ $option['on']['click'] ?? '' }}" value="{{ $option['value'] }}" {{ $value === $option['value'] ? 'selected' : '' }}>{{ $option['text'] }}</option>
            @endforeach
        </select>
@endswitch
