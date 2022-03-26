import 'dart:convert';
import 'dart:math';

import 'package:flutter/cupertino.dart';
import '../models/event.dart';
import '../models/user.dart';
import 'DatabaseHandler.dart';
import 'package:uuid/uuid.dart';
import 'package:username_generator/username_generator.dart';
import 'package:dio/dio.dart';
import 'package:jwt_decoder/jwt_decoder.dart';

class DataLoader extends ChangeNotifier {
  /// Links
  //Authentification links
  final String _authUri = "http://docketu.iutnc.univ-lorraine.fr:62015/auth";
  final String _userAuth = "http://docketu.iutnc.univ-lorraine.fr:62015/users/";

  //Events links
  final String _getUserEvents =
      "http://docketu.iutnc.univ-lorraine.fr:62015/users/{id}/events";
  final String _addEvent = "http://docketu.iutnc.univ-lorraine.fr:62015/events";

  //Card image uri
  final String cardImgUri =
      "https://media.istockphoto.com/photos/award-sparkling-background-picture-id1220754002?k=20&m=1220754002&s=170667a&w=0&h=vnj2Hm2FTsMfV47oznPMinqOGaBghQEj2vcOXlbZFRo=";
  //Handler
  late DatabaseHandler handler;

  //late User _user;
  late User _user;

  //User events
  List<EventItem> myEvents = [];

  ///-----------------------------------------------------------------------------------------------------------------------------------///
  ///********************************************************  User Methods  ***********************************************************///
  ///-----------------------------------------------------------------------------------------------------------------------------------///

  //Get current _user
  getUser() {
    return _user;
  }

  //set current user
  setUser(user) {
    _user = user;
    notifyListeners();
  }

  //Authentificate user
  Future<bool> authentificate(String email, String password) async {
    //Call authentificate api
    try {
      String basicAuth = 'Basic ' +
          base64Encode(
            utf8.encode('$email:$password'),
          );

      var response = await Dio().post(
        _authUri,
        options: Options(
          headers: <String, String>{
            'authorization': basicAuth,
            'Origin': "flutter"
          },
        ),
      );
      if (response.statusCode == 200) {
        Map<String, dynamic> decodedToken =
            JwtDecoder.decode(response.data['refresh-token']);
        _user = User(
          id: decodedToken['upr']['user_id'],
          email: decodedToken['upr']['user_email'],
          fullname: decodedToken['upr']['user_fullname'],
          username: decodedToken['upr']['user_username'],
          type: "user",
          token: response.data['refresh-token'],
        );
        setUser(_user);
        handler = DatabaseHandler();

        //In case of success store user to db
        handler.initializeDB().whenComplete(() async {
          await handler.insertUser(_user);
        });
        notifyListeners();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }

  //Authentificate guest
  Future<bool> authentificateGuest(String fullname) async {
    //Call authentificate api here
    if (1 == 1) {
      handler = DatabaseHandler();
      String id = const Uuid().v4();
      var generator = UsernameGenerator();
      //In case of success store user to db
      _user = User(
        id: id,
        fullname: fullname,
        username: generator.generate(fullname),
        type: "guest",
      );
      setUser(_user);

      notifyListeners();
      handler.initializeDB().whenComplete(() async {
        await handler.insertUser(_user);
      });
      return true;
    } else {
      return false;
    }
  }

  //Check authentification
  checkAuthSession() async {
    try {
      handler = DatabaseHandler();

      //Check db status (empty/not)
      bool dbCheck = await handler.dbIsEmptyOrNot();
      if (!dbCheck) {
        var user = await handler.getUser();
        if (user is User) {
          setUser(user);
          notifyListeners();
          return _user;
        }
      } else {
        return false;
      }
    } catch (e) {
      return false;
    }
  }

  //Update user
  Future<bool> updateUser(
    id,
    fullname,
    email,
    username,
    password,
    newPassword,
    confirmPassword,
    token,
  ) async {
    //Call authentificate api
    try {
      var response = await Dio().put(_userAuth + id,
          options: Options(
            headers: <String, String>{'token': token, 'Origin': "flutter"},
          ),
          data: {
            "fullname": fullname,
            "email": email,
            "username": username,
            "old_password": password,
            "new_password": newPassword,
            "new_password_confirm": confirmPassword,
          });
      if (response.statusCode == 200) {
        var refreshResp = await Dio().get(
          _userAuth + id,
          options: Options(
            headers: <String, String>{'token': token, 'Origin': "flutter"},
          ),
        );
        if (response.statusCode == 200) {
          //Refresh user in local

          handler = DatabaseHandler();
          await handler.delUser();

          _user = User(
            id: id,
            email: refreshResp.data['email'],
            fullname: refreshResp.data['fullname'],
            username: refreshResp.data['username'],
            type: "user",
            token: refreshResp.data['refresh_token'],
          );

          setUser(_user);
          handler = DatabaseHandler();

          handler.initializeDB().whenComplete(() async {
            await handler.insertUser(_user);
          });
          notifyListeners();
        }
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }

  //Check authentification
  logout() async {
    try {
      handler = DatabaseHandler();
      await handler.delUser();
      notifyListeners();
    } catch (e) {
      print(e.toString());
    }
  }

  ///-----------------------------------------------------------------------------------------------------------------------------------///
  ///******************************************************  Events Methods  ***********************************************************///
  ///-----------------------------------------------------------------------------------------------------------------------------------///

  //Get events
  Future<List<EventItem>> getEvents() async {
    try {
      //Call authentificate api
      var _getUserEvents = this._getUserEvents.replaceAll('{id}', _user.id);
      var response = await Dio().get(
        _getUserEvents,
        options: Options(
          headers: <String, String>{'Origin': "flutter"},
        ),
      );
      if (response.statusCode == 200) {
        myEvents = [];
        for (var event in response.data['events']) {
          var temp = EventItem(
            id: event['id'],
            title: event['title'],
            description: event['description'],
            location: [
              {
                "name": event['location']['name'],
                "latitude": event['location']['latitude'],
                "longitude": event['location']['longitude'],
              }
            ],
            user_id: event['user_id'],
            hour: event['heure'],
            date: event['date'],
            created_at: event['created_at'],
            updated_at: event['updated_at'],
          );
          //Add to myEvents
          myEvents.add(temp);
        }
      }
      return myEvents;
    } catch (e) {
      throw Exception('Failed to load events');
    }
  }

  //Add event
  Future<bool> addEvent(EventItem event) async {
    //Call authentificate api
    try {
      var parsedEvent = {
        "title": event.title,
        "user_id": _user.id,
        "description": event.description,
        "location": event.location[0],
        "heure": event.hour,
        "date": event.date,
      };

      var response = await Dio().post(
        _addEvent,
        options: Options(
          headers: {
            'token': _user.token,
            'Origin': "flutter",
            'Content-Type': 'application/json',
          },
        ),
        data: parsedEvent,
      );

      if (response.statusCode == 201) {
        notifyListeners();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }
}
