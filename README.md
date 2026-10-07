<p align="center">
  <a href="https://pollora.dev">
    <img src="https://raw.githubusercontent.com/Pollora/.github/main/brand/banners/WordPressArguments.png" width="100%" alt="WordPress Arguments: WordPress argument arrays built from objects">
  </a>
</p>

<p align="center">
  <a href="https://packagist.org/packages/pollora/wordpress-args"><img src="https://img.shields.io/packagist/v/pollora/wordpress-args" alt="Latest version"></a>
  <a href="https://packagist.org/packages/pollora/wordpress-args"><img src="https://img.shields.io/packagist/dt/pollora/wordpress-args" alt="Total downloads"></a>
  <a href="https://github.com/Pollora/WordPressArguments/actions/workflows/tests.yml"><img src="https://github.com/Pollora/WordPressArguments/actions/workflows/tests.yml/badge.svg" alt="Tests"></a>
  <a href="LICENSE"><img src="https://img.shields.io/github/license/Pollora/WordPressArguments" alt="License"></a>
</p>

WordPress Arguments turns an object into the arguments array that WordPress functions expect (`register_post_type()`, `register_taxonomy()`, `WP_Query`…). Add the `ArgumentHelper` trait to a class, give it one property per argument, and it builds the array for you: property names become snake_case keys, unset properties are left out, and raw arguments can be merged on top. It is for package authors who want a fluent, typed API over WordPress arguments instead of hand-written arrays.

> Part of [Pollora](https://pollora.dev), the Laravel framework for WordPress. In a Pollora project it is already installed, as the base of [pollora/query](https://github.com/Pollora/Query) and [pollora/entity](https://github.com/Pollora/WordPressEntity).

## Installation

```bash
composer require pollora/wordpress-args
```

Requires PHP 8.2+ and `illuminate/support` 12 or 13. Building the arguments calls WordPress's `wp_parse_args()`, so it runs inside WordPress.

## Quick start

```php
use Pollora\WordPressArgs\ArgumentHelper;

class BookPostType
{
    use ArgumentHelper;

    private ?bool $public = null;

    private ?bool $showInRest = null;

    private ?string $menuIcon = null;

    public function public(): self
    {
        $this->public = true;

        return $this;
    }

    public function showInRest(): self
    {
        $this->showInRest = true;

        return $this;
    }

    public function menuIcon(string $icon): self
    {
        $this->menuIcon = $icon;

        return $this;
    }

    public function getArgs(): array
    {
        return $this->buildArguments();
    }
}

$args = (new BookPostType)
    ->public()
    ->showInRest()
    ->setRawArgs(['has_archive' => 'books'])
    ->getArgs();

// [
//     'public' => true,
//     'show_in_rest' => true,
//     'has_archive' => 'books',
// ]

register_post_type('book', $args);
```

## How it works

`ArgumentHelper` reads the properties declared on the class (all visibilities):

- each property name is converted to snake_case with Laravel's `Str::snake()`: `showInRest` becomes `show_in_rest`;
- properties whose value is `null` are skipped, so WordPress keeps its own defaults for them;
- raw arguments set with `setRawArgs()` are merged last, with `wp_parse_args()`, and win over the properties of the same name.

### Methods

| Method | Visibility | Returns |
|:-------|:-----------|:--------|
| `extractArgumentFromProperties()` | public | The arguments built from the properties only. |
| `setRawArgs(array $rawArgs)` | public | `$this`. Stores arguments to merge on top. |
| `getRawArgs()` | public | The raw arguments, or `null`. |
| `buildArguments()` | protected | The properties' arguments merged with the raw ones. Expose it from your class (for example as `getArgs()`). |

`rawArgs` is the trait's own property and is never turned into an argument.

### In the wild

- [pollora/entity](https://github.com/Pollora/WordPressEntity): its `Entity` base class uses the trait, and `getArgs()` builds the arguments passed to post type and taxonomy registration.
- [pollora/query](https://github.com/Pollora/Query): `PostQuery` uses it to build the `WP_Query` arguments returned by `getArguments()`.

## Testing

```bash
vendor/bin/pest
```

## Contributing

Contributions are welcome: see the [contributing guide](https://github.com/Pollora/.github/blob/main/CONTRIBUTING.md). Report security issues privately, as described in the [security policy](https://github.com/Pollora/.github/blob/main/SECURITY.md).

## License

WordPress Arguments is open-source software licensed under the [MIT license](LICENSE). © [RuBee group](https://rubee.group)
