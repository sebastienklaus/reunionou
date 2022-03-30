import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/data/dataLoader.dart';
import 'package:reunionou/models/event.dart';
import 'package:reunionou/screens/eventPreview.dart';
import 'package:reunionou/widgets/spacer.dart';
import 'package:intl/intl.dart';

class EventCard extends StatelessWidget {
  const EventCard({Key? key, required this.eventItem}) : super(key: key);
  final EventItem eventItem;

  @override
  Widget build(BuildContext context) {
    final String img = context.read<DataLoader>().cardImgUri.toString();
    DateTime eventDate = DateTime.parse(eventItem.date + " " + eventItem.hour);
    final formatedDate = DateFormat("dd/MM/yyyy HH:mm").format(eventDate);

    return InkWell(
      onTap: () async {
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
              Text(
                  eventItem.title.length > 20
                      ? eventItem.title.substring(0, 19) + "..."
                      : eventItem.title,
                  style: const TextStyle(color: Colors.white, fontSize: 25),
                  textAlign: TextAlign.center),
              const SpacerWidget(
                space: 15,
              ),
              Text(
                formatedDate.toString(),
                style: const TextStyle(color: Colors.white),
              ),
              const SpacerWidget(
                space: 15,
              ),
              Text(
                eventItem.user_id == context.read<DataLoader>().getUser().id
                    ? "Organisateur"
                    : "",
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
