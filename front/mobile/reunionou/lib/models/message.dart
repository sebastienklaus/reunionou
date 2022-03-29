class EventMessage {
  const EventMessage({
    this.id,
    required this.event_id,
    required this.member_id,
    required this.content,
    required this.pseudo,
    this.media,
    required this.created_at,
    this.updated_at,
  });
  final String? id;
  final String event_id;
  final String member_id;
  final String content;
  final String pseudo;
  final String? media;
  final String created_at;
  final String? updated_at;

//From map
  EventMessage.fromMap(Map<String, dynamic> res)
      : id = res["id"],
        event_id = res['event_id'],
        member_id = res['member_id'],
        content = res['content'],
        pseudo = res['pseudo'],
        media = res['media'],
        created_at = res['created_at'],
        updated_at = res['updated_at'];

  //To json
  String toJson() {
    return '{id: $id, event_id: $event_id, member_id: $member_id, content: $content, pseudo: $pseudo, created_at: $created_at, updated_at: $updated_at}';
  }
}
