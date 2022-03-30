import 'package:flutter/material.dart';
import 'package:flutter_map/plugin_api.dart';
import 'package:latlong/latlong.dart';
import 'package:geocoder/geocoder.dart';

class MapWidget extends StatefulWidget {
  MapWidget({
    Key? key,
    this.lat,
    this.long,
    this.changedAdr,
    this.getAddress,
    this.action,
  }) : super(key: key);
  double? lat;
  double? long;
  bool? changedAdr;
  String? action;
  final Function? getAddress;

  @override
  _MapWidgetState createState() => _MapWidgetState();
}

class _MapWidgetState extends State<MapWidget> {
  late double lat;
  late double long;
  bool updatePin = false;

  @override
  void initState() {
    super.initState();
    if (widget.lat != 0 && widget.long != 0) {
      lat = widget.lat!;
      long = widget.long!;
      if (widget.action == "update") {
        updatePin = true;
      }
    } else {
      //Nancy
      lat = 48.69217956848309;
      long = 6.183914806048693;
      updatePin = true;
    }
  }

  @override
  Widget build(BuildContext context) {
    if (widget.changedAdr == true) {
      setState(() {
        lat = widget.lat!;
        long = widget.long!;
      });
    }

    return FlutterMap(
      options: MapOptions(
        onTap: (value) async {
          if (updatePin) {
            setState(() {
              lat = value.latitude;
              long = value.longitude;
            });
            final coordinates = Coordinates(lat, long);
            var addresses =
                await Geocoder.local.findAddressesFromCoordinates(coordinates);
            var first = addresses.first;
            widget.getAddress!(addresses.first);
          }
        },
        center: LatLng(
          lat,
          long,
        ),
        zoom: 13.0,
      ),
      layers: [
        TileLayerOptions(
          urlTemplate: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
          subdomains: ['a', 'b', 'c'],
        ),
        MarkerLayerOptions(
          markers: [
            Marker(
              width: 80.0,
              height: 80.0,
              point: LatLng(
                lat,
                long,
              ),
              builder: (ctx) => const IconTheme(
                data: IconThemeData(
                    color: Color.fromARGB(255, 116, 28, 151), size: 50),
                child: Icon(Icons.location_pin),
              ),
            ),
          ],
        ),
      ],
    );
  }
}
