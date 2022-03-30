import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/data/dataLoader.dart';
import 'package:reunionou/models/event.dart';
import 'package:reunionou/models/user.dart';
import 'package:reunionou/widgets/spacer.dart';

class OrganizerDetails extends StatelessWidget {
  const OrganizerDetails({
    Key? key,
    required this.event,
  }) : super(key: key);
  final EventItem event;

  @override
  Widget build(BuildContext context) {
    return FutureBuilder(
      future: context.read<DataLoader>().getUserById(event.user_id),
      builder: (BuildContext context, AsyncSnapshot<dynamic> snapshot) {
        if (!snapshot.hasData) {
          return const Center(
            child: CircularProgressIndicator(),
          );
        } else {
          if (snapshot.data is User) {
            return Center(
              child: Column(
                children: [
                  const SpacerWidget(
                    space: 30,
                  ),
                  const Text(
                    "Organisateur",
                    style: TextStyle(
                        fontSize: 25.0,
                        color: Colors.blueGrey,
                        letterSpacing: 2.0,
                        fontWeight: FontWeight.w400),
                  ),
                  const SpacerWidget(
                    space: 30,
                  ),
                  Text(
                    snapshot.data.fullname,
                    style: const TextStyle(
                        fontSize: 18.0,
                        color: Colors.black45,
                        letterSpacing: 2.0,
                        fontWeight: FontWeight.w300),
                  ),
                  const SpacerWidget(
                    space: 30,
                  ),
                  Text(
                    " ( " + snapshot.data.email + " )",
                    style: const TextStyle(
                        fontSize: 18.0,
                        color: Colors.black45,
                        letterSpacing: 2.0,
                        fontWeight: FontWeight.w300),
                  ),
                ],
              ),
            );
          } else {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(
                content:
                    Text("Quelque chose s'est mal passé essaie une autre fois"),
                backgroundColor: Colors.red,
              ),
            );
            return Container();
          }
        }
      },
    );
  }
}
