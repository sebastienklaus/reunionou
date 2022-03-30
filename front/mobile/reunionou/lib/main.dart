import 'package:flutter/material.dart';
import 'package:overlay_support/overlay_support.dart';
import 'package:reunionou/screens/addEvent.dart';
import 'package:reunionou/screens/guest_login.dart';
import 'package:reunionou/screens/home.dart';
import 'package:reunionou/screens/joinEvent.dart';
import 'package:reunionou/screens/profile.dart';
import 'package:reunionou/screens/user_login.dart';
import 'package:provider/provider.dart';
import 'data/dataLoader.dart';
import 'models/user.dart';

void main() {
  var dataCollection = DataLoader();
  runApp(
    ChangeNotifierProvider(
      create: (context) => dataCollection,
      child: MyApp(dataCollection: dataCollection),
    ),
  );
}

class MyApp extends StatelessWidget {
  MyApp({this.dataCollection, Key? key}) : super(key: key);
  DataLoader? dataCollection;

  @override
  Widget build(BuildContext context) {
    Widget initHome;
    return OverlaySupport(
      child: FutureBuilder(
        future: dataCollection!.checkAuthSession(),
        builder: (BuildContext context, AsyncSnapshot<dynamic> snapshot) {
          if (snapshot.data == null || snapshot.data == false) {
            initHome = const UserLoginScreen();
          } else {
            if (snapshot.data is User) {
              initHome = HomeScreen(user: snapshot.data);
            } else {
              initHome = const HomeScreen();
            }
          }
          return MaterialApp(
            title: 'Reunionou',
            theme: ThemeData(
              primarySwatch: Colors.deepPurple,
            ),
            debugShowCheckedModeBanner: false,
            initialRoute: '/',
            routes: {
              UserLoginScreen.route: (context) => const UserLoginScreen(),
              GuestLoginScreen.route: (context) => const GuestLoginScreen(),
              AddEventScreen.route: (context) => const AddEventScreen(),
              JoinEventScreen.route: (context) => const JoinEventScreen(),
              HomeScreen.route: (context) =>
                  HomeScreen(user: context.read<DataLoader>().getUser()),
              ProfileScreen.route: (context) =>
                  ProfileScreen(user: context.read<DataLoader>().getUser()),
            },
            home: initHome,
          );
        },
      ),
    );
  }
}
