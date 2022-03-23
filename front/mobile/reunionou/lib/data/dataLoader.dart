import 'dart:convert';

import 'package:flutter/cupertino.dart';
import '../models/user.dart';
import 'DatabaseHandler.dart';
import 'package:uuid/uuid.dart';
import 'package:username_generator/username_generator.dart';
import 'package:dio/dio.dart';

class DataLoader extends ChangeNotifier {
  /// Links
  //Authentification link
  String _authUri = "http://docketu.iutnc.univ-lorraine.fr:62011/auth";
  String _authCheckUri = "http://docketu.iutnc.univ-lorraine.fr:62011/check";

  //Handler
  late DatabaseHandler handler;
  //late User _user;
  var _user;

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
      String basicAuth =
          'Basic ' + base64Encode(utf8.encode('$email:$password'));
      var rep_auth = await Dio().post(
        _authUri,
        options: Options(
          headers: <String, String>{'authorization': basicAuth},
        ),
      );
      if (rep_auth.statusCode == 200) {
        //Authentificate success -> get user
        final String token = rep_auth.data['refresh-token'];
        var rep_check = await Dio().get(
          _authCheckUri,
          options: Options(
            headers: {
              'Authorization': 'Bearer ' + token,
            },
          ),
        );
        if (rep_check.statusCode == 200) {
          _user = User(
            id: rep_check.data['user_id'],
            email: rep_check.data['user_mail'],
            fullname: rep_check.data['user_fullname'],
            username: rep_check.data['user_mail'],
            type: "user",
            token: rep_check.data['user_token'],
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
}
