# Markdown loaded from a file

This document is read from disk at serialize time and compiled to HTML by
`Str::markdown()`.

## Features

- GitHub-flavored markdown
- **Bold**, *italic*, and `inline code`
- [Links](https://nova.laravel.com)

```php
Block::markdown(file: workbench_path('resources/demo.md'));
```

> Blockquotes work too.

| Column | Value |
| ------ | ----- |
| Source | file  |
| Type   | html  |
