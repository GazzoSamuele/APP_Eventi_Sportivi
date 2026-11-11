import 'package:flutter/material.dart';

import '../api/api_client.dart';
import '../models/sport.dart';

class SportsScreen extends StatefulWidget {
  const SportsScreen({super.key});

  @override
  State<SportsScreen> createState() => _SportsScreenState();
}

class _SportsScreenState extends State<SportsScreen> {
  late final Future<List<Sport>> _sport;

  @override
  void initState() {
    super.initState();
    _sport = ApiClient().fetchSports();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Sport')),
      body: FutureBuilder<List<Sport>>(
        future: _sport,
        builder: (context, snapshot) {
          // 1. in attesa
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          // 2. errore
          if (snapshot.hasError) {
            return Center(child: Text('Errore: ${snapshot.error}'));
          }

          // 3. dati arrivati
          final sport = snapshot.data!;

          return ListView(
            children: [
              for (final s in sport)
                ExpansionTile(
                  title: Text(s.name),
                  children: [
                    for (final c in s.categories)
                      ListTile(
                        title: Text(c.name),
                        subtitle: Text(
                          c.competitions.map((co) => co.name).join(', '),
                        ),
                      ),
                  ],
                ),
            ],
          );
        },
      ),
    );
  }
}
