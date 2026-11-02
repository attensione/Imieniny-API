<?php



function imionaZDniaParser(string $content, string $miesiac, int $dzien): array
{
	$miesiace = [
		1 => 'stycznia', 2 => 'lutego', 3 => 'marca',
		4 => 'kwietnia', 5 => 'maja', 6 => 'czerwca',
		7 => 'lipca', 8 => 'sierpnia', 9 => 'września',
		10 => 'października', 11 => 'listopada', 12 => 'grudnia',
	];
	
    // Znajdź sekcję, np. "== [[29 stycznia]] =="
    $naglowek = '/==\s*\[\[' . preg_quote((string) $dzien, '/') . '\s+'.$miesiace[$miesiac].'\]\]\s*==/u';

    if (!preg_match($naglowek, $content, $match, PREG_OFFSET_CAPTURE)) {
        return [];
    }

    $start = $match[0][1] + strlen($match[0][0]);
    $reszta = substr($content, $start);

    // Zakończ przed następnym nagłówkiem dnia
    $koniecSekcji = preg_split('/==\s*\[\[\d{1,2}\s+'.$miesiace[$miesiac].'\]\]\s*==/u', $reszta, 2);
    $sekcja = $koniecSekcji[0];

    // Wybierz nazwy widoczne w linkach: [[Strona|Widoczna nazwa]] → Widoczna nazwa
    preg_match_all('/\[\[([^\]]+)\]\]/u', $sekcja, $linki);

    return array_values(array_map(
        static function (string $link): string {
            $czesci = explode('|', $link, 2);
            return trim($czesci[1] ?? $czesci[0]);
        },
        $linki[1]
    ));
}




function pobierzImieniny(int $dzien = 15, int $miesiac = 1): array|null|string
{
    $miesiace = [
		1 => 'styczeń', 2 => 'luty', 3 => 'marzec',
		4 => 'kwiecień', 5 => 'maj', 6 => 'czerwiec',
		7 => 'lipiec', 8 => 'sierpień', 9 => 'wrzesień',
		10 => 'październik', 11 => 'listopad', 12 => 'grudzień',
	];

	if (!isset($miesiace[$miesiac])) {
		die('Nieprawidłowy miesiąc.');
	}

	$params = [
		'action' => 'query',
		'prop' => 'revisions',
		'titles' => 'Imieniny – ' . $miesiace[$miesiac],
		'rvprop' => 'content',
		'rvslots' => 'main',
		'format' => 'json',
		'formatversion' => 2,
	];

	$url = 'https://pl.wikipedia.org/w/api.php?' . http_build_query($params);

	$ch = curl_init($url);
	curl_setopt_array($ch, [
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_USERAGENT => 'ImieninyApp/1.0 (kontakt: twoj-email@example.com)',
		CURLOPT_TIMEOUT => 15,
	]);

	$response = curl_exec($ch);

	if ($response === false) {
		die('Błąd cURL: ' . curl_error($ch));
	}

	curl_close($ch);

	$data = json_decode($response, true);

	if (isset($data['error'])) {
		die('Błąd API: ' . $data['error']['info']);
	}
	
	$page = reset($data['query']['pages']) ?: null;

	$content = $page['revisions'][0]['slots']['main']['content'] ?? null;
	
	$imiona = imionaZDniaParser($content, $miesiac, $dzien);

	return $imiona;
}



function wyswietlImieniny() {
	date_default_timezone_set('Europe/Warsaw');
	header('Content-Type: application/json; charset=utf-8');

	$dzisiaj = new DateTimeImmutable('today');
	$data = $dzisiaj;
	$parametr = 'today';

	// Konkretna data: ?date=2026-11-05
	if (isset($_GET['date'])) {
		$date = DateTimeImmutable::createFromFormat('!Y-m-d', $_GET['date']);

		// Sprawdź format i czy data naprawdę istnieje, np. odrzuć 2026-02-30
		if (!$date || $date->format('Y-m-d') !== $_GET['date']) {
			http_response_code(400);
			echo json_encode([
				'api' => 'Imieniny API',
				'error' => 'Podaj poprawną datę w formacie YYYY-MM-DD',
			], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
			exit;
		}

		$data = $date;
		$parametr = 'date';
	}
	// Przesunięcie względem dzisiaj: ?day=2 lub ?day=-1
	elseif (isset($_GET['day'])) {
		$day = filter_var($_GET['day'], FILTER_VALIDATE_INT);

		if ($day === false || $day < -3 || $day > 3) {
			http_response_code(400);
			echo json_encode([
				'api' => 'Imieniny API',
				'error' => 'Parametr day musi być liczbą od -3 do 3',
			], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
			exit;
		}

		$data = $dzisiaj->modify(($day >= 0 ? '+' : '') . $day . ' days');
		$parametr = 'day';
	}

	$imiona = pobierzImieniny(
		(int) $data->format('j'),
		(int) $data->format('n')
	);

	echo json_encode([
		'api' => 'Imieniny API',
		'data' => $data->format('Y-m-d'),
		'wybrano_przez' => $parametr,
		'imiona' => $imiona,
	], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}



wyswietlImieniny();
