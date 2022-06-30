@extends('_layouts.main')

@section('head')
    @include('_partials.social', [
        'title' => 'Tony Messias',
        'description' => "Hey, there. I'm a programer from Brazil. This is my personal website and blog.",
    ])
@endsection

@section('body')
    <div>
        <div>
            <div class="prose sm:prose-xl mx-auto">
                <div class="pt-10 mx-auto md:pt-36">
                    <h1 class="text-center">Fighting boredom with curiosity.</h1>
                </div>
                <div class="py-10 mx-auto space-y-12 content md:pt-36">
                    <section>
                        <h2>Tony Messias</h2>
                        <p>I'm a programmer from Brazil. I write my techinical blog posts here, have a <a
                                href="https://www.youtube.com/c/TonyMessiasDev">YouTube channel</a> where I share some
                            screencasts, and a newsletter at <a href="https://world.hey.com/tonysm">Hey World</a>. Subcribe
                            there
                            if you want to receive my most recent posts/screencasts/ideas.</p>
                    </section>
                    <section>
                        <h3>Programmer at Tighten</h3>
                        <p>I'm a Full Stack Programmer at <a href="https://tighten.co/">Tighten</a>. My main focus is on
                            building
                            web applications with Laravel and whatever frontend stack the project needs or already uses.</p>
                    </section>
                    <section>
                        <h3>Creator of Turbo Laravel</h3>
                        <p>Lately I've been diving into <a href="https://hotwired.dev/">Hotwire</a> and I built the <a
                                href="https://github.com/tonysm/turbo-laravel">Turbo Laravel</a> package to make the most out
                            of
                            Hotwire in Laravel.</p>
                    </section>
                    <section>
                        <h2>Courses and Screencasts</h2>
                        <p>Sometimes I record a screencast about a topic I'm tinkering, a problem I've encountered, or a
                            tool
                            that I'm building or learning.</p>
                        <article>
                            <h3>Hotwire & Laravel (short video series)</h3>
                            <p>I've recorded a short series on using Hotwire, you may find it <a
                                    href="/courses/hotwire-laravel">here</a> (it's <em>free</em>).</p>
                        </article>
                        <article>
                            <h3>Kubernetes for Laravel Developers (video series)</h3>
                            <p>I've also recorded a video series on using Kubernetes from a perspective of a Laravel
                                developer.
                                You may find it <a href="/courses/kubernetes-for-laravel-developers">here</a> (it's also
                                <em>free</em>).
                            </p>
                        </article>
                    </section>
                </div>
            </div>

            <section>
                <div class="prose prose-xl mx-auto">
                    <h2>Writings</h2>
                    <p>My technical writings are hosted here, but you I also have a <a
                            href="https://world.hey.com/tonysm">Hey
                            World</a> newsletter. Subscribe there if you want to receive updates and new posts.</p>
                </div>

                <div class="sm:max-w-2xl mx-auto pt-4 space-y-8">
                    @foreach ($posts as $post)
                        <a class="block no-underline" href="{{ $post->getUrl() }}">
                            <div class="p-6 space-y-4 border rounded-lg shadow-lg post-card hover:shadow-xl">
                                <div class="text-center">
                                    <time datetime="{{ $post->date }}"
                                        class="text-sm text-gray-500">{{ date('F j, Y', $post->date) }}</time>

                                    <h3 class="text-3xl font-bold">{{ $post->title }}</h3>
                                </div>

                                <p>{{ strip_tags(\Illuminate\Support\Str::limit($post->getContent(), 300)) }}</p>

                                <div class="text-center">
                                    <span
                                        class="inline-block px-4 py-2 mx-auto text-sm no-underline border rounded-full button-link back hover:shadow-lg">Read
                                        more</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="my-8 prose prose-sm mx-auto text-center">
                    <p>
                        Want to receive updates on new content? Here's my <a href="/rss">RSS</a> feed. <br class="hidden sm:inline" />
                        My email address is <a href="mailto:tonysm@hey.com">tonysm@hey.com</a>
                    </p>
                </div>
            </section>
        </div>
    </div>
@endsection
