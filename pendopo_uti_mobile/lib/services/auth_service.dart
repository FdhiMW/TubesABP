import 'dart:convert';
import 'package:http/http.dart' as http;

class AuthService {
  // Emulator Android: 10.0.2.2
  // Device fisik: ganti dengan IP laptop/PC kamu, misalnya 192.168.1.10
  static const String baseUrl = 'http://192.168.1.10:8000/api';

  static Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {
        'Accept': 'application/json',
      },
      body: {
        'email': email,
        'password': password,
      },
    );

    final data = jsonDecode(response.body);

    if (response.statusCode == 200 && data['success'] == true) {
      return {
        'success': true,
        'message': data['message'] ?? 'Login berhasil',
        'token': data['data']?['token'],
        'user': data['data']?['user'],
      };
    }

    return {
      'success': false,
      'message': data['message'] ?? 'Login gagal',
    };
  }

  static Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/register'),
      headers: {
        'Accept': 'application/json',
      },
      body: {
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': passwordConfirmation,
      },
    );

    final data = jsonDecode(response.body);

    if ((response.statusCode == 200 || response.statusCode == 201) &&
        data['success'] == true) {
      return {
        'success': true,
        'message': data['message'] ?? 'Registrasi berhasil',
        'token': data['data']?['token'],
        'user': data['data']?['user'],
      };
    }

    return {
      'success': false,
      'message': data['message'] ?? 'Registrasi gagal',
    };
  }

  static Future<Map<String, dynamic>> saveFcmToken({
    required String token,
    required String fcmToken,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/save-fcm-token'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: {
        'fcm_token': fcmToken,
      },
    );

    print('STATUS: ${response.statusCode}');
    print('BODY: ${response.body}');

    return {
      'status': response.statusCode,
      'body': response.body,
    };
  }
}