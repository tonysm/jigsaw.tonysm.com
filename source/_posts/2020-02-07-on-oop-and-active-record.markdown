---
extends: _layouts.post
title: 'On OOP and Active Record'
date:   2020-02-07
tags: activerecord oop
section: content
---

I'm a huge fan o [Sandi Metz](https://twitter.com/sandimetz), I have watched some of her recorded talks multiple times, the most recent one was called "[Polly want a message](https://www.youtube.com/watch?v=XXi_FBrZQiU)" and it's also my favorite so far (if this is a reference to a Nirvana song, I like it even more).

In that talk, she goes on refactoring some code into smaller objects and giving them appropriate names. Eventually, she gets into this a very nice design with no branching in code (conditionals), which leads to easily testable code. I like it a lot.

![After the refactoring overview](/assets/images/on-oop-and-active-record/talk.png)

One thing that got me thinking was that "_Listing_" class. She has talked about such classes before in other talks. I remember another talk where she mentions that "the controller should only talk to a single class". Well, the Listing is that class (I think).

Recently, in a [podcast](https://twitter.com/JasonSwett/status/1222157273226792960), they explored that view a bit more, and it was confirmed that this is indeed the class that the controller should talk to.

During the interview, they go over another example referring to a _ReconcilableCharges_ class that talks to the AR models (or objects that respond to messages sent to the AR models). She even goes into the folder structure and states that these are the only classes allowed to interact with the AR models.

The folder structure could look something like this:

```bash
app/models
├── ar
│  ├── order.rb
│  └── payment.rb
├── orders.rb
├── payments.rb
└── reconcilable_charges.rb
```

We have the Active Record models inside the "models/ar" folder, while the domain classes living outside. Some people call these "service objects", or "interactors", you name it. 

The point is: these classes form the domain of the application. They are the ones talking to the Active Record models. But they can do much more than that.

I think I like this structure. I've seen this before in the [Phoenix Framework](https://www.phoenixframework.org). This looks similar to what they call "[contexts](https://hexdocs.pm/phoenix/contexts.html)".

## Taking to the extreme

I saw ideas like this taken to the extreme a few times. However, I think the problem I saw wasn't related to this structure, it was more about a misunderstanding of the Single Responsibility Principle (SRP). The idea that those _contexts_ (actors?) classes only having a single method or "do only 1 thing" and you end up with a bunch of behavior spread all over a handful of classes, when in fact those [_affordances_](https://adamwathan.me/2017/01/24/methods-are-affordances-not-abilities/) belong to a single actor in the system.

The SRP is all about the reason to change. Not how many methods or lines of code a class has. It means that your context should have one reason to change. It should answer to a single stakeholder.

That to say that these **context classes can have many methods**. Well, I prefer to say "a few" instead of "many", but you got it.

If you put logic in classes that do only 1 thing, you have procedural code wrapped in a class. Or a [Transaction Script](https://martinfowler.com/eaaCatalog/transactionScript.html). It might feel like it's easier to maintain because, at the end of the day, you have very shallow classes that do only 1 thing. But it's not cohesive.

Object-Oriented code is all about building simulations. Abstractions (or simplifications, if you prefer) of the real world (aka. objects) that communicate through message-passing (method calls, in class-based languages).

## Wrapping up

Although I like this idea, I have to be honest. I still have scars from code-bases where the "only 1 thing" rule was enforced, and they itch. I can see the usefulness of structures like this, but I can also see teams "mandating" that the AR models are never used anywhere else.

Ever since I started creating more controllers for my apps (see [here](https://www.youtube.com/watch?v=MF0jFKvS4SI) and [here](https://www.youtube.com/watch?v=GFhoSMD6idk)) I stopped worrying about more "robust architectures" (whatever that means to you) and started enjoying the code much more. Simpler and smaller controllers lead to a cohesive system.

This doesn't mean that I only have AR models in my domains, not at all. There are still places for plain-old objects. And you know those places. Those concepts that don't really match with any of your existing models and might depend on or fit in multiple of them. Yeah, those. Extract them to a class, name them properly and inject their dependencies (see [here](https://www.youtube.com/watch?v=hkmrfjex7jI&list=PL9wALaIpe0Py6E_oHCgTrD6FvFETwJLlx&index=4)).