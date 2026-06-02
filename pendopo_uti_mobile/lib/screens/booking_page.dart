import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'manage_page.dart';
import 'ketersediaan_page.dart';
import 'survey_page.dart';

class BookingPackage {
  final int id;
  final String name;
  final String priceLabel;
  final double price;
  final List<String> features;
  final bool isPopular;
  final String colorHex;

  BookingPackage({
    required this.id,
    required this.name,
    required this.priceLabel,
    required this.price,
    required this.features,
    this.isPopular = false,
    this.colorHex = '#D4B15F',
  });

  factory BookingPackage.fromJson(Map<String, dynamic> json) {
    final features = (json['features'] as List?)?.map((e) => e.toString()).toList() ?? <String>[];
    return BookingPackage(
      id: int.tryParse(json['id'].toString()) ?? 0,
      name: json['name']?.toString() ?? '-',
      priceLabel: json['price_label']?.toString() ?? 'Rp 0',
      price: double.tryParse(json['price']?.toString() ?? '0') ?? 0,
      features: features,
      isPopular: json['is_popular'] == true,
      colorHex: json['color']?.toString() ?? '#D4B15F',
    );
  }
}

class BookingApi {
  BookingApi({required this.baseUrl, required this.token});

  final String baseUrl;
  final String token;

  Map<String, String> get _headers => {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      };

  Future<List<BookingPackage>> fetchPackages() async {
    final res = await http.get(
        Uri.parse('$baseUrl/packages'),
        headers: _headers,
    );

    if (res.statusCode != 200) {
        throw Exception('Gagal memuat paket');
    }

    final body = jsonDecode(res.body);
    final list = (body is List) ? body : (body['data'] as List? ?? []);

    return list
        .map((e) => BookingPackage.fromJson(Map<String, dynamic>.from(e as Map)))
        .toList();
  }

  Future<Map<String, dynamic>> createBooking({
    required String fullName,
    required String email,
    required String phone,
    required String eventDate,
    required String eventTime,
    required String endTime,
    required int guestCount,
    required int packageId,
    int venueId = 1,
  }) async {
    final res = await http.post(
      Uri.parse('$baseUrl/bookings'),
      headers: _headers,
      body: {
        'full_name': fullName,
        'email': email,
        'phone': phone,
        'venue_id': venueId.toString(),
        'event_date': eventDate,
        'event_time': eventTime,
        'end_time': endTime,
        'guest_count': guestCount.toString(),
        'package_id': packageId.toString(),
      },
    );

    final decoded = jsonDecode(res.body);
    return {
      'ok': res.statusCode >= 200 && res.statusCode < 300,
      'statusCode': res.statusCode,
      'body': decoded,
    };
  }
}

/// Halaman Booking umum (hub menu) — venue form dibuka dari sini.
class BookingPage extends StatefulWidget {
  const BookingPage({
    super.key,
    required this.baseUrl,
    required this.token,
    required this.userName,
    required this.userEmail,
    required this.userPhone,
    this.openVenueDirectly = false,
  });

  final String baseUrl;
  final String token;
  final String userName;
  final String userEmail;
  final String userPhone;
  final bool openVenueDirectly;

  @override
  State<BookingPage> createState() => _BookingPageRootState();
}

class _BookingPageRootState extends State<BookingPage> {
  late bool _showVenueForm;

  @override
  void initState() {
    super.initState();
    _showVenueForm = widget.openVenueDirectly;
  }

  @override
  Widget build(BuildContext context) {
    if (_showVenueForm) {
      return _BookingVenueFormScreen(
        baseUrl: widget.baseUrl,
        token: widget.token,
        userName: widget.userName,
        userEmail: widget.userEmail,
        userPhone: widget.userPhone,
        onBackToHub: () => setState(() => _showVenueForm = false),
      );
    }

    return _BookingHubScreen(
      baseUrl: widget.baseUrl,
      token: widget.token,
      userName: widget.userName,
      userEmail: widget.userEmail,
      userPhone: widget.userPhone,
      onBookVenue: () => setState(() => _showVenueForm = true),
    );
  }
}

