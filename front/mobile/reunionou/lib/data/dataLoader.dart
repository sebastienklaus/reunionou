import 'dart:convert';
import 'package:dio/dio.dart';
import '../models/user.dart';
import 'DatabaseHandler.dart';
import 'package:uuid/uuid.dart';
import 'package:username_generator/username_generator.dart';

class DataLoader {
  late DatabaseHandler handler;

  //late User _user;
  late User _user;

  //Get current _user
  User getUser() {
    print(_user);
    return _user;
  }

  //set current _user
  void setUser(User user) {
    _user = user;
  }

  //Authentificate user
  Future<bool> authentificate(String email, String password) async {
    //Call authentificate api here
    if (1 == 1) {
      _user = const User(
        id: "54q6s5d4qsd",
        email: "malek@gmail.com",
        fullname: "malek bk",
        username: "malekbk",
        type: "user",
        token: "3qs4d6q5s4fvd6s5v165165aze1d",
      );

      handler = await DatabaseHandler();

      //In case of success store user to db
      handler.initializeDB().whenComplete(() async {
        await handler.insertUser(_user);
      });
      return true;
    } else {
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
        email: "malek@gmail.com",
        fullname: fullname,
        username: generator.generate(fullname),
        type: "guest",
      );
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
        handler.getUser().then((user) {
          setUser(user);
        });
        return handler.getUser();
      } else {
        return false;
      }
    } catch (e) {
      print(e.toString());
    }
  }

  //Check authentification
  logout() async {
    try {
      handler = DatabaseHandler();
      handler.delUser();
    } catch (e) {
      print(e.toString());
    }
  }
}
