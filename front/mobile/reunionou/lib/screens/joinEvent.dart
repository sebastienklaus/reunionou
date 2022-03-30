import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/animations/loginAnimation.dart';
import 'package:reunionou/data/dataLoader.dart';
import 'package:reunionou/widgets/manageEvent.dart';
import 'package:reunionou/widgets/navigation_drawer_widget.dart';
import 'package:reunionou/widgets/spacer.dart';

class JoinEventScreen extends StatefulWidget {
  const JoinEventScreen({
    Key? key,
  }) : super(key: key);
  static String get route => '/joinEvent';

  @override
  _AddEventState createState() => _AddEventState();
}

class _AddEventState extends State<JoinEventScreen> {
  final _formKey = GlobalKey<FormState>();
  late final TextEditingController eventUri;

  @override
  void initState() {
    super.initState();
    eventUri = TextEditingController(text: "");
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
          centerTitle: true,
        ),
        body: Container(
            padding: const EdgeInsets.all(15.0),
            child: Column(
              children: [
                const SpacerWidget(
                  space: 30,
                ),
                const Text(
                  "Rejoindre un événement",
                  style: TextStyle(
                      fontSize: 25.0,
                      color: Colors.blueGrey,
                      letterSpacing: 2.0,
                      fontWeight: FontWeight.w400),
                ),
                const SpacerWidget(
                  space: 30,
                ),
                Form(
                  key: _formKey,
                  child: Column(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(15.0),
                        decoration: const BoxDecoration(
                          border: Border(
                            bottom: BorderSide(color: Colors.grey),
                          ),
                        ),
                        child: TextFormField(
                          maxLines: 2,
                          controller: eventUri,
                          validator: (eventUri) {
                            if (eventUri!.isEmpty) {
                              return "Veuillez insérer le lien d'invitation";
                            }
                            return null;
                          },
                          decoration: InputDecoration(
                            border: InputBorder.none,
                            hintText: "Lien d'invitation",
                            hintStyle: TextStyle(
                              color: Colors.grey[400],
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                FadeAnimation(
                  2,
                  RaisedButton(
                    color: const Color.fromRGBO(143, 148, 251, 1),
                    splashColor: const Color.fromRGBO(143, 148, 251, 0.3),
                    child: const Center(
                      child: Text(
                        "Rejoindre",
                        style: TextStyle(
                            color: Colors.white, fontWeight: FontWeight.bold),
                      ),
                    ),
                    onPressed: () async {
                      if (_formKey.currentState!.validate()) {
                        context
                            .read<DataLoader>()
                            .joinEvent(eventUri.text)
                            .then(
                          (value) {
                            if (value) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  content: Text(
                                    "Evénement rejoint",
                                    style: TextStyle(
                                      color: Color.fromARGB(255, 255, 255, 255),
                                    ),
                                  ),
                                  backgroundColor: Colors.green,
                                ),
                              );
                              Navigator.pushNamed(
                                context,
                                '/home',
                              );
                            } else {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  content: Text(
                                      "Quelque chose s'est mal passé, essayé une autre fois"),
                                  backgroundColor: Colors.red,
                                ),
                              );
                            }
                          },
                        );
                      }
                    },
                  ),
                ),
              ],
            )),
      );
}
