@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['style' => 'background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; padding: 12px 16px; font-size: 13px; font-weight: 500; line-height: 1.5;']) }}>
        {{ $status }}
    </div>
@endif
