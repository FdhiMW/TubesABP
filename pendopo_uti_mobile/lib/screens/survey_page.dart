import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

class SurveyApi {
  SurveyApi({required this.baseUrl, required this.token});

  final String baseUrl;
  final String token;

  Map<String, String> get _headers => {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      };

  Future<String> createSurvey({
    required String proposedDate,
    required String proposedTime,
    String? notes,
    int venueId = 1,
  }) async {
    final res = await http.post(
      Uri.parse('$baseUrl/surveys'),
      headers: _headers,
      body: {
        'venue_id': venueId.toString(),
        'proposed_date': proposedDate,
        'proposed_time': proposedTime,
        if (notes != null && notes.isNotEmpty) 'notes': notes,
      },
    );

    dynamic body;
    try {
      body = jsonDecode(res.body);
    } catch (_) {}

    if (res.statusCode >= 200 && res.statusCode < 300) {
      if (body is Map && body['message'] != null) {
        return body['message'].toString();
      }
      return 'Survey berhasil dibooking!';
    }

    if (body is Map) {
      if (body['message'] != null) return body['message'].toString();
      final errors = body['errors'];
      if (errors is Map && errors.isNotEmpty) {
        final first = errors.values.first;
        if (first is List && first.isNotEmpty) return first.first.toString();
      }
    }

    throw Exception('Gagal membuat survey (HTTP ${res.statusCode})');
  }
}

class SurveyPage extends StatefulWidget {
  const SurveyPage({
    super.key,
    required this.baseUrl,
    required this.token,
    required this.userName,
    required this.userEmail,
    required this.userPhone,
  });

  final String baseUrl;
  final String token;
  final String userName;
  final String userEmail;
  final String userPhone;

  @override
  State<SurveyPage> createState() => _SurveyPageState();
}

class _SurveyPageState extends State<SurveyPage> {
  static const Color ricePaper = Color(0xFFFAFAF5);
  static const Color heritageGreen = Color(0xFF2D4B37);
  static const Color batikGold = Color(0xFFD4A373);
  static const Color onSurface = Color(0xFF1A1C19);
  static const Color onSurfaceVariant = Color(0xFF424843);
  static const Color outlineVariant = Color(0xFFC2C8C0);
  static const Color surfaceVariant = Color(0xFFE3E3DE);

  late final SurveyApi api;
  int currentStep = 0;
  bool isSubmitting = false;

  final nameController = TextEditingController();
  final emailController = TextEditingController();
  final phoneController = TextEditingController();
  final proposedDateController = TextEditingController();
  final proposedTimeController = TextEditingController(text: '10:00');
  final notesController = TextEditingController();

  @override
  void initState() {
    super.initState();
    api = SurveyApi(baseUrl: widget.baseUrl, token: widget.token);
    nameController.text = widget.userName;
    emailController.text = widget.userEmail;
    phoneController.text = widget.userPhone;
  }

  @override
  void dispose() {
    nameController.dispose();
    emailController.dispose();
    phoneController.dispose();
    proposedDateController.dispose();
    proposedTimeController.dispose();
    notesController.dispose();
    super.dispose();
  }

  Future<void> _pickDate() async {
    final now = DateTime.now();
    final picked = await showDatePicker(
      context: context,
      initialDate: now,
      firstDate: now,
      lastDate: DateTime(now.year + 3),
      builder: (context, child) => Theme(
        data: Theme.of(context).copyWith(
          colorScheme: const ColorScheme.light(
            primary: heritageGreen,
            onPrimary: Colors.white,
            onSurface: onSurface,
          ),
        ),
        child: child!,
      ),
    );
    if (picked != null) {
      proposedDateController.text =
          '${picked.year}-${picked.month.toString().padLeft(2, '0')}-${picked.day.toString().padLeft(2, '0')}';
    }
  }

  Future<void> _pickTime() async {
    final parts = proposedTimeController.text.split(':');
    final initial = TimeOfDay(
      hour: int.tryParse(parts.first) ?? 10,
      minute: int.tryParse(parts.length > 1 ? parts[1] : '0') ?? 0,
    );
    final picked = await showTimePicker(
      context: context,
      initialTime: initial,
      builder: (context, child) => Theme(
        data: Theme.of(context).copyWith(
          colorScheme: const ColorScheme.light(
            primary: heritageGreen,
            onPrimary: Colors.white,
            onSurface: onSurface,
          ),
        ),
        child: child!,
      ),
    );
    if (picked != null) {
      proposedTimeController.text =
          '${picked.hour.toString().padLeft(2, '0')}:${picked.minute.toString().padLeft(2, '0')}';
    }
  }

