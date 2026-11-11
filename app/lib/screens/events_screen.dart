import 'package:flutter/material.dart';

import 'login_screen.dart';
import '../api/api_client.dart';
import '../models/event.dart';

class EventsScreen extends StatefulWidget {
  const EventsScreen({super.key});

  @override
  State<EventsScreen> createState() => _EventsScreenState();
}

class _EventsScreenState extends State<EventsScreen> {
  late final Future<List<Event>> _event;

  @override
  void initState() {
    super.initState();
    _event = ApiClient().fetchEvents();
  }

  Future<void> _esci() async {
    await ApiClient().logout();

    if (!mounted) return;
    Navigator.of(context).pushAndRemoveUntil(
      MaterialPageRoute(builder: (_) => const LoginScreen()),
      (route) => false,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('I miei eventi'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            tooltip: 'Esci',
            onPressed: _esci,
          ),
        ],
      ),
      body: FutureBuilder<List<Event>>(
        future: _event,
        builder: (context, snapshot) {
          // 1. in attesa
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          // 2. errore
          if (snapshot.hasError) {
            return Center(child: Text('Errore: ${snapshot.error}'));
          }

          final eventi = snapshot.data!;

          // 3. nessun evento
          if (eventi.isEmpty) {
            return const Center(
              child: Text('Non segui ancora nessuna competizione'),
            );
          }

          // 4. dati arrivati: una riga per evento
          return ListView(
            children: [
              for (final e in eventi)
                ListTile(
                  title: Text(e.title),
                  subtitle: Text(
                    '${e.competition} · ${formattaData(e.start)}\n${e.broadcasters.join(', ')}',
                  ),
                  isThreeLine: true,
                ),
            ],
          );
        },
      ),
    );
  }
}
