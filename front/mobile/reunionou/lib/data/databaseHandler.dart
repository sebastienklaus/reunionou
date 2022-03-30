import 'package:path/path.dart';
import 'package:reunionou/models/user.dart';
import 'package:sqflite/sqflite.dart';

class DatabaseHandler {
// Initiate database
  Future<Database> initializeDB() async {
    String path = await getDatabasesPath();
    return openDatabase(
      join(path, 'reunionou.db'),
      onCreate: (database, version) async {
        //Create users table
        await database.execute(
          'CREATE TABLE users(id TEXT PRIMARY KEY, fullname TEXT,type TEXT, email INTEGER TEXT NULL, username TEXT, token TEXT DEFAULT NULL)',
        );
      },
      version: 1,
    );
  }

//Reset table
  Future<int> resetDb() async {
    int result = 0;
    final Database db = await initializeDB();
    result = await db.rawDelete('DELETE FROM user');
    return result;
  }

  // Insert user
  Future<int> insertUser(User user) async {
    int result = 0;
    final Database db = await initializeDB();
    result = await db.insert(
      'users',
      {
        'id': user.id,
        'fullname': user.fullname,
        'email': user.email,
        'username': user.username,
        'type': user.type,
        'token': user.token,
      },
      conflictAlgorithm: ConflictAlgorithm.replace,
    );

    return result;
  }

  //Get user by id
  Future<List<User>> getUserById(int id) async {
    final Database db = await initializeDB();
    final List<Map<String, Object?>> result =
        await db.rawQuery('SELECT * FROM email WHERE id=?', [id]);
    return result.map((e) => User.fromMap(e)).toList();
  }

  //Get user
  Future<User> getUser() async {
    final Database db = await initializeDB();
    final List<Map<String, Object?>> result =
        await db.rawQuery('SELECT * FROM users limit 1');
    return User.fromJson(result[0]);
  }

  //Count for empty DB or not
  Future<bool> dbIsEmptyOrNot() async {
    final Database db = await initializeDB();
    final List<Map<String, Object?>> queryResult = await db.query('users');
    return queryResult.isEmpty;
  }

  //Reset table
  Future<int> delUser() async {
    int result = 0;
    final Database db = await initializeDB();
    result = await db.rawDelete('DELETE FROM users');
    return result;
  }
}
