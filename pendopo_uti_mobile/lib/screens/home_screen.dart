import 'package:flutter/material.dart';
import 'ai_chat_sheet.dart';
import 'booking_page.dart';
import 'login_screen.dart';
import 'manage_page.dart';
import 'package:pendopo_uti_mobile/screens/venue_map_page.dart';
import '../services/auth_service.dart';

class HomeScreen extends StatelessWidget {
  final String userName;
  final String token;
  final String userEmail;
  final String userPhone;

  final GlobalKey _facilitiesKey = GlobalKey();

  HomeScreen({
    super.key,
    required this.userName,
    required this.token,
    required this.userEmail,
    required this.userPhone,
  });

  // Definisi Warna dari Tailwind Figma
  static const Color brandGreen = Color(0xFF2D4B37);
  static const Color brandDark = Color(0xFF1B3022);
  static const Color brandCream = Color(0xFFFAFAF4);
  static const Color brandGold = Color(0xFFC59D5F);

  void _scrollToFacilities() {
    if (_facilitiesKey.currentContext != null) {
      Scrollable.ensureVisible(
        _facilitiesKey.currentContext!,
        duration: const Duration(milliseconds: 600), // Durasi scroll
        curve: Curves.easeInOut, // Animasi perlambatan saat mulai dan berhenti
      );
    }
  }