/// Form multi-step booking venue (logika lama).
class _BookingVenueFormScreen extends StatefulWidget {
  const _BookingVenueFormScreen({
    required this.baseUrl,
    required this.token,
    required this.userName,
    required this.userEmail,
    required this.userPhone,
    this.onBackToHub,
  });

  final String baseUrl;
  final String token;
  final String userName;
  final String userEmail;
  final String userPhone;
  final VoidCallback? onBackToHub;

  @override
  State<_BookingVenueFormScreen> createState() => _BookingVenueFormScreenState();
}

class _BookingVenueFormScreenState extends State<_BookingVenueFormScreen> {
  late final BookingApi api;

  int currentStep = 0;
  int selectedPackageIndex = 0;
  bool isLoadingPackages = true;
  bool isSubmitting = false;
  String? errorMessage;

  final fullNameController = TextEditingController();
  final emailController = TextEditingController();
  final phoneController = TextEditingController();
  final eventDateController = TextEditingController();
  final startTimeController = TextEditingController(text: '10:00');
  final endTimeController = TextEditingController(text: '17:00');
  final guestCountController = TextEditingController(text: '300');

  List<BookingPackage> packages = const [];

  @override
  void initState() {
    super.initState();
    api = BookingApi(baseUrl: widget.baseUrl, token: widget.token);
    fullNameController.text = widget.userName;
    emailController.text = widget.userEmail;
    phoneController.text = widget.userPhone;

    _loadPackages();
  }

  @override
  void dispose() {
    fullNameController.dispose();
    emailController.dispose();
    phoneController.dispose();
    eventDateController.dispose();
    startTimeController.dispose();
    endTimeController.dispose();
    guestCountController.dispose();
    super.dispose();
  }

  Future<void> _loadPackages() async {
    try {
        final data = await api.fetchPackages();

        if (!mounted) return;
        setState(() {
        packages = data;
        isLoadingPackages = false;
        errorMessage = null;
        if (packages.isNotEmpty) {
            selectedPackageIndex = 0;
        }
        });
    } catch (e) {
        if (!mounted) return;
        setState(() {
            isLoadingPackages = false;
            errorMessage = 'Gagal memuat paket: $e';
        });
    }
  }

  Future<void> _pickDate() async {
    final now = DateTime.now();
    final picked = await showDatePicker(
      context: context,
      initialDate: now,
      firstDate: now,
      lastDate: DateTime(now.year + 3),
    );
    if (picked != null) {
      eventDateController.text = '${picked.year.toString().padLeft(4, '0')}-${picked.month.toString().padLeft(2, '0')}-${picked.day.toString().padLeft(2, '0')}';
    }
  }

  Future<void> _pickTime(TextEditingController controller) async {
    final now = TimeOfDay.now();
    final picked = await showTimePicker(
      context: context,
      initialTime: now,
    );
    if (picked != null) {
      final formatted = '${picked.hour.toString().padLeft(2, '0')}:${picked.minute.toString().padLeft(2, '0')}';
      controller.text = formatted;
    }
  }

  void nextStep() async {
    if (currentStep == 0) {
      if (fullNameController.text.trim().isEmpty ||
          emailController.text.trim().isEmpty ||
          phoneController.text.trim().isEmpty) {
        _toast('Lengkapi data diri terlebih dahulu.');
        return;
      }
    }

    if (currentStep == 1) {
      if (eventDateController.text.trim().isEmpty ||
          startTimeController.text.trim().isEmpty ||
          endTimeController.text.trim().isEmpty ||
          guestCountController.text.trim().isEmpty) {
        _toast('Lengkapi detail acara terlebih dahulu.');
        return;
      }

      if (startTimeController.text.compareTo(endTimeController.text) >= 0) {
        _toast('Waktu selesai harus setelah waktu mulai.');
        return;
      }
    }

    if (currentStep < 3) {
      setState(() => currentStep++);
      return;
    }

    await submitBooking();
  }

  void previousStep() {
    if (currentStep > 0) {
      setState(() => currentStep--);
    } else if (widget.onBackToHub != null) {
      widget.onBackToHub!();
    } else {
      Navigator.pop(context);
    }
  }

