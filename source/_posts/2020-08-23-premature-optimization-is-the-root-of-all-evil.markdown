---
extends: _layouts.post
title: 'Premature optimization is the root of all evil...'
date:   2020-08-23
tags: optimization fallacies
section: content
excerpt: Optimizations don't always equal to great improvements. First, measure it. Then, optimize if necessary.
---

You might have read this quote before. This is actually only part of the real quote, which [was](https://en.wikiquote.org/wiki/Donald_Knuth):

> Programmers waste enormous amounts of time thinking about, or worrying about, the speed of noncritical parts of their programs, and these attempts at efficiency actually have a strong negative impact when debugging and maintenance are considered. We should forget about small efficiencies, say about 97% of the time: premature optimization is the root of all evil. Yet we should not pass up our opportunities in that critical 3%.

This is not advice against optimization. The gist of this is that we shouldn't spend time blindly optimizing code that in _theory_ can be faster. We should always be backed by data.

The numbers seem arbitrary, though. Where did 97% and 3% come from? I haven't read Knuth's books yet, so I really don't know. In practice, I think it's safe to assume [Pareto's principle](https://en.wikipedia.org/wiki/Pareto_principle): 80% of your application performance issues come from 20% of the code.

When we're optimizing applications, it's important to understand "the grand scheme" of things. That little micro-optimization that made it 80% faster might not make any difference at all. How much of the total work is spent doing that in the first place?

That's not to say that micro-optimizations are bad. But if the application is spending 4ms of the total time doing that thing we optimized, making it 10% faster will only make it run in 3.6ms instead of 4ms. If the total response time is 300ms, saving 0.4ms doesn't seem like a lot, does it?

## You can't improve what you don't measure

Benchmarking only tells part of the story. It's only able to tell the micro-level of the optimization. We need to know the whole story. Or most of it (80%?). When we profile that change in a real-world scenario, we don't get much out of that micro-optimization.

When it comes to code optimization, it should always be backed by benchmarks and profiling.

* [Benchmark](https://en.wikipedia.org/wiki/Benchmark_(computing)) can give us how much faster code A is compared to code B;
* [Profiling](https://en.wikipedia.org/wiki/Profiling_(computer_programming)) can give us how much time our application spends running code A for an entire feature (or code path).

The first thing we need to do when we put an application in production is configuring a good instrumentation tool. Application Performance Management tools (aka. APMs) can give us the profiling information. Tools such as [Blackfire](https://blackfire.io), [NewRelic](https://newrelic.com/), or [Elastic APM](https://www.elastic.co/apm) seem like good choices. They can tell us where the code is spending more time at.

## Optimization Fallacies

We sometimes take other people's advice blindly. For sure N+1 queries are always bad, right? Well, not always. Basecamp uses a caching technique called "[Russian Doll Caching](https://signalvnoise.com/posts/3690-the-performance-impact-of-russian-doll-caching)" a lot. The idea consists of caching entire partials on the view layer so that the lazily-loaded relationships are not even used in the first place. This, in combination with [model touching](https://guides.rubyonrails.org/association_basics.html#options-for-belongs-to-touch) (if we have a `Post hasMany Comment` relationship set where `Comment` changes will touch on its `Post`'s timestamp) enables [N+1 queries as a feature](https://www.youtube.com/watch?v=ktZLpjCanvg) instead of a bug.

_Note: My feeling is that we underuse caching in Laravel. Not sure why, but stuff like clearing the cache is common in deployment scripts. Caching in the view layer isn't common either (IME). And eager-loading at the controller level to avoid N+1 queries seems to be the de facto way of doing things these days. Basecamp seems to heavily make use of caching and N+1 queries without much trouble._

The point is: we need to understand the full picture of things. The "grand scheme". We need profiling data in order to make any relevant optimizations on our applications. Rewriting applications in a faster, compiled languages won't necessarily make it that much faster or cheaper to [run](https://m.signalvnoise.com/only-15-of-the-basecamp-operations-budget-is-spent-on-ruby/).
