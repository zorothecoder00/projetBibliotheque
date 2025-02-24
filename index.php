<?php
include 'connexion.php';
session_start();

if (isset($_SESSION['id'])) {  
    $id = $_SESSION['id'];
    
    // Récupérer les informations du lecteur connecté depuis la base de données
    $sql = "SELECT nom, prenom FROM lecteurs WHERE id = ?";
    if ($stmt = $connection->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $lecteur = $result->fetch_assoc();
            $_SESSION['nom'] = $lecteur['nom'];
            $_SESSION['prenom'] = $lecteur['prenom'];
        }
        $stmt->close();
    }
}

?>
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
    
    <?php if (isset($_SESSION['id'])): ?>
        <p>Bonjour, <?php echo htmlspecialchars($_SESSION['prenom']) . ' ' . htmlspecialchars($_SESSION['nom']); ?> !</p>
        <a href="logout.php">Déconnexion</a>
    <?php else: ?>
        <p><a href="login.php">Connexion</a> | <a href="register.php">Inscription</a></p>
    <?php endif; ?>
    
            
        <p>Rejoignez-nous et plongez dans l'univers des livres ! Organisez, découvrez, et partagez vos lectures préférées en toute simplicité. La bibliothèque idéale vous attend !</p> 
    
    <section>
        
        <div class="formulaire">
        <form method="GET" action="results.php" class="formulaire">
            <input type="text" name="recherche" placeholder="Rechercher un livre">
            <select name="critere">
                <option value="titre">Titre</option>
                <option value="auteur">Auteur</option>
            </select>
            <button type="submit">Rechercher</button>
        </form>
        </div>
    </section>
    
    <script src="index.js"></script>
</body>
</html>