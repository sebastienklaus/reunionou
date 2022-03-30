import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/data/dataLoader.dart';
import 'package:reunionou/models/user.dart';

class NavigationDrawerWidget extends StatelessWidget {
  final padding = const EdgeInsets.symmetric(horizontal: 10);

  User? user;

  NavigationDrawerWidget({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    user = context.read<DataLoader>().getUser();
    if (user!.type == "user") {
      return Drawer(
          child: Material(
        color: const Color.fromRGBO(143, 148, 251, 1),
        child: ListView(
          padding: padding,
          children: <Widget>[
            DrawerHeader(
              child: Image.asset("assets/images/light-1.png"),
            ),
            const SizedBox(height: 16),
            buildMenuItem(
                text: user!.fullname,
                icon: Icons.circle,
                onClicked: () => selectedItem(context, 15)),
            const SizedBox(height: 16),
            buildMenuItem(
                text: 'Mes événements',
                icon: Icons.event,
                onClicked: () => selectedItem(context, 0)),
            const SizedBox(height: 16),
            buildMenuItem(
                text: "Rejoindre un événement",
                icon: Icons.share,
                onClicked: () => selectedItem(context, 4)),
            const SizedBox(height: 16),
            const SizedBox(height: 16),
            buildMenuItem(
                text: 'Mon Profil',
                icon: Icons.person,
                onClicked: () => selectedItem(context, 1)),
            const SizedBox(height: 16),
            buildMenuItem(
                text: 'Se déconnecter',
                icon: Icons.logout,
                onClicked: () => selectedItem(context, 3)),
          ],
        ),
      ));
    } else {
      return Drawer(
          child: Material(
        color: const Color.fromRGBO(143, 148, 251, 1),
        child: ListView(
          padding: padding,
          children: <Widget>[
            DrawerHeader(
              child: Image.asset("assets/images/light-1.png"),
            ),
            const SizedBox(height: 16),
            buildMenuItem(
                text: user!.fullname,
                icon: Icons.circle,
                onClicked: () => selectedItem(context, 1)),
            const SizedBox(height: 16),
            buildMenuItem(
                text: 'Mes événements',
                icon: Icons.event,
                onClicked: () => selectedItem(context, 0)),
            buildMenuItem(
                text: 'Se déconnecter',
                icon: Icons.logout,
                onClicked: () => selectedItem(context, 3)),
          ],
        ),
      ));
    }
  }

  selectedItem(BuildContext context, int i) async {
    switch (i) {
      case 0:
        Navigator.pushNamed(
          context,
          '/home',
        );
        break;

      case 1:
        Navigator.pushNamed(
          context,
          '/profile',
        );
        break;

      case 2:
        Navigator.pushNamed(
          context,
          '/home',
        );
        break;
      case 3:
        await context.read<DataLoader>().logout();
        Navigator.pushNamed(
          context,
          '/userLogin',
        );
        break;
      case 4:
        Navigator.pushNamed(
          context,
          '/joinEvent',
        );
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
