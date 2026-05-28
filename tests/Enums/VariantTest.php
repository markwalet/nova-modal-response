<?php

namespace Markwalet\NovaModalResponse\Tests\Enums;

use Markwalet\NovaModalResponse\Enums\Variant;
use Markwalet\NovaModalResponse\Tests\TestCase;
use ValueError;

class VariantTest extends TestCase
{
    public function test_cases_have_lowercase_string_values(): void
    {
        $this->assertSame('default', Variant::DEFAULT->value);
        $this->assertSame('info', Variant::INFO->value);
        $this->assertSame('success', Variant::SUCCESS->value);
        $this->assertSame('warning', Variant::WARNING->value);
        $this->assertSame('danger', Variant::DANGER->value);
    }

    public function test_from_coerces_known_strings(): void
    {
        $this->assertSame(Variant::SUCCESS, Variant::from('success'));
    }

    public function test_from_throws_on_unknown_string(): void
    {
        $this->expectException(ValueError::class);

        Variant::from('chartreuse');
    }
}
