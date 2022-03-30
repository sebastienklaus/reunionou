import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/data/dataLoader.dart';
import 'package:reunionou/models/event.dart';
import 'package:flutter_typeahead/flutter_typeahead.dart';
import 'package:reunionou/models/user.dart';
import 'package:reunionou/screens/eventPreview.dart';
import 'package:reunionou/widgets/spacer.dart';

class AddParticipantsWidget extends StatefulWidget {
  const AddParticipantsWidget({
    Key? key,
    this.event,
  }) : super(key: key);
  final EventItem? event;

  @override
  _AddParticipantsWidgetState createState() => _AddParticipantsWidgetState();
}

class _AddParticipantsWidgetState extends State<AddParticipantsWidget> {
  late final TextEditingController participant;
  final _formKey = GlobalKey<FormState>();
  String selecteUserId = "";
  @override
  void initState() {
    super.initState();
    participant = TextEditingController(text: "");
  }

  @override
  Widget build(BuildContext context) => Scaffold(
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
                      "Ajouter un participant",
                      style: TextStyle(fontSize: 16),
                    ),
                    const SizedBox(height: 8),
                    TypeAheadFormField(
                      textFieldConfiguration: TextFieldConfiguration(
                        controller: participant,
                      ),
                      suggestionsCallback: (pattern) {
                        return context
                            .read<DataLoader>()
                            .getUserByName(pattern);
                      },
                      itemBuilder: (context, User suggestion) {
                        return ListTile(
                          title: Text(suggestion.fullname),
                        );
                      },
                      transitionBuilder: (context, suggestionsBox, controller) {
                        return suggestionsBox;
                      },
                      onSuggestionSelected: (User suggestion) {
                        participant.text = suggestion.fullname;
                        selecteUserId = suggestion.id;
                      },
                      validator: (value) {
                        if (value!.isEmpty) {
                          return 'Veuillez sélectionner un utilisateur';
                        }
                      },
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
                      "Ajouter",
                      style: TextStyle(
                          color: Colors.white, fontWeight: FontWeight.bold),
                    ),
                  ),
                  onPressed: () async {
                    if (_formKey.currentState!.validate()) {
                      context
                          .read<DataLoader>()
                          .addMember(
                              widget.event!.id, selecteUserId, participant.text)
                          .then(
                        (value) {
                          if (value) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content: Text(
                                  "Participant ajouté avec succès",
                                  style: TextStyle(
                                    color: Color.fromARGB(255, 255, 255, 255),
                                  ),
                                ),
                                backgroundColor: Colors.green,
                              ),
                            );
                            Navigator.push(
                                context,
                                MaterialPageRoute(
                                    builder: (context) => EventPreviewScreen(
                                        event: widget.event!)));
                          } else {
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content: Text(
                                    "Quelque chose s'est mal passé essaie une autre fois"),
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
