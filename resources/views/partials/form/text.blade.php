<div class="form-group {{ [
        'md-3',
        'row',
        'row'
    ][$layout ?? 0] }}">
    <label for="@name($field)" class="{{ [
            '',
            'col-md-2 col-form-label',
            'col-md-3 col-form-label'
        ][$layout ?? 0] }}">
        @lang('form.'.($field_text ?? $field).'.label')
    </label>
    <div class="input-group p-0 {{ [
            '',
            'col-md-10',
            'col-md-9'
        ][$layout ?? 0] }}">
        <div class="col-form-label">
            @if ($field == 'category')
                @if (!empty($option[$value[0]]))
                    {{ $option[$value[0]]['text'] ?? '없음' }}
                    @if (!empty($option[$value[0]]['child']))
                        > {{ $option[$value[0]]['child'][$value[1]]['text'] ?? '없음' }}
                    @endif
                @endif
            @else
                {!! $value ?? '' !!}
            @endif
        </div>
    </div>
</div>
