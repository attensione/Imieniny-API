# Imieniny API

Proste API napisane w PHP, które zwraca imiona obchodzące imieniny dla wybranej daty. Domyślnie zwraca imieniny na dziś. Można też podać przesunięcie względem dzisiejszej daty albo konkretną datę.

## Użycie

```text
GET /api.php
Zwraca imieniny na dziś.
GET /api.php?day=2
Zwraca imieniny za dwa dni.
GET /api.php?day=-1
Zwraca imieniny z wczoraj.
GET /api.php?date=2026-11-05
Zwraca imieniny dla konkretnej daty. Parametr date przyjmuje format YYYY-MM-DD.

Jeśli podane są jednocześnie parametry date i day, parametr date ma pierwszeństwo. Parametr day przyjmuje wartości od -3 do 3.
Przykładowa odpowiedź
{
  "api": "Imieniny API",
  "data": "2026-11-05",
  "wybrano_przez": "date",
  "imiona": [
    "Sulpicjusz",
    "Ismena",
    "Gildas",
    "Waleriusz",
    "Zdzisław"
  ]
}
Wymagania
- PHP z rozszerzeniem cURL
- Dostęp do internetu
Źródło danych i atrybucja
Dane o imieninach są pobierane z polskiej Wikipedii i przetwarzane przez API.

Źródło: Wikipedia — Imieniny

Treści tekstowe Wikipedii są udostępniane na licencji Creative Commons Uznanie autorstwa – Na tych samych warunkach 4.0 Międzynarodowe (CC BY-SA 4.0). Dane zwracane przez API, które pochodzą z tych treści, należy dalej udostępniać zgodnie z warunkami tej licencji, z podaniem źródła i informacji o zmianach.

To niezależny projekt. Nie jest tworzony, zatwierdzony ani wspierany przez Wikimedia Foundation ani Wikipedię.
Licencja kodu
Kod źródłowy tego projektu jest udostępniany na licencji MIT. Pełny tekst licencji znajduje się w pliku LICENSE.

Licencja MIT dotyczy kodu projektu. Nie zastępuje ani nie zmienia warunków licencji dotyczących treści pochodzących z Wikipedii.
```
