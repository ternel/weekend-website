<?php

namespace App;

class Weekend
{
    private const array MAPPING_EMOTICONS_EMOJIS = [
        '\o/' => '🎉',
        '◄:•D' => '🥳',
        'B-)' => '😎',
        '¬‿¬' => '😏',
        ':(' => '😟',
        'X-(' => '😠',
    ];

    /**
     * Compute the main text with emojis.
     */
    public function getRichText(\DateTimeImmutable $now = new \DateTimeImmutable()): string
    {
        return strtr($this->getText($now), self::MAPPING_EMOTICONS_EMOJIS);
    }

    /**
     * Compute the main subtext with emojis.
     */
    public function getRichSubText(\DateTimeImmutable $now = new \DateTimeImmutable()): string
    {
        return strtr($this->getSubText($now), self::MAPPING_EMOTICONS_EMOJIS);
    }

    /**
     * Compute the main text.
     */
    public function getText(\DateTimeImmutable $now = new \DateTimeImmutable()): string
    {
        $msg = 'Non. ¯\_(ツ)_/¯'; // Default

        if ('April 1st' === $now->format('F jS')) {
            // April fool
            return "C'est le week-end ! \\o/";
        }

        if ('Friday' === $now->format('l')) {
            if ((int) $now->format('G') >= 18) {
                $msg = "C'est le week-end ! \\o/";
            } elseif ((int) $now->format('G') >= 16) {
                $msg = "Officiellement non, mais c'est comme si. ¬‿¬";
            } else {
                $msg = 'Presque, mais pas encore. :(';
            }
        } elseif ('Thursday' === $now->format('l') && ((int) $now->format('G') >= 14)) {
            $msg = 'Bientôt… B-)';
        } elseif ('Saturday' === $now->format('l')) {
            $msg = "C'est le week-end ! \\o/";
        } elseif ('Sunday' === $now->format('l')) {
            if ((int) $now->format('G') >= 21) {
                $msg = "C'est la fin… :(";
            } else {
                $msg = "C'est le week-end ! \\o/";
            }
        }

        return $msg;
    }

    /**
     * Compute the subtext.
     */
    public function getSubText(\DateTimeImmutable $now = new \DateTimeImmutable()): string
    {
        $msg = '';

        // Jour férié demain
        if (false !== $this->checkTomorrowNotWorkingDay($now)) {
            // Aujourd'hui c'est vendredi, donc demain Samedi
            if ('Friday' === $now->format('l')) {
                $msg = 'Et on perd un jour férié ce week-end. X-(';
            }
            // Aujourd'hui c'est samedi, donc demain Dimanche
            elseif ('Saturday' === $now->format('l')) {
                $msg = 'Et on perd un jour férié ce week-end. X-(';
            } else {
                $msg = 'Mais demain, on ne travaille pas ! B-)';
            }
        }

        // Jour férié aujourd'hui
        if (false !== $this->checkNotWorkingDay($now)) {
            if ('Friday' === $now->format('l')) {
                $msg = "En fait, si. C'est d'ores et déjà le week-end ! \\o/";
            } elseif ('Monday' === $now->format('l')) {
                $msg = "En fait, si. C'est toujours le week-end ! \\o/";
            } else {
                $msg = 'Mais on ne travaille pas ! B-)';
            }
        }

        return $msg;
    }

    public function isWeekend(\DateTimeImmutable $now = new \DateTimeImmutable()): bool
    {
        if ('April 1st' === $now->format('F jS')) {
            // April fool
            return true;
        }

        if ('Friday' === $now->format('l') && (int) $now->format('G') >= 18) {
            return true;
        }
        if ('Saturday' === $now->format('l') || 'Sunday' === $now->format('l')) {
            return true;
        }

        return false;
    }

    /**
     * Compute all holidays of the year.
     *
     * @return array<string, string> An array of holidays in the format 'd-m-Y'
     */
    private function getHolidays(\DateTimeImmutable $now, ?int $year = null): array
    {
        if (null === $year) {
            $year = (int) $now->format('Y');
        }

        // Everything can be compute from the easter date
        $easterDate = easter_date($year);
        $easterDay = (int) date('j', $easterDate);
        $easterMonth = (int) date('n', $easterDate);
        $easterYear = (int) date('Y', $easterDate);

        return [
            // These days have a fixed date
            'nouvelan' => date('d-m-Y', $this->mktime(0, 0, 0, 1, 1, $year)), // 1er janvier
            'fetetravail' => date('d-m-Y', $this->mktime(0, 0, 0, 5, 1, $year)), // Fête du travail
            'victoire' => date('d-m-Y', $this->mktime(0, 0, 0, 5, 8, $year)), // Victoire des alliés
            'fetenat' => date('d-m-Y', $this->mktime(0, 0, 0, 7, 14, $year)), // Fête nationale
            'assomption' => date('d-m-Y', $this->mktime(0, 0, 0, 8, 15, $year)), // Assomption
            'toussaint' => date('d-m-Y', $this->mktime(0, 0, 0, 11, 1, $year)), // Toussaint
            'armistice' => date('d-m-Y', $this->mktime(0, 0, 0, 11, 11, $year)), // Armistice
            'noel' => date('d-m-Y', $this->mktime(0, 0, 0, 12, 25, $year)), // Noël

            // These days have a date depending on easter
            'lundi' => date('d-m-Y', $this->mktime(0, 0, 0, $easterMonth, $easterDay + 1, $easterYear)), // Lundi de Pâques
            'ascension' => date('d-m-Y', $this->mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear)), // Ascension
            'pentecote' => date('d-m-Y', $this->mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear)), // Lundi de Pentecôte

            'nextnouvelan' => date('d-m-Y', $this->mktime(0, 0, 0, 1, 1, $year + 1)), // next 1er janvier
        ];
    }

    private function mktime(int $hour, int $minute, int $second, int $month, int $day, int $year): int
    {
        $time = mktime($hour, $minute, $second, $month, $day, $year);

        if (false === $time) {
            throw new \RuntimeException('Unable to compute the timestamp for the given date.');
        }

        return $time;
    }

    /**
     * Are we working today?
     */
    private function checkNotWorkingDay(\DateTimeImmutable $now): string|false
    {
        return array_search($now->format('d-m-Y'), $this->getHolidays($now), true);
    }

    /**
     * Are we working tomorrow?
     */
    private function checkTomorrowNotWorkingDay(\DateTimeImmutable $now): string|false
    {
        $tomorrow = $now->modify('+1 day')->format('d-m-Y');

        return array_search($tomorrow, $this->getHolidays($now), true);
    }
}
