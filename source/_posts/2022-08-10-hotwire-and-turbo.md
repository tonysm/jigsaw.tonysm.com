---
extends: _layouts.post
title: 'Hotwire and Turbo'
date:   2022-08-10
tags: hotwire laravel turbo
section: content
excerpt: Hotwire is an approach to building multi-platform applications powered by a web application, typically a monolith, but not necessarily.
is_draft: true
---

There are many introductions to Hotwire and Turbo out there, but I feel like there's still room for more, so here's mine.

Hotwire is an approach to building multi-platform applications powered by a web application, typically a monolith, but not necessarily.

This approach is devided into 3 main libs: Turbo, Stimulus, and Strada. I consider Turbo to be the main one, and the one that makes this approach possible. Although Stimulus works *extremely* well with this approach, you could replace it with something like Alpine.js, for instance (although, IME, it's not a straight-forward replacement.) We don't know much about Strada yet.

Turbo itself is devided into 3 other technologies: Turbo Drive, Turbo Frames, and Turbo Streams. There's also Turbo Native, which is the mobile development side of this and it consists of two other libs called Turbo Native for Android and Turbo Native for iOS, so it's really 5 techologies.

Let's focus on the web-side of Turbo first.

When we install the Turbo.js library in our web application, it enables Turbo Drive by default. Turbo Drive is the spiritual successor of Turbolinks, from the Rails community. In short, Turbo will hijack any links' clicks and any forms' submissions and will convert those into fetch requests (AJAX) instead of making full page refreshes, which would be the default browser behavior. It gets the `body` of the response and replaces the current page's with it. It also merges the head tags.

Your traditional, server rendered web application was converted into an SPA. Instead of refreshing the page, Turbo gives you a stateful process that runs in the user's browser, which is long-lived, until the page refreshes.

![Turbo Drive](/assets/images/hotwire-and-laravel/turbo-drive.png)

This is already pretty cool and has many benefits, like it improves the perceived speed of your web app (not the *actual* speed, but it's a significant improvement.) However, having to replace the entire body of your page whenever a link/form is triggered would be costly. It would also have some drawbacks like loosing event listeners that are attached to existing DOM elements and having to reconnect them (which happens automatically with Stimulus, but it's still work that needs to be done), or losing state in form elements and stuff.

It would be cool if we could replace only portions of the page. That's what Turbo Frames are for.

When you install Turbo, it activates Turbo Drive and also registers 2 new custom HTML elements: a `<turbo-frame>` tag as well as a `<turbo-stream>` one.

With Turbo Frames, you may annotate your HTML to instruct Turbo Drive which parts of the page will have its own "scope" for the links/forms triggers and the HTML replacement.

This means that instead of replacing the entire body of the page with the response's body, Turbo will look for a matching Turbo Frame. It will use the current "scoped" Turbo Frame ID for this "match." This means that the response must also contain a Turbo Frame with the same ID.

![Turbo Frames](/assets/images/hotwire-and-laravel/turbo-frames.png)

Now we can break our pages into smaller chunks using Turbo Frames and let them update independently. This means that things like event listeners and input states are preserved if they are not inside the Turbo Frame in scope.

That's cool, but a single request now can only update either the entire page or a single part of it. It would be cool if we could update multiple separate parts of the page from a single request. That's possible with Turbo Streams.

Turbo will add a `Accept: text/vnd.turbo-stream.html` header which can be detected by the web app on the server-side when handling the requests and instead of returning a regular HTML response, it could return a Turbo Stream response, which consists of a `Content-Type: text/vnd.turbo-stream.html` and a response body that may contain multiple `<turbo-stream>` tags.

![Turbo Streams](/assets/images/hotwire-and-laravel/turbo-streams.png)

What's also cool about Turbo Streams is that we can take that same HTML we return, and broadcast to our users over WebSockets or Server-Sent Events (SSE). WebSockets are not actually required for this to work, but it's pretty cool how well it works with Turbo.

So, that's really what Turbo is. At least the web portion of it. Once you understand Turbo, your main challenge is really decomposing your web application to make the most out of it.

![Decomposing the app](/assets/images/hotwire-and-laravel/decomposing-the-app.png)

All we saw so far has nothing to do with Laravel, or Rails, or any other web framework. Turbo is agnostic of the programming language or web framework you're using on the backend.

In the next article, we're going to dive into [Turbo Laravel](https://github.com/tonysm/turbo-laravel) and see how we can use it to bridge the server-side part of Turbo.
