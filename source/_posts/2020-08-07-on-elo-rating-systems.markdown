---
extends: _layouts.post
title: 'On Elo Rating Systems'
date:   2020-08-07
tags: elo ranking games php
section: content
---

![Elo Rating System at play in The Social Network movie](/assets/images/elo-rating-systems/social-network.jpeg)

You might remember this scene from the movie The Social Network. That formula indicates they used the [Elo Rating Systems](https://en.wikipedia.org/wiki/Elo_rating_system) in the "face match" app before Facebook was a thing. Zuckerberg and the others used it in an "evil" game context where we compare one person's appearance against another person's appearance, but that's not the point of this article.

Elo Rating Systems can be used when you have any kind of "match" between two players (or teams). If you have that kind of system at play, your first "naive" implementation might be a simpler scoring system like: winner gets 3 points; tie game each one gets 1 point; losses get nothing. That's very simplistic and doesn't work very well.

Let's say you are building a Tic-Tac-Toe gaming platform using that system. You have some players that have been using that score system for a very long time. Then, the World's best Tic-Tac-Toe player joins your platform. She's going to have a hard time getting to the top, and it's not because she's not good at the game, mostly because of how the system works. Let's say the current best player has a record of 900 wins, 800 losses, 900 ties. That's 3600 points. She would have to play 1200 matches and win them all to take the place of the current best player.

You might think this is ok, but.. think about it. What if, by random chance, she plays against the current best player and wins every time. She gets the same amount of points as she would get by playing against someone not very good at the game. That doesn't feel right.

To put it simply, Elo Rating Systems will take a few other things into consideration when you are calculating how much each player gets (or loses!) after a match based on each player's current score, their played matches, and/or whether or not there's "luck" at play.

There is a [PHP package](https://packagist.org/packages/zelenin/elo) we can use here, it can be as simple as:

```php
use Zelenin\Elo\Player;
use Zelenin\Elo\Match;

$player1 = new Player(1200);
$player2 = new Player(800);

$match = new Match($player1, $player2);
$match->setScore(1, 0)
    ->setK(32)
    ->count();

dump([
  'player-1' => $player1->getRating(),
  'player-2' => $player2->getRating(),
]);
```

In this match, we have one player with a current score of 1200 points and another with a score of 800 points. They are playing and the player with the lowest score wins, so their after-match score would be:

- **~1170.90** points to the player 1 (which previously had 1200 points);
- **~829.09** points to the player 2 (which previously had 800 points);

Now, consider another match where both players have relatively equal scores: player 1 has 700pts; and player 2 has 800pts. If the player 2 wins a match against player 1, the after-match score of them would be:

- **~688.48** points to player 1;
- **~811.51** points to player 2;

In the first match scenario, player 2 got more points than in the second match. That's because the first victory can be considered more difficult than the second one. Note that in both examples I'm using a fixed **k-factor** of 32, which is "fine", but there are more accurate ways to [calculate the best k-factor for a match](https://en.wikipedia.org/wiki/Elo_rating_system#Most_accurate_K-factor).

There are some issues with this rating system. As you can see, it can encourage players with high scores to not play that much (in order to not lose points) and sustain their position. To fix that, you need to consider bonuses based on activity. So if someone is sitting on a high score they either will eventually lose points or we can boost scores based on the player's activity. There are other issues as well, so check out the [Wikipedia](https://en.wikipedia.org/wiki/Elo_rating_system) page for more details on this.

## Conclusion

Whenever you have some rating system at play, consider using something like the Elo Rating Systems to compute scores. It creates a much more "fair" and fun environment for the competitors.