  Future<void> submitBooking() async {
    if (packages.isEmpty) {
      _toast('Paket belum tersedia.');
      return;
    }

    final guestCount = int.tryParse(guestCountController.text.trim()) ?? 0;
    if (guestCount < 1 || guestCount > 300) {
        _toast('Jumlah tamu maksimal 300.');
        return;
    }

    setState(() => isSubmitting = true);

    try {
      final selected = packages[selectedPackageIndex];
      final result = await api.createBooking(
        fullName: fullNameController.text.trim(),
        email: emailController.text.trim(),
        phone: phoneController.text.trim(),
        eventDate: eventDateController.text.trim(),
        eventTime: startTimeController.text.trim(),
        endTime: endTimeController.text.trim(),
        guestCount: int.tryParse(guestCountController.text.trim()) ?? 0,
        packageId: selected.id,
        venueId: 1,
      );
      debugPrint('SELECTED PACKAGE ID = ${selected.id}');

      if (!mounted) return;

      final body = result['body'] as Map<String, dynamic>? ?? {};
      final message = body['message']?.toString() ?? 'Booking berhasil dibuat.';

      if (result['ok'] == true) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(message)),
        );
        if (widget.onBackToHub != null) {
          widget.onBackToHub!();
        } else {
          Navigator.pop(context);
        }
      } else {
        final errors = body['errors'];
        if (errors is Map && errors.isNotEmpty) {
          final firstError = errors.values.first;
          if (firstError is List && firstError.isNotEmpty) {
            _toast(firstError.first.toString());
          } else {
            _toast(message);
          }
        } else {
          _toast(message);
        }
      }
    } catch (e) {
      if (!mounted) return;
      _toast('Terjadi error: $e');
    } finally {
      if (mounted) {
        setState(() => isSubmitting = false);
      }
    }
  }

  void _toast(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message)),
    );
  }

  String get _selectedPackageName {
    if (packages.isEmpty) return '-';
    return packages[selectedPackageIndex].name;
  }

  String get _selectedPackagePriceLabel {
    if (packages.isEmpty) return '-';
    return packages[selectedPackageIndex].priceLabel;
  }

  String get _selectedPackageTotal {
    if (packages.isEmpty) return 'Rp 0';
    final price = packages[selectedPackageIndex].price.toStringAsFixed(0).replaceAllMapped(
          RegExp(r'\B(?=(\d{3})+(?!\d))'),
          (m) => '.',
        );
    return 'Rp $price';
  }

  Widget _buildStepIndicator() {
    const titles = ['Data Diri', 'Detail Acara', 'Paket', 'Konfirmasi'];
    final themeColor = const Color(0xFFD4B15F);
    final inactiveColor = const Color(0xFFE6DCCF);

    return Row(
      children: List.generate(4, (index) {
        final active = index <= currentStep;
        final current = index == currentStep;

        return Expanded(
          child: Row(
            children: [
              Expanded(
                child: Column(
                  children: [
                    Container(
                      width: 48,
                      height: 48,
                      decoration: BoxDecoration(
                        color: active ? themeColor : inactiveColor,
                        shape: BoxShape.circle,
                      ),
                      alignment: Alignment.center,
                      child: Text(
                        index < currentStep ? '✓' : '${index + 1}',
                        style: TextStyle(
                          color: active ? Colors.white : const Color(0xFF9B8E7C),
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                    ),
                    const SizedBox(height: 10),
                    Text(
                      titles[index],
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: current ? FontWeight.w700 : FontWeight.w500,
                        color: current ? const Color(0xFF0B3B34) : const Color(0xFF8C8170),
                      ),
                    ),
                  ],
                ),
              ),
              if (index != 3)
                Container(
                  width: 38,
                  height: 2,
                  margin: const EdgeInsets.only(bottom: 34),
                  color: index < currentStep ? themeColor : inactiveColor,
                ),
            ],
          ),
        );
      }),
    );
  }

  InputDecoration _inputDecoration({Widget? suffixIcon}) {
    return InputDecoration(
      filled: true,
      fillColor: const Color(0xFFFCFAF7),
      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: Color(0xFFE3D6C4)),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: Color(0xFFD4B15F), width: 1.5),
      ),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 18),
      suffixIcon: suffixIcon,
    );
  }

  Widget _labelField(String label, TextEditingController controller,
      {bool readOnly = false, VoidCallback? onTap, Widget? suffixIcon}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700),
        ),
        const SizedBox(height: 10),
        TextField(
          controller: controller,
          readOnly: readOnly,
          onTap: onTap,
          decoration: _inputDecoration(suffixIcon: suffixIcon),
        ),
      ],
    );
  }

  Widget _buildPackageCard(int index) {
    final item = packages[index];
    final selected = selectedPackageIndex == index;

    return GestureDetector(
        onTap: () {
        setState(() {
            selectedPackageIndex = index;
        });
        },

        child: Container(
        width: double.infinity,
        margin: const EdgeInsets.symmetric(horizontal: 6),

        padding: const EdgeInsets.fromLTRB(14, 18, 14, 16),

        decoration: BoxDecoration(
            color: Colors.white,

            borderRadius: BorderRadius.circular(16),

            border: Border.all(
            color: selected
                ? const Color(0xFFD4B15F)
                : const Color(0xFFE3D8C7),

            width: selected ? 2 : 1,
            ),

            boxShadow: [
            BoxShadow(
                color: Colors.black.withOpacity(0.04),

                blurRadius: 16,

                offset: const Offset(0, 8),
            ),
            ],
        ),

        child: Column(
            mainAxisSize: MainAxisSize.min,

            children: [
            if (item.isPopular)
                Container(
                padding: const EdgeInsets.symmetric(
                    horizontal: 14,
                    vertical: 4,
                ),

                decoration: BoxDecoration(
                    color: const Color(0xFFD4B15F),

                    borderRadius: BorderRadius.circular(999),
                ),

                child: const Text(
                    'Popular',

                    style: TextStyle(
                    color: Colors.white,
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                    ),
                ),
                ),

            if (item.isPopular)
                const SizedBox(height: 8),

            Text(
                item.name,

                textAlign: TextAlign.center,

                style: const TextStyle(
                fontWeight: FontWeight.w700,
                fontSize: 14,
                color: Color(0xFF5A5A5A),
                ),
            ),

            const SizedBox(height: 8),

            Text(
                item.priceLabel,

                style: const TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.w800,
                color: Color(0xFFD4B15F),
                ),
            ),

            const SizedBox(height: 14),

            ...item.features.map(
                (feature) => Padding(
                padding: const EdgeInsets.only(bottom: 8),

                child: Row(
                    crossAxisAlignment: CrossAxisAlignment.start,

                    children: [
                    const Text(
                        '✓ ',
                        style: TextStyle(
                        color: Color(0xFF0B3B34),
                        ),
                    ),

                    Expanded(
                        child: Text(
                        feature,

                        style: const TextStyle(
                            fontSize: 13,
                            height: 1.3,
                        ),
                        ),
                    ),
                    ],
                ),
                ),
            ),

            const SizedBox(height: 16),

            SizedBox(
                width: double.infinity,

                child: OutlinedButton(
                onPressed: () {
                    setState(() {
                    selectedPackageIndex = index;
                    });
                },

                style: OutlinedButton.styleFrom(
                    side: BorderSide(
                    color: selected
                        ? const Color(0xFFD4B15F)
                        : const Color(0xFFCBB89A),
                    ),

                    foregroundColor: const Color(0xFF0B3B34),

                    shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10),
                    ),

                    padding: const EdgeInsets.symmetric(vertical: 14),
                ),

                child: Text(
                    selected ? '✓ Dipilih' : 'Pilih Paket',
                ),
                ),
            ),
            ],
        ),
        ),
    );
  }

  Widget _buildSummaryRow(String label, String value, {bool emphasize = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 12),
      child: Row(
        children: [
          Text(
            label,
            style: TextStyle(
              fontSize: 15,
              color: emphasize ? const Color(0xFFD4B15F) : const Color(0xFF6C6C6C),
              fontWeight: emphasize ? FontWeight.w700 : FontWeight.w500,
            ),
          ),
          const Spacer(),
          Text(
            value,
            style: TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.w700,
              color: emphasize ? const Color(0xFF0B3B34) : const Color(0xFF1E1E1E),
            ),
          ),
        ],
      ),
    );
  }

  Widget _stepBody() {
    if (isLoadingPackages) {
      return const Padding(
        padding: EdgeInsets.symmetric(vertical: 80),
        child: Center(child: CircularProgressIndicator()),
      );
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        _buildStepIndicator(),
        const SizedBox(height: 34),
        Text(
          currentStep == 0
              ? 'Isi Data Diri'
              : currentStep == 1
                  ? 'Detail Acara'
                  : currentStep == 2
                      ? 'Pilih Paket'
                      : 'Konfirmasi Booking',
          textAlign: TextAlign.center,
          style: const TextStyle(
            fontSize: 30,
            fontWeight: FontWeight.w500,
            color: Color(0xFF0B3B34),
          ),
        ),
        const SizedBox(height: 10),
        Text(
          currentStep == 0
              ? 'Masukkan informasi lengkap Anda untuk memulai booking pernikahan.'
              : currentStep == 1
                  ? 'Atur detail acara pernikahan Anda di bawah ini.'
                  : currentStep == 2
                      ? 'Silakan pilih paket pernikahan sesuai kebutuhan Anda.'
                      : 'Tinjau kembali detail pesanan Anda sebelum konfirmasi.',
          textAlign: TextAlign.center,
          style: const TextStyle(fontSize: 15, color: Color(0xFF7F7A72)),
        ),
        if (errorMessage != null) ...[
          const SizedBox(height: 16),
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: const Color(0xFFFFF5E3),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Text(
              errorMessage!,
              style: const TextStyle(fontSize: 13, color: Color(0xFF8A6C2E)),
            ),
          ),
        ],
        const SizedBox(height: 28),
        if (currentStep == 0) ...[
          _labelField('Nama Lengkap', fullNameController),
          const SizedBox(height: 18),
          _labelField('Email', emailController),
          const SizedBox(height: 18),
          _labelField('Nomor HP', phoneController),
          const SizedBox(height: 14),
          const Text(
            'Data diri diambil dari akun Anda.',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 13, color: Color(0xFF8E857B)),
          ),
        ],
        if (currentStep == 1) ...[
          _labelField(
            'Tanggal Pernikahan',
            eventDateController,
            readOnly: true,
            onTap: _pickDate,
            suffixIcon: const Icon(Icons.calendar_month_outlined),
          ),
          const SizedBox(height: 18),
          Row(
            children: [
              Expanded(
                child: _labelField(
                  'Waktu Mulai',
                  startTimeController,
                  readOnly: true,
                  onTap: () => _pickTime(startTimeController),
                  suffixIcon: const Icon(Icons.access_time),
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: _labelField(
                  'Waktu Selesai',
                  endTimeController,
                  readOnly: true,
                  onTap: () => _pickTime(endTimeController),
                  suffixIcon: const Icon(Icons.access_time),
                ),
              ),
            ],
          ),
          const SizedBox(height: 18),
          _labelField('Jumlah Tamu', guestCountController),
          const SizedBox(height: 14),
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: const Color(0xFFFFF5E3),
              borderRadius: BorderRadius.circular(10),
            ),
            child: const Text(
              '⏰ Jam operasional venue: 07:00 - 22:00. Maksimal 2 booking per tanggal.',
              style: TextStyle(fontSize: 13, color: Color(0xFF8A6C2E)),
            ),
          ),
        ],
        if (currentStep == 2) ...[
            Column(
                children: [
                    for (int i = 0; i < packages.length; i++) ...[
                        _buildPackageCard(i),
                        if (i != packages.length - 1) const SizedBox(height: 14),
                    ],
                ],
            ),
        ],
        if (currentStep == 3) ...[
          Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              color: const Color(0xFFFBF8F3),
              borderRadius: BorderRadius.circular(18),
              border: Border.all(color: const Color(0xFFE2C278), width: 1.5),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'RINGKASAN PESANAN',
                  style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700, letterSpacing: 0.3),
                ),
                const SizedBox(height: 18),
                _buildSummaryRow('🏛 Venue', 'Pendopo Utama UTI'),
                const Divider(height: 1),
                _buildSummaryRow('📅 Tanggal', eventDateController.text.isEmpty ? '-' : eventDateController.text),
                const Divider(height: 1),
                _buildSummaryRow('⏰ Jam', '${startTimeController.text} - ${endTimeController.text}'),
                const Divider(height: 1),
                _buildSummaryRow('👥 Jumlah Tamu', guestCountController.text),
                const Divider(height: 1),
                _buildSummaryRow('💎 Paket', _selectedPackageName),
              ],
            ),
          ),
          const SizedBox(height: 18),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 20),
            decoration: BoxDecoration(
              color: const Color(0xFFF8F1E3),
              borderRadius: BorderRadius.circular(18),
              border: Border.all(color: const Color(0xFFE2C278), width: 1.2),
            ),
            child: Row(
              children: [
                const Text(
                  'TOTAL',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800),
                ),
                const Spacer(),
                Text(
                  _selectedPackageTotal,
                  style: const TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFFD4B15F),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 18),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: const Color(0xFFDDEEFF),
              borderRadius: BorderRadius.circular(14),
            ),
            child: const Text(
              '📘 Cara Pembayaran:\nSetelah booking dibuat, admin akan meninjau pesanan Anda. Setelah disetujui admin, Anda dapat melakukan pembayaran via Midtrans.',
              style: TextStyle(fontSize: 14, height: 1.5, color: Color(0xFF0A3159)),
            ),
          ),
        ],
        const SizedBox(height: 28),
        Row(
          children: [
            Expanded(
              child: OutlinedButton(
                onPressed: isSubmitting ? null : previousStep,
                style: OutlinedButton.styleFrom(
                  side: const BorderSide(color: Color(0xFFD4B15F)),
                  foregroundColor: const Color(0xFFD4B15F),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                  padding: const EdgeInsets.symmetric(vertical: 18),
                ),
                child: const Text('← Kembali'),
              ),
            ),
            const SizedBox(width: 14),
            Expanded(
              flex: 2,
              child: ElevatedButton(
                onPressed: isSubmitting ? null : nextStep,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFD4B15F),
                  foregroundColor: Colors.white,
                  elevation: 0,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                  padding: const EdgeInsets.symmetric(vertical: 18),
                ),
                child: isSubmitting
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                      )
                    : Text(
                        currentStep == 3 ? '✓ Konfirmasi Booking' : 'Lanjut →',
                        style: const TextStyle(fontWeight: FontWeight.w700),
                      ),
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        Text(
          'Langkah ${currentStep + 1} dari 4',
          textAlign: TextAlign.center,
          style: const TextStyle(color: Color(0xFF8E857B), fontSize: 13),
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF7F3EE),
      appBar: AppBar(
        elevation: 0,
        backgroundColor: const Color(0xFFF7F3EE),
        centerTitle: false,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Color(0xFF0B3B34)),
          onPressed: previousStep,
        ),
        title: const Text(
          'Booking Venue',
          style: TextStyle(
            fontWeight: FontWeight.w800,
            color: Color(0xFF0B3B34),
            letterSpacing: 0.5,
          ),
        ),
      ),
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 980),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 28),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(28),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.06),
                      blurRadius: 30,
                      offset: const Offset(0, 14),
                    ),
                  ],
                ),
                child: _stepBody(),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

