import 'dart:convert';
import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'booking_page.dart';

// ==========================================
// DATA MODELS (Sesuai Struktur JSON Laravel)
// ==========================================
class ManageBooking {
  final int id;
  final String venueName;
  final String venueLocation;
  final String eventDate;
  final String eventTime;
  final String endTime;
  final String status;
  final String paymentStatus;

  ManageBooking({
    required this.id, required this.venueName, required this.venueLocation, 
    required this.eventDate, required this.eventTime, required this.endTime, 
    required this.status, required this.paymentStatus
  });

  static String _formatTime(dynamic value) {
    if (value == null) return '--:--';
    final s = value.toString();
    if (s.length >= 5 && s[2] == ':') return s.substring(0, 5);
    return s;
  }

  factory ManageBooking.fromJson(Map<String, dynamic> json) {
    final venue = json['venue'] as Map<String, dynamic>? ?? {};
    final rawDate = json['event_date']?.toString() ?? '-';
    final eventDate = rawDate.length >= 10 ? rawDate.substring(0, 10) : rawDate;
    return ManageBooking(
      id: int.tryParse(json['id']?.toString() ?? '0') ?? 0,
      venueName: venue['name']?.toString() ?? 'Venue',
      venueLocation: venue['location']?.toString() ?? 'Location',
      eventDate: eventDate,
      eventTime: _formatTime(json['event_time']),
      endTime: _formatTime(json['end_time']),
      status: json['status']?.toString() ?? 'pending',
      paymentStatus: json['payment_status']?.toString() ?? 'unpaid',
    );
  }
}

class ManageSurvey {
  final int id;
  final String venueName;
  final String venueLocation;
  final String proposedDate;
  final String proposedTime;
  final String endTime;
  final String status;

  ManageSurvey({
    required this.id, required this.venueName, required this.venueLocation, 
    required this.proposedDate, required this.proposedTime, required this.endTime, 
    required this.status
  });

  factory ManageSurvey.fromJson(Map<String, dynamic> json) {
    final venue = json['venue'] as Map<String, dynamic>? ?? {};
    final rawDate = json['proposed_date']?.toString() ?? '-';
    final proposedDate = rawDate.length >= 10 ? rawDate.substring(0, 10) : rawDate;
    return ManageSurvey(
      id: int.tryParse(json['id']?.toString() ?? '0') ?? 0,
      venueName: venue['name']?.toString() ?? 'Venue',
      venueLocation: venue['location']?.toString() ?? 'Location',
      proposedDate: proposedDate,
      proposedTime: ManageBooking._formatTime(json['proposed_time']),
      endTime: ManageBooking._formatTime(json['end_time']),
      status: json['status']?.toString() ?? 'pending',
    );
  }
}

// ==========================================
// API — memanggil ManageController & PaymentController (Laravel)
// Route: /api/manage, /api/booking/{id}/cancel, dll. (sama logika web)
// ==========================================
class ManageApi {
  final String baseUrl;
  final String token;

  ManageApi({required this.baseUrl, required this.token});

  Map<String, String> get _headers => {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      };

  String _messageFromBody(dynamic body, String fallback) {
    if (body is Map && body['message'] != null) {
      return body['message'].toString();
    }
    return fallback;
  }

  /// GET /api/manage → ManageController@index
  Future<Map<String, dynamic>> fetchManageData() async {
    final res = await http.get(Uri.parse('$baseUrl/manage'), headers: _headers);

    if (res.statusCode != 200) {
      dynamic body;
      try {
        body = jsonDecode(res.body);
      } catch (_) {}
      throw Exception(_messageFromBody(body, 'Gagal memuat riwayat (HTTP ${res.statusCode})'));
    }

    final body = jsonDecode(res.body);
    final bookingsList = (body['bookings'] as List? ?? [])
        .map((e) => ManageBooking.fromJson(Map<String, dynamic>.from(e)))
        .toList();
    final surveysList = (body['surveys'] as List? ?? [])
        .map((e) => ManageSurvey.fromJson(Map<String, dynamic>.from(e)))
        .toList();

    return {'bookings': bookingsList, 'surveys': surveysList};
  }

