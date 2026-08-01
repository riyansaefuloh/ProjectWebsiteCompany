<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Company Profile CMS' }}</title>
    @livewireStyles
</head>
<body style="margin: 0; padding: 0; font-family: sans-serif; background-color: #f9fafb;">
    {{ $slot ?? $content ?? '' }}
    @yield('content')
    @livewireScripts
</body>
</html>
