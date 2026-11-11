import 'package:flutter/material.dart';

import '../api/api_client.dart';
import '../auth/token_storage.dart';
import 'events_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _email = TextEditingController();
  final _password = TextEditingController();

  bool _caricamento = false;
  String? _errore;

  @override
  void dispose() {
    _email.dispose();
    _password.dispose();
    super.dispose();
  }

  Future<void> _accedi() async {
    // 1. i campi sono compilati bene? se no, mostra gli errori sotto i campi
    if (!_formKey.currentState!.validate()) return;

    // 2. mostra il caricamento e togli l'errore precedente
    setState(() {
      _caricamento = true;
      _errore = null;
    });

    try {
      // 3. chiama l'API e salva il token
      final token = await ApiClient().login(_email.text.trim(), _password.text);
      await TokenStorage.salva(token);

      // 4. vai alla schermata degli sport, senza poter tornare indietro al login
      if (!mounted) return;
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => const EventsScreen()),
      );
    } on ApiException catch (e) {
      setState(() => _errore = e.message);
    } catch (e) {
      debugPrint('Errore nel login: $e');
      setState(() => _errore = 'Impossibile contattare il server');
    } finally {
      if (mounted) setState(() => _caricamento = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Accedi')),
      body: Padding(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            mainAxisAlignment: .center,
            children: [
              TextFormField(
                controller: _email,
                keyboardType: TextInputType.emailAddress,
                decoration: const InputDecoration(labelText: 'Email'),
                validator: (valore) => (valore == null || !valore.contains('@'))
                    ? 'Inserisci un\'email valida'
                    : null,
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _password,
                obscureText: true,
                decoration: const InputDecoration(labelText: 'Password'),
                validator: (valore) => (valore == null || valore.isEmpty)
                    ? 'Inserisci la password'
                    : null,
              ),
              const SizedBox(height: 24),
              if (_errore != null) ...[
                Text(
                  _errore!,
                  style: TextStyle(color: Theme.of(context).colorScheme.error),
                ),
                const SizedBox(height: 16),
              ],
              SizedBox(
                width: double.infinity,
                child: FilledButton(
                  onPressed: _caricamento ? null : _accedi,
                  child: _caricamento
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        )
                      : const Text('Accedi'),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
