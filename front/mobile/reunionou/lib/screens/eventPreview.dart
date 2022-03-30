import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/data/dataLoader.dart';
import 'package:reunionou/models/event.dart';
import 'package:reunionou/widgets/commentsList.dart';
import 'package:reunionou/widgets/eventDetails.dart';
import 'package:reunionou/widgets/manageEvent.dart';
import 'package:reunionou/widgets/navigation_drawer_widget.dart';
import 'package:reunionou/widgets/organizerDetails.dart';
import 'package:reunionou/widgets/participantsList.dart';
import 'package:reunionou/widgets/spacer.dart';

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
        } else {
          return DefaultTabController(
            length:
                widget.event.user_id == context.read<DataLoader>().getUser().id
                    ? 5
                    : 4,
            child: Scaffold(
              endDrawer: NavigationDrawerWidget(),
              appBar: AppBar(
                leading: IconButton(
                  icon: const Icon(Icons.arrow_back, color: Colors.white),
                  onPressed: () => Navigator.of(context).pop(),
                ),
                bottom: TabBar(
                  tabs: [
                    const Tab(icon: Icon(Icons.event)),
                    const Tab(icon: Icon(Icons.badge)),
                    const Tab(icon: Icon(Icons.people)),
                    const Tab(icon: Icon(Icons.comment)),
                    if (widget.event.user_id ==
                        context.read<DataLoader>().getUser().id)
                      const Tab(icon: Icon(Icons.settings))
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
                  ParticipantsList(creator_id: widget.event.user_id!),
                  Container(
                    padding: const EdgeInsets.all(15.0),
                    child: CommentsList(
                      event_id: widget.event.id,
                    ),
                  ),
                  if (widget.event.user_id ==
                      context.read<DataLoader>().getUser().id)
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
        }
      },
    );
  }
}
