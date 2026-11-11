// un evento di GET /api/events, con le date già convertite nell'ora del telefono
class Event {
  final int id;
  final String title;
  final String sport;
  final String competition;
  final String status;
  final String? location;
  final DateTime start;
  final DateTime? end;
  final List<String> broadcasters;

  Event({
    required this.id,
    required this.title,
    required this.sport,
    required this.competition,
    required this.status,
    required this.location,
    required this.start,
    required this.end,
    required this.broadcasters,
  });

  factory Event.fromJson(Map<String, dynamic> json) {
    return Event(
      id: json['id'] as int,
      title: json['title'] as String,
      sport: json['sport'] as String,
      competition: json['competition'] as String,
      status: json['status'] as String,
      location: json['location'] as String?,
      start: DateTime.parse(json['start_utc'] as String).toLocal(),
      end: json['end_utc'] == null
          ? null
          : DateTime.parse(json['end_utc'] as String).toLocal(),
      broadcasters: List<String>.from(json['broadcasters']),
    );
  }
}

// "14/03/2027 05:00"
String formattaData(DateTime d) {
  final giorno = d.day.toString().padLeft(2, '0');
  final mese = d.month.toString().padLeft(2, '0');
  final ore = d.hour.toString().padLeft(2, '0');
  final minuti = d.minute.toString().padLeft(2, '0');
  return '$giorno/$mese/${d.year} $ore:$minuti';
}
