import 'dart:convert';

import 'package:http/http.dart' as http;

import '../config.dart';
import '../models/sport.dart';

class ApiClient {
  Future<List<Sport>> fetchSports() async {
    final risposta = await http.get(Uri.parse('$apiBaseUrl/api/sport'));

    if (risposta.statusCode != 200) {
      throw Exception('Errore ${risposta.statusCode} nel caricare gli sport');
    }

    final dati = jsonDecode(risposta.body) as List;

    return dati.map((s) => Sport.fromJson(s)).toList();
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
}

class ApiException implements Exception {
  final String message;

  ApiException(this.message);

  @override
  String toString() => message;
}
