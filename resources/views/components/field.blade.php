@props([
    'name',
    'label',
    'type' => 'text',
    'bag' => 'default',
    'required' => false,
    'autocomplete' => null,
    'value' => null,
    'hint' => null,
])
@php
    $error = $errors->getBag($bag)->first($name);
@endphp
<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @unless ($required)
            <span class="text-body-secondary">(opcional)</span>
        @endunless
    </label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $type === 'password' ? '' : old($name, $value) }}"
        class="form-control @if ($error) is-invalid @endif"
        @if ($required) required @endif
        @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @if ($hint) aria-describedby="{{ $name }}-hint" @endif
    >
    @if ($hint)
        <div id="{{ $name }}-hint" class="form-text">{{ $hint }}</div>
    @endif
    @if ($error)
        <div class="invalid-feedback">{{ $error }}</div>
    @endif
</div>
