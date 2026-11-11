import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class TokenStorage {
  static const _storage = FlutterSecureStorage();
  static const _chiave = 'token';

  static Future<void> salva(String token) =>
      _storage.write(key: _chiave, value: token);

  static Future<String?> leggi() => _storage.read(key: _chiave);

  static Future<void> cancella() => _storage.delete(key: _chiave);
}
