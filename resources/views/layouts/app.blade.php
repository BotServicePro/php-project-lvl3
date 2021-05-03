<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Web page analyzer for SEO - @yield('title')</title>
    <!-- Scripts -->
{{--    <script type="text/javascript" nonce="cfa9cc439e064eac8ae0154abe0" src="//local.adguard.org?ts=1618840921775&amp;type=content-script&amp;dmn=/&amp;app=com.google.Chrome&amp;css=1&amp;js=1&amp;gcss=1&amp;rel=1&amp;rji=1&amp;sbe=0&amp;stealth=1&amp;uag="></script>--}}
{{--    <script type="text/javascript" nonce="cfa9cc439e064eac8ae0154abe0" src="//local.adguard.org?ts=1618840921775&amp;name=AdGuard%20Extra&amp;type=user-script"></script><script src="/js/app.js" defer></script>--}}
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
          integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous"><header class="flex-shrink-0">
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <a class="navbar-brand" href="/">Web page analyzer</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="/">Main</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="/urls">Links</a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<body class="min-vh-100 d-flex flex-column expansion-alids-init vsc-initialized">

@yield('messages')


@yield('description_with_input')


@yield('link_data')


@yield('checks_data')


@yield('links_data_table')


@yield('url_input_form')



<footer class="border-top py-3 mt-5 flex-shrink-0">
    <div class="container-lg">
        <div class="text-center">
            <a href="https://ru.hexlet.io/u/karakinalex" target="_blank">AKarakin</a>
        </div>
    </div>
</footer>
</body>
</html>
