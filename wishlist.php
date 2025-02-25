<?php
include 'connexion.php';  
session_start(); // Démarrer la session   

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    die("Veuillez vous connecter pour gérer votre liste de lecture.");
}

$id_lecteur = $_SESSION['id'];  // Récupérer l'id du lecteur depuis la session

// Ajouter un livre à la liste de lecture
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_livre'])) {
    $id_livre = intval($_POST['id_livre']);  // Récupérer l'ID du livre envoyé dans le formulaire

    // Vérification pour éviter d'ajouter un livre déjà présent
    $check_sql = "SELECT * FROM liste_lecture WHERE id_lecteur = ? AND id_livre = ?";
    $stmt_check = $connection->prepare($check_sql);
    $stmt_check->bind_param('ii', $id_lecteur, $id_livre);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        // Ajouter le livre à la liste de lecture
        $insert_sql = "INSERT INTO liste_lecture (id_lecteur, id_livre) VALUES (?, ?)";
        $stmt_insert = $connection->prepare($insert_sql);
        $stmt_insert->bind_param('ii', $id_lecteur, $id_livre);

        if ($stmt_insert->execute()) {
            echo "<p>Le livre a été ajouté à votre liste de lecture.</p>";
        } else {
            echo "<p>Erreur lors de l'ajout du livre à la liste de lecture.</p>";
        }

        $stmt_insert->close();
    } else {
        echo "<p>Ce livre est déjà dans votre liste de lecture.</p>";
    }

    $stmt_check->close();
}

// Supprimer un livre de la liste de lecture
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_livre_supprimer'])) {
    $id_livre_supprimer = intval($_POST['id_livre_supprimer']);  // Récupérer l'ID du livre à supprimer

    // Requête de suppression
    $delete_sql = "DELETE FROM liste_lecture WHERE id_lecteur = ? AND id_livre = ?";
    $stmt_delete = $connection->prepare($delete_sql);
    $stmt_delete->bind_param('ii', $id_lecteur, $id_livre_supprimer);

    if ($stmt_delete->execute()) {
        echo "<p>Le livre a été retiré de votre liste de lecture.</p>";
    } else {
        echo "<p>Erreur lors de la suppression du livre.</p>";
    }

    $stmt_delete->close();
}

// Récupérer les livres de la liste de lecture de l'utilisateur
$sql = "SELECT livres.id, livres.titre, livres.auteur 
        FROM liste_lecture
        JOIN livres ON liste_lecture.id_livre = livres.id
        WHERE liste_lecture.id_lecteur = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $id_lecteur);
$stmt->execute();
$resultat = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma Liste de Lecture</title>
</head>
<body>
    <h1>Ma Liste de Lecture</h1>

    <?php if ($resultat->num_rows > 0): ?>
        <ul>
            <?php while ($row = $resultat->fetch_assoc()): ?>
                <li>
                    <strong><?php echo htmlspecialchars($row['titre']); ?></strong> 
                    par <?php echo htmlspecialchars($row['auteur']); ?><br>
                    
                    <!-- Lien vers les détails du livre -->
                    <a href="details.php?id=<?php echo $row['id']; ?>">Voir les détails</a><br>
                    
                    <!-- Formulaire pour retirer un livre de la liste -->
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="id_livre_supprimer" value="<?php echo $row['id']; ?>">
                        <button type="submit">Retirer</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Votre liste de lecture est vide.</p>
    <?php endif; ?>

    <a href="index.php"><button>Retour à la bibliothèque</button></a>
</body>
</html>

<?php
$stmt->close();
$connection->close();
?>