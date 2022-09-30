<?php
    $field_text = $field_text ?? $field;
    $type = $type ?? 'text';
    $labelText = __('form.'.$field_text.'.label');
    $placeholderText = $placeholder_text ?? __('form.'.$field_text.'.placeholder');
    $readonly = ($readonly ?? false) ? 'readonly' : '';
    $textarea = $textarea ?? false;
    $validateMessage = $validate_message ?? '';
    $required = ($required ?? false) ? 'required' : '';
    $value = $value ?? '';
    $layout = $layout ?? 0;
    $helper = $helper ?? '';
    $append = $append ?? [];
    $select = $select ?? null;
    $mb = $mb ?? 4;
    $descriptionRight = $descriptionRight ?? true;
    $multiple = isset($multiple) && ($multiple) ? 'multiple' : '';
    $accept = '.'.join(', .', explode(' ', $extensions ?? ''));
?>

@switch ($layout)
    @case (0)
        <?php
            $labelCol = $labelCol ?? 3;
            $descriptionCol = $descriptionCol ?? 0;
            $selectCol = $select ? ($selectCol ?? 3) : 0;
            $inputCol = 12 - $labelCol - $descriptionCol - $selectCol;
            if ($inputCol < 1) {
                $inputCol += 12;
            }
        ?>
        <div class="row mb-{{ $mb }}">
            <div class="col-md-{{ $labelCol }}">
                <label class="form-label p-0 m-0 fs-6" style="line-height:37px;" for="@name($field)">{{ $labelText }}</label>
            </div>
            <div class="col-md-{{ $inputCol }}">
                <div class="{{ count($append) > 0 ? 'input-group' : '' }}">
                    @if ($textarea)
                        <textarea class="form-control" {{ $readonly }} id="@name($field)" name="@name($field)" type="{{ $type }}" placeholder="{{ $placeholderText }}">{!! $value !!}</textarea>
                    @else
                        <input class="form-control" {{ $readonly }} value="{{ $value }}" {{ $multiple }} id="@name($field)" name="@name($field)" type="{{ $type }}" placeholder="{{ $placeholderText }}" accept="{{ $accept }}">
                    @endif
                    <div class="ml-1">
                        <div class="input-group">
                            @foreach ($append as $row)
                                @switch ($row['type'])
                                    @case ('button')
                                        <button class="btn {{ $row['button-type'] ?? 'btn-outline-primary' }}" id="{{ $row['text'] ?? '' }}" type="button" onclick="{{ $row['on']['click'] ?? '' }}">{{ $row['text'] }}</button>
                                        @break
                                @endswitch
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @if ($select)
                <?php $select['layout'] = 3;?>
                <div class="col-md-{{ $selectCol }}">
                    @include('partials.form.select', $select)
                </div>
            @endif
            @if ($descriptionCol)
                <div class="col-md-{{ $descriptionCol == 12 ? ($descriptionCol - $labelCol).' offset-md-'.$labelCol : $descriptionCol }}">
                    <label class="p-0 col-form-label {{ $descriptionRight ? 'text-right float-right ' : '' }}small">
                        {!! nl2br(__(('form.'.($field_text).'.description'))) !!}
                    </label>
                </div>
            @endif
        </div>
        @break
    @case (1)
        <div class="mb-3">
            <mwc-text{{ $textarea ? 'area' : 'field' }}
             helperpersistent
             helper="{{ $helper }}"
             class="w-100"
             type="{{ $type }}"
             label="{{ $labelText }}"
             id="@name($field)"
             name="@name($field)"
             value="{{ $value }}"
             {{ $readonly ? 'disabled' : '' }}
             {{ $required }}
             validationMessage="{{ $validateMessage }}"
             outlined></mwc-textfield>
        </div>
        <script>
            $(document).ready(function(){
                let id = '@name($field)';
                window.addEventListener('load', () => {
                    if(id != 'version')
                        document.getElementById(id).layout();
                })
            });
        </script>
        @break
    @case (2)
        @if ($textarea)
            <textarea class="form-control" {{ $readonly }} id="@name($field)" name="@name($field)" type="{{ $type }}" placeholder="{{ $placeholderText }}">{!! $value !!}</textarea>
        @else
            <input class="form-control" {{ $readonly }} value="{{ $value }}" {{ $multiple }} id="@name($field)" name="@name($field)" type="{{ $type }}" placeholder="{{ $placeholderText }}" accept="{{ $accept }}">
        @endif
        <div class="ml-1">
            <div class="input-group">
                @foreach ($append as $row)
                    @switch ($row['type'])
                        @case ('button')
                            <button class="btn {{ $row['button-type'] ?? 'btn-outline-primary' }}" id="{{ $row['text'] ?? '' }}" type="button" onclick="{{ $row['on']['click'] ?? '' }}">{{ $row['text'] }}</button>
                            @break
                    @endswitch
                @endforeach
            </div>
        </div>
        @break

    @case (3)
        <?php
            $labelCol = $labelCol ?? 3;
            $descriptionCol = $descriptionCol ?? 0;
            $selectCol = $select ? ($selectCol ?? 3) : 0;
            $inputCol = 12 - $labelCol - $descriptionCol - $selectCol;
            if ($inputCol < 1) {
                $inputCol += 12;
            }
        ?>

        @if ($select)
            <?php $select['layout'] = 3;?>
            <div class="col-md-{{ $selectCol }}">
                @include('partials.form.select', $select)
            </div>
        @endif
        @break

@endswitch
