<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 14.08.2015
 * Time: 23:53
 */

namespace Application\View\Helper;


use DateTime;
use Zend\Form\View\Helper\AbstractHelper;

class TimeAgoHelper extends AbstractHelper {
    public function __invoke(DateTime $dateTime)
    {
        $dateDiff = $dateTime->diff(new DateTime());

        if ($dateDiff->y >= 1) {
            if ($dateDiff->y == 1) {
                $diffText = 'Vor einem Jahr.';
            } else {
                $diffText = 'Vor ' . $dateDiff->y . ' Jahren';
            }
        } elseif ($dateDiff->m >= 1) {
            if ($dateDiff->m == 1) {
                $diffText = 'Vor einem Monat.';
            } else {
                $diffText = 'Vor ' . $dateDiff->m . ' Monaten';
            }
        } elseif ($dateDiff->d >= 1) {
            if ($dateDiff->d == 1) {
                $diffText = 'Gestern.';
            } elseif($dateDiff->d == 2) {
                $diffText = 'Vorgestern';
            } else {
                $diffText = 'Vor ' . $dateDiff->d . ' Tagen';
            }
        } elseif ($dateDiff->h >= 1) {

            if ($dateDiff->h == 1) {
                $diffText = 'Vor einer Stunde.';
            } else {
                $diffText = 'Vor ' . $dateDiff->h . ' Stunden';

            }
        } elseif ($dateDiff->i >= 10) {
            $diffText = 'Vor ' . $dateDiff->i . ' Minuten';
        } else {
            $diffText = 'Gerade eben.';
        }

        return $diffText;
    }
} 