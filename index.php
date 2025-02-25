<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="index.css">  
    <title>Bibliothèque</title>
</head>
<body>
    <h1>Bienvenue sur notre bibliothèque</h1>
    
    <?php 

    include 'connexion.php';
    session_start(); // Démarrer la session

    if (isset($_SESSION['id'])): ?>
        <p>Hello, <?php echo htmlspecialchars($_SESSION['prenom']) . ' ' . htmlspecialchars($_SESSION['nom']); ?> !</p>
        <a href="logout.php">Déconnexion</a>
    <?php else: ?>
        <p><a href="login.php">Connexion</a> | <a href="register.php">Inscription</a></p>
    <?php endif; ?>
    
    <p>Rejoignez-nous et plongez dans l'univers des livres ! Organisez, découvrez, et partagez vos lectures préférées en toute simplicité.</p> 

    <!-- Formulaire de recherche -->
    <section>
        <h2>Rechercher un livre</h2>
        <form method="GET" action="results.php">
            <input type="text" name="recherche" placeholder="Rechercher un livre">
            <select name="critere">
                <option value="titre">Titre</option>
                <option value="auteur">Auteur</option>
            </select>
            <button type="submit">Rechercher</button>
        </form>  
    </section>

    <!-- Ajout de livre uniquement pour les utilisateurs connectés -->
    <?php if (isset($_SESSION['id'])): 

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter'])) {
        // Vérifier que les champs ne sont pas vides
        if (!empty($_POST['titre']) && !empty($_POST['auteur'])) {
            $titre = trim($_POST['titre']);
            $auteur = trim($_POST['auteur']);

            // Préparer et exécuter l'insertion en base
            $sql = "INSERT INTO livres (titre, auteur) VALUES (?, ?)";
            if ($stmt = $connection->prepare($sql)) {
                $stmt->bind_param("ss", $titre, $auteur);
                if ($stmt->execute()) {
                    // Redirection pour éviter le rechargement du formulaire
                    header("Location: index.php");
                    exit();
                } else {
                    echo "Erreur lors de l'ajout du livre.";
                }
                $stmt->close();
            }
        } else {
            echo "Tous les champs sont obligatoires.";
        }
    }

?>


    <section>
        <h2>Ajouter un livre</h2>
        <form method="POST">
            <input type="text" name="titre" placeholder="Titre du livre" required>
            <input type="text" name="auteur" placeholder="Auteur" required>
            <button type="submit" name="ajouter">Ajouter</button>
        </form>

        

    </section>
    <?php endif; ?>


     <?php
        // Suppression d'un livre
        if (isset($_POST['supprimer']) && isset($_POST['id_livre'])) {
        $id_livre = $_POST['id_livre'];

        // Vérifier si l'utilisateur est connecté et que l'ID du livre est valide
        if (isset($_SESSION['id']) && !empty($id_livre)) {
            // Préparer la requête SQL pour supprimer le livre
            $sql = "DELETE FROM livres WHERE id = ?";
            if ($stmt = $connection->prepare($sql)) {
                $stmt->bind_param("i", $id_livre);
                if ($stmt->execute()) {
                    // Rediriger pour éviter de soumettre à nouveau la suppression en cas de rechargement
                    header("Location: index.php");
                    exit();
                } else {
                    echo "Erreur lors de la suppression du livre.";
                }
                $stmt->close();
            }
        }
    }
?>
    <!-- Liste des livres -->
    <section>
        <h2>Liste des livres</h2>

        <?php 
        // Récupérer la liste des livres
        $sql = "SELECT id, titre, auteur FROM livres";
        $resultats = $connection->query($sql);
        if ($resultats->num_rows > 0): ?>
            <ul>
                <?php while ($livre = $resultats->fetch_assoc()): ?>
                    <li style="margin-top: 10px;">
                        <strong><?php echo htmlspecialchars($livre['titre']); ?></strong> - 
                        <?php echo htmlspecialchars($livre['auteur']); ?>



                        <?php if (isset($_SESSION['id'])): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id_livre" value="<?php echo $livre['id']; ?>">
                            <button type="submit" name="supprimer" class="delete">Supprimer</button>
                        </form>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>Aucun livre trouvé.</p>
        <?php endif; ?>
    </section>

    <script src="index.js"></script>
</body>
</html>
