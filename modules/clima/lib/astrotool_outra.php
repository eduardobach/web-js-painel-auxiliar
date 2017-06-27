<?php

/*
AstroTool é uma library PHP para cálculos envolvendo astros, em especial o sol e a lua. Suas posições e fases.
 
Baseado no trabalho: Vladimir Agafonkin's JavaScript library.
https://github.com/mourner/AstroTool

Cálculos solares baseados em: http://aa.quae.nl/en/reken/zonpositie.html
Cálculos lunares baseados em: http://aa.quae.nl/en/reken/hemelpositie.html

Cálculos de iluminação e parametros lunares baseados em:
Capítulo 48 de "Astronomical Algorithms" 2nd edition by Jean Meeus (Willmann-Bell, Richmond) 1998.
E na url: http://idlastro.gsfc.nasa.gov/ftp/pro/astro/mphase.pro 

Cálculos de posição da lua baseados em:
http://www.stargazing.net/kepler/moonrise.html
*/

// para facilitar a leitura de fórmulas
define('PI', M_PI);
define('rad', PI / 180);


// date/time constantes e conversões
define('daySec', 60 * 60 * 24);
define('J1970', 2440588);
define('J2000', 2451545);
// cálculos gerais para posição
define('e', rad * 23.4397); // obliquidade da Terra
define('J0', 0.0009);


function toJulian($date){ return $date->getTimestamp() / daySec - 0.5 + J1970; }
function fromJulian($j){
    if (!is_nan($j)) {
        $dt = new DateTime("@".round(($j + 0.5 - J1970) * daySec));
        $dt->setTimezone((new DateTime())->getTimezone());
        return $dt;
    }
}
function toDays($date){ return toJulian($date) - J2000; }

function rightAscension($l, $b){ return atan2(sin($l) * cos(e) - tan($b) * sin(e), cos($l)); }
function declination($l, $b){ return asin(sin($b) * cos(e) + cos($b) * sin(e) * sin($l)); }

function azimuth($H, $phi, $dec){ return atan2(sin($H), cos($H) * sin($phi) - tan($dec) * cos($phi)); }
function altitude($H, $phi, $dec){ return asin(sin($phi) * sin($dec) + cos($phi) * cos($dec) * cos($H)); }

function siderealTime($d, $lw){ return rad * (280.16 + 360.9856235 * $d) - $lw; }

// cálculo para o tempo solar
function julianCycle($d, $lw){ return round($d - J0 - $lw / (2 * PI)); }

function approxTransit($Ht, $lw, $n){ return J0 + ($Ht + $lw) / (2 * PI) + $n; }
function solarTransitJ($ds, $M, $L){ return J2000 + $ds + 0.0053 * sin($M) - 0.0069 * sin(2 * $L); }

function hourAngle($h, $phi, $d){ return acos((sin($h) - sin($phi) * sin($d)) / (cos($phi) * cos($d))); }

// returns set time for the given sun altitude
function getSetJ($h, $lw, $phi, $dec, $n, $M, $L) {
    $w = hourAngle($h, $phi, $dec);
    $a = approxTransit($w, $lw, $n);
    return solarTransitJ($a, $M, $L);
}

// gerar cálculos gerais do sol
function solarMeanAnomaly($d){ return rad * (357.5291 + 0.98560028 * $d); }
function eclipticLongitude($M){
    $C = rad * (1.9148 * sin($M) + 0.02 * sin(2 * $M) + 0.0003 * sin(3 * $M)); // equation of center
    $P = rad * 102.9372; // periélio da Terra
    return $M + $C + $P + PI;
}

function hoursLater($date, $h){
    $dt = clone $date;
    return $dt->add( new DateInterval('PT'.round($h*3600).'S') );
}

class DecRa{
    public $dec;
    public $ra;
    function __construct($d, $r){
        $this->dec = $d;
        $this->ra  = $r;
    }
}

