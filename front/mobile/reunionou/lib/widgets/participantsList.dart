import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/models/event.dart';
import 'package:reunionou/widgets/spacer.dart';

import '../data/dataLoader.dart';
import '../models/member.dart';

class ParticipantsList extends StatelessWidget {
  const ParticipantsList({
    Key? key,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    Iterable<Member> confirmedParts =
        context.read<DataLoader>().getMemberByStatus("1");
    Iterable<Member> declinedParts =
        context.read<DataLoader>().getMemberByStatus("0");
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
            child: ListView.builder(
              itemCount: confirmedParts.length,
              itemBuilder: (context, index) {
                return Card(
                  child: ListTile(
                    title: Text(
                      confirmedParts.elementAt(index).pseudo.toString(),
                    ),
                  ),
                );
              },
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
            child: ListView.builder(
              itemCount: declinedParts.length,
              itemBuilder: (context, index) {
                return Card(
                  child: ListTile(
                    title: Text(
                      declinedParts.elementAt(index).pseudo.toString(),
                    ),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
