import 'package:flutter/material.dart';
import '../models/user.dart';
import '../widgets/navigation_drawer_widget.dart';

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
    print(widget.user);
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        endDrawer: NavigationDrawerWidget(user: widget.user),
        appBar: AppBar(
          title: const Text('Home page'),
          centerTitle: true,
        ),
      );
}