// =============================================================================
// BOOKING HUB — menu umum (Figma)
// =============================================================================
class _BookingHubScreen extends StatelessWidget {
  const _BookingHubScreen({
    required this.baseUrl,
    required this.token,
    required this.userName,
    required this.userEmail,
    required this.userPhone,
    required this.onBookVenue,
  });

  final String baseUrl;
  final String token;
  final String userName;
  final String userEmail;
  final String userPhone;
  final VoidCallback onBookVenue;

  static const Color ricePaper = Color(0xFFFAFAF5);
  static const Color heritageGreen = Color(0xFF2D4B37);
  static const Color batikGold = Color(0xFFD4A373);
  static const Color onSurfaceVariant = Color(0xFF424843);
  static const Color outlineVariant = Color(0xFFC2C8C0);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: ricePaper,
      appBar: AppBar(
        backgroundColor: ricePaper,
        elevation: 0,
        scrolledUnderElevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.menu, color: heritageGreen),
          onPressed: () {},
        ),
        title: const Text(
          'Pendopo Uti',
          style: TextStyle(
            fontFamily: 'serif',
            fontSize: 22,
            fontWeight: FontWeight.w600,
            color: heritageGreen,
          ),
        ),
        centerTitle: true,
        actions: [
          IconButton(
            icon: const Icon(Icons.search, color: heritageGreen),
            onPressed: () {},
          ),
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(height: 1, color: outlineVariant.withOpacity(0.3)),
        ),
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            SizedBox(
              height: 420,
              child: Stack(
                fit: StackFit.expand,
                children: [
                  Image.asset(
                    'assets/images/hero.png',
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => Container(color: heritageGreen),
                  ),
                  Container(color: Colors.black.withOpacity(0.4)),
                  Positioned(
                    left: 20,
                    right: 20,
                    bottom: 48,
                    child: Column(
                      children: [
                        Text(
                          'Plan Your',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            fontFamily: 'serif',
                            fontSize: 36,
                            fontWeight: FontWeight.w600,
                            color: batikGold,
                            height: 1.1,
                            shadows: [
                              Shadow(
                                color: Colors.black.withOpacity(0.35),
                                blurRadius: 8,
                              ),
                            ],
                          ),
                        ),
                        const Text(
                          'Dream Wedding',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            fontFamily: 'serif',
                            fontSize: 36,
                            fontWeight: FontWeight.w700,
                            color: Colors.white,
                            height: 1.1,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 32, 20, 16),
              child: Column(
                children: [
                  _goldDivider(context),
                  const SizedBox(height: 16),
                  _menuCard(
                    icon: Icons.calendar_month,
                    title: 'Lihat Ketersediaan Tanggal',
                    description:
                        'Cek jadwal kosong untuk hari bahagia Anda secara real-time dan kunci tanggal favorit Anda segera.',
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => KetersediaanPage(baseUrl: baseUrl),
                        ),
                      );
                    },
                  ),
                  const SizedBox(height: 16),
                  _menuCard(
                    icon: Icons.apartment,
                    title: 'Book a Venue',
                    description:
                        'Pilih area pendopo atau taman sesuai tema pernikahan Anda.',
                    onTap: onBookVenue,
                  ),
                  const SizedBox(height: 16),
                  _menuCard(
                    icon: Icons.assignment,
                    title: 'Book a Survey',
                    description:
                        'Konsultasikan konsep pernikahan Anda langsung di lokasi bersama tim kami.',
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => SurveyPage(
                            baseUrl: baseUrl,
                            token: token,
                            userName: userName,
                            userEmail: userEmail,
                            userPhone: userPhone,
                          ),
                        ),
                      );
                    },
                  ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 24, 20, 32),
              child: Column(
                children: [
                  _goldDivider(context),
                  const SizedBox(height: 32),
                  GridView.count(
                    crossAxisCount: 2,
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    mainAxisSpacing: 32,
                    crossAxisSpacing: 16,
                    childAspectRatio: 1.6,
                    children: const [
                      _StatTile(value: '150+', label: 'Weddings Hosted'),
                      _StatTile(value: '1k+', label: 'Happy Guests'),
                      _StatTile(value: '24/7', label: 'Online Booking'),
                      _StatTile(value: '4.9', label: 'Avg. Rating'),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 88),
          ],
        ),
      ),
      bottomNavigationBar: _BookingBottomNav(
        currentIndex: 2,
        baseUrl: baseUrl,
        token: token,
        userName: userName,
        userEmail: userEmail,
        userPhone: userPhone,
        onFacilities: () => Navigator.pop(context),
      ),
    );
  }

  Widget _goldDivider(BuildContext context) {
    return Center(
      child: Container(
        width: MediaQuery.sizeOf(context).width * 0.66,
        height: 1,
        color: batikGold.withOpacity(0.3),
      ),
    );
  }

  Widget _menuCard({
    required IconData icon,
    required String title,
    required String description,
    required VoidCallback onTap,
  }) {
    return Material(
      color: ricePaper,
      borderRadius: BorderRadius.circular(12),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: ricePaper,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: batikGold.withOpacity(0.4)),
            boxShadow: [
              BoxShadow(
                color: heritageGreen.withOpacity(0.08),
                blurRadius: 12,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Row(
            children: [
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: heritageGreen,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Icon(icon, color: ricePaper, size: 26),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: const TextStyle(
                        fontFamily: 'serif',
                        fontSize: 17,
                        fontWeight: FontWeight.w600,
                        color: heritageGreen,
                        letterSpacing: 0.5,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      description,
                      style: const TextStyle(
                        fontSize: 13,
                        height: 1.4,
                        color: onSurfaceVariant,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              ),
              const Icon(Icons.chevron_right, color: heritageGreen),
            ],
          ),
        ),
      ),
    );
  }
}

