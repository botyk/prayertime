<?

require 'PrayTime.php';
$pt = new PrayTime();

echo '<pre>';

$id = 4; // ID город
$full = true;
$year = 2026;
$month = 2; // Февраль

//print_r($pt->getCities());
//print_r($pt->getCities(false, $full));
//print_r($pt->getCities($id));
//print_r($pt->getCities($id, $full));

//print_r($pt->getYear($id));
//print_r($pt->getYear($id, $year));
//print_r($pt->getMonth($id));
//print_r($pt->getMonth($id, $month, $year));

print_r($pt->getDay($id));