  void _toast(String message) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(message)));
  }

  void _handleBack() {
    if (currentStep > 0) {
      setState(() => currentStep--);
    } else {
      Navigator.pop(context);
    }
  }

  Future<void> _handleNext() async {
    if (currentStep == 0) {
      setState(() => currentStep = 1);
      return;
    }

    if (proposedDateController.text.trim().isEmpty ||
        proposedTimeController.text.trim().isEmpty) {
      _toast('Lengkapi tanggal dan waktu survey.');
      return;
    }

    setState(() => isSubmitting = true);
    try {
      final message = await api.createSurvey(
        proposedDate: proposedDateController.text.trim(),
        proposedTime: proposedTimeController.text.trim(),
        notes: notesController.text.trim(),
      );
      if (!mounted) return;
      _toast(message);
      Navigator.pop(context);
    } catch (e) {
      if (!mounted) return;
      _toast(e.toString().replaceFirst('Exception: ', ''));
    } finally {
      if (mounted) setState(() => isSubmitting = false);
    }
  }

  Widget _buildStepper() {
    return Row(
      children: [
        Expanded(child: _stepCircle(0, 'Data Diri')),
        Expanded(
          child: Container(
            height: 1,
            margin: const EdgeInsets.only(bottom: 28),
            color: outlineVariant,
          ),
        ),
        Expanded(child: _stepCircle(1, 'Detail Acara')),
      ],
    );
  }

  Widget _stepCircle(int index, String label) {
    final active = currentStep == index;
    final done = currentStep > index;
    final bg = active || done ? batikGold : surfaceVariant;
    final fg = active || done ? Colors.white : onSurfaceVariant;

    return Column(
      children: [
        Container(
          width: 48,
          height: 48,
          decoration: BoxDecoration(color: bg, shape: BoxShape.circle, boxShadow: active ? [BoxShadow(color: batikGold.withValues(alpha: 0.35), blurRadius: 8)] : null),
          alignment: Alignment.center,
          child: Text(
            done ? '✓' : '${index + 1}',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600, color: fg),
          ),
        ),
        const SizedBox(height: 8),
        Text(
          label,
          textAlign: TextAlign.center,
          style: TextStyle(
            fontSize: 12,
            fontWeight: active ? FontWeight.w600 : FontWeight.w500,
            color: active ? onSurface : onSurfaceVariant,
          ),
        ),
      ],
    );
  }

  Widget _buildField({
    required String label,
    required TextEditingController controller,
    bool readOnly = false,
    VoidCallback? onTap,
    int maxLines = 1,
    TextInputType? keyboardType,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: onSurface)),
        const SizedBox(height: 4),
        TextField(
          controller: controller,
          readOnly: readOnly,
          onTap: onTap,
          maxLines: maxLines,
          keyboardType: keyboardType,
          style: const TextStyle(fontSize: 14, color: onSurface),
          decoration: InputDecoration(
            filled: true,
            fillColor: ricePaper,
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: const BorderSide(color: outlineVariant)),
            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: const BorderSide(color: outlineVariant)),
            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: const BorderSide(color: heritageGreen, width: 1.5)),
          ),
        ),
      ],
    );
  }

  Widget _buildFormCard() {
    final isStep1 = currentStep == 0;

    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: outlineVariant.withValues(alpha: 0.6)),
        boxShadow: [
          BoxShadow(
            color: heritageGreen.withValues(alpha: 0.06),
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          _buildStepper(),
          const SizedBox(height: 32),
          Text(
            isStep1 ? 'Isi Data Diri' : 'Isi Detail Acara',
            textAlign: TextAlign.center,
            style: const TextStyle(
              fontFamily: 'serif',
              fontSize: 28,
              fontWeight: FontWeight.w700,
              color: heritageGreen,
              height: 1.15,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            isStep1
                ? 'Masukkan informasi lengkap Anda untuk memulai booking pernikahan.'
                : 'Atur jadwal survei gedung di lokasi venue.',
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 14, height: 1.45, color: onSurfaceVariant),
          ),
          const SizedBox(height: 28),
          if (isStep1) ...[
            _buildField(label: 'Nama Pengantin', controller: nameController, readOnly: true),
            const SizedBox(height: 20),
            _buildField(label: 'Email', controller: emailController, readOnly: true, keyboardType: TextInputType.emailAddress),
            const SizedBox(height: 20),
            _buildField(label: 'No Telepon / WhatsApp', controller: phoneController, readOnly: true, keyboardType: TextInputType.phone),
            const SizedBox(height: 8),
            const Text(
              'Data diri diambil dari akun Anda.',
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 12, color: onSurfaceVariant),
            ),
          ] else ...[
            _buildField(
              label: 'Tanggal Survey',
              controller: proposedDateController,
              readOnly: true,
              onTap: _pickDate,
            ),
            const SizedBox(height: 20),
            _buildField(
              label: 'Waktu Survey',
              controller: proposedTimeController,
              readOnly: true,
              onTap: _pickTime,
            ),
            const SizedBox(height: 20),
            _buildField(label: 'Catatan (Opsional)', controller: notesController, maxLines: 3),
            const SizedBox(height: 12),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFFFF5E3),
                borderRadius: BorderRadius.circular(8),
              ),
              child: const Text(
                '⏰ Jam operasional: 07:00–22:00. Durasi survei ±1 jam.',
                style: TextStyle(fontSize: 12, color: Color(0xFF8A6C2E), height: 1.4),
              ),
            ),
          ],
          const SizedBox(height: 28),
          Row(
            children: [
              Expanded(
                child: OutlinedButton(
                  onPressed: isSubmitting ? null : _handleBack,
                  style: OutlinedButton.styleFrom(
                    foregroundColor: batikGold,
                    side: const BorderSide(color: batikGold),
                    minimumSize: const Size(0, 48),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                  ),
                  child: const Text('← Kembali', style: TextStyle(fontWeight: FontWeight.w600)),
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: ElevatedButton(
                  onPressed: isSubmitting ? null : _handleNext,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: batikGold,
                    foregroundColor: Colors.white,
                    minimumSize: const Size(0, 48),
                    elevation: 0,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                  ),
                  child: isSubmitting
                      ? const SizedBox(
                          width: 22,
                          height: 22,
                          child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                        )
                      : Text(
                          isStep1 ? 'Lanjut →' : '✓ Booking Sekarang →',
                          style: const TextStyle(fontWeight: FontWeight.w600),
                        ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            'Langkah ${currentStep + 1} dari 2',
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 11, letterSpacing: 0.5, color: onSurfaceVariant),
          ),
        ],
      ),
    );
  }

  Widget _buildSideImage() {
    return ClipRRect(
      borderRadius: BorderRadius.circular(16),
      child: Image.asset(
        'assets/images/examwedding.png',
        fit: BoxFit.cover,
        errorBuilder: (_, __, ___) => Container(
          color: surfaceVariant,
          alignment: Alignment.center,
          child: const Icon(Icons.image_outlined, size: 64, color: outlineVariant),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: ricePaper,
      appBar: AppBar(
        backgroundColor: ricePaper,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: heritageGreen),
          onPressed: _handleBack,
        ),
        title: const Text(
          'Book a Survey',
          style: TextStyle(
            fontFamily: 'serif',
            fontSize: 18,
            fontWeight: FontWeight.w600,
            color: heritageGreen,
          ),
        ),
        centerTitle: true,
      ),
      body: LayoutBuilder(
        builder: (context, constraints) {
          final wide = constraints.maxWidth >= 720;

          return SingleChildScrollView(
            padding: const EdgeInsets.fromLTRB(20, 8, 20, 32),
            child: Center(
              child: ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 1000),
                child: wide
                    ? Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Expanded(flex: 5, child: _buildFormCard()),
                          const SizedBox(width: 32),
                          Expanded(
                            flex: 4,
                            child: SizedBox(height: 620, child: _buildSideImage()),
                          ),
                        ],
                      )
                    : Column(
                        children: [
                          _buildFormCard(),
                          const SizedBox(height: 24),
                          SizedBox(height: 280, child: _buildSideImage()),
                        ],
                      ),
              ),
            ),
          );
        },
      ),
    );
  }
}
