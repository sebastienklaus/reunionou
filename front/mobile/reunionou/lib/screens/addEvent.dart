import 'package:flutter/material.dart';
import 'package:reunionou/widgets/manageEvent.dart';
import 'package:reunionou/widgets/navigation_drawer_widget.dart';

class AddEventScreen extends StatefulWidget {
  const AddEventScreen({
    Key? key,
  }) : super(key: key);
  static String get route => '/addEvent';

  @override
  _AddEventState createState() => _AddEventState();
}

class _AddEventState extends State<AddEventScreen> {
  final _formKey = GlobalKey<FormState>();

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        endDrawer: NavigationDrawerWidget(),
        appBar: AppBar(
          leading: IconButton(
            icon: const Icon(Icons.arrow_back, color: Colors.white),
            onPressed: () => Navigator.pushNamed(
              context,
              '/home',
            ),
          ),
          title: const Text("Ajouter un événement"),
          centerTitle: true,
        ),
        body: const Scaffold(
          body: ManageEventScreen(
            action: 'add',
          ),
        ),
      );
}
