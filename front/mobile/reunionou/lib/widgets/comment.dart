import 'package:flutter/material.dart';

class CommentWidget extends StatelessWidget {
  const CommentWidget({
    Key? key,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return const ListTile(
      title: Text("User"),
      subtitle: Text("Lorem ipsum"),
      leading: Icon(
        Icons.person,
        color: Colors.deepPurple,
      ),
      trailing: Text("22-03-2022 14:40"),
    );
  }
}
