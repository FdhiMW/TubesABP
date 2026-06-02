import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

class AiChatApi {
  AiChatApi({required this.baseUrl, required this.token});

  final String baseUrl;
  final String token;

  Future<String> ask(String question) async {
    final res = await http.post(
      Uri.parse('$baseUrl/ai/chat'),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode({'question': question}),
    );

    dynamic body;
    try {
      body = jsonDecode(res.body);
    } catch (_) {}

    if (res.statusCode >= 200 && res.statusCode < 300 && body is Map) {
      if (body['answer'] != null) return body['answer'].toString();
      if (body['message'] != null) return body['message'].toString();
    }

    if (body is Map && body['message'] != null) {
      throw Exception(body['message'].toString());
    }

    throw Exception('Tidak dapat terhubung ke server (HTTP ${res.statusCode})');
  }
}

class _ChatMessage {
  final String text;
  final bool isUser;
  final bool isLoading;

  const _ChatMessage({
    required this.text,
    required this.isUser,
    this.isLoading = false,
  });
}

/// Modal chat — sama UX web dashboard (home.blade.php chatbot).
void showAiChatSheet(
  BuildContext context, {
  required String baseUrl,
  required String token,
}) {
  showModalBottomSheet(
    context: context,
    isScrollControlled: true,
    backgroundColor: Colors.transparent,
    builder: (ctx) => Padding(
      padding: EdgeInsets.only(bottom: MediaQuery.viewInsetsOf(ctx).bottom),
      child: AiChatSheet(baseUrl: baseUrl, token: token),
    ),
  );
}

class AiChatSheet extends StatefulWidget {
  const AiChatSheet({
    super.key,
    required this.baseUrl,
    required this.token,
  });

  final String baseUrl;
  final String token;

  @override
  State<AiChatSheet> createState() => _AiChatSheetState();
}

class _AiChatSheetState extends State<AiChatSheet> {
  static const Color forest = Color(0xFF2D4B37);
  static const Color gold = Color(0xFFC59D5F);
  static const Color cream = Color(0xFFFAFAF4);
  static const Color ink = Color(0xFF1A1C19);

  late final AiChatApi api;
  late final ScrollController _scrollController;
  late final TextEditingController _inputController;

  final List<_ChatMessage> _messages = [
    const _ChatMessage(
      text: 'Halo 👋 Ada yang bisa saya bantu tentang venue?',
      isUser: false,
    ),
  ];

  bool _isSending = false;

  @override
  void initState() {
    super.initState();
    api = AiChatApi(baseUrl: widget.baseUrl, token: widget.token);
    _scrollController = ScrollController();
    _inputController = TextEditingController();
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _inputController.dispose();
    super.dispose();
  }

  void _scrollToBottom() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!_scrollController.hasClients) return;
      _scrollController.animateTo(
        _scrollController.position.maxScrollExtent,
        duration: const Duration(milliseconds: 250),
        curve: Curves.easeOut,
      );
    });
  }

  Future<void> _send() async {
    final question = _inputController.text.trim();
    if (question.isEmpty || _isSending) return;

    setState(() {
      _messages.add(_ChatMessage(text: question, isUser: true));
      _messages.add(const _ChatMessage(text: 'Mengetik…', isUser: false, isLoading: true));
      _isSending = true;
      _inputController.clear();
    });
    _scrollToBottom();

    try {
      final answer = await api.ask(question);
      if (!mounted) return;
      setState(() {
        _messages.removeLast();
        _messages.add(_ChatMessage(text: answer, isUser: false));
        _isSending = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _messages.removeLast();
        _messages.add(_ChatMessage(
          text: '⚠️ Tidak dapat terhubung ke server. Coba lagi.',
          isUser: false,
        ));
        _isSending = false;
      });
    }
    _scrollToBottom();
  }

  @override
  Widget build(BuildContext context) {
    final maxH = MediaQuery.sizeOf(context).height * 0.72;
    final sheetH = maxH.clamp(380.0, 520.0);

    return Align(
      alignment: Alignment.bottomCenter,
      child: Container(
        height: sheetH,
        margin: const EdgeInsets.fromLTRB(12, 0, 12, 16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.15),
              blurRadius: 24,
              offset: const Offset(0, 8),
            ),
          ],
        ),
        clipBehavior: Clip.antiAlias,
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
              color: forest,
              child: Row(
                children: [
                  Container(
                    width: 10,
                    height: 10,
                    decoration: const BoxDecoration(color: Color(0xFF4ADE80), shape: BoxShape.circle),
                  ),
                  const SizedBox(width: 10),
                  const Expanded(
                    child: Text(
                      'Wedding AI Assistant',
                      style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w600),
                    ),
                  ),
                  IconButton(
                    onPressed: () => Navigator.pop(context),
                    icon: const Icon(Icons.close, color: Colors.white70),
                    padding: EdgeInsets.zero,
                    constraints: const BoxConstraints(),
                  ),
                ],
              ),
            ),
            Expanded(
              child: Container(
                color: cream.withValues(alpha: 0.6),
                child: ListView.builder(
                  controller: _scrollController,
                  padding: const EdgeInsets.all(16),
                  itemCount: _messages.length,
                  itemBuilder: (context, index) {
                    final msg = _messages[index];
                    return _buildBubble(msg);
                  },
                ),
              ),
            ),
            Container(
              padding: const EdgeInsets.fromLTRB(12, 10, 12, 12),
              decoration: BoxDecoration(
                color: Colors.white,
                border: Border(top: BorderSide(color: Colors.black.withValues(alpha: 0.06))),
              ),
              child: Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: _inputController,
                      enabled: !_isSending,
                      textInputAction: TextInputAction.send,
                      onSubmitted: (_) => _send(),
                      decoration: InputDecoration(
                        hintText: 'Tanyakan sesuatu...',
                        hintStyle: TextStyle(color: ink.withValues(alpha: 0.45), fontSize: 14),
                        filled: true,
                        fillColor: cream.withValues(alpha: 0.7),
                        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(24),
                          borderSide: BorderSide.none,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Material(
                    color: gold,
                    shape: const CircleBorder(),
                    child: InkWell(
                      onTap: _isSending ? null : _send,
                      customBorder: const CircleBorder(),
                      child: const SizedBox(
                        width: 44,
                        height: 44,
                        child: Icon(Icons.send_rounded, color: Colors.white, size: 22),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBubble(_ChatMessage msg) {
    final isUser = msg.isUser;
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Align(
        alignment: isUser ? Alignment.centerRight : Alignment.centerLeft,
        child: Container(
          constraints: BoxConstraints(maxWidth: MediaQuery.sizeOf(context).width * 0.72),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
          decoration: BoxDecoration(
            color: isUser ? gold : Colors.white,
            borderRadius: BorderRadius.only(
              topLeft: const Radius.circular(16),
              topRight: const Radius.circular(16),
              bottomLeft: Radius.circular(isUser ? 16 : 4),
              bottomRight: Radius.circular(isUser ? 4 : 16),
            ),
            boxShadow: [
              if (!isUser)
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.06),
                  blurRadius: 6,
                  offset: const Offset(0, 2),
                ),
            ],
          ),
          child: msg.isLoading
              ? Text(
                  msg.text,
                  style: TextStyle(
                    fontSize: 14,
                    color: ink.withValues(alpha: 0.5),
                    fontStyle: FontStyle.italic,
                  ),
                )
              : Text(
                  msg.text,
                  style: TextStyle(
                    fontSize: 14,
                    height: 1.45,
                    color: isUser ? Colors.white : ink,
                  ),
                ),
        ),
      ),
    );
  }
}
