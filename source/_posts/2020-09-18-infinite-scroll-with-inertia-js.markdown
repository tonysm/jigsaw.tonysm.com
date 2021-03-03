---
extends: _layouts.post
title: 'Infinite Scrolling with Inertia.js'
date:   2020-09-18
tags: laravel inertiajs vue
section: content
---

A while ago [I wrote an introduction to Inertia.js](https://madewithlove.com/introduction-to-inertia-js/) article showing how it enables building modern monolith applications that wants to use a JavaScript framework as the rendering engine instead of your regular server-rendered HTML templating engine, such as Laravel's Blade or Rails' ERBs.

I was working on a piece of UI that had a requirement for [Infinite Scrolling](https://www.smashingmagazine.com/2013/05/infinite-scrolling-lets-get-to-the-bottom-of-this/). So I went ahead to try to implement that using Inertia. I tried a couple of ways, so I figured it would be fun to document my attempts and what I ended up using, as well as some foggy ideas.

My attempts were:

1. Load more messages using `Inertia.visit`; and
2. Load more messages using `axios.get` instead of making an Inertia visit.

Either way, I had to fix something first: we need a local state in our page component to keep the current messages shown, otherwise Inertia will replace our props with the items on the new page. Let me show you want I mean.

## Local State Management with Inertia.js

The issue with Infinite Scrolling and Inertia is that we usually pass data down to your components from the controller and using it as props on the page component:

```php

class ChatRoomsController extends Controller
{
  public function show(ChatRoom $chatRoom)
  {
    $messages = $chatRoom->messages()
      ->latest()
      ->with(['user'])
      ->paginate();

    return Inertia\Inertia::render('ChatRooms/Show', [
      'chatRoom' => $chatRoom,
      'messages' => $messages,
    ]);
  }
}

```

In the page component, we would have something like this:

```html

<template>
  <div>
    <button @click="loadMore">Load more...</button>
    <ul>
      <li v-for="message in messages.data" :key="message.id">
        {{ message.user.name }} said: {{ message.content }}
      </li>
    </ul>
  </div>
</template>

<script>
  export default {
    props: {
      chatRoom: Object,
      messages: Object,
    },
    methods: {
      loadMore() {
        // Get more messages.
      },
    },
  }
</script>

```

If we make another _Inertia visit_ to the `GET /chat-rooms/{chatRoom}` endpoint passing a `?page=2` query string, it would work in the backend, the query would skip the first items and give us the second "page" of messages, but Inertia would replace our `messages` prop, therefore we would lose track of the messages previously shown.

Luckily, we can fix that relatively easy by introducing a bit of local state, so our page component would become something like this:

```diff
<template>
  <div>
-   <button @click="loadMore">Load more...</button>
+   <button @click="loadMore" :disabled="loadingMore">Load more...</button>
    <ul>
-      <li v-for="message in messages.data" :key="message.id">
+      <li v-for="message in localMessages" :key="message.id">
        {{ message.user.name }} said: {{ message.content }}
      </li>
    </ul>
  </div>
</template>

<script>
  export default {
    props: {
      chatRoom: Object,
      messages: Object,
    },
+   data () {
+     return {
+       loadingMore: false,
+       localMessages: this.messages.data,
+       pagination: this.messages,
+     };
+   },
    methods: {
      loadMore() {
        // Get more messages.
      },
    },
  }
</script>
```

Alright, now we are ready to explore the first attempt.

## Load more messages using Inertia.visit

To implement the `loadMore` methods in the `ChatRooms/Show.vue` page component, we need to make another Inertia visit:

```html
<script>
  export default {
    // The rest of the component...
    methods: {
      loadMore() {
        if (this.loadingMore) return;

        this.loadingMore = true;

        this.$inertia
          .visit(
            `/chat-rooms/${this.chatRoom.id}?page=${this.pagination.current_page + 1}`,
            { preserveState: true }
          )
          .then(() => {
            // Prepending the old messages to the list.
            this.localMessages = [
              ...this.messages.data,
              ...this.localMessages,
            ]);

            // Update our pagination meta information.
            this.pagination = this.messages;
          })
          .finally(() => this.loadingMore = false);
      },
    },
  }
</script>
```

So, essentially, we are making another Inertia visit and that will get to the `ChatRoomsController@show` controller, load the second page of messages and return to Inertia, so it can then re-render the page component with the new props. If we had more props here, we could tell it to only care about the `messages` prop by using [Partial Reloads](https://inertiajs.com/requests#partial-reloads) and [Lazy Evaluation](https://inertiajs.com/responses#lazy-evaluation), but let's keep it simple for now.

What is impontant to note here is that we are telling Inertia to _preserve the current component's state_ by passing `{ preserveState: true }` to the visit, otherwise it would force a new component (with a new state) to render, losing our `localMessages` data.

Although this approach works, when we load more items into the page, since this is a new Inertia visit, we get a new page added to your browser history stack. Which means that if we hit the back button of our browser after loading a couple pages, you will go back to the previous page, but also losing your local state, because Inertia will only restore the previous props.

Also, with this approach, if we hit _refresh_ on our browser, we will only see the current page's messages, which means your local state was also lost and the backend is making use of the `?page=3` param in the query string.

We could solve this problem by storing the messages on `localStorage` keyed by the chat room ID or something like that, but I think that would get even more trickier.

Let's explore the second approach.

## Loading more messages using axios.get

We could make this one work by using axios directly, instead of making an Inertia.visit. Let me show you what I mean:

```diff
<script>
  export default {
    // The rest of the component...
    methods: {
      loadMore() {
        if (this.loadingMore) return;

        this.loadingMore = true;

-       this.$inertia
-         .visit(
-           `/chat-rooms/${this.chatRoom.id}?page=${this.pagination.current_page + 1}`,
-           { preserveState: true }
-         )
+       axios.get(`/chat-rooms/${this.chatRoom.id}?page=${this.pagination.current_page + 1}`)

-         .then(() => {
+         .then(({ data }) => {
            // Prepending the old messages to the list.
            this.localMessages = [
-             ...this.messages.data,
+             ...data.data,
              ...this.localMessages,
            ]);

            // Update our pagination meta information.
-           this.pagination = this.messages;
+           this.pagination = data;
          })
          .finally(() => this.loadingMore = false);
      },
    },
  }
</script>
```

We are not done yet. Now we are making an AJAX request to the `GET /chat-rooms/{chatRoom}` route, which returns an Inertia response, but we don't want that. Since this is not an Inertia visit, it would treat the request as a "first render" of Inertia, giving us the HTML used in the first page render. We could change the backend to treat AJAX requests differently:

```diff
class ChatRoomsController extends Controller
{
  public function show(ChatRoom $chatRoom)
  {
    $messages = $chatRoom->messages()
      ->latest()
      ->with(['user'])
      ->paginate();
+
+   if (request()->wantsJson()) {
+     return $messages;
+   }
+
    return Inertia\Inertia::render('ChatRooms/Show', [
      'chatRoom' => $chatRoom,
      'messages' => $messages,
    ]);
  }
}
```

Now, if you try to load more messages again, it should work as expected. However, something smells here. Our `ChatRoomsController@show` action is returning messages instead of the `chatRoom` resource expected. Let's fix that.

## Creating a new Messages resource

We can create another route for the Chat Room's Messages, like:

```php
class ChatRoomMessagesController extends Controller
{
  public function index(ChatRoom $chatRoom)
  {
    return $chatRoom->messages()
      ->latest()
      ->with(['user'])
      ->paginate();
  }
}
```

And we can change our `loadMore` method to get more messages from this new endpoint instead of the current ChatRoom show:

```diff
<script>
  export default {
    methods: {
      loadMore () {
        if (this.loadingMore) return;

-       axios.get(`/chat-rooms/${this.chatRoom.id}?page=${this.pagination.current_page + 1}`)
+       axios.get(`/chat-rooms/${this.chatRoom.id}/messages?page=${this.pagination.current_page + 1}`)
            .then(({ data }) => {
              this.localMessages = [
                ...data.data,
                ...this.localMessages,
              ];

              this.pagination = data;
            })
            .finally(() => this.loadingMore = false);
      }
    },
  }
</script>
```

Now, we have a dedicated endpoint for the chat room's messages. I think I like that more. There's a bit of duplication here, though. Both actions know how to get paginated messages of a chat room. Since these were the only two places where this happens, I'm fine with it. Otherwise, we could create a query object or something like that and place this logic there.

I also simplified the query side a bit. In a chat, we would have the show the latest messages but in reverse order, so the very last message would appear at the bottom of the page, not at the top. We could solve it but reversing the collection inside our paginator, like this:

```php
use Illuminate\Pagination\LengthAwarePaginator;

$messages = tap($chatRoom->messages()
  ->latest()
  ->with(['user'])
  ->paginate(50), function (LengthAwarePaginator $paginator) {
    $paginator->setCollection(
      $paginator->getCollection()->reverse()->values()
    );
  });
```

And in this case, I think I would prefer to place it in a query object somewhere and/or call it from my `ChatRoom` model like this:

```php
$messages = $chatRoom->getPaginatedMessages();
```

Anyways, I wanted to keep the example simple. Another way to fix this would be to create a computed prop in the page component that sorts the messages by timestamp. Either way is fine by me.

## Conclusion

As you can see, I ended up using a simple `axios.get` and prepending the new messages to my `localMessages` state in the page component. This solution isn't perfect, though. If you change rooms and go back in history, you are still left with only the latest messages of the room (you lost all the pages that were loaded later). But it's a lot better than making via `Inertia.visit`, for this use case.

It got me thinking if there couldn't be a way to tell Inertia to "merge" props with the current props instead of replacing it. Something like this:

```js
this.$inertia.visit(
  '...',
  {
    preserveState: true,
    only: ['messages'],
    mergeProps: { messages: 'prepend' },
  }
)
```

This would allow us to keep the current page of messages from the first visit and merge new messages by prepending it. Could also be useful when we are creating a new message, something like:

```php
class ChatRoomMessagesController extends Controller
{
  public function store(ChatRoom $chatRoom)
  {
    $message = $chatRoom->createMessage(
      request()->user(),
      request()->input('message.content')
    );
    
    return Inertia\Inertia::appendProps([
      'messages' => [$message],
    ]);
  }
}
```

Which would add the new message to the end of the current list of messages in the component's props.

Also, it got me thinking if there shouldn't be a way to make inertia visits "transparently". And with that I mean without affecting the browser history (skip push state) and all that.

I don't know, maybe all this would make things more complicated. For now, I would say keep it simple and use local state + `axios.get` when you need something like Infinite Scrolling.

Anyways, I hope you enjoyed the ride.