import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:email_validator/email_validator.dart';
import 'package:reunionou/animations/loginAnimation.dart';
import 'package:reunionou/data/dataLoader.dart';
import 'package:reunionou/widgets/spacer.dart';

class GuestLoginScreen extends StatefulWidget {
  const GuestLoginScreen({Key? key}) : super(key: key);
  static String get route => '/guestLogin';

  @override
  _GuestLoginScreenState createState() => _GuestLoginScreenState();
}

class _GuestLoginScreenState extends State<GuestLoginScreen> {
  final username = TextEditingController();
  final evenUrl = TextEditingController();

  final _formKey = GlobalKey<FormState>();

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: () async => false,
      child: FutureBuilder(
        future: DataLoader().checkAuthSession(),
        builder: (BuildContext context, AsyncSnapshot<dynamic> snapshot) {
          if (snapshot.data == null) {
            return const Center(
              child: CircularProgressIndicator(),
            );
          } else {
            return Scaffold(
              backgroundColor: Colors.white,
              body: SingleChildScrollView(
                child: Column(
                  children: <Widget>[
                    Container(
                      height: 400,
                      decoration: const BoxDecoration(
                          image: DecorationImage(
                              image: AssetImage('assets/images/background.png'),
                              fit: BoxFit.fill)),
                      child: Stack(
                        children: <Widget>[
                          Positioned(
                            left: 30,
                            width: 80,
                            height: 200,
                            child: FadeAnimation(
                                1,
                                Container(
                                  decoration: const BoxDecoration(
                                      image: DecorationImage(
                                          image: AssetImage(
                                              'assets/images/light-1.png'))),
                                )),
                          ),
                          Positioned(
                            left: 140,
                            width: 80,
                            height: 150,
                            child: FadeAnimation(
                                1.3,
                                Container(
                                  decoration: const BoxDecoration(
                                      image: DecorationImage(
                                          image: AssetImage(
                                              'assets/images/light-2.png'))),
                                )),
                          ),
                          Positioned(
                            right: 40,
                            top: 40,
                            width: 80,
                            height: 150,
                            child: FadeAnimation(
                                1.5,
                                Container(
                                  decoration: const BoxDecoration(
                                      image: DecorationImage(
                                          image: AssetImage(
                                              'assets/images/clock.png'))),
                                )),
                          ),
                          Positioned(
                            child: FadeAnimation(
                                1.6,
                                Container(
                                  margin: const EdgeInsets.only(top: 220),
                                  child: Center(
                                    child: Column(
                                      children: const [
                                        Text(
                                          "Reunionou",
                                          style: TextStyle(
                                              color: Colors.white,
                                              fontSize: 40,
                                              fontWeight: FontWeight.bold),
                                        ),
                                        Text(
                                          "Organiser votre prochain événement",
                                          style: TextStyle(
                                            color: Colors.white,
                                            fontSize: 17,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                )),
                          )
                        ],
                      ),
                    ),
                    const Text(
                      "Se connecter en tant qu'invité",
                      style: TextStyle(
                        color: Colors.deepPurple,
                        fontSize: 20,
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(30.0),
                      child: Column(
                        children: <Widget>[
                          FadeAnimation(
                            1.8,
                            Container(
                              padding: const EdgeInsets.all(5),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(10),
                                boxShadow: const [
                                  BoxShadow(
                                      color: Color.fromRGBO(143, 148, 251, .2),
                                      blurRadius: 20.0,
                                      offset: Offset(0, 10))
                                ],
                              ),
                              child: Form(
                                key: _formKey,
                                child: Column(
                                  children: [
                                    Container(
                                      padding: const EdgeInsets.all(8.0),
                                      decoration: const BoxDecoration(
                                        border: Border(
                                          bottom:
                                              BorderSide(color: Colors.grey),
                                        ),
                                      ),
                                      child: TextFormField(
                                        controller: username,
                                        validator: (username) {
                                          if (username!.isEmpty) {
                                            return 'Veuillez insérer votre pseudo';
                                          }
                                          return null;
                                        },
                                        decoration: InputDecoration(
                                          border: InputBorder.none,
                                          hintText: "Votre pseudo",
                                          hintStyle: TextStyle(
                                            color: Colors.grey[400],
                                          ),
                                        ),
                                      ),
                                    ),
                                    Container(
                                      padding: const EdgeInsets.all(8.0),
                                      decoration: const BoxDecoration(
                                        border: Border(
                                          bottom:
                                              BorderSide(color: Colors.grey),
                                        ),
                                      ),
                                      child: TextFormField(
                                        controller: evenUrl,
                                        validator: (evenUrl) {
                                          if (evenUrl!.isEmpty) {
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
                            ),
                          ),
                          const SpacerWidget(
                            space: 30,
                          ),
                          FadeAnimation(
                            2,
                            RaisedButton(
                              color: const Color.fromRGBO(143, 148, 251, 1),
                              splashColor:
                                  const Color.fromRGBO(143, 148, 251, 0.3),
                              child: const Center(
                                child: Text(
                                  "Continuer",
                                  style: TextStyle(
                                      color: Colors.white,
                                      fontWeight: FontWeight.bold),
                                ),
                              ),
                              onPressed: () async {
                                if (_formKey.currentState!.validate()) {
                                  var rep = await context
                                      .read<DataLoader>()
                                      .authentificateGuest(
                                          username.text, evenUrl.text)
                                      .then(
                                    (rep) {
                                      if (rep) {
                                        ScaffoldMessenger.of(context)
                                            .showSnackBar(
                                          const SnackBar(
                                            content: Text(
                                              "Content de te revoir :)",
                                              style: TextStyle(
                                                color: Color.fromARGB(
                                                    255, 255, 255, 255),
                                              ),
                                            ),
                                            backgroundColor: Color.fromARGB(
                                                143, 148, 251, 1),
                                          ),
                                        );
                                        Navigator.pushNamed(
                                          context,
                                          '/home',
                                        );
                                      } else {
                                        ScaffoldMessenger.of(context)
                                            .showSnackBar(
                                          const SnackBar(
                                            content: Text(
                                                "Authentification échouée :("),
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
                          const SpacerWidget(
                            space: 70,
                          ),
                          FadeAnimation(
                            1.5,
                            InkWell(
                              onTap: () {
                                Navigator.pushNamed(context, "/userLogin");
                              },
                              child: const Padding(
                                padding: EdgeInsets.all(10.0),
                                child: Text(
                                  "Se connecter en tant qu'utilisateur",
                                  style: TextStyle(
                                    color: Color.fromRGBO(143, 148, 251, 1),
                                    fontSize: 18,
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    )
                  ],
                ),
              ),
            );
          }
        },
      ),
    );
  }
}
