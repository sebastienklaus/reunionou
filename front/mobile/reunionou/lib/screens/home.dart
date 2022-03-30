import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/data/dataLoader.dart';
import 'package:reunionou/models/user.dart';
import 'package:reunionou/widgets/eventCard.dart';
import 'package:reunionou/widgets/navigation_drawer_widget.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({
    Key? key,
    User? this.user,
  }) : super(key: key);
  static String get route => '/home';
  final User? user;

  @override
  _HomeScreenState createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        endDrawer: NavigationDrawerWidget(),
        appBar: AppBar(
          title: const Text('Mes événements'),
          centerTitle: true,
        ),
        body: FutureBuilder(
          future: context.read<DataLoader>().getEvents(),
          builder: (BuildContext context, AsyncSnapshot<dynamic> snapshot) {
            if (!snapshot.hasData) {
              return const Center(
                child: CircularProgressIndicator(),
              );
            } else {
              return GridView.count(
                primary: false,
                padding: const EdgeInsets.all(20),
                crossAxisSpacing: 10,
                mainAxisSpacing: 10,
                crossAxisCount: 2,
                children: List.generate(
                  snapshot.data.length,
                  (index) {
                    return Center(
                      child: EventCard(eventItem: snapshot.data[index]),
                    );
                  },
                ),
              );
            }
          },
        ),
        floatingActionButton: widget.user!.type == "user"
            ? FloatingActionButton(
                tooltip: "Ajouter un événement",
                child: const Icon(Icons.add),
                onPressed: () {
                  Navigator.pushNamed(
                    context,
                    '/addEvent',
                  );
                },
              )
            : Container(),
      );
}
