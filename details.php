<?php 

echo "<style>
    body {
        background-image: url('livre1.webp'); /* Remplace 'background.jpg' par le chemin de ton image */
        background-size: cover; /* Couvre toute la page */
        background-position: center; /* Centre l'image */
        background-repeat: no-repeat; /* Évite la répétition */
        font-family: Arial, sans-serif; /* Police d'écriture */
        color: white; /* Texte en blanc pour bien contraster */
        text-align: center;
        margin: 0;
        padding: 0;
    }
    h2 {
        margin-top: 20px;
    }
    ul {
        list-style: none;
        padding: 0;
    }
    li {
        background: rgba(0, 0, 0, 0.7); /* Fond semi-transparent pour améliorer la lisibilité */
        padding: 15px;
        margin: 10px auto;
        width: 60%;
        border-radius: 10px;
    }
    button {
        background-color: #ffcc00;
        border: none;
        padding: 10px;
        cursor: pointer;
        font-size: 16px;
        border-radius: 5px;
    }
    button:hover {
        background-color: #ffaa00;
    }
</style>";

include 'connexion.php';
session_start(); // Démarrer la session   

// Vérifier si un ID de livre a été fourni
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_livre = intval($_GET['id']); // Sécuriser l'ID en le convertissant en entier 

    // Requête pour récupérer les détails du livre
    $sql = "SELECT * FROM livres WHERE id = ?";

    if ($stmt = $connection->prepare($sql)) {
        $stmt->bind_param('i', $id_livre);
        $stmt->execute();
        $resultat = $stmt->get_result();
  
        // Vérifier si un livre a été trouvé
        if ($resultat->num_rows > 0) {
            $livre = $resultat->fetch_assoc();
?>
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Détails du livre</title>
            </head>
            <body>
                <h1><?php echo htmlspecialchars($livre['titre']); ?></h1>
                <p><strong>Auteur :</strong> <?php echo htmlspecialchars($livre['auteur']); ?></p>
                <p><strong>Description :</strong> <?php echo nl2br(htmlspecialchars($livre['description'])); ?></p>
                <p><strong> </strong> <?php 
                if (!empty($livre['image'])) {
                    $imageData = base64_encode($livre['image']);
                    echo '<img src="data:image/jpeg;base64,' . $imageData . '" width="200" height="300">';
                } else {
                    echo "Pas d'image disponible.";
                }
                
                 ?></p>
                <p><strong>Maison d'édition :</strong> <?php echo htmlspecialchars($livre['maison_edition']); ?></p>
                <p><strong>Nombre d'exemplaires :</strong> <?php echo htmlspecialchars($livre['nombre_exemplaire']); ?></p>

                <!-- Le formulaire pour ajouter à la liste de lecture -->
                <form method="POST" action="wishlist.php">
                    <input type="hidden" name="id_livre" value="<?php echo $id_livre; ?>">
                    <button type="submit" style="margin-bottom: 20px;">Ajouter à la liste de lecture</button>
                </form>

                <a href="index.php"><button>Retour à la bibliothèque</button></a>
            </body>  
            </html>

<?php
        } else {
            echo "<p>Aucun livre trouvé avec cet ID.</p>";
        }

        // Fermeture du statement
        $stmt->close();
    } else {
        echo "<p>Erreur lors de la préparation de la requête.</p>";
    }
} else {
    echo "<p>ID du livre invalide.</p>";
}

// Fermeture de la connexion
$connection->close(); 
?>