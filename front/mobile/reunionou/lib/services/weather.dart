import 'dart:async';
import 'dart:convert';
import 'dart:io';

import 'package:dio/dio.dart';

class Weather {
  final String _apiUri = "http://api.weatherapi.com/v1/current.json?";
  final String _key = "key=920bc19c7c4049d4ab291530222803";

  //Get weather
  getWeather(lat, long) async {
    var rep;
    try {
      String _uri = _apiUri +
          _key +
          "&q=" +
          lat.toString() +
          "," +
          long.toString() +
          "&lang=fr";
      var response = await Dio().get(
        _uri,
        options: Options(
          headers: {
            'Content-Type': 'application/json',
          },
        ),
      );
      if (response.statusCode == 200) {
        rep = {
          "text": response.data['current']['temp_c'].toString() +
              "°C (" +
              response.data['current']['condition']['text'] +
              ")",
          "icon": response.data['current']['condition']['icon'],
        };
      }
      return rep;
    } catch (e) {
      throw ('ops');
    }
  }
}
