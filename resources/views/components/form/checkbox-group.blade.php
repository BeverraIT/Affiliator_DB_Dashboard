@props([
    'label',
    'name',
    'options',
    'selected' => [],
    'maxInfo' => ''
])

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-semibold">{{ $label }}</div>
            @if($maxInfo)
                <div class="text-secondary small">{{ $maxInfo }}</div>
            @endif
        </div>
        
        <div class="row g-2">
            @foreach ($options as $id => $optionName)
                <div class="col-12 col-sm-6 col-lg-4">
                    <label class="w-100 rounded-pill border bg-light px-3 py-2 d-flex align-items-center gap-2 user-select-none" 
                           style="cursor:pointer;">
                        <input 
                            class="form-check-input m-0" 
                            type="checkbox" 
                            name="{{ $name }}[]" 
                            value="{{ $id }}" 
                            style="transform: scale(1.05);"
                            {{ in_array($id, old($name, $selected)) ? 'checked' : '' }}>
                        <span class="fw-semibold text-dark" style="font-size: 13px;">
                            {{ $optionName }}
                        </span>
                    </label>
                </div>
            @endforeach
        </div>

        @error($name)
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror

        @error($name.'.*')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror

    </div>
</div>