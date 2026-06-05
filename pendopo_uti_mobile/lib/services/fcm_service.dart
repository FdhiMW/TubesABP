import 'package:firebase_messaging/firebase_messaging.dart';

class FCMService {
  static Future<void> initialize() async {
    FirebaseMessaging messaging = FirebaseMessaging.instance;

    await messaging.requestPermission();

    String? token = await messaging.getToken();

    print("FCM TOKEN:");
    print(token);
  }
}