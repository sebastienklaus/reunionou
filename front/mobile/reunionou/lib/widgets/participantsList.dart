import 'package:flutter/material.dart';
import 'package:reunionou/models/event.dart';
import 'package:reunionou/widgets/spacer.dart';

class ParticipantsList extends StatelessWidget {
  const ParticipantsList({
    Key? key,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        children: <Widget>[
          const SpacerWidget(
            space: 30,
          ),
          const Text(
            "Participants confirmé",
            style: TextStyle(
                fontSize: 25.0,
                color: Colors.green,
                letterSpacing: 2.0,
                fontWeight: FontWeight.w400),
          ),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(8),
              children: const <Widget>[
                Card(
                  child: ListTile(
                    title: Text(
                      "Anchor",
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SpacerWidget(
            space: 30,
          ),
          const Text(
            "Participants décliner",
            style: TextStyle(
                fontSize: 25.0,
                color: Colors.red,
                letterSpacing: 2.0,
                fontWeight: FontWeight.w400),
          ),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(8),
              children: const <Widget>[
                Card(
                  child: ListTile(
                    title: Text(
                      "Anchor",
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