  /// POST /api/booking|survey/{id}/cancel → ManageController
  Future<void> cancelItem(String type, int id) async {
    final res = await http.post(
      Uri.parse('$baseUrl/$type/$id/cancel'),
      headers: _headers,
    );
    if (res.statusCode >= 200 && res.statusCode < 300) return;
    dynamic body;
    try {
      body = jsonDecode(res.body);
    } catch (_) {}
    throw Exception(_messageFromBody(body, 'Gagal membatalkan'));
  }

  /// POST /api/booking|survey/{id}/reschedule → ManageController
  Future<void> rescheduleItem(String type, int id, String date, String time, {String? endTime}) async {
    final Map<String, String> bodyData = type == 'booking'
        ? {'event_date': date, 'event_time': time, 'end_time': endTime ?? time}
        : {'proposed_date': date, 'proposed_time': time};

    final res = await http.post(
      Uri.parse('$baseUrl/$type/$id/reschedule'),
      headers: _headers,
      body: bodyData,
    );
    if (res.statusCode >= 200 && res.statusCode < 300) return;
    dynamic body;
    try {
      body = jsonDecode(res.body);
    } catch (_) {}
    throw Exception(_messageFromBody(body, 'Gagal reschedule'));
  }

  /// GET /api/payment/{id} → PaymentController@pay (sama web manage)
  Future<String?> getPaymentToken(int id) async {
    final res = await http.get(Uri.parse('$baseUrl/payment/$id'), headers: _headers);
    if (res.statusCode >= 200 && res.statusCode < 300) {
      final body = jsonDecode(res.body);
      return body['token']?.toString();
    }
    return null;
  }
}

// ==========================================
// STATEFUL WIDGET MAIN UI
// ==========================================
class ManagePage extends StatefulWidget {
  final String baseUrl;
  final String token;
  final String userName;
  final String userEmail;
  final String userPhone;
  final int initialTabIndex;

  const ManagePage({
    super.key,
    required this.baseUrl,
    required this.token,
    required this.userName,
    required this.userEmail,
    required this.userPhone,
    this.initialTabIndex = 0, 
  });

  @override
  State<ManagePage> createState() => _ManagePageState();
}

class _ManagePageState extends State<ManagePage> {
  late final ManageApi api;
  
  bool isLoading = true;
  String? loadError;
  List<ManageBooking> bookings = [];
  List<ManageSurvey> surveys = [];
  
  bool showBanner = false;
  String bannerMessage = '';
  bool isBannerGreen = true;

