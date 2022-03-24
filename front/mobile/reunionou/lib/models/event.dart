class EventItem {
  const EventItem(
      {required this.id,
      required this.title,
      required this.hour,
      required this.date,
      required this.location});
  final String id;
  final String title;
  final String hour;
  final String date;
  final List location;
}