class DecRaDist extends DecRa {
    public $dist;
    function __construct($d, $r, $dist) {
        parent::__construct($d, $r);
        $this->dist = $dist;
    }
}

class AzAlt{
    public $azimuth;
    public $altitude;
    function __construct($az, $alt){
        $this->azimuth = $az;
        $this->altitude = $alt;
    }
}

class AzAltDist extends AzAlt{
    public $dist;
    function __construct($az, $alt, $dist) {
        parent::__construct($az, $alt);
        $this->dist = $dist;
    }
}

function sunCoords($d){
    $M = solarMeanAnomaly($d);
    $L = eclipticLongitude($M);
    return new DecRa(
        declination($L, 0),
        rightAscension($L, 0)
    );
}

function moonCoords($d) { // Coordenadas eclípticas geocêntricas da lua
    $L = rad * (218.316 + 13.176396 * $d); // ecliptic longitude
    $M = rad * (134.963 + 13.064993 * $d); // mean anomaly
    $F = rad * (93.272 + 13.229350 * $d);  // mean distance

    $l  = $L + rad * 6.289 * sin($M); // longitude
    $b  = rad * 5.128 * sin($F);     // latitude
    $dt = 385001 - 20905 * cos($M);  // distance to the moon in km

    return new DecRaDist(
        declination($l, $b),
        rightAscension($l, $b),
        $dt
    );
}


class AstroTool {
    var $date;
    var $lat;
    var $lng;

    // configuração do tempo solar (angle, morning name, evening name)
    private $times = [
        [-0.833, 'nascerSol',       'porSol'   ], // 'sunrise',       'sunset'
        [  -0.3, 'nascerSolFim',    'porSolIni'], // 'sunriseEnd',    'sunsetStart'
        [    -6, 'amanhecer',     'crepúsculo' ], // 'dawn',          'dusk'
        [   -12, 'nauticoIni',     'nauticoFim'], // 'nauticalDawn',  'nauticalDusk'
        [   -18, 'noiteFim',          'noite'  ], // 'nightEnd',      'night'
        [     6, 'horaDouradaFim','horaDourada'] // 'goldenHourEnd', 'goldenHour'
    ];

    // adicionar tempo customizado na config
    private function addTime($angle, $riseName, $setName) {
        $this->times[] = [$angle, $riseName, $setName];
    }

    function __construct($date, $lat, $lng) {
        $this->date = $date;
        $this->lat  = $lat;
        $this->lng  = $lng;
    }

    // calcular a posição do sol por uma data e latitude/longitude
    function getSunPosition() {

        $lw  = rad * -$this->lng;
        $phi = rad * $this->lat;
        $d   = toDays($this->date);

        $c   = sunCoords($d);
        $H   = siderealTime($d, $lw) - $c->ra;

        return new AzAlt(
            azimuth($H, $phi, $c->dec),
            altitude($H, $phi, $c->dec)
        );
    }

    // calculates sun times for a given date and latitude/longitude
    function getSunTimes() {

        $lw = rad * -$this->lng;
        $phi = rad * $this->lat;

        $d = toDays($this->date);
        $n = julianCycle($d, $lw);
        $ds = approxTransit(0, $lw, $n);

        $M = solarMeanAnomaly($ds);
        $L = eclipticLongitude($M);
        $dec = declination($L, 0);

        $Jnoon = solarTransitJ($ds, $M, $L);

        $result = [
            'solarNoon'=> fromJulian($Jnoon),
            'nadir'    => fromJulian($Jnoon - 0.5)
        ];

        for ($i = 0, $len = count($this->times); $i < $len; $i += 1) {
            $time = $this->times[$i];

            $Jset = getSetJ($time[0] * rad, $lw, $phi, $dec, $n, $M, $L);
            $Jrise = $Jnoon - ($Jset - $Jnoon);

            $result[$time[1]] = fromJulian($Jrise);
            $result[$time[2]] = fromJulian($Jset);
        }

        return $result;
    }


