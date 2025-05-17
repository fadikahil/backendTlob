<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ $favicon ?? url('assets/images/logo/favicon.png') }}" type="image/x-icon">
    <title>@yield('title') || {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    @include('layouts.include')
    @yield('css')
</head>
<body>
<div id="app">
    @if(Auth::user() !== null) @include('layouts.sidebar') @endif
    <div id="main" class='layout-navbar'>
        @if(Auth::user() !== null) @include('layouts.topbar') @endif
        <div id="main-content">
            <div class="page-heading">
                @yield('page-title')
            </div>
            @yield('content')
        </div>
    </div>
    <div class="wrapper mt-5">
        <div class="content">
            @include('layouts.footer')
        </div>
    </div>
</div>

<!-- Add language initialization script -->
<script>
    // Initialize language labels if not already defined
    window.languageLabels = window.languageLabels || {
        "Are you sure": "{{ __('Are you sure') }}",
        "You wont be able to revert this": "{{ __('You wont be able to revert this') }}",
        "Yes Delete": "{{ __('Yes Delete') }}",
        "Cancel": "{{ __('Cancel') }}",
        "Confirm": "{{ __('Confirm') }}",
        "Delete": "{{ __('Delete') }}",
        "Edit": "{{ __('Edit') }}",
        "Yes Restore it": "{{ __('Yes Restore it') }}",
        "Yes Delete Permanently": "{{ __('Yes Delete Permanently') }}"
    };
</script>

@include('layouts.footer_script')
@yield('js')
@yield('script')
</body>
</html>
