<!doctype html>
<html class="no-js" lang="de" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
          value="Deutschlandweit haben sich knapp 100 ausgewählte Dentallabore der Idee von Padento angeschlossen, Menschen in ihrem Dentallabor vor Ort zahntechnisch zu beraten. ">
    <link rel="alternate" hreflang="de" href="{{url('/')}}"/>
    <link rel="alternate" hreflang="de-AT" href="{{url('/at')}}"/>
    <link rel="stylesheet" href="{{ URL::asset('assets/css/style.css?v=1.0') }}">
    @if (app()->environment() == 'local')
        <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
    @endif
    <title>
        @if(isset($title))
            – {{ $title }}
        @else
            Zahnimplantat | Beratung Zahnersatz | Padento.de
        @endif
    </title>

    @if (app()->environment() != 'local')
    <!-- Facebook Pixel Code -->
        <script>
            var disableStr = 'facebookOptOut';
            if (document.cookie.indexOf(disableStr + '=true') > -1) {
                console.log('Opt Out Facebook');
            } else {
                !function (f, b, e, v, n, t, s) {
                    if (f.fbq) return;
                    n = f.fbq = function () {
                        n.callMethod ?
                            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                    };
                    if (!f._fbq) f._fbq = n;
                    n.push = n;
                    n.loaded = !0;
                    n.version = '2.0';
                    n.queue = [];
                    t = b.createElement(e);
                    t.async = !0;
                    t.src = v;
                    s = b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t, s)
                }(window, document, 'script',
                    'https://connect.facebook.net/en_US/fbevents.js');

                fbq('init', '798274203651101');
                fbq('track', 'PageView');
                fbq('track', '{{ AB::getCurrentTest()  }}');
            }

            function optOutFacebook() {
                document.cookie = disableStr + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
            }
        </script>

        <script>
            var disableStr = 'GoogleAnalyticsOptOut';
            if (document.cookie.indexOf(disableStr + '=true') > -1) {
                console.log('Opt Out Google Analytics');
            } else {
                (function (i, s, o, g, r, a, m) {
                    i['GoogleAnalyticsObject'] = r;
                    i[r] = i[r] || function () {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
                    a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a, m)
                })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

                ga('create', 'UA-49629438-1', 'auto');
                ga('set', 'anonymizeIp', true);
                ga('send', 'pageview');
            }

            function optOutGA() {
                document.cookie = disableStr + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
            }
        </script>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-TR7X3RG');</script>
        <!-- End Google Tag Manager -->
    @endif
    @yield('head')
<style>
    @media (min-width: 612px) {
        #cta {
            font-size: 3.5vw;
        }
    }
    @media (min-width: 1024px) {
        #cta {
            font-size: 2.5vw;
        }
    }
    @media (max-width: 1024px) {
        #cta {
            font-size: 2.5vw;
        }
    }
    @media (min-width: 1024px) {
        #cta {
            font-size: 2vw;
        }
    }
    @media (max-width: 612px) {
        #cta {
            font-size: 4vw;
        }
    }
    @media (min-width: 1624px) {
        #cta {
            font-size: 1.6vw;
        }
    }
</style>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TR7X3RG"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php
if (!isset($_COOKIE['orig_ref'])) {
    if (isset($_SERVER['HTTP_REFERER']))
        setcookie('orig_ref', $_SERVER['HTTP_REFERER']);
}
if (!isset($_COOKIE['orig_page'])) {
    if (isset($_SERVER['HTTP_REFERER'])) {
        $value = '';
        $value = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        setcookie('orig_page', $value);
    }
}
?>

@include('common.page-header')
@yield('content')
@include('common.page-footer')

<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/cookieconsent.min.css') }}"/>
<script type="text/javascript" src="{{ URL::asset('assets/js/cookieconsent.min.js') }}"></script>
<script>
    window.addEventListener("load", function () {
        window.cookieconsent.initialise({
            "palette": {
                "popup": {
                    "background": "#f2f2f2"
                },
                "button": {
                    "background": "#00aff5"
                }
            },
            "position": "bottom-right",
            "content": {
                "message": "Um unsere Webseite für Sie optimal zu gestalten und fortlaufend verbessern zu können, verwenden wir Cookies. Durch die weitere Nutzung der Webseite stimmen Sie der Verwendung von Cookies zu. Weitere Informationen zum Datenschutz und Anpassungsoptionen zu Cookies erhalten Sie in unserer Datenschutzerklärung.",
                "dismiss": "Verstanden",
                "link": "Datenschutzerklärung",
                "href": "https://padento.de/datenschutzerklaerung"
            }
        })
    });

</script>


<!--script type="text/javascript" src="{{ URL::asset('js/all.js') }}"></script-->
<script type="text/javascript" src="{{ URL::asset('assets/js/scripts.js?v=1.1') }}"></script>

@yield('foot')
<!--script src='//cdn.tinymce.com/4/tinymce.min.js'></script-->

</body>
</html>
