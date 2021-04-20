<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Web page analyzer for SEO - @yield('title')</title>
    <!-- Scripts -->
    <script type="text/javascript" nonce="cfa9cc439e064eac8ae0154abe0" src="//local.adguard.org?ts=1618840921775&amp;type=content-script&amp;dmn=/&amp;app=com.google.Chrome&amp;css=1&amp;js=1&amp;gcss=1&amp;rel=1&amp;rji=1&amp;sbe=0&amp;stealth=1&amp;uag="></script>
    <script type="text/javascript" nonce="cfa9cc439e064eac8ae0154abe0" src="//local.adguard.org?ts=1618840921775&amp;name=AdGuard%20Extra&amp;type=user-script"></script><script src="/js/app.js" defer></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="https://php-l3-page-analyzer.herokuapp.com/css/app.css" rel="stylesheet">
</head>
<body class="min-vh-100 d-flex flex-column">

<header class="flex-shrink-0">
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



@yield('messages')


@yield('description with input')


@yield('Link Data')


@yield('linksDataTable')


@yield('url_input_form')
</body>
</html>
