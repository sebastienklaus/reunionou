import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/animations/loginAnimation.dart';
import 'package:reunionou/data/dataLoader.dart';
import 'package:reunionou/models/message.dart';
import 'package:reunionou/widgets/spacer.dart';
import 'comment.dart';

class CommentsList extends StatefulWidget {
  CommentsList({
    Key? key,
    this.event_id,
  }) : super(key: key);

  String? event_id;

  @override
  CommentsListState createState() => CommentsListState();
}

class CommentsListState extends State<CommentsList> {
  final commentaire = TextEditingController();
  final _formKey = GlobalKey<FormState>();
  List<EventMessage> messages = [];

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder(
      future: context.read<DataLoader>().getMessages(widget.event_id),
      builder: (BuildContext context, AsyncSnapshot<dynamic> snapshot) {
        if (!snapshot.hasData) {
          return const Center(
            child: CircularProgressIndicator(),
          );
        } else {
          return Center(
            child: Column(
              children: <Widget>[
                const SpacerWidget(
                  space: 30,
                ),
                const Text(
                  "Commentaires",
                  style: TextStyle(
                      fontSize: 25.0,
                      color: Colors.blueGrey,
                      letterSpacing: 2.0,
                      fontWeight: FontWeight.w400),
                ),
                const SpacerWidget(
                  space: 30,
                ),
                context.read<DataLoader>().getCurrentMember().status != -1
                    ? Column(
                        children: [
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
                                    controller: commentaire,
                                    validator: (commentaire) {
                                      if (commentaire!.isEmpty) {
                                        return 'Veuillez insérer un commentaire';
                                      }
                                      return null;
                                    },
                                    decoration: InputDecoration(
                                      border: InputBorder.none,
                                      hintText: "Votre commentaire",
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
                              splashColor:
                                  const Color.fromRGBO(143, 148, 251, 0.3),
                              child: const Center(
                                child: Text(
                                  "Envoyer",
                                  style: TextStyle(
                                      color: Colors.white,
                                      fontWeight: FontWeight.bold),
                                ),
                              ),
                              onPressed: () async {
                                if (_formKey.currentState!.validate()) {
                                  context
                                      .read<DataLoader>()
                                      .addMessage(
                                          widget.event_id, commentaire.text)
                                      .then(
                                    (value) {
                                      if (value) {
                                        setState(() {
                                          commentaire.text = "";

                                          context
                                              .read<DataLoader>()
                                              .getMessages(widget.event_id)
                                              .then((value) {
                                            messages = value;
                                          });
                                        });

                                        ScaffoldMessenger.of(context)
                                            .showSnackBar(
                                          const SnackBar(
                                            content: Text(
                                              "Commentaire créé avec succès",
                                              style: TextStyle(
                                                color: Color.fromARGB(
                                                    255, 255, 255, 255),
                                              ),
                                            ),
                                            backgroundColor: Colors.green,
                                          ),
                                        );
                                      } else {
                                        ScaffoldMessenger.of(context)
                                            .showSnackBar(
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
                      )
                    : const Text(
                        "Veuillez mettre à jour votre présence afin d'ajouter un commentaire"),
                Expanded(
                  child: ListView.builder(
                    itemCount: snapshot.data.length,
                    padding: const EdgeInsets.only(top: 15.0),
                    itemBuilder: (context, index) {
                      return CommentWidget(message: snapshot.data[index]);
                    },
                  ),
                ),
              ],
            ),
          );
        }
      },
    );
  }
}
