class Member {
  String id;
  String? user_id;
  String event_id;
  String pseudo;
  int? status;
  String created_at;
  String updated_at;

//Constructor
  Member({
    required this.id,
    this.user_id,
    required this.event_id,
    required this.pseudo,
    this.status,
    required this.created_at,
    required this.updated_at,
  });

  //From Json
  factory Member.fromJson(Map<String, dynamic> json) {
    return Member(
      id: json['id'],
      user_id: json['user_id'],
      event_id: json['event_id'],
      pseudo: json['pseudo'],
      status: json['status'],
      created_at: json['created_at'],
      updated_at: json['updated_at'],
    );
  }

  Member.fromMap(Map<String, dynamic> res)
      : id = res["id"],
        user_id = res['user_id'],
        event_id = res['event_id'],
        pseudo = res['pseudo'],
        status = res['status'],
        created_at = res['created_at'],
        updated_at = res['updated_at'];

  //To string
  @override
  String toString() {
    return 'Member{id: $id, user_id: $user_id, event_id: $event_id, pseudo: $pseudo, status: $status, created_at: $created_at, updated_at: $updated_at}';
  }
}
