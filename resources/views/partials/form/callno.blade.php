<?php
    $field_text = $field_text ?? $field;
    $type = $type ?? 'text';
    $labelText = __('form.'.$field_text.'.label');
    $mb = $mb ?? 4;
    $readonly = ($readonly ?? false) ? 'readonly' : '';
    $layout = $layout ?? 0;
    $value = $value ?? '';
    $value = explode('-', $value ?: '--');
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
        <div class="col-md-{{ $labelCol }}">
            <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="@name($field)">{{ $labelText }}</label>
        </div>
        <div class="col-md-{{ $inputCol }}">
            <div class="input-group">
                <input class="form-control text-center" {{ $readonly }} value="{{ $value[0] }}" name="@name($field)" type="text">
                <span class="input-group-text" style="" id="basic-addon1">-</span>
                <input class="form-control text-center" {{ $readonly }} value="{{ $value[1] }}" name="@name($field)" type="text">
                <span class="input-group-text" style="" id="basic-addon1">-</span>
                <input class="form-control text-center" {{ $readonly }} value="{{ $value[2] }}" name="@name($field)" type="text">
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
