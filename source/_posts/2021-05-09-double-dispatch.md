---
extends: _layouts.post
title: 'Double Dispatch'
date:   2021-05-09
tags: patterns visitor double-dispatch
section: content
---

I have been reading the book "Smalltalk Best Pracice and Patterns", so I'm going to share some cool patterns in this blog. I shared this on Twitter:

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Some cool design patterns I&#39;ve learned recently:<br><br>- Method Object<br>- Double Dispatch (aka. Duet or Pas de Deux)<br>- Pluggable Behavior<br><br>🤓</p>&mdash; Tony Messias (@tonysmdev) <a href="https://twitter.com/tonysmdev/status/1391169860231704590?ref_src=twsrc%5Etfw">May 8, 2021</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

And [Freek Van der Herten](https://twitter.com/freekmurze) mentioned that I could cover them as blogposts. Here's is the first one. Well, techinically, the second one. See, I found another pattern that I like called "Method Object", but I have already covered it here in this blog on the post titled ["When Objects Are Not Enough"](https://www.tonysm.com/when-objects-are-not-enough/). Same idea. Which is cool. I've updated the post to add

The computation of a method call is only dependent on a single object at a time. This is enough most of the time. *Sometimes* we need the computation to also depend on the argument being passed to the method call.

Think you have two hierarchies of objects interacting with each other and the computation of these interactions depends on both objects, not on one of them.

We're going to TDD our way through this pattern. We're using [Pest](https://pestphp.com/) for the test case. Feel free to use whatever you want.

## Example: Adding Integers and Floats

Let's get to the first example: adding numbers. For this example, let's imagine we are building the base classes for numbers in a language and that our language is not able to sum primitives of the same type.

We'll start with the use case of summing integers:

```php
declare(strict_types = 1);

class IntegerNumber
{
  public function __construct(public int $value) {}

  public function add($number)
  {
  	return new IntegerNumber($this->value + $number->value);
  }
}

test('adds integers', function () {
  $first = new IntegerNumber(40);
  $second = new IntegerNumber(2);

  $this->assertSame(42, $first->add($second)->value);
});
```

That works. For now. Notice that we added a `declare(strict_types = 1);` to the PHP file. I did this because PHP is very smart and is able to sum ints and floats, so I wanted to force us to cast the values for the purpose of this example.

Let's add test for summing floats:

```php
class FloatNumber
{
  public function __construct(public float $value) {}

  public function add($number)
  {
    return new FloatNumber($this->value + $number->value);
  }
}

test('adds floats', function () {
  $first = new FloatNumber(40.0);
  $second = new FloatNumber(2.0);

  $this->assertSame(42.0, $first->add($second)->value);
});
```

Our tests should be green. So far, so good. Let's add our first cross-addition: adding integers and floats.

```php
test('adds integers and floats', function () {
  $first = new IntegerNumber(40);
  $second = new FloatNumber(2.0);

  $this->assertSame(42, $first->add($second)->value);
  $this->assertSame(42.0, $second->add($first)->value);
});
```

OK, how can we get that one working? The answer is: double dispatch. The pattern states the following:

> Send a message to the argument. Append the class name of the receiver to the selector. Pass the receiver as an argument. (Kent Beck in "Smalltalk Best Practice Patterns", pg. 56)

This was in Smalltalk. For us, the *selector* is the method name (close enough). Let's apply the pattern. First, let's handle our first use case adding integers:

```php
class IntegerNumber
{
  public function __construct(public int $value) {}

  public function add($number)
  {
    return $number->addInteger($this);
  }

  public function addInteger(IntegerNumber $number)
  {
  	return new IntegerNumber($this->value + $number->value);
  }
}
```

If we run the first test, it should still pass (the last test should still be broken). Now, let's apply the pattern to floats so we can add floats together:

```php
class FloatNumber
{
  public function __construct(public float $value) {}

  public function add($number)
  {
  	return $number->addFloat($this);
  }

  public function addFloat(FloatNumber $number)
  {
  	return new FloatNumber($this->value + $number->value);
  }
}
```

Our first two tests should be passing now. Nice! Let's now add the cross methods. First, an integer only knows how to add other integers (primitives). Similarly, floats should only know how to add their own primitives. However, integers can know how to convert themselves to floats and vice-versa. This will allow us to add floats and integers. Let's see how an integer handles adding floats:

```php
class IntegerNumber
{
    public function __construct(public int $value)
    {
    }

    public function add($number)
    {
        return $number->addInteger($this);
    }

    public function addInteger(IntegerNumber $number)
    {
        return new IntegerNumber($this->value + $number->value);
    }

    public function addFloat(FloatNumber $number)
    {
        return $number->addFloat($this->asFloat());
    }

    public function asFloat()
    {
        return new FloatNumber(floatval($this->value));
    }
}

class FloatNumber
{
    public function __construct(public float $value)
    {
    }

    public function add($number)
    {
        return $number->addFloat($this);
    }

    public function addFloat(FloatNumber $number)
    {
        return new FloatNumber($this->value + $number->value);
    }

    public function addInteger(IntegerNumber $number)
    {
        return $number->addInteger($this->asInteger());
    }

    public function asInteger()
    {
        return new IntegerNumber(intval($this->value));
    }
}
```

![Numbers Test Passing](/assets/images/double-dispatch/numbers-tests-passing.png)

It works! Nice. If you're like me, you're now delighted with such a sophisticated implementation.

## Example: Star Trek

OK, the numbers example was cool and all, but chances are we're not implementing a language. Is this even useful somewhere else? Well, the important thing about a pattern is the design, not the implementation. You can re-use this implementation in other contexts.

Let's say we're building a Star Trek game. We'll control a spaceship and there might be some enemies along the way, so they have to fight. Some enemies will be critical while others will not make any damage depending on the spaceship.

So we have two hierarchies at play: Spaceships and Enemies. And the computation of the combat depends on both of them. Perfect use case for the Double Dispatch pattern.

Let's start with a simple case: an asteroid and a space shuttle. The asteroid damages the shuttle, but not critically:

```php
test('asteroid damages shuttle', function () {
    $spaceship = new Shuttle(hitpoints: 100);
    $enemy = new Asteroid();

    $spaceship->fight($enemy);

    $this->assertEquals(90, $spaceship->hitpoints);
});
```

The implementation would be something like this:

```php
class Shuttle
{
    public function __construct(public int $hitpoints)
    {
    }

    public function fight($enemy)
    {
        $this->hitpoints -= $enemy->damage();
    }
}

class Asteroid
{
    public function damage()
    {
        return 10;
    }
}
```

The test should be green. Nice. Let's add another spaceship. The USS Voyager should not receive any damage from an Asteroid.

```php
test('asteroid does not damage uss voyager', function () {
	$spaceship = new UssVoyager(hitpoints: $initialHitpoints = 100);
	$enemy = new Asteroid();

	$spaceship->fight($enemy);

	$this->assertSame($initialHitpoints, $spaceship->hitpoints);
});
```

Let's implement our new spaceship:

```php
class UssVoyager
{
    public function __construct(public int $hitpoints)
    {
    }

    public function fight($enemy)
    {
        // Nothing happens.
    }
}
```

Our tests should be green now. Uhm... it looks weird, right? Let's add another enemy and see if it this design still works. Our new enemy is a Borg Cube. Borgs will assimilate any spaceship (resistance is futile.)

Let's start with a test for the Shuttle facing the Borg Cube:

```php
test('borg cube critically damages the shuttle', function () {
    $spaceship = new Shuttle(hitpoints: 100);
    $enemy = new BorgCube();

    $spaceship->fight($enemy);

    $this->assertSame(0, $spaceship->hitpoints);
});
```

Let's implement the Bug Cube enemy:

```php
class BorgCube
{
    public function damage()
    {
        return 100;
    }
}
```

OK, our test should be green. Let's add another test before we refactor this. Borgs will also assimilate the USS Voyager:

```php
test('borg cube critically damages the uss voyager', function () {
    $spaceship = new UssVoyager(hitpoints: 100);
    $enemy = new BorgCube();

    $spaceship->fight($enemy);

    $this->assertSame(0, $spaceship->hitpoints);
});
```

And... red. Tests are failing. That's because so far nothing damaged the USS Voyager. I think it's time to apply the pattern. First, let's send a message to the enemy, append the spaceship name to the message and pass it along as an argument:

```php
class Shuttle
{
  public function __construct(public int $hitpoints) {}

  public function fight($enemy)
  {
    $enemy->fightShuttle($this);
  }
}

class UssVoyager
{
  public function __construct(public int $hitpoints) {}

  public function fight($enemy)
  {
    $enemy->fightUssVoyager($this);
  }
}

class Asteroid
{
  public function fightShuttle(Shuttle $shuttle)
  {
    $shuttle->hitpoints -= 10;
  }

  public function fightUssVoyager(UssVoyager $ussVoyager)
  {
    // Does nothing...
  }
}

class BorgCube
{
  public function fightShuttle(Shuttle $shuttle)
  {
    $shuttle->hitpoints = 0;
  }

  public function fightUssVoyager(UssVoyager $ussVoyager)
  {
    $ussVoyager->hitpoints = 0;
  }
}
```

If we extract an Enemy interface here, we would have something like this:

```php
interface Enemy
{
  public function fightShuttle(Shuttle $shuttle);
  public function fightUssVoyager(UssVoyager $ussVoyager);
}
```

If we add a new enemy to the system, we know we only have to implement the enemy interface and it should *Just Work*™. Adding a new spaceship? We also need to add it to the enemy interface.

## Conclusion

This is not always flowers and sunshine, though. There is a bunch of indirection at play here. But I think that's OK in this case. The alternative would involve a couple if or switch statements around, so I think it's worth it.

You might think this is similar to the Visitor Pattern, and that's true. The Visitor Pattern solves the problem when Double Dispatch cannot be used (see [the Wikipedia for Double Dispatch](https://en.wikipedia.org/wiki/Double_dispatch).) Also make sure to check out [this video](https://www.youtube.com/watch?v=TeZqKnC2gvA) on the subject.

I had fun writing this piece. And I'm having a lot of fun reading the book. Let me know what you think.
