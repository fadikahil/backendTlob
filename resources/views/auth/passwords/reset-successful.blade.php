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
    <style>
        .center-screen {
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            min-height: 100vh;
        }
    </style>
</head>
<body>
<div id="app">
    <div id="main" class='layout-navbar'>
            <div class="container center-screen">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h1>Password reset successful</h1>
                            </div>
                        </div>
                    </div>
                </div>
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
