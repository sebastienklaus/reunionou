class EventItem {
  const EventItem({
    required this.id,
    required this.title,
    required this.description,
    required this.location,
    required this.user_id,
    required this.date,
    required this.hour,
    this.created_at,
    this.updated_at,
  });
  final String id;
  final String title;
  final String description;
  final List location;
  final String user_id;
  final String hour;
  final String date;
  final String? created_at;
  final String? updated_at;
}
