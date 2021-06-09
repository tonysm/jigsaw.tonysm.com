@extends('_layouts.main')

@section('body')
    <a href="/"
       class="z-10 inline-block px-4 py-2 mx-auto my-4 text-sm text-center no-underline bg-white border border-gray-700 rounded-full button-link back md:shadow-lg md:fixed md:top-0 md:left-5">
        ᐊ back to home
    </a>

    <div class="leading-relaxed">
        <div class="max-w-6xl pt-10 mx-auto md:pt-36">
            <h1 class="mb-4 text-5xl font-bold text-center md:text-6xl">Hotwire & Laravel</h1>
        </div>
        <div class="max-w-2xl py-10 mx-auto prose prose-lg content">
            <p>I’ve been working on <a href="https://github.com/tonysm/turbo-laravel">Turbo Laravel</a> - a package that
                bridges Hotwire with Laravel, and although I have done an <a href="https://youtu.be/qqLVbd_uGiI?t=171">introduction
                    to Hotwire</a> before, I didn't really dive into using the package itself (lol, right?). And I also
                didn't cover the <a href="https://turbo.hotwire.dev/handbook/native">Turbo Native</a> aspect of building
                applications this way, so… it’s time!</p>

            <h2 id="screencasts">Screencasts</h2>

            <p>If you enjoy video content, I’ve recorded 3 videos showing the building blocks of using this
                technique.</p>

            <h3 id="01-hotwire-101">01 Hotwire 101</h3>

            <p>In the first episode, I cover what <a href="https://hotwire.dev/">Hotwire</a> is, going over the concepts
                of Turbo Drive, Turbo Frames, and Turbo Streams over HTTP (as responses). The demo application uses a
                Laravel app. We end up having to do a lot of boilerplate to get things to work (such as request
                validations).</p>

            <div class="embed-responsive">
                <iframe src="https://www.youtube.com/embed/d11HRp-WYC4"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen="" frameborder="0"></iframe>
            </div>

            <h3 id="02-turbo-laravel">02 Turbo Laravel</h3>

            <p>For the second episode, it’s time to introduce the <a href="https://github.com/tonysm/turbo-laravel">Turbo
                    Laravel</a> package and solve some of the pain-points we saw on the previous video. I also take the
                opportunity to show how to broadcast Turbo Streams over WebSockets, and for that I go over how to setup
                the <a href="https://beyondco.de/docs/laravel-websockets/getting-started/introduction">Laravel
                    WebSockets</a> package with <a href="https://laravel.com/docs/8.x/sail">Laravel Sail</a> so you can
                test it all locally.</p>

            <div class="embed-responsive">
                <iframe src="https://www.youtube.com/embed/70fCMBNsKvs"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen="" frameborder="0"></iframe>
            </div>

            <h3 id="03-turbo-native">03 Turbo Native</h3>

            <p>For the third and last episode, I go over the <a href="https://turbo.hotwire.dev/handbook/native">Turbo
                    Native</a> aspect of Hotwire. The Basecamp folks prepared a <a
                        href="https://github.com/hotwired/turbo-android/tree/main/demo">demo app</a> for us, so I’ll use
                that to show the technique. A little disclaimer here: I’m no mobile developer. I’ve done an Android
                development course once, but it was a long time ago. Anyways, I think this is a really powerful way of
                building hybrid applications and allows you to enhance your mobile apps per page and as much fidelity as
                you need.</p>

            <div class="embed-responsive">
                <iframe src="https://www.youtube.com/embed/RcGgpY6sERo"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen="" frameborder="0"></iframe>
            </div>

            <h2 id="hotwire--laravel">Hotwire &amp; Laravel</h2>

            <p>The <a href="https://github.com/tonysm/turbo-laravel">Turbo Laravel</a> package is pretty much done. I
                haven’t tagged v1 yet because I’m waiting on the <a href="https://turbo.hotwire.dev/">Turbo.js</a>
                library to get stable as well, although I can’t seen anything changing that would force me to change a
                lot (the integration surface of the backend and Turbo.js is really small).</p>

            <p>I thought of writing a longer version of an introduction to the package itself (some kind of Getting
                Started), but I think that both the <a href="https://turbo.hotwire.dev/handbook/introduction">Turbo
                    Handbook</a> and the <a href="https://github.com/tonysm/turbo-laravel/tree/main/docs">Turbo Laravel
                    documentation</a> really covers a lot of what is possible. So, for now, I’ll just reference them.
            </p>

            <p>I hope you enjoy the videos and let me know if I said something wrong.</p>
        </div>
    </div>
@endsection
