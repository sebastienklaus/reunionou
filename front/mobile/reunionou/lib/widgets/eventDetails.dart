import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/models/event.dart';
import 'package:reunionou/widgets/spacer.dart';
import 'package:url_launcher/url_launcher.dart';
import '../data/dataLoader.dart';
import 'addParticipants.dart';
import 'map.dart';

class EventDetails extends StatefulWidget {
  const EventDetails({
    Key? key,
    required this.event,
  }) : super(key: key);
  final EventItem event;

  @override
  _EventDetailsState createState() => _EventDetailsState();
}

class _EventDetailsState extends State<EventDetails> {
  int confirmedParts = 0;
  int declinedParts = 0;
  @override
  void initState() {
    super.initState();
    confirmedParts = context.read<DataLoader>().getMemberByStatus("1").length;
    declinedParts = context.read<DataLoader>().getMemberByStatus("0").length;
  }

  @override
  Widget build(BuildContext context) {
    return Center(
      child: SingleChildScrollView(
        child: Column(
          children: [
            Card(
              child: SizedBox(
                height: 250,
                child: MapWidget(
                  lat: widget.event.location[0]['latitude'],
                  long: widget.event.location[0]['longitude'],
                ),
              ),
            ),
            const SpacerWidget(
              space: 20,
            ),
            Text(
              widget.event.title,
              style: const TextStyle(
                  fontSize: 25.0,
                  color: Colors.blueGrey,
                  letterSpacing: 2.0,
                  fontWeight: FontWeight.w400),
            ),
            const SpacerWidget(
              space: 10,
            ),
            Text(
              widget.event.date + "(" + widget.event.hour + ")",
              style: const TextStyle(
                  fontSize: 16.0,
                  color: Colors.black45,
                  letterSpacing: 2.0,
                  fontWeight: FontWeight.w300),
            ),
            Text(
              widget.event.location[0]['name'],
              textAlign: TextAlign.center,
              style: const TextStyle(
                  fontSize: 16.0,
                  color: Colors.black45,
                  letterSpacing: 2.0,
                  fontWeight: FontWeight.w300),
            ),
            const SpacerWidget(
              space: 10,
            ),
            Text(
              widget.event.description,
              style: const TextStyle(
                  fontSize: 18.0,
                  color: Colors.black,
                  letterSpacing: 2.0,
                  fontWeight: FontWeight.w300),
            ),
            const SpacerWidget(
              space: 15,
            ),
            Card(
              margin:
                  const EdgeInsets.symmetric(horizontal: 20.0, vertical: 8.0),
              elevation: 2.0,
              child: InkWell(
                onTap: () async {
                  String cord =
                      widget.event.location[0]['latitude'].toString() +
                          "," +
                          widget.event.location[0]['longitude'].toString();

                  var url =
                      'https://www.google.com/maps/search/?api=1&query=$cord';
                  var urllaunchable = await canLaunch(url);
                  if (urllaunchable) {
                    await launch(url);
                  } else {
                    print("URL can't be launched.");
                  }
                },
                child: const Padding(
                  padding: EdgeInsets.symmetric(vertical: 12, horizontal: 30),
                  child: Text(
                    "Get direction",
                    style: TextStyle(
                        letterSpacing: 2.0, fontWeight: FontWeight.w300),
                  ),
                ),
              ),
            ),
            const SpacerWidget(
              space: 30,
            ),
            const Text(
              "Participants",
              style: TextStyle(
                  fontSize: 25.0,
                  color: Colors.blueGrey,
                  letterSpacing: 2.0,
                  fontWeight: FontWeight.w400),
            ),
            Card(
              margin:
                  const EdgeInsets.symmetric(horizontal: 20.0, vertical: 8.0),
              child: Padding(
                padding: const EdgeInsets.all(8.0),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: [
                    Expanded(
                      child: Column(
                        children: [
                          const Text(
                            "Confirmé",
                            style: TextStyle(
                                color: Colors.deepPurple,
                                fontSize: 22.0,
                                fontWeight: FontWeight.w600),
                          ),
                          const SpacerWidget(
                            space: 7,
                          ),
                          Text(
                            confirmedParts.toString(),
                            style: const TextStyle(
                                color: Colors.black,
                                fontSize: 22.0,
                                fontWeight: FontWeight.w300),
                          )
                        ],
                      ),
                    ),
                    Expanded(
                      child: Column(
                        children: [
                          const Text(
                            "Décliner",
                            style: TextStyle(
                                color: Colors.deepPurple,
                                fontSize: 22.0,
                                fontWeight: FontWeight.w600),
                          ),
                          const SpacerWidget(
                            space: 7,
                          ),
                          Text(
                            declinedParts.toString(),
                            style: const TextStyle(
                                color: Colors.black,
                                fontSize: 22.0,
                                fontWeight: FontWeight.w300),
                          )
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SpacerWidget(
              space: 30,
            ),
            widget.event.user_id != context.read<DataLoader>().getUser().id
                ? Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      RaisedButton(
                        onPressed: () {},
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(30.0),
                        ),
                        color: Colors.green,
                        child: Ink(
                          child: Container(
                            constraints: const BoxConstraints(
                              maxWidth: 100.0,
                              maxHeight: 40.0,
                            ),
                            alignment: Alignment.center,
                            child: const Text(
                              "Je participe",
                              style: TextStyle(
                                  color: Colors.white,
                                  fontSize: 12.0,
                                  letterSpacing: 2.0,
                                  fontWeight: FontWeight.w300),
                            ),
                          ),
                        ),
                      ),
                      RaisedButton(
                        onPressed: () {},
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(30.0),
                        ),
                        color: Colors.red,
                        child: Ink(
                          child: Container(
                            constraints: const BoxConstraints(
                              maxWidth: 100.0,
                              maxHeight: 40.0,
                            ),
                            alignment: Alignment.center,
                            child: const Text(
                              "Je refuse",
                              style: TextStyle(
                                  color: Colors.white,
                                  fontSize: 12.0,
                                  letterSpacing: 2.0,
                                  fontWeight: FontWeight.w300),
                            ),
                          ),
                        ),
                      ),
                    ],
                  )
                : SizedBox(
                    height: 400,
                    child: AddParticipantsWidget(
                      event: widget.event,
                    ),
                  ),
            const SpacerWidget(
              space: 30,
            ),
          ],
        ),
      ),
    );
  }
}
