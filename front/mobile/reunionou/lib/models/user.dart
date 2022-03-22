class User {
  final String id;
  final String fullname;
  final String username;
  final String type;
  final String? email;
  final String? token;

//Constructor
  const User({
    required this.id,
    required this.fullname,
    required this.username,
    required this.type,
    this.email,
    this.token,
  });

  //From Json
  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      fullname: json['fullname'],
      username: json['username'],
      type: json['type'],
      token: json['token'],
      email: json['email'],
    );
  }

  User.fromMap(Map<String, dynamic> res)
      : id = res["id"],
        fullname = res['fullname'],
        type = res['type'],
        email = res['email'],
        username = res['username'],
        token = res['token'];

  //To string
  @override
  String toString() {
    return 'User{id: $id, fullname: $fullname, username: $username, type: $type, email: $email, token: $token}';
  }
}
