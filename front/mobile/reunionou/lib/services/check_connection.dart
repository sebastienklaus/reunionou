import 'dart:async';
import 'dart:io';

class CheckConnection {
  //Check internet connection
  static Future<bool> initializeCheck() async {
    try {
      final result = await InternetAddress.lookup('www.google.com');
      if (result.isNotEmpty && result[0].rawAddress.isNotEmpty) {
        return true;
      }
      return false;
    } on SocketException catch (_) {
      return false;
    }
  }
}