    function getMoonPosition($date) {
        $lw  = rad * -$this->lng;
        $phi = rad * $this->lat;
        $d   = toDays($date);

        $c = moonCoords($d);
        $H = siderealTime($d, $lw) - $c->ra;
        $h = altitude($H, $phi, $c->dec);

        // altitude correction for refraction
        $h = $h + rad * 0.017 / tan($h + rad * 10.26 / ($h + rad * 5.10));

        return new AzAltDist(
            azimuth($H, $phi, $c->dec),
            $h,
            $c->dist
        );
    }


    function getMoonIllumination() {

        $d = toDays($this->date);
        $s = sunCoords($d);
        $m = moonCoords($d);

        $sdist = 149598000; // distance from Earth to Sun in km

        $phi = acos(sin($s->dec) * sin($m->dec) + cos($s->dec) * cos($m->dec) * cos($s->ra - $m->ra));
        $inc = atan2($sdist * sin($phi), $m->dist - $sdist * cos($phi));
        $angle = atan2(cos($s->dec) * sin($s->ra - $m->ra), sin($s->dec) * cos($m->dec) - cos($s->dec) * sin($m->dec) * cos($s->ra - $m->ra));

        return [
            'fraction' => (1 + cos($inc)) / 2,
            'phase'    => 0.5 + 0.5 * $inc * ($angle < 0 ? -1 : 1) / PI,
            'angle'    => $angle
        ];
    }

    function getMoonTimes($inUTC=false) {
        $t = clone $this->date;
        if ($inUTC) $t->setTimezone(new DateTimeZone('UTC'));

        $t->setTime(0, 0, 0);

        $hc = 0.133 * rad;
        $h0 = $this->getMoonPosition($t, $this->lat, $this->lng)->altitude - $hc;
        $rise = 0;
        $set = 0;

        // go in 2-hour chunks, each time seeing if a 3-point quadratic curve crosses zero (which means rise or set)
        for ($i = 1; $i <= 24; $i += 2) {
            $h1 = $this->getMoonPosition(hoursLater($t, $i), $this->lat, $this->lng)->altitude - $hc;
            $h2 = $this->getMoonPosition(hoursLater($t, $i + 1), $this->lat, $this->lng)->altitude - $hc;

            $a = ($h0 + $h2) / 2 - $h1;
            $b = ($h2 - $h0) / 2;
            $xe = -$b / (2 * $a);
            $ye = ($a * $xe + $b) * $xe + $h1;
            $d = $b * $b - 4 * $a * $h1;
            $roots = 0;

            if ($d >= 0) {
                $dx = sqrt($d) / (abs($a) * 2);
                $x1 = $xe - $dx;
                $x2 = $xe + $dx;
                if (abs($x1) <= 1) $roots++;
                if (abs($x2) <= 1) $roots++;
                if ($x1 < -1) $x1 = $x2;
            }

            if ($roots === 1) {
                if ($h0 < 0) $rise = $i + $x1;
                else $set = $i + $x1;

            } else if ($roots === 2) {
                $rise = $i + ($ye < 0 ? $x2 : $x1);
                $set = $i + ($ye < 0 ? $x1 : $x2);
            }

            if ($rise != 0 && $set != 0) break;

            $h0 = $h2;
        }

        $result = [];

        if ($rise != 0) $result['moonrise'] = hoursLater($t, $rise);
        if ($set != 0) $result['moonset'] = hoursLater($t, $set);

        if ($rise==0 && $set==0) $result[$ye > 0 ? 'alwaysUp' : 'alwaysDown'] = true;

        return $result;
    }
}

// tests
/*
$test = new AstroTool(new DateTime(), 48.85, 2.35);
print_r($test->getSunTimes());
print_r($test->getMoonIllumination());
print_r($test->getMoonTimes());
print_r(getMoonPosition(new DateTime(), 48.85, 2.35));
*/
?>