  // Fungsi untuk Trigger Navigasi Booking (Digunakan di 2 tombol)
  void _navigateToBooking(BuildContext context) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => BookingPage(
          baseUrl: 'http://192.168.18.10:8000/api', // Logika API asli
          token: token,
          userName: userName,
          userEmail: userEmail,
          userPhone: userPhone,
        ),
      ),
    );
  }

  void _navigateToMaps(BuildContext context) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => VenueMapPage(),
      ),
    );
  }

  // Fungsi untuk pergi ke ManagePage
  void _navigateToManage(BuildContext context) {
    Navigator.push(
      context,
      MaterialPageRoute(
      builder: (_) => ManagePage(
      baseUrl: 'http://192.168.0.101:8000/api', // PASTIKAN BARIS INI DITAMBAHKAN
      token: token,
      userName: userName,
      userEmail: userEmail,
      userPhone: userPhone,
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: brandCream,
      extendBodyBehindAppBar: true, // Agar gambar tembus ke belakang appbar
      
      // ==========================================
      // TOP NAVIGATION (APPBAR)
      // ==========================================
      appBar: AppBar(
        backgroundColor: brandGreen.withOpacity(0.9), // Efek backdrop blur semi-transparan
        elevation: 0,
        centerTitle: false,
        leading: IconButton(
          icon: const Icon(Icons.menu, color: brandCream),
          onPressed: () {},
        ),
        actions: [
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 10.0),
            child: OutlinedButton(
              onPressed: () {
                // Logika Logout Asli
                Navigator.pushAndRemoveUntil(
                  context,
                  MaterialPageRoute(builder: (_) => const LoginScreen(showLogoutMessage: true)),
                  (route) => false,
                );
              },
              style: OutlinedButton.styleFrom(
                side: const BorderSide(color: Colors.white),
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(30),
                ),
                padding: const EdgeInsets.symmetric(horizontal: 16),
              ),
              child: const Text(
                'Logout',
                style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, letterSpacing: 0.5),
              ),
            ),
          ),
        ],
      ),

      // ==========================================
      // KONTEN UTAMA (SCROLL)
      // ==========================================
      body: SingleChildScrollView(
        child: Column(
          children: [
            // --- HERO SECTION ---
            Stack(
              children: [
                // Background Image dengan Overlay Gelap
                Container(
                  height: MediaQuery.of(context).size.height * 0.85,
                  width: double.infinity,
                  decoration: const BoxDecoration(
                    color: brandDark,
                    image: DecorationImage(
                      // Menggunakan placeholder image pernikahan elegan
                      image: AssetImage('assets/images/hero.png'),
                      fit: BoxFit.cover,
                    ),
                  ),
                ),
                Container(
                  height: MediaQuery.of(context).size.height * 0.85,
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                      colors: [
                        brandDark.withOpacity(0.4),
                        brandDark.withOpacity(0.2),
                        brandDark.withOpacity(0.9),
                      ],
                    ),
                  ),
                ),
                // Hero Content
                Positioned(
                  bottom: 60,
                  left: 20,
                  right: 20,
                  child: Column(
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Container(width: 30, height: 1, color: brandGold),
                          const SizedBox(width: 10),
                          const Text(
                            'WELCOME TO',
                            style: TextStyle(
                              color: brandGold,
                              fontSize: 10,
                              fontWeight: FontWeight.w600,
                              letterSpacing: 3,
                            ),
                          ),
                          const SizedBox(width: 10),
                          Container(width: 30, height: 1, color: brandGold),
                        ],
                      ),
                      const SizedBox(height: 16),
                      const Text(
                        'Pendopo UTI',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          color: brandCream,
                          fontSize: 48,
                          fontWeight: FontWeight.w700,
                          height: 1.1,
                        ),
                      ),
                      const Text(
                        'Wedding Venue',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          color: brandCream,
                          fontSize: 20,
                          fontStyle: FontStyle.italic,
                          fontWeight: FontWeight.w400,
                        ),
                      ),
                      const SizedBox(height: 24),
                      // Menyelipkan data user di sini agar tetap ada sapaan
                      Text(
                        'Halo, $userName!',
                        style: const TextStyle(
                          color: brandGold,
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 4),
                      const Text(
                        'Booking sekarang dan dapatkan pengalaman pernikahan yang sempurna di tengah arsitektur klasik yang megah dan asri.',
                        textAlign: TextAlign.center,
                        style: TextStyle(color: brandCream, fontSize: 13, height: 1.5),
                      ),
                      const SizedBox(height: 32),
                      
                      // Tombol BOOK NOW (Memanggil logika asli)
                      SizedBox(
                        width: double.infinity,
                        height: 50,
                        child: ElevatedButton(
                          onPressed: () => _navigateToBooking(context),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: brandGold,
                            foregroundColor: Colors.white,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(30),
                            ),
                          ),
                          child: const Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Text('BOOK NOW', style: TextStyle(fontWeight: FontWeight.bold, letterSpacing: 1.5, fontSize: 13)),
                              SizedBox(width: 8),
                              Icon(Icons.arrow_forward, size: 16),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Tombol Maps
                      SizedBox(
                        width: double.infinity,
                        height: 50,
                        child: ElevatedButton.icon(
                          onPressed: () => _navigateToMaps(context),
                          icon: const Icon(Icons.map),
                          label: const Text(
                            'LIHAT LOKASI',
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                              letterSpacing: 1.5,
                              fontSize: 13,
                            ),
                          ),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.white,
                            foregroundColor: brandDark,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(30),
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Tombol Lihat Fasilitas
                      SizedBox(
                        width: double.infinity,
                        height: 50,
                        child: OutlinedButton(
                          onPressed: _scrollToFacilities,
                          style: OutlinedButton.styleFrom(
                            side: BorderSide(color: brandCream.withOpacity(0.5)),
                            foregroundColor: brandCream,
                            backgroundColor: Colors.transparent,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(30),
                            ),
                          ),
                          child: const Text(
                            'LIHAT FASILITAS',
                            style: TextStyle(fontWeight: FontWeight.bold, letterSpacing: 1.5, fontSize: 13),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),

            // --- FACILITIES SECTION ---
            Container(
              key: _facilitiesKey,
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 80),
              color: brandCream,
              child: Column(
                children: [
                  const Text(
                    'OUR FACILITIES',
                    style: TextStyle(color: brandGold, fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 2.5),
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    'Ruang megah untuk\nmomen tak terlupakan',
                    textAlign: TextAlign.center,
                    style: TextStyle(color: brandDark, fontSize: 28, fontWeight: FontWeight.w700, height: 1.2),
                  ),
                  const SizedBox(height: 32),
                  Container(width: 60, height: 1, color: brandGold),
                  const SizedBox(height: 40),
                  
                  // Facility Card
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Elegan & Berkelas',
                        style: TextStyle(color: brandDark, fontSize: 22, fontWeight: FontWeight.w700),
                      ),
                      const SizedBox(height: 12),
                      const Text(
                        'Menghadirkan suasana elegan dengan desain arsitektur klasik yang dipadukan sentuhan modern. Lorong luas dengan pilar-pilar megah serta pencahayaan dramatis menciptakan atmosfer yang sempurna untuk menyambut tamu Anda.',
                        style: TextStyle(color: Colors.black54, fontSize: 13, height: 1.6),
                      ),
                      const SizedBox(height: 24),
                      ClipRRect(
                        borderRadius: BorderRadius.circular(16),
                        child: Image.asset(
                          'assets/images/colonnade.png', // Placeholder Lorong Megah
                          height: 250,
                          width: double.infinity,
                          fit: BoxFit.cover,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            // --- CTA SECTION ---
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 80),
              decoration: const BoxDecoration(
                color: brandDark,
              ),
              child: Column(
                children: [
                  const Text(
                    'Siap merencanakan\nhari istimewa Anda?',
                    textAlign: TextAlign.center,
                    style: TextStyle(color: brandCream, fontSize: 26, fontWeight: FontWeight.w700, height: 1.2),
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'Pesan jadwal survey atau booking venue sekarang — tim kami siap membantu.',
                    textAlign: TextAlign.center,
                    style: TextStyle(color: brandCream.withOpacity(0.8), fontSize: 13, height: 1.5),
                  ),
                  const SizedBox(height: 40),
                  ElevatedButton(
                    onPressed: () => _navigateToBooking(context),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: brandGold,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(30),
                      ),
                    ),
                    child: const Text('MULAI BOOKING', style: TextStyle(fontWeight: FontWeight.bold, letterSpacing: 1.5, fontSize: 12)),
                  ),
                ],
              ),
            ),

            // --- FOOTER ---
            Container(
              width: double.infinity,
              padding: const EdgeInsets.only(left: 24, right: 24, top: 60, bottom: 40),
              color: const Color(0xFF16241A),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'PENDOPO\nUTI',
                    style: TextStyle(color: brandCream, fontSize: 18, fontWeight: FontWeight.w700, letterSpacing: 2),
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'Jl. Contoh Raya No. 123,\nBandung, Jawa Barat 40123\n\nhalo@pendopouti.example',
                    style: TextStyle(color: brandCream.withOpacity(0.7), fontSize: 13, height: 1.5),
                  ),
                  const SizedBox(height: 40),
                  const Text('TAUTAN', style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold, letterSpacing: 1.5)),
                  const SizedBox(height: 12),
                  _buildFooterLink('Facilities', _scrollToFacilities),
                  _buildFooterLink('Booking', () => _navigateToBooking(context)), // Panggil fungsi routing di sini
                  _buildFooterLink('Manage', () => _navigateToManage(context)),
                  _buildFooterLink('Kontak', () {}),
                  const SizedBox(height: 32),
                  const Text('IKUTI KAMI', style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold, letterSpacing: 1.5)),
                  const SizedBox(height: 16),
                  Row(
                    children: [
                      _buildSocialIcon(Icons.facebook),
                      const SizedBox(width: 12),
                      _buildSocialIcon(Icons.camera_alt), // Instagram icon placeholder
                      const SizedBox(width: 12),
                      _buildSocialIcon(Icons.alternate_email), // Twitter/X icon placeholder
                    ],
                  ),
                  const SizedBox(height: 40),
                ],
              ),
            ),
          ],
        ),
      ),

      // ==========================================
      // BOTTOM NAVIGATION BAR
      // ==========================================
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          border: Border(top: BorderSide(color: Colors.grey.shade200, width: 1)),
        ),
        child: BottomNavigationBar(
          type: BottomNavigationBarType.fixed,
          backgroundColor: Colors.white,
          selectedItemColor: brandGreen,
          unselectedItemColor: Colors.grey.shade400,
          selectedFontSize: 10,
          unselectedFontSize: 10,

          onTap: (index) {
            if (index == 1) { 
              // Ke bagian fasilitas (scroll ke bawah)
              _scrollToFacilities();
            } else if (index == 2) { 
              // Pindah ke halaman Booking
              _navigateToBooking(context);
            } else if (index == 3) { 
              _navigateToManage(context);
            }
          },

          items: const [
            BottomNavigationBarItem(icon: Icon(Icons.home_rounded), label: 'Home'),
            BottomNavigationBarItem(icon: Icon(Icons.business_rounded), label: 'Facilities'),
            BottomNavigationBarItem(icon: Icon(Icons.calendar_month_rounded), label: 'Booking'),
            BottomNavigationBarItem(icon: Icon(Icons.dashboard_rounded), label: 'Manage'),
          ],
        ),
      ),

      // ==========================================
      // FLOATING CHAT BUTTON (AI ASSISTANT)
      // ==========================================
      floatingActionButton: FloatingActionButton(
        onPressed: () => showAiChatSheet(
          context,
          baseUrl: AuthService.baseUrl,
          token: token,
        ),
        backgroundColor: brandGold,
        elevation: 4,
        shape: const CircleBorder(),
        child: const Icon(Icons.chat_bubble_outline, color: Colors.white),
      ),
    );
  }

  // Widget Bantuan untuk Icon Footer
  Widget _buildSocialIcon(IconData icon) {
    return Container(
      width: 40,
      height: 40,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        border: Border.all(color: brandCream.withOpacity(0.2)),
      ),
      child: Icon(icon, color: brandGold, size: 18),
    );
  }

  // Widget Bantuan untuk Tautan Footer yang bisa diklik
  Widget _buildFooterLink(String title, VoidCallback? onTap) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8.0),
      child: InkWell(
        onTap: onTap,
        child: Text(
          title, 
          style: TextStyle(
            color: brandCream.withOpacity(0.7), 
            fontSize: 13,
          ),
        ),
      ),
    );
  }
}