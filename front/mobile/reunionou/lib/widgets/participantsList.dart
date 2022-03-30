import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/data/dataLoader.dart';
import 'package:reunionou/models/event.dart';
import 'package:reunionou/models/member.dart';
import 'package:reunionou/models/user.dart';
import 'package:reunionou/widgets/spacer.dart';

class ParticipantsList extends StatelessWidget {
  ParticipantsList({
    Key? key,
    required this.creator_id,
  }) : super(key: key);

  String creator_id;

  @override
  Widget build(BuildContext context) {
    Iterable<Member> confirmedParts =
        context.read<DataLoader>().getMemberByStatus(1);
    Iterable<Member> declinedParts =
        context.read<DataLoader>().getMemberByStatus(0);
    User _user = context.read<DataLoader>().getUser();

    return Center(
      child: Column(
        children: <Widget>[
          const SpacerWidget(
            space: 30,
          ),
          const Text(
            "Participants confirmés",
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
                if (confirmedParts.elementAt(index).user_id != creator_id) {
                  return Card(
                    child: ListTile(
                      title: Text(
                        confirmedParts.elementAt(index).pseudo.toString(),
                      ),
                    ),
                  );
                } else {
                  return Container();
                }
              },
            ),
          ),
          const SpacerWidget(
            space: 30,
          ),
          const Text(
            "Participants déclinés",
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