class _StatTile extends StatelessWidget {
  const _StatTile({required this.value, required this.label});

  final String value;
  final String label;

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Text(
          value,
          style: const TextStyle(
            fontFamily: 'serif',
            fontSize: 28,
            fontWeight: FontWeight.w600,
            color: _BookingHubScreen.heritageGreen,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          label.toUpperCase(),
          textAlign: TextAlign.center,
          style: const TextStyle(
            fontSize: 11,
            letterSpacing: 0.8,
            fontWeight: FontWeight.w500,
            color: _BookingHubScreen.onSurfaceVariant,
          ),
        ),
      ],
    );
  }
}

class _BookingBottomNav extends StatelessWidget {
  const _BookingBottomNav({
    required this.currentIndex,
    required this.baseUrl,
    required this.token,
    required this.userName,
    required this.userEmail,
    required this.userPhone,
    this.onFacilities,
  });

  final int currentIndex;
  final String baseUrl;
  final String token;
  final String userName;
  final String userEmail;
  final String userPhone;
  final VoidCallback? onFacilities;

  @override
  Widget build(BuildContext context) {
    const heritageGreen = _BookingHubScreen.heritageGreen;

    return Container(
      decoration: BoxDecoration(
        border: Border(top: BorderSide(color: Colors.grey.shade200)),
      ),
      child: SafeArea(
        child: SizedBox(
          height: 72,
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _navItem(context, 0, Icons.home_rounded, 'Home', currentIndex == 0, heritageGreen, () {
                Navigator.popUntil(context, (route) => route.isFirst);
              }),
              _navItem(context, 1, Icons.apartment, 'Facilities', currentIndex == 1, heritageGreen, () {
                if (onFacilities != null) {
                  onFacilities!();
                } else {
                  Navigator.popUntil(context, (route) => route.isFirst);
                }
              }),
              _navItem(context, 2, Icons.calendar_month_rounded, 'Booking', currentIndex == 2, heritageGreen, () {}),
              _navItem(context, 3, Icons.dashboard_rounded, 'Manage', currentIndex == 3, heritageGreen, () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => ManagePage(
                      baseUrl: baseUrl,
                      token: token,
                      userName: userName,
                      userEmail: userEmail,
                      userPhone: userPhone,
                    ),
                  ),
                );
              }),
            ],
          ),
        ),
      ),
    );
  }

  Widget _navItem(
    BuildContext context,
    int index,
    IconData icon,
    String label,
    bool selected,
    Color activeColor,
    VoidCallback onTap,
  ) {
    return InkWell(
      onTap: onTap,
      child: SizedBox(
        width: 72,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
              decoration: selected
                  ? BoxDecoration(
                      color: const Color(0xFFE7F3E8),
                      borderRadius: BorderRadius.circular(24),
                    )
                  : null,
              child: Icon(icon, size: 24, color: selected ? activeColor : Colors.grey.shade400),
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: TextStyle(
                fontSize: 10,
                fontWeight: selected ? FontWeight.bold : FontWeight.w500,
                color: selected ? activeColor : Colors.grey.shade400,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
