import 'package:flutter/material.dart';
import 'package:reunionou/models/event.dart';
import '../widgets/spacer.dart';
import 'package:datetime_picker_formfield/datetime_picker_formfield.dart';
import 'package:intl/intl.dart';

class UpdateEventScreen extends StatefulWidget {
  const UpdateEventScreen({
    Key? key,
    this.event,
  }) : super(key: key);
  static String get route => '/profile';
  final EventItem? event;

  @override
  _UpdateEventScreenState createState() => _UpdateEventScreenState();
}

class _UpdateEventScreenState extends State<UpdateEventScreen> {
  late final TextEditingController title;
  late final TextEditingController description;
  late final TextEditingController location;
  late final TextEditingController date;
  late final TextEditingController hour;

  final _formKey = GlobalKey<FormState>();

  @override
  void initState() {
    super.initState();
    title = TextEditingController(text: widget.event!.title);
    description = TextEditingController(text: widget.event!.description);
    location = TextEditingController(text: "");
    date = TextEditingController(text: widget.event!.date);
    hour = TextEditingController(text: widget.event!.hour);
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
                      "Titre",
                      style:
                          TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    const SizedBox(height: 8),
                    TextFormField(
                      controller: title,
                      validator: (title) {
                        if (title!.isEmpty) {
                          return "Veuillez insérer le titre d'événement";
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
                      "Description",
                      style:
                          TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    const SpacerWidget(
                      space: 8,
                    ),
                    TextFormField(
                      controller: description,
                      validator: (description) {
                        if (description!.isEmpty) {
                          return "Veuillez insérer le description d'événement";
                        }
                        return null;
                      },
                      decoration: InputDecoration(
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      maxLines: 7,
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
                      "Date",
                      style:
                          TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    const SpacerWidget(
                      space: 8,
                    ),
                    DateTimeField(
                      format: DateFormat("yyyy-MM-dd"),
                      controller: date,
                      validator: (date) {
                        if (date == null) {
                          return "Veuillez insérer le date d'événement";
                        }
                        return null;
                      },
                      onShowPicker: (context, currentValue) {
                        return showDatePicker(
                          helpText: "Date d'événement",
                          context: context,
                          firstDate: DateTime.now(),
                          initialDate: currentValue ?? DateTime.now(),
                          lastDate: DateTime(2100),
                        );
                      },
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
                      "Heure",
                      style:
                          TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    const SpacerWidget(
                      space: 8,
                    ),
                    DateTimeField(
                      format: DateFormat("HH:mm:ss"),
                      controller: hour,
                      validator: (hour) {
                        if (hour == null) {
                          return "Veuillez insérer l'heure d'événement";
                        }
                        return null;
                      },
                      onShowPicker: (context, currentValue) async {
                        final time = await showTimePicker(
                          context: context,
                          initialTime: TimeOfDay.fromDateTime(
                              currentValue ?? DateTime.now()),
                          builder: (context, child) => MediaQuery(
                              data: MediaQuery.of(context)
                                  .copyWith(alwaysUse24HourFormat: true),
                              child: child!),
                        );
                        return DateTimeField.convert(time);
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
                      "Mise a jour",
                      style: TextStyle(
                          color: Colors.white, fontWeight: FontWeight.bold),
                    ),
                  ),
                  onPressed: () async {
                    if (_formKey.currentState!.validate()) {}
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
