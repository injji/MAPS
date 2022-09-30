<?php
    $field_text = $field_text ?? $field;
    $type = $type ?? 'text';
    $labelText = __('form.'.$field_text.'.label');
    $required = ($required ?? false) ? 'required' : '';
    $value = $value ?? asset('images/xbox.png');
    $layout = $layout ?? 0;
    $mb = $mb ?? 4;
    $descriptionRight = $descriptionRight ?? true;
    $accept = '.'.join(', .', explode(' ', $extensions ?? ''));
    $validate = $validate ?? [];
    $descriptionRight = $descriptionRight ?? true;
?>

@switch ($layout)
    @case (0)
        <?php
            $labelCol = $labelCol ?? 3;
            $inputCol = 12 - $labelCol;
            if ($inputCol < 1) {
                $inputCol += 12;
            }
        ?>
        <div class="row mb-{{ $mb }}">
            <div class="col-md-{{ $labelCol }}">
                <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="@name($field)">{{ $labelText }}</label>
            </div>
            <div class="col-md-{{ $inputCol }}">
                <img src="{{ $value }}" data-xbox="{{ asset('images/xbox.png') }}" width="150" onclick="$(this).next().click()">
                <input type="file" name="@name($field)" class="d-none" accept="{{ $accept }}" onchange='thumbnailImg(this, $(this).prev(), @json($validate))'>
                <label class="p-0 col-form-label {{ $descriptionRight ? 'text-right float-right ' : '' }}small">
                    {!! nl2br(__(('form.'.($field_text).'.description'))) !!}
                </label>
            </div>
        </div>
        @break
@endswitch
