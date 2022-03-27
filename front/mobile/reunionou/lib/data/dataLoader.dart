import 'dart:convert';
import 'dart:math';

import 'package:flutter/cupertino.dart';
import '../models/event.dart';
import '../models/member.dart';
import '../models/message.dart';
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
  final String _usersUri = "http://docketu.iutnc.univ-lorraine.fr:62015/users/";

  //Events links
  final String _getUserEvents =
      "http://docketu.iutnc.univ-lorraine.fr:62015/users/{id}/events";
  final String _eventsUri =
      "http://docketu.iutnc.univ-lorraine.fr:62015/events";

  //Members links
  final String _membersUri =
      "http://docketu.iutnc.univ-lorraine.fr:62015/members";
  final String _eventMembers =
      "http://docketu.iutnc.univ-lorraine.fr:62015/events/{id}/members";
  final String _eventMemberByPseudo =
      "http://docketu.iutnc.univ-lorraine.fr:62015/members/{pseudo}/events";

  //Event Messages
  final String _getEventMessages =
      "http://docketu.iutnc.univ-lorraine.fr:62015/events/{id}/messages";
  final String _messageUri =
      "http://docketu.iutnc.univ-lorraine.fr:62015/messages";

  //Card image uri
  final String cardImgUri =
      "https://media.istockphoto.com/photos/award-sparkling-background-picture-id1220754002?k=20&m=1220754002&s=170667a&w=0&h=vnj2Hm2FTsMfV47oznPMinqOGaBghQEj2vcOXlbZFRo=";
  //Handler
  late DatabaseHandler handler;

  //late User _user;
  late User _user;

  //User events
  List<EventItem> myEvents = [];

  //Participants list
  List<Member> participantsList = [];

  //Messages list
  List<EventMessage> eventMessages = [];

  ///-------------------------------------------------------------------------------------------------------------------------///
  ///***************************************************  User Methods  ******************************************************///
  ///-------------------------------------------------------------------------------------------------------------------------///

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
    //Call api
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
    //Call api here
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
          user.type = "user";
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

  //Get all users
  Future<List<User>> getUsers() async {
    try {
      //Call api
      var response = await Dio().get(
        _usersUri,
        options: Options(
          headers: <String, String>{'Origin': "flutter"},
        ),
      );
      List<User> users = [];
      if (response.statusCode == 200) {
        for (var user in response.data) {
          if (_user.id != user['user_id']) {
            var temp = User(
              id: user['user_id'],
              fullname: user['user_fullname'],
              username: user['user_username'],
            );
            users.add(temp);
          }
        }
      }
      return users;
    } catch (e) {
      throw Exception('Failed to load users');
    }
  }

  //Search user by name
  Future<Iterable<User>> getUserByName(search) async {
    try {
      //Refresh users;
      return getUsers().then((users) {
        return users.where((user) => user.fullname.contains(search));
      });
    } catch (e) {
      throw Exception('Failed to load users');
    }
  }

  //Update user
  Future<bool> updateUser(id, fullname, email, username, password, newPassword,
      confirmPassword, token) async {
    //Call api
    try {
      var response = await Dio().put(_usersUri + id,
          options: Options(
            headers: <String, String>{'token': token, 'Origin': "flutter"},
          ),
          data: {
            "fullname": fullname,
            "email": email,
            "username": username,
            "old_password": password,
            "new_password": newPassword,
            "new_password_confirm": confirmPassword
          });
      if (response.statusCode == 200) {
        var refreshResp = await Dio().get(
          _usersUri + id,
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

  //Logout
  logout() async {
    try {
      handler = DatabaseHandler();
      await handler.delUser();
      notifyListeners();
    } catch (e) {
      print(e.toString());
    }
  }

  ///-------------------------------------------------------------------------------------------------------------------------///
  ///*************************************************  Events Methods  ******************************************************///
  ///-------------------------------------------------------------------------------------------------------------------------///

  //Get events
  Future<List<EventItem>> getEvents() async {
    try {
      myEvents = [];
      if (_user.type == "user") {
        //get user event (creator)
        await getUserEvents();

        //get memebr event (by user_id )
        await getMemberEvents("psuedo"); //replace with user_id
      } else {
        //get memebr event (by pseudo)
        await getMemberEvents("psuedo");
      }
      return myEvents;
    } catch (e) {
      throw Exception('Failed to load events');
    }
  }

  //Get user events
  Future<List<EventItem>> getUserEvents() async {
    try {
      //Call api
      var _getUserEvents = this._getUserEvents.replaceAll('{id}', _user.id);
      var response = await Dio().get(
        _getUserEvents,
        options: Options(
          headers: <String, String>{'Origin': "flutter"},
        ),
      );
      if (response.statusCode == 200) {
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

  //Get member events
  Future<List<EventItem>> getMemberEvents(type) async {
    try {
      String uri;

      if (type == "psuedo") {
        uri = _eventMemberByPseudo.replaceAll('{pseudo}', _user.username!);
      } else {
        uri = _eventMemberByPseudo.replaceAll('{user_id}', _user.id);
      }

      var response = await Dio().get(
        uri,
        options: Options(
          headers: <String, String>{'Origin': "flutter"},
        ),
      );
      if (response.statusCode == 200) {
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

  //Get guest event

  //Add event
  Future<bool> addEvent(EventItem event) async {
    //Call api
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
        _eventsUri,
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

//Update event
  Future<bool> updateEvent(EventItem event) async {
    //Call api
    try {
      var parsedEvent = {
        "title": event.title,
        "user_id": _user.id,
        "description": event.description,
        "location": event.location[0],
        "heure": event.hour.substring(0, 5),
        "date": event.date,
      };

      var response = await Dio().put(
        _eventsUri + "/" + event.id!,
        options: Options(
          headers: {
            'token': _user.token,
            'Origin': "flutter",
            'Content-Type': 'application/json',
          },
        ),
        data: parsedEvent,
      );

      if (response.statusCode == 200) {
        await getEvents();
        notifyListeners();
        return true;
      }
      return false;
    } catch (e) {
      print(e.toString());
      return false;
    }
  }

  ///-------------------------------------------------------------------------------------------------------------------------///
  ///************************************************  Members Methods  ******************************************************///
  ///-------------------------------------------------------------------------------------------------------------------------///

  //Add member to event
  Future<bool> addMember(
      String? eventId, String userId, String username) async {
    //Call api
    try {
      var parsedMember = {
        "event_id": eventId,
        "user_id": userId,
        "pseudo": username
      };

      var response = await Dio().post(
        _membersUri,
        options: Options(
          headers: {
            'token': _user.token,
            'Origin': "flutter",
            'Content-Type': 'application/json',
          },
        ),
        data: parsedMember,
      );

      if (response.statusCode == 201) {
        await getEventParticipants(eventId);
        notifyListeners();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }

  //Get event members
  Future<Object> getEventParticipants(String? eventId) async {
    try {
      var _newGetUserEvents = _eventMembers.replaceAll('{id}', eventId!);

      //Call  api
      var response = await Dio().get(
        _newGetUserEvents,
        options: Options(
          headers: {
            'token': _user.token,
            'Origin': "flutter",
          },
        ),
      );

      if (response.statusCode == 200) {
        participantsList = [];
        for (var member in response.data['member']) {
          Member temp = Member(
            id: member['id'],
            user_id: member['user_id'],
            event_id: member['event_id'],
            pseudo: member['pseudo'],
            status: "1",
            created_at: member['created_at'],
            updated_at: member['updated_at'],
          );
          //Add to participants list
          participantsList.add(temp);
        }
        notifyListeners();
      }

      return participantsList;
    } catch (e) {
      throw ("Failed to fetch event members");
    }
  }

  //Get members by status
  Iterable<Member> getMemberByStatus(status) {
    return participantsList.where((element) => element.status == status);
  }

  ///-------------------------------------------------------------------------------------------------------------------------///
  ///***********************************************  Messages Methods  ******************************************************///
  ///-------------------------------------------------------------------------------------------------------------------------///

  //Get event messages
  Future<List<EventMessage>> getMessages(event_id) async {
    try {
      //Call api
      var _getEventMessages =
          this._getEventMessages.replaceAll('{id}', event_id);
      var response = await Dio().get(
        _getEventMessages,
        options: Options(
          headers: <String, String>{'Origin': "flutter"},
        ),
      );
      if (response.statusCode == 200) {
        eventMessages = [];
        for (var msg in response.data['message']) {
          var temp = EventMessage(
            id: msg['id'],
            event_id: msg['event_id'],
            member_id: msg['member_id'],
            content: msg['content'],
          );
          //Add to eventMessages
          eventMessages.add(temp);
        }
      }
      return eventMessages;
    } catch (e) {
      throw Exception('Failed to load event messages');
    }
  }

  //Add message to event
  Future<bool> addMessage(String? eventId, String message) async {
    //Call api
    try {
      var parsedMessage = {
        "event_id": eventId,
        "content": message,
        "member_id": "sdq"
      };

      var response = await Dio().post(
        _messageUri,
        options: Options(
          headers: {
            'token': _user.token,
            'Origin': "flutter",
            'Content-Type': 'application/json',
          },
        ),
        data: parsedMessage,
      );

      if (response.statusCode == 201) {
        await getMessages(eventId);
        notifyListeners();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }
}
