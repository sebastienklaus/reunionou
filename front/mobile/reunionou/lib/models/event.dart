class EventItem {
  const EventItem({
    this.id,
    this.user_id,
    required this.title,
    required this.description,
    required this.location,
    required this.date,
    required this.hour,
    this.created_at,
    this.updated_at,
  });
  final String? id;
  final String? user_id;
  final String title;
  final String description;
  final List location;
  final String hour;
  final String date;
  final String? created_at;
  final String? updated_at;

//From map
  EventItem.fromMap(Map<String, dynamic> res)
      : id = res["id"],
        user_id = res['user_id'],
        title = res['title'],
        description = res['description'],
        location = res['location'],
        hour = res['hour'],
        date = res['date'],
        created_at = res['created_at'],
        updated_at = res['updated_at'];

  //To json
  String toJson() {
    return '{id: $id, user_id: $user_id, title: $title, description: $description, location: $location, hour: $hour, date: $date, created_at: $created_at, updated_at: $updated_at}';
  }
}
