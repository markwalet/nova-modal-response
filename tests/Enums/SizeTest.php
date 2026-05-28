<?php

namespace Markwalet\NovaModalResponse\Tests\Enums;

use Markwalet\NovaModalResponse\Enums\Size;
use Markwalet\NovaModalResponse\Tests\TestCase;
use ValueError;

class SizeTest extends TestCase
{
    public function test_cases_have_lowercase_string_values(): void
    {
        $this->assertSame('small', Size::SMALL->value);
        $this->assertSame('medium', Size::MEDIUM->value);
        $this->assertSame('large', Size::LARGE->value);
    }

    public function test_from_coerces_known_strings(): void
    {
        $this->assertSame(Size::LARGE, Size::from('large'));
    }

    public function test_from_throws_on_unknown_string(): void
    {
        $this->expectException(ValueError::class);

        Size::from('huge');
    }
}
