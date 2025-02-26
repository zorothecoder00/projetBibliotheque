 <?php
session_start();
include 'connexion.php'; // Connexion à la base de données

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Vérifier que tous les champs sont remplis
    if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($password)) {
        // Vérifier si l'email est déjà utilisé
        $sql = "SELECT id FROM lecteurs WHERE email = ?";
        if ($stmt = $connection->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $erreur = "Cet email est déjà utilisé.";
            } else {
                // Hacher le mot de passe
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // Insérer le nouvel utilisateur
                $sql = "INSERT INTO lecteurs (nom, prenom, email) VALUES (?, ?, ?, ?)";
                if ($stmt = $connection->prepare($sql)) {
                    $stmt->bind_param("ssss", $nom, $prenom, $email);
                    if ($stmt->execute()) {
                        // Récupérer l'ID du nouvel utilisateur
                        $_SESSION['id'] = $stmt->insert_id;
                        $_SESSION['nom'] = $nom;
                        $_SESSION['prenom'] = $prenom;

                        // Redirection vers l'index
                        header("Location: index.php");
                        exit();
                    } else {
                        $erreur = "Erreur lors de l'inscription.";
                    }
                }
            }
            $stmt->close();
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription</title>
</head>
<body>
    <h2>Inscription</h2>

    <?php
    if (isset($erreur)) {
        echo "<p style='color: red;'>" . htmlspecialchars($erreur) . "</p>";
    }
    ?>

    <form method="POST" action="">
        <label>Nom :</label>
        <input type="text" name="nom" required>
        
        <label>Prénom :</label>
        <input type="text" name="prenom" required>

        <label>Email :</label>
        <input type="email" name="email" required>

        
        <button type="submit">S'inscrire</button>
    </form>

    <p>Déjà inscrit ? <a href="login.php">Connectez-vous ici</a></p>
</body>
</html>
