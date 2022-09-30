<?php
    $field_text = $field_text ?? $field;
    $type = $type ?? 'text';
    $labelText = __('form.'.$field_text.'.label');
    $mb = $mb ?? 4;
    $readonly = ($readonly ?? false) ? 'readonly' : '';
    $required = ($required ?? false) ? 'required' : '';
    $layout = $layout ?? 0;
?>

@switch ($layout)
    @case (0)
        <?php
            $labelCol = $labelCol ?? 3;
            $descriptionCol = $descriptionCol ?? 0;
            $inputCol = 12 - $labelCol - $descriptionCol;
            if ($inputCol < 1) {
                $inputCol += 12;
            }
    ?>
        <div class="row mb-{{ $mb }}">
            <div class="col-md-{{ $labelCol }}">
                <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="@name($field)">{{ $labelText }}</label>
            </div>
            <div class="col-md-{{ $inputCol }}">
                @foreach ($options as $option)
                    <mwc-formfield label="{{ $option['text'] }}">
                        <mwc-checkbox
                         onchange="{{ $option['on']['change'] ?? '' }}"
                         {{ ($option['checked'] ?? false) ? 'checked' : '' }}
                         id="@name($option['field'])-{{ $option['value'] }}"
                         name="@name($option['field'])"
                         value="{{ $option['value'] }}">
                        </mwc-radio>
                    </mwc-formfield>
                @endforeach
            </div>
            @if ($descriptionCol)
                <div class="col-md-{{ $descriptionCol == 12 ? ($descriptionCol - $labelCol).' offset-md-'.$labelCol : $descriptionCol }}">
                    <label class="p-0 col-form-label {{ $descriptionRight ? 'text-right float-right ' : '' }}small">
                        {!! nl2br(__(('form.'.($field_text).'.description'))) !!}
                    </label>
                </div>
            @endif
        </div>
        @break
@endswitch
