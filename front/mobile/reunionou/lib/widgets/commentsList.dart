import 'package:flutter/material.dart';
import 'package:reunionou/widgets/spacer.dart';

import '../animations/loginAnimation.dart';
import 'comment.dart';

class CommentsList extends StatefulWidget {
  const CommentsList({
    Key? key,
  }) : super(key: key);

  @override
  CommentsListState createState() => CommentsListState();
}

class CommentsListState extends State<CommentsList> {
  final commentaire = TextEditingController();
  final _formKey = GlobalKey<FormState>();

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
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
          Form(
            key: _formKey,
            child: Column(
              children: [
                Container(
                  padding: const EdgeInsets.all(8.0),
                  decoration: const BoxDecoration(
                    border: Border(
                      bottom: BorderSide(color: Colors.grey),
                    ),
                  ),
                  child: TextFormField(
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
              splashColor: const Color.fromRGBO(143, 148, 251, 0.3),
              child: const Center(
                child: Text(
                  "commenter",
                  style: TextStyle(
                      color: Colors.white, fontWeight: FontWeight.bold),
                ),
              ),
              onPressed: () async {
                if (_formKey.currentState!.validate()) {
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text("failed"),
                      backgroundColor: Colors.red,
                    ),
                  );
                }
              },
            ),
          ),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(8),
              children: const <Widget>[
                CommentWidget(),
                CommentWidget(),
                CommentWidget(),
                CommentWidget(),
                CommentWidget(),
                CommentWidget(),
                CommentWidget(),
                CommentWidget(),
                CommentWidget(),
                CommentWidget(),
                CommentWidget(),
                CommentWidget(),
                CommentWidget(),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
