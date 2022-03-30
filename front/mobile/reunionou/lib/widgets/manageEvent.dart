import 'package:flutter/material.dart';
import 'package:geocoder/geocoder.dart';
import 'package:provider/provider.dart';
import 'package:reunionou/data/dataLoader.dart';
import 'package:reunionou/models/event.dart';
import 'package:reunionou/widgets/map.dart';
import 'package:datetime_picker_formfield/datetime_picker_formfield.dart';
import 'package:intl/intl.dart';
import 'package:reunionou/widgets/spacer.dart';

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
      location = TextEditingController(text: widget.event!.location[0]['name']);
      date = TextEditingController(text: widget.event!.date);
      hour = TextEditingController(text: widget.event!.hour);
      lat = widget.event!.location[0]['latitude'];
      long = widget.event!.location[0]['longitude'];
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
                          return "Veuillez insérer le titre de l'événement";
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
                          return "Veuillez insérer le description de l'événement";
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
                      validator: (value) {
                        if (date.text == "") {
                          return "Veuillez insérer la date de l'événement";
                        }
                        return null;
                      },
                      onShowPicker: (context, currentValue) {
                        var minDate = DateTime.now().add(Duration(hours: 24));
                        return showDatePicker(
                          helpText: "Date de l'événement",
                          context: context,
                          firstDate: minDate,
                          initialDate: currentValue ?? minDate,
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
                      format: DateFormat("HH:mm"),
                      controller: hour,
                      validator: (value) {
                        if (hour.text == "") {
                          return "Veuillez insérer l'heure de l'événement";
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
                        lat: double.parse(lat.toString()),
                        long: double.parse(long.toString()),
                        action: widget.action,
                        changedAdr: changedAdr,
                        getAddress: (Address address) {
                          location.text = "${address.addressLine}";
                          setState(() {
                            address = address;
                            changedAdr = false;
                            lat = address.coordinates.latitude;
                            long = address.coordinates.longitude;
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
                          return "Veuillez insérer l'addresse de l'événement";
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
                          : "Mettre à jour",
                      style: const TextStyle(
                          color: Colors.white, fontWeight: FontWeight.bold),
                    ),
                  ),
                  onPressed: () async {
                    if (_formKey.currentState!.validate()) {
                      if (widget.action == "add") {
                        EventItem newEvent = EventItem(
                          title: title.text,
                          description: description.text,
                          location: [
                            {
                              "name": location.text,
                              "latitude": lat,
                              "longitude": long,
                            }
                          ],
                          date: date.text,
                          hour: hour.text,
                        );
                        context
                            .read<DataLoader>()
                            .addEvent(newEvent)
                            .then((value) {
                          if (value) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content: Text(
                                  "événement créé avec succès",
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
                        });
                      } else {
                        EventItem event = EventItem(
                          id: widget.event!.id,
                          title: title.text,
                          description: description.text,
                          location: [
                            {
                              "name": location.text,
                              "latitude": lat,
                              "longitude": long,
                            }
                          ],
                          date: date.text,
                          hour: hour.text,
                        );
                        context
                            .read<DataLoader>()
                            .updateEvent(event)
                            .then((value) {
                          if (value) {
                            setState(() {});
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content: Text(
                                  "événement mis à jour avec succès",
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
                        });
                      }
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
