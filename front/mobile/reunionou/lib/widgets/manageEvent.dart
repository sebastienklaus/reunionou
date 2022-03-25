import 'package:flutter/material.dart';
import 'package:geocoder/geocoder.dart';
import 'package:reunionou/models/event.dart';
import 'package:reunionou/widgets/map.dart';
import '../widgets/spacer.dart';
import 'package:datetime_picker_formfield/datetime_picker_formfield.dart';
import 'package:intl/intl.dart';

class ManageEventScreen extends StatefulWidget {
  const ManageEventScreen({
    Key? key,
    this.action,
    this.event,
  }) : super(key: key);
  final String? action;
  final EventItem? event;

  @override
  _ManageEventScreenState createState() => _ManageEventScreenState();
}

class _ManageEventScreenState extends State<ManageEventScreen> {
  late final TextEditingController title;
  late final TextEditingController description;
  late final TextEditingController location;
  late final TextEditingController date;
  late final TextEditingController hour;
  late Address address;
  late double lat;
  bool changedAdr = false;
  late double long;
  final _formKey = GlobalKey<FormState>();

  @override
  void initState() {
    super.initState();
    if (widget.action == "add") {
      title = TextEditingController(text: "");
      description = TextEditingController(text: "");
      location = TextEditingController(text: "");
      date = TextEditingController(text: "");
      hour = TextEditingController(text: "");
      lat = 0;
      long = 0;
    } else {
      title = TextEditingController(text: widget.event!.title);
      description = TextEditingController(text: widget.event!.description);
      location = TextEditingController(text: "");
      date = TextEditingController(text: widget.event!.date);
      hour = TextEditingController(text: widget.event!.hour);
      long = widget.event!.location[0];
      long = widget.event!.location[1];
    }
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
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      "Localisation",
                      style:
                          TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    const SpacerWidget(
                      space: 8,
                    ),
                    SizedBox(
                      height: 400,
                      child: MapWidget(
                        lat: lat,
                        long: long,
                        changedAdr: changedAdr,
                        getAddress: (Address address) {
                          location.text = "${address.addressLine},";
                          setState(() {
                            address = address;
                            changedAdr = false;
                          });
                        },
                      ),
                    ),
                    const SpacerWidget(
                      space: 10,
                    ),
                    TextFormField(
                      controller: location,
                      onChanged: (value) async {
                        var address =
                            await Geocoder.local.findAddressesFromQuery(value);
                        setState(() {
                          lat = address.first.coordinates.latitude;
                          long = address.first.coordinates.longitude;
                          changedAdr = true;
                        });
                      },
                      validator: (location) {
                        if (location!.isEmpty) {
                          return "Veuillez insérer l'addresse d'événement";
                        }
                        return null;
                      },
                      decoration: InputDecoration(
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      maxLines: 2,
                    ),
                  ],
                ),
                const SpacerWidget(
                  space: 24,
                ),
                RaisedButton(
                  color: const Color.fromRGBO(143, 148, 251, 1),
                  splashColor: const Color.fromRGBO(143, 148, 251, 0.3),
                  child: Center(
                    child: Text(
                      widget.action == "add"
                          ? "Ajouter un événement"
                          : "Mise a jour",
                      style: const TextStyle(
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
