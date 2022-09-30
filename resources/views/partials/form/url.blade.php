<?php
    $field_text = $field_text ?? $field;
    $type = $type ?? 'text';
    $labelText = Lang::has('form.'.$field_text.'.label') ? __('form.'.$field_text.'.label') : '';
    $readonly = ($readonly ?? false) ? 'readonly' : '';
    $placeholderText = __('form.'.$field_text.'.placeholder');
    $validateMessage = $validate_message ?? '';
    $required = ($required ?? false) ? 'required' : '';
    $value = $value ?? '';
    $mb = $mb ?? 4;
    $layout = $layout ?? 0;
    $https = $value == '' || str_starts_with($value, 'https://');
    $value = preg_replace('/http[s]{0,1}:\/\//', '', $value);
    $id = $id ?? '';
?>

@switch ($layout)
    @case (0)
        <?php
            $labelCol = $labelCol ?? 3;
            $selectCol = $selectCol ?? 2;
            $inputCol = 12 - $labelCol - $selectCol;
        ?>
        <div class="row mb-{{ $mb }}" id="{{ $id }}">
            <div class="col-{{ $labelCol }}">
                <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="@name($field)">{{ $labelText }}</label>
            </div>
            <div class="col-{{ $selectCol }}">
                <mwc-select label="{{ $labelText }}" name="@name($field)">
                    <mwc-list-item value="https://">https://</mwc-list-item>
                    <mwc-list-item value="http://">http://</mwc-list-item>
                </mwc-select>
            </div>
            <div class="col-{{ $inputCol }}">
                <input class="form-control" {{ $readonly }} value="{{ $value }}" id="@name($field)" name="@name($field)" type="{{ $type }}" placeholder="{{ $placeholderText }}">
            </div>
        </div>
        @break
@endswitch

{{-- <div class="form-group mb-3 {{ [
        '',
        'row',
        'row'
    ][$layout ?? 0] }} url-input" {!! isset($id) ? 'id="'.$id.'"' : '' !!}>
    <label for="@name($field)" class="{{ [
            '',
            'col-md-2 col-form-label',
            'col-md-3 col-form-label'
        ][$layout ?? 0] }}">
        @if(isset($required) && $required)
            *
        @endif
        @lang('form.'.($field_text ?? $field).'.label')
    </label>
    <div class="input-group p-0 {{ [
            '',
            'col-md-10',
            'col-md-9'
        ][$layout ?? 0] }}">
        <div class="input-group-prepend">
            <select class="selectpicker" {{ $http ?? true ? '' : 'disabled' }} data-style="btn-outline-dark" data-type="protocol">
                <option value="https://">https://</option>
                <option value="http://" {{ ($http ?? true) && preg_match('/^http\:/', $value ?? '') ? 'selected' : '' }}>http://</option>
            </select>
        </div>
        <input
         name="@name($field)"
         id="@name($field)"
         class="form-control"
         type="text"
         value="{{ preg_replace('/http[s]{0,1}:\/\//', '', ($value ?? '')) }}"
         placeholder="@lang('form.'.($field_text ?? $field).'.placeholder')">
    </div>
</div>

@if (request()->ajax())
    <script type="text/javascript">
        $('.selectpicker').selectpicker();
    </script>
@endif
--}}
