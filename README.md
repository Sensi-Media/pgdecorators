# sensimedia/pgdecorators
Postgresql Ornament decorators

Adds support for Postgresql's built-in array and pgarray data types.

## Installation
```sh
composer require sensimedia/pgdecorators
```

## Usage
Type-hint the model properties as required. No further configuration should be
necessary. Note both decorators extend `ArrayObject` and can be used as such
after the decoration is done.

