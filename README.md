# Nova Modal Response

This package aims to make it easier to respond with a custom modal when executing an action in [Laravel Nova](https://nova.laravel.com).

More info for this specific feature can be found in the [Nova Documentation](https://nova.laravel.com/docs/4.0/actions/defining-actions.html#custom-modal-responses).

## Installation

```shell
composer require --dev markwalet/nova-modal-response
```

## Usage

```php
return Action::modal('modal-response', [
    'title' => 'Preview Navision Payload',
    'body' => 'Lorem ipsum dolor sit amed',
]);
```
