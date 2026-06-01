import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

/// Satu hari di kalender — format sama dengan `BookingController::availability`.
class DayAvailability {
  final String date;
  final String title;
  final Color color;

  const DayAvailability({
    required this.date,
    required this.title,
    required this.color,
  });

  factory DayAvailability.fromJson(Map<String, dynamic> json) {
    final hex = json['color']?.toString() ?? '#b7e4c7';
    return DayAvailability(
      date: json['start']?.toString() ?? '',
      title: json['title']?.toString() ?? 'Tersedia',
      color: _colorFromHex(hex),
    );
  }

  static Color _colorFromHex(String hex) {
    var h = hex.replaceFirst('#', '');
    if (h.length == 6) h = 'FF$h';
    return Color(int.parse(h, radix: 16));
  }
}

class KetersediaanApi {
  /// `GET /api/availability-data` — logika sama web, path API agar CORS `api/*` berlaku.
  Future<Map<String, DayAvailability>> fetchAvailability(String apiBaseUrl) async {
    final res = await http.get(
      Uri.parse('$apiBaseUrl/availability-data'),
      headers: {'Accept': 'application/json'},
    );

    if (res.statusCode != 200) {
      throw Exception('Gagal memuat ketersediaan (HTTP ${res.statusCode})');
    }

    final list = jsonDecode(res.body) as List<dynamic>;
    final map = <String, DayAvailability>{};
    for (final item in list) {
      if (item is Map<String, dynamic>) {
        final day = DayAvailability.fromJson(item);
        if (day.date.isNotEmpty) map[day.date] = day;
      }
    }
    return map;
  }
}

class KetersediaanPage extends StatefulWidget {
  const KetersediaanPage({super.key, required this.baseUrl});

  /// Base URL API Flutter, mis. `http://192.168.x.x:8000/api`
  final String baseUrl;

  @override
  State<KetersediaanPage> createState() => _KetersediaanPageState();
}

class _KetersediaanPageState extends State<KetersediaanPage> {
  static const Color ricePaper = Color(0xFFFAFAF5);
  static const Color heritageGreen = Color(0xFF2D4B37);
  static const Color onSurfaceVariant = Color(0xFF424843);
  static const Color outlineVariant = Color(0xFFC2C8C0);
  static const Color availableGreen = Color(0xFFB7E4C7);
  static const Color oneBookingYellow = Color(0xFFFFE066);
  static const Color fullRed = Color(0xFFFF6B6B);

  final _api = KetersediaanApi();

  bool isLoading = true;
  String? errorMessage;
  Map<String, DayAvailability> _byDate = {};

