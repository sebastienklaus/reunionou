import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/data/dataLoader.dart';
import 'package:reunionou/models/event.dart';
import 'package:reunionou/models/member.dart';
import 'package:reunionou/services/weather.dart';
import 'package:reunionou/widgets/spacer.dart';
import 'package:url_launcher/url_launcher.dart';
import 'addParticipants.dart';
import 'map.dart';
import 'package:flutter_share/flutter_share.dart';
import 'package:intl/intl.dart';

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
  String weatherStatus = "";
  late String weatherIcon = "";
  late Member? _member;

  @override
  void initState() {
    super.initState();
    getWeather();
    getMember();
    getCount();
  }

  //Get weather info
  Future<void> getWeather() async {
    var temp = await Weather().getWeather(widget.event.location[0]['latitude'],
        widget.event.location[0]['longitude']);
    setState(() {
      weatherStatus = temp['text'];
      weatherIcon = temp['icon'];
    });
  }

  //Get member
  Future<void> getMember() async {
    Member? temp = await context.read<DataLoader>().getMember(widget.event.id!);
    setState(() {
      _member = temp;
    });
  }

  //Change attendance count
  getCount() async {
    await getMember();
    setState(() {
      confirmedParts = context.read<DataLoader>().getMemberByStatus(1).length -
          1; //Remove creator
      declinedParts = context.read<DataLoader>().getMemberByStatus(0).length;

      //Creator don't count
      if (confirmedParts < 0) {
        confirmedParts = 0;
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    DateTime eventDate =
        DateTime.parse(widget.event.date + " " + widget.event.hour);
    final formatedDate = DateFormat("dd/MM/yyyy HH:mm").format(eventDate);

    return FutureBuilder(
      future: context.read<DataLoader>().getMember(widget.event.id),
      builder: (BuildContext context, AsyncSnapshot<dynamic> snapshot) {
        if (!snapshot.hasData) {
          return const Center(
            child: CircularProgressIndicator(),
          );
        } else {
          return Center(
            child: SingleChildScrollView(
              child: Column(
                children: [
                  Card(
                    child: SizedBox(
                      height: 250,
                      child: MapWidget(
                        lat: double.parse(
                            widget.event.location[0]['latitude'].toString()),
                        long: double.parse(
                            widget.event.location[0]['longitude'].toString()),
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
                    formatedDate,
                    style: const TextStyle(
                        fontSize: 16.0,
                        color: Colors.black45,
                        letterSpacing: 2.0,
                        fontWeight: FontWeight.w300),
                  ),
                  Container(
                    padding: const EdgeInsets.all(20.0),
                    child: Text(
                      widget.event.location[0]['name'],
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                          fontSize: 16.0,
                          color: Colors.black45,
                          letterSpacing: 2.0,
                          fontWeight: FontWeight.w300),
                    ),
                  ),
                  const SpacerWidget(
                    space: 10,
                  ),
                  Container(
                    padding: const EdgeInsets.all(20.0),
                    child: Text(
                      widget.event.description,
                      style: const TextStyle(
                          fontSize: 18.0,
                          color: Colors.black,
                          letterSpacing: 2.0,
                          fontWeight: FontWeight.w300),
                    ),
                  ),
                  const SpacerWidget(
                    space: 15,
                  ),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      Card(
                        elevation: 2.0,
                        child: InkWell(
                          onTap: () async {
                            String cord = widget.event.location[0]['latitude']
                                    .toString() +
                                "," +
                                widget.event.location[0]['longitude']
                                    .toString();

                            var url =
                                'https://www.google.com/maps/search/?api=1&query=$cord';
                            var urllaunchable = await canLaunch(url);
                            if (urllaunchable) {
                              await launch(url);
                            } else {
                              print("L'URL ne peut pas être lancée.");
                            }
                          },
                          child: const Padding(
                            padding: EdgeInsets.symmetric(
                                vertical: 12, horizontal: 30),
                            child: Text(
                              "Obtenir l'itinéraire",
                              style: TextStyle(
                                  letterSpacing: 2.0,
                                  fontWeight: FontWeight.w300),
                            ),
                          ),
                        ),
                      ),
                      Card(
                        elevation: 2.0,
                        color: Colors.deepPurple,
                        child: InkWell(
                          onTap: () async {
                            await FlutterShare.share(
                                title: widget.event.title,
                                text: widget.event.title,
                                linkUrl:
                                    'https://reunionou.netlify.app/#/invite/' +
                                        widget.event.id.toString(),
                                chooserTitle: 'Partager via');
                          },
                          child: const Padding(
                            padding: EdgeInsets.symmetric(
                                vertical: 12, horizontal: 30),
                            child: Text(
                              "Partager",
                              style: TextStyle(
                                  letterSpacing: 2.0,
                                  fontWeight: FontWeight.w300,
                                  color: Colors.white),
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SpacerWidget(
                    space: 30,
                  ),
                  const Text(
                    "Météo",
                    style: TextStyle(
                        fontSize: 25.0,
                        color: Colors.blueGrey,
                        letterSpacing: 2.0,
                        fontWeight: FontWeight.w400),
                  ),
                  const SpacerWidget(
                    space: 13,
                  ),
                  Text(
                    weatherStatus,
                    style: const TextStyle(
                        fontSize: 18.0,
                        color: Colors.black,
                        letterSpacing: 2.0,
                        fontWeight: FontWeight.w300),
                  ),
                  if (weatherIcon != "")
                    Image.network(
                      "https:" + weatherIcon,
                      width: 50,
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
                    margin: const EdgeInsets.symmetric(
                        horizontal: 20.0, vertical: 8.0),
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
                                  "Décliné",
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
                  widget.event.user_id !=
                          context.read<DataLoader>().getUser().id
                      ? Row(
                          mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                          children: [
                            RaisedButton(
                              onPressed: () {
                                context
                                    .read<DataLoader>()
                                    .updateAttendance(1, widget.event.id!)
                                    .then((value) {
                                  if (value) {
                                    //Refresh count
                                    getCount();
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                        content: Text(
                                          "Bien reçu, bienvenue à notre événement",
                                          style: TextStyle(
                                            color: Color.fromARGB(
                                                255, 255, 255, 255),
                                          ),
                                        ),
                                        backgroundColor: Colors.green,
                                      ),
                                    );
                                  } else {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                        content: Text(
                                            "Quelque chose s'est mal passé, essayé une autre fois"),
                                        backgroundColor: Colors.red,
                                      ),
                                    );
                                  }
                                });
                              },
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(30.0),
                              ),
                              color:
                                  _member!.status == 1 || _member!.status == -1
                                      ? Colors.green
                                      : Color.fromARGB(74, 30, 209, 6),
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
                              onPressed: () {
                                context
                                    .read<DataLoader>()
                                    .updateAttendance(0, widget.event.id!)
                                    .then((value) {
                                  if (value) {
                                    //Refresh count
                                    getCount();
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                        content: Text(
                                          "Bien reçu",
                                          style: TextStyle(
                                            color: Color.fromARGB(
                                                255, 255, 255, 255),
                                          ),
                                        ),
                                        backgroundColor: Colors.green,
                                      ),
                                    );
                                  } else {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                        content: Text(
                                            "Quelque chose s'est mal passé, essayé une autre fois"),
                                        backgroundColor: Colors.red,
                                      ),
                                    );
                                  }
                                });
                              },
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(30.0),
                              ),
                              color:
                                  _member!.status == 0 || _member!.status == -1
                                      ? Colors.red
                                      : Color.fromARGB(75, 209, 6, 6),
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
                    space: 50,
                  ),
                ],
              ),
            ),
          );
        }
      },
    );
  }
}
