---
extends: _layouts.post
title: 'Reddit''s "hotness" algorithm'
date:   2020-08-07
tags: reddit algorithm laravel eloquent
section: content
excerpt: Reddit's "hotness" Algorithm can be used in web applications to generate the feeling of "trends" in many different scenarios. Here's how to implement it in Laravel with Eloquent and a Relational Database.
---

I long time ago I was working on a location-based social network. We basically divided the World into groups according to the demographics of the region (crowded places would have more, smaller groups). Inside a group, you have many different "topics" (that's not what we called all this, it's just an example so you get a general idea).

These topics would be like chat-rooms. We sorted the topics based on the number of messages being exchanged in them. So the most-chatted topics would appear on top. But there is a problem with this approach. If a topic arises and is heated up, there are chances the topic would cool down after a while and get "stale" but always on the top. To fix that, we implemented the Reddit "hotness" algorithm based on the messages count.

There is a [great write-up](restfulmvc.com/reddit-algorithm.shtml) full of details on how the algorithm works, but the general idea is that we should implement a sorting rule that takes the number of messages in the topic and when the topic was created. This way, new messages will give a topic a boost, but eventually, new topics will be "hot" no matter the number of messages a topic has.

To do that in SQL, we would have something like this:

```sql
SELECT topics.*, LOG10(messages_count + 1) * 287015 + UNIX_TIMESTAMP(topics.created_at) AS topic_hotness
FROM topics
ORDER BY topic_hotness DESC
```

If you using Eloquent, you could write this query like this:

```php
App\Topic::query()
  ->orderByDesc(
    DB::raw('LOG10(messages_count + 1) * 287015 + UNIX_TIMESTAMP(created_at)')
  )
  ->get();
```

In this case, I have a cached value called `messages_count` which is incremented every time a new message is sent to that topic. I could use sub-queries here, I guess. Not sure about performance, though. The `created_at` field is stored as a Unix timestamp. In the app I mentioned, I think we used stored-procedures and triggers to update a score field (I don't have the code anymore to look back at it). Not something I would do these days, to be honest.

Check the article I linked above for a very detailed explanation of the problem and this solution. With a random dataset, this query would generate the following JSON payload:

```json
[
  {
    "id": 2,
    "title": "ab reprehenderit ipsa",
    "messages_count": 100,
    "created_at": "2020-08-05T20:32:00.000000Z"
  },
  {
    "id": 3,
    "title": "laborum quis qui",
    "messages_count": 500,
    "created_at": "2020-08-02T20:32:00.000000Z"
  },
  {
    "id": 1,
    "title": "odit est consectetur",
    "messages_count": 10,
    "created_at": "2020-08-07T20:32:00.000000Z"
  }
]
```

There are other more advanced algorithms at play in Reddit related to ranking, which are based on up/down votes (see [here](https://medium.com/hacking-and-gonzo/how-reddit-ranking-algorithms-work-ef111e33d0d9) and also [here](https://www.evanmiller.org/how-not-to-sort-by-average-rating.html) - this one even contains SQL and Excel versions of the ranking so you can toy around with spreadsheets if that's your thing).

For reference, here's what the results would be to sort by date:

```json
[
  {
    "id": 1,
    "title": "odit est consectetur",
    "messages_count": 10,
    "created_at": "2020-08-07T20:32:00.000000Z"
  },
  {
    "id": 2,
    "title": "ab reprehenderit ipsa",
    "messages_count": 100,
    "created_at": "2020-08-05T20:32:00.000000Z"
  },
  {
    "id": 3,
    "title": "laborum quis qui",
    "messages_count": 500,
    "created_at": "2020-08-02T20:32:00.000000Z"
  }
]
```

And here's what it would look like if we sorted it by `messages_count`:

```json
[
  {
    "id": 3,
    "title": "laborum quis qui",
    "messages_count": 500,
    "created_at": "2020-08-02T20:32:00.000000Z"
  },
  {
    "id": 2,
    "title": "ab reprehenderit ipsa",
    "messages_count": 100,
    "created_at": "2020-08-05T20:32:00.000000Z"
  },
  {
    "id": 1,
    "title": "odit est consectetur",
    "messages_count": 10,
    "created_at": "2020-08-07T20:32:00.000000Z"
  }
]
```

## Conclusion

These algorithms can be used to create more "relevant" ways to consume the data in your application. We can adapt it to our needs, depending on what our data looks like.

## Bonus

You can wrap the hotness algorithm behind a query scope and add that dynamic field to the query when you need it, like:

```php
App\Topic::query()
  ->withHotnessScore()
  ->get()
```

Where the query scope would be something like:

```php
class Topic extends Model
{
    public function scopeWithHotnessScore(Builder $query, array $columns = ['topics.*'])
    {
        $query
            ->select(array_merge($columns, [
                DB::raw('LOG10(topics.messages_count + 1) * 287015 + UNIX_TIMESTAMP(topics.created_at) as hotness_score'),
            ]))
            ->reorder('hotness_score', 'DESC');

    }
}
```

If you know a more elegant way of adding this dynamic field to an Eloquent query, let me know.
