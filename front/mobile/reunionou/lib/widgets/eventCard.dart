import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/screens/home.dart';

import '../data/dataLoader.dart';
import '../models/event.dart';
import '../screens/eventPreview.dart';

class EventCard extends StatelessWidget {
  const EventCard({Key? key, required this.eventItem}) : super(key: key);
  final EventItem eventItem;

  @override
  Widget build(BuildContext context) {
    final String img = context.read<DataLoader>().cardImgUri.toString();

    return InkWell(
      onTap: () {
        Navigator.push(
            context,
            MaterialPageRoute(
                builder: (context) => EventPreviewScreen(event: eventItem)));
      },
      child: Container(
        child: Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.center,
            children: <Widget>[
              Text(eventItem.title,
                  style: const TextStyle(color: Colors.white, fontSize: 25),
                  textAlign: TextAlign.center),
              Text(
                eventItem.location[0]['name'],
                style: const TextStyle(color: Colors.white, fontSize: 17),
              ),
              Text(
                eventItem.date + " " + eventItem.hour,
                style: const TextStyle(color: Colors.white),
              ),
            ],
          ),
        ),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(10),
          image: DecorationImage(
            colorFilter: const ColorFilter.mode(
                Color.fromARGB(199, 50, 77, 173), BlendMode.dstATop),
            image: NetworkImage(img),
            fit: BoxFit.fitHeight,
            alignment: Alignment.topCenter,
          ),
        ),
      ),
    );
  }
}
