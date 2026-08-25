<!DOCTYPE html>
<html lang="{{ \App::getLocale() }}" style="background:#0b0c10">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <base href="{{ url('/') }}/" />
  <script>
    (function () {
      var theme = localStorage.getItem('launchhr_theme');
      if (theme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
      }
    })();
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/tailwind.css')) }}">
  <script id="app-js" src="{{ asset(mix('js/app.js')) }}" defer></script>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon.png') }}" />
  <title>@yield('title', config('app.name'))</title>

  @if (config('app.sentry.enabled'))
  <script>
    const SentryConfig = {!! \json_encode([
      'dsn' => config('sentry.dsn'),
      'environment' => config('sentry.environment'),
      'sendDefaultPii' => config('sentry.send_default_pii'),
      'tracesSampleRate' => config('sentry.traces_sample_rate'),
    ]); !!}
  </script>
  @endif

  @if (config('officelife.fathom_api_key') && config('app.env') == 'production')
    <script src="https://cdn.usefathom.com/script.js" data-site="{{ config('officelife.fathom_api_key') }}" defer></script>
  @endif

  @routes
</head>

<body>

  @inertia

</body>

</html>
