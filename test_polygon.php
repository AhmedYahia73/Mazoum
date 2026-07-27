<?php

function isInsidePolygon(float $lat, float $lng, array $polygon): bool
{
    $count   = count($polygon);
    $inside  = false;
    $j       = $count - 1;

    for ($i = 0; $i < $count; $i++) {
        $xi = (float) $polygon[$i]['lat'];
        $yi = (float) $polygon[$i]['lng'];
        $xj = (float) $polygon[$j]['lat'];
        $yj = (float) $polygon[$j]['lng'];

        if ((($yi > $lng) !== ($yj > $lng)) &&
            ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi) + $xi)) {
            $inside = !$inside;
        }
        $j = $i;
    }

    return $inside;
}

$polygon = [
    [ "lat" => 30.69427231822187, "lng" => 29.54555606482953 ],
    [ "lat" => 27.89545262397229, "lng" => 24.223679211125077 ],
    [ "lat" => 22.16507047898729, "lng" => 26.508835461125077 ],
    [ "lat" => 21.512410247418885, "lng" => 36.00102296112508 ],
    [ "lat" => 27.73998530940237, "lng" => 35.64946046112508 ],
    [ "lat" => 32.00625620798214, "lng" => 32.66117921112508 ]
];

// Cairo
$lat = 30.0444;
$lng = 31.2357;

var_dump(isInsidePolygon($lat, $lng, $polygon));

?>
