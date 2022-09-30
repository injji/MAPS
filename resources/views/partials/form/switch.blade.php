<?php
    $field_text = $field_text ?? $field;
    $type = $type ?? 'text';
    $labelText = __('form.'.$field_text.'.label');
    $disabled = ($readonly ?? false) ? 'disabled' : '';
    $checked = $checked ?? false;
    $on['change'] = $on['change'] ?? '';
    $mb = $mb ?? 4;
    $layout = $layout ?? 0;
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
        <div class="row mb-{{ $mb }}">
            <div class="col-md-{{ $labelCol }} col-4">
                <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="@name($field)">{{ $labelText }}</label>
            </div>
            <div class="col-md-{{ $inputCol }} col-8" style="line-height:37px;">
                <div class="form-check form-switch form-switch-md">
                    <input class="form-check-input" onchange="{{ $on['change'] }}" id="@name($field)-switch" {{ $disabled }} type="checkbox" {{ $checked ? 'checked' : '' }} name="@name($field)">
                    <label class="form-check-label" for="@name($field)-switch"></label>
                </div>
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
