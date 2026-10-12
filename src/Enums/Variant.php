<?php

namespace Markwalet\NovaModalResponse\Enums;

enum Variant: string
{
    case DEFAULT = 'default';
    case INFO = 'info';
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case DANGER = 'danger';
}
