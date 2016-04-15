<?php

namespace Ternel;

class Weekend
{
    /**
     * Compute the main text
     *
     * @return string
     */
    public function getText()
    {
        $msg = '';
        // April fool
        if (1 == date('j') && 4 == date('n')) {
            return 'C\'est le week-end ! \o/';
        }

        // Vendredi
        if (5 == date('w')) {
            if (date('G') >= 18) {
                $msg = 'C\'est le week-end ! \o/';
            } elseif (date('G') >= 16){
                $msg = 'Officiellement non, mais c\'est comme si.';
            } else {
                $msg = 'Presque, mais pas encore. :( ';
            }
        }
        // Jeudi aprem
        elseif (4 == date('w') && (date('G') >= 14)) {
            $msg = 'Bientôt...';
        }
        // Samedi
        elseif (6 == date('w')) {
            $msg = 'C\'est le week-end ! \o/';
        }
        // Dimanche
        elseif (0 == date('w')) {
            if ((date('G') >= 21)) {
                $msg = 'C\'est la fin... :(';
            } else {
                $msg = 'C\'est le week-end ! \o/';
            }
        }
        // Semaine
        else {
            $msg = 'Non.';
        }

        return $msg;
    }



    /**
     * Compute the subtext
     *
     * @return string
     */
    public function getSubText()
    {
        $msg = '';

        // Jour férié demain
        if (false !== $this->checkTomorrowNotWorkingDay()) {
            // Aujourd'hui c'est vendredi, donc demain Samedi
            if (5 == date('w')) {
                $msg = "Et on perd un jour férié ce week-end :(";
            }
            // Aujourd'hui c'est samedi, donc demain Dimanche
            elseif (6 == date('w')) {
                $msg = "Et on perd un jour férié ce week-end :(";
            } else {
                $msg = "Mais demain, on ne travaille pas \o/";
            }
        }

        // Jour férié aujourd'hui
        if (false !== $this->checkNotWorkingDay()) {
            // Vendredi
            if (5 == date('w')) {
                $msg = "En fait, si. C'est d’ores et déjà le week-end \o/";
            }
            // Lundi
            elseif (1 == date('w')) {
                $msg = "En fait, si. C'est toujours le week-end \o/";
            } else {
                $msg = "Mais on ne travaille pas \o/";
            }
        }

        return $msg;
    }

    /**
     * Compute all holiday of the year
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
            'lundi'     => date('d-m-Y', mktime(0, 0, 0, $easterMonth, $easterDay + 1,    $easterYear)), // Lundi de pâcques
            'ascension' => date('d-m-Y', mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear)), // Ascension
            'pentecote' => date('d-m-Y', mktime(0, 0, 0, $easterMonth, $easterDay + 49, $easterYear)), // Pentecôte

            'nextnouvelan' => date('d-m-Y', mktime(0, 0, 0, 1,  1,  $year+1)), // next 1er janvier
            //'test' => date('d-m-Y', time()), // TEST
        );

        if($this->isAlsacianHolidaysDate($easterDate, $year)){
            $holidays = array_merge($holidays, $this->addAlsacianHolidays($easterDate, $year));
        }

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

    /**
     * Avoid calling ip file every day & every hit
     *
     * @param $easterDate
     * @param $year
     * @return bool
     */
    private function isAlsacianHolidaysDate($easterDate, $year)
    {
        //3 days before easter, can't $easterDay - 3 if easter is the first day of month
         return date('d-m-Y') == date('d-m-Y', mktime(0, 0, 0, 12, 26, $year)) ||
            date('d-m-Y') == date('d-m-Y', mktime($easterDate - (3600 * 3 * 24)));
    }

    /**
     * We call ipinfo if we are on alsacian holidays
     *
     * @param $easterDate
     * @param $year
     * @return array
     */
    private function addAlsacianHolidays($easterDate, $year)
    {
        //3 days before easter, can't $easterDay - 3 if easter is the first day of month
        $alsacianHolidays = [
            'vendredisaint' => date('d-m-Y', mktime($easterDate - (3600 * 3 * 24))),
            'saintetienne' => date('d-m-Y', mktime(0, 0, 0, 12, 26, $year))
        ];

        //fast ip
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        //we allow only 3 districts
        $districsAllowed = [
            '67',
            '68',
            '90'
        ];

        //yolo json
        $json = json_decode(file_get_contents('http://ipinfo.io/' . $ip . '/json'));

        foreach($districsAllowed as $district){
            if(substr((int)$json->postal, 0, 2) == $district){
                return $alsacianHolidays;
            }
        }

        //nicht im Elsass
        return [];
    }
}
