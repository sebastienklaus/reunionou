import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../data/dataLoader.dart';
import '../models/event.dart';
import '../widgets/commentsList.dart';
import '../widgets/eventDetails.dart';
import '../widgets/navigation_drawer_widget.dart';
import '../widgets/organizerDetails.dart';
import '../widgets/participantsList.dart';
import '../widgets/manageEvent.dart';
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
    return FutureBuilder(
      future: context.read<DataLoader>().getEventParticipants(widget.event.id),
      builder: (BuildContext context, AsyncSnapshot<dynamic> snapshot) {
        if (!snapshot.hasData) {
          return const Center(
            child: CircularProgressIndicator(),
          );
        } else {}
        return DefaultTabController(
          length: 1 == 1 ? 5 : 4,
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
                  Tab(icon: Icon(Icons.comment)),
                  if (1 == 1) Tab(icon: Icon(Icons.settings))
                ],
              ),
              title: Text(widget.event.title.length > 15
                  ? widget.event.title.substring(0, 15) + "..."
                  : widget.event.title),
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
                if (1 == 1)
                  Column(
                    children: [
                      const SpacerWidget(space: 30),
                      const Text(
                        "Modifier l'événement",
                        style: TextStyle(
                            fontSize: 25.0,
                            color: Colors.blueGrey,
                            letterSpacing: 2.0,
                            fontWeight: FontWeight.w400),
                      ),
                      Expanded(
                        child: ManageEventScreen(
                            event: widget.event, action: 'update'),
                      ),
                    ],
                  )
              ],
            ),
          ),
        );
      },
    );
  }
}
