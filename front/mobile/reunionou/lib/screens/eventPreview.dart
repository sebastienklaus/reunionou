import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../data/dataLoader.dart';
import '../models/event.dart';
import '../widgets/eventCard.dart';
import '../widgets/map.dart';
import '../widgets/navigation_drawer_widget.dart';

class EventPreviewScreen extends StatefulWidget {
  const EventPreviewScreen({
    Key? key,
    required EventItem this.event,
  }) : super(key: key);
  static String get route => '/eventPreview';
  final EventItem event;

  @override
  _HomeScreenState createState() => _HomeScreenState();
}

class _HomeScreenState extends State<EventPreviewScreen> {
  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        endDrawer: NavigationDrawerWidget(),
        appBar: AppBar(
          title: Text(widget.event.title),
          centerTitle: true,
        ),
        body: Column(
          children: <Widget>[
            Card(
              child: SizedBox(
                height: 250,
                child: MapWidget(
                  event: widget.event,
                ),
              ),
            ),
            Expanded(
              child: Container(),
            ),
          ],
        ),
      );
}
