import 'dart:convert';

import '../auth/token_storage.dart';

import 'package:http/http.dart' as http;
import 'package:flutter/foundation.dart';

import '../config.dart';
import '../models/event.dart';
import '../models/sport.dart';

class ApiClient {
  Future<Map<String, String>> _headers() async {
    final token = await TokenStorage.leggi();

    debugPrint(
      'Token letto dalla cassaforte: ${token == null ? "NESSUNO" : "presente"}',
    );

    return {
      'Content-Type': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  Future<List<Sport>> fetchSports() async {
    final risposta = await http.get(Uri.parse('$apiBaseUrl/api/sport'));

    if (risposta.statusCode != 200) {
      throw Exception('Errore ${risposta.statusCode} nel caricare gli sport');
    }

    final dati = jsonDecode(risposta.body) as List;

    return dati.map((s) => Sport.fromJson(s)).toList();
  }

  Future<List<Event>> fetchEvents() async {
    final risposta = await http.get(
      Uri.parse('$apiBaseUrl/api/events'),
      headers: await _headers(),
    );

    if (risposta.statusCode != 200) {
      throw Exception('Errore ${risposta.statusCode} nel caricare gli eventi');
    }

    final dati = jsonDecode(risposta.body) as List;

    return dati.map((e) => Event.fromJson(e)).toList();
  }

  Future<String> login(String email, String password) async {
    final risposta = await http.post(
      Uri.parse('$apiBaseUrl/api/login'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'email': email, 'password': password}),
    );

    final dati = jsonDecode(risposta.body) as Map<String, dynamic>;

    if (risposta.statusCode != 200) {
      throw ApiException(
        dati['errore'] as String? ?? 'Errore ${risposta.statusCode}',
      );
    }

    return dati['token'] as String;
  }

  Future<void> logout() async {
    try {
      await http.post(
        Uri.parse('$apiBaseUrl/api/logout'),
        headers: await _headers(),
      );
    } finally {
      await TokenStorage.cancella();
    }
  }
}

class ApiException implements Exception {
  final String message;

  ApiException(this.message);

  @override
  String toString() => message;
}
