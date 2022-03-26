import 'package:flutter/material.dart';
import 'package:reunionou/models/event.dart';
import 'package:reunionou/widgets/spacer.dart';
import 'package:url_launcher/url_launcher.dart';
import 'map.dart';

class EventDetails extends StatelessWidget {
  const EventDetails({
    Key? key,
    required this.event,
  }) : super(key: key);
  final EventItem event;

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
                  lat: event.location[0]['latitude'],
                  long: event.location[0]['longitude'],
                ),
              ),
            ),
            const SpacerWidget(
              space: 20,
            ),
            Text(
              event.title,
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
              event.date + "(" + event.hour + ")",
              style: const TextStyle(
                  fontSize: 16.0,
                  color: Colors.black45,
                  letterSpacing: 2.0,
                  fontWeight: FontWeight.w300),
            ),
            Text(
              event.location[0]['name'],
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
              event.description,
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
                  String cord = event.location[0]['latitude'].toString() +
                      "," +
                      event.location[0]['longitude'].toString();

                  var url =
                      'https://www.google.com/maps/search/?api=1&query=$cord';
                  var urllaunchable = await canLaunch(
                      url); //canLaunch is from url_launcher package
                  if (urllaunchable) {
                    await launch(
                        url); //launch is from url_launcher package to launch URL
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
                        children: const [
                          Text(
                            "Confirmé",
                            style: TextStyle(
                                color: Colors.deepPurple,
                                fontSize: 22.0,
                                fontWeight: FontWeight.w600),
                          ),
                          SpacerWidget(
                            space: 7,
                          ),
                          Text(
                            "15",
                            style: TextStyle(
                                color: Colors.black,
                                fontSize: 22.0,
                                fontWeight: FontWeight.w300),
                          )
                        ],
                      ),
                    ),
                    Expanded(
                      child: Column(
                        children: const [
                          Text(
                            "Décliner",
                            style: TextStyle(
                                color: Colors.deepPurple,
                                fontSize: 22.0,
                                fontWeight: FontWeight.w600),
                          ),
                          SpacerWidget(
                            space: 7,
                          ),
                          Text(
                            "5",
                            style: TextStyle(
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
              space: 50,
            ),
            Row(
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
