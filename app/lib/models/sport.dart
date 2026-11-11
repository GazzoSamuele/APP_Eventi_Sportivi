// le classi che rappresentano la risposta di GET /api/sport:
// Sport → Category → Competition, come l'albero del JSON

class Competition {
  final int id;
  final String name;

  Competition({required this.id, required this.name});

  factory Competition.fromJson(Map<String, dynamic> json) {
    return Competition(id: json['id'] as int, name: json['name'] as String);
  }
}

class Category {
  final int id;
  final String name;
  final List<Competition> competitions;

  Category({required this.id, required this.name, required this.competitions});

  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
      id: json['id'] as int,
      name: json['name'] as String,
      // la lista JSON diventa una lista di oggetti Competition
      competitions: (json['competitions'] as List)
          .map((c) => Competition.fromJson(c))
          .toList(),
    );
  }
}

class Sport {
  final int id;
  final String name;
  final List<Category> categories;

  Sport({required this.id, required this.name, required this.categories});

  factory Sport.fromJson(Map<String, dynamic> json) {
    return Sport(
      id: json['id'] as int,
      name: json['name'] as String,
      categories: (json['categories'] as List)
          .map((c) => Category.fromJson(c))
          .toList(),
    );
  }
}
