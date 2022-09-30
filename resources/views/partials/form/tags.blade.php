<?php
    $field_text = $field_text ?? $field;
    $type = $type ?? 'text';
    $mb = $mb ?? 4;
    $labelText = __('form.'.$field_text.'.label');
    $readonly = ($readonly ?? false) ? 'readonly' : '';
    $value = join(',', $value ?? []);
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
                <input
                 type="text"
                 name="@name($field)"
                 id="@name($field)"
                 value="{{ $value }}"
                 data-role="tag-input"/>
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

<script type="text/javascript">
    $(document).ready(function(){
        let init = () => {
            $('input[data-role=tag-input]').selectize({
                persist: !1,
                createOnBlur: !0,
                create: !0
            });
        };
        if (document.readyState == 'complete')
            init();
        else
            window.addEventListener('load', init);        
    });
</script>
