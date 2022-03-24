class EventItem {
  const EventItem(
      {required this.id,
      required this.title,
      required this.date,
      required this.hour,
      required this.description,
      required this.location});
  final String id;
  final String title;
  final String hour;
  final String date;
  final String description;
  final List location;
}
