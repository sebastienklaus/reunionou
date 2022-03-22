import 'package:flutter/material.dart';
import 'package:reunionou/screens/home.dart';
import 'package:reunionou/screens/login.dart';
import '../data/dataLoader.dart';

class NavigationDrawerWidget extends StatelessWidget {
  final padding = const EdgeInsets.symmetric(horizontal: 10);

  const NavigationDrawerWidget({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Drawer(
        child: Material(
      color: Color.fromRGBO(143, 148, 251, 1),
      child: ListView(
        padding: padding,
        children: <Widget>[
          DrawerHeader(
            child: Image.asset("assets/logo.png"),
          ),
          const SizedBox(height: 16),
          buildMenuItem(
              text: 'Accueil',
              icon: Icons.home,
              onClicked: () => selectedItem(context, 0)),
          const SizedBox(height: 16),
          buildMenuItem(
              text: 'Profile',
              icon: Icons.person,
              onClicked: () => selectedItem(context, 1)),
          const SizedBox(height: 16),
          buildMenuItem(
              text: 'Paramètres',
              icon: Icons.settings,
              onClicked: () => selectedItem(context, 2)),
          const SizedBox(height: 16),
          buildMenuItem(
              text: 'Se déconnecter',
              icon: Icons.logout,
              onClicked: () => selectedItem(context, 3)),
        ],
      ),
    ));
  }

  selectedItem(BuildContext context, int i) async {
    switch (i) {
      case 0:
        Navigator.of(context).push(MaterialPageRoute(
          builder: (context) => const HomeScreen(),
        ));
        break;

      case 1:
        Navigator.of(context).push(MaterialPageRoute(
          builder: (context) => const HomeScreen(),
        ));
        break;

      case 2:
        Navigator.of(context).push(MaterialPageRoute(
          builder: (context) => const HomeScreen(),
        ));
        break;
      case 3:
        await DataLoader().logout();
        Navigator.of(context).push(MaterialPageRoute(
          builder: (context) => const LoginScreen(),
        ));
        break;
    }
  }

  buildHeader({
    required String logo,
  }) =>
      {
        InkWell(
          child: Container(
            padding: padding.add(const EdgeInsets.symmetric(vertical: 40)),
            child: Row(
              children: [Image.asset('assets/images/logo.png')],
            ),
          ),
        )
      };
}

Widget buildMenuItem({
  required String text,
  required IconData icon,
  VoidCallback? onClicked,
}) {
  return ListTile(
    leading: Icon(icon, color: Colors.white),
    title: Text(
      text,
      style: const TextStyle(
        color: Colors.white,
      ),
    ),
    onTap: onClicked,
  );
}
