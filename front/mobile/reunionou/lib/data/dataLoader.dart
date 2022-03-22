import 'dart:convert';
import 'package:dio/dio.dart';
import '../models/user.dart';
import 'DatabaseHandler.dart';

class DataLoader {
  late DatabaseHandler handler;

  //late User _user;
  final User _user = const User(
    id: "54q6s5d4qsd",
    email: "malek@gmail.com",
    fullname: "malek bk",
    username: "malekbk",
    token: "3qs4d6q5s4fvd6s5v165165aze1d",
  );

  //Authentificate
  Future<bool> authentificate(String email, String password) async {
    //Call authentificate api here
    if (1 == 1) {
      print("handler");
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

  //Check authentification
  checkAuthSession() async {
    try {
      handler = await DatabaseHandler();

      //Check db status (empty/not)
      bool dbCheck = await handler.dbIsEmptyOrNot();
      if (!dbCheck) {
        return handler.getUsers();
      } else {
        return false;
      }
    } catch (e) {
      print(e.toString());
    }
  }

  //Check authentification
  logout() async {
    print('logout');
    try {
      handler = await DatabaseHandler();
      handler.delUser();
    } catch (e) {
      print(e.toString());
    }
  }
}
