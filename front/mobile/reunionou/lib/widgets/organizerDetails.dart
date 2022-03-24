import 'package:flutter/material.dart';
import 'package:reunionou/models/event.dart';
import 'package:reunionou/widgets/spacer.dart';

class OrganizerDetails extends StatelessWidget {
  const OrganizerDetails({
    Key? key,
    required this.event,
  }) : super(key: key);
  final EventItem event;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        children: const [
          SpacerWidget(
            space: 30,
          ),
          Text(
            "Organisateur",
            style: TextStyle(
                fontSize: 25.0,
                color: Colors.blueGrey,
                letterSpacing: 2.0,
                fontWeight: FontWeight.w400),
          ),
          SpacerWidget(
            space: 30,
          ),
          Text(
            "malekbk (malek@gmail.com)",
            style: TextStyle(
                fontSize: 18.0,
                color: Colors.black45,
                letterSpacing: 2.0,
                fontWeight: FontWeight.w300),
          ),
        ],
      ),
    );
  }
}
