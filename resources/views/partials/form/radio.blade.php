<?php
    $field_text = $field_text ?? $field;
    $type = $type ?? 'text';
    $labelText = __('form.'.$field_text.'.label');
    $placeholderText = __('form.'.$field_text.'.placeholder');
    $readonly = ($readonly ?? false) ? 'readonly' : '';
    $textarea = $textarea ?? false;
    $validateMessage = $validate_message ?? '';
    $required = ($required ?? false) ? 'required' : '';
    $mb = $mb ?? 4;
    $value = $value ?? '';
    $layout = $layout ?? 0;
    $options = $options ?? [];
    $on['change'] = $on['change'] ?? '';
    $display = $display ?? true;
    $id = $id ?? $field.'-wrapper';
?>

@switch ($layout)
    @case (0)
        <?php
            $labelCol = $labelCol ?? 3;
            $descriptionCol = $descriptionCol ?? 0;
            $inputCol = 12 - $labelCol - $descriptionCol;
            if ($inputCol < 1) {
                $inputCol = 12 - $labelCol;
            }
    ?>
        <div class="row mb-{{ $mb }} {{ $display ? '' : 'd-none' }}" id="{{ $id }}">
            <div class="col-md-{{ $labelCol }}">
                <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="@name($field)">{{ $labelText }}</label>
            </div>
            <div class="col-md-{{ $inputCol }}">
                @foreach ($options as $option)
                    <div class="form-check form-check-inline">
                        <input
                         class="form-check-input"
                         onchange="{{ $on['change'] }}"
                         id="@name($field)-{{ $option['value'] }}"
                         type="radio"
                         value="{{ $option['value'] }}"
                         {{ $value == $option['value'] ? 'checked' : '' }}
                         name="@name($field)">
                        <label
                         class="form-check-label"
                         for="@name($field)-{{ $option['value'] }}">
                            {{ $option['text'] }}
                        </label>
                    </div>
                @endforeach
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
@endswitch
