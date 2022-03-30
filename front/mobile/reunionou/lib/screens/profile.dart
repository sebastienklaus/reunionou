import 'package:email_validator/email_validator.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/data/dataLoader.dart';
import 'package:reunionou/models/user.dart';
import 'package:reunionou/widgets/navigation_drawer_widget.dart';
import 'package:reunionou/widgets/spacer.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({
    Key? key,
    User? this.user,
  }) : super(key: key);
  static String get route => '/profile';
  final User? user;

  @override
  _ProfileScreenState createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  late final TextEditingController fullname;
  late final TextEditingController email;
  late final TextEditingController username;
  late final TextEditingController password;
  late final TextEditingController newPassword;
  late final TextEditingController confirmPassword;

  final _formKey = GlobalKey<FormState>();

  @override
  void initState() {
    super.initState();
    fullname = TextEditingController(text: widget.user!.fullname);
    email = TextEditingController(text: widget.user!.email);
    username = TextEditingController(text: widget.user!.username);
    password = TextEditingController(text: "");
    newPassword = TextEditingController(text: "");
    confirmPassword = TextEditingController(text: "");
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
          title: const Text('Mon profil'),
          centerTitle: true,
        ),
        body: Scaffold(
          body: Form(
            key: _formKey,
            child: ListView(
              padding: const EdgeInsets.symmetric(horizontal: 32),
              physics: const BouncingScrollPhysics(),
              children: [
                const SizedBox(height: 24),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      "Votre nom",
                      style:
                          TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    const SizedBox(height: 8),
                    TextFormField(
                      controller: fullname,
                      validator: (fullname) {
                        if (fullname!.isEmpty) {
                          return 'Veuillez insérer votre nom';
                        }
                        return null;
                      },
                      decoration: InputDecoration(
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      maxLines: 1,
                    ),
                  ],
                ),
                const SpacerWidget(
                  space: 24,
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      "Votre email",
                      style:
                          TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    const SpacerWidget(
                      space: 8,
                    ),
                    TextFormField(
                      controller: email,
                      validator: (email) {
                        if (email!.isEmpty || !EmailValidator.validate(email)) {
                          return 'Veuillez insérer un email valide';
                        }
                        return null;
                      },
                      decoration: InputDecoration(
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      maxLines: 1,
                    ),
                  ],
                ),
                const SpacerWidget(
                  space: 24,
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      "Votre pseudo",
                      style:
                          TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    const SpacerWidget(
                      space: 8,
                    ),
                    TextFormField(
                      controller: username,
                      validator: (username) {
                        if (username!.isEmpty) {
                          return 'Veuillez insérer un pseudo valide';
                        }
                        return null;
                      },
                      decoration: InputDecoration(
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      maxLines: 1,
                    ),
                  ],
                ),
                const SpacerWidget(
                  space: 24,
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      "Votre mot de passe",
                      style:
                          TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    const SpacerWidget(
                      space: 8,
                    ),
                    TextFormField(
                      controller: password,
                      obscureText: true,
                      enableSuggestions: false,
                      autocorrect: false,
                      validator: (password) {
                        if (password!.isEmpty) {
                          return 'Veuillez insérer un mot de passe';
                        }
                        return null;
                      },
                      decoration: InputDecoration(
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      maxLines: 1,
                    ),
                  ],
                ),
                const SpacerWidget(
                  space: 24,
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      "Nouveau mot de passe",
                      style:
                          TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    const SpacerWidget(
                      space: 8,
                    ),
                    TextFormField(
                      controller: newPassword,
                      obscureText: true,
                      enableSuggestions: false,
                      autocorrect: false,
                      validator: (newPassword) {
                        if (newPassword!.isNotEmpty && newPassword.length < 8) {
                          return 'Le mot de passe doit comporter au moins 8 caractères';
                        }
                        return null;
                      },
                      decoration: InputDecoration(
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      maxLines: 1,
                    ),
                  ],
                ),
                const SpacerWidget(
                  space: 24,
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      "Confirmer le nouveau mot de passe",
                      style:
                          TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    const SpacerWidget(
                      space: 8,
                    ),
                    TextFormField(
                      controller: confirmPassword,
                      obscureText: true,
                      enableSuggestions: false,
                      autocorrect: false,
                      validator: (confirmPassword) {
                        if (newPassword.text != confirmPassword) {
                          return 'Veuillez confirmer le nouveau mot de passe';
                        }
                        return null;
                      },
                      decoration: InputDecoration(
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      maxLines: 1,
                    ),
                  ],
                ),
                const SpacerWidget(
                  space: 24,
                ),
                RaisedButton(
                  color: const Color.fromRGBO(143, 148, 251, 1),
                  splashColor: const Color.fromRGBO(143, 148, 251, 0.3),
                  child: const Center(
                    child: Text(
                      "Mettre à jour",
                      style: TextStyle(
                          color: Colors.white, fontWeight: FontWeight.bold),
                    ),
                  ),
                  onPressed: () async {
                    if (_formKey.currentState!.validate()) {
                      context
                          .read<DataLoader>()
                          .updateUser(
                              widget.user!.id,
                              fullname.text,
                              email.text,
                              username.text,
                              password.text,
                              newPassword.text,
                              confirmPassword.text,
                              widget.user!.token)
                          .then(
                        (rep) {
                          if (rep) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content: Text(
                                  "Profil mis à jour",
                                  style: TextStyle(
                                    color: Color.fromARGB(255, 255, 255, 255),
                                  ),
                                ),
                                backgroundColor: Colors.green,
                              ),
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
                const SpacerWidget(
                  space: 30,
                ),
              ],
            ),
          ),
        ),
      );
}