  @override
  void initState() {
    super.initState();
    api = ManageApi(baseUrl: widget.baseUrl, token: widget.token);
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() {
      isLoading = true;
      loadError = null;
    });
    try {
      final data = await api.fetchManageData();
      if (!mounted) return;
      setState(() {
        bookings = data['bookings'];
        surveys = data['surveys'];
        isLoading = false;
      });
    } catch (e) {
      debugPrint('Manage API error: $e');
      if (!mounted) return;
      setState(() {
        bookings = [];
        surveys = [];
        loadError = e.toString().replaceFirst('Exception: ', '');
        isLoading = false;
      });
    }
  }

  Future<void> _handleCancel(String type, int id) async {
    try {
      await api.cancelItem(type, id);
      _showNotification(type == 'booking' ? 'Booking berhasil dibatalkan' : 'Survey berhasil dibatalkan', true);
      
      setState(() {
        if (type == 'booking') {
          final index = bookings.indexWhere((b) => b.id == id);
          if (index != -1) bookings[index] = ManageBooking(id: bookings[index].id, venueName: bookings[index].venueName, venueLocation: bookings[index].venueLocation, eventDate: bookings[index].eventDate, eventTime: bookings[index].eventTime, endTime: bookings[index].endTime, status: 'cancelled', paymentStatus: bookings[index].paymentStatus);
        } else {
          final index = surveys.indexWhere((s) => s.id == id);
          if (index != -1) surveys[index] = ManageSurvey(id: surveys[index].id, venueName: surveys[index].venueName, venueLocation: surveys[index].venueLocation, proposedDate: surveys[index].proposedDate, proposedTime: surveys[index].proposedTime, endTime: surveys[index].endTime, status: 'cancelled');
        }
      });
    } catch (e) {
      final msg = e.toString().replaceFirst('Exception: ', '');
      _showNotification(msg.isNotEmpty ? msg : 'Gagal membatalkan. Coba lagi.', false);
    }
  }

  Future<void> _handleReschedule(String type, int id, String date, String time, {String? endTime}) async {
    try {
      await api.rescheduleItem(type, id, date, time, endTime: endTime);
      _showNotification('Reschedule $type berhasil', true);
      
      setState(() {
        if (type == 'booking') {
          final index = bookings.indexWhere((b) => b.id == id);
          if (index != -1) bookings[index] = ManageBooking(id: bookings[index].id, venueName: bookings[index].venueName, venueLocation: bookings[index].venueLocation, eventDate: date, eventTime: time, endTime: endTime ?? bookings[index].endTime, status: bookings[index].status, paymentStatus: bookings[index].paymentStatus);
        } else {
          final index = surveys.indexWhere((s) => s.id == id);
          if (index != -1) surveys[index] = ManageSurvey(id: surveys[index].id, venueName: surveys[index].venueName, venueLocation: surveys[index].venueLocation, proposedDate: date, proposedTime: time, endTime: surveys[index].endTime, status: surveys[index].status);
        }
      });
    } catch (e) {
      final msg = e.toString().replaceFirst('Exception: ', '');
      _showNotification(msg.isNotEmpty ? msg : 'Gagal reschedule. Coba lagi.', false);
    }
  }

  Future<void> _handlePayment(int id) async {
    try {
      final token = await api.getPaymentToken(id);
      if (token == null) {
        _showNotification('Gagal memuat pembayaran.', false);
        return;
      }
      _showNotification('Token pembayaran siap (integrasikan Midtrans Snap).', true);
    } catch (e) {
      _showNotification('Gagal memuat pembayaran.', false);
    }
  }

  void _showNotification(String message, bool isGreen) {
    if (!mounted) return;
    setState(() {
      bannerMessage = message;
      isBannerGreen = isGreen;
      showBanner = true;
    });
    Future.delayed(const Duration(seconds: 3), () {
      if (mounted) setState(() => showBanner = false);
    });
  }

  // ==========================================
  // PALET WARNA TAILWIND (Sesuai Figma)
  // ==========================================
  static const Color ricePaper = Color(0xFFFAFAF5);
  static const Color heritageGreen = Color(0xFF2D4B37);
  static const Color outlineVariant = Color(0xFFC2C8C0);
  static const Color surfaceContainerHighest = Color(0xFFE3E3DE);
  static const Color surfaceContainer = Color(0xFFEEEEE9);
  static const Color onSurface = Color(0xFF1A1C19);
  static const Color onSurfaceVariant = Color(0xFF424843);
  static const Color outline = Color(0xFF727972);
  static const Color errorContainer = Color(0xFFFFDAD6);
  static const Color onErrorContainer = Color(0xFF93000A);
  static const Color error = Color(0xFFBA1A1A);
  static const Color secondaryContainer = Color(0xFFFFCA98);
  static const Color onSecondaryContainer = Color(0xFF7A532A);
  static const Color primaryFixed = Color(0xFFC8EBD0);
  static const Color onPrimaryFixed = Color(0xFF022110);
  static const Color blueButton = Color(0xFF2563EB);
  static const Color cancelBannerBg = Color(0xFFF0FDF4);
  static const Color cancelBannerBorder = Color(0xFFBBF7D0);
  static const Color cancelBannerText = Color(0xFF166534);
  static const Color batikGold = Color(0xFFD4A373); 
  static const Color surveyBtnCancelBg = Color(0xFFFFF8F7);
  static const Color surveyBtnRescheduleBg = Color(0xFFF7FBF8);
  static const Color surveyBtnRescheduleBorder = Color(0xFFADCFB4);
  static const Color surveyBtnRescheduleText = Color(0xFF163422);

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2, 
      initialIndex: widget.initialTabIndex, 
      child: Scaffold(
        backgroundColor: ricePaper,
        appBar: AppBar(
          backgroundColor: ricePaper,
          elevation: 0,
          scrolledUnderElevation: 0,
          bottom: PreferredSize(
            preferredSize: const Size.fromHeight(1.0),
            child: Container(color: outlineVariant, height: 1.0),
          ),
          leading: IconButton(
            icon: const Icon(Icons.arrow_back, color: heritageGreen),
            onPressed: () => Navigator.pop(context),
          ),
          title: const Text('Manage', style: TextStyle(color: heritageGreen, fontSize: 20, fontWeight: FontWeight.bold)),
          centerTitle: true,
        ),
        
        body: isLoading 
          ? const Center(child: CircularProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(heritageGreen)))
          : loadError != null
          ? Center(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.cloud_off, size: 48, color: outline),
                    const SizedBox(height: 16),
                    Text(loadError!, textAlign: TextAlign.center, style: const TextStyle(color: onSurfaceVariant)),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: _loadData,
                      style: ElevatedButton.styleFrom(backgroundColor: heritageGreen, foregroundColor: Colors.white),
                      child: const Text('Coba lagi'),
                    ),
                  ],
                ),
              ),
            )
          : Column(
          children: [
            Container(
              decoration: const BoxDecoration(border: Border(bottom: BorderSide(color: surfaceContainerHighest, width: 1))),
              child: const TabBar(
                indicatorColor: heritageGreen, indicatorWeight: 2, labelColor: heritageGreen, unselectedLabelColor: outline,
                labelStyle: TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
                unselectedLabelStyle: TextStyle(fontSize: 14, fontWeight: FontWeight.w500),
                tabs: [Tab(text: 'Booking Venue'), Tab(text: 'Survei Gedung')],
              ),
            ),
            Expanded(
              child: TabBarView(
                children: [
                  // TAB 1: Booking Venue
                  RefreshIndicator(
                    onRefresh: _loadData, color: heritageGreen,
                    child: ListView(
                      padding: const EdgeInsets.all(16),
                      children: [
                        if (showBanner) _buildSuccessBanner(bannerMessage, isGreenMode: isBannerGreen),
                        if (bookings.isEmpty) const Center(child: Padding(padding: EdgeInsets.all(40.0), child: Text('Belum ada riwayat booking', style: TextStyle(color: outline)))),
                        ...bookings.map((b) => _buildBookingCard(b)),
                      ],
                    ),
                  ),
                  // TAB 2: Survei Gedung
                  RefreshIndicator(
                    onRefresh: _loadData, color: heritageGreen,
                    child: ListView(
                      padding: const EdgeInsets.all(16),
                      children: [
                        if (showBanner) _buildSuccessBanner(bannerMessage, isGreenMode: isBannerGreen),
                        if (surveys.isEmpty) const Center(child: Padding(padding: EdgeInsets.all(40.0), child: Text('Belum ada riwayat survey', style: TextStyle(color: outline)))),
                        ...surveys.map((s) => _buildSurveyCard(s)),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),

        bottomNavigationBar: Container(
          decoration: BoxDecoration(border: Border(top: BorderSide(color: Colors.grey.shade200, width: 1))),
          child: BottomNavigationBar(
            type: BottomNavigationBarType.fixed, backgroundColor: Colors.white, selectedItemColor: heritageGreen, unselectedItemColor: Colors.grey.shade400, selectedFontSize: 10, unselectedFontSize: 10, currentIndex: 3, 
            onTap: (index) {
              if (index == 0 || index == 1) {
                Navigator.popUntil(context, (route) => route.isFirst);
              } else if (index == 2) {
                Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => BookingPage(baseUrl: widget.baseUrl, token: widget.token, userName: widget.userName, userEmail: widget.userEmail, userPhone: widget.userPhone)));
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
      ),
    );
  }

  // ==========================================
  // WIDGET KARTU BOOKING VENUE
  // ==========================================
  Widget _buildBookingCard(ManageBooking data) {
    final bool isCancelled = data.status == 'cancelled';
    final bool isConfirmedPaid = data.status == 'confirmed' && data.paymentStatus == 'paid';

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: isConfirmedPaid ? const Color(0xFFE8F5E9) : Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: isConfirmedPaid ? const Color(0xFFA5D6A7) : outlineVariant),
      ),
      child: Column(
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 64, height: 64,
                decoration: BoxDecoration(color: const Color(0xFFF4F4EE), borderRadius: BorderRadius.circular(8), border: Border.all(color: surfaceContainerHighest)),
                child: const Center(child: Icon(Icons.location_on, color: Color(0xFF7D562D), size: 28)),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        _buildStatusBadge(data.status),
                        if (!isCancelled) ...[
                          const SizedBox(width: 8),
                          _buildPaymentBadge(data.paymentStatus),
                        ],
                      ],
                    ),
                    const SizedBox(height: 4),
                    Text(data.venueName, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: onSurface), maxLines: 1, overflow: TextOverflow.ellipsis),
                    const SizedBox(height: 2),
                    Text(data.venueLocation, style: const TextStyle(fontSize: 14, color: onSurfaceVariant), maxLines: 1, overflow: TextOverflow.ellipsis),
                  ],
                ),
              ),
            ],
          ),
          const Padding(padding: EdgeInsets.symmetric(vertical: 12), child: Divider(color: surfaceContainerHighest, height: 1)),
          Row(
            children: [
              Expanded(
                child: Row(
                  children: [
                    const Icon(Icons.event, size: 18, color: outline), const SizedBox(width: 4),
                    Text(data.eventDate, style: const TextStyle(fontSize: 13, color: onSurfaceVariant)),
                    const Padding(padding: EdgeInsets.symmetric(horizontal: 6), child: Text('•', style: TextStyle(color: outline))),
                    const Icon(Icons.schedule, size: 18, color: outline), const SizedBox(width: 4),
                    Text('${data.eventTime} - ${data.endTime}', style: const TextStyle(fontSize: 13, color: onSurfaceVariant)),
                  ],
                ),
              ),
            ],
          ),
          if (!isCancelled) ...[
            const SizedBox(height: 16),
            _buildBookingActionButtons(data), 
          ],
        ],
      ),
    );
  }

  // ==========================================
  // WIDGET KARTU SURVEI GEDUNG
  // ==========================================
  Widget _buildSurveyCard(ManageSurvey data) {
    final bool isCancelled = data.status == 'rejected' || data.status == 'cancelled';
    final bool isCompleted = data.status == 'approved' || data.status == 'completed';
    IconData cardIcon = (isCompleted || isCancelled) ? Icons.assignment_turned_in : Icons.assignment;

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12), border: Border.all(color: outlineVariant)),
      child: Column(
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 64, height: 64,
                decoration: BoxDecoration(color: const Color(0xFFF4F4EE), borderRadius: BorderRadius.circular(8), border: Border.all(color: surfaceContainerHighest)),
                child: Center(child: Icon(cardIcon, color: const Color(0xFF7D562D), size: 28)),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(mainAxisAlignment: MainAxisAlignment.end, children: [_buildSurveyStatusBadge(data.status)]),
                    const SizedBox(height: 4),
                    Text(data.venueName, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: onSurface), maxLines: 1, overflow: TextOverflow.ellipsis),
                    const SizedBox(height: 2),
                    Text(data.venueLocation, style: const TextStyle(fontSize: 14, color: onSurfaceVariant), maxLines: 1, overflow: TextOverflow.ellipsis),
                  ],
                ),
              ),
            ],
          ),
          const Padding(padding: EdgeInsets.symmetric(vertical: 12), child: Divider(color: surfaceContainerHighest, height: 1)),
          Row(
            children: [
              Expanded(
                child: Row(
                  children: [
                    const Icon(Icons.calendar_today, size: 18, color: outline), const SizedBox(width: 4),
                    Text(data.proposedDate, style: const TextStyle(fontSize: 13, color: onSurfaceVariant)),
                    const Padding(padding: EdgeInsets.symmetric(horizontal: 6), child: Text('•', style: TextStyle(color: outline))),
                    const Icon(Icons.schedule, size: 18, color: outline), const SizedBox(width: 4),
                    Text('${data.proposedTime} - ${data.endTime}', style: const TextStyle(fontSize: 13, color: onSurfaceVariant)),
                  ],
                ),
              ),
            ],
          ),
          if (!isCancelled && !isCompleted) ...[
            const SizedBox(height: 16),
            _buildSurveyActionButtons(data), 
          ],
        ],
      ),
    );
  }

  // ==========================================
  // TOMBOL AKSI BOOKING & SURVEI
  // ==========================================
  Widget _buildBookingActionButtons(ManageBooking data) {
    if (data.status == 'pending') {
      return Row(
        children: [
          Expanded(
            child: OutlinedButton.icon(
              onPressed: () => _handleCancel('booking', data.id), 
              icon: const Icon(Icons.close, size: 18), label: const Text('Batalkan'),
              style: OutlinedButton.styleFrom(foregroundColor: error, side: const BorderSide(color: error), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)), padding: const EdgeInsets.symmetric(vertical: 12)),
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: OutlinedButton.icon(
              onPressed: () => _showRescheduleDialog('booking', data.id, endTime: data.endTime), 
              icon: const Icon(Icons.update, size: 18), label: const Text('Reschedule'),
              style: OutlinedButton.styleFrom(foregroundColor: heritageGreen, side: const BorderSide(color: heritageGreen), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)), padding: const EdgeInsets.symmetric(vertical: 12)),
            ),
          ),
        ],
      );
    } else if (data.status == 'confirmed' && data.paymentStatus == 'unpaid') {
      return SizedBox(
        width: double.infinity, 
        child: ElevatedButton.icon(
          onPressed: () => _handlePayment(data.id),
          icon: const Icon(Icons.credit_card, size: 18), label: const Text('Bayar'),
          style: ElevatedButton.styleFrom(backgroundColor: blueButton, foregroundColor: Colors.white, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)), padding: const EdgeInsets.symmetric(vertical: 12), elevation: 0),
        ),
      );
    }
    return const SizedBox.shrink();
  }

  Widget _buildSurveyActionButtons(ManageSurvey data) {
    return Row(
      children: [
        Expanded(
          child: OutlinedButton.icon(
            onPressed: () => _handleCancel('survey', data.id), 
            icon: const Icon(Icons.close, size: 18), label: const Text('Batalkan'),
            style: OutlinedButton.styleFrom(foregroundColor: error, backgroundColor: surveyBtnCancelBg, side: const BorderSide(color: errorContainer), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)), padding: const EdgeInsets.symmetric(vertical: 12)),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: OutlinedButton.icon(
            onPressed: () => _showRescheduleDialog('survey', data.id), 
            icon: const Icon(Icons.refresh, size: 18), label: const Text('Reschedule'),
            style: OutlinedButton.styleFrom(foregroundColor: surveyBtnRescheduleText, backgroundColor: surveyBtnRescheduleBg, side: const BorderSide(color: surveyBtnRescheduleBorder), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)), padding: const EdgeInsets.symmetric(vertical: 12)),
          ),
        ),
      ],
    );
  }

  // ==========================================
  // POPUP MODAL RESCHEDULE 
  // ==========================================
  void _showRescheduleDialog(String type, int id, {String? endTime}) {
    DateTime selectedDate = DateTime.now(); 
    TimeOfDay selectedTime = TimeOfDay.now();
    final String preservedEndTime = endTime ?? '17:00';

    showDialog(
      context: context,
      barrierColor: onSurface.withOpacity(0.5),
      builder: (BuildContext context) {
        return StatefulBuilder(
          builder: (context, setStateModal) {
            String formattedDate = '${selectedDate.year}-${selectedDate.month.toString().padLeft(2, '0')}-${selectedDate.day.toString().padLeft(2, '0')}';
            String formattedTime = '${selectedTime.hour.toString().padLeft(2, '0')}:${selectedTime.minute.toString().padLeft(2, '0')}';

            return BackdropFilter(
              filter: ImageFilter.blur(sigmaX: 5, sigmaY: 5),
              child: Dialog(
                backgroundColor: Colors.white, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)), insetPadding: const EdgeInsets.symmetric(horizontal: 24),
                child: Padding(
                  padding: const EdgeInsets.all(32.0),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Container(width: 48, height: 4, decoration: BoxDecoration(color: batikGold.withOpacity(0.3), borderRadius: BorderRadius.circular(8))),
                      const SizedBox(height: 24),
                      const Text('Reschedule', style: TextStyle(color: heritageGreen, fontSize: 24, fontWeight: FontWeight.w700)),
                      const SizedBox(height: 4),
                      const Text('PILIH WAKTU BARU', style: TextStyle(color: outline, fontSize: 11, fontWeight: FontWeight.w500, letterSpacing: 1.5)),
                      const SizedBox(height: 32),
                      
                      _buildInteractiveField(
                        label: 'TANGGAL', value: formattedDate, icon: Icons.calendar_month_outlined,
                        onTap: () async {
                          final DateTime? picked = await showDatePicker(context: context, initialDate: selectedDate, firstDate: DateTime.now(), lastDate: DateTime(2030), builder: (context, child) => Theme(data: Theme.of(context).copyWith(colorScheme: const ColorScheme.light(primary: heritageGreen, onPrimary: Colors.white, onSurface: onSurface)), child: child!));
                          if (picked != null) setStateModal(() => selectedDate = picked);
                        },
                      ),
                      const SizedBox(height: 24),
                      _buildInteractiveField(
                        label: 'WAKTU', value: formattedTime, icon: Icons.schedule_outlined,
                        onTap: () async {
                          final TimeOfDay? picked = await showTimePicker(context: context, initialTime: selectedTime, builder: (context, child) => Theme(data: Theme.of(context).copyWith(colorScheme: const ColorScheme.light(primary: heritageGreen, onPrimary: Colors.white, onSurface: onSurface)), child: child!));
                          if (picked != null) setStateModal(() => selectedTime = picked);
                        },
                      ),
                      const SizedBox(height: 40),
                      
                      Row(
                        children: [
                          Expanded(
                            child: OutlinedButton(
                              onPressed: () => Navigator.pop(context),
                              style: OutlinedButton.styleFrom(foregroundColor: outline, side: const BorderSide(color: outlineVariant), padding: const EdgeInsets.symmetric(vertical: 16), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8))),
                              child: const Text('Batal', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600)),
                            ),
                          ),
                          const SizedBox(width: 16),
                          Expanded(
                            child: ElevatedButton(
                              onPressed: () {
                                Navigator.pop(context);
                                _handleReschedule(
                                  type,
                                  id,
                                  formattedDate,
                                  formattedTime,
                                  endTime: type == 'booking' ? preservedEndTime : null,
                                );
                              },
                              style: ElevatedButton.styleFrom(backgroundColor: heritageGreen, foregroundColor: Colors.white, padding: const EdgeInsets.symmetric(vertical: 16), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)), elevation: 0),
                              child: const Text('Simpan', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600)),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
        );
      },
    );
  }

  Widget _buildInteractiveField({required String label, required String value, required IconData icon, required VoidCallback onTap}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: TextStyle(color: heritageGreen.withOpacity(0.7), fontSize: 12, fontWeight: FontWeight.w500, letterSpacing: 1)),
        const SizedBox(height: 8),
        InkWell(
          onTap: onTap, borderRadius: BorderRadius.circular(8),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12), decoration: BoxDecoration(color: const Color(0xFFFAFAF5), border: Border.all(color: outlineVariant), borderRadius: BorderRadius.circular(8)),
            child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(value, style: const TextStyle(fontSize: 14, color: onSurface)), Icon(icon, color: heritageGreen, size: 20)]),
          ),
        ),
      ],
    );
  }

  // ==========================================
  // WIDGET BANTUAN: LENCANA & BANNER
  // ==========================================
  Widget _buildSuccessBanner(String text, {required bool isGreenMode}) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16), padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: isGreenMode ? primaryFixed : cancelBannerBg, borderRadius: BorderRadius.circular(12), border: Border.all(color: isGreenMode ? outlineVariant : cancelBannerBorder)),
      child: Row(
        children: [
          Icon(isGreenMode ? Icons.check_circle_outline : Icons.check_circle, color: isGreenMode ? heritageGreen : cancelBannerText),
          const SizedBox(width: 12),
          Text(text, style: TextStyle(color: isGreenMode ? onPrimaryFixed : cancelBannerText, fontSize: 14, fontWeight: FontWeight.w500)),
        ],
      ),
    );
  }

  Widget _buildStatusBadge(String status) {
    if (status == 'pending') return _badgeUI('Menunggu', secondaryContainer, onSecondaryContainer);
    if (status == 'cancelled') return _badgeUI('Dibatalkan', surfaceContainerHighest, onSurfaceVariant);
    return _badgeUI('Terkonfirmasi', primaryFixed, onPrimaryFixed);
  }
  
  Widget _buildSurveyStatusBadge(String status) {
    if (status == 'pending') return _badgeUI('Menunggu', secondaryContainer, onSecondaryContainer);
    if (status == 'rejected' || status == 'cancelled') return _badgeUI('Dibatalkan', surfaceContainerHighest, onSurfaceVariant);
    return _badgeUI('Disetujui', surfaceContainer, onSurfaceVariant);
  }

  Widget _buildPaymentBadge(String status) {
    if (status == 'paid') return _badgeUI('Sudah Bayar', primaryFixed, onPrimaryFixed);
    return _badgeUI('Belum Bayar', errorContainer, onErrorContainer);
  }

  Widget _badgeUI(String text, Color bg, Color textCol) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(4)),
      child: Text(text, style: TextStyle(color: textCol, fontSize: 10, fontWeight: FontWeight.w600)),
    );
  }
}