import 'package:flutter/cupertino.dart';
import '../models/user.dart';
import 'DatabaseHandler.dart';
import 'package:uuid/uuid.dart';
import 'package:username_generator/username_generator.dart';

class DataLoader extends ChangeNotifier {
  late DatabaseHandler handler;

  //late User _user;
  var _user;

  //Get current _user
  getUser() {
    print('get');
    print(_user);
    return _user;
  }

  //set current user
  setUser(user) {
    _user = user;
    print('set');
    print(_user);
    notifyListeners();
  }

  //Authentificate user
  Future<bool> authentificate(String email, String password) async {
    //Call authentificate api here
    if (1 == 1) {
      _user = User(
        id: "54q6s5d4qsd",
        email: "malek@gmail.com",
        fullname: "malek bk",
        username: "malekbk",
        type: "user",
        token: "3qs4d6q5s4fvd6s5v165165aze1d",
      );
      setUser(_user);
      handler = await DatabaseHandler();

      //In case of success store user to db
      handler.initializeDB().whenComplete(() async {
        await handler.insertUser(_user);
      });
      notifyListeners();
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
      print(e.toString());
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
