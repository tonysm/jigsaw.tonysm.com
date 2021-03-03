---
extends: _layouts.post
title: "On SaaS Boilerplates"
date:   2020-12-11
tags: jetstream saas boilerplate
section: content
---

I tried to explain why it's so much simpler to adopt a SaaS boilerplate such as [Jetstream](https://jetstream.laravel.com/1.x/introduction.html) instead of rolling your own, but apparently I failed. I'm not sure if I did a bad job at explaining or what. So here's another attempt.

To me, the best thing about such boilerplate is how the teams are set up. People assume that the resources they create in a web application are "isolated" (unless specified otherwise). That "trait" is called multi-tenancy. It comes in different sizes and shapes.

The most common implementation of multi-tenancy is isolating by a "scope key". Something that uniquely identifies the resource owner. It's easy to assume that whenever we create something "we" are the "scope", but very shortly we'll want to share these resources with others.

You can model that collaboration in different ways. With these boilerplates, I would assign these resources to the user's current team. Billing would also be handled at the team level. Collaborators would be added to the team with different roles. Everything gets so much simpler this way.

What about "the regular user" that is not part of an organization or a team? Do they need to create their own teams? Short answer, yes. However, there is no "extra process", the sign up flow creates your "personal team" as soon as you sign up. You set up the billing information on your personal team, and you should be able to create your own resources, which will get assigned to your personal team.

If, at any point in time, you want to invite a collaborator, you can just do that. Personal Teams are Teams after all. Many applications have limitations on how many collaborators you can have in a team, which I'm not a fan of as this approach creates some friction on the collaborative aspects of your applications. [Some people call this "Collaboration Tax"](https://m.signalvnoise.com/why-we-never-sold-basecamp-by-the-seat/).

Anyways, with Jetstream we get a lot of things out-of-the-box such as a very flexible and simple way to organize our application's users and resources in "teams". Much simpler than trying to have different, special flows and multiple types of resource owners (users or teams) or multiple ways to collaborate on your applications.

You can call teams whatever you want on your applications, and you can have other levels of granularity to organize users, such as "groups", inside your teams. You just have to implement it yourself.
