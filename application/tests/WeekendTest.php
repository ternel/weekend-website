<?php

namespace App\Tests;

use App\Weekend;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class WeekendTest extends TestCase
{
    private Weekend $weekend;

    protected function setUp(): void
    {
        $this->weekend = new Weekend();
    }

    // --- getText() ---

    #[DataProvider('provideGetTextCases')]
    public function testGetText(\DateTimeImmutable $now, string $expected): void
    {
        self::assertSame($expected, $this->weekend->getText($now));
    }

    public static function provideGetTextCases(): iterable
    {
        // 2025-01-10 = vendredi, 2025-01-09 = jeudi, 2025-01-11 = samedi, 2025-01-12 = dimanche, 2025-01-06 = lundi
        yield 'avril 1er (poisson)' => [new \DateTimeImmutable('2025-04-01 10:00'), "C'est le week-end ! \\o/"];
        yield 'vendredi >= 18h' => [new \DateTimeImmutable('2025-01-10 18:00'), "C'est le week-end ! \\o/"];
        yield 'vendredi 16h-17h59' => [new \DateTimeImmutable('2025-01-10 16:00'), "Officiellement non, mais c'est comme si. ¬‿¬"];
        yield 'vendredi < 16h' => [new \DateTimeImmutable('2025-01-10 10:00'), 'Presque, mais pas encore. :('];
        yield 'jeudi >= 14h' => [new \DateTimeImmutable('2025-01-09 14:00'), 'Bientôt… B-)'];
        yield 'jeudi < 14h' => [new \DateTimeImmutable('2025-01-09 10:00'), 'Non. ¯\_(ツ)_/¯'];
        yield 'samedi' => [new \DateTimeImmutable('2025-01-11 10:00'), "C'est le week-end ! \\o/"];
        yield 'dimanche < 21h' => [new \DateTimeImmutable('2025-01-12 18:00'), "C'est le week-end ! \\o/"];
        yield 'dimanche >= 21h' => [new \DateTimeImmutable('2025-01-12 22:00'), "C'est la fin… :("];
        yield 'lundi (défaut)' => [new \DateTimeImmutable('2025-01-06 10:00'), 'Non. ¯\_(ツ)_/¯'];
    }

    // --- getRichText() — vérifie la substitution des émoticons ---

    public function testGetRichTextReplacesEmoticons(): void
    {
        // vendredi 18h → getText() contient \o/ → doit devenir 🎉
        $now = new \DateTimeImmutable('2025-01-10 18:00');
        self::assertStringContainsString('🎉', $this->weekend->getRichText($now));
        self::assertStringNotContainsString('\o/', $this->weekend->getRichText($now));
    }

    public function testGetRichTextReplacesSmiling(): void
    {
        // jeudi 14h → "Bientôt… B-)" → doit devenir 😎
        $now = new \DateTimeImmutable('2025-01-09 14:00');
        self::assertStringContainsString('😎', $this->weekend->getRichText($now));
        self::assertStringNotContainsString('B-)', $this->weekend->getRichText($now));
    }

    // --- isWeekend() ---

    #[DataProvider('provideIsWeekendCases')]
    public function testIsWeekend(\DateTimeImmutable $now, bool $expected): void
    {
        self::assertSame($expected, $this->weekend->isWeekend($now));
    }

    public static function provideIsWeekendCases(): iterable
    {
        yield 'avril 1er (poisson)' => [new \DateTimeImmutable('2025-04-01 10:00'), true];
        yield 'vendredi >= 18h' => [new \DateTimeImmutable('2025-01-10 18:00'), true];
        yield 'vendredi < 18h' => [new \DateTimeImmutable('2025-01-10 17:00'), false];
        yield 'samedi' => [new \DateTimeImmutable('2025-01-11 10:00'), true];
        yield 'dimanche' => [new \DateTimeImmutable('2025-01-12 10:00'), true];
        yield 'lundi' => [new \DateTimeImmutable('2025-01-06 10:00'), false];
        yield 'mercredi' => [new \DateTimeImmutable('2025-01-08 10:00'), false];
    }

    // --- getSubText() ---

    public function testGetSubTextEmptyByDefault(): void
    {
        // Lundi ordinaire sans jour férié
        self::assertSame('', $this->weekend->getSubText(new \DateTimeImmutable('2025-01-06 10:00')));
    }

    public function testGetSubTextTomorrowHolidayOtherDay(): void
    {
        // 2025-11-10 = lundi, demain = 2025-11-11 = Armistice
        self::assertSame('Mais demain, on ne travaille pas ! B-)', $this->weekend->getSubText(new \DateTimeImmutable('2025-11-10 10:00')));
    }

    public function testGetSubTextTomorrowHolidayFriday(): void
    {
        // 2025-10-31 = vendredi, demain = 2025-11-01 = Toussaint (samedi)
        self::assertSame('Et on perd un jour férié ce week-end. X-(', $this->weekend->getSubText(new \DateTimeImmutable('2025-10-31 10:00')));
    }

    public function testGetSubTextTomorrowHolidaySaturday(): void
    {
        // 2026-10-31 = samedi, demain = 2026-11-01 = Toussaint (dimanche)
        self::assertSame('Et on perd un jour férié ce week-end. X-(', $this->weekend->getSubText(new \DateTimeImmutable('2026-10-31 10:00')));
    }

    public function testGetSubTextTodayHolidayMonday(): void
    {
        // 2025-07-14 = lundi = Fête nationale
        self::assertSame("En fait, si. C'est toujours le week-end ! \\o/", $this->weekend->getSubText(new \DateTimeImmutable('2025-07-14 10:00')));
    }

    public function testGetSubTextTodayHolidayFriday(): void
    {
        // 2025-08-15 = vendredi = Assomption
        self::assertSame("En fait, si. C'est d'ores et déjà le week-end ! \\o/", $this->weekend->getSubText(new \DateTimeImmutable('2025-08-15 10:00')));
    }

    public function testGetSubTextTodayHolidayOtherDay(): void
    {
        // 2025-01-01 = mercredi = Nouvel An
        self::assertSame('Mais on ne travaille pas ! B-)', $this->weekend->getSubText(new \DateTimeImmutable('2025-01-01 10:00')));
    }

    // --- getRichSubText() — vérifie la substitution des émoticons ---

    public function testGetRichSubTextReplacesEmoticons(): void
    {
        // 2025-11-10 : "Mais demain, on ne travaille pas ! B-)" → 😎
        $now = new \DateTimeImmutable('2025-11-10 10:00');
        self::assertStringContainsString('😎', $this->weekend->getRichSubText($now));
        self::assertStringNotContainsString('B-)', $this->weekend->getRichSubText($now));
    }
}
