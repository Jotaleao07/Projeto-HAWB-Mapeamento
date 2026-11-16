@php($checked = $checked ?? false)
<div class="a11y-option">
    <span class="a11y-option-text">
        <strong>{{ $label }}</strong>
        <small>{{ $desc }}</small>
    </span>
    <button type="button" class="a11y-switch" role="switch"
            aria-checked="{{ $checked ? 'true' : 'false' }}"
            data-group="{{ $group }}" data-key="{{ $key }}"
            aria-label="{{ $label }}"><span class="knob"></span></button>
</div>
