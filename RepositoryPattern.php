<?php 
class PersonEntity {
    public function __construct(public $id, public $name,  public $age)
    {
        
    }
}

class PersonRepository {
    public function __construct(private PDO $pdo) {}

    private function arrayToModel(array $entry): PersonEntity {
        return new PersonEntity(
            $entry['ID'],
            $entry['name'],
            $entry['age']
        );
    }

    public function fetchById(int $id): ?PersonEntity {
        $stmt = $this->pdo->prepare('SELECT * FROM `people` WHERE `ID` = :id');

        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($entry)) 
            return $this->arrayToModel($entry);

        return null;
    }

    public function fetch(): array {
        $stmt = $this->pdo->prepare('SELECT * 
            FROM `people` ');

        $stmt->execute();

        $models = [];
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($entries AS $entry) {
            $models[] = $this->arrayToModel($entry);
        }

        return $models;
    }

    public function paginate(int $page, int $perPage = 5): array {

        $page = max(1, $page);
        $lastPage = $this->lastPage($perPage);
        $page = min($page, $lastPage);

        $stmt = $this->pdo->prepare('SELECT * 
            FROM `people` 
            ORDER BY `ID` ASC
            LIMIT :limit OFFSET :offset');

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);

        $stmt->execute();

        $models = [];
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($entries AS $entry) {
            $models[] = $this->arrayToModel($entry);
        }

        return $models;
    }

    public function count(): int {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) AS `count` FROM `people`');
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function lastPage($perPage): int {

        $count = $this->count();
        $lastPage = ceil($count / $perPage);

        return $lastPage;
    }

    public function update(int $id, array $properties): PersonEntity {
        $stmt = $this->pdo->prepare('UPDATE `people` 
            SET 
                `name` = :name,
                `age` = :age
            WHERE `id` = :id');

        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':name', $properties['name']);
        $stmt->bindValue(':age', $properties['age']);
      
        $stmt->execute();

        return $this->fetchById($id);
    }
}