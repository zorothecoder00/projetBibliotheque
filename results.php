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

session_start();
include 'connexion.php'; //Inclut la connexion à la base de donnée   

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    die("Veuillez vous connecter pour voir les résultats.");
}

$id = $_SESSION['id'];  // Récupérer l'id du lecteur depuis la session


//Traitement des données du formulaire
if(isset($_GET['recherche']) && isset($_GET['critere'])){
	$recherche = trim($_GET['recherche']);
	$critere = $_GET['critere'];    


	if($recherche){
		// Préparer la requête en fonction du critère choisi
		if($critere === 'titre'){
			$sql = "SELECT * FROM livres WHERE titre LIKE ?";	
		
		}elseif($critere === 'auteur'){
			$sql = "SELECT * FROM livres WHERE auteur LIKE ?";
		}else{
			die("Critère de recherche invalide.");
		}

		//Excécution de la requête préparée
		if($stmt = $connection->prepare($sql)){
			$like = "%$recherche%";
			$stmt->bind_param('s' ,$like);
			$stmt->execute();
			$resultat = $stmt->get_result();
		
   
			// Affichage des résultats
			if($resultat->num_rows > 0){
				echo "<h2>Le résultat de la recherche est : </h2>";
				echo "<ul>"; // Ajout d'une liste pour afficher les livres
				while ($row = $resultat->fetch_assoc()) {
					echo "<li>";
					echo "Titre : ".htmlspecialchars($row['titre']).'<br>'." "."Auteur : ".htmlspecialchars($row['auteur']).'<br>';

					 // Convertir l'image en base64 pour l'afficher directement
				    if ($row['image']) {
				        $imageData = base64_encode($row['image']);
				        echo '<img src="data:image/jpeg;base64,' . $imageData . '" width="100" height="150"><br>';
				    } else {
				        echo "Pas d'image disponible.<br>";
				    }
  
					// Ajout d'un bouton pour voir les détails
					echo "<a href = 'details.php?id=" .$row['id'] ."'><button>Voir les détails</button></a>";
					echo "</li>";
				}
				echo "</ul>";  

			}else{
				echo "<p>Aucun livre trouvé.</p>";
			}

		// Fermeture du statement
        $stmt->close();

		}else{
			die("Erreur lors de la préparation de la requête");
		}

	}else{
		echo "Veuillez entrer un terme de recherche";
	}

}else{
	echo "Paramètre manquant";
}


// Fermeture de la connexion
$connection->close();

?>