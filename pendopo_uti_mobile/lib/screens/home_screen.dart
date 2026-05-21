import 'package:flutter/material.dart';
import 'booking_page.dart';
import 'login_screen.dart';

class HomeScreen extends StatelessWidget {
  final String userName;
  final String token;
  final String userEmail;
  final String userPhone;

  const HomeScreen({
    super.key,
    required this.userName,
    required this.token,
    required this.userEmail,
    required this.userPhone,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF7F3EE),
      appBar: AppBar(
        elevation: 0,
        backgroundColor: const Color(0xFFF7F3EE),
        title: const Text(
          'Pendopo Uti',
          style: TextStyle(
            fontWeight: FontWeight.w800,
            color: Color(0xFF0B3B34),
          ),
        ),
        actions: [
          TextButton(
            onPressed: () {},
            child: const Text('Home'),
          ),
          TextButton(
            onPressed: () {},
            child: const Text('Facilities'),
          ),
          TextButton(
            onPressed: () {},
            child: const Text(
              'Booking',
              style: TextStyle(fontWeight: FontWeight.w700),
            ),
          ),
          TextButton(
            onPressed: () {},
            child: const Text('Manage'),
          ),
          const SizedBox(width: 8),
          TextButton(
            onPressed: () {
              Navigator.pushAndRemoveUntil(
                context,
                MaterialPageRoute(builder: (_) => const LoginScreen()),
                (route) => false,
              );
            },
            child: const Text('Logout'),
          ),
          const SizedBox(width: 12),
        ],
      ),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Container(
            width: 520,
            padding: const EdgeInsets.all(28),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(24),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.06),
                  blurRadius: 24,
                  offset: const Offset(0, 12),
                ),
              ],
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Icon(
                  Icons.person,
                  size: 56,
                  color: Color(0xFFD4B15F),
                ),
                const SizedBox(height: 16),
                Text(
                  'Selamat datang, $userName',
                  style: const TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF0B3B34),
                  ),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 8),
                Text(
                  userEmail,
                  style: const TextStyle(color: Color(0xFF7F7A72)),
                ),
                const SizedBox(height: 28),
                SizedBox(
                  width: double.infinity,
                  height: 52,
                  child: ElevatedButton(
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => BookingPage(
                            baseUrl: 'http://192.168.0.101:8000/api',
                            token: token,
                            userName: userName,
                            userEmail: userEmail,
                            userPhone: userPhone,
                          ),
                        ),
                      );
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFD4B15F),
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    child: const Text(
                      'Booking Venue',
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}