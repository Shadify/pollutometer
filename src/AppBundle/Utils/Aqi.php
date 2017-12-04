<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 11/30/17
 * Time: 11:27 AM
 *
 *
 */

Namespace AppBundle\Utils;

class Aqi {
    public function calculateAQI($gasName, $concentration, $table) {
        $bpLow = 1;
        $bpHi = 2;
        $bpLowIndex = 1;
        $bpHiIndex = 1;

        $arr = $table ->{$gasName} ->{'breakpoints'};
        foreach ($arr as $index => $value) {
            if ($value <= $concentration && $table->{$gasName}->{'breakpoints'}[$index + 1] >= $concentration) {
                $bpLow = $value;
                $bpLowIndex = $index;
            }

            if ($value >= $concentration && $table->{$gasName}->{'breakpoints'}[$index - 1] <= $concentration) {
                $bpHi = $value;
                $bpHiIndex = $index;
            }

        };



        $airQualityIndex = (($table->{$gasName}->{'aq'}[$bpHiIndex] - $table->{$gasName}->{'aq'}[$bpLowIndex]) / ($bpHi - $bpLow)) * ($concentration - $bpLow) + $table->{$gasName}->{'aq'}[$bpLowIndex];

        return $airQualityIndex;

    }
}

?>