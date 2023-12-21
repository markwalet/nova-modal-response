# Nova Modal Response

This package aims to make it easier to respond with a custom modal when executing an action in [Laravel Nova](https://nova.laravel.com).

More info for this specific feature can be found in the [Nova Documentation](https://nova.laravel.com/docs/4.0/actions/defining-actions.html#custom-modal-responses).

This is a fork of https://github.com/markwalet/nova-modal-response. The initial purpose of the fork was to add the ability to specify the size of the modal.

## Installation

```shell
composer require markwalet/nova-modal-response
```

## Usage

```php
return Action::modal('modal-response', [
    'title' => 'Result in a model',
    'body' => 'This is way better than that small notification in the bottom right!',
]);
```

When you want to render raw html, you can use the `html` parameter instead:

```php
return Action::modal('modal-response', [
    'title' => 'Next steps',
    'html' => '<ul><li>Show this package to your friends</li><li>Contribute</li><li>???</li><li>Profit!</li></ul>',
]);
```

There is also a special mode for rendering code snippets. This will surround the body with a `<pre>` and `<code>` tag but still leave escaping enabled:

```php
return Action::modal('modal-response', [
    'title' => 'The JSON response we got back from the external API',
    'code' => json_encode($response->json(), JSON_PRETTY_PRINT),
]);
```

Specify the size of the modal using
```php
'size' => "7xl" //or whatever size you need
```
