@extends('_layouts.main')

@section('body')
    <div class="max-w-6xl mx-auto pt-10 md:pt-36">
        <h1 class="text-5xl md:text-6xl text-center font-bold mb-4">Hey, there!</h1>
    </div>
    <div class="max-w-2xl mx-auto content space-y-16 py-10 md:pt-36">
        <section class="space-y-4">
            <h2 class="text-3xl font-mono font-semibold mb-4">$ whoami</h2>
            <p>My name is <strong>Tony Messias</strong>, a curious <em>Software Geek</em> from Brazil 🇧🇷.</p>
        </section>

        <section class="space-y-4">
            <h2 class="text-3xl font-mono font-semibold mb-4">$ cat /var/work.txt</h2>
            <p>I'm working at <a href="https://worksitesafety.ca/">Worksite Safety</a> as a Lead PHP Developer. Working primarily with Laravel/PHP/Vue.</p>
            <p>Before that, I worked at <a href="https://madewithlove.com/">madewithlove</a> as a Full-Stack Software Engineer, and part-time DevOps for a bit more than 5 years. Built the most different kind of projects: from content platforms, to booking websites, to real-estate systems, and even a bit of IoT working on a building management platform (yes, automating all sorts of things in a building).</p>
            <p>As part-time DevOps person, I many deploying and configuring local development environments on a lot of different projects. We had a lot different projects there, so I had to learn a bunch of different OPS tools and platforms, such as Docker, Kubernetes, Laravel Forge, Laravel Envoyer, DigitalOcean, AWS (including, but not limited to, some non-conventional services like the AWS Transcribe and AWS IoT Core), Azure, GCP, among other things.</p>
            <p>And before that, I worked as a Back-end Engineer at Gabstr, a location-based social network. Built the APIs we used and a geo-location based content platform on top of Redis and PostGIS.</p>
        </section>

        <section class="space-y-4">
            <h2 class="text-3xl font-mono font-semibold">$ cat /var/thoughts.txt</h2>
            <p>Writing is how we consolidate our learnings. Here's some of my writings:</p>
            <ul class="list-disc ml-5">
                @foreach($posts as $post)
                    <li>
                        <a href="{{ $post->getUrl() }}">{{ $post->title }}</a> − <time datetime="{{ $page->date }}">{{ date('F j, Y', $page->date) }}</time>
                    </li>
                @endforeach
            </ul>
            <p>
                Want to receive updates on new content? Here's my <a href="/rss">RSS</a> feed.
            </p>
            <p>
                My email address is <a href="mailto:tonysm@hey.com">tonysm@hey.com</a>
            </p>
        </section>
    </div>
@endsection
