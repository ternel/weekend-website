<?php

namespace App;

class Weekend
{
    private const MAPPING_EMOTICONS_EMOJIS = [
        '\o/'  => '🎉',
        '◄:•D' => '🥳',
        'B-)'  => '😎',
        '¬‿¬'  => '😏',
        ':('   => '😟',
        'X-('  => '😠',
    ];

    /**
     * Compute the main text with emojis
     */
    public function getRichText(): string
    {
        return strtr($this->getText(), self::MAPPING_EMOTICONS_EMOJIS);
    }

    /**
     * Compute the main subtext with emojis
     */
    public function getRichSubText(): string
    {
        return strtr($this->getSubText(), self::MAPPING_EMOTICONS_EMOJIS);
    }

    /**
     * Compute the main text
     */
    public function getText(): string
    {
        $msg = 'Non. ¯\_(ツ)_/¯'; // Default

        if ('April 1st' == date('F jS')) {
            // April fool
            return 'C\'est le week-end ! \o/';
        }

        if ('Friday' == date('l')) {
            if (date('G') >= 18) {
                $msg = 'C\'est le week-end ! \o/';
            } elseif (date('G') >= 16){
                $msg = 'Officiellement non, mais c\'est comme si. ¬‿¬';
            } else {
                $msg = 'Presque, mais pas encore. :(';
            }
        }
        elseif ('Thursday' == date('l') && (date('G') >= 14)) {
            $msg = 'Bientôt… B-)';
        }
        elseif ('Saturday' == date('l')) {
            $msg = 'C\'est le week-end ! \o/';
        }
        elseif ('Sunday' == date('l')) {
            if ((date('G') >= 21)) {
                $msg = 'C\'est la fin… :(';
            } else {
                $msg = 'C\'est le week-end ! \o/';
            }
        }

        return $msg;
    }

    /**
     * Compute the subtext
     */
    public function getSubText(): string
    {
        $msg = '';

        // Jour férié demain
        if (false !== $this->checkTomorrowNotWorkingDay()) {
            // Aujourd'hui c'est vendredi, donc demain Samedi
            if ('Friday' == date('l')) {
                $msg = "Et on perd un jour férié ce week-end. X-(";
            }
            // Aujourd'hui c'est samedi, donc demain Dimanche
            elseif ('Saturday' == date('l')) {
                $msg = "Et on perd un jour férié ce week-end. X-(";
            } else {
                $msg = "Mais demain, on ne travaille pas ! B-)";
            }
        }

        // Jour férié aujourd'hui
        if (false !== $this->checkNotWorkingDay()) {
            if ('Friday' == date('l')) {
                $msg = "En fait, si. C'est d’ores et déjà le week-end ! \o/";
            }
            elseif ('Monday' == date('l')) {
                $msg = "En fait, si. C'est toujours le week-end ! \o/";
            } else {
                $msg = "Mais on ne travaille pas ! B-)";
            }
        }

        return $msg;
    }

    public function isWeekend()
    {
        if ('April 1st' == date('F jS')) {
            // April fool
            return true;
        }

        if ('Friday' == date('l') && date('G') >= 18) {
            return true;
        }
        elseif ('Saturday' == date('l') || 'Sunday' == date('l')) {
            return true;
        }

        return false;
    }

    /**
     * Compute all holidays of the year
     *
     * @param int|null $year
     * @return array
     */
    private function getHolidays($year = null)
    {
        if ($year === null) {
            $year = intval(date('Y'));
        }

        // Everything can be compute from the easter date
        $easterDate  = easter_date($year);
        $easterDay   = date('j', $easterDate);
        $easterMonth = date('n', $easterDate);
        $easterYear  = date('Y', $easterDate);

        $holidays = array(
            // These days have a fixed date
            'nouvelan'    => date('d-m-Y', mktime(0, 0, 0, 1,  1,  $year)), // 1er janvier
            'fetetravail' => date('d-m-Y', mktime(0, 0, 0, 5,  1,  $year)), // Fête du travail
            'victoire'    => date('d-m-Y', mktime(0, 0, 0, 5,  8,  $year)), // Victoire des alliés
            'fetenat'     => date('d-m-Y', mktime(0, 0, 0, 7,  14, $year)), // Fête nationale
            'assomption'  => date('d-m-Y', mktime(0, 0, 0, 8,  15, $year)), // Assomption
            'toussaint'   => date('d-m-Y', mktime(0, 0, 0, 11, 1,  $year)), // Toussaint
            'armistice'   => date('d-m-Y', mktime(0, 0, 0, 11, 11, $year)), // Armistice
            'noel'        => date('d-m-Y', mktime(0, 0, 0, 12, 25, $year)), // Noël

            // These days have a date depending on easter
            'lundi'     => date('d-m-Y', mktime(0, 0, 0, $easterMonth, $easterDay + 1,    $easterYear)), // Lundi de Pâques
            'ascension' => date('d-m-Y', mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear)), // Ascension
            'pentecote' => date('d-m-Y', mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear)), // Lundi de Pentecôte

            'nextnouvelan' => date('d-m-Y', mktime(0, 0, 0, 1,  1,  $year+1)), // next 1er janvier
            //'test' => date('d-m-Y', time()), // TEST
        );

        //sort($holidays);

        return $holidays;
    }

    /**
     * Are we working today?
     *
     * @return mixed
     */
    private function checkNotWorkingDay()
    {
        return array_search(date('d-m-Y'), $this->getHolidays());
    }

    /**
     * Are we working tomorrow?
     *
     * @return mixed
     */
    private function checkTomorrowNotWorkingDay()
    {
        $tomorrow = date('d-m-Y', strtotime("+1day"));

        return array_search($tomorrow, $this->getHolidays());
    }
}
