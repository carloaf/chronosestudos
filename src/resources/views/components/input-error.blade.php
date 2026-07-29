@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['style' => 'font-size: 12px; color: #e53e3e; margin-top: 4px; padding-left: 4px; list-style: none; line-height: 1.5;']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