  DateTime _focusedMonth = DateTime(DateTime.now().year, DateTime.now().month);
  String? _selectedDateKey;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });
    try {
      final data = await _api.fetchAvailability(widget.baseUrl);
      if (!mounted) return;
      setState(() {
        _byDate = data;
        isLoading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        isLoading = false;
        errorMessage = e.toString().replaceFirst('Exception: ', '');
      });
    }
  }

  String _dateKey(DateTime d) =>
      '${d.year}-${d.month.toString().padLeft(2, '0')}-${d.day.toString().padLeft(2, '0')}';

  DayAvailability _dayInfo(DateTime d) {
    return _byDate[_dateKey(d)] ??
        const DayAvailability(date: '', title: 'Tersedia', color: availableGreen);
  }

  void _prevMonth() {
    setState(() {
      _focusedMonth = DateTime(_focusedMonth.year, _focusedMonth.month - 1);
      _selectedDateKey = null;
    });
  }

  void _nextMonth() {
    setState(() {
      _focusedMonth = DateTime(_focusedMonth.year, _focusedMonth.month + 1);
      _selectedDateKey = null;
    });
  }

  static const _monthNames = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: ricePaper,
      appBar: AppBar(
        backgroundColor: ricePaper,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: heritageGreen),
          onPressed: () => Navigator.pop(context),
        ),
        title: const Text(
          'Ketersediaan Tanggal',
          style: TextStyle(
            fontFamily: 'serif',
            fontSize: 20,
            fontWeight: FontWeight.w600,
            color: heritageGreen,
          ),
        ),
        centerTitle: true,
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(height: 1, color: outlineVariant.withValues(alpha: 0.3)),
        ),
      ),
      body: isLoading
          ? const Center(
              child: CircularProgressIndicator(color: heritageGreen),
            )
          : errorMessage != null
              ? _buildError()
              : _buildContent(),
    );
  }

  Widget _buildError() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.error_outline, size: 48, color: outlineVariant),
            const SizedBox(height: 16),
            Text(errorMessage!, textAlign: TextAlign.center, style: const TextStyle(color: onSurfaceVariant)),
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: _load,
              style: ElevatedButton.styleFrom(backgroundColor: heritageGreen, foregroundColor: Colors.white),
              child: const Text('Coba lagi'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildContent() {
    final selected = _selectedDateKey != null ? _byDate[_selectedDateKey!] : null;

    return SingleChildScrollView(
      padding: const EdgeInsets.fromLTRB(20, 8, 20, 24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          const Text(
            'Lihat jadwal booking & survey venue secara real-time. Maksimal 2 aktivitas per tanggal.',
            style: TextStyle(fontSize: 14, height: 1.45, color: onSurfaceVariant),
          ),
          const SizedBox(height: 20),
          _buildLegend(),
          const SizedBox(height: 20),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: outlineVariant.withValues(alpha: 0.5)),
              boxShadow: [
                BoxShadow(
                  color: heritageGreen.withValues(alpha: 0.06),
                  blurRadius: 16,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Column(
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    IconButton(
                      onPressed: _prevMonth,
                      icon: const Icon(Icons.chevron_left, color: heritageGreen),
                    ),
                    Text(
                      '${_monthNames[_focusedMonth.month - 1]} ${_focusedMonth.year}',
                      style: const TextStyle(
                        fontFamily: 'serif',
                        fontSize: 18,
                        fontWeight: FontWeight.w600,
                        color: heritageGreen,
                      ),
                    ),
                    IconButton(
                      onPressed: _nextMonth,
                      icon: const Icon(Icons.chevron_right, color: heritageGreen),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                _buildWeekdayHeader(),
                const SizedBox(height: 8),
                _buildMonthGrid(),
              ],
            ),
          ),
          if (selected != null) ...[
            const SizedBox(height: 20),
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: selected.color.withValues(alpha: 0.35),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: selected.color),
              ),
              child: Row(
                children: [
                  Icon(Icons.event, color: heritageGreen.withValues(alpha: 0.9)),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          _selectedDateKey!,
                          style: const TextStyle(fontWeight: FontWeight.w600, color: heritageGreen),
                        ),
                        Text(
                          selected.title,
                          style: const TextStyle(fontSize: 15, color: onSurfaceVariant),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ],
          const SizedBox(height: 24),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: () => Navigator.pop(context),
              style: ElevatedButton.styleFrom(
                backgroundColor: heritageGreen,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                elevation: 0,
              ),
              child: const Text('Tutup', style: TextStyle(fontWeight: FontWeight.w600)),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLegend() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceAround,
      children: [
        _legendItem(availableGreen, 'Tersedia'),
        _legendItem(oneBookingYellow, '1 booking'),
        _legendItem(fullRed, 'Penuh'),
      ],
    );
  }

  Widget _legendItem(Color color, String label) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(width: 14, height: 14, decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(3))),
        const SizedBox(width: 6),
        Text(label, style: const TextStyle(fontSize: 11, color: onSurfaceVariant)),
      ],
    );
  }

  Widget _buildWeekdayHeader() {
    const labels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    return Row(
      children: labels
          .map(
            (l) => Expanded(
              child: Center(
                child: Text(l, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: onSurfaceVariant)),
              ),
            ),
          )
          .toList(),
    );
  }

  Widget _buildMonthGrid() {
    final first = DateTime(_focusedMonth.year, _focusedMonth.month, 1);
    final daysInMonth = DateTime(_focusedMonth.year, _focusedMonth.month + 1, 0).day;
    final startWeekday = first.weekday % 7; // Sun=0 .. Sat=6

    final cells = <Widget>[];
    for (var i = 0; i < startWeekday; i++) {
      cells.add(const SizedBox(height: 44));
    }
    for (var day = 1; day <= daysInMonth; day++) {
      final date = DateTime(_focusedMonth.year, _focusedMonth.month, day);
      final key = _dateKey(date);
      final info = _dayInfo(date);
      final isSelected = _selectedDateKey == key;
      final isToday = _dateKey(DateTime.now()) == key;

      cells.add(
        GestureDetector(
          onTap: () => setState(() => _selectedDateKey = key),
          child: Container(
            height: 44,
            margin: const EdgeInsets.all(2),
            decoration: BoxDecoration(
              color: info.color,
              borderRadius: BorderRadius.circular(8),
              border: Border.all(
                color: isSelected
                    ? heritageGreen
                    : isToday
                        ? heritageGreen.withValues(alpha: 0.5)
                        : Colors.transparent,
                width: isSelected ? 2 : 1,
              ),
            ),
            alignment: Alignment.center,
            child: Text(
              '$day',
              style: TextStyle(
                fontSize: 14,
                fontWeight: isSelected || isToday ? FontWeight.w700 : FontWeight.w500,
                color: info.title == 'Penuh' ? const Color(0xFF7A1F1F) : const Color(0xFF1A1C19),
              ),
            ),
          ),
        ),
      );
    }

    return GridView.count(
      crossAxisCount: 7,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      children: cells,
    );
  }
}
