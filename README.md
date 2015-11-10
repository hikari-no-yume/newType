# newType - Create an opaque type with a single line of code

`newType()` is a handy function for creating opaque types, that is, a type that merely wraps another type. It takes two parameters: the name of your new wrapper type, and the name of the type you want to wrap. You get a class with a constructor taking a value of the wrapped type, and an `->unbox()` method to get out the value of the wrapped type.

Require it with `composer require ajf/newtype` to use it. It's PHP 7-only, since PHP 7 is the first version of PHP with scalar type declarations. It would be possible to backport this, though.

An example of where you might use this:

```PHP
<?php

namespace JaneBlogges\WonderfulApp;

use function ajf\newType\newType;

// Makes the new opaque type!
newType(FilePath::class, 'string');

function moveFile(FilePath $sourcePath, FilePath $destinationPath): bool {
    return rename($sourcePath->unbox(), $destinationPath->unbox());
}

moveFile(new FilePath('foo'), new FilePath('bar'));
```

Use it for that extra bit of type safety!

If you're after a mere alias that doesn't require explicit conversion to and from, check out [PHP's built-in `class\_alias` function](http://php.net/class_alias) - though beware that only works with classes and not primitive types.

The name comes from [Haskell's `newtype` declaration](https://wiki.haskell.org/Newtype), which does the same thing:

```Haskell
newtype FilePath = FilePath String
```

It's similar to [Hack's `newtype` declaration](http://docs.hhvm.com/manual/en/hack.typealiasing.opaquetypealiasing.php) as well, although that works a little differently.
