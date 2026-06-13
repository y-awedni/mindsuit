<?php

namespace AppBundle\Service;

class FormatDate {

    public function formatDate($date) {
        if ($date === '') {
            return null;
        }
        $dd = substr($date, 0, 2);
        $mm = substr($date, 3, 2);
        $yy = substr($date, 6, 4);
        return $yy . "-" . $mm . "-" . $dd;
    }

}
