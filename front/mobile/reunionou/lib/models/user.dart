class User {
  final String id;
  final String fullname;
  final String email;
  final String username;
  final String token;

//Constructor
  const User({
    required this.id,
    required this.fullname,
    required this.email,
    required this.username,
    required this.token,
  });

  //From Json
  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      fullname: json['fullname'],
      email: json['email'],
      username: json['username'],
      token: json['token'],
    );
  }

  User.fromMap(Map<String, dynamic> res)
      : id = res["id"],
        fullname = res['fullname'],
        email = res['email'],
        username = res['username'],
        token = res['token'];

  //To string
  @override
  String toString() {
    return 'User{id: $id, fullname: $fullname, email: $email, username: $username, token: $token}';
  }
}
