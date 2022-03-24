import 'package:flutter/material.dart';
import 'package:flutter_map/plugin_api.dart';
import 'package:latlong/latlong.dart';

import '../models/event.dart';

class MapWidget extends StatelessWidget {
  const MapWidget({
    Key? key,
    required this.event,
  }) : super(key: key);
  final EventItem event;

  @override
  Widget build(BuildContext context) {
    return FlutterMap(
      options: MapOptions(
        center: LatLng(
          event.location[0]['latitude'],
          event.location[0]['longitude'],
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
                event.location[0]['latitude'],
                event.location[0]['longitude'],
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
