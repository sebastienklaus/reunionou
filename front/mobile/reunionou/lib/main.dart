import 'package:flutter/material.dart';
import 'package:overlay_support/overlay_support.dart';
import 'package:reunionou/screens/home.dart';
import 'package:reunionou/screens/login.dart';
import 'package:provider/provider.dart';
import 'data/datProvider.dart';
import 'data/dataLoader.dart';

Future<void> main() async {
  var pataProider = DataProider();
  runApp(
    ChangeNotifierProvider(
      create: (context) => pataProider,
      child: const MyApp(),
    ),
  );
}

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    Widget initHome;
    return OverlaySupport(
      child: FutureBuilder(
        future: DataLoader().checkAuthSession(),
        builder: (BuildContext context, AsyncSnapshot<dynamic> snapshot) {
          if (snapshot.data == null || snapshot.data == false) {
            initHome = const LoginScreen();
          } else {
            initHome = const HomeScreen();
          }
          return MaterialApp(
            title: 'Reunionou',
            theme: ThemeData(
              primarySwatch: Colors.blue,
            ),
            debugShowCheckedModeBanner: false,
            initialRoute: '/',
            routes: {
              LoginScreen.route: (context) => const LoginScreen(),
              HomeScreen.route: (context) => const HomeScreen(),
            },
            home: initHome,
          );
        },
      ),
    );
  }
}
