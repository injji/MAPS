<?php
    $value = $value ?? '';
    $value = explode('@', $value ?: '@');
    $field_text = $field_text ?? $field;
    $labelText = __('form.'.$field_text.'.label');
    $mb = $mb ?? 4;
    $readonly = ($readonly ?? false) ? 'readonly' : '';
    $disabled = ($readonly ?? false) ? 'disabled' : '';
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
                $inputCol = 12 - $labelCol;
            }
    ?>
        <div class="row mb-{{ $mb }}">
            <div class="col-md-{{ $labelCol }}">
                <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="@name($field)">{{ $labelText }}</label>
            </div>
            <div class="col-md-{{ $inputCol }} position-relative">
                <div class="input-group">
                    <input class="form-control" {{ $readonly }} value="{{ $value[0] }}" id="@name($field)-adress" name="@name($field)" type="text">
                    <span class="input-group-text" style="{{ $readonly ? 'border-color: rgba(0, 0, 0, 0.06);' : '' }}" id="basic-addon1">@</span>
                    <input class="form-control" {{ $value[1] || $readonly ? 'readonly' : '' }} value="{{ $value[1] }}" id="@name($field)-domain" name="@name($field)" type="text">
                    @include('partials.form.select', [
                        'width' => 150,
                        'layout' => 3,
                        'readonly' => $disabled ?? false,
                        'value' => $value[1],
                        'on' => ['change' => "$(this).prev().val($(this).val()).attr('readonly', $(this).val() ? true : null);"],
                        'options' => collect(['', 'naver.com', 'hanmail.net', 'gmail.com', 'nate.com', 'yahoo.com'])->map(function($item, $key) {
                            return [
                                'value' => $item,
                                'text' => $item ? $item : __('form.email.default'),
                            ];
                        }),
                    ])
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
