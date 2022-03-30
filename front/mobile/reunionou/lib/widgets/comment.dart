import 'package:flutter/material.dart';
import 'package:reunionou/models/message.dart';
import 'package:intl/intl.dart';

class CommentWidget extends StatelessWidget {
  CommentWidget({
    Key? key,
    required this.message,
  }) : super(key: key);

  EventMessage message;

  @override
  Widget build(BuildContext context) {
    DateTime eventDate = DateTime.parse(message.created_at);
    final formatedDate = DateFormat("dd/MM/yyyy HH:mm").format(eventDate);

    return ListTile(
      title: Text(message.content),
      subtitle: Text(message.pseudo),
      leading: const Icon(
        Icons.person,
        color: Colors.deepPurple,
      ),
      trailing: Text(formatedDate),
    );
  }
}
