import 'package:flutter/material.dart';
import 'package:reunionou/models/message.dart';

class CommentWidget extends StatelessWidget {
  CommentWidget({
    Key? key,
    required this.message,
  }) : super(key: key);

  EventMessage message;

  @override
  Widget build(BuildContext context) {
    return ListTile(
      title: Text(message.content),
      subtitle: Text(message.pseudo),
      leading: const Icon(
        Icons.person,
        color: Colors.deepPurple,
      ),
      trailing: Text(message.created_at),
    );
  }
}
