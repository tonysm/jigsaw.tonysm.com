@extends('_layouts.main')

@section('body')
    <div class="leading-relaxed">
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
                <p>I'm working at <a href="https://worksitesafety.ca/">Worksite Safety</a> as a Lead PHP Developer.
                    Working primarily with Laravel/PHP/Vue.</p>
                <p>Before that, I worked at <a href="https://madewithlove.com/">madewithlove</a> as a Full-Stack
                    Software Engineer, and part-time DevOps for a bit more than 5 years. Built the most different kind
                    of projects: from content platforms, to booking websites, to real-estate systems, and even a bit of
                    IoT working on a building management platform (yes, automating all sorts of things in a building).
                </p>
                <p>As part-time DevOps person, I managed to deploy and configure local development environments on a lot
                    of different stacks. I was part of a bunch of different projects, so I interacted with a set of different
                    OPS tools and platforms, such as Docker, Kubernetes, Laravel Forge, Laravel Envoyer, DigitalOcean,
                    AWS (including, but not limited to, some non-conventional services like the AWS Transcribe and AWS
                    IoT Core), Azure, GCP, among other things.</p>
                <p>And before that, I worked as a Back-end Engineer at Gabstr, a location-based social network. Built
                    the APIs we used there and a geolocation based content platform on top of Redis and PostGIS.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-3xl font-mono font-semibold mb-4">$ cat /var/courses.txt</h2>
                <p>I've been working on video content, you can find them here:</p>

                <ul class="list-disc ml-5">
                    <li class="space-x-2"><a href="/courses/hotwire-laravel">Hotwire & Laravel</a> <span
                                class="text-sm px-2 py-1 rounded bg-blue-100 text-blue-800">#free</span></li>
                    <li class="space-x-2"><a href="/courses/kubernetes-for-laravel-developers">Kubernetes for Laravel
                            Developers</a> <span
                                class="text-sm px-2 py-1 rounded bg-blue-100 text-blue-800">#free</span></li>
		</ul>
		<p>You can find more videos on my <a href="https://www.youtube.com/c/TonyMessiasDev">YouTube channel</a> 🎬.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-3xl font-mono font-semibold">$ cat /var/thoughts.txt</h2>
                <p>Writing is how we consolidate our learnings. I'm currently experimenting with
                    <a href="https://world.hey.com/tonysm">HEY World</a>, so check that out for new posts. Here's
                    some of my past writings:</p>
                <div class="space-y-6">
                    @foreach($posts as $post)
                        <div class="p-6 rounded-lg border shadow-lg space-y-4">
                            <div class="text-center">
                                <time datetime="{{ $post->date }}"
                                      class="text-sm text-gray-500">{{ date('F j, Y', $post->date) }}</time>

                                <h3 class="text-3xl font-bold">{{ $post->title }}</h3>
                            </div>

                            <p>{{ strip_tags(\Illuminate\Support\Str::limit($post->getContent(), 300)) }}</p>

                            <div class="text-center">
                                <a href="{{ $post->getUrl() }}"
                                   class="inline-block mx-auto rounded-full border px-4 py-2 text-sm no-underline back">Read
                                    more</a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p>
                    Want to receive updates on new content? Here's my <a href="/rss">RSS</a> feed.
                </p>
                <p>
                    My email address is <a href="mailto:tonysm@hey.com">tonysm@hey.com</a>
                </p>
            </section>
        </div>
    </div>
@endsection
