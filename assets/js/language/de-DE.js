if(!window.calendar_languages) {
	window.calendar_languages = {};
}
window.calendar_languages['de-DE'] = {
	error_noview:     'Kalender: Ansicht {0} nicht gefunden',
	error_dateformat: 'Kalender: Falsches Datumsformat {0}. Sollte entweder "now" oder "yyyy-mm-dd" sein',
	error_loadurl:    'Kalender: Event-URL nicht gesetzt.',
	error_where:      'Kalender: Falsche Navigationsrichtung {0}. Nur "next", "prev" oder "today" sind erlaubt',
	error_timedevide: 'Kalender: Parameter für die Zeiteinteilung muss ein Teiler von 60 sein. Beispielsweise 10, 15, 30',

	no_events_in_day: 'Keine Ereignisse an diesem Tag.',

	title_year:  '{0}',
	title_month: '{0} {1}',
	title_week:  '{0}. Kalenderwoche {1}',
	title_day:   '{0}, der {1}. {2} {3}',

	week:        'KW {0}',
	all_day:     'Ganztägig',
	time:        'Zeit',
	events:      'Ereignisse',
	before_time: 'Endet vor Zeitspanne',
	after_time:  'Beginnt nach Zeitspanne',

	m0:  'Januar',
	m1:  'Februar',
	m2:  'März',
	m3:  'April',
	m4:  'Mai',
	m5:  'Juni',
	m6:  'Juli',
	m7:  'August',
	m8:  'September',
	m9:  'Oktober',
	m10: 'November',
	m11: 'Dezember',

	ms0:  'Jan',
	ms1:  'Feb',
	ms2:  'Mär',
	ms3:  'Apr',
	ms4:  'Mai',
	ms5:  'Jun',
	ms6:  'Jul',
	ms7:  'Aug',
	ms8:  'Sep',
	ms9:  'Okt',
	ms10: 'Nov',
	ms11: 'Dez',

	d0: 'So<span class="d-none d-lg-inline">nntag</span>',
	d1: 'Mo<span class="d-none d-lg-inline">ntag</span>',
	d2: 'Di<span class="d-none d-lg-inline">enstag</span>',
	d3: 'Mi<span class="d-none d-lg-inline">ttwoch</span>',
	d4: 'Do<span class="d-none d-lg-inline">nnerstag</span>',
	d5: 'Fr<span class="d-none d-lg-inline">eitag</span>',
	d6: 'Sa<span class="d-none d-lg-inline">mstag</span>',

	first_day: 1,
	week_numbers_iso_8601: true,

	holidays: {
		'01-01':     'Neujahr',
		'06-01':     'Heilige Drei Könige',
		'easter-3':  'Gründonnerstag',
		'easter-2':  'Karfreitag',
		'easter':    'Ostersonntag',
		'easter+1':  'Ostermontag',
		'01-05':     'Tag der Arbeit',
		'easter+39': 'Himmelfahrt',
		'easter+49': 'Pfingstsonntag',
		'easter+50': 'Pfingstmontag',
		'15-08':     'Mariä Himmelfahrt',
		'03-10':     'Tag der Deutschen Einheit',
		'01-11':     'Allerheiligen',
		'25-12':     'Erster Weihnachtsfeiertag',
		'26-12':     'Zweiter Weihnachtsfeiertag'
	}
};
