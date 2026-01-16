<?php
$title = 'Время намаза';
?><!doctype html>
<html lang="ru">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=$title?></title>
</head>
<body>

<h3><?=$title?> API: <a href="https://namaztimes.kz/ru/dev" target="_blank">https://namaztimes.kz/ru/dev</a></h3>

<?php

function getApi($url) {
	$is_bot = preg_match(
		'~(Google|Yahoo|Rambler|Bot|Yandex|Spider|Snoopy|Crawler|Finder|Mail|curl)~i', 
		$_SERVER['HTTP_USER_AGENT']
	);
	$praytimes = !$is_bot ? json_decode(file_get_contents($url), true) : [];
	return $praytimes;
}

function getArray($url) {
	$array = getApi($url);
	uasort($array, function ($a, $b) {
		return mb_strtolower($a, 'UTF-8') <=> mb_strtolower($b, 'UTF-8');
	});
	return $array;
}

$selectedCountry = $_GET['country'] ?? 0;
$selectedState = $_GET['state'] ?? '';
$selectedCity = $_GET['city'] ?? 0;

// Country
$countries = getArray('https://namaztimes.kz/ru/api/country');
echo '<form method="get"><select name="country" onchange="resetCountry(this.form)">
<option value="">=== ничего не выбрано ===</option>';
foreach ($countries as $k => $v) {
	if ($k == 91) continue;
	$sel = $k == $selectedCountry ? ' selected' : '';
	echo "<option value='{$k}'{$sel}>{$v}</option>";
}
echo '</select>';

// State
if ($selectedCountry) {
	$states = getArray("https://namaztimes.kz/ru/api/states?id={$selectedCountry}");
	echo '<select name="state" onchange="resetState(this.form)">
	<option value="">=== ничего не выбрано ===</option>';
	foreach ($states as $k => $v) {
		$sel = urlencode($k) == $selectedState ? ' selected' : '';
		echo "<option value='" . urlencode($k) . "'{$sel}>{$v}</option>";
	}
	echo '</select>';
}

// City
if ($selectedState) {
	$cities = getArray("https://namaztimes.kz/ru/api/cities?id=$selectedState");
	echo '<select name="city" onchange="this.form.submit()">
	<option value="">=== ничего не выбрано ===</option>';
	foreach ($cities as $k => $v) {
		$sel = $k == $selectedCity ? ' selected' : '';
		echo "<option value='{$k}'{$sel}>{$v}</option>";
	}
	echo '</select>';
}
echo '</form>';

if ($selectedCity) {
	$praytimes = getApi("https://namaztimes.kz/api/praytimes?id={$selectedCity}");
	if ($praytimes) {
		echo "<br><b>Город:</b> {$praytimes['attributes']['CityName']}<br>
		<b>Календарь:</b> {$praytimes['date']}<br>
		<b>Календарь хиджры:</b> {$praytimes['islamic_date']}<br>
		<b>Часовой пояс:</b> {$praytimes['attributes']['TimeZone']}<hr>
		<b>Имсак:</b> {$praytimes['praytimes']['imsak']}<br>
		<b>Фаджр:</b> {$praytimes['praytimes']['bamdat']}<br>
		<b>Восход:</b> {$praytimes['praytimes']['kun']}<br>
		<b>Ишрак:</b> {$praytimes['praytimes']['ishraq']}<br>
		<b>Керахат:</b> {$praytimes['praytimes']['kerahat']}<br>
		<b>Зухр:</b> {$praytimes['praytimes']['besin']}<br>
		<b>Аср-и Ауваль:</b> {$praytimes['praytimes']['asriauual']}<br>
		<b>Аср:</b> {$praytimes['praytimes']['ekindi']}<br>
		<b>Исфирар:</b> {$praytimes['praytimes']['isfirar']}<br>
		<b>Магриб:</b> {$praytimes['praytimes']['aqsham']}<br>
		<b>Иштибак:</b> {$praytimes['praytimes']['ishtibaq']}<br>
		<b>Иша’а:</b> {$praytimes['praytimes']['quptan']}<br>
		<b>Иша-и Сани:</b> {$praytimes['praytimes']['ishaisani']}<hr>
		<b>Широта:</b> {$praytimes['attributes']['Latitude_1']}<br>
		<b>Долгота:</b> {$praytimes['attributes']['Longitude_1']}<br>
		<b>Угол Киблы:</b> {$praytimes['attributes']['QiblaDir']}<br>
		<b>MagnetDev:</b> {$praytimes['attributes']['MagnetDev']}<br>
		<b>Город ID:</b> {$praytimes['attributes']['ID']}<br>
		<b>Страна ID:</b> {$praytimes['attributes']['countryID']}<br>";
	}
}

?>

<script>
function resetCountry(form) {
    if (form.state) form.state.value = '';
    if (form.city)  form.city.value = '';
    form.submit();
}

function resetState(form) {
    if (form.city) form.city.value = '';
    form.submit();
}
</script>

</body>

</html>
