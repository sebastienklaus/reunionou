import 'package:flutter/material.dart';
import '../animations/loginAnimation.dart';
import '../models/event.dart';
import '../widgets/commentsList.dart';
import '../widgets/eventDetails.dart';
import '../widgets/map.dart';
import '../widgets/navigation_drawer_widget.dart';
import '../widgets/organizerDetails.dart';
import '../widgets/participantsList.dart';
import '../widgets/spacer.dart';

class EventPreviewScreen extends StatefulWidget {
  const EventPreviewScreen({
    Key? key,
    required this.event,
  }) : super(key: key);
  static String get route => '/eventPreview';
  final EventItem event;

  @override
  _HomeScreenState createState() => _HomeScreenState();
}

class _HomeScreenState extends State<EventPreviewScreen> {
  final commentaire = TextEditingController();

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 4,
      child: Scaffold(
        endDrawer: NavigationDrawerWidget(),
        appBar: AppBar(
          leading: IconButton(
            icon: const Icon(Icons.arrow_back, color: Colors.white),
            onPressed: () => Navigator.of(context).pop(),
          ),
          bottom: const TabBar(
            tabs: [
              Tab(icon: Icon(Icons.event)),
              Tab(icon: Icon(Icons.badge)),
              Tab(icon: Icon(Icons.people)),
              Tab(icon: Icon(Icons.comment))
            ],
          ),
          title: Text(widget.event.title),
          centerTitle: true,
        ),
        body: TabBarView(
          children: [
            EventDetails(
              event: widget.event,
            ),
            OrganizerDetails(
              event: widget.event,
            ),
            const ParticipantsList(),
            const CommentsList(),
          ],
        ),
      ),
    );
  }
}
