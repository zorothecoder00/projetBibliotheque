<?php
session_start(); 
include 'connexion.php'; // Connexion à la base de données

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer et sécuriser l'email
    $email = trim($_POST['email']);

    if (!empty($email)) {
        // Requête pour récupérer le lecteur avec cet email
        $sql = "SELECT id, nom, prenom FROM lecteurs WHERE email = ?";
        
        if ($stmt = $connection->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultat = $stmt->get_result();

            // Vérifier si un utilisateur a été trouvé
            if ($resultat->num_rows == 1) {
                $lecteur = $resultat->fetch_assoc();

                // Stocker les infos du lecteur dans la session
                $_SESSION['id'] = $lecteur['id'];
                $_SESSION['nom'] = $lecteur['nom'];
                $_SESSION['prenom'] = $lecteur['prenom'];

                // Redirection vers la page principale
                header("Location: index.php");
                exit();
            } else {
                $erreur = "Aucun compte trouvé avec cet email.";
            }
            $stmt->close();
        } else {
            $erreur = "Erreur de connexion à la base de données.";
        }
    } else {
        $erreur = "Veuillez entrer votre email.";
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion</title>
</head>
<body>
    <h2>Connexion</h2>
    
    <?php
    if (isset($erreur)) {
        echo "<p style='color: red;'>" . htmlspecialchars($erreur) . "</p>";
    }
    ?>

    <form method="POST" action="">
        <label>Email :</label>
        <input type="email" name="email" required>
        
        <button type="submit">Se connecter</button>
    </form>

    <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous ici</a></p>
</body>
</html>